<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">


<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );
include_once( '../guild.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['login'] ) )
{
	$login = $HTTP_GET_VARS['login'];
	$item_id = $HTTP_GET_VARS['item_id'];
	settype( $item_id, 'integer' ) ;
	$where = '';
	if( $item_id != -2 ) $where = " AND item_id = $item_id";

	$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$login'" );
	$arr = f_MFetch( $res );
	if( !$arr ) printf( "<font color=red>��� ������ ������</font><br>" );
	else
	{
		$res = f_MQuery( "SELECT * FROM player_log WHERE player_id = $arr[0] $where ORDER BY entry_id DESC" );
		echo "<table border=1><tr><td><b>�����</b></td><td><b>�������� ����</b></td><td><b>���������</b></td><td><b>����</b></td><td><b>�����</b></td><td><b>��������</b></td></tr>";
		while( $arr = f_MFetch( $res ) )
		{
			if( $arr[item_id] == 0 ) $name = "�������";
			else if( $arr[item_id] == -1 ) $name = "�������";
			else
			{
				$mres = f_MQuery( "SELECT name FROM items WHERE item_id = $arr[item_id]" );
				$marr = f_MFetch( $mres );
				if( !$marr ) $name = "����� #$arr[item_id]";
				else $name = $marr[0];
			}
			$num = $arr[have] - $arr[had];
			if( $arr['type'] == 0 ) $str = "������ <a href=phrase_editor.php?id=$arr[arg1]>����� $arr[arg1]</a>";
			else if( $arr['type'] == 1 )
			{
				if( $arr[arg2] == 0 ) $str = "��������� ����� � ������� {$guilds[$arr[arg1]][0]}";
				else if( $arr[arg2] == 1 ) $str = "��������� ����� � ������� {$guilds[$arr[arg1]][0]} - �������";
				else if( $arr[arg2] == 2 ) $str = "��������� �������� � ������� {$guilds[$arr[arg1]][0]}";
				else $str = "��������� ����� � ������� {$guilds[$arr[arg1]][0]} - ��������� ���";
			}
			else if( $arr['type'] == 2 )
			{
				$mres = f_MQuery( "SELECT login FROM characters WHERE player_id = $arr[arg1]" );
				$marr = f_MFetch( $mres );
				if( !$marr ) $lg = "���-�� ��������� ($arr[arg1])";
				else $lg = "<a href=../player_info.php?nick=$marr[0]>".$marr[0]."</a>";
				$str = "������ � $lg";

			}
			else if( $arr['type'] == 3 )
			{
				$str = "������� �� ����� � ������� $arr[arg1] � ����� $arr[arg2]";
			}
			else if( $arr['type'] == 4 )
			{
				$str = "�������� � ����� � ������� $arr[arg1] � ����� $arr[arg2]";
			}
			else if( $arr['type'] == 5 )
			{
				if( $arr['arg1'] == 0 ) $gm = "������";
				else if(  $arr['arg1'] == 1 ) $gm = "�������";
				else if(  $arr['arg1'] == 2 ) $gm = "�������";
				else if(  $arr['arg1'] == 3 ) $gm = "����";

				$str = "������ - $gm";
			}
			else if( $arr['type'] == 6 )
			{
				if( $arr[arg1] == -1 )  $sn = "���������";
				else
				{
    				$mres = f_MQuery( "SELECT name FROM shops WHERE shop_id = $arr[arg1]" );
    				$marr = f_MFetch( $mres );
    				if( !$marr ) $sn = "�����������";
    				else $sn = $marr[0];
    			}
				$str = "������ � ��������� $sn ($arr[arg1])";
			}
			else if( $arr['type'] == 7 )
			{
				$str = "����� � ������� �� ������� $arr[arg1]";
			}
			else if( $arr['type'] == 8 )
			{
				$str = "������� �� ������ $arr[arg1]";
			}
			else if( $arr['type'] == 9 )
			{
				if( $arr[arg1] == 2 ) $do = " ����� ���������";
				else if( $arr[arg1] == 8 ) $do = " ����� ��������";
				else if( $arr[arg1] == 6 ) $do = " ����� ��������";
				$str = "�������� � ���� - $do ($arr[arg1])";
			}
			else if( $arr['type'] == 10 )
			{
				$str = "������� �� ���������� ��� (����� ������ ������� ���������)";
			}
			else if( $arr['type'] == 11 )
			{
				$str = "�������� � ��������";
			}
			else if( $arr['type'] == 12 )
			{
				$str = "���������";
			}
			else if( $arr['type'] == 13 )
			{
				$str = "����� ��������";
			}
			else if( $arr['type'] == 14 )
			{
				$str = "����� ������";
			}
			else if( $arr['type'] == 15 )
			{
				$str = "������� - ������, ������� ������ ��� ��������� ����";
			}
			else if( $arr['type'] == 16 )
			{
				$str = "����� � ���������";
			}
			else if( $arr['type'] == 17 )
			{
				$str = "������ ���� $arr[arg1] � ���";
			}
			else if( $arr['type'] == 18 )
			{
				$str = "������� � ������";
			}
			else if( $arr['type'] == 19 )
			{
				if( $arr['arg1'] == 0 )
					$str = "�������� �� ����� ".f_MValue( "SELECT login FROM characters WHERE player_id=$arr[arg2]" ). " ( $arr[arg2] )";
				if( $arr['arg1'] == 1 )
					$str = "������� �� ����� ".f_MValue( "SELECT login FROM characters WHERE player_id=$arr[arg2]" ). " ( $arr[arg2] )";
				if( $arr['arg1'] == 2 )
					$str = "�������� �� ���������� ������ ".f_MValue( "SELECT login FROM characters WHERE player_id=$arr[arg2]" ). " ( $arr[arg2] )";
				if( $arr['arg1'] == 3 )
					$str = "��������� ���������������� �� ����� ".f_MValue( "SELECT login FROM characters WHERE player_id=$arr[arg2]" ). " ( $arr[arg2] )";
			}
			else if( $arr['type'] == 20 )
			{
				$str = "������� ������� � NPC";
			}
			else if( $arr['type'] == 21 )
			{
				if( $arr[arg1] == 1000 )
				{
					if( $arr[arg2] == 0 ) $str = "������ ����� $arr[arg3] ��������";
					else if( $arr[arg2] == 1 ) $str = "������� 30 ��� � ������";
					else if( $arr[arg2] == 2 ) $str = "����� ����� ������� ������ $arr[arg3]";
					else if( $arr[arg2] == 3 ) $str = "������ ���";
					else if( $arr[arg2] == 4 ) $str = "������ ���";
					else if( $arr[arg2] == 5 ) $str = "������ ���� ����";
				}
				else $str = "����� ��� ������� ������� $arr[arg1]";
			}
			else if( $arr['type'] == 22 )
			{
				$str = "����� ����� ";
				if( $arr['arg1'] == 0 ) $str .= "SMS";
				else if( $arr['arg1'] == 1 ) $str .= "WM";
			}
			else if( $arr['type'] == 23 )
			{
				$str = "������������ �����";
			}
			else if( $arr['type'] == 24 )
			{
				$str = "�������������� � ����-���� $arr[arg1]";
			}
			else if( $arr['type'] == 25 )
			{
				$str = "����������� ���������";
			}
			else if( $arr['type'] == 26 )
			{
				if( $arr['have'] > $arr['had'] && $arr['arg2'] > 0 ) $str = "������� � �������� � ������ $arr[arg2] � �������� $arr[arg1]";
				else if( $arr['have'] > $arr['had'] )  $str = "������� ������ � �������� $arr[arg1]"; 
				else $str = "������ � �������� $arr[arg1]"; 
			}
			else if( $arr['type'] == 27 )
			{
				$str = "�������� � ���";
			}
			else if( $arr['type'] == 28 )
			{
				$str = "������� �������";
			}
			else if( $arr['type'] == 29 )
			{
				$str = "������� �� ������� ����� ������";
			}
			else if( $arr['type'] == 30 )
				$str = "����� ��� ������� �������� �� ������";
			else if( $arr['type'] == 31 )
				$str = "��������� ���� ���";
			else if( $arr['type'] == 32 )
			{
				if( $arr['have'] < $arr['had'] )
					$str = "������ ����� ������ ����������";
				else $str = "������� � ����������";
			}
			else if( $arr['type'] == 33 )
				$str = "����������, $arr[arg1] ����";
			else if( $arr['type'] == 34 )
				$str = "������ ��������������� ���������� ��� ������ � ��������� ��������";
			else if( $arr['type'] == 35 )
			{
				if( $arr['had'] > $arr['have'] ) $str = "����������� �� ������� ��� �� �����";
				else $str = "���������� ��� �� �����";
			}
			else if( $arr['type'] == 36 )
			{
				$str = "";
				if( $arr['arg1'] == 0 ) $str .= "������ ��������";
				else if( $arr['arg1'] == 1 ) $str .=  "������ ��������";
				else if( $arr['arg1'] == 2 ) $str .=  "������ �������";
				$str .= ", ";
				if( $arr['arg2'] == 0 ) $str .= "����� ������";
				else if( $arr['arg2'] == 1 ) $str .=  "������� ������";
				else if( $arr['arg2'] == 2 ) $str .=  "�������� ������";
				else if( $arr['arg2'] == 3 && $arr['arg1'] == 2 ) $str .=  "�������� ����������";
				else if( $arr['arg2'] == 3 ) $str .=  "������������ ����������";
				else if( $arr['arg2'] == 4 ) $str .=  "������� �����";
			}
			else if( $arr['type'] == 37 )
			{
				$str = "�����";
			}
			else if( $arr['type'] == 38 )
			{
				if( $arr['have'] > $arr['had'] ) $str = "����� ���� �� ������������ ������";
				else $str = "�������� ������� � ������";
			}
			else if( $arr['type'] == 39 )
			{
				$str = "�������� ���� ����� ���������� �� �����";
			}

			else $str = "����������� ������ $arr[type]";
			echo "<tr><td>".date( "d.m.Y H:i:s", $arr['time'] )."</td><td>$name ($arr[item_id])</td><td>$num</td><td>$arr[had]</td><td>$arr[have]</td><td>$str</td></tr>";
		}
		echo "</table>";
	}
}


?>

<a href=index.php>�� �������</a><br>
<b>���� ���� �������� ����� � ����� � ���������</b><br>
<table>
<form action=player_log.php method=get>
<tr><td>����� ���������: </td><td><input type=text name=login value='<?=$login?>' class=m_btn></td></tr>
<tr><td>���� ����: </td><td><input type=text name=item_id class=m_btn value=-2><br>0 - ������, -1 - �������, -2 - ���</td></tr>
<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=��������></td></tr>
</form>
</table>

<?

f_MClose( );


?>
