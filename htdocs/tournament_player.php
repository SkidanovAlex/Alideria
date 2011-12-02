<?

include_once( "no_cache.php" );

?>
<meta http-equiv=Content-Type content="text/html; charset=windows-1251" />
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<link rel="icon" type="image/png" href="favicon.png">
<link href="style2.css" rel="stylesheet" type="text/css">
<?

include_once('player.php');
include_once('functions.php');

if (!isset($_GET['player_id']))
	die();

$player_id = (int)$_GET['player_id'];

if ($player_id <= 0)
	die();

$plr = new Player($player_id);
echo "<center>";

echo "Турниры игрока <b>".$plr->login." </b><br><br>";

echo "<table border=1>";
echo "<tr><td>Дата проведения турнира</td><td>Название турнира</td><td>Призовое место</td></tr>";
$res = f_MQuery("SELECT a.name, a.date, r.* FROM tournament_announcements AS a, tournament_players AS p, tournament_results as r WHERE a.tournament_id = p.tournament_id AND r.tournament_id=a.tournament_id AND a.date<".time()." AND p.player_id =".$player_id." ORDER BY a.date");
while ($arr = f_MFetch($res))
{
	echo "<tr><td>".date( 'd.m.Y', $arr[1])."</td><td><a href='tournament_net.php?id=".$arr[2]."' target=_blank>".$arr[0]."</a></td>";
	$str = "Не в тройке лидеров";
	if ($arr[3] == $player_id) $str = "Первое место";
	if ($arr[4] == $player_id) $str = "Второе место";
	if ($arr[5] == $player_id) $str = "Третье место";
	echo "<td>$str</td></tr>";
}
echo "</table>";
echo "</center>";

?>