<?

include_once( 'functions.php' );
include_once( 'tournament_order_functions.php' );
include_once( 'player.php' );

$id = $_GET['id'];

settype( $id, 'integer' );

f_MConnect( );

$res = f_MQuery( "SELECT name, type FROM tournament_announcements WHERE tournament_id=$id" );
$arr = f_MFetch( $res );
if( !$arr ) die( "Нет такого турнира" );


?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">

<head>
<title>Участники турнира <?=$arr[name]?></title>
</head>

<?

include_js( "js/clans.php" );
include_js( "js/ii.js" );

echo "<center><br>";
if ($arr['type'] != 2)
{
    $res = f_MQuery( "SELECT player_id FROM tournament_players WHERE tournament_id = $id" );
    if( !f_MNum( $res ) ) echo "<i>Никто не подал заявку на участие в турнире</i>";
    else while( $arr = f_MFetch( $res ) )
    {
    	$plr = new Player( $arr[0] );
    	echo "<script>document.write( ".$plr->Nick2( )." );</script><br>";
    }
}
else
{
	include_js('js/skin.js');
	$res = f_MQuery( "SELECT * FROM tournament_group_bets WHERE tournament_id=$id" );
	if (!f_MNum($res)) echo "<i>Никто не записан на этот турнир</i>";
	else
	{
		echo "<table width=365>";
		while ($arr = f_MFetch($res))
		{
			$carr = f_MFetch( f_MQuery( "SELECT name FROM clans WHERE clan_id=$arr[clan_id]" ) );
			echo "<tr><td><script>FLUc();</script><a href=orderpage.php?id=$arr[clan_id] target=_blank>$carr[name]</a><table width=100%>";
			echo showBet($arr, false);
			echo "</table><script>FLL();</script></td></tr>";
		}
		echo "</table>";
	}
}
echo "</center>";

?>
