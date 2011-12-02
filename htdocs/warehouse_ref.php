<?

header("Content-type: text/html; charset=windows-1251");

include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
$tm = time( );

$res = f_MQuery( "SELECT expires FROM player_warehouse WHERE player_id = {$player->player_id} AND expires > $tm" );
$arr = f_MFetch( $res );

if( !$arr ) die( 'alert( "Сперва следует заплатить за услуги пользования Хранилищем!" );' );

$arr = @explode( "|", $HTTP_RAW_POST_DATA );
if( count( $arr ) != 2 ) die( "alert( ".count( $arr )." )" );

$item_id = $arr[0];
$number = $arr[1];

if( $player->location != 2 || $player->depth != 42 )
	RaiseError( "Попытка оперировать с хранилищем вне Хранилища", "$player->location != 2 || $player->depth != 42" );

settype( $item_id, 'integer' );
settype( $number, 'integer' );

if( $number > 0 )
{
	if( $player->DropItems( $item_id, $number ) )
	{
		$player->AddToLogPost( $item_id, -$number, 12 );
		$res = f_MQuery( "SELECT item_id FROM player_warehouse_items WHERE player_id={$player->player_id} AND item_id=$item_id" );
		$arr = f_MFetch( $res );
		if( $arr ) f_MQuery( "UPDATE player_warehouse_items SET number = number + $number WHERE player_id={$player->player_id} AND item_id=$item_id" );
		else f_MQuery( "INSERT INTO player_warehouse_items ( player_id, item_id, number ) VALUES ( {$player->player_id}, $item_id, $number )" );
		echo "remove_item( $item_id, $number );refresh_items();";
	} else echo 'alert( "У вас нет такого количества вещей!" )';
}
else if( $number < 0 )
{
	$number = - $number;
	$res = f_MQuery( "SELECT number FROM player_warehouse_items WHERE item_id=$item_id AND player_id={$player->player_id}" );
	$arr = f_MFetch( $res );
	if( !$arr || $arr[0] < $number ) echo "alert( 'У вас нет такого количества вещей в Хранилище!' );";
	else
	{
		if( $arr[0] == $number ) f_MQuery( "DELETE FROM player_warehouse_items WHERE item_id=$item_id AND player_id={$player->player_id}" );
		else f_MQuery( "UPDATE player_warehouse_items SET number = number - $number WHERE player_id={$player->player_id} AND item_id=$item_id" );
		$player->AddItems( $item_id, $number );
		$player->AddToLogPost( $item_id, $number, 12 );
		echo "remove_item( $item_id, $number );refresh_items();";
	}
}

?>
