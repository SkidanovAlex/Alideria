<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

?>

<a href=index.php>�� �������</a><br><br>

���, ���, ��� �������, ��������<br>
������� �������� ���!!!<br>
<table>
<form action='admin_drops.php' method=get>
<tr><td>ID �������:</td><td><input type=text value=-1 name=loc> -1 ��� ���� �������</td></tr>
<tr><td>ID �����:</td><td><input type=text value=-1 name=place> -1 ��� ���� ����</td></tr>
<tr><td>��� ������ 1:</td><td><input type=text name=pl_id></td></tr>
<tr><td>��� ������ 2:</td><td><input type=text name=pl_id_2></td></tr>
<tr><td>�� ���������:</td><td><input type=text value=180 name=days> ����</td></tr>
<tr><td>��� ��������:</td><td><input type=text value=-1 name=type_items>0 ��� ���� �����, -1 ��� ���� ���������</td></tr>
<tr><td><input type=submit value=��������></td></tr>
</form>
</table>

<?
if (isset($_GET['loc']) && isset($_GET['place']))
{
	$loc = (int)$_GET['loc'];
	$place = (int)$_GET['place'];
	$type_items = (int)$_GET['type_items'];
	if ($type_items >= 0)
		$str_type_items = " AND type=".$type_items;
	else
		$str_type_items = "";
	$str = "";
	if ($loc != -1)
	{
		$str = " AND arg1=$loc";
		if ($place != -1)
			$str .= " AND arg2=$place";
	}
	$pl_id = f_MValue("SELECT player_id FROM characters WHERE login='".$_GET['pl_id']."'");
	if ($pl_id>0)
	{
		$str .= " AND player_id IN ($pl_id";
		$pl_id_2 = f_MValue("SELECT player_id FROM characters WHERE login='".$_GET['pl_id_2']."'");
		if ($pl_id_2 > 0)
			$str .= ", $pl_id_2";
		$str .= ")";
	}
	$ds = (int)$_GET['days'];
	if ($ds > 0)
		$str .= " AND time>".(time()-$ds*24*60*60);
	$res = f_MQuery("SELECT * FROM player_log WHERE (type=3 OR type=4)".$str." ORDER BY time DESC");
	echo "<table border=1>";
	echo "<tr><td>�����</td><td>��� ������ (ID ������)</td><td>��� �������� (ID ��������)</td><td>����������</td><td>��������</td><td>������� - �����</td></tr>";
	while ($arr = f_MFetch($res))
	{

		$login = f_MValue("SELECT login FROM characters WHERE player_id=".$arr[0]);
		$item_name = f_MValue("SELECT name FROM items WHERE item_id=".$arr[1].$str_type_items);
		if ($item_name)
		{
			echo "<tr><td>".date( 'd.m.Y H:i:s', $arr[9] )."</td><td>$login ({$arr[0]})</td><td>$item_name ({$arr[1]})</td><td>";
			if ($arr[4] == 3 )
				echo ($arr[2] - $arr[3])."</td><td>�������";
			else
				echo ($arr[3] - $arr[2])."</td><td>��������";
			echo "</td><td>{$arr[5]} - {$arr[6]}</td>";
			echo "</tr>";
		}
	}
}

f_MClose( );
?>