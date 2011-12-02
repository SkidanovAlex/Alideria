<?

include( "../functions.php" );

f_MConnect( );

include( "admin_header.php" );

f_MQuery( "LOCK TABLE characters WRITE, player_items WRITE, items WRITE" );

$res = f_MQuery( "SELECT player_id, login FROM characters" );

print( "<a href=index.php>На главную</a><br><br>" );

while( $arr = f_MFetch( $res ) )
{
	$sum = 0;
	$res2 = f_MQuery( "SELECT items.weight, player_items.number FROM items, player_items WHERE items.item_id = player_items.item_id AND player_items.player_id = $arr[player_id]" );
	while( $arr2 = f_MFetch( $res2 ) )
	{
		$sum += $arr2[0] * $arr2[1];
	}
	echo( "$arr[login]: $sum<br>" );
	f_MQuery( "UPDATE characters SET items_weight = $sum WHERE player_id = $arr[player_id]" );
}

f_MQuery( "UNLOCK TABLES" );

?>
