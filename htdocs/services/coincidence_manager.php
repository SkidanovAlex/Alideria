<?
include_once( "../functions.php" );
include_js("../js/ajax.js");

f_MConnect( );

include( "ranks_header.php" );

?>

<html>
<head>
<script src="../js/clans.php"></script>
<script src="../js/ii_a.js"></script>
<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">
</head>
<body>
<script>
function setCheck(id)
{
	var ch;
	if(document.getElementById('ch_'+id).checked)
		ch = 1;
	else
		ch = 0;
	query('coincidence_manager_ajax.php', id+'|'+ch);
}
</script>
<?
echo "<table border=1>";
echo "<tr>";
echo "<td>";
echo "Player 1";
echo "</td>";
echo "<td>";
echo "Player 2";
echo "</td>";
echo "<td>";
echo "IP or ...";
echo "</td>";
echo "<td>";
echo "Checked";
echo "</td>";
echo "</tr>";
$res = f_MQuery("SELECT * FROM coincidence_ip ORDER BY ip");
while($arr = f_MFetch($res))
{
	echo "<tr>";
	$pl_1 = new Player($arr[1]);
	$pl_2 = new Player($arr[2]);
	echo "<td><script>document.write(".$pl_1->Nick().");</script></td>";
	echo "<td><script>document.write(".$pl_2->Nick().");</script></td>";
	if($player && $player->Rank() == 1)
		echo "<td>".$arr[3]."</td>";
	else
		echo "<td>".md5($arr[3])."</td>";
	echo "<td align=center><input onclick='setCheck(".$arr[0].")' type=checkbox id=ch_".$arr[0].($arr[4]?' checked':'')."></td>";
	echo "</tr>";
}
?>
</table>
</body>
</html>