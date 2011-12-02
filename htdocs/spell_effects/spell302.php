if( ( mt_rand( 1, 2 ) ) == ( 1 ) ) {
	$sdk->heal( $sdk->friends, 100 );
} else {
	$sdk->kill( $sdk->myself );
}

