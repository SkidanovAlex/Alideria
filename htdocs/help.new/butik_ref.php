<?

header("Content-type: text/html; charset=windows-1251");

include( "../no_cache.php" );
include( "../functions.php" );
include( "../player.php" );
include( "../items.php" );

$type = (int)$_GET['type'];
$lvl = (int)$_GET['lvl'];
            
f_MConnect( );
$plr = new Player( 172 );
$stats = $plr->getAllAttrNames( );

f_MConnect( );
$st = '';
$res = f_MQuery( "SELECT * FROM items WHERE type=$type AND level=$lvl AND item_id=parent_id" );
if( !f_MNum( $res ) ) $st = '<i>Нет таких вещей</i>';
else while( $arr = f_MFetch( $res ) )
{
	$st .= " <img style='cursor:pointer;' onclick='do_item(\"$arr[effect]\",\"$arr[req]\",$arr[item_id],\"".addslashes($arr['name'])."\",\"".addslashes(itemFullDescr( $arr ))."\",\"$arr[image]\",$arr[type])' onmousemove='showTooltipW(event,\"".addslashes(itemFullDescr( $arr ))."\",300)' onmouseout='hideTooltip()' src=../images/items/".itemImage( $arr ).">";
}

$st = AddSlashes( $st );
echo "_( 'items' ).innerHTML = '$st';";

?>
