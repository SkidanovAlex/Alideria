$sdk->damage( $sdk->opponent, mt_rand( 20, 60 ) );
if( ( $sdk->get_attrib_value( $sdk->opponent, 1 ) ) % ( 3 ) == 0 ) {
	$sdk->damage( $sdk->opponent, 45 );
} else {
}

