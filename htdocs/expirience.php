<?

function getExp( $level, $wear_level, $turns, $my_lvl, $my_num, $his_lvl, $his_num, $bonus = 0 )
{
	if( $level == 0 ) RaiseError( "Нулевой уровень персонажа" );

	$base_exp = pow( $level, 0.8 ) * 3.0;
	$time_coef = pow( 1 + $turns, 0.5 );
	$wear_coef = 1 + $wear_level / ( 12 * $level );
	$rand_coef = 0.759 + mt_rand( 0, 100 ) * 0.00578;

	$level_coef = 1;

	$my_lvl = $my_lvl / $my_num * pow( 1.2, $my_num );
	$his_lvl = $his_lvl / $his_num * pow( 1.2, $his_num );
	if( $my_lvl - $his_lvl >= 5 ) $level_coef = 0.5;
	else if( $my_lvl - $his_lvl >= 2 ) $level_coef = 0.7;
	else if( $my_lvl - $his_lvl >= 1 ) $level_coef = 0.85;
	else if( $his_lvl - $my_lvl >= 5 ) $level_coef = 2;
	else if( $his_lvl - $my_lvl >= 2 ) $level_coef = 1.5;
	else if( $his_lvl - $my_lvl >= 1 ) $level_coef = 1.25;
	else if( $his_lvl > $my_lvl ) $level_coef = 1.15;

	$exp = $base_exp * $time_coef * $wear_coef * $rand_coef * $level_coef;
	settype( $exp, 'integer' );
	
	return $exp;
}

?>
