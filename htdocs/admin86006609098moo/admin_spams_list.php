<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['del'] ) )
{
    $del = $HTTP_GET_VARS['del'];
    f_MQuery("DELETE FROM spams WHERE spam_id=$del");
}

if (isset( $HTTP_GET_VARS['val'] ))
{
	$val = $_GET['val'];
	if ($val != "")
		f_MQuery("INSERT INTO spams (spam_name) VALUES ('$val')");
}
echo "<a href=index.php>�� �������</a><br><br>";

?>

������� ���������� ������(����������� ����� / �����������).<br>
������ �����������. ��� �� ����-������ � ������ ������-�� �� �������.<br>
����� ����� ����������� <b>���������, ��� �� � ��������� ��������� ������ �����.</b><br>
<table>
<form action='admin_spams_list.php' method=get>
<tr><td>���� ������:</td><td><input type=text name=val></td></tr>
<tr><td><input type=submit value=��������></td></tr>
</form>
</table>

<?
echo "<table>";
echo "<tr><td>���� ������</td><td>�������?</td></tr>";
$res = f_MQuery("SELECT * FROM spams ORDER BY spam_name");
while ($arr = mysql_fetch_array($res))
{
    echo "<tr><td>$arr[1]</td><td><a href='admin_spams_list.php?del=".$arr[0]."'>�������</a></td></tr>";
}
echo "</table><br>";

f_MClose( );

?>