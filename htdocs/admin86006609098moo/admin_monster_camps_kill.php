<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['id'] ))
{
    $id = $HTTP_GET_VARS['id'];
    f_MQuery("DELETE FROM forest_monster_camps WHERE combat_id=$id");
}
echo "<a href=index.php>�� �������</a><br><br>";
echo "<table>";
echo "<tr><td>ID ���</td></tr>";
$res = f_MQuery("SELECT combat_id FROM forest_monster_camps");
$arr = mysql_fetch_array($res);
while ($arr)
{
    echo "<tr><td>$arr[0]</td></tr>";
    $arr = mysql_fetch_array($res);
}
echo "</table><br>";

f_MClose( );

?>

<table>
<form action='admin_monster_camps_kill.php' method=get>
<tr><td>ID ���:</td><td><input type=text name=id></td></tr>
<tr><td><input type=submit value=������></td></tr>
</form>
</table>
