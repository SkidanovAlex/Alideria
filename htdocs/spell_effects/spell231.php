$val = 1/6*$sdk->get_attrib_value($sdk->myself, 0);

if( $sdk->get_turn_number( ) == 0 ) $sdk->damage( $sdk->opponent, 200*$val );
else if( $sdk->get_turn_number( ) == 1 ) $sdk->damage( $sdk->opponent, 150*$val );
else $sdk->damage( $sdk->opponent, 84*$val );

