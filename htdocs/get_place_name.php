<?

function GetPlaceName( $loc, $depth )
{
	if( $loc == 0 && $depth <= 20 || $loc == 4 ) return "Глубина $depth";
	else if( $loc == 5 && $depth == 1 )
	{
		return "Лабиринт";
	}
	else if( $loc == 1 || $loc == 6 || $loc == 7 )
	{
		include_once( 'forest_functions.php' );
		$res = f_MQuery( "SELECT tile FROM forest_tiles WHERE location=$loc AND depth=$depth" );
		$arr = f_MFetch( $res );
		if( !$arr )
		{
			if ($loc == 1) $arr[0] = 1;
			if ($loc == 6) $arr[0] = 100;
		}
		return $forest_names[$arr[0]];
	}
	else
	{
		$res = f_MQuery( "SELECT title FROM loc_texts WHERE loc = $loc AND depth = $depth" );
		$arr = f_MFetch( $res );
		return $arr[0];
	}
}

?>
