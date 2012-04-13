<?

function LocationAddItems( $loc, $depth, $item_id, $number, $pl_id=0 )
{
	if (!($loc >=100 && $loc <= 200))
	{
		$res = f_MQuery( "SELECT * FROM location_items WHERE location = $loc AND depth = $depth AND item_id = $item_id" );
		if( f_MNum( $res ) ) f_MQuery( "UPDATE location_items SET number = number + $number WHERE location = $loc AND depth = $depth AND item_id = $item_id" );
		else f_MQuery( "INSERT INTO location_items ( location, depth, item_id, number ) VALUES ( $loc, $depth, $item_id, $number )" );
	}
	elseif ($pl_id!=0)
	{
		$cell_num = f_MValue("SELECT cell_num FROM dungeon_players WHERE player_id=".$pl_id);
		$res = f_MQuery("SELECT * FROM dungeon_items WHERE group_number=$depth AND cell_num=$cell_num AND item_id=".$item_id);
		if (f_MNum($res))
			f_MQuery("UPDATE dungeon_items SET number=number+ $number WHERE WHERE group_number=$depth AND cell_num=$cell_num AND item_id=".$item_id);
		else
			f_MQuery("INSERT INTO dungeon_items (group_number, cell_num, item_id, number) VALUES ($depth, $cell_num, $item_id, $number)");
	}
}

?>
