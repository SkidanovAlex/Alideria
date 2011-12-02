<?

header("Content-type: text/html; charset=windows-1251");

include_once( "functions.php" );
include_once( "player.php" );
include_once( "clan.php" );
include_once( "items.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
$clan_id = $player->clan_id;

if( $player->location != 2 || $player->depth != 50 && $player->depth != 19 )
	RaiseError( "Попытка работать со складом Ордена вне зала собраний", "{$player->location} != 2 || {$player->depth} != 19" );

$arr = @explode( "|", $HTTP_RAW_POST_DATA );

function put_to_silo( $item_id, $number, $clr )
{
	global $player;
	global $clan_id;
	global $CAN_PUT_TO_SILO_RED;
	
	$cap1 = getSiloCurCapacity( $clan_id );
	$cap2 = getSiloCapacity( getBLevel( 3 ) );
	$weight1 = getSiloCurWeight($clan_id);
	$weight2 = getSiloWeight(getBLevel( 3 ));
	$res = f_MQuery( "SELECT item_id FROM clan_items WHERE clan_id={$player->clan_id} AND item_id=$item_id AND color=$clr" );
	$arr = f_MFetch( $res );

	$bres = f_MQuery( "SELECT balance FROM player_clans WHERE player_id={$player->player_id} AND clan_id={$clan_id}" );
	$barr = f_MFetch( $bres );
	$balance = $barr[0];

	$ires = f_MQuery( "SELECT price FROM items WHERE item_id = $item_id" );
	$iarr = f_MFetch( $ires );
	$balance += $iarr[0] * $number;

	// проверим пометку ордена на предмете
	if ( checkOrderItem( $item_id ) )
	{
		$check_res = f_MQuery( "select order_id from items_order where unique_id=$item_id" );
		if ( mysql_num_rows( $check_res ) > 0 )
		{
			$check_arr = f_MFetch( $check_res );
			if ( $check_arr["order_id"] != $player->clan_id )
				return 'Нельзя положить на склад не принадлежащую ордену вещь.';
		}
		else
			return 'Выбранный предмет помечен как орденский, но ни одному ордену не принадлежит. Удивительно!';
	}
	// ----8<----------

	if( 0 == ( getPlayerPermitions( $player->clan_id, $player->player_id ) & ( $CAN_PUT_TO_SILO_RED * pow( 2, $clr ) ) ) )
		return 'У вас нет прав класть на эту полку склада';
	else if( !$arr && $cap1 >= $cap2 )
		return 'На складе нет места';
	else if ($weight1/100 >= $weight2)
		return 'На складе достигнут предельный вес';
	else if( $player->DropItems( $item_id, $number ) )
	{
		$iiarr = f_MFetch( f_MQuery( "SELECT parent_id, type FROM items WHERE item_id=$item_id" ) );
		if( $iiarr['type'] > 0 && $iiarr['type'] < 20 )
		{
			for( $i = 0; $i < $number && $i < 10; ++ $i )
			{
    			$entry_id = f_MValue( "SELECT entry_id FROM player_clan_items WHERE player_id={$player->player_id} AND item_id={$iiarr[parent_id]}" );
    			if( !$entry_id ) break;
    			f_MQuery( "DELETE FROM player_clan_items WHERE entry_id=$entry_id" );
			}
		}
		f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0, arg1, arg2 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 6, $item_id, $number, $clr )" );
		$player->AddToLogPost( $item_id, -$number, 14 );
		if( $arr ) f_MQuery( "UPDATE clan_items SET number = number + $number WHERE clan_id={$player->clan_id} AND item_id=$item_id AND color=$clr" );
		else f_MQuery( "INSERT INTO clan_items ( clan_id, item_id, number, color ) VALUES ( {$player->clan_id}, $item_id, $number, $clr )" );
		f_MQuery( "UPDATE player_clans SET balance = $balance WHERE player_id=$player->player_id AND clan_id=$clan_id" );
		return '';
	} else return 'У вас нет такого количества вещей';
}

