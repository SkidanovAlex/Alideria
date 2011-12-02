$coef = 50;

$val = $coef * $sdk->me->turns_unsuccessfull;
$sdk->damage( $sdk->opponent, $val );

$sdk->alter_attrib( $sdk->opponent, 222, -1 );
$sdk->alter_attrib( $sdk->enemies, 501, 3 );