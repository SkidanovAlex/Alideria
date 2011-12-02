if( ( $sdk->has_creature_in_slot( $sdk->myself, $sdk->current_creature ) ) == (false) ) {
	$sdk->summon( 2024, $sdk->current_creature );
} else {
	if( ( $sdk->has_creature_in_slot( $sdk->myself, $sdk->creature_slot_1 ) ) == (false) ) {
		$sdk->summon( 2024, $sdk->creature_slot_1 );
	} else {
		if( ( $sdk->has_creature_in_slot( $sdk->myself, $sdk->creature_slot_2 ) ) == (false) ) {
			$sdk->summon( 2024, $sdk->creature_slot_2 );
		} else {
			if( ( $sdk->has_creature_in_slot( $sdk->myself, $sdk->creature_slot_3 ) ) == (false) ) {
				$sdk->summon( 2024, $sdk->creature_slot_3 );
			} else {
				$sdk->summon( 2024, $sdk->current_creature );
			}
		}
	}
}