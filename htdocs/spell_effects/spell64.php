<?

$val = mt_rand( 1, 20 );
$sdk->heal( $sdk->myself, 10 );
$sdk->alter_attrib( $sdk->myself, 130, 20 );
$sdk->damage( $sdk->opponent, $val );

/*if( $sdk->get_attrib_value( $sdk->opponent, 1 ) % 3 == 0 ) $sdk->kill( $sdk->opponent );*/

$v1 = mt_rand( 1, 5 );
$v2 = mt_rand( 1, 5 );
$sdk->set_creature_attrib( $sdk->opponent, $sdk->current_creature, $sdk->creature_attack, $v1 );
$sdk->set_creature_attrib( $sdk->opponent, $sdk->current_creature, $sdk->creature_defence, $v2 );

?>
