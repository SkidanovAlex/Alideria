<?

if( !isset( $mid_php ) ) die( );

echo "<b>���� ������</b> - <a href=game.php?order=main>�����</a><br>";

if( 0 == ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_WATCH_LOG ) )
{
	echo( "� ��� ��� ���� �� ������ � ����� ������������� ������. ���� ��� ����� ���, ���������� � ����� ��� ��� ������������.<br><a href=game.php?order=main>�����</a>" );
	return;
}

$page = $_GET['p'];
settype( $page, 'integer' );
if( $page < 0 ) $page = 0;
$start = $page * 20;

$filter = "";
$lnk = "";
$pid = -1;
$act = -1;
if( isset( $_GET['pid'] ) ) 
{
	$pid = (int)$_GET['pid'];
	if( $pid != -1 ) { $filter .= " AND player_id=$pid"; $lnk .= "&pid=$pid"; }
}
if ($pid==-1 && isset($_GET['pname']))
{
	$pid = f_MValue("SELECT player_id FROM characters WHERE login='".$_GET['pname']."'");
	if ($pid > 0)
	{
		$filter .= " AND player_id=$pid"; $lnk .= "&pid=$pid";
	}
	else
		$pid = -1;
}
if( isset( $_GET['act'] ) )
{
	$act = (int)$_GET['act'];
	if( $act != -1 ) { $filter .= " AND action=$act"; $lnk .= "&act=$act"; }
}

$res = f_MQuery( "SELECT * FROM clan_log WHERE clan_id=$clan_id $filter ORDER BY entry_id DESC LIMIT $start, 21" );

if( f_MNum( $res ) == 0 )
{
	echo "<i>���� �����.</i>";
	return;
}

echo "<table><tr><td>";

echo "<table style='border:1px solid black'>";

