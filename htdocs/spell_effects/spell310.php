$val = 1/14*$sdk->get_attrib_value($sdk->myself, 0);
if( !$sdk->dcast ) $sdk->damage( $sdk->opponent, (int)270*$val );
else $sdk->damage( $sdk->opponent, (int)540*$val );