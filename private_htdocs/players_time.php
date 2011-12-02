<?
require_once("time_functions.php");


require_once( 'functions.php' );

f_MConnect( );
$st = time( );
$tmg = time( ) - 15 * 60;
$tmg2 = time( ) - 30 * 60;
print( "\n" ); print(  date( "d.m.Y H:i:s", $st ) ); print( "\n" );

$res = f_MQuery( "SELECT online.player_id FROM online INNER JOIN characters ON online.player_id=characters.player_id WHERE last_ping < $tmg AND regime <> 104 OR last_ping < $tmg2 AND regime = 104 LIMIT 2" );
echo f_MNum( $res ); print( "\n" );

while( $arr = f_MFetch( $res ) )
{
	// запись в логе
	$player_id = $arr['player_id'];
	$tm = time( );
	if( $tm - $st > 3 ) die( );

	$ress = f_MQuery( "SELECT max( entry_id ) FROM history_logon_logout WHERE player_id = $player_id" );
	$arrr = f_MFetch( $ress );

       ClearCachedValue('USER:' . $player_id . ':scrc_key');



	if( $arrr )
	{

	    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_connect($sock, "127.0.0.1", 1100);
        $msg = "player\nOffline_{$player_id}\n".mt_rand()."\n$player_id\n000000\n000000\n0\n1\n";
        socket_write( $sock, $msg, strlen($msg) ); 
        socket_close( $sock );

		$entry_id = $arrr[0];
		f_MQuery( "UPDATE history_logon_logout SET logout_time = $tm, logout_ip = login_ip, logout_ip_x = login_ip_x, logout_reason = 'Timeout' WHERE entry_id = $entry_id" );
	}
	f_MQuery( "DELETE FROM online WHERE player_id=$player_id" );
	

	
}

f_MClose( );

?>
