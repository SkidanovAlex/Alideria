$val = 50;
if( !$sdk->dcast )
{
$sdk->alter_attrib( $sdk->opponent, 140, 0 - $val );
$sdk->alter_attrib( $sdk->opponent, 150, 0 - $val );
$sdk->alter_attrib( $sdk->opponent, 130, 0 - $val );
} else {
$sdk->set_attrib( $sdk->opponent, 140, 10 );
$sdk->set_attrib( $sdk->opponent, 150, 10 );
$sdk->set_attrib( $sdk->opponent, 130, 10 );
}
