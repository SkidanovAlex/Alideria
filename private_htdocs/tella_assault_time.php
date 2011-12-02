<?
/* @author = Ishamael, undefined
 * @date = 2 марта 2011
 * @about = Штурмы Теллы, скрипт, управляющий результатом штурма 
 */
	require_once( 'time_functions.php' );
	require_once( 'functions.php' );
	require_once( 'player.php' );
	require_once( 'tella_assault.php' );
	
	f_MConnect( );
	
	$undefined = new Player( 286464 );
	$undefined->syst2( 'tella_assault_time == BEGIN' );
	
	$won = 0;
	$lost = 0;
	
	for( $i = 0; $i < 7; ++ $i )
	{
		$mode = ta_check( $i );
		
		if( $mode == 2 )
		{
			++ $won;
		}
		elseif( $mode == 0 )
		{
			++ $lost;
		}
		print " $mode";
	}
	
	if( $won == 7 )
	{
		f_MQuery( 'UPDATE clans SET ta_lost = ta_lost + 1 WHERE ta_lost > 0 AND ta_lost < 3' );

		// Обновляем статус боёв на дефолтный
		f_MQuery( 'UPDATE ta_combats SET combat_id = 0' );
		
		$undefined->syst2( 'tella_assault_time == WON' );
	}
	elseif( $won + $lost == 7 )
	{
		f_MQuery( 'UPDATE clans SET ta_lost = 1 WHERE ta_lost < 10 AND hascamp > 0' );
		f_MQuery( 'UPDATE clans SET ta_lost = 0 WHERE clan_id=56' );
		
		// Обновляем статус боёв на дефолтный
		f_MQuery( 'UPDATE ta_combats SET combat_id = 0' );
		
		$undefined->syst2( 'tella_assault_time == LOST' );
	}
	else
	{
		$undefined->syst2( 'tella_assault_time == SOMETHING STRANGE!' );	
	}

	$undefined->syst2( 'tella_assault_time == END' );
?>