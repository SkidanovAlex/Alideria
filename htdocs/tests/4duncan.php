<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1251" />
</head>
<body>
<?
die();
include("../functions.php");
include_once("../arrays.php");
include_once("../forest_functions.php");
include_once("../get_place_name.php");
f_MConnect();
$res=f_MQuery("SELECT mob_id, name, loc, defend_depth FROM mobs");
echo "<table border=1>";
echo "<tr><td>Имя монстра</td><td>Место обитания</td><td>Локация</td></tr>";
while ($arr=f_MFetch($res))
{
if ($arr[3]==1000) continue;
if ($arr[2]==1||$arr[2]==6||$arr[2]==7)
	$nm = $forest_names[$arr[3]];
else
	$nm = GetPlaceName($arr[2], $arr[3]);
echo "<tr><td>$arr[1]</td><td>".$loc_names[$arr[2]]."</td><td>".$nm."</td></tr>";
}
echo "</table>";
?>
</body>
</html>