<?

include_once( "items.php" );

function phrase_prolong_premium($act, $days = 7)
{
	global $player;
	$res1 = f_MQuery("SELECT * FROM frozen_premiums WHERE player_id={$player->player_id}");
	if (f_MNum($res1))
	{
		f_MQuery( "LOCK TABLE frozen_premiums WRITE" );
		$res = f_MQuery( "SELECT duration FROM frozen_premiums WHERE player_id={$player->player_id} AND premium_id=$act" );
		$arr = f_MFetch( $res );
		$duration = $days * 24 * 60 * 60;
		if( !$arr ) f_MQuery( "INSERT INTO frozen_premiums( player_id, premium_id, duration, available ) VALUES ( {$player->player_id}, $act, $duration, 1 )" );
		else if( $arr[0] < time( ) ) f_MQuery( "UPDATE frozen_premiums SET duration=$duration WHERE player_id={$player->player_id} AND premium_id=$act" ); 
		else f_MQuery( "UPDATE frozen_premiums SET duration=duration+{$days}*24*60*60 WHERE player_id={$player->player_id} AND premium_id=$act" ); 
		f_MQuery( "UNLOCK TABLES" );
	}
	else
	{
		f_MQuery( "LOCK TABLE premiums WRITE" );
		$res = f_MQuery( "SELECT deadline FROM premiums WHERE player_id={$player->player_id} AND premium_id=$act" );
		$arr = f_MFetch( $res );
		$deadline = time( ) + $days * 24 * 60 * 60;
		if( !$arr ) f_MQuery( "INSERT INTO premiums( player_id, premium_id, deadline ) VALUES ( {$player->player_id}, $act, $deadline )" );
		else if( $arr[0] < time( ) ) f_MQuery( "UPDATE premiums SET deadline=$deadline WHERE player_id={$player->player_id} AND premium_id=$act" ); 
		else f_MQuery( "UPDATE premiums SET deadline=deadline+{$days}*24*60*60 WHERE player_id={$player->player_id} AND premium_id=$act" ); 
		f_MQuery( "UNLOCK TABLES" );
	}
}

