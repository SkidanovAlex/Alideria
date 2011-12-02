<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once('../player.php');

f_MConnect( );

include( 'admin_header.php' );

?>

<a href=index.php>На главную</a><br><br>


<table>
<form action='admin_drops.php' method=get>
<tr><td>ID локации:</td><td><input type=text name=loc></td></tr>
<tr><td>ID места:</td><td><input type=text name=place></td></tr>
<tr><td><input type=submit value=Смотреть></td></tr>
</form>
</table>

<?
if (isset($_GET['loc']) && isset($_GET['place']))
{
	$loc = (int)$_GET['loc'];
	$place = (int)$_GET['place'];
	$str = "";
	if ($loc != -1)
	{
		$str = " WHERE arg1=$loc";
		if ($place != -1)
			$str .= " AND arg2=$place";
	}
	$res = f_MQuery("SELECT * FROM player_log".$str." ORDER BY time");
	echo "<table border=1>";
	echo "<tr><td>Время</td><td>Имя игрока</td><td>Имя предмета</td><td>Количество</td><td>Действие</td><td>Локация - Место</td></tr>";
	while ($arr = f_MFetch($res))
	{
//		$login = 
	}
}

f_MClose( );
?>