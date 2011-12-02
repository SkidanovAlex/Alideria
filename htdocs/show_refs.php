<?

header("Content-type: text/html; charset=windows-1251");

include_once( "functions.php" );
include_once( "player.php" );

include_js( 'functions.js' );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$res = f_MQuery( "SELECT player_id FROM player_invitations WHERE ref_id=$_COOKIE[c_id]" );

?>
<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">
<?

echo "<center><br><b>Игроки, приглашенные вами</b><br><br>";

if( !f_MNum( $res ) )
{
	echo "<i>Вы еще никого не пригласили в игру.</i><br>";
}

else
{
	echo "<script src=js/clans.php></script><script src=js/ii.js></script><script>";
    while( $arr = f_MFetch( $res ) )
    {
    	$plr = new Player( $arr[0] );
    	echo "document.write( ".$plr->Nick( )." + '<br>' );";
    }

	echo "</script></center>";
}

?>
