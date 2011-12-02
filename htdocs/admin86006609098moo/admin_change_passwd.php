<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

echo "<a href=index.php>На главную</a><br><br>";
if( isset( $HTTP_GET_VARS['login'] ) && isset( $HTTP_GET_VARS['pwd'] ) )
{
	$login = $HTTP_GET_VARS['login'];
	$pwd = $HTTP_GET_VARS['pwd'];
	$md = md5($pwd);
	
	if (!f_MQuery("UPDATE characters SET pswrddmd5='{$md}' WHERE login='{$login}'")) echo "Ошибка!!!<br>";
	else echo "Пароль изменен!<br>";

}
f_MClose( );

?>

<table>
<form action='admin_change_passwd.php' method=get>
<tr><td>Логин персонажа:</td><td><input type=text name=login></td></tr>
<tr><td>Новый пароль:</td><td><input type=text name=pwd></td></tr>
<tr><td><input type=submit value=Изменить></td></tr>
</form>
</table>
