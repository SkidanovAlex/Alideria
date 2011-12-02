$val = mt_rand( 10, 20 );
$sdk->damage( $sdk->opponent, $sdk->get_attrib_value( $sdk->opponent, 1 ) * $val / 100 );