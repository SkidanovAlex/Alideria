$val = 3;
$val1 =mt_rand(0,2);
if( $val1== 0 )$val=5;
else if ( $val1==1 )$val=7;

$sdk->damage( $sdk->opponent, $sdk->get_attrib_value( $sdk->myself, 1 ) / $val );