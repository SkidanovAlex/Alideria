if( ( mt_rand( 1, 2 ) ) == ( 1 ) ) {
	$sdk->heal( $sdk->friends, 300 );
} else {
	$sdk->kill( $sdk->myself );
}

