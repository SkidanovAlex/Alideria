<?

function AlterProfExp( $player, $value )
{
	$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=3" ) );
	if( $barr[0] ) $value = mt_rand( floor( $value * 1.5 ), ceil( $value * 1.5 ) );

	f_MQuery( "UPDATE characters SET prof_exp = prof_exp + $value WHERE player_id={$player->player_id}" );
	$player->prof_exp += $value;
	return " (+{$value}он)";
}

?>

