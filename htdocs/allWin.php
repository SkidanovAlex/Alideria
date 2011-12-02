<?
	require_once( 'functions.php' );

	f_MConnect( );
	
	$players = f_MQuery( 'SELECT player_id FROM player_triggers WHERE trigger_id = 5000' );
	
	while( $player = f_MFetch( $players ) )
	{
		echo $player['player_id'];
		
		f_MQuery( 'INSERT INTO player_triggers( player_id, trigger_id ) VALUES( '.$player['player_id'].', 5001 )' );
		
		echo '[<span style="font-weight: bold; color: green;">OK</span>]<br />';
	}
?>