$val = $sdk->get_attrib_value($sdk->myself, 0);
$num = mt_rand( 1, 10 );
for( $i = 0; $i < $num; ++ $i )
{
$sdk->damage( $sdk->opponent, 14);
}