<?
require_once("time_functions.php");

include_once( 'functions.php' );
include_once( 'combat_ai.php' );

include_once( 'combat_functions.php' );
include_once('combat_turn.php');
    	

f_MConnect( );

// unfreeze combats

$ttm = time( ) - 20;
$res = f_MQuery( "SELECT combat_id FROM combats WHERE turn_done = 1 AND turn_done_when < $ttm" );
while( $arr = f_MFetch( $res ) )
{
	LogError(print "Бой $arr[0] завис" );
	f_MQuery( "UPDATE combat_players SET ready=0 WHERE combat_id=$arr[0] AND ready < 2" );
	f_MQuery( "UPDATE combats SET turn_done=0, last_turn_made = ".time()." where combat_id=$arr[0]" );
}
                                                  
//autoforce
/*
$_tm = time();
$res = f_MQuery("SELECT combat_id FROM combats WHERE last_turn_made<={$_tm}-timeout AND combat_id = 1511274");
while ($arr = f_MFetch($res))
{
//	f_MQuery( "INSERT INTO combat_log( combat_id, string ) VALUES ( $arr[0], 'Найди себя.".time()."<br>' )" );
	f_MQuery( "LOCK TABLE combat_players WRITE" );
	$check = f_MValue("SELECT count(ready) FROM combat_players WHERE combat_id={$arr[0]} AND ready=1 AND ai=1");
//f_MQuery( "INSERT INTO combat_log( combat_id, string ) VALUES ( $arr[0], '".$check."Ход.<br>' )" );
	if ($check > 0)
	{
		f_MQuery( "UPDATE combat_players SET forces=forces+ 1, ready=1, card_id=-1 WHERE combat_id = $arr[0] AND ready = 0" );
		f_MQuery( "UNLOCK TABLES" );
//		f_MQuery( "INSERT INTO combat_log( combat_id, string ) VALUES ( $arr[0], 'Ход форсирован автоматически.<br>' )" );
		CheckTurnOver( $arr[0], 1, "<font color=darkblue>Ход форсирован автоматически.</font><br>" );
		
	} else f_MQuery( "UNLOCK TABLES" );
}
*/
/*
  _players

*/


//CheckTurnOver( $combat_id, $side, '', true );







$lim = time( ) - 10 * 60;
$res = f_MQuery( "SELECT * FROM combat_players inner join combats on combat_players.combat_id=combats.combat_id WHERE ready >= 2 AND last_turn_made < $lim and ( location=4 OR location=2 AND place=43 )" );
while( $arr = f_MFetch( $res ) )
{
	$plr = new Player( $arr['player_id'] );
	$plr->LeaveCombat( );
}

$res = f_MQuery( "SELECT * FROM combat_players WHERE ready >= 2 AND ai = 1" );
while( $arr = f_MFetch( $res ) )
{
	leavecombatAI( $arr[player_id], $arr[combat_id] );
	$res2 = f_MQuery( "SELECT loc, depth FROM characters WHERE player_id=$arr[player_id]" );
	$arr2 = f_MFetch( $res2 );
	if( $arr2[0] == 2 && $arr2[1] == 43 ) // do not delete tournament mobcombat_time.php
	{
		continue;
	}
//	print( "$arr[player_id]<br>" );
	f_MQuery( "DELETE FROM characters WHERE player_id = $arr[player_id]" );
	f_MQuery( "DELETE FROM player_profile WHERE player_id = $arr[player_id]" );
	f_MQuery( "DELETE FROM player_attributes WHERE player_id = $arr[player_id]" );
	f_MQuery( "DELETE FROM player_cards WHERE player_id = $arr[player_id]" );
	f_MQuery( "DELETE FROM player_avatars WHERE player_id = $arr[player_id]" );
	f_MQuery( "DELETE FROM player_statistics WHERE player_id = $arr[player_id]" );
	f_MQuery( "DELETE FROM player_polomka WHERE player_id = $arr[player_id]" );
	f_MQuery( "DELETE FROM player_depths WHERE player_id = $arr[player_id]" );
	f_MQuery( "DELETE FROM player_forest_data WHERE player_id = $arr[player_id]" );
	f_MQuery( "DELETE FROM history_combats WHERE player_id = $arr[player_id]" );

}

$res = f_MQuery( "SELECT * FROM combat_bets" );
while( $arr = f_MFetch( $res ) )
{
	if( !f_MNum( f_MQuery( "SELECT * FROM online WHERE player_id = $arr[leader]" ) ) )
	{
		f_MQuery( "DELETE FROM combat_bets WHERE bet_id = $arr[bet_id]" );
		f_MQuery( "DELETE FROM player_bets WHERE bet_id = $arr[bet_id]" );
	}
}

$res = f_MQuery( "SELECT * FROM chess_asks" );
while( $arr = f_MFetch( $res ) )
{
	if( !f_MNum( f_MQuery( "SELECT * FROM online WHERE player_id = $arr[player1]" ) ) || f_MNum( f_MQuery( "SELECT * FROM online WHERE player_id = $arr[player2]" ) ) ) 
		f_MQuery( "DELETE FROM chess_asks WHERE id = $arr[id]" );
}

include_once( "wear_items.php" );
include_once( "player.php" );

$tm = time( );
$res = f_MQuery( "SELECT * FROM player_potions WHERE expires < $tm" );
while( $arr = f_MFetch( $res ) )
{
	$player = new Player( $arr['player_id'] );
	UnWearItem( $arr['slot_id'] );
}
f_MQuery( "DELETE FROM player_potions WHERE expires < $tm" );

f_MClose( );

?>
