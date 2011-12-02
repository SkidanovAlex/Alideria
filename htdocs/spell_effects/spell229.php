$sdk->damage( $sdk->opponent, mt_rand( 160, 180 ) );
if( ( $sdk->get_attrib_value( $sdk->opponent, 1 ) ) % ( 4 ) == 0 ) {
	$sdk->damage( $sdk->opponent, 50 );
} else {
}