$i = 0;
while( $i < 20 && $arr = f_MFetch( $res ) )
{                  
	++ $i;
	$plr = new Player( $arr['player_id'] );
	echo "<tr><td>".date( "d.m.Y H:i", $arr['time'] )."</td><td><script>document.write( ".$plr->Nick( )." );</script></td><td>";
	if( $arr['action'] == 1 )
	{
		if( $arr['arg1'] == 1 ) echo "������ &laquo;".$buildings[$arr['arg0']]."&raquo; ��������� � �������";
		else echo "������ &laquo;".$buildings[$arr['arg0']]."&raquo; ������� �� �������";
	}
	else if( $arr['action'] == 2 )
	{
		if( $arr['arg0'] == 1 ) echo '��������� ����� ������� �� �������� ������';
		if( $arr['arg0'] == -1 ) echo '������� ������� �� �������� ������';
		if( $arr['arg0'] == 0 ) echo '�������� ���� �� ������� �� �������� ������';

	}
	else if( $arr['action'] == 3 )
	{
		if( $arr['arg0'] == 1 && $arr['arg1'] == 1 ) echo "��������� ��� ������������� ������ $arr[arg2]";
   		if( $arr['arg0'] == 1 && $arr['arg1'] == 2 ) echo "��������� ��� ������������� ��������� $arr[arg2]";
		if( $arr['arg0'] == -1 && $arr['arg1'] == 1 ) echo "������� ������ $arr[arg2]";
   		if( $arr['arg0'] == -1 && $arr['arg1'] == 2 ) echo "������� ��������� $arr[arg2]";
	}
	else if( $arr['action'] == 4 )
	{
		if( $arr['arg0'] == 1 ) echo "����� ������ $arr[arg1] �������� � <a href=clan_permitions.php?p=$arr[arg2] target=_blank>�����</a> �� <a href=clan_permitions.php?p=$arr[arg3] target=_blank>�����</a>";
		if( $arr['arg0'] == 2 ) echo "����� ��������� $arr[arg1] �������� � <a href=clan_permitions.php?p=$arr[arg2] target=_blank>�����</a> �� <a href=clan_permitions.php?p=$arr[arg3] target=_blank>�����</a>";
	}
	else if( $arr['action'] == 5 )
	{
		$trg = new Player( $arr['arg0'] );
		echo "������ <script>document.write( ".$trg->Nick( )." );</script> ����������� ������: $arr[arg1], ���������: $arr[arg2], ������: $arr[arg3], ��: $arr[arg4]";
	}
	else if( $arr['action'] == 6 )
	{
		$clrs = Array( "�������", "���������", "������", "�����", "�������" );
		$arr2 = f_MFetch( f_MQuery( "SELECT name FROM items WHERE item_id=$arr[arg0]" ) );
		if( $arr[arg1] > 0 )
			echo "��������� �� �����: [$arr[arg1]] $arr2[0] - {$clrs[$arr[arg2]]} �����";
		else
		{
			$arr[arg1] = - $arr[arg1];
			echo "����� �� ������: [$arr[arg1]] $arr2[0] - {$clrs[$arr[arg2]]} �����";
		}
	}
	else if( $arr['action'] == 7 )
	{
		$number = $arr['arg0'];
		if( $number > 0 ) echo "� ����� ��������� $number ��������";
		else
		{
			$number = - $number;
			echo "�� ����� ����� $number ��������";
		}
	}
	else if( $arr['action'] == 8 )
	{
		if( $arr['arg0'] == 0 ) echo "������������� �������� � ��������";
		else echo "������������ $arr[arg1] ������ ���";
	}
	else if( $arr['action'] == 9 )
	{
		$shelves2 = Array( '�������', '���������', '������', '�����', '�������' );
		if( $arr['arg0'] == 1 ) echo "�������� �������� ��� {$shelves2[$arr[arg1]]} �����";
		if( $arr['arg0'] == 2 ) echo "������� �������� ��� {$shelves2[$arr[arg1]]} �����";

	}
	else if( $arr['action'] == 10 )
	{
		$pres = f_MQuery( "SELECT login FROM characters WHERE player_id=$arr[arg0]" );
		$parr = f_MFetch( $pres );
		if( $arr['arg1'] == 1 ) echo "����� $parr[0] ������ � �����";
		if( $arr['arg1'] == 2 ) echo "����� $parr[0] �������� �� ������";

	}
	else if ( $arr['action'] == 100 )
	{
		if ($arr[arg0] == 0)
		{
			echo "������ � ������ �����������: $arr[arg1] �����";
		}
		else
		{
			$clrs = Array( "�������", "���������", "������", "�����", "�������" );
			$arr2 = f_MFetch( f_MQuery( "SELECT name FROM items WHERE item_id=$arr[arg0]" ) );
			echo "������ � ������ �����������: [$arr[arg1]] $arr2[0] - {$clrs[$arr[arg2]]} �����";
		}
	}

	else echo "����������� ��������! ������������ ����� ������!";
	echo "</td></tr>";
}

echo "</table>";

$arr = f_MFetch( $res );
                       
echo "<table width=100%><tr><td align=left>";
if( $page > 0 ) echo "<a href=game.php?order=log&p=".($page-1)."$lnk>���������� ��������</a> ";
else echo "���������� ��������";
echo "</td><td align=right>";
if( $arr ) echo "<a href=game.php?order=log&p=".($page+1)."$lnk>��������� ��������</a> ";
else echo "��������� ��������";
echo "</td></tr></table>";

echo "</td><td valign=top>";

echo "<b>������:</b><br>";
echo "<form action=game.php method=get><input type=hidden name=order value=log>";
echo "<table>";

$pids = array( -1 => "��� ������" );
$res = f_MQuery( "SELECT player_id, login FROM characters WHERE clan_id=$clan_id" );
while( $arr = f_MFetch( $res ) ) $pids[$arr[0]] = $arr[1];

$acts = array( -1 => "��� ������", 1 => "������� ��������", 2 => "������� ������� ������", 3 => "������� ������ � ���������� (����� ����)", 4 => "������� ��������� ����", 5 => "������� ���������� ��������", 6 => "������� ������", 7 => "������� ������������", 8 => "������� ��������" );

echo "<tr><td valign=top>��������:</td><td>".create_select_global( 'pid', $pids, $pid );
echo "<br><input type=text class=edit_box name=pname style='width: 100%'>";
echo "</td></tr>";
echo "<tr><td>����:</td><td>".create_select_global( 'act', $acts, $act )."</td></tr>";
echo "<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=��������></td></tr>";

echo "</table>";
echo "</form>";

echo "</td></tr></table>";

?>
