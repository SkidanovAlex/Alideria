<?

include_once( 'functions.php' );

function alterProfValue( $player_id, $prof_id, $value )
{
	$sub_value = $value;
	$value *= 10;
	$res = f_MQuery( "SELECT * FROM player_profs WHERE player_id = $player_id AND profession_id = $prof_id" );
	if( !f_MNum( $res ) ) f_MQuery( "INSERT INTO player_profs VALUES( $player_id, $prof_id, $value )" );
	else f_MQuery( "UPDATE player_profs SET value = value + $value WHERE player_id = $player_id AND profession_id = $prof_id" );
	
	f_MQuery( "UPDATE player_profs SET value = value - $sub_value WHERE player_id = $player_id AND profession_id <> $prof_id" );
	f_MQuery( "DELETE FROM player_profs WHERE value <= 0" );
}

function getProfValue( $player_id, $prof_id )
{
	$res = f_MQuery( "SELECT * FROM player_profs WHERE player_id = $player_id AND profession_id = $prof_id" );
	$arr = f_MFetch( $res )	;
	if( !$arr ) return 0;
	return $arr['value'];
}

?>
