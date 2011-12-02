$sdk->damage( $sdk->opponent, mt_rand( 120, 150 ) );
if( ( $sdk->get_attrib_value( $sdk->opponent, 1 ) ) % ( 6 ) == 0 ) {
	$sdk->damage( $sdk->opponent, 50 );
} else {
}

