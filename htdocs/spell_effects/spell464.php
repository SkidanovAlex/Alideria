$val0 =15+10*$sdk->get_attrib_value($sdk->myself, 0);
$val1=$val0*0.85;
$val2=$sdk->get_attrib_value($sdk->myself, 0)/7.0;
$sdk->damage( $sdk->opponent, (int)$val1);

$sdk->me->player->SetQuestValue( 600, $val2 );
$sdk->aura( $sdk->myself, 58, 20);