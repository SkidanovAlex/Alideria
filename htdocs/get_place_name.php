<?

function GetPlaceName( $loc, $depth )
{
	if( $loc == 0 && $depth <= 20 || $loc == 4 ) return "Глубина $depth";
	else if( $loc == 5 && $depth == 1 )
	{
		return "Лабиринт";
	}
	else if( $loc == 1 )
	{
		include_once( 'forest_functions.php' );
		$res = f_MQuery( "SELECT tile FROM forest_tiles WHERE location=$loc AND depth=$depth" );
		$arr = f_MFetch( $res );
		if( !$arr ) $arr[0] = 1;
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
