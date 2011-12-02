if( $sdk->has_creature_in_slot( $sdk->myself, $sdk->current_creature ) )
{
$sdk->kill_creature( $sdk->myself, $sdk->current_creature );
$sdk->damage( $sdk->opponent, 420 );
}