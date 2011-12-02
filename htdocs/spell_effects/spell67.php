$sdk->alter_attrib( $sdk->myself, 130, 2 );
$sdk->alter_attrib( $sdk->myself, 132, 5 );
if( ( $sdk->get_attrib_value( $sdk->myself, 130 ) ) > ( 20 ) ) {
	$sdk->damage( $sdk->opponent, $sdk->get_attrib_value( $sdk->myself, 130 ) / 2 );
} else {
}

