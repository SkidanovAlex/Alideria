<?
require_once("time_functions.php");


require_once( 'functions.php' );
require_once( 'player.php' );

f_MConnect( );

$tm = time( );

f_MQuery( "DELETE FROM player_berry_places WHERE expires < $tm" );
f_MQuery( "DELETE FROM player_warehouse WHERE expires < $tm" );
f_MQuery( "DELETE FROM player_hare_coords WHERE expires < $tm" );
f_MQuery( "DELETE FROM player_government_delays WHERE expires < $tm" );

//f_MQuery( "DELETE FROM forum_avatars WHERE expires < $tm" );

f_MQuery( "DELETE FROM ref_ips" );

// Проверим жизнь Древа Жизни
f_MQuery("LOCK TABLE clans WRITE, player_clans WRITE");
$res_cl = f_MQuery("SELECT clan_id, tree_active FROM clans WHERE tree_active >= 0");
while ($arr_cl = f_MFetch($res_cl))
{
	$countcl = f_MValue("SELECT count(player_clans.player_id) FROM clans, player_clans WHERE clans.clan_id = player_clans.clan_id AND clans.clan_id=".$arr_cl[0]." AND player_clans.tree_effects=0 ");
	if (f_MValue( "SELECT count(player_clans.player_id) FROM clans, player_clans WHERE clans.clan_id = player_clans.clan_id AND clans.clan_id=".$arr_cl[0] ) < 5)
		$countcl = 5;
	$ta = $arr_cl[1] - $countcl;
	if ($ta < 0) $ta = 0;
	f_MQuery("UPDATE clans SET tree_active=$ta WHERE clan_id=".$arr_cl[0]);
$query = "UPDATE player_clans SET trup=trup-1, trup_val=trup_val-1 WHERE tree_effects =0 AND clan_id=".$arr_cl[0];
	f_MQuery($query);
	f_MQuery("UPDATE player_clans SET trup = 0 WHERE trup < 0 AND clan_id=".$arr_cl[0]);
	f_MQuery("UPDATE player_clans SET tree_effects = 0 WHERE clan_id=".$arr_cl[0]);
	f_MQuery("UNLOCK TABLES");

}
// Проверим жизнь Древа Жизни КОНЕЦ

// Заполним магазины
f_MQuery( "LOCK TABLES shops WRITE, items WRITE, shop_goods WRITE" );

$res = f_MQuery( "SELECT items.*, shops.shop_id FROM shops, shop_goods, items WHERE items.item_id=shop_goods.item_id AND shops.shop_id=shop_goods.shop_id AND shops.owner_id=-1 AND shops.shop_id=47" );
while( $arr = f_MFetch( $res ) )
{
	if( $arr['item_id'] == 26 ) $num = 50;
	else if( $arr['type'] == 0 ) continue;
	else if( $arr['type'] == 21 || $arr['type'] == 22 ) $num = 50;
	else if( $arr['level'] == 1 ) $num = 200;
	else if( $arr['level'] == 2 ) $num = 100;
	else if( $arr['level'] == 3 ) $num = 50;
	else if( $arr['level'] == 4 ) $num = 25;
	else if( $arr['level'] == 5 ) $num = 10;
	else $num = 1;

	f_MQuery( "UPDATE shop_goods SET number = $num WHERE shop_id=$arr[shop_id] AND item_id=$arr[item_id]" );
}

f_MQuery( "UNLOCK TABLES" );

// удаляем старые логи игрока
$otm = time( ) - 60 * 24 * 60 * 60;
f_MQuery( "DELETE FROM player_log WHERE time < $otm AND item_id != -1" );
f_MQuery( "DELETE FROM shop_log WHERE timestamp < $otm" );

// удаляем старые бои
$otm = time( ) - 5 * 24 * 60 * 60;
$res = f_MQuery( "SELECT combat_id FROM combats WHERE last_turn_made < $otm" );
while( $arr = f_MFetch( $res ) )
{
	$res2 = f_MQuery( "SELECT player_id FROM combat_players WHERE combat_id=$arr[combat_id]" );
	while( $arr2 = f_MFetch( $res2 ) )
	{
		f_MQuery( "UPDATE characters SET regime=0 WHERE player_id=$arr2[player_id] AND regime=100" );
		f_MQuery( "DELETE FROM combat_turn_desc WHERE player_id=$arr2[player_id]" );
	}
	f_MQuery( "DELETE FROM history_combats WHERE combat_id=$arr[combat_id]" );
	f_MQuery( "DELETE FROM combat_ajax_data WHERE combat_id=$arr[combat_id]" );
	f_MQuery( "DELETE FROM combat_players WHERE combat_id=$arr[combat_id]" );
	f_MQuery( "DELETE FROM combats WHERE combat_id=$arr[combat_id]" );
	f_MQuery( "DELETE FROM lab_combats WHERE combat_id=$arr[combat_id]" );
	f_MQuery( "DELETE FROM combat_statistics WHERE combat_id=$arr[combat_id]" );
}

