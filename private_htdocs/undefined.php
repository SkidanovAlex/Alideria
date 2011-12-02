<?
	// Библиотечки
	require_once( 'time_functions.php' );
	require_once( 'functions.php' );
	require_once( 'player.php' );
	
	// Коннект к БД
	f_MConnect( );
	
	$undefined = new Player( 286464 );
	$undefined->syst2( 'Test' );
	$Rein = new Player(6825);
	$Rein->syst2('Test');
?>