$val = 100;
for( $i = 0; $i < 5; ++ $i )
{
if( mt_rand( 1, 99 ) <= 33 ) $sdk->damage( $sdk->myself, $val );
else $sdk->damage( $sdk->opponent, $val );
}