<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );

f_MConnect( );

include( 'admin_header.php' );

echo "<a href=index.php>�� �������</a><br><br>";
echo "��������� ��������� ����� ����� ���� �������.<br>";
echo "time()=".time()."<br>";

if (isset($_GET['t1']) && isset($_GET['t2']))
{
	$t1 = time() - 24*3600*((int)$_GET['t1']);
	$t2 = time() - 24*3600*((int)$_GET['t2']);
	$dif_money = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND (type=0 OR type=5 OR (type=6 AND arg1<=20) OR type=10 OR type=11 OR type=12 OR type=17 OR type=18 OR type=20 OR type=21 OR type=28 OR type=29 OR type=31 OR type=32 OR type=34 OR type=40 OR type=41 OR type=42 OR type=1001)");
	echo "������ �������: {$_GET['t1']} ���� �����<br>����� �������: {$_GET['t2']} ���� �����<br>";
	echo "<table border=1>";
	echo "<tr><td>������ ��������-�������</td><td>���������</td></tr>";
	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=0");
	echo "<tr><td>������ �����</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=5");
	echo "<tr><td>���-��������</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=6 AND arg1<=20");
	echo "<tr><td>���������� � ����</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=8");
	echo "<tr><td>������� �� ������</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=10");
	echo "<tr><td>���</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=11");
	echo "<tr><td>��������</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=12");
	echo "<tr><td>��������� �������</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=17");
	echo "<tr><td>������ ���� � ���</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=18");
	echo "<tr><td>�����</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=20");
	echo "<tr><td>������� ������� � ����</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=21");
	echo "<tr><td>������ ���� �����</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=28");
	echo "<tr><td>������� ������</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=29");
	echo "<tr><td>������� �� �����</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=31");
	echo "<tr><td>4-� ���� ���</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=32");
	echo "<tr><td>�������</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=34");
	echo "<tr><td>�� ����, ���� ���������</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=40");
	echo "<tr><td>�����</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=41");
	echo "<tr><td>����� ������</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=42");
	echo "<tr><td>������</td><td>{$dif_}</td></tr>";

	$dif_ = f_MValue("SELECT SUM(have-had) FROM player_log WHERE player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND item_id=0 AND time>=".$t1." AND time<=".$t2." AND type=1001");
	echo "<tr><td>����� ���������</td><td>{$dif_}</td></tr>";

	echo "</table>";
	echo "��������� ���������� ����� �� ������ �����: $dif_money <br>";
}

f_MClose( );

?>

<form method=GET action='admin_money_difs.php'>
������ ���������� �������: <input type=text value=1 name='t1'> ���� �����<br>
����� ���������� �������: <input type=text value=0 name='t2'> ���� �����<br>
<input type=submit value='���������'>
</form>