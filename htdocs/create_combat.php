<?

$combat_last_error = '';


// $lf - массив идентификаторов персонажей левой стороны
// $rg - массив идентификаторов персонажей правой стороны
// $loc, $place - место боя
// $win_action, $win_action_param - параметры боя для текущего игрока (возможности указать для всех в этой версии нет)
// $bloody - кровавый бой или нет

function CreateCombat( $lf, $rg, $loc, $place, $combat_type = 0 )
{
	global $player;
	$tm = time( );
	f_MQuery( "INSERT INTO combats ( last_turn_made, location, place ) VALUES ( $tm, $loc, $place )" );
	$combat_id = mysql_insert_id( );
	$l0 = 0; $n0 = 0;
	$l1 = 0; $n1 = 0;
	foreach( $lf as $i=>$a )
	{
		$ct = $combat_type;
		if( $ct == 1 ) $ct = $rg[0];
		if( $a != $player->player_id )
		{
			$plr = new Player( $a );
			$plr->UpdateHP( );
			$l0 += $plr->level; ++ $n0;
			f_MQuery( "INSERT INTO combat_players ( combat_id, player_id, side, log_type ) VALUES ( $combat_id, $a, 0, $ct )" );
			if( $i < count( $rg ) ) f_MQuery( "UPDATE combat_players SET opponent_id={$rg[$i]} WHERE player_id=$a" );
			$plr->UploadCombatToJavaServer( );
		}
		else
		{
			$player->UpdateHP( );
			$player->regime = 100;
			$l0 += $player->level; ++ $n0;
			f_MQuery( "INSERT INTO combat_players ( combat_id, player_id, side, log_type ) VALUES ( $combat_id, $a, 0, $ct )" );
			if( $i < count( $rg ) ) f_MQuery( "UPDATE combat_players SET opponent_id={$rg[$i]} WHERE player_id=$a" );
			$player->UploadCombatToJavaServer( );
		}
		f_MQuery( "UPDATE characters SET regime = 100 WHERE player_id = $a" );
	}
	foreach( $rg as $i=>$a )
	{
		$ct = $combat_type;
		if( $ct == 1 ) $ct = $lf[0];
		if( $a != $player->player_id )
		{
			$plr = new Player( $a );
			$plr->UpdateHP( );
			$l1 += $plr->level; ++ $n1;
			f_MQuery( "INSERT INTO combat_players ( combat_id, player_id, side, log_type ) VALUES ( $combat_id, $a, 1, $ct )" );
			if( $i < count( $lf ) ) f_MQuery( "UPDATE combat_players SET opponent_id={$lf[$i]} WHERE player_id=$a" );
			$plr->UploadCombatToJavaServer( );
		}
		else
		{
			$player->UpdateHP( );
			$player->regime = 100;
			$l1 += $player->level; ++ $n1;
			f_MQuery( "INSERT INTO combat_players ( combat_id, player_id, side, log_type ) VALUES ( $combat_id, $a, 1, $ct )" );
			if( $i < count( $lf ) ) f_MQuery( "UPDATE combat_players SET opponent_id={$lf[$i]} WHERE player_id=$a" );
			$player->UploadCombatToJavaServer( );
		}
		f_MQuery( "UPDATE characters SET regime = 100 WHERE player_id = $a" );
	}

	f_MQuery( "UPDATE combats SET side0_lvl=$l0, side1_lvl=$l1, side0_num=$n0, side1_num=$n1 WHERE combat_id=$combat_id" );
	
	return $combat_id;
}

