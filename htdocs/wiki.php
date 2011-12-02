<?

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "arrays.php" );
include_once( "skin.php" );

f_MConnect( );

if( !check_cookie( ) )
	$me = new Player( 0 );
else $me = new Player( $HTTP_COOKIE_VARS['c_id'] );	

$mid_php = 1;	


$mode = 0;

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<link rel="icon" type="image/png" href="favicon.png">
<link href="style2.css" rel="stylesheet" type="text/css">
<head><title>Моя Алидерия</title></head>
<body style='background:#e0c3a0;'>

<?

include_js( "js/skin.js" );

?>

<center>

<table width=950><colgroup><col width=250><col width=700>

<tr><td colspan=2>

<table width=100%><tr><td rowspan=2><div style='position:relative;top:0px;left:0px;'><div style='position:absolute;top:1px;left:1px;'><font color=#565656 size=+3><b>Вики-Алидерия</b></font></div><div style='position:relative;top:0px;left:0px;'><font size=+3><b>Вики-Алидерия</b></font></div></div></td><td align=right valign=top>Поиск: <input class=m_btn id=srch_find></td></tr><tr><td align=right valign=bottom>
<a href=wiki.php>Править</a> &#183; <a href=wiki.php>История</a> &#183; <a href=wiki.php>Обсудить</a>
</td></tr>

</td></tr>

<tr>
<td valign=top width=250><script>FUlt();</script><table width=100%><tr><td><script>FLUl();</script>



<script>FLL();</script></td></tr></table><script>FL();</script></td>
<td valign=top width=700><script>FUlt();</script><table width=100%><tr><td><script>FLUl();</script>

<?

	foreach( $_GET as $a=>$b )
	{
		$title = trim(htmlspecialchars($a,ENT_QUOTES));
	}
	if( !$title ) $title = "Главная";

	mysql_select_db( "alideri2_wiki" );

	$article_id = f_MValue( "SELECT article_id FROM articles WHERE LOWER(title)=LOWER(\"$title\")" );
	if( !$article_id )
	{
		echo "<h1>Статьи &laquo;$title&raquo; в настоящий момент не существует</h1>";
		echo "Вы можете:<ul>";
		echo "<li><a href=wiki.php?do=create&title=$title>Создать статью с заголовком &laquo;$title&raquo;</a>";
		echo "<li><a href=wiki.php?do=search&q=$title>Искать в Вики-Алидерии &laquo;$title&raquo;</a>";
		echo "</ul>";
	}

	mysql_select_db( "alideri2_alideria" );

?>

<script>FLL();</script></td></tr></table><script>FL();</script></td>
</tr></table>

</center>
