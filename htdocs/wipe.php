<?

die( );
include( 'functions.php' );
include( 'player.php' );

f_MConnect( );

// 1. ”далить вещи, которых больше нет
/*$res = f_MQuery( "SELECT item_id, price FROM items WHERE inner_spell_id > 0 OR type=21 OR type=22 OR item_id IN( 171, 172, 170, 169, 168, 272, 166, 165, 164, 202,203,201,200,199,195,196,197,198,249,248,245,242,243,244,245,246,247,1607,1608,1609,1610,1611,1612,1613,1614,1615,5616,5617,5618,5619,5620,5621,5622,5623,5625,8009,8010,8011,8008,8007,8003,8004,8005,8006 )" );
/// 46,47,48,49,50,51,52,53,54,57,58,59,60,61,62,63,64,65,108,109,110,111,112,113,114,115,116,152,153,154,155,156,157,158,159,160,172,173,174,175,176,177,178,179,180
echo f_MNum( $res );
while( $arr = f_MFetch( $res ) )
{
	$pres = f_MQuery( "SELECT player_id, number FROM player_items WHERE item_id=$arr[0]" );
	while( $parr = f_MFetch( $pres ) )
	{
		$val = $arr[1] * $parr[1];
		f_MQuery( "UPDATE characters SET money=money+$val WHERE player_id=$parr[0]" );
		echo "PLAYER $parr[0] : $val<br>";
	}
	$pres = f_MQuery( "SELECT player_id, number FROM player_warehouse_items WHERE item_id=$arr[0]" );
	while( $parr = f_MFetch( $pres ) )
	{
		$val = $arr[1] * $parr[1];
		f_MQuery( "UPDATE characters SET money=money+$val WHERE player_id=$parr[0]" );
		echo "PLAYER $parr[0] : $val<br>";
	}
	$pres = f_MQuery( "SELECT shops.owner_id, shop_goods.number FROM shops INNER JOIN shop_goods ON shops.shop_id=shop_goods.shop_id WHERE item_id=$arr[0]" );
	while( $parr = f_MFetch( $pres ) )
	{
		$val = $arr[1] * $parr[1];
		f_MQuery( "UPDATE clans SET money=money+$val WHERE clan_id=$parr[0]" );
		echo "CLAN $parr[0] : $val<br>";
	}
	f_MQuery( "DELETE FROM player_items WHERE item_id=$arr[0]" );
	f_MQuery( "DELETE FROM player_warehouse_items WHERE item_id=$arr[0]" );
	f_MQuery( "DELETE FROM clan_items WHERE item_id=$arr[0]" );
	f_MQuery( "DELETE FROM shop_goods WHERE item_id=$arr[0]" );
	f_MQuery( "DELETE FROM items WHERE item_id=$arr[0]" );
	f_MQuery( "DELETE FROM post_items WHERE item_id=$arr[0]" );
}/**/

// post
/*$res = f_MQuery( "SELECT p.*,i.parent_id FROM post_items as p INNER JOIN items as i ON p.item_id=i.item_id WHERE i.parent_id <> i.item_id" );
echo f_MNum( $res );
while( $arr = f_MFetch( $res ) )
{
//	$plr = new Player( $arr['player_id'] );

	f_MQuery( "UPDATE post_items SET item_id=$arr[parent_id] WHERE entry_id=$arr[entry_id]" );
//		$num = $plr->NumberItems( $arr['item_id'] );
//		$plr->DropItems( $arr['item_id'], $num );
//		$plr->AddItems( $arr['parent_id'], $num );
}*/

// silo
/*$res = f_MQuery( "SELECT p.*,i.parent_id FROM clan_items as p INNER JOIN items as i ON p.item_id=i.item_id WHERE i.parent_id <> i.item_id" );
echo f_MNum( $res );
while( $arr = f_MFetch( $res ) )
{
//	$plr = new Player( $arr['player_id'] );

	$q = f_MQuery( "SELECT * FROM clan_items WHERE item_id=$arr[parent_id] AND clan_id=$arr[clan_id]" );
	if( f_MNum( $q ) )
	{
		f_MQuery( "UPDATE clan_items SET number=number+$arr[number] WHERE item_id=$arr[parent_id] AND clan_id=$arr[clan_id]" );
		f_MQuery( "DELETE FROM clan_items WHERE item_id=$arr[item_id] AND clan_id=$arr[clan_id]" );
	}
	else
	{
		f_MQuery( "UPDATE clan_items SET item_id=$arr[parent_id] WHERE item_id=$arr[item_id] AND clan_id=$arr[clan_id]" );
	}
//		$num = $plr->NumberItems( $arr['item_id'] );
//		$plr->DropItems( $arr['item_id'], $num );
//		$plr->AddItems( $arr['parent_id'], $num );
}*/


// player_warehouse
/*$res = f_MQuery( "SELECT p.*,i.parent_id FROM player_warehouse_items as p INNER JOIN items as i ON p.item_id=i.item_id WHERE i.parent_id <> i.item_id" );
echo f_MNum( $res );
while( $arr = f_MFetch( $res ) )
{
//	$plr = new Player( $arr['player_id'] );

	$q = f_MQuery( "SELECT * FROM player_warehouse_items WHERE item_id=$arr[parent_id] AND player_id=$arr[player_id]" );
	if( f_MNum( $q ) )
	{
		f_MQuery( "UPDATE player_warehouse_items SET number=number+$arr[number] WHERE item_id=$arr[parent_id] AND player_id=$arr[player_id]" );
		f_MQuery( "DELETE FROM player_warehouse_items WHERE item_id=$arr[item_id] AND player_id=$arr[player_id]" );
	}
	else
	{
		f_MQuery( "UPDATE player_warehouse_items SET item_id=$arr[parent_id] WHERE item_id=$arr[item_id] AND player_id=$arr[player_id]" );
	}
//		$num = $plr->NumberItems( $arr['item_id'] );
//		$plr->DropItems( $arr['item_id'], $num );
//		$plr->AddItems( $arr['parent_id'], $num );
}*/

