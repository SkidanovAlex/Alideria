$sdk->alter_attrib( $sdk->opponent, 132, -3000 );
$sdk->alter_attrib( $sdk->opponent, 142, -3000 );
$sdk->alter_attrib( $sdk->opponent, 152, -3000 );

$val = 500;

$sdk->alter_attrib( $sdk->enemies, 140, 0 - $val );
$sdk->alter_attrib( $sdk->enemies, 150, 0 - $val );
$sdk->alter_attrib( $sdk->enemies, 130, 0 - $val );

$val = 300;
$sdk->alter_attrib( $sdk->myself, 13, $val );
$sdk->alter_attrib( $sdk->myself, 15, $val );
$sdk->alter_attrib( $sdk->myself, 16, $val );