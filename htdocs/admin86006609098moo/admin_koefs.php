<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['id'] ) && isset( $HTTP_GET_VARS['val'] ) )
{
    $id = $HTTP_GET_VARS['id'];
    $val = $HTTP_GET_VARS['val'];
    f_MQuery("UPDATE koefs SET koef_value=$val WHERE koef_id=$id");
}
echo "<a href=index.php>�� �������</a><br><br>";
echo "<table>";
echo "<tr><td>ID ������������</td><td>�������� ������������</td><td>�������� ������������</td></tr>";
$res = f_MQuery("SELECT * FROM koefs");
$arr = f_MFetch($res);
while ($arr)
{
    echo "<tr><td>$arr[0]</td><td>$arr[1]</td><td>$arr[2]</td></tr>";
    $arr = f_MFetch($res);
}
echo "</table><br>";

f_MClose( );

?>

������� ���������� �����(ID - ����� �����, �������� - ������� ����� ����� �����)
<table>
<form action='admin_koefs.php' method=get>
<tr><td>ID ������������:</td><td><input type=text name=id></td></tr>
<tr><td>�������� ������������:</td><td><input type=text name=val></td></tr>
<tr><td><input type=submit value=��������></td></tr>
</form>
</table>