// 1. ”далить вещи, которых больше нет
/*$res = f_MQuery( "SELECT item_id, price FROM items WHERE type=21 OR type=22 OR item_id IN( 171, 172, 170, 169, 168, 272, 166, 165, 164, 202,203,201,200,199,195,196,197,198,249,248,245,242,243,244,245,246,247,1607,1608,1609,1610,1611,1612,1613,1614,1615,5616,5617,5618,5619,5620,5621,5622,5623,5625,8009,8010,8011,8008,8007,8003,8004,8005,8006 )" );
/// 46,47,48,49,50,51,52,53,54,57,58,59,60,61,62,63,64,65,108,109,110,111,112,113,114,115,116,152,153,154,155,156,157,158,159,160,172,173,174,175,176,177,178,179,180
echo f_MNum( $res );
while( $arr = f_MFetch( $res ) )
{
	$pres = f_MQuery( "SELECT player_id, number FROM post_items WHERE item_id=$arr[0]" );
	while( $parr = f_MFetch( $pres ) )
	{
		$val = $arr[1] * $parr[1];
//		f_MQuery( "UPDATE characters SET money=money+$val WHERE player_id=$parr[0]" );
		echo "$arr[0] : $val<br>";
	}
	f_MQuery( "DELETE FROM player_warehouse_items WHERE item_id=$arr[0]" );
//	f_MQuery( "DELETE FROM items WHERE item_id=$arr[0]" );
}*/

die( );

include( 'functions.php' );
include( 'player.php' );

f_MConnect( );

function reg_err( $a ) { };

$res = f_MQuery( "SELECT player_id, pswrddmd5, login, email, sex FROM characters" );

function moo( $a, $b = 'player_id' )
{
	global $id;
  	f_MQuery( "DELETE FROM $a WHERE $b = $id" );
}

$id = 0;

while( $arr = f_MFetch( $res ) )
{
$id = $arr[0];

moo( 'chess_asks', 'player1' );
moo( 'chess_asks', 'player2' );

moo( 'chess_opponents', 'player1' );
moo( 'chess_opponents', 'player2' );

moo( 'combat_auras' );
moo( 'combat_auras' );

$qres = f_MQuery( "SELECT bet_id FROM combat_bets WHERE leader = $id"  );
while( $qarr = f_MFetch( $qres ) ) f_MQuery( "DELETE FROM player_bets WHERE bet_id = $arr[0]" );

moo( 'combat_bets', 'leader' );
moo( 'player_bets' );

moo( 'combat_creatures' );
moo( 'combat_players' );

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
moo( 'player_talks', 'player_id' );
moo( 'player_triggers', 'player_id' );
moo( 'player_casino', 'player_id' );

$q = $id;

f_MQuery( "DELETE FROM characters WHERE player_id =$arr[player_id]" );
f_MQuery( "INSERT INTO characters ( player_id, login, pswrddmd5, email, loc, depth, text_clr, nick_clr, sex ) VALUES ( $arr[player_id], '$arr[login]', '$arr[pswrddmd5]', '$arr[email]', 2, 0, '000000', '000000', $arr[sex] )" );

			if( !f_MQuery( "INSERT INTO player_attributes ( player_id, attribute_id, value, real_value ) VALUES ( $q, 1000, 3, 3 )" ) )
				reg_err( "¬нутренн€€ ошибка сервера" );
			if( !f_MQuery( "INSERT INTO player_attributes ( player_id, attribute_id, value, real_value ) VALUES ( $q, 1001, 3, 3 )" ) )
				reg_err( "¬нутренн€€ ошибка сервера" );
			if( !f_MQuery( "INSERT INTO player_attributes ( player_id, attribute_id, value, real_value ) VALUES ( $q, 1, 100, 100 )" ) )
				reg_err( "¬нутренн€€ ошибка сервера" );
			if( !f_MQuery( "INSERT INTO player_attributes ( player_id, attribute_id, value, real_value ) VALUES ( $q, 101, 100, 100 )" ) )
				reg_err( "¬нутренн€€ ошибка сервера" );
			if( !f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( $q, 56, 1 )" ) )
				reg_err( "¬нутренн€€ ошибка сервера" );
			if( !f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( $q, 57, 1 )" ) )
				reg_err( "¬нутренн€€ ошибка сервера" );
			if( !f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( $q, 58, 1 )" ) )
				reg_err( "¬нутренн€€ ошибка сервера" );
			if( !f_MQuery( "INSERT INTO player_selected_cards ( player_id, card_id ) VALUES ( $q, 56 )" ) )
				reg_err( "¬нутренн€€ ошибка сервера" );
			if( !f_MQuery( "INSERT INTO player_selected_cards ( player_id, card_id ) VALUES ( $q, 57 )" ) )
				reg_err( "¬нутренн€€ ошибка сервера" );
			if( !f_MQuery( "INSERT INTO player_selected_cards ( player_id, card_id ) VALUES ( $q, 58 )" ) )
				reg_err( "¬нутренн€€ ошибка сервера" );
			if( !f_MQuery( "INSERT INTO player_items ( player_id, item_id, number, weared ) VALUES ( $q, 154, 1, 13 )" ) )
				reg_err( "¬нутренн€€ ошибка сервера" );


}

?>
