<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">


<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );
include_once( '../guild.php' );

f_MConnect( );

include( 'admin_header.php' );
?>

<a href=index.php>На главную</a><br>
<p>Статистика крафта</p>

<?
$whr = "";
$tm = time();
$ret = "";

if (isset($_GET['items']))
{
	$items = $_GET['items'];
	if ($items)
	$whr .= " AND l.item_id IN ($items)";
}

if (isset($_GET['only_result']))
{
	if ((int)$_GET['only_result'] == -1)
		$whr .= " AND l.had>l.have";
	elseif ((int)$_GET['only_result'] == 1)
		$whr .= " AND l.had<l.have";
}

if (isset($_GET['t1']) && isset($_GET['t2']))
{
	$t1 = $tm - 3600*24*(int)$_GET['t1'];
	$t2 = $tm - 3600*24*(int)$_GET['t2'];
	$whr .= " AND l.time <= $t1 AND l.time >= $t2";
	echo $whr;
	$res = f_MQuery("SELECT item_id FROM player_log as l WHERE l.type=40".$whr." GROUP BY item_id");
	$ret .= "<table border=1><tr><td colspan=2>Получено в результате крафта или отмены крафта</td></tr>";
	while ($arr = f_MFetch($res))
	{
		if ($arr[0]==0)
		{
			$rnum = f_MQuery("SELECT SUM(-l.had+l.have) FROM player_log as l WHERE l.type=40 AND l.had<l.have".$whr);
			$arrn = f_MFetch($rnum);
			$ret .= "<tr><td>Дублоны</td><td>{$arrn[0]}</td></tr>";
		}
		else
		{
//			echo "<br>SELECT SUM(-l.had+l.have) FROM player_log as l WHERE l.type=40 AND l.had < l.have AND l.item_id={$arr[0]}".$whr;
			$num = f_MValue("SELECT SUM(-l.had+l.have) FROM player_log as l WHERE l.type=40 AND l.had<l.have AND l.item_id={$arr[0]}".$whr);
			if ($num)
				$ret .= "<tr><td>".f_MValue("SELECT name FROM items WHERE item_id=".$arr[0])."({$arr[0]})</td><td>{$num}</td></tr>";
		}
	}
	$ret .= "</table>";
	
	$res = f_MQuery("SELECT item_id FROM player_log as l WHERE l.type=40".$whr." GROUP BY item_id");
	$ret .= "<br><table border=1><tr><td colspan=2>Потрачено в результате крафта</td></tr>";
	while ($arr = f_MFetch($res))
	{
		if ($arr[0]==0)
		{
			$rnum = f_MQuery("SELECT SUM(l.had-l.have) FROM player_log as l WHERE l.type=40 AND l.had>l.have".$whr);
			$arrn = f_MFetch($rnum);
			$ret .= "<tr><td>Дублоны</td><td>{$arrn[0]}</td></tr>";
		}
		else
		{
			$num = f_MValue("SELECT SUM(l.had-l.have) FROM player_log as l WHERE l.type=40 AND l.had>l.have AND l.item_id={$arr[0]}".$whr);
			if ($num)
				$ret .= "<tr><td>".f_MValue("SELECT name FROM items WHERE item_id=".$arr[0])."({$arr[0]})</td><td>{$num}</td></tr>";
		}
	}
	$ret .= "</table>";
	
	echo $ret;
}

f_MClose();

?>

<table>
<form action=admin_craft_stats.php method=get>
<tr><td>ID предметов через запятую, либо оставить пустым для всех предметов: </td><td><input type=text name=items class=m_btn value=<?=$_GET['items']?>></td></tr>
<tr><td>Что отобразить(0 - все, -1 - только потрачено, 1 - только получено): </td><td><input type=text name=only_result class=m_btn value=0><br></td></tr>
<tr><td>Начало расчетного периода дней назад: </td><td><input type=text name=t2 class=m_btn value=1></td></tr>
<tr><td>Конец расчетного периода дней назад: </td><td><input type=text name=t1 class=m_btn value=0></td></tr>
<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=Показать></td></tr>
</form>
</table>