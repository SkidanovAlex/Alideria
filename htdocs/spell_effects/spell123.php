if( ( mt_rand( 1, 3 ) ) == ( 1 ) ) {
	$sdk->damage( $sdk->opponent, 70 );
} else {
	if( ( mt_rand( 1, 2 ) ) == ( 1 ) ) {
		$sdk->alter_attrib( $sdk->opponent, 152, 0 - 15 );
	} else {
		$sdk->alter_attrib( $sdk->opponent, 130, 0 - 30 );
	}
}

