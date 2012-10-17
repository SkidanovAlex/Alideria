if( ( $sdk->has_creature_in_slot( $sdk->myself, $sdk->current_creature ) ) == (false) ) {
	$sdk->summon( 2068, $sdk->current_creature );
} else {
	$sdk->alter_creature_attrib( $sdk->myself, $sdk->current_creature, $sdk->creature_attack, 100 );
	$sdk->alter_creature_attrib( $sdk->myself, $sdk->current_creature, $sdk->creature_defence, 118 );
}