function allow_phrase( $a, $consider_random = true )
{
	global $player;
	
	$res = f_MQuery( "SELECT * FROM phrase_items WHERE ( regime=0 OR regime>=3 AND regime != 7 ) AND phrase_id = $a" );
	while( $arr = f_MFetch( $res ) )
	{
		if( $arr[regime] == 4 )
		{
        	if( !$player->HasWearedItem( $arr[item_id] ) ) return 0;
        }	
		else if( $arr[regime] == 5 )
		{
        	if( !$player->HasItem( $arr[item_id] ) ) return 0;
		}
		else if( $arr[regime] == 6 )
		{
        	if( !$player->HasUnwearedItem( $arr[item_id] ) ) return 0;
		}
		else
		{
			if( $arr['item_id'] == -1 ) $val = $player->umoney;
			else $val = ( $arr['item_id'] ) ? $player->NumberItems( $arr[item_id] ) : $player->money;
			if( $val < $arr[number] && $arr[regime] == 0 ) // требует не одетой
				return 0;
			if( $val >= $arr[number] && $arr[regime] == 3 ) // требует отсутствия
				return 0;
		}
	}

	$res = f_MQuery( "SELECT * FROM phrase_triggers WHERE ( regime=0 OR regime=1 ) AND phrase_id = $a" );
	while( $arr = f_MFetch( $res ) )
	{
		if( $arr[regime] == 0 && !( $player->HasTrigger( $arr[trigger_id] ) ) )
			return 0;
		if( $arr[regime] == 1 && $player->HasTrigger( $arr[trigger_id] ) )
			return 0;
	}
	
	$res = f_MQuery( "SELECT * FROM phrase_values WHERE ( regime>= 1 AND regime <= 3 ) AND phrase_id = $a" );
	while( $arr = f_MFetch( $res ) )
	{
		$rval = $arr['value'];
		$aval = $player->GetQuestValue( $arr['value_id'] );
		$regime = $arr['regime'];
		if( $regime == 1 && $rval != $aval ) return 0;
		if( $regime == 2 && $rval >= $aval ) return 0;
		if( $regime == 3 && $rval <= $aval ) return 0;
	}
	$res = f_MQuery("SELECT * FROM phrase_sex WHERE phrase_id=$a"); // проверка пола(0-мальчики, 1-девочки)
	while ($arr = f_MFetch($res))
	{
		if ($arr[1] != $player->sex) return 0;
		if ($a == 1458 && f_MValue("SELECT p0 FROM player_wedding_bets WHERE p1 = $player->player_id AND moo=0") == 0) return 0;
	}
	$res = f_MQuery("SELECT * FROM phrase_clans WHERE phrase_id = $a"); //клановая проверка
	while ($arr = f_MFetch($res) )
	{
		if ($arr['action'] == 1)
			if ($player->clan_id != $arr['clan_id']) return 0;
		if ($arr['action'] == 0)
			if ($player->clan_id == $arr['clan_id']) return 0;
	}
	$res = f_MQuery( "SELECT * FROM phrase_guilds WHERE phrase_id = $a ORDER BY action" );
	while( $arr = f_MFetch( $res ) )
	{
		if( $arr[action] == 1 ) if( !f_MNum( f_MQuery( "SELECT * FROM player_guilds WHERE player_id = {$player->player_id} AND guild_id = {$arr[guild_id]}" ) ) ) return 0;
		if( $arr[action] == 2 ) if( f_MNum( f_MQuery( "SELECT * FROM player_guilds WHERE player_id = {$player->player_id} AND guild_id = {$arr[guild_id]}" ) ) ) return 0;
		if( $arr[action] >= 5 && $arr[action] <= 7 )
		{
			$gres = f_MQuery( "SELECT rank FROM player_guilds WHERE player_id={$player->player_id} AND guild_id={$arr[guild_id]}" );
			$garr = f_MFetch( $gres );
			if( !$garr ) RaiseError( "Ошибка гильдийных требований в разговоре", "Фраза: $a; Гильдия: $arr[guild_id]; Режим: $arr[action]" );
			$gval = $garr[0];
			if( $arr['action'] == 5 && $gval < $arr['value'] ) return 0;
			if( $arr['action'] == 6 && $gval >= $arr['value'] ) return 0;
			if( $arr['action'] == 7 && $gval != $arr['value'] ) return 0;
		}
		if( $arr[action] >= 8 && $arr[action] <= 10 )
		{
			$gres = f_MQuery( "SELECT rating FROM player_guilds WHERE player_id={$player->player_id} AND guild_id={$arr[guild_id]}" );
			$garr = f_MFetch( $gres );
			if( !$garr ) RaiseError( "Ошибка гильдийных требований в разговоре", "Фраза: $a; Гильдия: $arr[guild_id]; Режим: $arr[action]" );
			$gval = $garr[0];
			if( $arr['action'] == 8 && $gval < $arr['value'] ) return 0;
			if( $arr['action'] == 9 && $gval >= $arr['value'] ) return 0;
			if( $arr['action'] == 10 && $gval != $arr['value'] ) return 0;
		}
	}

	$res = f_MQuery( "SELECT * FROM phrases WHERE phrase_id = $a" );

	$arr = f_MFetch( $res );
	if( $arr['minlevel'] > $player->level || $arr['maxlevel'] < $player->level )
		return 0;

	if( $arr['req_guild_slot'] )
	{
		include_once( 'guild.php' );
		$max_guilds_num = GuildsPerLevel( $player->level );
		$cur_guilds_num = f_MNum( f_MQuery( "SELECT guild_id FROM player_guilds WHERE player_id = {$player->player_id}" ) );
		if( $max_guilds_num < $cur_guilds_num ) LogError( "У чувака гильдий $cur_guilds_num при максимуме $max_guilds_num на его {$player->level} уровне" );
		if( $arr['req_guild_slot'] == -1 && $cur_guilds_num < $max_guilds_num ) return 0;
		if( $arr['req_guild_slot'] == 1 && $cur_guilds_num == $max_guilds_num ) return 0;
	}

	if( $consider_random )
	{
		$val = mt_rand( 1, 1000000 );
		if( $val > $arr[chance1000000] ) return 0;
	}

	// additional requirements
	if( $a == 496 ) // quest 15, shamahan, setting up the cage
	{
		$hour = (int)date( "H", time( ) );
		if( $hour >= 6 && $hour < 18 ) return false;
	}
	if( $a == 498 ) // quest 15, shamahan, checking the cage
	{
		$hour = (int)date( "H", time( ) );
		if( $hour < 6 || $hour >= 18 ) return false;
	}
	if( $a == 634 || $a == 638 ) // is player clan leader
	{
		include_once( 'clan.php' );
		if( $player->login != 'Atlantida' )
			if( 0 == ( getPlayerPermitions( $player->clan_id, $player->player_id ) & 4 ) ) return false;

		$res = f_MQuery( "SELECT hascamp, ta_lost FROM clans WHERE clan_id={$player->clan_id}" );
		$arr = f_MFetch( $res );
		if( $arr[0] ) return false;
		if( $a == 634 && $arr[1] >= 10 ) return false;
		if( $a == 638 && $arr[1] != 11 ) return false;
	}

	return 1;
}

