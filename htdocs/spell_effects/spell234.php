$val = 1/6*$sdk->get_attrib_value($sdk->myself, 0);

if( $sdk->get_turn_number( ) == 0 ) $sdk->damage( $sdk->opponent, 350*$val );
else if( $sdk->get_turn_number( ) == 1 ) $sdk->damage( $sdk->opponent, 225*$val );
else $sdk->damage( $sdk->opponent, 102*$val );

