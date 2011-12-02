<?

include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$res = f_MQuery( "SELECT combat_id, side FROM combat_players WHERE player_id={$player->player_id} AND ready <> 2" );
$arr = f_MFetch( $res );

if( !$arr )
{
	die( "alert( 'Вы уже закончили бой и не можете выбрать цель для призыва.' );" );
}

$combat_id = $arr[0];
$side = $arr[1];

$a = $HTTP_GET_VARS['id'];
settype( $a, "integer" );

if( $a < 0 || $a > 2 ) $a = 0;

f_MQuery( "UPDATE combat_players SET target=$a WHERE player_id={$player->player_id}" );

?>

query('combat_ref.php','ref');
