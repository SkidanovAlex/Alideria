<?

header("Content-type: text/html; charset=windows-1251");

include_once( "../no_cache.php" );
include_once( "../functions.php" );
include_once( "../player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$res = f_MQuery( "SELECT talk_id FROM player_talks WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( !$arr || $arr[0] != 182 ) die( );

$id = $HTTP_RAW_POST_DATA;

settype( $id, 'integer' );

if( $id < 0 || $id >= 3 ) RaiseError( "Попытка выбрать неверный наперсток" );

$correct = mt_rand( 0, 1 );
if( $correct >= $id ) ++ $correct;
if( mt_rand( 1, 10 ) == 1 )
{
	$correct = $id;
	$player->SetTrigger( 43, 1 );
	f_MQuery( "DELETE FROM player_cooldowns WHERE player_id={$player->player_id} AND spell_id=109" );
	f_MQuery( "UPDATE player_talks SET talk_id=184 WHERE player_id={$player->player_id}" );
}
else f_MQuery( "UPDATE player_talks SET talk_id=185 WHERE player_id={$player->player_id}" );

echo "selectCallBack( $correct );";

?>
