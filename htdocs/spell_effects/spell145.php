$val = 10;
while( true )
{
$sdk->damage( $sdk->enemies, $val );
if( mt_rand( 0, 99 ) < 50 ) break;
$val *= 2;
}