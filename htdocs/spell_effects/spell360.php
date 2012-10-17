$val = 50;

$v = $sdk->get_attrib_value($sdk->opponent, 131);
if($v >= 0)
$sdk->alter_attrib( $sdk->opponent, 131, 0 - $val );
else
$sdk->alter_attrib( $sdk->myself, 131, $val );

$v = $sdk->get_attrib_value($sdk->opponent, 141);
if($v >= 0)
$sdk->alter_attrib( $sdk->opponent, 141, 0 - $val );
else
$sdk->alter_attrib( $sdk->myself, 141, $val );

$v = $sdk->get_attrib_value($sdk->opponent, 151);
if($v >= 0)
$sdk->alter_attrib( $sdk->opponent, 151, 0 - $val );
else
$sdk->alter_attrib( $sdk->myself, 151, $val );