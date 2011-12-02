$coef = 60;

$val = $coef * $sdk->me->turns_unsuccessfull;
$sdk->damage( $sdk->opponent, $val );