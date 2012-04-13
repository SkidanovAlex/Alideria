<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1251" />
</head>
<body>
<?
include("functions.php");
f_MConnect();
$q=f_MValue("SELECT SUM(value) FROM player_quest_values WHERE value_id=12201");
echo $q."<br>";

$r=f_MQuery("SELECT c.login, w.value FROM characters as c, player_quest_values as w WHERE w.value_id=12201 AND w.player_id=c.player_id ORDER BY w.value DESC");
echo "<table>";
while ($a=f_MFetch($r))
{
echo "<tr><td>$a[0]</td><td>$a[1]</td></tr>";
}
echo "</table>";
?>
</body>
</html>