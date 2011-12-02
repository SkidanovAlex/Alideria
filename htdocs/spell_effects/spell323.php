if( $sdk->get_attrib_value( $sdk->opponent, 1 ) >= 800 )
{
$sdk->damage( $sdk->opponent, 450 );
$sdk->set_damage_blocking( $sdk->opponent, 1 );
}