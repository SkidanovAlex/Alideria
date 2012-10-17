<?

//die( );
/*

tournament_anouncements statuses:
0 - touenament is just announced;
1 - tournament will take place in less then an hour
2 - tournament will take place in less then half an hour
3 - tournament will take place in less then five finutes
4 - tournament is started
5 - tournament is finished

*/

require_once("time_functions.php");


include_once( 'functions.php' );
include_once( 'player.php' );
include_once( 'tournament.php' );
include_once( 'tournament_functions.php' );
include_once( 'mob.php' );
include_once( 'create_combat.php' );
include_once( 'magic_functions.php' );
include_once( 'tournament_order_functions.php' );
include_once( 'feathers.php' );

f_MConnect( );

//if( check_cookie( ) ) LogError( "Вызов технического скрипта tournament_time с куками" );

function winmsg( $player_id, $place, $prize, $name, $type )
{
	$plr = new Player( $player_id );
	if( $place == 1 )
	{
		$money = ceil( $prize * 0.6 );
		$plr->syst2( "Поздравляем! Вы победитель турнира <b>$name</b>. В качестве награды за победу вы получаете $money дублонов!" );
		if( $plr->clan_id != 0 && $type == 0 ) 
		{
			$plr->syst2( "Ваш Орден получает 10 очков славы!" );
			f_MQuery( "UPDATE clans SET glory=glory+10 WHERE clan_id={$plr->clan_id}" );
		}
		$plr->AddMoney( $money );
	}
	if( $place == 2 )
	{
		$money = ceil( $prize * 0.3 );
		$plr->syst2( "Поздравляем! Вы заняли второе место на турнире <b>$name</b>. В качестве награды за успешное выступление вы получаете $money дублонов!" );
		if( $plr->clan_id != 0 && $type == 0 ) 
		{
			$plr->syst2( "Ваш Орден получает 5 очков славы!" );
			f_MQuery( "UPDATE clans SET glory=glory+5 WHERE clan_id={$plr->clan_id}" );
		}
		$plr->AddMoney( $money );
	}
	if( $place == 3 )
	{
		$money = ceil( $prize * 0.1 );
		$plr->syst2( "Поздравляем! Вы заняли третье место на турнире <b>$name</b>. В качестве награды за успешное выступление вы получаете $money дублонов!" );
		if( $plr->clan_id != 0 && $type == 0 ) 
		{
			$plr->syst2( "Ваш Орден получает 2 очка славы!" );
			f_MQuery( "UPDATE clans SET glory=glory+2 WHERE clan_id={$plr->clan_id}" );
		}
		$plr->AddMoney( $money );
	}
}

