$sdk->damage( $sdk->opponent, mt_rand( 180, 200 ) );
if( ( $sdk->get_attrib_value( $sdk->opponent, 1 ) ) % ( 3 ) == 0 ) {
	$sdk->damage( $sdk->opponent, 50 );
} else {
}

