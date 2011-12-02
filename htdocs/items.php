<?

function ParseItemStr( $s )
{
	$s = trim( $s );
	$res = Array( );
	
	$l = strlen( $s );
	
	$a = 0;
	$b = 0;
	$q = 0;
	$sign = 0;
	for( $i = 0; $i < $l; ++ $i )
	{
		if( $s[$i] == '.' || $s[$i] == ':' )
		{
			if( $sign ) $b = - $b;
			if( $q ) $res[$a] += $b;
			$q = 1 - $q;
			$a = $b;
			$b = 0;
			$sign = 0;
			if( $s[$i] == '.' ) break;
		}
		else if( $s[$i] == '-' ) $sign = 1;
		else $b = $b * 10 + ord( $s[$i] ) - ord( '0' );
	}
	
	return $res;
}

function ItemEffectStr( $s )
{
	global $stats;
	
	$arr = ParseItemStr( $s );
	
	$st = "";
	
	foreach( $arr as $a=>$b )
		if( $a >= 300 && $a < 400 )
			$st .= ( "<b>".$stats[$a].":</b> {$b}%<br>" );
		else if( $a == 555 )
			$st .= ( "<b>".$stats[$a].":</b> +{$b}%<br>" );
		else if( $b > 0 )
			$st .= ( "<b>".$stats[$a].":</b> +$b<br>" );
		else
			$st .= ( "<b>".$stats[$a].":</b> $b<br>" );
			
	return $st;
}		

function ItemReqStr( $s )
{
	global $stats;
	
	$arr = ParseItemStr( $s );
	
	$st = "";
	
	foreach( $arr as $a=>$b )
		$st .= ( "<b>".$stats[$a].":</b> $b<br>" );
		
	return $st;
}	

function itemDescr( $arr, $need_descr = true )	
{
	$effect = $arr['effect'];
	$req = $arr['req'];
	$descr = $arr['descr'];

	$st = "";
	$st .= ItemEffectStr( $effect );
	if( $arr[inner_spell_id] )
	{
		$card = new Card( $arr[inner_spell_id] );
		$st .= "Встроенное заклинание: <a href=help.php?id=1011&card_id=$arr[inner_spell_id] target=_blank>".$card->name."</a><br>";
		$st .= "<b>Зарядов</b>: $arr[charges]/$arr[max_charges] <small>($arr[charges_spent])</small><br>";
	}
	if( $arr[learn_spell_id] )
	{
		$card = new Card( $arr[learn_spell_id] );
		$st .= "Изучает заклинание: <a href=help.php?id=1011&card_id=$arr[learn_spell_id] target=_blank>".$card->name."</a><br>";
	}
	if( $arr[learn_recipe_id] )
	{
		$cres = f_MQuery( "SELECT name FROM recipes WHERE recipe_id=$arr[learn_recipe_id]" );
		$carr = f_MFetch( $cres );
		$st .= "Изучает рецепт: <a href=help.php?id=1015&recipe_id=$arr[learn_recipe_id] target=_blank>".$carr['name']."</a><br>";
	}

	if( $arr['type'] > 1 && $arr['type'] < 20 ) $st .= "<b>Прочность</b>: $arr[decay]/$arr[max_decay]<br>";

	$gnr = array( "вода", "природа", "огонь" );
	if( $arr['charges_level'] != 0 ) $st .= "<br><b>Заряжен на улучшение:</b><br>Уровень: $arr[charges_level]<br>Улучшение: $arr[charges_mk]<br>Стихия: {$gnr[$arr[charges_genre]]}<br>";

	if( $req )
	{
		$st .= "Требует:<br>";
		$st .= ItemReqStr( $req );
	}
	if( $need_descr ) $st .= "<i>".str_replace( "\n", "<br>", str_replace( "\r\n", "<br>", $descr ) )."</i><br>";

	$orderName = checkOrderItem( $arr['item_id'] );
	if( $orderName != false )
		$st .= "<br><small><b>Владелец: </b><font color=green>" . $orderName . "</font></small><br>";

	return $st;
}

function itemFullDescr( $arr, $need_descr = true )
{
	$name = $arr['name'];
	$level = $arr['level'];

	$st = "<a href=help.php?id=1010&item_id=$arr[parent_id] target=_blank><b>$name</b></a><br>";
	if( $arr['type'] != 0 ) $st .= "Уровень: $level<br>";                        
	$st .= itemDescr( $arr, $need_descr );
	
	return $st;
}

function itemFullDescr2( $arr, $need_descr = true )
{
	global $player;
	$ret = itemFullDescr( $arr, $need_descr );
	if( $arr['weared'] && $arr['type'] == 1 )
	{
		$eres = f_MQuery( "SELECT expires FROM player_potions WHERE player_id={$player->player_id} AND slot_id=$arr[weared]" );
		$earr = f_MFetch( $eres );
		$eval = $earr[0] - time( ) + 59;
		$ret .= "До окончания действия ".my_time_str( $eval, false );
	}
	return $ret;
}

