<?

include_once( "../player.php" );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$res = f_MQuery( "SELECT * FROM player_ranks WHERE player_id = {$player->player_id}" );
if( !mysql_num_rows( $res ) ) die( );
$arr = f_MFetch( $res );
if( $arr[rank] != 1 && $arr[rank] != 5 ) die( );

?>
