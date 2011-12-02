<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['id'] ) && isset( $HTTP_GET_VARS['news'] ) )
{
    $id = $HTTP_GET_VARS['id'];
    $news = $HTTP_GET_VARS['news'];
    f_MQuery("UPDATE news SET news_text='$news' WHERE id=$id");
}
echo "<a href=index.php>На главную</a><br><br>";
echo "<table>";
echo "<tr><td>ID</td><td>Новость</td></tr>";
$res = f_MQuery("SELECT * FROM news");
$arr = mysql_fetch_array($res);
while ($arr)
{
    echo "<tr><td>$arr[0]</td><td>$arr[1]</td></tr>";
    $arr = mysql_fetch_array($res);
}
echo "</table><br>";

f_MClose( );

?>

<table>
<form action='admin_news.php' method=get>
<tr><td>ID:</td><td><input type=text name=id></td></tr>
<tr><td>Новость:</td><td><input type=text name=news></td></tr>
<tr><td><input type=submit value=Изменить></td></tr>
</form>
</table>
