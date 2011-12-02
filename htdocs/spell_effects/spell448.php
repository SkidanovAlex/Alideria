$val = 1/6*$sdk->get_attrib_value($sdk->myself, 0);

if( $sdk->get_turn_number( ) == 0 ) $sdk->damage( $sdk->opponent, 450*$val );
else if( $sdk->get_turn_number( ) == 1 ) $sdk->damage( $sdk->opponent, 350*$val );
else if( $sdk->get_turn_number( ) == 2 ) $sdk->damage( $sdk->opponent, 250*$val );
else $sdk->damage( $sdk->opponent, 120*$val );
$sdk->aura( $sdk->myself, 17, 20 );