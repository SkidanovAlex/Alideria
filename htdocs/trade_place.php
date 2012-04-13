<?

include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

include_once( "trade_functions.php" );

$res = f_MQuery( "SELECT * FROM trades WHERE player1 = $player->player_id OR player2 = $player->player_id" );
$arr = f_MFetch( $res );
if( !$arr )
{
	die( "<script>location.href='trade_ref.php';</script>" );
}

if( $arr['player1'] == $player->player_id ) { $opponent = $arr['player2']; $status = $arr['status1']; }
else { $opponent = $arr['player1']; $status = $arr['status2']; }

if( $status != 0 )
{
?>
<script>
location.href='trade_ref.php';
</script>

<?
	die( );
}

$item_id = $HTTP_GET_VARS['item_id'];
$number = $HTTP_GET_VARS['number'];

settype( $item_id, 'integer' );
settype( $number, 'integer' );

if( $item_id == 0 )
{
	$res = f_MQuery( "SELECT number FROM trade_goods WHERE player_id = $player->player_id AND good_type = 0" );
	$arr = f_MFetch( $res );
	if( !$arr ) $q = 0;
	else $q = $arr[0];
	
	$player_has = $player->money;
	
	$item_id = 0;
	$good_type = 0;
}
else if( $item_id == -1 )
{
	$res = f_MQuery( "SELECT number FROM trade_goods WHERE player_id = $player->player_id AND good_type = -1" );
	$arr = f_MFetch( $res );
	if( !$arr ) $q = 0;
	else $q = $arr[0];
	
	$player_has = $player->umoney;
	
	$item_id = -1;
	$good_type = -1;
}
else
{
	$res = f_MQuery( "SELECT number FROM player_items WHERE player_id = $player->player_id AND item_id = $item_id AND weared=0" );
	$arr = f_MFetch( $res );
	if( !$arr ) $player_has = 0;
	else $player_has = $arr[0];
	
	$res = f_MQuery( "SELECT number FROM trade_goods WHERE player_id = $player->player_id AND good_type = 1 AND good_id = $item_id" );
	$arr = f_MFetch( $res );
	if( !$arr ) $q = 0;
	else $q = $arr[0];
	
	$good_type = 1;
}
if (checkCanDrop($item_id))
if( $number > 0 )
{
	if( $q + $number <= $player_has )
	{
		if( $arr ) f_MQuery( "UPDATE trade_goods SET number = number + $number WHERE player_id = $player->player_id AND good_type = $good_type AND good_id = $item_id" );
		else f_MQuery( "INSERT INTO trade_goods ( player_id, good_type, good_id, number ) VALUES( $player->player_id, $good_type, $item_id, $number )" );
	}
}
else if( $number < 0 )
{
	$number = - $number;
	if( $q >= $number )
	{
		if( $q > $number ) f_MQuery( "UPDATE trade_goods SET number = number - $number WHERE player_id = $player->player_id AND good_type = $good_type AND good_id = $item_id" );
		else f_MQuery( "DELETE FROM trade_goods WHERE player_id = $player->player_id AND good_type = $good_type AND good_id = $item_id" );
	}
}

?>

<script>
location.href='trade_ref.php';
</script>
