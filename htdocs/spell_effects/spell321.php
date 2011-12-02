if( $sdk->get_attrib_value( $sdk->opponent, 1 ) >= 800 )
{
$sdk->damage( $sdk->opponent, 300 );
$sdk->set_damage_blocking( $sdk->opponent, 1 );
}
$sdk->alter_attrib( $sdk->opponent, 222, 0 - $sdk->get_attrib_value($sdk->opponent, 0) );