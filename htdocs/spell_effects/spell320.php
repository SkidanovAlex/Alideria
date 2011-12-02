$lim = 0.1;
$eps = 1e-7;
$p1 = $sdk->get_attrib_value( $sdk->myself, 1 ) / $sdk->get_attrib_value( $sdk->myself, 101 );
$p2 = $sdk->get_attrib_value( $sdk->opponent, 1 ) / $sdk->get_attrib_value( $sdk->opponent, 101 );
if( $p1 > $lim - $eps && $p2 < 1 - $lim + $eps )
{
$a = $sdk->get_attrib_value( $sdk->myself, 1 ) * $sdk->get_attrib_value( $sdk->opponent, 101 ) / $sdk->get_attrib_value( $sdk->myself, 101 );
$b = $sdk->get_attrib_value( $sdk->opponent, 1 ) * $sdk->get_attrib_value( $sdk->myself, 101 ) / $sdk->get_attrib_value( $sdk->opponent, 101 );

$sdk->set_attrib( $sdk->opponent, 1, (int)$a );
$sdk->set_attrib( $sdk->myself, 1, (int)$b );
}
