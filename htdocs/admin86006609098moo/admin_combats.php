<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once('../player.php');
include_once( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

if (isset($_GET['del']) && isset($_GET['id']))
{
	$cid = $_GET['id'];
	if (f_MNum(f_MQuery("SELECT * FROM combat_players WHERE ai=0 AND combat_id=".$cid)) == 0 )
	{
		f_MQuery("DELETE FROM combat_players WHERE combat_id=".$cid);
		f_MQuery("DELETE FROM combats WHERE combat_id=".$cid);
	}
}

echo "<a href=index.php>На главную</a><br><br>";
echo "Немного инфы по текущим боям&nbsp;<a href='admin_combats.php'>Обновить</a><br><br>";
echo "<table border=1>";
echo "<tr><td>ID битвы</td><td>Локация</td><td>Место</td><td>Левая сторона</td><td>Правая сторона</td><td>Номер текущего хода</td><td>Время последнего хода</td></tr>";
$res = f_MQuery("SELECT * FROM combats ORDER BY combat_id");

while ($arr = f_MFetch($res))
{
	$isHuman = false;
	echo "<tr><td><a href='../combat_log.php?id=$arr[0]' target=_blank>$arr[0]</a></td><td>".$arr['location']."</td><td>".$arr['place']."</td>";
	$res1 = f_MQuery("SELECT * FROM combat_players WHERE combat_id=$arr[0] AND side=0");
	echo "<td>";
	while ($arr1 = f_MFetch($res1))
	{
		if ($arr1['ai'] == 0) $isHuman=true;
		$plr = new Player($arr1[1]);
		echo "<a href='../player_info.php?nick=$plr->login' target=_blank>$plr->login</a> => $arr1[4] => $arr1[7] => $arr1[8] <br>";
	}
	echo "</td>";
	$res1 = f_MQuery("SELECT * FROM combat_players WHERE combat_id=$arr[0] AND side=1");
	echo "<td>";
	while ($arr1 = f_MFetch($res1))
	{
		if ($arr1['ai'] == 0) $isHuman=true;
		$plr = new Player($arr1[1]);
		echo "<a href='../player_info.php?nick=$plr->login' target=_blank>$plr->login</a> => $arr1[4] => $arr1[7] => $arr1[8] <br>";
	}
	echo "</td><td>$arr[5]</td><td>".date( 'd.m.Y H:i:s', $arr[1] )."</td>";
	if (!$isHuman && time() > $arr['last_turn_made'] + 90)
		echo "<td><a href='admin_combats.php?del=1&id={$arr[0]}'>Удалить</a></td>";
	echo "</tr>";

}

echo "</table><br>";

f_MClose( );

?>