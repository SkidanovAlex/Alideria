if( ( mt_rand( 1, 2 ) ) == ( 0 ) ) {
	$sdk->damage( $sdk->opponent, 150 );
} else {
	$sdk->kill( $sdk->myself );
}

