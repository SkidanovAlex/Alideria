<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

echo "<a href=index.php>На главную</a><br><br>";

if( isset( $HTTP_GET_VARS['id'] ) )
{
	$id = $HTTP_GET_VARS['id'];
	if ($id == 0) $str_query = "SELECT login, money FROM characters WHERE money>0 ORDER BY money DESC";
	elseif ($id == -1) $str_query = "SELECT login, umoney FROM characters WHERE umoney>0 ORDER BY umoney DESC";
	echo "<table>";
	echo "<tr><td>Игрок</td><td>Наличность</td></tr>";
	$res = f_MQuery($str_query);
	$arr = mysql_fetch_array($res);
	$allm = 0;
	while ($arr)
	{
		echo "<tr><td>$arr[0]</td><td>$arr[1]</td></tr>";
		$allm = $allm + $arr[1];
		$arr = mysql_fetch_array($res);
	}
	echo "</table><br>";
	echo "Всего наличности у игроков: $allm<br><br>";
}


f_MClose( );

?>

Вводите корректные числа(ID = 0 для дублонов, ID = -1 для талантов)
<table>
<form action='admin_tals.php' method=get>
<tr><td>ID:</td><td><input type=text name=id></td></tr>
<tr><td><input type=submit value=Окей></td></tr>
</form>
</table>
