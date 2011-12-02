<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "magic_functions.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player_id = $HTTP_COOKIE_VARS['c_id'];

$tm = time( );
f_MQuery( "UPDATE online SET last_ping = $tm WHERE player_id = $player_id" );

$res = f_MQuery( "SELECT * FROM magic_players WHERE player_id=$player_id" );
$arr = f_MFetch( $res );
if( !$arr )
{
	f_MQuery( "UNLOCK TABLES" );
	die( "Omg!" );
}

$game_id = $arr['game_id'];
$my_turn = $arr['my_turn'];

// check timeout
f_MQuery( "LOCK TABLES magic_players WRITE, magic WRITE, magic_cards WRITE" );

$res = f_MQuery( "SELECT last_turn_made FROM magic WHERE game_id=$game_id" );
$arr = f_MFetch( $res );

$ltm = $arr['0'];
$tm = time( );

if( !$my_turn && $arr['0'] + 40 < $tm )
{
	$res = f_MQuery( "SELECT count( player_id ) FROM magic_players WHERE game_id=$game_id AND my_turn = 1" );
	$arr = f_MFetch( $res );
	if( $arr[0] == 1 )
	{
		f_MQuery( "UPDATE magic_players SET turn_begin=1, my_turn=1-my_turn WHERE game_id=$game_id" );
		f_MQuery( "UPDATE magic SET last_turn_made = $tm WHERE game_id=$game_id" );
		$ltm = $tm;

		$me = new Magician( $player_id );
		$me->Process( );
		$me->Store( );
	}
}
f_MQuery( "UNLOCK TABLES" );

f_MQuery( "LOCK TABLE magic_animation WRITE" );
$res = f_MQuery ( "SELECT * FROM magic_animation WHERE player_id=$player_id ORDER BY entry_id" );
while( $arr = f_MFetch( $res ) )
	echo "doa( $arr[slot_r], $arr[slot1], $arr[slot2], $arr[card_id], $arr[alpha] );";
f_MQuery( "DELETE FROM magic_animation WHERE player_id=$player_id" );
f_MQuery( "UNLOCK TABLES" );

echo "do_act( \""; refr( $player_id ); echo "\" );";

?>
