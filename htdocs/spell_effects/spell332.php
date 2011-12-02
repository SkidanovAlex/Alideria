$val = 120;
while( true )
{
$sdk->damage( $sdk->opponent, $val );
if( mt_rand( 0, 99 ) < 50 ) break;
}