<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

if( !$included )
{
  f_MConnect( );

  include( 'admin_header.php' );

  $id = $_GET['id'];

  function moo( $a, $b = 'player_id' )
  {
  	global $id;
  	f_MQuery( "DELETE FROM $a WHERE $b = $id" );
  }
}

settype( $id, 'integer' );


moo( 'chess_asks', 'player1' );
moo( 'chess_asks', 'player2' );

moo( 'chess_opponents', 'player1' );
moo( 'chess_opponents', 'player2' );

moo( 'combat_auras' );
moo( 'combat_auras' );

$res = f_MQuery( "SELECT bet_id FROM combat_bets WHERE leader = $id"  );
while( $arr = f_MFetch( $res ) ) f_MQuery( "DELETE FROM player_bets WHERE bet_id = $arr[0]" );

moo( 'combat_bets', 'leader' );
moo( 'player_bets' );

moo( 'combat_creatures' );
moo( 'combat_players' );

moo( 'player_sets' );

moo( 'player_labs' );

f_MQuery( "UPDATE forum_posts SET author_id = 173 WHERE author_id = $id" );
f_MQuery( "UPDATE forum_threads SET author_id = 173 WHERE author_id = $id" );

moo( 'history_combats' );
moo( 'history_logon_logout' );
moo( 'history_punishments' );
moo( 'history_trades', 'player_id1' );
moo( 'history_trades', 'player_id2' );

moo( 'loto_players' );
moo( 'loto_past' );
moo( 'lottery' );
moo( 'market_bets' );
moo( 'online' );

moo( 'player_attributes', 'player_id' );
moo( 'player_bets', 'player_id' );
moo( 'player_cards', 'player_id' );
moo( 'player_craft', 'player_id' );
moo( 'player_depths', 'player_id' );
moo( 'player_forest_data', 'player_id' );
moo( 'player_forest_riddle', 'player_id' );
moo( 'player_guilds', 'player_id' );
moo( 'player_items', 'player_id' );
moo( 'player_num', 'player_id' );
moo( 'player_number', 'player_id' );
moo( 'player_permissions', 'player_id' );
moo( 'player_profile', 'player_id' );
moo( 'player_profs', 'player_id' );
moo( 'player_quest_parts', 'player_id' );
moo( 'player_quests', 'player_id' );
moo( 'player_ranks', 'player_id' );
moo( 'player_recipes', 'player_id' );
moo( 'player_selected_cards', 'player_id' );
moo( 'player_stone_table', 'player_id' );
moo( 'player_talks', 'player_id' );
moo( 'player_triggers', 'player_id' );
moo( 'player_casino', 'player_id' );
moo( 'player_mines', 'player_id' );

moo( 'player_government_work', 'player_id' );
moo( 'player_government_delays', 'player_id' );

moo( 'clan_creation', 'player_id' );
moo( 'clan_creation_players', 'player_id' );
moo( 'clan_creation_players', 'invites_whom' );

moo( 'characters', 'player_id' );

?>
