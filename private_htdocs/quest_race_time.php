<?
set_time_limit(0);
require_once("time_functions.php");



include_once( "quest_race.php" );

if( !checkQuestActivity( ) )
{
	if( mt_rand( 1, 24 ) == 3 && ( (int)date("H") >= 9 && (int)date("H") <= 24 ) )
	{
    	generateQuest( );
    	$msg = displayQuest( );
    	echo $msg;
    	
    	$res = f_MQuery( "SELECT player_id FROM player_triggers WHERE trigger_id=262 ORDER BY player_id" );
    	
       	$plr = new Player( 1249423 );
        $plr->UploadInfoToJavaServer( );

    	while( $arr = f_MFetch( $res ) )
    	{

            $sock = socket_create(AF_INET, SOCK_STREAM, 0);
            socket_connect($sock, "127.0.0.1", 1100);
            $tm = date( "H:i", time( ) );
// echo $arr[0] . '<br>';
            $st = "say\n{$msg}\n1249423\n{$arr[0]}\n-3333\n{$tm}\n";
            socket_write( $sock, $st, strlen($st) ); 
            socket_close( $sock );

    	}

            $sock = socket_create(AF_INET, SOCK_STREAM, 0);
            socket_connect($sock, "127.0.0.1", 1100);
            $tm = date( "H:i", time( ) );
            $st = "say\n{$msg}\n1249423\n0\n0\n{$tm}\n";
            socket_write( $sock, $st, strlen($st) ); 
            socket_close( $sock );
	}
}

?>
