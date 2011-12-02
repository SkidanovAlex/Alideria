<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once('../player.php');
include_once( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

?>

<form action='admin_combat_cheat.php' method=GET>
<input type=text name='comid'>
<input type=submit value=Ok>
</form>

<?

if (isset($_GET['comid']))
{
	$comid = $_GET['comid'];
	echo "Немного инфы по бою ".$_POST['comid']."&nbsp;<a href='admin_combat_cheat.php?comid=".$comid."'>Обновить</a><br><br>";
	$res = f_MQuery("SELECT ch.login, ca.name, ca.genre, cp.side FROM combat_players as cp, cards as ca, characters as ch WHERE cp.card_id=ca.card_id AND ch.player_id=cp.player_id AND cp.combat_id=".$comid);
	echo "<table border=1>";
	while ($arr=f_MFetch($res))
	{
		echo "<tr><td>";
		echo $arr[0]."</td><td>".$arr[3]."</td><td>";
		if ($arr[2]==-1)
			echo " <font color=gray>".$arr[1]."</font>";
		if ($arr[2]==0)
			echo " <font color=blue>".$arr[1]."</font>";
		if ($arr[2]==1)
			echo " <font color=green>".$arr[1]."</font>";
		if ($arr[2]==2)
			echo " <font color=red>".$arr[1]."</font>";
		echo "</td></tr>";
	}
	echo "</table>";
}

f_MClose( );

?>