$val = 1/6*$sdk->get_attrib_value($sdk->myself, 0);
$sdk->alter_attrib( $sdk->myself, 522, (int)(17*$val) );