<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "magic_functions.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player_id = $HTTP_COOKIE_VARS['c_id'];

f_MQuery( "LOCK TABLE magic_players WRITE" );
$res = f_MQuery( "SELECT * FROM magic_players WHERE player_id=$player_id" );
$arr = f_MFetch( $res );
$game_id = $arr['game_id'];
$status = $arr['status'];

if( $arr && $arr['status'] != 0 )
{
	f_MQuery( "DELETE FROM magic_players WHERE player_id=$player_id" );
	f_MQuery( "UNLOCK TABLES" );

	$res = f_MQuery( "SELECT count( player_id ) FROM magic_players WHERE player_id=$player_id" );
	$arr = f_MFetch( $res );
	if( $arr[0] == 0 ) 
	{
		f_MQuery( "DELETE FROM magic WHERE game_id=$game_id" );
		f_MQuery( "DELETE FROM magic_cards WHERE game_id=$game_id" );
	}
		

	// проверим турнир
	$res = f_MQuery( "SELECT regime FROM characters WHERE player_id=$player_id" );
	$arr = f_MFetch( $res );
	if( $arr[0] == 111 )
	{
    	if( $status == 1 )
    	{
    		$res = f_MQuery( "SELECT tournament_id FROM tournament_announcements WHERE status = 4 AND type = 1" );
    		$arr = f_MFetch( $res );
    		$expires = time( ) - 5;
    		f_MQuery( "INSERT INTO tournament_queue( tournament_id, player_id, expires ) VALUES ( $arr[0], $player_id, $expires )" );
    	}
		f_MQuery( "UPDATE characters SET regime=0 WHERE player_id=$player_id" );
		die( "<script>location.href='game.php?rnd=" . mt_rand( ) . "';</script>" );
	}
	else f_MQuery( "UPDATE player_waste SET regime=0 WHERE player_id=$player_id" );

	die( "<script>location.href='waste.php?rnd=" . mt_rand( ) . "';</script>" );
}

?>
