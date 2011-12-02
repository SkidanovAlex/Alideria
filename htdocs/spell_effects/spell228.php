$sdk->damage( $sdk->opponent, mt_rand( 140, 170 ) );
if( ( $sdk->get_attrib_value( $sdk->opponent, 1 ) ) % ( 5 ) == 0 ) {
	$sdk->damage( $sdk->opponent, 50 );
} else {
}

