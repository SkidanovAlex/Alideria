<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">

<html>
<head><title>Ордена Алидерии</title></head>
</body>

<?

include( 'functions.php' );
include( 'player.php' );

include_js( 'js/skin.js' );

f_MConnect( );

$res = f_MQuery( "SELECT clans.*, count( player_clans.player_id ) as moo FROM clans INNER JOIN player_clans ON clans.clan_id=player_clans.clan_id WHERE clans.clan_id <> 1 GROUP BY clans.clan_id ORDER BY glory DESC, clan_id" );

echo "<br><br><center><table><tr><td><script>FLUl();</script><table>";
echo "<tr><td>&nbsp;</td><td align=center><b>Название</b></td><td align=center width=60><b>Слава</b></td><td width=60 align=center><b>Членов</b></td><td width=100 align=center><b>Стихия</b></td><td width=100><b>Направленность</b></td><td width=100 align=right><b>Страница</b></td>";
$id = 0;
$last_glory = -1;
while( $arr = f_MFetch( $res ) )
{
	echo "<tr>";
	++ $id;
	if( $arr['glory'] != $last_glory ) echo "<td align=right><b>$id.</b></td>";
	else echo "<td align=center>-</td>";
	$last_glory = $arr['glory'];
	echo "<td><img width=18 height=13 border=0 src=images/clans/$arr[icon]>&nbsp;<b>$arr[name]</b></td>";
	echo "<td align=center><b><font color=darkred>$arr[glory]</font></b></td>";
	echo "<td align=center><a target=_blank href=orderpage.php?id=$arr[clan_id]&page=1>$arr[moo]</a></td>";
	echo "<td align=center>{$elements[$arr[element]]}</td>";
	echo "<td align=center>{$orientations[$arr[orientation]]}</td>";
	echo "<td align=right><a target=_blank href=orderpage.php?id=$arr[clan_id]>Открыть</a></td>";
	echo "</tr>";
}
echo "</table><script>FLL();</script></td></tr></table>";

?>

</body>
</html>