$res = f_MQuery( "SELECT max( combat_id ) FROM combat_log" );
$arr = f_MFetch( $res );
$val = $arr[0] - 100000;
if( $val < 0 )  $val = 0;
f_MQuery( "DELETE FROM combat_log WHERE combat_id < $val" );
f_MQuery( "DELETE FROM combat_ajax_data WHERE combat_id < $val" );

$res = f_MQuery( "SELECT player_id FROM player_triggers WHERE trigger_id=78" );
while( $arr = f_MFetch( $res ) )
{
	$val = f_MValue( "SELECT count( player_id ) FROM player_triggers WHERE player_id=$arr[0] AND trigger_id=84" );
	$lvl = f_MValue( "SELECT level FROM characters WHERE player_id=$arr[0]" );
	if( !$val && $lvl >= 5 )
	{	
		f_MQuery( "INSERT INTO player_triggers( player_id, trigger_id ) VALUES ( $arr[0], 84 )" );
		f_MQuery( "INSERT INTO player_triggers( player_id, trigger_id ) VALUES ( $arr[0], 110 )" );
	}
}

	// Подарки лидерам рейтингов Лабиринта Влюблённых
/*
	$pair = f_MFetch( f_MQuery( 'SELECT p0,p1 FROM labyrinth_of_love ORDER BY best_time LIMIT 1' ) );
	f_MQuery( 'UPDATE characters SET umoney = umoney + 1 WHERE player_id = '.$pair[p0].' OR player_id = '.$pair[p1] );
	f_MQuery( "INSERT INTO player_log ( player_id, item_id, had, have, type, time ) VALUES ( ".$pair[0].", -1, 0, 1, 1006, ".time()." )" );
	f_MQuery( "INSERT INTO player_log ( player_id, item_id, had, have, type, time ) VALUES ( ".$pair[1].", -1, 0, 1, 1006, ".time()." )" );
*/	
	// Подарки сегодня родившимся
	$birthPlayers = f_MQuery( 'SELECT player_id FROM `player_profile` WHERE DAYOFMONTH( FROM_UNIXTIME( birthday ) ) = DAYOFMONTH( NOW( ) ) AND MONTH( FROM_UNIXTIME( birthday ) ) = MONTH( NOW( ) )' );
	$presentTime = 172800; // Два Дня В Секундах
	$presentDeadline = time( ) + $presentTime;
	
	while( $playerId = f_MFetch( $birthPlayers ) )
	{
		$playerId = $playerId['player_id']; // Психоделично, да и необходимо
		
		$Player = new Player( $playerId );
		$Player->syst2( "Администрация Алидерии поздравляет Вас с Днём Рождения и дарит в подарок 2 дня всех премиумов! <img src=/images/smiles/congratulations.gif />" );
		$Player->syst3( "Администрация Алидерии поздравляет Вас с Днём Рождения и дарит в подарок 2 дня всех премиумов! <img src=/images/smiles/congratulations.gif />" );
		
		// Активируем все премиумы на два дня
		for( $premiumType = 0; $premiumType < 6; ++ $premiumType )
		{
			// Проверяем, есть ли уже премиум такого типа
			if( f_MValue( 'SELECT * FROM premiums WHERE player_id = '.$playerId.' AND premium_id = '.$premiumType ) )
			{
				// Да, такой премиум у персонажа есть, увеличиваем его продолжительность на принятую продолжительность
				f_MQuery( 'UPDATE premiums SET deadline = deadline + '.$presentTime.' WHERE player_id = '.$playerId.' AND premium_id = '.$premiumType ); 
			
			}
			elseif( f_MValue( 'SELECT * FROM frozen_premiums WHERE player_id = '.$playerId.' AND premium_id = '.$premiumType ) )
			{
				// Да, такой премиум у персонажа есть, но он заморожен. Всё равно увеличиваем его на принятую длину
				f_MQuery( 'UPDATE frozen_premiums SET duration = duration + '.$presentTime.' WHERE player_id = '.$playerId.' AND premium_id = '.$premiumType );
			}
			else
			{
				// Нет, у игрока нет премиума такого типа
			
				// Но есть ли у него хоть какие-то замороженные премиумы?
				if( $available = f_MValue( 'SELECT available FROM frozen_premiums WHERE player_id = '.$playerId ) )
				{
					// Да, есть, добавляем подарки в замороженные
					f_MQuery( 'INSERT INTO frozen_premiums( player_id, premium_id, duration, available ) VALUES( '.$playerId.', '.$premiumType.', '.$presentTime.', '.$available[0].' )' );				
				}
				else
				{
					// Нет, нету, добавляем подарки в активные
					f_MQuery( 'INSERT INTO premiums( player_id, premium_id, deadline ) VALUES( '.$playerId.', '.$premiumType.', '.$presentDeadline.' )' );
				}	
			}
		}
	}
?>