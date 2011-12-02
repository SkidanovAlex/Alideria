if( $sdk->get_turn_number( ) % 5 == 0 )
{
$sdk->set_attrib( $sdk->opponent, 1, 50 );
$sdk->set_damage_blocking( $sdk->opponent, 1 );
}