function do_phrase( $phrase_id, $script_tags = true )
{
	global $player;

	$pres = f_MQuery( "SELECT * FROM phrases WHERE phrase_id = $phrase_id" );
	$parr = f_MFetch( $pres );
	
	if( !$parr ) RaiseError( "Выполнение несуществующей фразы", "phrase_id: $phrase_id" );
	
	// Квесты
	$allow = Array( );
	$res = f_MQuery( "SELECT * FROM phrase_quests WHERE phrase_id = $phrase_id ORDER BY quest_id, status" );
	while( $arr = f_MFetch( $res ) )
	{
		$quest_id = $arr[quest_id];
		$action = $arr[status];
		$qres = f_MQuery( "SELECT status FROM player_quests WHERE player_id = {$player->player_id} AND quest_id = $quest_id" );
		$qarr = f_MFetch( $qres );
		if( !$qarr && $action ) continue;
		if( $qarr && !$action ) continue;
		if( !$allow[$quest_id] && $qarr && $qarr[status] && $action != -5 ) continue;

		$nres = f_MQuery( "SELECT name FROM quests WHERE quest_id = $quest_id" );
		$narr = f_MFetch( $nres );
		
		if( !$narr )
		{
			RaiseError( "Фраза дает несуществующий квест $quest_id!" );
			die( );
		}
		
		$quest_name = $narr[0];
					
		$allow[$quest_id] = 1;

		if( $action == -5 )	
		{
			$moo = f_MQuery( "SELECT quest_part_id FROM quest_parts WHERE quest_id = $quest_id" );
			while( $hru = f_MFetch( $moo ) )
				f_MQuery( "DELETE FROM player_quest_parts WHERE player_id = {$player->player_id} AND quest_part_id = $hru[0]" );
			f_MQuery( "DELETE FROM player_quests WHERE player_id = {$player->player_id} AND quest_id = $quest_id" );
		}
		else if( $action == -2 )
		{
			f_MQuery( "UPDATE player_quests SET status = -1 WHERE player_id = {$player->player_id} AND quest_id = $quest_id" );
			$player->syst( "Квест <b>$quest_name</b> провален.", $script_tags );
		}
		else if( $action == -1 )
		{
			$player->syst( "Квест <b>$quest_name</b> успешно выполнен. Поздравляем!", $script_tags );
			f_MQuery( "UPDATE player_quests SET status = 1 WHERE player_id = {$player->player_id} AND quest_id = $quest_id" );
		}
		else if( $action == 0 )
		{
			$tm = time( );
			$player->syst( "Вы получаете квест <b>$quest_name</b>.", $script_tags );
			f_MQuery( "INSERT INTO player_quests VALUES ( {$player->player_id}, $quest_id, 0, $tm )" );
		}
		else
		{
			$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$player->player_id} AND quest_part_id = $action" );
			if( !mysql_num_rows( $qres ) )
			{
				$player->syst( "Информация о квесте <b>$quest_name</b> обновлена.", $script_tags );
				f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$player->player_id}, $action )" );
			}
		}
	}
	
	// Экспа
	if( $parr['gain_exp'] )
	{
		if( $player->exp + $parr['gain_exp'] < 0 ) f_MQuery( "UPDATE characters SET exp = 0 WHERE player_id = $player->player_id" );
		else f_MQuery( "UPDATE characters SET exp = exp + $parr[gain_exp] WHERE player_id = $player->player_id" );
		if( $parr['gain_exp'] > 0 )
			$player->syst( "Вы получаете <b>$parr[gain_exp]</b> опыта.", $script_tags );
		else
		{
			$val = - $parr['gain_exp'];
			$player->syst( "Вы теряете <b>$val</b> опыта.", $script_tags );
		}
	}

	if( $parr['warp_loc'] != -1 )
	{
		$player->SetLocation( $parr['warp_loc'] );
		$player->SetDepth( $parr['warp_depth'] );
	}
	
	// Эффекты
	$res = f_MQuery( "SELECT * FROM phrase_effects WHERE phrase_id=$phrase_id" );
	while( $arr = f_MFetch( $res ) )
	{
		if ($arr['duration'] >= 0)
			$dur = time() + $arr['duration'];
		else
			$dur = -1;
		$player->AddEffect( $arr['effect_id'], $arr['type'], $arr['name'], $arr['description'], $arr['image'], $arr['effect'], $dur );
		if ($dur == -1)
			$player->syst("На вас наложен эффект &laquo;$arr[name]&raquo;");
		else
			$player->syst( "На вас наложен эффект &laquo;$arr[name]&raquo; на ".my_time_str2( $arr['duration'] ) );
	}
	
	// Гильдии
	$res = f_MQuery( "SELECT * FROM phrase_guilds WHERE phrase_id=$phrase_id" );
	while( $arr = f_MFetch( $res ) )
	{
		if( $arr['action'] == 3 ) f_MQuery( "INSERT INTO player_guilds ( player_id, guild_id ) VALUES ( {$player->player_id}, {$arr[guild_id]} )" );
		if( $arr['action'] == 4 ) { f_MQuery( "DELETE FROM player_guilds WHERE player_id = {$player->player_id} AND guild_id = {$arr[guild_id]}" ); f_MQuery( "DELETE FROM player_kapkans WHERE player_id = {$player->player_id} AND guild_id = {$arr[guild_id]}" ); }
		if( $arr['action'] == 11 ) f_MQuery( "UPDATE player_guilds SET rank = rank + $arr[value] WHERE player_id = {$player->player_id} AND guild_id = {$arr[guild_id]}" );
		if( $arr['action'] == 12 ) f_MQuery( "UPDATE player_guilds SET rating = rating + $arr[value] WHERE player_id = {$player->player_id} AND guild_id = {$arr[guild_id]}" );
	}
	
	// Вещи
	$res = f_MQuery( "SELECT * FROM phrase_items WHERE regime <> 0 AND phrase_id = $phrase_id" );
	while( $arr = f_MFetch( $res ) )
	{
		if( $arr[regime] == 1 )
		{
			$player->AddToLog( $arr[item_id], - $arr[number], 0, $phrase_id );

			if( $arr[item_id] > 0 ) 
			{
				$player->DropItems( $arr[item_id], $arr[number] );
				$player->syst( "Вы теряете <b>".my_word_form2( $arr['number'], getItemNameForm( $arr['item_id'], "4" ), getItemNameForm( $arr['item_id'], "13" ), getItemNameForm( $arr['item_id'], "2_m" ) )."</b>", $script_tags );
			}
			else if( $arr[item_id] == 0 )
			{
				$player->SpendMoney( $arr[number] );
				$player->syst( "Вы теряете <b>".my_word_form2( $arr['number'], 'монету', 'монеты', 'монет' )."</b>", $script_tags );
			}
			else $player->SpendUMoney( $arr[number] );
		}
		else if( $arr[regime] == 2 )
		{
			$player->AddToLog( $arr[item_id], $arr[number], 0, $phrase_id );

			if( $arr[item_id] > 0 )
			{
				$player->AddItems( $arr[item_id], $arr[number] );
				$player->syst( "Вы получаете <b>".my_word_form2( $arr['number'], getItemNameForm( $arr['item_id'], "4" ), getItemNameForm( $arr['item_id'], "13" ), getItemNameForm( $arr['item_id'], "2_m" ) )."</b>", $script_tags );
			}
			else if( $arr[item_id] == 0 )
			{
				$player->AddMoney( $arr[number] );
				$player->syst( "Вы получаете <b>".my_word_form2( $arr['number'], 'монету', 'монеты', 'монет' )."</b>", $script_tags );
			}
			else $player->AddUMoney( $arr[number] );
		}
		else if( $arr[regime] == 7 )
		{
			$res2 = f_MQuery( "SELECT items.item_id, number FROM player_items, items WHERE player_id = {$player->player_id} AND items.parent_id={$arr[item_id]} AND player_items.item_id=items.item_id AND weared = 0" );
			$arr2 = f_MFetch( $res2 );
			$player->AddToLog( $arr2[item_id], -1, 0, $phrase_id );

			$player->DropItems( $arr2[item_id] );
			$player->syst( "Вы теряете <b>".my_word_form2( $arr['number'], getItemNameForm( $arr['item_id'], "4" ), getItemNameForm( $arr['item_id'], "13" ), getItemNameForm( $arr['item_id'], "2_m" ) )."</b>", $script_tags );
		}
	}
	
	// квестовые величины
	$res = f_MQuery( "SELECT * FROM phrase_values WHERE ( regime >= 4 ) AND phrase_id = $phrase_id" );
	while(  $arr = f_MFetch( $res ) )
	{
		if( $arr['regime'] == 4 ) $player->AlterQuestValue( $arr[value_id], $arr[value] );
		else if( $arr['regime'] == 5 ) $player->SetQuestValue( $arr[value_id], $arr[value] );
	}
			
	// Триггеры
	$res = f_MQuery( "SELECT * FROM phrase_triggers WHERE ( regime=2 OR regime=3 ) AND phrase_id = $phrase_id" );
	while( $arr = f_MFetch( $res ) )
	{
		if( $arr[regime] == 2 )
			$player->SetTrigger( $arr[trigger_id], 1 );
		else if( $arr[regime] == 3 )
			$player->SetTrigger( $arr[trigger_id], 0 );
	}


	// захардкоденные дполнительные действия фраз
	if( $phrase_id == 558 )
	{
		include_once( "mob.php" );
		$mob = new Mob;
		$mob->CreateMob( 29, 3, 2 );
		$mob->AttackPlayer( $player->player_id, 0, 0, true /* нападаем кроваво */ );
		f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( {$mob->combat_id}, '{$player->login} чувствует прилив сил. Атака всех магий у {$player->login} увеличивается на 10, жизнь у {$player->login} увеличивается на 100, восстанление жизни у {$player->login} увеличивается на 5' )" );
		$player->AlterAttrib( 151, 10 );
		$player->AlterAttrib( 141, 10 );
		$player->AlterAttrib( 131, 10 );
		$player->AlterAttrib( 101, 100 );
		$player->AlterAttrib( 222, 5 );
		die( "<script>location.href='combat.php';</script>" );
	}
	if( $phrase_id == 1267 ) // пегас свадьбы
	{
		include_once( "mob.php" );
		$mob = new Mob;
		$mob->CreateMirrorMob( $player->player_id, $player->location, $player->depth, 'Шамаханин', 'sham3.jpg' );
		$mob->AttackPlayer( $player->player_id, 0, 0, false /* нападаем НЕ кроваво */ );
		die( "<script>location.href='combat.php';</script>" );
	}
	if( $phrase_id == 1274 || $phrase_id == 1277 )
	{
		$player->SetQuestValue( 44, time( ) + 2 * 7 * 24 * 3600 );
	}
	if( $phrase_id == 637 )
	{
		f_MQuery( "UPDATE clans SET ta_lost=10 WHERE clan_id={$player->clan_id}" );
	}
	if( $phrase_id == 639 )
	{
		f_MQuery( "UPDATE clans SET ta_lost=12 WHERE clan_id={$player->clan_id}" );
	}
	if( $phrase_id == 1240 && $player->HasTrigger( 212 ) )
	{
		phrase_prolong_premium(0, 2);
		phrase_prolong_premium(1, 2);
		$player->syst2( "Вы продлеваете <b>премум-бои</b> и <b>премиум-добычу</b> на два дня!" );
	}
/*
	if ($phrase_id == 1576)
	{
		phrase_prolong_premium(0, 1);
		phrase_prolong_premium(1, 1);
		phrase_prolong_premium(2, 1);
		phrase_prolong_premium(3, 1);
		phrase_prolong_premium(4, 1);
		phrase_prolong_premium(5, 1);
		$player->syst2( "Вы получаете один день премиумов!" );
	}
*/

	return $parr[attack_id];
}

?>
