<?

if( !$mid_php ) die( );

include_js( "js/items_renderer.js" );
echo "<center><table width=90%><tr><td><script>FLUl();</script><b>���� � ���������</b> - <a href=game.php>��������� � ���������</a>";
echo "<script>\n";
echo "item_err = '� ��������� ��� �� ����� ����� ����.';";
while( $arr = f_MFetch( $res ) )
{
	echo "add_item( $arr[item_id], $arr[type], '$arr[name]', '".itemImage( $arr )."', '".itemFullDescr( $arr )."', $arr[number] );\n";
}
echo "document.write( render_items( false, '' ) );\n";
echo "FLL();</script></td></tr></table></center>\n";


?>
