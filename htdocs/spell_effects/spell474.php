$val =mt_rand(0,2);
$val1 = 20;
$sdk->damage( $sdk->opponent, 15+10*$sdk->get_attrib_value($sdk->myself, 0) );

if( $val== 0 ) $sdk->alter_attrib( $sdk->myself, 131, $val1 );
else if( $val==1 ) $sdk->alter_attrib( $sdk->myself, 141, $val1 );
else if( $val==2 ) $sdk->alter_attrib( $sdk->myself, 151, $val1 );