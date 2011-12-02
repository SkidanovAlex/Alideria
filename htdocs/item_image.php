<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">

<?

include( "functions.php" );
include( "skin.php" );

f_MConnect( );

$id = $_GET['id'];
settype( $id, 'integer' );

$ires = f_MQuery( "SELECT image_large FROM items WHERE item_id = $id" );
$iarr = f_MFetch( $ires );

			print( "<table width=100% height=100%><tr><td align=center valign=middle><table width=500><tr><td>" );
			ScrollTableStart( );
			print( "<img src=images/items/$iarr[image_large] width=480 height=360>" );
			ScrollTableEnd( );
			print( "<script></script></td></tr></table></td></tr></table>" );

?>