function startTournament( $id, $min_level, $max_level, $name )
{
	$res = f_MQuery( "SELECT player_id FROM tournament_players WHERE tournament_id=$id" );
	$num = 0;
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr['player_id'] );

		$msg = "Внимание, <b>турнир $name</b> начался. ";
		if( $plr->level < $min_level || $plr->level > $max_level )
		{
			$msg .= "Вы не подходите по уровню для участия в нем, и автоматически исключаетесь из списка участников";
			f_MQuery( "DELETE FROM tournament_players WHERE player_id=$arr[player_id] AND tournament_id=$id" );
		}
		else if( $plr->location != 2 || $plr->depth != 43 )
		{
			$msg .= "Вы находитесь не в зале турниров, и автоматические исключаетесь из списка участников";
			f_MQuery( "DELETE FROM tournament_players WHERE player_id=$arr[player_id] AND tournament_id=$id" );
		}
		else 
		{
			$msg .= "Удачи в боях!";
            ++ $num;
			f_MQuery( "INSERT INTO tournament_busy_players ( player_id ) VALUES ( $arr[player_id] )" );

			$fres = f_MQuery( "SELECT * FROM player_feathers WHERE player_id={$arr[player_id]}" );
			f_MQuery( "DELETE FROM player_feathers WHERE player_id={$arr[player_id]}" );
            while( $farr = f_MFetch( $fres ) )
            {
            	undoFeather( $plr, $farr['feather_id'] );
            }
		}

		$plr->syst2( $msg );
	}

	$q = 8;
	while( $q < $num )
		$q *= 2;

	for( $i = $num; $i < $q; ++ $i )
	{
		$res = f_MQuery( "SELECT mob_id FROM mobs WHERE loc = 2 AND defend_depth = 43 ORDER BY rand() LIMIT 1" );
		$arr = f_MFetch( $res );
		$mob = new Mob( );
		$mob->CreateMob( $arr[0], 2, 43 );
		$player_id = $mob->player_id;
		f_MQuery( "INSERT INTO tournament_players ( tournament_id, player_id ) VALUES ( $id, $player_id )" );
		f_MQuery( "INSERT INTO tournament_mobs ( tournament_id, player_id ) VALUES ( $id, $player_id )" );
	}

	$res = f_MQuery( "SELECT player_id FROM tournament_players WHERE tournament_id=$id" );
	$num = 0;
	$human_ids = Array( );
	$mob_ids = Array( );
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr['player_id'] );
		
		checkZhorik( $plr, 6, 1 ); // квест жорика принять участие в турнире


		if( $plr->level < $max_level )
			$plr->AlterRealAttrib( 101, ( $max_level - $plr->level ) * 60 );
		if( $plr->login[0] != '!' ) $human_ids[] = $plr->player_id;
		else $mob_ids[] = $plr->player_id;
	}

	$player_ids = Array( );
	$i = 0; $j = 0;
	while( $i < count( $mob_ids ) || $j < count( $human_ids ) )
	{
		if( $i < count( $mob_ids ) )
			$player_ids[] = $mob_ids[$i ++];
		if( $j < count( $human_ids ) )
			$player_ids[] = $human_ids[$j ++];
	}

	$tournament = new Tournament( $id, $player_ids );
//	include_js( 'js/ii.js' );
	$tournament->SaveState( );
//	echo $tournament->Render( );
}

