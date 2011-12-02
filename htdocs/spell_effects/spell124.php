if( ( $sdk->get_attrib_value( $sdk->myself, 40 ) ) > ( 7 ) ) {
	$sdk->heal( $sdk->myself, 70 );
} else {
}
if( ( $sdk->get_attrib_value( $sdk->myself, 50 ) ) > ( 7 ) ) {
	$sdk->damage( $sdk->opponent, 70 );
} else {
}
if( ( $sdk->get_attrib_value( $sdk->myself, 30 ) ) > ( 7 ) ) {
	$sdk->damage( $sdk->myself, 70 );
} else {
}

