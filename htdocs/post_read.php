<?

header("Content-type: text/html; charset=windows-1251");

include_once( 'no_cache.php' );
include_once( 'functions.php' );
include_once( 'player.php' );
require_once( 'textedit.php' );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$id = $_GET['id'];
settype( $id, 'integer' );

$res = f_MQuery( "SELECT * FROM post WHERE receiver_id={$player->player_id} AND entry_id={$id}" );
$arr = f_MFetch( $res );

if( !$arr ) die( "alert( 'У вас нет такого письма' );" );

f_MQuery( "UPDATE post SET readed=1 WHERE entry_id=$id" );

$title = strip_tags( $arr['title'] );
$text = $arr['content'];

// Здесь обрабатываются теги, смайлы и прочая внутренняя разметка. Для $title обработки нет и быть не может.
if( $arr['sender_id'] != 69055 )
	$text = post_process_str( htmlspecialchars( $text, ENT_QUOTES ) );

$title = addslashes( str_replace( "\n", '<br />', str_replace( "\r", '', $title ) ) );
$text = str_replace( "\n", '<br />', str_replace( "\r", '', $text ) );

$st = "<b>$title</b><br>$text<br><li><a href=\"javascript:deleteLetter($id)\">Удалить</a><br><br>";

$ares = f_MQuery( "SELECT items.name, post_items.number FROM items INNER JOIN post_items ON items.item_id=post_items.item_id WHERE post_items.entry_id=$id" );

if( $arr['money'] > 0 || f_MNum( $ares ) )
{
	$st .= "<b>Вложения:</b><br>";
	if( $arr['money'] > 0 ) $st .= "[$arr[money]] Дублоны<br>";
	while( $aarr = f_MFetch( $ares ) )
	{
		$st .= "[$aarr[1]] $aarr[0]<br>"; 
	}
    if( $arr['np'] > 0 )
    {
    	$st .= "<small>Письмо отправлено наложенным платежом, чтобы его получить, необходимо заплатить</small> <img src=images/money.gif width=11 height=11> <b>$arr[np]</b><br>";
    	$st .= "<small>Если вложение не забрать, оно автоматически вернется отправителю через ".my_time_str( $arr['deadline'] - time( ) )."</small><br>";

    	$st .= "<li><a href=\"javascript:takeAtt($id)\">Заплатить и взять</a><br><li><a href=\"javascript:refuseAtt($id)\">Отказаться</a>";
    }
    else $st .= "<li><a href=\"javascript:takeAtt($id)\">Забрать</a>";
}

echo "document.getElementById( 'qdescr' ).innerHTML = '$st';";
echo "document.getElementById( 'ltr$id' ).innerHTML = '$title';";

?>
