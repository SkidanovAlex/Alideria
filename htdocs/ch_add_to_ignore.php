<?
header("Content-type: text/html; charset=windows-1251");

include( "functions.php" );
include( "player.php" );

f_MConnect( );

if( !check_cookie( ) ) die( '/* Неверные настройки Cookie */' );
$player_id = $HTTP_COOKIE_VARS[c_id];

if( isset( $_GET['nick'] ) )
{
	$name = HtmlSpecialChars( $_GET['nick'], ENT_QUOTES );
	$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$name'" );
	$arr = f_MFetch( $res );
	if( !$arr ) ;
	else if( $arr[0] == $player_id ) ;
	else
	{
		// ---------------------
        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_connect($sock, "127.0.0.1", 1100);
        $msg = "ignore\n$player_id\n$arr[0]\n";
        socket_write( $sock, $msg, strlen($msg) ); 
        socket_close( $sock );
        // ---------------------

		f_MQuery( "DELETE FROM ch_ignore WHERE player_id=$player_id AND target=$arr[0]" );
		f_MQuery( "INSERT INTO ch_ignore VALUES( $player_id, $arr[0] )" );
	}
}

?>
