$val =1/5*$sdk->get_attrib_value($sdk->myself, 0);

if( $sdk->has_creature_in_slot( $sdk->opponent, $sdk->creature_slot_1 ) )
    $sdk->kill_creature( $sdk->opponent, $sdk->creature_slot_1 );
else if( $sdk->has_creature_in_slot( $sdk->opponent, $sdk->creature_slot_2 ) )
    $sdk->kill_creature( $sdk->opponent, $sdk->creature_slot_2 );
else if( $sdk->has_creature_in_slot( $sdk->opponent, $sdk->creature_slot_3 ) )
    $sdk->kill_creature( $sdk->opponent, $sdk->creature_slot_3 );
else $sdk->damage( $sdk->opponent, 40*$val );