<?

include_once( "functions.php" );
include_once( "player.php" );
include_once( "shop.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
$shop_id = $_GET['shop_id'];
settype( $shop_id, 'integer' );
$position = $_GET['position'];
settype( $position, 'integer' );
$dir = $_GET['dir'];
settype( $dir, 'integer' );

// новая переменная для автоматической сортировки
if ( isset ( $_GET['sort'] ) )
{
	$sort = $_GET['sort'];
	settype( $sort, 'integer' );
}

if( $player->IsShopOwner( $shop_id ) )
{
	if ( $sort == 1 ) // Автоматическая сортировка по типу предмету
	{
		f_MQuery( "lock table shop_goods write, items write" );
		
		$blah = f_MQuery( "select items.type, items.item_id, shop_goods.* from items, shop_goods where shop_goods.shop_id=$shop_id and items.item_id=shop_goods.item_id order by items.type, items.level" );

		f_MQuery( "delete from shop_goods where shop_id=$shop_id" );
		for ( $i = 0; $i < mysql_num_rows( $blah ); $i++ )
		{
			$rec = f_MFetch ( $blah );
			f_MQuery( "insert into shop_goods (shop_id, item_id, buy_price, sell_price, regime, number) values ($rec[shop_id], $rec[item_id], $rec[buy_price], $rec[sell_price], $rec[regime], $rec[number])" );
		}
		
		f_MQuery( "unlock tables" );
		print("<script>parent.location.href = 'game.php?shop_id=$shop_id';</script>");
	}
	if ( $sort == 2 ) // по уровню
	{
		f_MQuery( "lock table shop_goods write, items write" );
		
		$blah = f_MQuery( "select items.type, items.item_id, shop_goods.* from items, shop_goods where shop_goods.shop_id=$shop_id and items.item_id=shop_goods.item_id order by items.level, items.type" );

		f_MQuery( "delete from shop_goods where shop_id=$shop_id" );
		for ( $i = 0; $i < mysql_num_rows( $blah ); $i++ )
		{
			$rec = f_MFetch ( $blah );
			f_MQuery( "insert into shop_goods (shop_id, item_id, buy_price, sell_price, regime, number) values ($rec[shop_id], $rec[item_id], $rec[buy_price], $rec[sell_price], $rec[regime], $rec[number])" );
		}
		
		f_MQuery( "unlock tables" );
		print("<script>parent.location.href = 'game.php?shop_id=$shop_id';</script>");
	}
	if ( $sort == 3 ) // по уровню по убыванию
	{
		f_MQuery( "lock table shop_goods write, items write" );
		
		$blah = f_MQuery( "select items.type, items.item_id, shop_goods.* from items, shop_goods where shop_goods.shop_id=$shop_id and items.item_id=shop_goods.item_id order by items.level DESC, items.type" );

		f_MQuery( "delete from shop_goods where shop_id=$shop_id" );
		for ( $i = 0; $i < mysql_num_rows( $blah ); $i++ )
		{
			$rec = f_MFetch ( $blah );
			f_MQuery( "insert into shop_goods (shop_id, item_id, buy_price, sell_price, regime, number) values ($rec[shop_id], $rec[item_id], $rec[buy_price], $rec[sell_price], $rec[regime], $rec[number])" );
		}
		
		f_MQuery( "unlock tables" );
		print("<script>parent.location.href = 'game.php?shop_id=$shop_id';</script>");
	}
	else
	{
		$pos = Array();
		$foo = -1;
		$blah = f_MQuery( "select position from shop_goods where shop_id=" . $shop_id . " order by position" );
		if( mysql_num_rows( $blah ) )
		{
			for ( $i = 0; $i < mysql_num_rows( $blah ); $i++ )
			{
				$rec = f_MFetch ( $blah );
				$pos[$i] = $rec['position']; // здесь будем запоминать позиции по порядку, чтобы получить доступ к позиции-1 и позиции+1
				if( $pos[$i] == $position ) $foo = $i;
			}
			
			if( $foo == -1 ) die( );
			
			if( $dir == 1 ) // если поднимаем вверх
			{
				if( !array_key_exists( $foo-1, $pos ) )
					die( "<script>alert( 'Этот предмет нельзя поднять выше!' );</script>" );
				$sum = $pos[$foo] + $pos[$foo - 1];
				f_MQuery( "lock table shop_goods write" );
				f_MQuery( "update shop_goods set position= - position where shop_id=" . $shop_id . " and ( position=" . $pos[$foo] . " OR position=" . ($pos[$foo - 1]) . ")");
				f_MQuery( "update shop_goods set position=$sum + position where shop_id=" . $shop_id . " and ( position=-" . $pos[$foo] . " OR position=-" . ($pos[$foo - 1]) . ")");
				f_MQuery( "unlock tables" );
				print("<script>parent.location.href = 'game.php?shop_id=$shop_id';</script>");
			}
			
			if( $dir == 2 ) // если опускаем вниз
			{
				if( !array_key_exists( $foo+1, $pos ) )
					die( "<script>alert( 'Этот предмет нельзя опустить ниже!' );</script>" );
				$sum = $pos[$foo] + $pos[$foo + 1];
				f_MQuery( "lock table shop_goods write" );
				f_MQuery( "update shop_goods set position= - position where shop_id=" . $shop_id . " and ( position=" . $pos[$foo] . " OR position=" . ($pos[$foo + 1]) . ")");
				f_MQuery( "update shop_goods set position=$sum + position where shop_id=" . $shop_id . " and ( position=-" . $pos[$foo] . " OR position=-" . ($pos[$foo + 1]) . ")");
				f_MQuery( "unlock tables" );
				print("<script>parent.location.href = 'game.php?shop_id=$shop_id';</script>");
			}
			
			// здесь должна быть функция отрисовки магазина заново через Ajax, но я ее не нашел
			// нужно отобразить заново либо весь магазин, либо искусственно поменять местам 2 задействованных поля
			// наверное лучше отрисовать сразу весь магазин :-[
		}
	}
}
else
{
	die( "<script>alert( 'Вы не имеете права управлять этим магазином!' );</script>" );
}

?>
