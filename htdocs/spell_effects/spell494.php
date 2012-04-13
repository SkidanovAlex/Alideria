$val = 50;
$sdk->damage( $sdk->myself, $sdk->get_attrib_value( $sdk->myself, 1 ) * $val / 100 );
$sdk->aura( $sdk->opponent, 60, 3 );
}