$a = $sdk->get_attrib_value( $sdk->myself, 1 ) * $sdk->get_attrib_value( $sdk->opponent, 101 ) / $sdk->get_attrib_value( $sdk->myself, 101 );
$b = $sdk->get_attrib_value( $sdk->opponent, 1 ) * $sdk->get_attrib_value( $sdk->myself, 101 ) / $sdk->get_attrib_value( $sdk->opponent, 101 );

$sdk->set_attrib( $sdk->opponent, 1, 2 * $a );
$sdk->set_attrib( $sdk->myself, 1, $b );
