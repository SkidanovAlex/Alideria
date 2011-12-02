<?

include_once( "functions.php" );
include_once( "items.php" );
include_once( "skin.php" );

class Shop
{
	var $shop_id;
	var $name;
	var $buy_mul;
	var $sell_mul;
	var $regime;
	var $location;
	var $place;
	var $cost;
	var $owner_id;
	var $money;
	var $capacity;
	
	function Shop( $shop_id )
	{
		$this->shop_id = $shop_id;
		
		$res = f_MQuery( "SELECT * FROM shops WHERE shop_id = $shop_id" );
		$arr = f_MFetch( $res );
		
		if( !$arr ) RaiseError( "Магазина $shop_id не существует!" );
		
		$this->name = $arr['name'];
		$this->buy_mul = $arr['buy_mul'];
		$this->sell_mul = $arr['sell_mul'];
		$this->regime = $arr['regime'];
		$this->location = $arr['location'];
		$this->place = $arr['place'];
		$this->cost = $arr['cost'];
		$this->owner_id = $arr['owner_id'];
		$this->money = $arr['money'];
		$this->capacity = $arr['capacity'];
	}
	
	function SetName( $a ) {
		f_MQuery( "UPDATE shops SET name='$a' WHERE shop_id = {$this->shop_id}" );
		$this->name = $a;
		return true;
	}
	function SetSellPriceMul( $a ) {
		f_MQuery( "UPDATE shops SET sell_mul='$a' WHERE shop_id = {$this->shop_id}" );
		$this->sell_mul = $a;
	}
	function SetBuyPriceMul( $a ) {
		f_MQuery( "UPDATE shops SET buy_mul='$a' WHERE shop_id = {$this->shop_id}" );
		$this->buy_mul = $a;
	}
	function SetRegime( $a ) {
		f_MQuery( "UPDATE shops SET regime='$a' WHERE shop_id = {$this->shop_id}" );
		$this->regime = $a;
	}
	function AddMoney( $a ) {
		if( $this->owner_id == -1 ) return;
		f_MQuery( "UPDATE shops SET money = money + '$a' WHERE shop_id = {$this->shop_id}" );
		$this->money += $a;
	}
	function SpendMoney( $a ) {
		if( $this->owner_id == -1 ) return true;
		if( $this->money < $a )
			return false;
		f_MQuery( "UPDATE shops SET money = money - '$a' WHERE shop_id = {$this->shop_id}" );
		$this->money -= $a;
		return true;
	}
	
	function AddItems( $item_id, $number = 1 )
	{
		if( $number <= 0 ) return 0;
		
		$res = f_MQuery( "SELECT number FROM shop_goods WHERE shop_id = {$this->shop_id} AND item_id={$item_id}" );
		if( mysql_num_rows( $res ) )
			f_MQuery( "UPDATE shop_goods SET number = number + $number WHERE shop_id = {$this->shop_id} AND item_id={$item_id}" );
		else
			f_MQuery( "INSERT INTO shop_goods ( shop_id, item_id, number ) VALUES ( {$this->shop_id}, $item_id, $number )" );
			
		return $number;
	}

	function NumberItems( $item_id )
	{
		$res = f_MQuery( "SELECT number FROM shop_goods WHERE shop_id = {$this->shop_id} AND item_id={$item_id}" );
		$arr = f_MFetch( $res );
		if( !$arr ) return 0;
		return $arr[0];
	}
	
	function DropItems( $item_id, $number = 1 )
	{
		f_MQuery( "LOCK TABLE shop_goods WRITE" );
		$res = f_MQuery( "SELECT number FROM shop_goods WHERE shop_id = {$this->shop_id} AND item_id={$item_id}" );
		if( !mysql_num_rows( $res ) ) { f_MQuery( "UNLOCK TABLES" ); return 0; }
		else
		{
			$arr = f_MFetch( $res );
			if( $number == -1 ) // -1 = выбросить все
			{
				f_MQuery( "DELETE FROM shop_goods WHERE shop_id = {$this->shop_id} AND item_id={$item_id}" );
				f_MQuery( "UNLOCK TABLES" );
				return 1;
			}
			else if( $number < 0 ) { f_MQuery( "UNLOCK TABLES" ); return 0; }
			if( $arr[number] < $number ) { f_MQuery( "UNLOCK TABLES" ); return 0; }
			if( $arr[number] == $number )
				f_MQuery( "DELETE FROM shop_goods WHERE shop_id = {$this->shop_id} AND item_id={$item_id}" );
			else
				f_MQuery( "UPDATE shop_goods SET number = number - $number WHERE shop_id = {$this->shop_id} AND item_id={$item_id}" );
			f_MQuery( "UNLOCK TABLES" );
		}
		
		return 1;
	}
	
