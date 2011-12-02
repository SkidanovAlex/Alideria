<?

function PlayerEnterChannel( $pid, $cid )
{
	f_MQuery( "LOCK TABLE ch_channels WRITE" );
	f_MQuery( "DELETE FROM ch_channels WHERE player_id=$pid AND channel_id=$cid" );
	f_MQuery( "INSERT INTO ch_channels ( player_id, channel_id ) VALUES ( $pid, $cid )" );
	f_MQuery( "UNLOCK TABLES" );

	// ---------------------
    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
    socket_connect($sock, "127.0.0.1", 1100);
    $msg = "enter\n{$pid}\n{$cid}\n";
    socket_write( $sock, $msg, strlen($msg) ); 
    socket_close( $sock );
    // ---------------------
}

function PlayerLeaveChannel( $pid, $cid )
{
	f_MQuery( "DELETE FROM ch_channels WHERE player_id=$pid AND channel_id=$cid" );

	// ---------------------
    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
    socket_connect($sock, "127.0.0.1", 1100);
    $msg = "leave\n{$pid}\n{$cid}\n";
    socket_write( $sock, $msg, strlen($msg) ); 
    socket_close( $sock );
    // ---------------------
}

?>
