$sdk->damage( $sdk->opponent, mt_rand( 100, 130 ) );
if( ( $sdk->get_attrib_value( $sdk->opponent, 1 ) ) % ( 7 ) == 0 ) {
	$sdk->damage( $sdk->opponent, 50 );
} else {
}

