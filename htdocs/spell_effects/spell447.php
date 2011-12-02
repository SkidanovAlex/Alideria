$val = 42/6*$sdk->get_attrib_value($sdk->myself, 0);

$sdk->alter_attrib( $sdk->myself, 141, $val );
$sdk->aura( $sdk->myself, 17, 20 );