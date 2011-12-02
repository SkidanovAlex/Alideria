$sdk->kill( $sdk->opponent );

$val = 300;
$sdk->alter_attrib( $sdk->myself, 13, $val );
$sdk->alter_attrib( $sdk->myself, 15, $val );
$sdk->alter_attrib( $sdk->myself, 16, $val );

$val = -300;
$sdk->alter_attrib( $sdk->enemies, 13, $val );
$sdk->alter_attrib( $sdk->enemies, 15, $val );
$sdk->alter_attrib( $sdk->enemies, 16, $val );