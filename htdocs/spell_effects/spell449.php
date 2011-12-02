$val1 = $sdk->get_attrib_value($sdk->myself, 0);//уровень перса
$val = $val1/14;
if( !$sdk->dcast ) $sdk->damage( $sdk->opponent, (int)(300*$val) );
else $sdk->damage( $sdk->opponent, (int)(600*$val) );
$sdk->alter_attrib( $sdk->myself, 222, $val1 );