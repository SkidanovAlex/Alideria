<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">
<head><title>���� ��������</title></head>

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['trigger_id'] ) )
{
	$trigger_id = $HTTP_GET_VARS['trigger_id'];
	$set = $HTTP_GET_VARS['set'];
	$player->SetTrigger( $trigger_id, $set );
	printf( "<font color=blue>�������</font><br>" );
}

?>
<a href=index.php>�� �������</a><br>
<b>������ �� ������ ����������</b><br>

<?
print( '������������� ��������:<br>' );
$res = f_MQuery( "SELECT trigger_id FROM player_triggers WHERE player_id = $player->player_id ORDER BY trigger_id" );
while( $arr = f_MFetch( $res ) ) printf( "$arr[0]<br>" );
printf( "<br>" );

?>

<table>
<form action=admin_triggers.php method=get>
<tr><td>���� ��������: </td><td><input type=text name=trigger_id class=m_btn></td></tr>
<tr><td>��� �������? </td><td><select name=set><option value=1>����������<option value=0>��������</select></td></tr>
<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=�������></td></tr>
</form>
</table>

<?

f_MClose( );

?>