function itemImage( $arr )
{
	return (($arr[image_large] == '')?$arr[image]:$arr[image_large]);
}

function copyItem( $item_id, $improve = false, $clan_mark = false )
{
	f_MQuery( "LOCK TABLE items WRITE" );
	$res = f_MQuery( "SELECT * FROM items WHERE item_id=$item_id" );
	if( !f_MNum( $res ) ) RaiseError( "Ошибка при копировании вещи - вещи не существует", "item_id: $item_id" );
	$arr = f_MFetch( $res );
	if( $arr['improved'] )
	{
		if( $clan_mark ) f_MQuery( "UPDATE items SET clan_marked=1 WHERE item_id=$item_id" );
		f_MQuery( "UNLOCK TABLES" );
		return $item_id;
	}
	if( $arr['clan_marked'] )
	{
		if( $improve ) f_MQuery( "UPDATE items SET improved=1 WHERE item_id=$item_id" );
		f_MQuery( "UNLOCK TABLES" );
		return $item_id;
	}
	
	$fields = "parent_id, improved, clan_marked";
	$ii = ( $improve?1:0 );
	$cc = ( $clan_mark?1:0 );
	$values = "$arr[parent_id], $ii, $cc";

	for( $i = 0; $i < mysql_num_fields( $res ); ++ $i )
	{
		$meta = mysql_fetch_field( $res, $i );
		if( !$meta ) continue;
		if( $meta->name != 'item_id' && $meta->name != 'improved' && $meta->name != 'parent_id' && $meta->name != 'clan_marked' )
		{
			$fields .= ", ".$meta->name;
			$values .= ", '".AddSlashes( $arr[$meta->name] )."'";
		}
	}
	f_MQuery( "INSERT INTO items ( $fields ) VALUES ( $values )" );
	$new_id = mysql_insert_id( );
//	echo "INSERT INTO items ( $fields ) VALUES ( $values )";
//	echo "<br>$new_id";
	f_MQuery( "UNLOCK TABLES" );

	return $new_id;
}

function getItemNameForm( $item_id, $form )
{
	$res = f_MQuery( "SELECT name$form, name FROM items WHERE item_id=$item_id" );
	$arr = f_MFetch( $res );
	if( $arr[0] == "" ) return $arr[1];
	return $arr[0];
}


// проверка орденской принадлежности вещи
// возвращает имя ордена для принадлежащей ордену вещи
// и false для нейтральной шмотки
function checkOrderItem ( $item_id )
{
	$res = f_MQuery( "select items_order.*, clans.name, clans.clan_id from items_order, clans where items_order.unique_id=$item_id and clans.clan_id=items_order.order_id");
	if ( $arr = f_MFetch( $res ) ) 
	{
		return $arr["name"];
	}
	else return false;
}

// случайно вспомнил про недостаточную проверку
// функция для проверки типа предмета, чтобы помечать можно было только оружие и шмот
function checkItemType( $item_id )
{
	$res = f_MQuery( "select type from items where item_id=$item_id" );
	if ( $arr = f_MFetch( $res ) )
	{
		if ( $arr["type"] > 1 && $arr["type"] < 20 )
			return true;
		else 
			return false;
	}
	else
		return false;
}

// сделать вещь орденской
// Копирование предмета, занесение информации в таблицу items_order для новой вещи. Не забыть сделать проверку, является ли вещь уже уникальной.
// вернуть false в случае ошибки
function createUniqueItem ( $item_id, $order_id, $color )
{
	$newItemId = 0;
	if ( !checkOrderItem ( $item_id ) && checkItemType( $item_id ) )
	{
		//f_MQuery( "LOCK TABLES clan_items, items_order WRITE" );
		
		// получаем текущее количество вещей выбранного типа на складе
		$itemsAmount = 0;
		$res = f_MQuery( "select number from clan_items where clan_id=$order_id and item_id=$item_id and color=$color" );
		if ( mysql_num_rows( $res ) > 0 ) 
		{
			$arr = f_MFetch( $res );
			$itemsAmount = $arr["number"];
		}
		else
		{
			return false;
		}
		
		// уменьшаем кол-во старых вещей на 1 (либо удаляем, если берем последнюю)
		if ( $itemsAmount > 1 )
		{
			f_MQuery( "update clan_items set number=$itemsAmount-1 where clan_id=$order_id and item_id=$item_id and color=$color" );
		}
		else
		{
			f_MQuery( "delete from clan_items where clan_id=$order_id and item_id=$item_id and color=$color" );
		}
		
		// кладем на склад новую уникальную шмотку (ниже нужно решить вопрос с полкой, об этом выше
		$newItemId = copyItem ( $item_id, false, true );
		f_MQuery( "insert into items_order (order_id, unique_id) values ($order_id, $newItemId)" );
		f_MQuery( "insert into clan_items(clan_id, item_id, number, color) values ($order_id, $newItemId, 1, $color)" );
		
		//f_MQuery( "UNLOCK TABLES" );
		return true;
	}
	else
		return false;
}

