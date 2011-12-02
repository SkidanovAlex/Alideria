<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$res = f_MQuery( "SELECT combat_id, ready FROM combat_players WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

if( !$arr )
{
	?>
<script>
window.top.closePrivateNamed( 'Бой - Все' );
window.top.closePrivateNamed( 'Бой - Свои' );
window.top.char_ref.location.href='char_ref.php';
</script>

	<?
	die( );
}

if( $arr[1] < 2 )
	die( "<script>location.href='combat.php';</script>" );
	
$player->LeaveCombat( $arr[0] );
$player->RestoreAttribs( );
$player->UploadCombatToJavaServer( );

?>

<script>
window.top.closePrivateNamed( 'Бой - Все' );
window.top.closePrivateNamed( 'Бой - Свои' );
window.top.char_ref.location.href='char_ref.php';
</script>
