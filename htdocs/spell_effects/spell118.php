$x = mt_rand( 50, 100 );
$sdk->damage( $sdk->opponent, $x );
if( $x >= 80 )
$sdk->damage( $sdk->myself, 10 );

