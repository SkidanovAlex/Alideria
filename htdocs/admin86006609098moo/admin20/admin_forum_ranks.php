<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

// ����� ������� ����������� ��� �� � forum_inner.php
$forum_room_names = Array( 22 => "����� ��� �������� ����������", 21 => "The TOP SECRET project IDE!", 0 => "������� �� �������", 1 => "������ ����", 2 => "������ �� �������������", 3 => "����� �������", 4 => "����������� �������", 5 => "���� � �����������", 6 => "���������", 7 => "��������� �������", 8 => "���������� �������", 10 => "���������� - �������", 9 => "���������� - �������", 20 => "�������� ������ �����������" );

f_MConnect( );

include( 'admin_header.php' );

?>
<a href=index.php>�� �������</a><br>
<b>����� ������� �� ������</b><br>

<?

function create_select( $nm, $arr, $val )
{
	$st = "<select name='$nm'>";
	
	foreach( $arr as $key=>$value )
	{
		$st .= "<option value=$key";
		if( $key == $val ) $st .= " selected";
		$st .= ">$value" ;
	}
	
	$st .= '</select>';
	
	return $st;
}

$ok = false;
if( $HTTP_GET_VARS[login] )
{
	$res = f_MQuery( "SELECT * FROM characters WHERE login='$HTTP_GET_VARS[login]'" );
	$arr = f_MFetch( $res );
	if( !$arr )
	{
		print( "��� ������ ������" );
	}
	else
	{
		$ok = true;
		$login = $HTTP_GET_VARS[login];
		$player_id = $arr[player_id];
		
		if( isset( $HTTP_GET_VARS[add] ) )
		{
			$tm = time( );
			f_MQuery( "INSERT INTO forum_ranks ( player_id, room_id ) VALUES ( $player_id, $HTTP_GET_VARS[add] )" );
		}
		if( isset( $HTTP_GET_VARS[del] ) )
		{
			f_MQuery( "DELETE FROM forum_ranks WHERE player_id = $player_id AND room_id = $HTTP_GET_VARS[del]" );
		}

		print( "�������� $login<br><br>" );
		print( "<b>����� ������������ �������:</b><br>" );
		$has_quests = false;
		$res = f_MQuery( "SELECT * FROM forum_ranks WHERE player_id = $player_id" );
		while( $arr = f_MFetch( $res ) )
		{
			print( "<b>{$forum_room_names[$arr[room_id]]}</b> <a href=admin_forum_ranks.php?login=$login&del=$arr[room_id]>�������� ����� �������������</a><br>" );
			$has_quests = true;
		}
		if( !$has_quests ) print( "<i>��� ���� ������������� �� ����� �������</i><br>" );
		print( "<br>" );
		
		print( "�������� ����� ������������ �������:<br>" );
		print( "<form action=admin_forum_ranks.php method=get>" );
		print( "<input type=hidden name=login value='$login'>" );
		print( create_select( "add", $forum_room_names, 2 ) );
		print( "<input type=submit value='��������'></form>" );
	}
}

if( !$ok )
{
?>

<form action=admin_forum_ranks.php method=get>
����� ���������: <input name=login><input type=submit value='�������'>
</form>

<?
}

/*
?>

<table>
<form action=admin_triggers.php method=get>
<tr><td>���� ��������: </td><td><input type=text name=trigger_id class=m_btn></td></tr>
<tr><td>��� �������? </td><td><select name=set><option value=1>����������<option value=0>��������</select></td></tr>
<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=�������></td></tr>
</form>
</table>

<?
*/

f_MClose( );

?>