function processTournament( $id, $name, $prize, $type )
{
	global $player;

	echo "Processing tournament <b>$name</b><br>";

    // 0. Initialize tournament
    $player_ids = Array( );
	$res = f_MQuery( "SELECT * FROM tournament_players WHERE tournament_id = $id" );
	while( $arr = f_MFetch( $res ) ) $player_ids[] = $arr['player_id'];

	$tournament = new Tournament( $id, $player_ids );
	$tournament->LoadState( );

	if( $tournament->champion && $tournament->secondPlace && $tournament->thirdPlace )
	{
		f_MQuery( "UPDATE tournament_announcements SET status=5 WHERE tournament_id=$id" );
		echo "Tournament $name is finished<br>";
		return;
	}

	// 0. Force timeouts
	$tm = time( );

	// Форсим на обычном турнире
	$res = f_MQuery( "SELECT combat_id FROM combats WHERE location=2 AND place=43 AND last_turn_made + timeout < $tm" );
	while( $arr = f_MFetch( $res ) )
	{
		$combat_id = $arr[0];
		if(f_MValue("SELECT forces FROM combat_players WHERE combat_id = $combat_id AND ready = 0 LIMIT 1") >= 5)
		{
			f_MQuery( "UPDATE combat_players SET forces=forces+ 1, ready=1 WHERE combat_id = $combat_id AND ready = 0" );
			f_MQuery( "UPDATE combat_players SET card_id=384 WHERE combat_id = $combat_id AND ai=1" );
		}
		else
		{
			f_MQuery( "UPDATE combat_players SET forces=forces+ 1, ready=1 WHERE combat_id = $combat_id AND ready = 0" );
			f_MQuery( "UPDATE combat_players SET card_id=56 WHERE combat_id = $combat_id AND ai=1" );
		}
    	include_once( 'combat_functions.php' );
    	CheckTurnOver( $combat_id, 0, "<font color=darkblue>Ход форсируется автоматически</font><br>" );
	}

	$undefined = new Player( 286464 );
	// Форсим в Магии
	$res = f_MQuery( "SELECT `game_id` FROM `magic` WHERE `last_turn_made` + 180 < $tm" );
	while( $game_id = f_MFetch( $res ) )
	{
		// Получаем данные на игроков
		$happyPlayers = array( );
		$happyPlayers[0] = f_MValue( 'SELECT player_id FROM  `magic_players` WHERE `game_id` = '.$game_id[0].' LIMIT 0, 1' );
		$happyPlayers[1] = f_MValue( 'SELECT player_id FROM  `magic_players` WHERE `game_id` = '.$game_id[0].' LIMIT 1, 1' );

		// Побеждает случайнейший		
		$happyPlayer = mt_rand( 0, 1 );
		$Wonner = new Player( $happyPlayers[$happyPlayer] );
		$Looser = new Player( $happyPlayers[ 1 - $happyPlayer ] );
		
		// Проверяем, оба ли в Зале Турниров
		if( $Wonner->location != 2 or $Wonner->depth != 43 or
			 $Looser->location != 2 or $Looser->depth != 43
			)
		{
			continue;		
		}

		// Формула победы
		require_once( 'waste_stats.php' );
		f_MQuery( "UPDATE magic_players SET status=1 WHERE player_id={$Wonner->player_id}" );
		f_MQuery( "UPDATE magic_players SET status=2 WHERE player_id={$Looser->player_id}" );
		storeGame( 0, $Wonner->player_id, $Looser->player_id, 0, true );
	}

	// 1. Advance all winners
	$just_advanced = Array( );
	$incs = Array( );
	$res = f_MQuery( "SELECT * FROM tournament_queue WHERE tournament_id=$id AND expires < ".time( ) );
	while( $arr = f_MFetch( $res ) )
		$incs[$arr['player_id']] ++;
	foreach( $tournament->matches as $m )
	{
		if( count( $m->players ) == 2 && $m->res[0] < 2 && $m->res[1] < 2 )
		{
			if( $incs[$m->players[0]] )
			{
				$m->res[0] += $incs[$m->players[0]];
				$incs[$m->players[0]] = 0;
				f_MQuery( "UPDATE tournament_net SET score = ".$m->res[0]." WHERE tournament_id=$id AND player_id=".$m->players[0]." AND round={$m->id}" );

				if( $m->res[0] == 2 )
				{
					if( $m->winner_goes_to == 'champion' )
					{
						$tournament->champion = $m->players[0];
						winmsg( $m->players[0], 1, $prize, $name, $type );
						PlayerLeaveTournament( $m->players[0], $id );
					}
					else if( $m->winner_goes_to == 'third' )
					{
						$tournament->thirdPlace = $m->players[0];
						winmsg( $m->players[0], 3, $prize, $name, $type );
						PlayerLeaveTournament( $m->players[0], $id );
					}
					else
					{
						$tournament->matches[$m->winner_goes_to]->players[] = $m->players[0];
						f_MQuery( "INSERT INTO tournament_net ( tournament_id, round, player_id ) VALUES ( $id, {$m->winner_goes_to}, ".$m->players[0]." )" );
						$just_advanced[$m->players[0]] = 1;
					}

					if( $m->looser_goes_to == 'second' )
					{
						$tournament->secondPlace = $m->players[1];
						winmsg( $m->players[1], 2, $prize, $name, $type );
						PlayerLeaveTournament( $m->players[1], $id );
					}
					else if( (int)$m->looser_goes_to == 0 )
						PlayerLeaveTournament( $m->players[1], $id );
					else
					{
						$tournament->matches[$m->looser_goes_to]->players[] = $m->players[1];
						f_MQuery( "INSERT INTO tournament_net ( tournament_id, round, player_id ) VALUES ( $id, {$m->looser_goes_to}, ".$m->players[1]." )" );
						$just_advanced[$m->players[1]] = 1;
					}
				}
			}
			if( $incs[$m->players[1]] ) 
			{
				$m->res[1] += $incs[$m->players[1]];
				$incs[$m->players[1]] = 0;
				f_MQuery( "UPDATE tournament_net SET score = ".$m->res[1]." WHERE tournament_id=$id AND player_id=".$m->players[1]." AND round={$m->id}" );

				if( $m->res[1] == 2 )
				{
					if( $m->winner_goes_to == 'champion' )
					{
						$tournament->champion = $m->players[1];
						winmsg( $m->players[1], 1, $prize, $name, $type );
						PlayerLeaveTournament( $m->players[1], $id );
					}
					else if( $m->winner_goes_to == 'third' )
					{
						$tournament->thirdPlace = $m->players[1];
						winmsg( $m->players[1], 3, $prize, $name, $type );
						PlayerLeaveTournament( $m->players[1], $id );
					}
					else
					{
						$tournament->matches[$m->winner_goes_to]->players[] = $m->players[1];
						f_MQuery( "INSERT INTO tournament_net ( tournament_id, round, player_id ) VALUES ( $id, {$m->winner_goes_to}, ".$m->players[1]." )" );
						$just_advanced[$m->players[1]] = 1;
					}

					if( $m->looser_goes_to == 'second' )
					{
						$tournament->secondPlace = $m->players[0];
						winmsg( $m->players[0], 2, $prize, $name, $type );
						PlayerLeaveTournament( $m->players[0], $id );
					}
					else if( (int)$m->looser_goes_to == 0 )
						PlayerLeaveTournament( $m->players[0], $id );
					else
					{
						$tournament->matches[$m->looser_goes_to]->players[] = $m->players[0];
						f_MQuery( "INSERT INTO tournament_net ( tournament_id, round, player_id ) VALUES ( $id, {$m->looser_goes_to}, ".$m->players[0]." )" );
						$just_advanced[$m->players[0]] = 1;
					}
				}
			}
		}
	}
	f_MQuery( "DELETE FROM tournament_queue WHERE tournament_id=$id AND expires < ".time( ) );

	$res = f_MQuery( "SELECT * FROM tournament_queue WHERE tournament_id=$id" );
	while( $arr = f_MFetch( $res ) )
		$just_advanced[$arr['player_id']] = true;

    // 2. Start new combats
    // we do combats before advances in order to give one minute to them to see against which person they will fight
	foreach( $tournament->matches as $m )
	{
		if( count( $m->players ) == 2 && $m->res[0] < 2 && $m->res[1] < 2 )
		{
			$ok = true;
			$id1 = $m->players[0];
			$id2 = $m->players[1];
			if( $just_advanced[$id1] || $just_advanced[$id2] )
			{
				echo "Do not process $id1 - $id2, one of them has just advanced<br>";
				continue;
			}
			$logins = Array( );
			$res = f_MQuery( "SELECT regime, player_id, login FROM characters WHERE player_id IN ($id1,$id2)" );
			while( $arr = f_MFetch( $res ) ) if( $arr[0] == 100 ) 
			{
				// check combat
				$cres = f_MQuery( "SELECT ready, combat_id FROM combat_players WHERE player_id=$arr[1]" );
				$carr = f_MFetch( $cres );
				if( $carr[0] >= 2 )
				{
					$plr = new Player( $arr[1] );
					$player = $plr;
					$plr->LeaveCombat( $carr[1] );
					$plr->RestoreAttribs( );
					$plr->UploadCombatToJavaServer( );
					$plr->syst2( "/items" );
					$logins[$arr[1]] = $arr[2];
				}
				$ok = false;
			}
			else if( $arr[0] == 111 )
			{
				$player_id = $arr[1];
                $cres = f_MQuery( "SELECT * FROM magic_players WHERE player_id=$player_id" );
                $carr = f_MFetch( $cres );
                $game_id = $carr['game_id'];
                $status = $carr['status'];

                if( $status != 0 )
                {
                	f_MQuery( "DELETE FROM magic_players WHERE player_id=$player_id" );
                	f_MQuery( "UNLOCK TABLES" );

                	$cres = f_MQuery( "SELECT count( player_id ) FROM magic_players WHERE player_id=$player_id" );
                	$carr = f_MFetch( $cres );
                	if( $carr[0] == 0 ) 
                	{
                		f_MQuery( "DELETE FROM magic WHERE game_id=$game_id" );
                		f_MQuery( "DELETE FROM magic_cards WHERE game_id=$game_id" );
                	}
                		
					$plr = new Player( $arr[1] );
					$plr->syst2( "/items" );

                	// проверим турнир
                	if( $status == 1 )
                	{
                		$expires = time( ) - 5;
                		f_MQuery( "INSERT INTO tournament_queue( tournament_id, player_id, expires ) VALUES ( $id, $player_id, $expires )" );
                	}
            		f_MQuery( "UPDATE characters SET regime=0 WHERE player_id=$player_id" );
            	}
            	$ok = false;
			}
			else $logins[$arr[1]] = $arr[2];

			if( $logins[$id1][0] == '!' && $logins[$id2][0] == '!' )
			{
				$expires = time( ) - 5;
				if( mt_rand( 1, 2 ) == 1 )
				{
					f_MQuery( "INSERT INTO tournament_queue VALUES ( $id, $id1, $expires )" );
					f_MQuery( "INSERT INTO tournament_queue VALUES ( $id, $id1, $expires )" );
				} else
				{
					f_MQuery( "INSERT INTO tournament_queue VALUES ( $id, $id2, $expires )" );
					f_MQuery( "INSERT INTO tournament_queue VALUES ( $id, $id2, $expires )" );
				}
			}
			else if( $type == 1 && ( $logins[$id1][0] == '!' || $logins[$id2][0] == '!' ) )
			{
				echo "$id1 : $id2<br>";
				$expires = time( ) - 5;
				if( $logins[$id2][0] == '!' )
				{
					f_MQuery( "INSERT INTO tournament_queue VALUES ( $id, $id1, $expires )" );
					f_MQuery( "INSERT INTO tournament_queue VALUES ( $id, $id1, $expires )" );
				} else
				{
					f_MQuery( "INSERT INTO tournament_queue VALUES ( $id, $id2, $expires )" );
					f_MQuery( "INSERT INTO tournament_queue VALUES ( $id, $id2, $expires )" );
				}
			}
			else if( $ok && $type == 0 )
			{
				$arr1 = Array( $id1 );
				$arr2 = Array( $id2 );

				echo "Starting combat between ".$logins[$id1]." and ".$logins[$id2]."<br>";

				$plr1 = new Player( $id1 );
				$plr2 = new Player( $id2 );
				$player = $plr1;

				$combat_id = CreateCombat( $arr1, $arr2, 2, 43 );
				setCombatTimeout( $combat_id, 75 );
		
				f_MQuery( "UPDATE combat_players SET win_action = 3, win_action_param = $id, log_type=-5 WHERE combat_id = $combat_id" );
				f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( {$combat_id}, 'Начался бой между <b>".$logins[$id1]."</b> и <b>".$logins[$id2]."</b> в рамках турнира <b>$name</b><br>' )" );
				f_MQuery( "UPDATE characters SET regime = 100 WHERE player_id = {$id1} OR player_id = {$id2}" );
				if( $logins[$id1][0] == '!' ) f_MQuery( "UPDATE combat_players SET ai = true WHERE player_id = $id1" );
				if( $logins[$id2][0] == '!' ) f_MQuery( "UPDATE combat_players SET ai = true WHERE player_id = $id2" );

				$val1 = f_MFetch( f_MQuery( "SELECT value FROM player_attributes WHERE player_id=$id1 AND attribute_id=101" ) );
				$val2 = f_MFetch( f_MQuery( "SELECT value FROM player_attributes WHERE player_id=$id2 AND attribute_id=101" ) );

				f_MQuery( "UPDATE player_attributes SET value= $val1[0] WHERE player_id=$id1 AND attribute_id=1" );
				f_MQuery( "UPDATE player_attributes SET value= $val2[0] WHERE player_id=$id2 AND attribute_id=1" );

				$plr1->syst2( "Начался бой против <b>".$logins[$id2]."</b> в рамках турнира <b>$name</b>" );
				$plr2->syst2( "Начался бой против <b>".$logins[$id1]."</b> в рамках турнира <b>$name</b>" );
				$plr1->syst2( "/combat" );
				$plr2->syst2( "/combat" );
			}
			else if( $ok && $type == 1 )
			{
	    		create_game( $id1, $id2, 0 );

				$plr1 = new Player( $id1 );
				$plr2 = new Player( $id2 );

	    		$plr1->SetRegime( 111 );
	    		$plr2->SetRegime( 111 );

				$plr1->syst2( "Началась игра против <b>".$logins[$id2]."</b> в рамках турнира <b>$name</b>" );
				$plr2->syst2( "Началась игра против <b>".$logins[$id1]."</b> в рамках турнира <b>$name</b>" );
				$plr1->syst2( "/items" );
				$plr2->syst2( "/items" );
			}
		}
	}
	

	$tournament->SaveResults( );
}