// убрать с вещи пометку "Принадлежит ордену"
// вернуть false, если шмотка не является орденской
function removeUniqueItem ( $item_id, $order_id )
{
	if ( !checkOrderItem ( $item_id ) && checkItemType( $item_id ) )
	{
		return false;
	}
	else
	{		
		//f_MQuery( "LOCK TABLES clan_items, items_order WRITE" );
/*		$new_item_id = $item_id;
		
		// удаляем уникальную шмотку из таблицы принадлежащих орденам вещей
		$iiarr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$item_id" ) );
		if( !$iiarr['improved'] )
		{
    		$cres = f_MQuery( "SELECT item_id FROM items WHERE parent_id=$iiarr[parent_id] AND decay=$iiarr[decay] AND max_decay=$iiarr[max_decay] AND clan_marked=0 AND improved=0" );
    		$carr = f_MFetch( $cres );
    		if( $carr ) $new_item_id = $carr[0];
		}
		
		// если добрались сюда, значит шмотка где-то удалилась; положим ее на красную полку склада-владельца
		if( f_MValue( "SELECT count( item_id ) FROM clan_items WHERE item_id=$item_id AND color=0 AND clan_id=$order_id" ) )
			f_MQuery( "UPDATE clan_items SET number=number+1 WHERE item_id=$item_id AND color=0 AND clan_id=$order_id" );
		else f_MQuery( "insert into clan_items(clan_id, item_id, number, color) values ($order_id, $item_id, 1, 0)" );*/

		f_MQuery( "delete from items_order where unique_id=$item_id and order_id=$order_id" );
		
		//f_MQuery( "UNLOCK TABLES" );
		return true;
	}
}

// вернуть орденскую шмотку на полку склада
function returnUniqueItem( $item_id, $order_id, $color = 0 )
{
	// в $oderItem либо false, либо имя ордена-владельца
	//$oderItem = checkOrderItem( $item_id );

	if ( $orderItem = checkOrderItem( $item_id ) ) // если вещь принадлежит ордену
	{
		// странная процедура проверки принадлежности вещи запросившему ее ордену
		$res = f_MQuery( "select clan_id from clans where name='$orderItem'" );
		if ( mysql_num_rows( $res ) > 0 )
		{
			$arr = f_MFetch( $res );
			if ( $order_id != $arr["clan_id"] )
				return false;
		}
		else
			return false;
		
		//f_MQuery( "LOCK TABLES clan_items, items_order WRITE" );
		
		// проверим, лежит ли шмотка у какого-либо игрока
		$res = f_MQuery( "select weared, player_id from player_items where item_id=$item_id" );
		if ( mysql_num_rows( $res ) > 0 )
		{
			$arr = f_MFetch( $res );
			$slot = $arr["weared"];
			include_once( 'wear_items.php' );	
			
			// зашаманим тут немного...
			global $player;
			$old_player = $player;
			$player = new Player( $arr['player_id'] );
			UnWearItem( $slot, true );
			$player->AddToLog( $item_id, -1, 39 );
			$player->syst2( "/items" );
			$player = $old_player;
			f_MQuery( "delete from player_items where item_id=$item_id" ); // количество тут не имеет значения, т.к. уникальная шмотка должна быть одна
		}
		else
		{
			// теперь проверим почтовые вложения
			$res = f_MQuery( "select entry_id from post_items where item_id=$item_id" );
			if ( mysql_num_rows( $res ) > 0 )
			{
				$arr = f_MFetch( $res );
				$entry_id = $arr["entry_id"];
				f_MQuery( "delete from post_items where entry_id=$entry_id and item_id=$item_id" ); // удалим вложение из письма
			}
			else
			{
				// и хранилище проверим для полного счастья
				$res = f_MQuery( "select * from player_warehouse_items where item_id=$item_id" );
				if ( mysql_num_rows( $res ) > 0 )
				{
					f_MQuery( "delete from player_warehouse_items where item_id=$item_id" ); // без проверки персонажей, ведь вещь всего одна
				}
				else
				{
					// дурацкая вложенность использована именно для этого
					// вдруг шмотки не будет ни в инвентаре, ни в хранилище, ни в почтовом вложении? 
					return false;
				}
			}
		}
		
		f_MQuery( "insert into clan_items(clan_id, item_id, number, color) values ($order_id, $item_id, 1, $color)" );
		
		//f_MQuery( "UNLOCK TABLES" );
		
		// epic fail, забыл добавить return true хотя бы где-нибудь, искал ошибку в рабочей функции
		return true;
	}
	else
	{
		return false;
	}
}


/*include_once( 'functions.php' );
f_MConnect( );
copyItem( 222 );/**/

?>