if( isset( $_GET['list_action'] ) )
{
	$stats = $player->getAllAttrNames( );
	$action = (int)$_GET['list_action'];
	if( $action < -1 || $action >= 5 ) RaiseError( "Неизвестный режим работы с листом склада: $action" );
	$report = 'Результат:\n';
	foreach( $arr as $parent_id )
	{
		$parent_id = (int)$parent_id;
		$name = f_MValue( "SELECT name FROM items WHERE item_id=$parent_id" );
		$report .= "{$name}: ";
		$item_id = f_MValue( "SELECT i.item_id FROM items as i INNER JOIN player_items as p ON i.item_id = p.item_id WHERE p.player_id={$player->player_id} AND i.parent_id={$parent_id} ORDER BY max_decay DESC, decay DESC LIMIT 1" );
		if( $action != -1 && !$item_id ) $report .= "не найдена!";
		else {
			$ok = true;
			$entry_id = f_MValue( "SELECT entry_id FROM player_clan_items WHERE player_id={$player->player_id} AND item_id={$parent_id}" );
			if( $action == -1 ) f_MQuery( "DELETE FROM player_clan_items WHERE entry_id=$entry_id" );
			else
			{
				$ret = put_to_silo( $item_id, 1, $action );
				if( $ret != '' ) {
					$ok = false;
					$report .= $ret;
				}
				else {
					$iarr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id={$item_id}" ) );
					echo "add_item( $iarr[item_id], $iarr[type], '".addslashes($iarr['name'])."', '".itemImage( $iarr )."', '".addslashes(itemFullDescr( $iarr ))."', 1, $action );\n";
				}
			}
			
			if( $ok ) {
				$report .= "OK!";
				echo "remove_taken_item( $parent_id );";
			}
		}
		$report .= '\n';
	}
	echo "ref();alert( '$report' );";
	die( );
}

if( count( $arr ) != 3 ) die( "alert( ".count( $arr )." + '$HTTP_RAW_POST_DATA' )" );

$item_id = $arr[0];
$number = $arr[1];
$clr = $arr[2];

settype( $item_id, 'integer' );
settype( $number, 'integer' );
settype( $clr, 'integer' );

if( $clr < 0 || $clr >= 5 )
	RaiseError( "Неверный цвет полки на складе $clr" );

if( $number > 0 )
{
	$ret = put_to_silo( $item_id, $number, $clr );
	if( $ret == '' ) echo "remove_item( $item_id, $number );refresh_items();";
	else echo "alert( '$ret' )";
}
else if( $number < 0 )
{
	$number = - $number;
	$res = f_MQuery( "SELECT number FROM clan_items WHERE item_id=$item_id AND clan_id={$player->clan_id} AND color=$clr" );
	$arr = f_MFetch( $res );

	$bres = f_MQuery( "SELECT balance FROM player_clans WHERE player_id={$player->player_id} AND clan_id={$clan_id}" );
	$barr = f_MFetch( $bres );
	$balance = $barr[0];

	$ires = f_MQuery( "SELECT price FROM items WHERE item_id = $item_id" );
	$iarr = f_MFetch( $ires );
	$balance -= $iarr[0] * $number;

	if( !$arr || $arr[0] < $number ) echo "alert( 'У вас нет столько вещей на складе' );";
	else if( $balance >= 0 && 0 == ( getPlayerPermitions( $player->clan_id, $player->player_id ) & ( 64 * pow( 2, $clr ) ) ) )
		echo "alert( 'У вас нет прав брать с этой полки склада' );";
	else if( $balance < 0 && 0 == ( getPlayerPermitions( $player->clan_id, $player->player_id ) & ( 2048 * pow( 2, $clr ) ) ) )
		echo "alert( 'У вас нет прав брать с этой полки склада' );";

	else
	{
		$iiarr = f_MFetch( f_MQuery( "SELECT parent_id, type, name FROM items WHERE item_id=$item_id" ) );
		if( $number == 1 && $iiarr['type'] > 0 && $iiarr['type'] < 20 )
		{
			f_MQuery( "INSERT INTO player_clan_items ( player_id, item_id ) VALUES ( {$player->player_id}, {$iiarr[parent_id]} )" );
			if( isset( $_GET['weap'] ) ) echo "add_taken_item( $iiarr[parent_id], '".addslashes($iiarr[name])."' );";
		}
		f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0, arg1, arg2 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 6, $item_id, -$number, $clr )" );
		if( $arr[0] == $number ) f_MQuery( "DELETE FROM clan_items WHERE item_id=$item_id AND clan_id={$player->clan_id} AND color=$clr" );
		else f_MQuery( "UPDATE clan_items SET number = number - $number WHERE item_id=$item_id AND clan_id={$player->clan_id} AND color=$clr" );
		f_MQuery( "UPDATE player_clans SET balance = $balance WHERE player_id=$player->player_id AND clan_id=$clan_id" );
		$player->AddItems( $item_id, $number );
		$player->AddToLogPost( $item_id, $number, 14 );
		echo "remove_item( $item_id, $number, $clr );refresh_items();";
	}
}

?>
