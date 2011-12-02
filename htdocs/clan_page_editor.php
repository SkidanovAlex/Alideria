<?

include_once( "textedit.php" );
include_js( 'js/textedit.js' );

if( !isset( $mid_php ) ) die( );

if( 0 == ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_CHANGE_PAGE ) )
{
	echo( "У вас нет прав работать с этим разделом Ордена<br><a href=game.php?order=main>Назад</a>" );
	return;
}

echo "<b>Страница Ордена</b> - <a href=game.php?order=main>Назад</a><br>";

if( isset( $_GET['del'] ) )
{
	$page_id = $_GET['del'];
	settype( $page_id, 'integer' );
	$res = f_MQuery( "SELECT is_title FROM clan_pages WHERE clan_id=$clan_id AND page_id=$page_id" );
	$arr = f_MFetch( $res );
	if( !$arr ) echo "<font color=darkred>У вашего Ордена нет этой страницы.</font>"; 
	else if( $arr[0] != 0 ) echo "<font color=darkred>Титульную страницу нельзя удалить!</font>";
	else if( playerSpendControlPoint( $clan_id, $player->player_id ) )
	{
		f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 2, -1 )" );
		f_MQuery( "DELETE FROM clan_pages WHERE page_id=$page_id" );
	}
}

else if( $_GET['page'] == 'add' )
{
	f_MQuery( "LOCK TABLE clan_pages WRITE" );
	$res = f_MQuery( "SELECT count( page_id ) FROM clan_pages WHERE clan_id=$clan_id" );
	$arr = f_MFetch( $res );
	if( $arr[0] >= 4 )
	{
		echo "<font color=darkred>У Ордена не может быть больше четырех страниц.</font>";
		f_MQuery( "UNLOCK TABLES" );
	}
	else
	{
		f_MQuery( "UNLOCK TABLES" );
		if( playerSpendControlPoint( $clan_id, $player->player_id ) )
		{
			f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 2, 1 )" );
			f_MQuery( "INSERT INTO clan_pages( clan_id, title, text ) VALUES ( $clan_id, '(Новая)', '' )" );
			$_GET['page'] = mysql_insert_id( );
		}
	}
}

$page_id = $_GET['page'];
settype( $page_id, 'integer' );
$res = f_MQuery( "SELECT * FROM clan_pages WHERE clan_id=$clan_id AND ( page_id=$page_id )" );
$arr = f_MFetch( $res );
if( !$arr ) $arr = f_MFetch( f_MQuery( "SELECT * FROM clan_pages WHERE clan_id=$clan_id ORDER BY page_id" ) );
else
{
	if( isset( $_POST['txt'] ) )
	{
		$txt = trim( HtmlSpecialChars( $_POST['txt'] ) );
		$txt = str_replace( "\n", "<br>", $txt );
		$txt = mysql_real_escape_string( process_str( $txt ) );
		$title = mysql_real_escape_string( trim( HtmlSpecialChars( $_POST['title'] ) ) );

		if( strlen( $title ) < 3 || strlen( $title ) > 20 )
			echo "<font color=darkred>Заголовок страницы должен быть не короче трёх и не больше двадцати символов!";


		else if( playerSpendControlPoint( $clan_id, $player->player_id ) )
		{
			f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 2, 0 )" );
			f_MQuery( "UPDATE clan_pages SET text='$txt', title='$title' WHERE clan_id=$clan_id AND page_id=$page_id" );
            $res = f_MQuery( "SELECT * FROM clan_pages WHERE clan_id=$clan_id AND ( page_id=$page_id )" );
            $arr = f_MFetch( $res );
		}
	}
}
$page_id = $arr['page_id'];
$title = $arr['title'];
$text = $arr['text'];

echo "<table><tr><td><script>FLUl();</script>";

echo "<table><tr><td width=120 height=100% valign=top><script>FUlt();</script>";
$res = f_MQuery( "SELECT * FROM clan_pages WHERE clan_id=$clan_id" );
echo "<table width=100% cellspacing=0 cellpadding=0 border=0>";
while( $arr = f_MFetch( $res ) )
{
	echo "<tr><td>";
	if( $arr['page_id'] == $page_id ) echo "<b>$title</b><br>";
	else echo "<a href=game.php?order=page&page=$arr[page_id]>$arr[title]</a><br>";
	echo "</td><td valign=top align=right><a href='#' onclick='if( confirm( \"Действительно удалить страницу $arr[title]?\" ) ) location.href=\"game.php?order=page&del=$arr[page_id]\"'><img width=11 height=11 alt=Удалить title=Удалить src=images/x.gif border=0></a></td></tr>";
}
echo "</table>";
echo "<br>";
echo "<a href=game.php?order=page&page=add>Добавить</a>";
echo "<script>FL();</script></td><td height=100% valign=top><script>FUct();</script>";
echo "<form name=frm id=frm action=game.php?order=page&page=$page_id method=POST>";
echo "Заголовок: <input type=text name=title class=m_btn value='$title'><br>";
echo insert_text_edit( 'frm', 'txt', process_str_inv( $text ) );
echo "<input type=submit class=s_btn value='Изменить'>";
echo "</form>";
echo "<small>Вставьте на пустую страницу тэг <b>(гильдии)</b> для вывода списка профессий игроков на этой странице.</small>";
echo "<script>FL();</script></td></tr></table>";

echo "<script>FLL();</script></td></tr></table>";

?>
