<?
require_once("time_functions.php");


require_once( 'functions.php' );
require_once( 'player.php' );

f_MConnect( );

$res = f_MQuery( "SELECT count( player_id ) FROM online" );
$arr = f_MFetch( $res );

$tm = time( );
f_MQuery( "INSERT INTO online_graph ( timestamp, value ) VALUES( $tm, $arr[0] );" );
f_MQuery( "DELETE FROM player_presents WHERE deadline < $tm" );
f_MQuery( "DELETE FROM premiums WHERE deadline < $tm" );

// сымаем эффекты и мидальки
$plef = f_MQuery( "SELECT player_id, id FROM player_effects WHERE expires < $tm AND expires > -1" );
while( $efid = f_MFetch( $plef ) )
{
	$Player = new Player( $efid['player_id'] );
	$Player->RemoveEffect( $efid['id'] );
}


// Разводим желающих развестись и дождавшихся этого
$wishingToDivorce = f_MQuery( 'SELECT * FROM wishing_to_divorce WHERE divorce_time < '.$tm );
while( $wishing = f_MFetch( $wishingToDivorce ) )
{
	f_MQuery("DELETE FROM player_triggers WHERE (trigger_id>=12000 AND trigger_id<=12005) AND player_id=".$wishing[p1]);
	// Проверяем, участвовала ли пара в Лабиринте Влюблённых
	$labId = f_MValue( 'SELECT id FROM labyrinth_of_love WHERE p0 = '.$wishing[p0].' OR p1 = '.$wishing[p1] );
	if( $labId )
	{
		f_MQuery( 'DELETE FROM labyrinth_of_love WHERE id = '.$labId );
		f_MQuery( 'DELETE FROM lol_hearts WHERE labyrinth_id = '.$labId );
		$pair = f_MFetch( f_MQuery( 'SELECT p0,p1 FROM player_weddings WHERE p0 = '.$wishing[p0].' OR p1 = '.$wishing[p1] ) );
		f_MQuery( 'DELETE FROM lol_players WHERE player_id = '.$pair[p0] );
		f_MQuery( 'DELETE FROM lol_players WHERE player_id = '.$pair[p1] );
	}
}
f_MQuery( "DELETE wishing_to_divorce, player_weddings, player_triggers FROM wishing_to_divorce, player_weddings, player_triggers WHERE ( player_weddings.p0 = wishing_to_divorce.p0 OR player_weddings.p1 = wishing_to_divorce.p1 ) AND wishing_to_divorce.divorce_time < $tm AND ( player_triggers.trigger_id = 2011 AND player_triggers.player_id = player_weddings.p0 )" );
f_MQuery( "DELETE wishing_to_divorce, player_weddings FROM wishing_to_divorce, player_weddings WHERE ( player_weddings.p0 = wishing_to_divorce.p0 OR player_weddings.p1 = wishing_to_divorce.p1 ) AND wishing_to_divorce.divorce_time < $tm" );


$tm = time( ) - 60*60;
f_MQuery( "DELETE FROM waste_bets WHERE timestamp < $tm" );
?>
