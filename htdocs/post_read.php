<?

header("Content-type: text/html; charset=windows-1251");

include_once( 'no_cache.php' );
include_once( 'functions.php' );
include_once( 'player.php' );
require_once( 'textedit.php' );

f_MConnect( );

if( !check_cookie( ) )
	die( "�������� ��������� Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$id = $_GET['id'];
settype( $id, 'integer' );

$res = f_MQuery( "SELECT * FROM post WHERE receiver_id={$player->player_id} AND entry_id={$id}" );
$arr = f_MFetch( $res );

if( !$arr ) die( "alert( '� ��� ��� ������ ������' );" );

f_MQuery( "UPDATE post SET readed=1 WHERE entry_id=$id" );

$title = strip_tags( $arr['title'] );
$text = $arr['content'];

// ����� �������������� ����, ������ � ������ ���������� ��������. ��� $title ��������� ��� � ���� �� �����.
if( $arr['sender_id'] != 69055 )
	$text = post_process_str( htmlspecialchars( $text, ENT_QUOTES ) );

$title = addslashes( str_replace( "\n", '<br />', str_replace( "\r", '', $title ) ) );
$text = str_replace( "\n", '<br />', str_replace( "\r", '', $text ) );

$st = "<b>$title</b><br>$text<br><li><a href=\"javascript:deleteLetter($id)\">�������</a><br><br>";

$ares = f_MQuery( "SELECT items.name, post_items.number FROM items INNER JOIN post_items ON items.item_id=post_items.item_id WHERE post_items.entry_id=$id" );

if( $arr['money'] > 0 || f_MNum( $ares ) )
{
	$st .= "<b>��������:</b><br>";
	if( $arr['money'] > 0 ) $st .= "[$arr[money]] �������<br>";
	while( $aarr = f_MFetch( $ares ) )
	{
		$st .= "[$aarr[1]] $aarr[0]<br>"; 
	}
    if( $arr['np'] > 0 )
    {
    	$st .= "<small>������ ���������� ���������� ��������, ����� ��� ��������, ���������� ���������</small> <img src=images/money.gif width=11 height=11> <b>$arr[np]</b><br>";
    	$st .= "<small>���� �������� �� �������, ��� ������������� �������� ����������� ����� ".my_time_str( $arr['deadline'] - time( ) )."</small><br>";

    	$st .= "<li><a href=\"javascript:takeAtt($id)\">��������� � �����</a><br><li><a href=\"javascript:refuseAtt($id)\">����������</a>";
    }
    else $st .= "<li><a href=\"javascript:takeAtt($id)\">�������</a>";
}

echo "document.getElementById( 'qdescr' ).innerHTML = '$st';";
echo "document.getElementById( 'ltr$id' ).innerHTML = '$title';";

?>
