<?
/* @author = undefined
 * @about = Удаление клана 
 */
 
	$clanId = (int)$_POST['clanId'];
	
	require_once( 'clan.php' );
	
	deleteClan( $clanId, 'по воле Демиурга' );

	/*// Оповещаем всех клановых игроков, что их Орден расформирован и тут же отчисляем их из Ордена
	$clanPlayers = f_MQuery( 'SELECT player_id FROM characters WHERE clan_id = '.$clanId );
	while( $player_id = f_MFetch( $clanPlayers ) )
	{
		$ClanPlayer = new Player( $player_id[player_id] );
		
		$ClanPlayer->syst3( 'Ваш Орден расформирован.' );
		f_MQuery( 'UPDATE characters SET clan_id = 0 WHERE player_id = '.$ClanPlayer->player_id );
		f_MQuery( 'UPDATE characters SET regime=0, go_till=0, loc = 2, depth = 0 WHERE player_id='.$ClanPlayer->player_id.' AND loc=2 AND depth=19' );
	}

	// Непосредственно, удаление
	f_MQuery( 'DELETE FROM clans WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_bets WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_buildings WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_build_queue WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_items WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_jobs WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_log WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_ranks WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_shelf_names WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_wonders WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_wonder_ips WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM clan_wonder_items_spent WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM player_clans WHERE clan_id = '.$clanId );
	f_MQuery( 'DELETE FROM forum_rooms WHERE id = -'.$clanId );*/
	
	// Сообщаем об результате
	echo '<span style="color: green; font-weight: bold;">OK!</span>';
?>