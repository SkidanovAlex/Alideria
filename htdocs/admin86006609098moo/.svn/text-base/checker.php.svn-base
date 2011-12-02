<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

$step = $_GET['step'];
settype( $step, 'integer' );

$errors = 0;
function check_one_to_many( $table1, $column1, $table2, $column2, $add = '' )
{
	global $errors;
	$failed = 0;
	echo "<b>Проверка отношения один ко многим $column2 в $table2 и $column1 в $table1... </b>";
	$res = f_MQuery( "SELECT DISTINCT $column1 FROM $table1 $add" );
	while( $arr = f_MFetch( $res ) )
	{
		$res2 = f_MQuery( "SELECT count( $column2 ) FROM $table2 WHERE $column2=$arr[0]" );
		$arr2 = f_MFetch( $res2 );
		if( $arr2[0] == 0 )
		{
			++ $failed;
			++ $errors;
			f_MQuery( "DELETE FROM $table1 WHERE $column1 = $arr[0];" );
			echo "<br>&nbsp;&nbsp;Неверный ключ $arr[0]. Исправлено";
		}
	}
	if( !$failed ) echo "<b><font color=blue>OK</font></b><br>";
	else echo "<br><b><font color=red>$failed замечаний</font></b><br>";
}

if( $step == 0 )
{
check_one_to_many( 'cave_items', 'item_id', 'items', 'item_id' );
check_one_to_many( 'forest_items', 'item_id', 'items', 'item_id' );
check_one_to_many( 'lake_items', 'item_id', 'items', 'item_id' );
check_one_to_many( 'location_items', 'item_id', 'items', 'item_id' );
check_one_to_many( 'phrase_items', 'item_id', 'items', 'item_id', 'WHERE item_id != 0' );
check_one_to_many( 'player_items', 'item_id', 'items', 'item_id' );
check_one_to_many( 'shop_goods', 'item_id', 'items', 'item_id' );
check_one_to_many( 'forest_items', 'item_id', 'items', 'item_id' );
}

if( $step == 1 )
{
check_one_to_many( 'player_attributes', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_bets', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_cards', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_craft', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_depths', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_forest_data', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_forest_riddle', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_items', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_num', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_number', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_permissions', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_profile', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_profs', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_quest_parts', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_quests', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_ranks', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_recipes', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_selected_cards', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_talks', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_triggers', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'player_casino', 'player_id', 'characters', 'player_id' );
}

if( $step == 2 )
{
check_one_to_many( 'combat_players', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'combat_creatures', 'creature_id', 'creatures', 'creature_id' );
check_one_to_many( 'combat_creatures', 'creature_id', 'creatures', 'creature_id' );
check_one_to_many( 'combat_auras', 'aura_id', 'auras', 'aura_id' );
check_one_to_many( 'combat_bets', 'leader', 'characters', 'player_id' );
check_one_to_many( 'player_bets', 'bet_id', 'combat_bets', 'bet_id' );

check_one_to_many( 'mob_attributes', 'attribute_id', 'attributes', 'attribute_id' );
check_one_to_many( 'mob_attributes', 'mob_id', 'mobs', 'mob_id' );
check_one_to_many( 'mob_cards', 'card_id', 'cards', 'card_id' );
check_one_to_many( 'mob_cards', 'mob_id', 'mobs', 'mob_id' );

check_one_to_many( 'player_cards', 'card_id', 'cards', 'card_id' );
check_one_to_many( 'player_selected_cards', 'card_id', 'cards', 'card_id' );

}

if( $step == 3 )
{
check_one_to_many( 'talk_phrases', 'talk_id', 'talks', 'talk_id' );
check_one_to_many( 'talk_phrases', 'phrase_id', 'phrases', 'phrase_id' );
check_one_to_many( 'talk_redirects', 'talk_id', 'talks', 'talk_id' );
check_one_to_many( 'talk_redirects', 'npc_id', 'npcs', 'npc_id' );
check_one_to_many( 'talk_redirects', 'talk_id', 'talks', 'talk_id' );
check_one_to_many( 'npcs', 'talk_id', 'talks', 'talk_id' );

check_one_to_many( 'phrase_items', 'phrase_id', 'phrases', 'phrase_id' );
check_one_to_many( 'phrase_triggers', 'phrase_id', 'phrases', 'phrase_id' );
check_one_to_many( 'phrase_quests', 'phrase_id', 'phrases', 'phrase_id' );
check_one_to_many( 'phrase_quests', 'quest_id', 'quests', 'quest_id' );
}

if( $step == 4 )
{
check_one_to_many( 'history_combats', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'history_punishments', 'player_id', 'characters', 'player_id' );
check_one_to_many( 'history_logon_logout', 'player_id', 'characters', 'player_id' );
}

if( $step == 5 )
{
	echo "<h1>Проверка успешно выполнена</h1>";
	die( );
}

++ $step;
echo "<a href=checker.php?step=$step>Следующий шаг</a><br>";

?>
