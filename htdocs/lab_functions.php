<?

function getNextStepInfo( $lab_id, $x, $y, $z, $dir )
{
	$dxs = Array( -1, 0, 1, 0 );
	$dys = Array( 0, -1, 0, 1 );

	$x += $dxs[$dir];
	$y += $dys[$dir];
	$arr = f_MFetch( $res = f_MQuery( "SELECT cell_id FROM lab WHERE lab_id=$lab_id AND x=$x AND y=$y AND z=$z" ) );
	if( !$arr ) { echo "_( 'addinfo' ).innerHTML = '';"; return; }
	$cell_id = $arr[0];
	$arr = f_MFetch( f_MQuery( "SELECT combat_id FROM lab_combats WHERE cell_id = $cell_id" ) );
	if( !$arr ) { echo "_( 'addinfo' ).innerHTML = '';"; return; }
	$st = "";
	$res = f_MQuery( "SELECT player_id FROM combat_players WHERE combat_id=$arr[0]" );
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr[0] );
		$st .= ' + '.$plr->Nick( ).' + "<br>"';
	}
	echo "_( 'addinfo' ).innerHTML = '<br><b>Перед вами идет бой:</b><br>' + ".substr( $st , 2 ).";";
}

?>
