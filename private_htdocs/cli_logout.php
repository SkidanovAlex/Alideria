<?php
// UPD
if (!((count($argv) > 1) && ((int) $argv[1]) > 0 )) die("no id");

require_once("time_functions.php");

require_once( 'functions.php' );

f_MConnect();
	$player_id = (int) $argv[1];

	$ress = f_MQuery( "SELECT max( entry_id ) FROM history_logon_logout WHERE player_id = $player_id" );
	$arrr = f_MFetch( $ress );

       ClearCachedValue('USER:' . $player_id . ':scrc_key');

$tm = time();

	    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_connect($sock, "127.0.0.1", 1100);
        $msg = "player\nOffline_{$player_id}\n".mt_rand()."\n$player_id\n000000\n000000\n0\n1\n";
        socket_write( $sock, $msg, strlen($msg) ); 
        socket_close( $sock );

		$entry_id = $arrr[0];
	f_MQuery( "UPDATE history_logon_logout SET logout_time = $tm, logout_ip = login_ip, logout_ip_x = login_ip_x, logout_reason = 'Timeout' WHERE entry_id = $entry_id" );
	f_MQuery( "DELETE FROM online WHERE player_id=$player_id" );
	
f_MClose( );

?>


