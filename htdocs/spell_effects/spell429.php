$val = $sdk->get_attrib_value($sdk->myself, 0);
$num = mt_rand( 1, 15 );
for( $i = 0; $i < $num; ++ $i )
{
$sdk->damage( $sdk->enemies, 15);
}
$sdk->alter_attrib( $sdk->myself, 142, 50);