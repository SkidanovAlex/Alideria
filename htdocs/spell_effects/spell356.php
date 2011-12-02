$cutoff = 550;
$val = 450;

$sum = 0;
$sum += $sdk->me->dmg_magic;
$sum += $sdk->me->dmg_spells;
$sum += $sdk->me->dmg_creatures;

if( $sum < $cutoff ) $val = 220;
$sdk->damage( $sdk->opponent, $val );