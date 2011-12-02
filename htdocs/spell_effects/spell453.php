$value = 350;
$pid = $sdk->me->player->player_id;
$res = f_MQuery( "SELECT c.genre FROM cards as c INNER JOIN combat_players as p ON c.card_id=p.lcard WHERE p.player_id=$pid" );
$arr = f_MFetch( $res );
if( $arr && $arr[0] == 2 ) $sdk->damage(  $sdk->opponent, $value );
else if( $arr && $arr[0] == 1 ) $sdk->heal( $sdk->myself, $value );
else $sdk->damage( $sdk->myself, $value );

if( ( $sdk->has_creature_in_slot( $sdk->myself, $sdk->current_creature ) ) == (false) ) {
	$sdk->summon( 2023, $sdk->current_creature );
} else {
	$sdk->alter_creature_attrib( $sdk->myself, $sdk->current_creature, $sdk->creature_attack, 20 );
	$sdk->alter_creature_attrib( $sdk->myself, $sdk->current_creature, $sdk->creature_defence, 10 );
}