function ccAttackPlayer( $attacker_id, $target_id, $ai, $bloody = true, $lim25 = true )
{
	f_MQuery( "LOCK TABLE combat_players WRITE, characters WRITE" );

	$res = f_MQuery( "SELECT combat_id FROM combat_players WHERE player_id=$attacker_id" );
	$arr = f_MFetch( $res );
	if( $arr )
	{
		f_MQuery( "UNLOCK TABLES" );
		return $arr[0];
	}

	$res = f_MQuery( "SELECT regime FROM characters WHERE player_id=$attacker_id" );
	$arr = f_MFetch( $res );
	if( $arr[0] == 100 )
	{
		global $combat_last_error;
		f_MQuery( "UNLOCK TABLES" );
		$combat_last_error = "Вы пытаетесь атаковать слишком быстро! Нажмите F5.";
		return 0;
	}

	$res = f_MQuery( "SELECT combat_id, side, ready FROM combat_players WHERE player_id = $target_id" );
	$arr = f_MFetch( $res );
	if( $arr )
	{
		if( $arr[2] >= 2 )
		{
			global $combat_last_error;
			f_MQuery( "UNLOCK TABLES" );
			$combat_last_error = "Игрок уже закончил бой";
			return 0;
		}
		f_MQuery( "UPDATE characters SET regime=100 WHERE player_id=$attacker_id" );
		f_MQuery( "UNLOCK TABLES" );

		global $player;
		$combat_id = $arr[0];
		$side = 1 - $arr['side'];
		$lvl = 0;

		if (!($combat_id==1753037 || $combat_id==1755274))
	{

		$tres = f_MQuery( "SELECT count( player_id ) FROM history_combats WHERE player_id=$attacker_id AND combat_id=$combat_id" );
		$tarr = f_MFetch( $tres );

		if( $tarr[0] > 0 )
		{
			global $combat_last_error;
			f_MQuery( "UPDATE characters SET regime=0 WHERE player_id=$attacker_id" );
			$combat_last_error = "Нельзя войти в один бой дважды";
			return 0;
		}
	}
		if( $lim25 )
		{
    		$tres = f_MQuery( "SELECT count( player_id ) FROM combat_players WHERE combat_id=$combat_id AND side=$side AND ready < 2" );
    		$tarr = f_MFetch( $tres );

    		if( $tarr[0] >= 25 )
    		{
    			global $combat_last_error;
    			f_MQuery( "UPDATE characters SET regime=0 WHERE player_id=$attacker_id" );
    			$combat_last_error = "В бою на одной стороне не может сражаться больше 25-ти персонажей";
    			return 0;
    		}
		}

		if( $attacker_id == $player->player_id )
		{
			$player->UpdateHP( true );
			$player->regime = 100;
			$lvl = $player->level;
		}
		else
		{
			$plr = new Player( $attacker_id );
			$lvl = $plr->level;
			$plr->UpdateHP( true );
		}
		
		$cres = f_MQuery( "SELECT cur_turn FROM combats WHERE combat_id = $arr[combat_id]" );
		$carr = f_MFetch( $cres );
		if( !$carr ) RaiseError( "Игрок в несуществующем бою?" );

		f_MQuery( "INSERT INTO combat_players( combat_id, player_id, side, ai, since_turn ) VALUES ( $combat_id, $attacker_id, $side, $ai, $carr[cur_turn] )" );
		f_MQuery( "UPDATE combats SET side{$side}_num=side{$side}_num+1, side{$side}_lvl=side{$side}_lvl+$lvl WHERE combat_id=$arr[combat_id]" );

// Возвращаем ресы при крафте
		$arr_cr = f_MFetch(f_MQuery( "SELECT * FROM player_craft WHERE player_id = $target_id" ));
		if ($arr_cr)
		{
			include_once("items.php");
			$AttackPlayer = new Player($target_id);
			$res_1 = f_MQuery( "SELECT * FROM recipes WHERE recipe_id = ".$arr_cr[recipe_id] );
			$arr_1 = f_MFetch( $res_1 );
			$arr_in = ParseItemStr( $arr_1['ingridients'] );
			foreach( $arr_in as $a=>$b )
			{
				$AttackPlayer->syst2($a);
				if( $a ) {$AttackPlayer->AddItems( $a, $b ); $AttackPlayer->AddToLogPost($a, $b, 40);}
				else {$AttackPlayer->AddMoney( $b ); $AttackPlayer->AddToLogPost(0, $b, 40);}
			}
			$AttackPlayer->syst2("Вы досрочно завершаете работу из-за того, что на Вас напали. Ресурсы, затраченные на крафт, были возвращены");
			f_MQuery( "DELETE FROM player_craft WHERE player_id = $AttackPlayer->player_id" );
			f_MQuery( "DELETE FROM player_craft_queue WHERE player_id = $AttackPlayer->player_id" );
		}
// Возвращаем ресы при крафте END
//		f_MQuery("DELETE FROM player_craft WHERE player_id=$target_id OR player_id=".$attacker_id);
		if( $bloody )
		{
			f_MQuery( "UPDATE combat_players SET bloody = 1 WHERE player_id = $attacker_id" );
			f_MQuery( "UPDATE combat_players SET bloody = 1 WHERE player_id = $target_id" );
		}

		if( $attacker_id == $player->player_id )
			$player->UploadCombatToJavaServer( );
		else
			$plr->UploadCombatToJavaServer( );

		return $combat_id;
	}
	else
	{
		f_MQuery( "UPDATE characters SET regime=100 WHERE player_id=$attacker_id" );
		f_MQuery( "UPDATE characters SET regime=100 WHERE player_id=$target_id" );
		f_MQuery( "UNLOCK TABLES" );

// Возвращаем ресы при крафте
		$arr_cr = f_MFetch(f_MQuery( "SELECT * FROM player_craft WHERE player_id = $target_id" ));
		if ($arr_cr)
		{
			include_once("items.php");
			$AttackPlayer = new Player($target_id);
			$res_1 = f_MQuery( "SELECT * FROM recipes WHERE recipe_id = ".$arr_cr[recipe_id] );
			$arr_1 = f_MFetch( $res_1 );
			$arr_in = ParseItemStr( $arr_1['ingridients'] );
			foreach( $arr_in as $a=>$b )
			{
				$AttackPlayer->syst2($a);
				if( $a ) {$AttackPlayer->AddItems( $a, $b ); $AttackPlayer->AddToLogPost($a, $b, 40);}
				else {$AttackPlayer->AddMoney( $b ); $AttackPlayer->AddToLogPost(0, $b, 40);}
			}
			$AttackPlayer->syst2("Вы досрочно завершаете работу из-за того, что на Вас напали. Ресурсы, затраченные на крафт, были возвращены");
			f_MQuery( "DELETE FROM player_craft WHERE player_id = $AttackPlayer->player_id" );
			f_MQuery( "DELETE FROM player_craft_queue WHERE player_id = $AttackPlayer->player_id" );
		}
// Возвращаем ресы при крафте END
//		f_MQuery("DELETE FROM player_craft WHERE player_id=$target_id OR player_id=".$attacker_id);

		$arr1 = Array( $target_id );
		$arr2 = Array( $attacker_id );
		
		$plr = new Player( $attacker_id );
		
		$combat_id = CreateCombat( $arr1, $arr2, $plr->location, $plr->depth, 0 );
		if( $ai ) f_MQuery( "UPDATE combat_players SET ai = true WHERE player_id = $attacker_id" );
		if( $bloody )
		{
			f_MQuery( "UPDATE combat_players SET bloody = 1 WHERE player_id = $attacker_id" );
			f_MQuery( "UPDATE combat_players SET bloody = 1 WHERE player_id = $target_id" );
		}
		
		return $combat_id;
	}
}

function setCombatTimeout( $combat_id, $timeout )
{
	f_MQuery( "UPDATE combats SET timeout=$timeout WHERE combat_id=$combat_id" );
}

?>
