<?

function GetSubAttribList( $attr_id )
{
	$res = f_MQuery( "SELECT attribute_id FROM attributes WHERE parent=$attr_id" );
	$moo = Array( );
	while( $arr = f_MFetch( $res ) )
		$moo[] = $arr[attribute_id];
		
	return $moo;
}

?>