function spam( $id, $msg )
{
	$res = f_MQuery( "SELECT player_id FROM tournament_players WHERE tournament_id=$id" );
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr['player_id'] );
		$plr->syst2( $msg );
	}
	$res = f_MQuery( "SELECT * FROM tournament_group_bets WHERE tournament_id=$id" );
	while( $arr = f_MFetch( $res ) )
	{
		for( $i = 0; $i < 6; ++ $i ) if( $arr["slot_{$i}"] )
		{
    		$plr = new Player( $arr["slot_{$i}"] );
    		$plr->syst2( $msg );
		}
	}
}

f_MQuery( "START TRANSACTION" );
$res = f_MQuery( "SELECT * FROM tournament_announcements WHERE status <= 4" );
while( $arr = f_MFetch( $res ) )
{
	if( $arr['status'] == 0 && $arr['date'] - time( ) < 3600 )
	{
		spam( $arr[tournament_id], "Внимание! До начала <b>турнира \"$arr[name]\"</b> остался <b>один час</b>. Вы должны обязательно находиться в зале турниров в момент начала, чтобы принять участие. Обратите внимание, что после начала турнира вы не сможете покинуть зал до тех пор, пока турнир не окончится." );
		f_MQuery( "UPDATE tournament_announcements SET status = status + 1 WHERE tournament_id=$arr[tournament_id]" );
	}
	if( $arr['status'] == 1 && $arr['date'] - time( ) < 1800 )
	{
		spam( $arr[tournament_id], "Внимание! До начала <b>турнира \"$arr[name]\"</b> осталось <b>30 минут</b>. Вы должны обязательно находиться в зале турниров в момент начала, чтобы принять участие. Обратите внимание, что после начала турнира вы не сможете покинуть зал до тех пор, пока турнир не окончится." );
		f_MQuery( "UPDATE tournament_announcements SET status = status + 1 WHERE tournament_id=$arr[tournament_id]" );
	}
	if( $arr['status'] == 2 && $arr['date'] - time( ) < 300 )
	{
		spam( $arr[tournament_id], "Внимание! До начала <b>турнира \"$arr[name]\"</b> осталось <b>5 минут</b>. Вы должны обязательно находиться в зале турниров в момент начала, чтобы принять участие. Обратите внимание, что после начала турнира вы не сможете покинуть зал до тех пор, пока турнир не окончится." );
		f_MQuery( "UPDATE tournament_announcements SET status = status + 1 WHERE tournament_id=$arr[tournament_id]" );
	}
	if( $arr['status'] == 3 && $arr['date'] - time( ) < 0 )
	{
		if( $arr['type'] != 2 ) startTournament( $arr['tournament_id'], $arr['min_level'], $arr['max_level'], $arr['name'] );
		else StartGroupTournament( $arr['tournament_id'] );
		f_MQuery( "UPDATE tournament_announcements SET status = status + 1 WHERE tournament_id=$arr[tournament_id]" );
	}
	if( $arr['status'] == 4 )
	{
		if( $arr['type'] != 2 ) processTournament( $arr['tournament_id'], $arr['name'], $arr['prize'], $arr['type'] );
		else ProcessGroupTournament( $arr['tournament_id'] );
	}
}

f_MQuery( "COMMIT" );

echo "Moo!";

?>