	function CheckCapacity( $item_id )
	{
		$result = f_MQuery("SELECT item_id FROM shop_goods WHERE item_id=$item_id AND shop_id='$this->shop_id'");
		if(mysql_num_rows($result) > 0) return TRUE;
		
		$result = f_MQuery("SELECT item_id FROM shop_goods WHERE shop_id='$this->shop_id'");
		if( mysql_num_rows($result) >= $this->capacity ) return FALSE;
		else return TRUE;
	}
	
	function ShowGoods( $ordering = "items.type, items.level" )
	{
		global $player;
		global $stats;
		
		if( $player->IsShopOwner( $this->shop_id ) )
		{
			print( "\n\n<script src='js/shop_owner.php'></script>\n" );
		}
		else
		{
			print( "\n\n<script src='js/shop.php'></script>\n" );
		}
		
		ScrollLightTableStart("left");

		$stats = $player->getAllAttrNames( );
		$res = f_MQuery( "SELECT items.*, shop_goods.number, shop_goods.sell_price, shop_goods.buy_price, shop_goods.position FROM items, shop_goods WHERE shop_id = {$this->shop_id} AND items.item_id = shop_goods.item_id ORDER BY $ordering" );
		if( !mysql_num_rows( $res ) ) print( "<i>Прилавок магазина пуст</i><br>" );
		else if( $this->regime == 3 ) print( "<i>Магазин закрыт</i><br>" );
		else
		{
			print( "<script>\n" );
			print( "\tshop_id = {$this->shop_id};\n" );
			print( "\tshop_setRegime( {$this->regime} );\n" );
			while( $arr = f_MFetch( $res ) )
			{
				$sell_price = $arr['sell_price'];
				$buy_price = $arr['buy_price'];
				
				if( $sell_price == -1 ) $sell_price = $arr['price'] * $this->sell_mul / 100.0;
				if( $buy_price == -1 ) $buy_price = $arr['price'] * $this->buy_mul / 100.0;
				
				if( $sell_price < 0 ) RaiseError( "Неверная цена продажи товара $arr[name] в магазине {$this->shop_id}: $sell_price" );
				if( $buy_price < 0 ) RaiseError( "Неверная цена покупки товара $arr[name] в магазине {$this->shop_id}: $buy_price" );

				$descr = itemFullDescr( $arr, false );
				if( $arr['level'] > $player->level )
					$descr = str_replace( "Уровень: $arr[level]", "<b><font color=red>Уровень: $arr[level]</font></b>", $descr );
				
				print( "\tshop_addItem( $arr[item_id], $arr[type], $arr[number], ".($player->NumberItems($arr['item_id'])).", $sell_price, $buy_price, '$arr[name]', '".itemImage( $arr )."', '$descr', '$arr[position]' );\n" );
			}
			print( "\tshop_showHtml( );\n" );
			print( "</script>\n" );
		}

		ScrollLightTableEnd();
	}
	
	function GetBuyOrSellCost( $item_id, $number )
	{
		// -1 - не хватает товара
		// -2 = последний экземпляр
		// -3 = нет товара
		// -4 - не подходящий режим магазина
		// иначе - стоимость покупки/продажи
		$res = f_MQuery( "SELECT items.price, shop_goods.number, shop_goods.sell_price, shop_goods.buy_price, shop_goods.regime FROM items, shop_goods WHERE shop_id = {$this->shop_id} AND items.item_id = shop_goods.item_id AND items.item_id = $item_id" );
		$arr = f_MFetch( $res );
		if( !$arr ) return -3;
		
		$sell_price = $arr['sell_price'];
		$buy_price = $arr['buy_price'];
		
		if( $sell_price == -1 ) $sell_price = $arr['price'] * $this->sell_mul / 100.0;
		if( $buy_price == -1 ) $buy_price = $arr['price'] * $this->buy_mul / 100.0;
		
		if( $sell_price < 0 ) RaiseError( "Неверная цена продажи товара $arr[name] в магазине {$this->shop_id}: $sell_price" );
		if( $buy_price < 0 ) RaiseError( "Неверная цена покупки товара $arr[name] в магазине {$this->shop_id}: $buy_price" );

		if( $number < 0 )
		{
			if( $this->regime == 1 || $this->regime == 3 ) return -4;
			return - $buy_price * $number;
		}
		if( $number == 0 ) return 0;
		
		if( $this->regime == 2 || $this->regime == 3 ) return -4;
		
		$good_regime = $arr['regime'];
		if( $good_regime == -1 ) $good_regime = ( $this->regime == 4 ) ? 1 : 0;
		if( $good_regime == 1 && $arr['number'] == $number ) return -2;
		if( $arr['number'] < $number ) return -1;
		
		return $sell_price * $number;
	}
}


?>
