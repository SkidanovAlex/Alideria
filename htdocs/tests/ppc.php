<?
	//echo mktime (16,30, 0, date("n"), date("j"), date("Y") );
	
	// Выводим список боёв
	for( $i = 0; $i < 3; ++ $i )
	{
		$combats = f_MQuery( "SELECT * FROM quest_9m WHERE kind = $kind" );
		while( $combat = f_MFetch( $combats ) )
		{
			print_r( $combat );
			echo '<br />';
		}
	}
?>