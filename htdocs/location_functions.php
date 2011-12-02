<?

function LocationAddItems( $loc, $depth, $item_id, $number )
{
	$res = f_MQuery( "SELECT * FROM location_items WHERE location = $loc AND depth = $depth AND item_id = $item_id" );
	if( f_MNum( $res ) ) f_MQuery( "UPDATE location_items SET number = number + $number WHERE location = $loc AND depth = $depth AND item_id = $item_id" );
	else f_MQuery( "INSERT INTO location_items ( location, depth, item_id, number ) VALUES ( $loc, $depth, $item_id, $number )" );
}

?>
