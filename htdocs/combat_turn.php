<?

include_once( 'functions.php' );
include_once( 'player.php' );
include_once( 'card.php' );
include_once( 'aura.php' );
include_once( 'creature.php' );
include_once( 'mob.php' );
include_once( "expirience.php" );

f_MConnect( );

	function combat_calcDamage( $a, $p, $d, $genre, $r = 0 )
	{
		if( $p->damage_block > 0 ) return 0;

		$d *= ( $p->damage_coef + 1e-7 );
		$d = (int)$d;
		if( $a !== false ) $d += (int)$a->attributes[510 + $genre];
		$d -= (int)$p->attributes[520 + $genre];

		$d -= $r;
		if( $d < 0 ) $d = 0;

		if( $p->poison_dream )
		{
			$d = - $d;
		}
		else if( $p->nature_shield && $p->attributes[140] > 0 )
		{
			$nd = min( (int)( $d / 2 ), $p->attributes[140] );
//			LogError( "MOo: [$d, {$p->attributes[140]}, $nd]" );
			$p->attributes[140] -= $nd;
			$d -= $nd;
		}

		return $d;
	}

class CombatPlayer
{
	var $card;
	var $creatures;
	var $auras;
	var $attributes;
	var $opponent_id;
	var $opponent_id_2;

	var $target;
	var $damage_block;
	var $damage_coef;

	var $nature_shield;
	var $poison_dream;

	var $killed;

	// statistics
	var $dmg_given_magic;
	var $dmg_given_spells;
	var $dmg_given_resist;
	var $dmg_given_creatures;
	var $dmg_magic;
	var $dmg_spells;
	var $dmg_creatures;
	var $turns_successfull;
	var $turns_unsuccessfull;
	var $turns_draw;

	var $row;
	var $turn_arr;

	var $forces;

	var $player;
};

class Combat
{
	var $combat_id;
	var $sides; // only ids here
	var $side_players; // pointers to players
	var $players;
	var $cplayers;
	var $texts;
	var $turn;

	var $location;
	var $depth;

	function Combat( $id )
	{
		$this->combat_id = $id;
		$res = f_MQuery( "SELECT cur_turn, location, place FROM combats WHERE combat_id = $id" );
		$arr = f_MFetch( $res );
		if( !$arr ) RaiseError( "Combat: Неизвестный ID боя: $id" );
		$this->turn = $arr[0];
		$this->location = $arr[1];
		$this->depth = $arr[2];
	}

	function flavorText( $login1, $login2, $genre1, $genre2 )
	{
		$q = mt_rand( 1, 3 );
		return ",[1,'$login1','$login2',$q,$genre1,$genre2]";
	}

	function neutralDamage( $player, $genre, $cur1=0 )
	{
		$player->attributes[130 + $player->card->genre * 10] -= $player->card->cost;
		$cur = $player->player->level * 5;
		if ($cur1 > 0) $cur = $cur1 * 2;
		$cur = $this->calcDamage( false, $player, $cur, $genre, 0/*$player->attributes[132 + 10 * $genre]*/ ); // защита не учитывается при нейтрале
		$cur -= $player->attributes[502];
		if( $cur < 0 ) $cur = 0;

		$player->attributes[1] -= $cur;
		$player->dmg_given_resist += $cur;

		if( $cur )
		{
            if( $player->card->genre == 0 ) $player->turn_arr .= ",[csdir2+'wa.gif',-{$cur},\"\"]";
            if( $player->card->genre == 1 ) $player->turn_arr .= ",[csdir2+'na.gif',-{$cur},\"\"]";
            if( $player->card->genre == 2 ) $player->turn_arr .= ",[csdir2+'fa.gif',-{$cur},\"\"]";
        }

		return ",[2,'{$player->player->login}',$cur,$genre]";
	}

	function processCard( $p1, $p2, $side, $magic_dmg = true )
	{
		global $counters_str;
		global $global_dmg;

		if( $p1->card === null )
			return ",[3,'{$p1->player->login}']";
		else
		{
			$cast_str = ",[4,'{$p1->player->login}',".$p1->card->Text( )."]";

			// расход заряда посоха
			$res = f_MQuery( "SELECT staff FROM player_selected_cards WHERE player_id={$p1->player->player_id} AND card_id={$p1->card->card_id}" );
			$arr = f_MFetch( $res );
			if( $arr && $arr[0] != 0 )
			{
				$ibv = f_MValue("SELECT COUNT(*) FROM player_items, items WHERE player_id={$p1->player->player_id} AND weared > 1 AND weared < 14 AND items.inner_base_spell_id={$p1->card->card_id} AND items.item_id=player_items.item_id");
				if (!$ibv)
				{
				//LogError( "Moo: player_id={$p1->player->player_id} AND card_id={$p1->card->card_id}" );
				$iires = f_MQuery( "SELECT items.*, player_items.weared FROM player_items,items WHERE weared > 1 AND weared < 14 AND charges > 0 AND items.inner_spell_id={$p1->card->card_id} AND items.item_id=player_items.item_id AND player_id={$p1->player->player_id} ORDER BY rand() LIMIT 1" );
    			$iiarr = f_MFetch( $iires );
    			if( $iiarr )
    			{
    				$slot = $iiarr['weared'];
    				$item_id = copyItem( $iiarr['item_id'] );
    				if( $item_id != $iiarr['item_id'] )
    					f_MQuery( "UPDATE player_items SET item_id=$item_id WHERE weared=$slot AND player_id={$p1->player->player_id}" );
    				$charges = $iiarr['charges'];
    				-- $charges;
    				f_MQuery( "UPDATE items SET charges_spent=charges_spent+1, charges = $charges WHERE item_id=$item_id" );
    			}
    			else return ",[5,'{$p1->player->login}']";
    			}
			}
			// расход заряда - конец

			// Попроцессим свиток
			$p1->attributes[130 + $p1->card->genre * 10] -= $p1->card->cost;
			$str_1 = $p1->card->Process2( $p1, $p2, $this->side_players[$side], $this->side_players[1 - $side], $this->combat_id, $this->turn, false );
			$str = ",[0,\"<br>".$str_1."\"]";

			$bodrost = (int)$p1->attributes[500];
			if( $p1->card->genre != 1 ) $bodrost = 0;
			if( $bodrost > 0 )
			{
				$str .= ",[6,'{$p1->player->login}',$bodrost]";
				$p1->attributes[1] += $bodrost;
			}

			$p2->turn_arr .= ",[csdir1+'".$p1->card->img_small."',-{$global_dmg},\"".$str_1."\"]";

			$dmgst = '';

			// Удача - либо $magic_dmg, либо амулик природы
            // Если удача - попроцессим еще разик
            $koef_luck = f_MValue("SELECT koef_value FROM koefs WHERE koef_id = 3");
   			if( !$p1->card->unlucky && ( ( !$magic_dmg && mt_rand( 1, 100 ) <= $p1->attributes[315] ) || ( $magic_dmg && mt_rand( 1, 1000 ) <= $p1->attributes[13] * $koef_luck / $p1->player->level ) ) )
   			{
   				$cast_str = ",[7,'{$p1->player->login}',".$p1->card->Text( )."]";
   				$str_1 = $p1->card->Process2( $p1, $p2, $this->side_players[$side], $this->side_players[1 - $side], $this->combat_id, $this->turn, true );
   				$str .= ",[0,\"<br>".$str_1."\"]";
    			if( $bodrost > 0 )
    			{
					$str .= ",[6,'{$p1->player->login}',$bodrost]";
    				$p1->attributes[1] += $bodrost;
    			}

				$p2->turn_arr .= ",[csdir1+'".$p1->card->img_small."',-{$global_dmg},\"".$str_1."\"]";

    			// Если триплкаст - процессим третий раз
    			if( mt_rand( 1, 100 ) <= $p1->attributes[313] )
    			{
       				$cast_str = ",[32,'{$p1->player->login}',".$p1->card->Text( )."]";
       				$str_1 = $p1->card->Process2( $p1, $p2, $this->side_players[$side], $this->side_players[1 - $side], $this->combat_id, $this->turn, false );
       				$str .= ",[0,\"<br>".$str_1."\"]";
        			if( $bodrost > 0 )
        			{
						$str .= ",[6,'{$p1->player->login}',$bodrost]";
        				$p1->attributes[1] += $bodrost;
        			}
					$p2->turn_arr .= ",[csdir1+'".$p1->card->img_small."',-{$global_dmg},\"".$str_1."\"]";
    			}
   			}

			if( $magic_dmg )
			{
                // Повреждение магией
                $krit = 1;
                $koef_krit = f_MValue("SELECT koef_value FROM koefs WHERE koef_id = 5");
                if( mt_rand( 1, 1000 ) <= $p1->attributes[16] * $koef_krit / $p1->player->level ) // расчет крита
                {
                	$krit = 2;
                	if( mt_rand( 1, 100 ) <= $p1->attributes[316] ) $krit = 3;
                }
    			$dmg = $p1->attributes[131 + $p1->card->genre * 10] * $krit;

    			$dmg = $this->calcDamage( $p1, $p2, $dmg, $card->genre, $p2->attributes[132 + $p1->card->genre * 10] );

    			if( $dmg != 0 )
    			{
    				$p2->attributes[1] -= $dmg;
    				$p2->dmg_given_magic += $dmg;
    				$p1->dmg_magic += $dmg;
                    $dmgst = ",[8,'{$p1->player->login}',$dmg,{$p1->card->genre},".($krit-1)."]";
                    if( $p1->card->genre == 0 ) $p2->turn_arr .= ",[csdir2+'wa.gif',-{$dmg},\"\"]";
                    if( $p1->card->genre == 1 ) $p2->turn_arr .= ",[csdir2+'na.gif',-{$dmg},\"\"]";
                    if( $p1->card->genre == 2 ) $p2->turn_arr .= ",[csdir2+'fa.gif',-{$dmg},\"\"]";
    			}
			}

			return $cast_str.$str.",[9]$dmgst,[9]";
		}
	}

	function CollectDataSide( $id )
	{
		$res = f_MQuery( "SELECT * FROM combat_players WHERE combat_id = {$this->combat_id} AND side = $id AND ready < 2 ORDER BY entry_id" );
		while( $arr = f_MFetch( $res ) )
		{
			$cp = new CombatPlayer;
			$cp->player = new Player( $arr[player_id] );
			$cp->card = ( $arr[card_id] == -1 ) ? null : new Card( $arr[card_id] );
			if( $arr[card_id] != -1 ) $cp->card->LoadPlayer( $cp->player );
			$cp->target = $arr['target'];
			$cp->damage_block = $arr[damage_block];
			$cp->damage_coef = $arr[damage_coef];

			$cp->dmg_given_magic     = $arr['dmg_given_magic'];
			$cp->dmg_given_spells    = $arr['dmg_given_spells'];
			$cp->dmg_given_resist    = $arr['dmg_given_resist'];
			$cp->dmg_given_creatures = $arr['dmg_given_creatures'];
			$cp->dmg_magic           = $arr['dmg_magic'];
			$cp->dmg_spells          = $arr['dmg_spells'];
			$cp->dmg_creatures       = $arr['dmg_creatures'];
			$cp->turns_successfull   = $arr['turns_successfull'];
			$cp->turns_unsuccessfull = $arr['turns_unsuccessfull'];
			$cp->turns_draw          = $arr['turns_draw'];

			$cp->row = $arr['row'];
			$cp->forces = $arr['forces'];

			$cp->creatures = Array( null, null, null );
			$cres = f_MQuery( "SELECT * FROM combat_creatures WHERE player_id = $arr[player_id]" );
			while( $carr = f_MFetch( $cres ) )
			{
				$cp->creatures[$carr[slot_id]] = new Creature( $carr[creature_id] );
				$cp->creatures[$carr[slot_id]]->attack = $carr[attack];
				$cp->creatures[$carr[slot_id]]->defence = $carr[defence];
			}

			$cp->auras = array( );
			$cp->nature_shield = false;
			$cp->poison_dream = false;
			$ares = f_MQuery( "SELECT aura_id FROM combat_auras WHERE player_id={$arr[player_id]}" );
			while( $aarr = f_MFetch( $ares ) )
			{
				$cp->auras[] = new Aura( $aarr[0] );
				if( $aarr[0] == 16 ) $cp->nature_shield = true;
				if( $aarr[0] == 6 ) $cp->poison_dream = true;
			}

			$cp->attributes = Array( );
			$ares = f_MQuery( "SELECT * FROM player_attributes WHERE player_id = $arr[player_id]" );
			while( $aarr = f_MFetch( $ares ) )
			{
				$cp->attributes[$aarr['attribute_id']] = $aarr['value'];
				settype( $cp->attributes[$aarr['attribute_id']], 'integer' );
			}

			$cp->opponent_id = $arr[opponent_id];
			$cp->killed = false;

			$cp2 = clone( $cp );
			if( $cp->card ) $cp2->card = clone( $cp->card );
			for( $i = 0; $i < 3; ++ $i ) if( $cp->creatures[$i] ) $cp2->creatures[$i] = clone( $cp->creatures[$i] );
			$cp2->player = clone( $cp->player );


			$cp2->opponent_id = 0;

			$this->players[$arr[player_id]] = $cp;
			$this->cplayers[$arr[player_id]] = $cp2;
			$this->sides[$id][] = $arr[player_id];
			$this->side_players[$id][] = $this->cplayers[$arr[player_id]];
		}
	}

	function CollectData( )
	{
		$this->sides = Array( );
		$this->CollectDataSide( 0 );
		$this->CollectDataSide( 1 );
	}

	function UpdateOpponents( )
	{
		$n = min( count( $this->sides[0] ), count( $this->sides[1] ) );
		for( $i = 0; $i < $n; ++ $i )
		{
			$this->cplayers[$this->sides[0][$i]]->opponent_id = $this->players[$this->sides[1][$i]]->player->player_id;
			$this->cplayers[$this->sides[1][$i]]->opponent_id = $this->players[$this->sides[0][$i]]->player->player_id;
			$this->cplayers[$this->sides[0][$i]]->opponent_id_2 = $this->players[$this->sides[1][$i]]->player->player_id;
			$this->cplayers[$this->sides[1][$i]]->opponent_id_2 = $this->players[$this->sides[0][$i]]->player->player_id;
			if( $this->players[$this->sides[1][$i]]->card ) $this->cplayers[$this->sides[0][$i]]->row .= $this->players[$this->sides[1][$i]]->card->genre;
			if( $this->players[$this->sides[0][$i]]->card ) $this->cplayers[$this->sides[1][$i]]->row .= $this->players[$this->sides[0][$i]]->card->genre;
		}
	}

	function calcDamage( $a, $p, $d, $genre, $r = 0 )
	{
		return combat_calcDamage( $a, $p, $d, $genre, $r );
	}

	function processCreatureAttackingPlayer( &$creature, &$c1, &$c2, $dmg = -1 )
	{
		$bg = '';
		if( $dmg == -1 ) $dmg = $creature->attack;
		$dmg = $this->calcDamage( false, $c2, $dmg, (int)$creature->genre );
		if( $dmg == 0 )
		{
			$bg .= ",[28,".$creature->Text( ).",'{$c1->player->login}','{$c2->player->login}']";
		}
		else
		{
			$c2->attributes[1] -= $dmg;
			$c2->dmg_given_creatures += $dmg;
			$c1->dmg_creatures += $dmg;
			$bg .= ",[29,".$creature->Text( ).",'{$c1->player->login}','{$c2->player->login}',{$dmg}]";
			$c2->turn_arr .= ",[csdir1+'".$creature->image."',-{$dmg},\"<b>{$creature->name}</b> атакует игрока <b>{$c2->player->login}</b>\"]";
		}

		return $bg;
	}

	function ProcessCreatures( )
	{
		$n = min( count( $this->sides[0] ), count( $this->sides[1] ) );
		for( $i = 0; $i < $n; ++ $i )
		{
			$dd = '';
			$bg = '';

			$c1 = $this->cplayers[$this->sides[0][$i]];
			$c2 = $this->cplayers[$this->sides[1][$i]];
			for( $j = 0; $j < 3; ++ $j )
			{
				$m1 = $c1->creatures[$j];
				$m2 = $c2->creatures[$j];
				if( $m1 != null && $m1->just_summoned && !$m1->haste ) $m1 = null;
				if( $m2 != null && $m2->just_summoned && !$m2->haste ) $m2 = null;

				if( $m1 !== null && $m2 === null )
				{
					$creature = $c1->creatures[$j];
					$bg .= $this->processCreatureAttackingPlayer( $creature, $c1, $c2 );
				}
				else if( $m1 === null && $m2 !== null )
				{
					$creature = $c2->creatures[$j];
					$bg .= $this->processCreatureAttackingPlayer( $creature, $c2, $c1 );
				}
				else if( $m1 !== null && $m2 !== null )
				{
					$creature1 = $c1->creatures[$j];
					$creature2 = $c2->creatures[$j];

					$cr1f = false; $cr2f = false;

					if( $creature1->firststrike && !$creature2->firststrike ) $cr1f = true;
					if( $creature2->firststrike && !$creature1->firststrike ) $cr2f = true;

					if( $cr1f )
					{
    					$creature2->defence -= $creature1->attack;
    					$bg .= ",[30,".$creature1->Text( ).",'{$c1->player->login}',".$creature2->Text( ).",'{$c2->player->login}',{$creature1->attack}]";
    				}
    				if( $cr2f )
    				{
    					$creature1->defence -= $creature2->attack;
    					$bg .= ",[30,".$creature2->Text( ).",'{$c2->player->login}',".$creature1->Text( ).",'{$c1->player->login}',{$creature2->attack}]";
    				}

    				$rem_od2 = $creature2->defence; //запомним сейчас, чтобы все было честно
    				if( !$cr1f && $creature1->defence > 0 )
    				{
    					$creature2->defence -= $creature1->attack;
    					$bg .= ",[30,".$creature1->Text( ).",'{$c1->player->login}',".$creature2->Text( ).",'{$c2->player->login}',{$creature1->attack}]";
					}

    				if( !$cr2f && $rem_od2 > 0 )
    				{
    					$creature1->defence -= $creature2->attack;
    					$bg .= ",[30,".$creature2->Text( ).",'{$c2->player->login}',".$creature1->Text( ).",'{$c1->player->login}',{$creature2->attack}]";
    				}

    				$c1tr = false; $c2tr = false;
    				if( $creature1->trample ) $c1tr = true;
					if( $creature2->trample ) $c2tr = true;

					if( $creature2->defence <= 0 )
					{
						if( $c1tr && $creature2->defence < 0 ) $bg .= $this->processCreatureAttackingPlayer( $creature1, $c1, $c2, -$creature2->defence );
						$dd .= ",[31,".$creature2->Text( ).",'{$c2->player->login}']";
						$c2->creatures[$j] = null;
					}
					if( $creature1->defence <= 0 )
					{
						if( $c2tr && $creature1->defence < 0 ) $bg .= $this->processCreatureAttackingPlayer( $creature2, $c2, $c1, -$creature1->defence );
						$dd .= ",[31,".$creature1->Text( ).",'{$c1->player->login}']";
						$c1->creatures[$j] = null;
					}
				}

                // actions
                if ($m1 !== null && $m1->effect_always)
                {
                    $str_111 = $m1->Process2( $m1->effect_always, $c1, $c2, $this->side_players[0], $this->side_players[1], $this->combat_id, $this->turn, $j );
                    $bg .= ",[0,\"".$str_111."<br>\"]";
                }
                if ($m2 !== null && $m2->effect_always)
                {
                    $str_111 = $m2->Process2( $m2->effect_always, $c2, $c1, $this->side_players[1], $this->side_players[0], $this->combat_id, $this->turn, $j );
                    $bg .= ",[0,\"".$str_111."<br>\"]";
                }
			}

			$this->texts[$i] = $bg.$dd . $this->texts[$i];
		}
	}

	function ProcessCards( )
	{
		$n = min( count( $this->sides[0] ), count( $this->sides[1] ) );
		$m = max( count( $this->sides[0] ), count( $this->sides[1] ) );

		for( $i = 0; $i < $n; ++ $i )
		{
			$bg = '';
			$this->texts[$i] = '';

			$p1 = $this->cplayers[$this->sides[0][$i]];
			$p2 = $this->cplayers[$this->sides[1][$i]];

			if( $p1->card === null || $p2->card === null )
			{
				$bg .= $this->processCard( $p1, $p2, 0 );
				$bg .= $this->processCard( $p2, $p1, 1 );
				$w1 = ( $p1->card !== null );
				$w2 = ( $p2->card !== null );
			}
			else // оба чувака поставили свиток, определим кто круче
			{
				$bg .= $this->flavorText( $p1->player->login, $p2->player->login, $p1->card->genre, $p2->card->genre );
				if ( $p1->card->genre == 3 && $p2->card->genre != 3 )
				{
					$p1->turns_successfull ++;
					$p2->turns_unsuccessfull ++;

					$bg .= $this->processCard( $p1, $p2, 0 );
					$w1 = true; $w2 = false;
				}else
				if ( $p2->card->genre == 3 && $p1->card->genre != 3 )
				{
					$p2->turns_successfull ++;
					$p1->turns_unsuccessfull ++;

					$bg .= $this->processCard( $p2, $p1, 1 );
					$w2 = true; $w1 = false;
				}else
				if( $p1->card->genre == ( $p2->card->genre + 1 ) % 3 )
				{
					$p1->turns_successfull ++;
					$p2->turns_unsuccessfull ++;

					$bg .= $this->processCard( $p1, $p2, 0 );
					$koef_otd = f_MValue("SELECT koef_value FROM koefs WHERE koef_id = 4");
					if( mt_rand( 1, 1000 ) <= $p2->attributes[15] * $koef_otd / $p2->player->level ) // расчет отдачи
					{
						$bg .= ",[10,'{$p2->player->login}']";
						$bg .= $this->processCard( $p2, $p1, 1, false );
					}
					$w1 = true; $w2 = false;
				}else
				if( $p2->card->genre == ( $p1->card->genre + 1 ) % 3 )
				{
					$p2->turns_successfull ++;
					$p1->turns_unsuccessfull ++;

					$bg .= $this->processCard( $p2, $p1, 1 );
					$koef_otd = f_MValue("SELECT koef_value FROM koefs WHERE koef_id = 4");
					if( mt_rand( 1, 1000 ) <= $p1->attributes[15] * $koef_otd / $p1->player->level )
					{
						$bg .= ",[10,'{$p1->player->login}']";
						$bg .= $this->processCard( $p1, $p2, 0, false );
					}
					$w2 = true; $w1 = false;
				}else
				if( $p2->card->genre == $p1->card->genre ) // свитки одинаковые, навернем магией
				{
					$p1->turns_draw ++;
					$p2->turns_draw ++;
					if ($p1->player->level > $p2->player->level) $max_pl_level = $p1->player->level;
					else $max_pl_level = $p2->player->level;
					$bg .= $this->neutralDamage( $p1, $p1->card->genre, $max_pl_level );
					$bg .= $this->neutralDamage( $p2, $p2->card->genre, $max_pl_level );

					$w1 = false; $w2 = false;
				}
			}

			$w1 = ( $w1 ? 1 : 0 );
			$w2 = ( $w2 ? 1 : 0 );

			f_MQuery( "INSERT INTO combat_animation( combat_id, player_id, scenario ) VALUES ( {$this->combat_id}, {$p1->player->player_id}, '$w1, $w2' )" );
			f_MQuery( "INSERT INTO combat_animation( combat_id, player_id, scenario ) VALUES ( {$this->combat_id}, {$p2->player->player_id}, '$w2, $w1' )" );

			$this->texts[$i] .= $bg;
		}

		for( $i = $n; $i < $m; ++ $i )
		{
			$bg = '';
			if( $i < count( $this->sides[0] ) )
			{
				$p1 = $this->cplayers[$this->sides[0][$i]];
				$p2 = $this->cplayers[$this->sides[1][mt_rand( 0, count( $this->sides[1] ) - 1 )]];
				$p1->opponent_id_2 = $p2->player->player_id;
				$bg .= ",[11,'{$p1->player->login}','{$p2->player->login}']";
				if( $p2->card === null || $p1->card->genre == ( $p2->card->genre + 1 ) % 3 || $p1->card->genre == 3 )
					$bg .= $this->processCard( $p1, $p2, 0 );
				else $bg .= ",[12,'{$p1->player->login}']";
			}
			else
			{
				$p1 = $this->cplayers[$this->sides[0][mt_rand( 0, count( $this->sides[0] ) - 1 )]];
				$p2 = $this->cplayers[$this->sides[1][$i]];
				$p2->opponent_id_2 = $p1->player->player_id;
				$bg .= ",[11,'{$p2->player->login}','{$p1->player->login}']";
				if( $p1->card === null || $p2->card->genre == ( $p1->card->genre + 1 ) % 3 || $p2->card->genre == 3 )
					$bg .= $this->processCard( $p2, $p1, 1 );
				else $bg .= ",[12,'{$p2->player->login}']";
			}
			$this->texts[$i] .= $bg;
		}
	}

	function AlterAttributes( )
	{
		$st = '';
		foreach( $this->cplayers as $plr )
		{
			$plr->attributes[130] += $this->players[$plr->player->player_id]->attributes[30];
			$plr->attributes[140] += $this->players[$plr->player->player_id]->attributes[40];
			$plr->attributes[150] += $this->players[$plr->player->player_id]->attributes[50];

			$plr->attributes[130] += $this->players[$plr->player->player_id]->attributes[33];
			$plr->attributes[140] += $this->players[$plr->player->player_id]->attributes[42];
			$plr->attributes[150] += $this->players[$plr->player->player_id]->attributes[51];

			$plr->attributes[222] -= (int)$plr->attributes[501];
			$regen = min( $plr->attributes[222], max( $plr->attributes[101] - $plr->attributes[1], 0 ) );
			$plr->attributes[1] += $regen;

			if( $regen > 0 ) $st = ",[14,'{$plr->player->login}',$regen]".$st;
			else if( $regen < 0 ) $st = $st = ",[13,'{$plr->player->login}',".(-$regen)."]".$st;
			if( $regen != 0 ) $plr->turn_arr .= ",[csdir2+'r.gif',$regen,\"\"]";

			if( $plr->attributes[1] > $plr->attributes[101] )
				$plr->attributes[1] = $plr->attributes[101];
			if( $plr->damage_block < 0 )
				$plr->damage_block = 0;

			if( strlen( $plr->row ) > 30 ) $plr->row = substr( $plr->row, 1 );
		}
		return $st;
	}

	function DispellAuras( )
	{
		$au = "";

		f_MQuery( "UPDATE combat_auras SET duration = duration - 1 WHERE combat_id={$this->combat_id}" );
		$res = f_MQuery( "SELECT * FROM combat_auras WHERE combat_id = {$this->combat_id} AND duration <= 0" );
		while( $arr = f_MFetch( $res ) )
		{
			$plr = $this->cplayers[$arr[player_id]];
			if( $plr )
			{
    			$aura = new Aura( $arr[aura_id] );

    			$au .= ",[15,'{$plr->player->login}','{$aura->name}']";
    			$au .= ",[0,\"".$aura->Dispell2( $plr, $combat_id )."\"]";
    			$au .= ",[9]";
			}
		}
		f_MQuery( "DELETE FROM combat_auras WHERE combat_id = {$this->combat_id} AND duration <= 0" );

		return $au;
	}

	function GetHeader( )
	{
		$st = ",[16]";
		$m = max( count( $this->sides[0] ), count( $this->sides[1] ) );
		for( $i = 0; $i < $m; ++ $i )
		{
			if( $i < count( $this->sides[0] ) ) $s1 = "'".$this->side_players[0][$i]->player->login."',".$this->side_players[0][$i]->attributes[1].','.$this->side_players[0][$i]->attributes[101];
			else $s1 = '-1';

			if( $i < count( $this->sides[1] ) ) $s2 = "'".$this->side_players[1][$i]->player->login."',".$this->side_players[1][$i]->attributes[1].','.$this->side_players[1][$i]->attributes[101];
			else $s2 = '-1';

			$st .= ",[17,$s1,$s2]";
		}
		$st .= ',[18]';
		return $st;
	}

	function StorePlayers( )
	{
		$mob_st = ''; // в эту строку складывается лут от мобов
		$st = '';
		f_MQuery( "DELETE FROM combat_turn_desc WHERE combat_id={$this->combat_id}" );
		foreach( $this->players as $id=>$player )
		{
			$cplayer = $this->cplayers[$id];
			$statistics = '';
			$statistics .= ', dmg_given_magic     = '.$cplayer->dmg_given_magic    ;
			$statistics .= ', dmg_given_spells    = '.$cplayer->dmg_given_spells   ;
			$statistics .= ', dmg_given_resist    = '.$cplayer->dmg_given_resist   ;
			$statistics .= ', dmg_given_creatures = '.$cplayer->dmg_given_creatures;
			$statistics .= ', dmg_magic           = '.$cplayer->dmg_magic          ;
			$statistics .= ', dmg_spells          = '.$cplayer->dmg_spells         ;
			$statistics .= ', dmg_creatures       = '.$cplayer->dmg_creatures;
			$statistics .= ', turns_successfull   = '.$cplayer->turns_successfull  ;
			$statistics .= ', turns_unsuccessfull = '.$cplayer->turns_unsuccessfull;
			$statistics .= ', turns_draw          = '.$cplayer->turns_draw         ;

			f_MQuery( "UPDATE combat_players SET row='{$cplayer->row}', opponent_id = {$cplayer->opponent_id}, opponent_id_2 = {$cplayer->opponent_id_2}, damage_block = {$cplayer->damage_block}, damage_coef = {$cplayer->damage_coef} {$statistics} WHERE player_id = $id" );

			$g = 3;
			if( $cplayer->card !== null ) $g = $cplayer->card->genre;
			if( strlen( $cplayer->turn_arr ) > 0 ) $cplayer->turn_arr = substr( $cplayer->turn_arr, 1 );
			$cplayer->turn_arr = $g.",[".$cplayer->turn_arr."]";
			f_MQuery( "INSERT INTO combat_turn_desc( combat_id, player_id, val ) VALUES ( {$this->combat_id},{$cplayer->player->player_id},'".addslashes($cplayer->turn_arr)."' )" );

			foreach( $cplayer->attributes as $attr_id=>$val )
			{
				if( !isset( $player->attributes[$attr_id] ) ) f_MQuery( "INSERT INTO player_attributes ( player_id, attribute_id, value, real_value, actual_value ) VALUES ( $id, $attr_id, $val, 0, 0 )" );
				else if( $val != $player->attributes[$attr_id] ) f_MQuery( "UPDATE player_attributes SET value = $val WHERE player_id = $id AND attribute_id = $attr_id" );
			}

			for( $i = 0; $i < 3; ++ $i )
			{
				if( $player->creatures[$i] !== null && $cplayer->creatures[$i] === null )
					f_MQuery( "DELETE FROM combat_creatures WHERE player_id = $id AND slot_id = $i" );
				else if( $player->creatures[$i] === null && $cplayer->creatures[$i] !== null )
					f_MQuery( "INSERT INTO combat_creatures( player_id, slot_id, creature_id, attack, defence, trample, haste, firststrike, genre, effect_dmg_p, effect_dmg_c, effect_die, effect_got_dmg, effect_always ) VALUES( $id, $i, {$cplayer->creatures[$i]->creature_id}, {$cplayer->creatures[$i]->attack}, {$cplayer->creatures[$i]->defence}, {$cplayer->creatures[$i]->trample}, {$cplayer->creatures[$i]->haste}, {$cplayer->creatures[$i]->firststrike}, {$cplayer->creatures[$i]->genre}, '".addslashes($cplayer->creatures[$i]->effect_dmg_p)."', '".addslashes($cplayer->creatures[$i]->effect_dmg_c)."', '".addslashes($cplayer->creatures[$i]->effect_die)."', '".addslashes($cplayer->creatures[$i]->effect_got_dmg)."', '".addslashes($cplayer->creatures[$i]->effect_always)."' )" );
				else if( $player->creatures[$i] !== null && $cplayer->creatures[$i] !== null )
				{
					if( $player->creatures[$i]->creature_id != $cplayer->creatures[$i]->creature_id ||
					    $player->creatures[$i]->haste       != $cplayer->creatures[$i]->haste ||
					    $player->creatures[$i]->trample     != $cplayer->creatures[$i]->trample ||
					    $player->creatures[$i]->firststrike != $cplayer->creatures[$i]->firststrike ||
					    $player->creatures[$i]->attack      != $cplayer->creatures[$i]->attack ||
					    $player->creatures[$i]->defence     != $cplayer->creatures[$i]->defence )
					    f_MQuery( "UPDATE combat_creatures SET creature_id = {$cplayer->creatures[$i]->creature_id}, attack = {$cplayer->creatures[$i]->attack}, defence = {$cplayer->creatures[$i]->defence}, trample = {$cplayer->creatures[$i]->trample}, haste = {$cplayer->creatures[$i]->haste}, firststrike = {$cplayer->creatures[$i]->firststrike} WHERE player_id = $id AND slot_id = $i" );
				}
			}

			

			if( $cplayer->attributes[1] <= 0 )
			{
				$st .= ",[19,'{$player->player->login}']";
				f_MQuery( "DELETE FROM combat_statistics WHERE player_id={$id}" );
				f_MQuery( "insert into combat_statistics select combat_id, player_id, dmg_spells, dmg_magic, dmg_given_magic, dmg_given_spells, dmg_given_resist, turns_successfull, turns_unsuccessfull, turns_draw, dmg_creatures, dmg_given_creatures, side from combat_players where player_id=$id" );
				f_MQuery( "UPDATE combat_players SET ready=2 WHERE player_id={$id}" );

				// move opponent to the bottom
				if( $cplayer->opponent_id )
				{
					f_MQuery( "LOCK TABLE combat_players WRITE" );
					$earr = f_MFetch( f_MQuery( "SELECT max( entry_id ) FROM combat_players" ) );
    				f_MQuery( "UPDATE combat_players SET entry_id = 1 + $earr[0] WHERE player_id={$cplayer->opponent_id}" );
    				f_MQuery( "UNLOCK TABLES" );
				}

				// probably we need to drop something
				$res = f_MQuery( "SELECT ai, mob_id, side FROM combat_players WHERE player_id={$id}" );
				$arr = f_Mfetch( $res );
				if( $arr && $arr['ai'] )
				{
					$prem = false;
					$pid = 0;
					$login = 0;
					if( $cplayer->opponent_id )
					{
						$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$cplayer->opponent_id} AND premium_id=5" ) );
						if( $barr[0] ) $prem = true;
						$pid = $cplayer->opponent_id;
						$login = $this->cplayers[$pid]->player->login;
					}

/*
					if ($cplayer->opponent_id==6825);
					{
						$c_p = count($this->side_players[1-$arr[2]]);
						$c_p_rnd = mt_rand(0, $c_p);
						$mob_st .= ",[19,'{$c_p_rnd}']";
						$i=0;
						foreach($this->side_players[1-$arr[2]] as $c_plr)
						{
							if ($i == $c_p_rnd)
							{
								$pid = $c_plr->player->player_id;
								$login = $c_plr->player->login;
								$mob_st .= ",[19,'{$c_plr->player->player_id}']";
							}
							$i++;
						}
					}
*/
					$chsans = 100;
					$c_lvl = 0;
					foreach( $this->sides[1-$arr[2]] as $c_id )
						if ($this->cplayers[$c_id]->player->level > $c_lvl) $c_lvl = $this->cplayers[$c_id]->player->level;
					if ($cplayer->player->level <= 5)
						if ($cplayer->player->level + 3 <= $c_lvl)
							$chsans = 10;
					$moo = ",[0,\"".mobDrop2( $arr['mob_id'], $this->location, $this->depth, $this->combat_id, $pid, $login, $prem, $chsans )."\"]";
					if( $moo != '' ) $mob_st = $mob_st . $moo;
				}
			}
		}

		return $mob_st . $st;
	}

	function StoreTexts( )
	{
		$n = min( count( $this->sides[0] ), count( $this->sides[1] ) );
		$m = max( count( $this->sides[0] ), count( $this->sides[1] ) );

		$st = '';
		for( $i = 0; $i < $n; ++ $i )
		{
			$this->texts[$i] = AddSlashes( $this->texts[$i] );
			f_MQuery( "INSERT INTO combat_ajax_data ( combat_id, data ) VALUES ( {$this->combat_id}, '{$this->texts[$i]}' )" );
			$note_id = mysql_insert_id( );
			f_MQuery( "UPDATE combat_players SET note_id = $note_id WHERE player_id = {$this->side_players[0][$i]->player->player_id} OR player_id = {$this->side_players[1][$i]->player->player_id}" );
			$st .= ",[20,$note_id,'{$this->side_players[0][$i]->player->login}','{$this->side_players[1][$i]->player->login}']";
		}

		for( $i = $n; $i < $m; ++ $i )
		{
			$this->texts[$i] = AddSlashes( $this->texts[$i] );
			f_MQuery( "INSERT INTO combat_ajax_data ( combat_id, data ) VALUES ( {$this->combat_id}, '{$this->texts[$i]}' )" );
			$note_id = mysql_insert_id( );

			$player = ( $i < count( $this->sides[0] ) ) ? $this->side_players[0][$i] : $this->side_players[1][$i];
			f_MQuery( "UPDATE combat_players SET note_id = $note_id WHERE player_id = {$player->player->player_id}" );
			$st .= ",[21,$note_id,'{$player->player->login}']";
		}

		return $st;
	}

	function CheckWinners( $side, $ignoreSideCheck = false)
	{
		$st = "";
		$enemy = 1 - $side;
		$combat_id = $this->combat_id;
		$koef_money = f_MValue("SELECT koef_value FROM koefs WHERE koef_id = 1");
		$cres = f_MQuery( "SELECT cur_turn, side{$side}_lvl, side{$side}_num, side{$enemy}_lvl, side{$enemy}_num FROM combats WHERE combat_id=$combat_id" );
		$carr = f_MFetch( $cres );
		$cur_turn = $carr[0];
		$res1 = f_MQuery( "SELECT player_id FROM combat_players WHERE combat_id={$this->combat_id} AND side=$enemy AND ready <> 2" );
		$res = f_MQuery( "SELECT combat_players.player_id, characters.login, characters.level, characters.wear_level, combat_players.since_turn, characters.loc, characters.depth FROM characters, combat_players WHERE combat_id={$this->combat_id} AND side=$side AND characters.player_id=combat_players.player_id AND ready <> 3" );
		if($ignoreSideCheck ||  mysql_num_rows( $res1 ) == 0 && mysql_num_rows( $res ) > 0 )
		{
			while( $arr = f_MFetch( $res ) )
			{
				$tm = time( );
				$st .= ",[22,'{$arr[1]}']";
				$utenka = getExp( $arr[2], $arr[3], $cur_turn - $arr[4], $carr[1], $carr[2], $carr[3], $carr[4] );
				$money = (int)($koef_money * mt_rand(1, $utenka));
				if( $arr[5] == 2 && $arr[6] == 1 )
					$utenka = ceil( $utenka * 1.3 );
				if( $arr[5] == 3 && $arr[6] == 5 )
					$utenka = ceil( $utenka * 3.1 );

				$punished = false;
				$arr2 = f_MFetch( f_MQuery( "SELECT fights, fights_reason FROM player_permissions WHERE player_id=$arr[0]" ) );
				if( $arr2 && $arr2[0] > time( ) )
				{
					$punished = true;
				}


				$barr = f_MFetch( f_MQuery( "SELECT level FROM clan_buildings WHERE clan_id=( SELECT clan_id FROM characters WHERE player_id=$arr[0] ) AND building_id=4" ) );
				if( $barr )
					$blvl = $barr[0];
				else $blvl = 0;
				$add_exp = '';
				$utka2 = $utenka;

				$exp_to_log = $utenka;

				if( $punished )
				{
					$message = "Поскольку вы наказаны запретом на бои, никакого опыта вам не положено.";
				}
				else
				{
					if( $blvl )
					{
						$bonus = ceil( $utenka * ( 0.02 ) * $blvl );
						$exp_to_log += $bonus;
						$add_exp .= ". Ваша академия приносит вам дополнительно <b>$bonus</b> ".my_word_str( $bonus, "единицу опыта", "единицы опыта", "единиц опыта" );
						f_MQuery( "UPDATE characters SET exp = exp + $bonus WHERE player_id = $arr[0]" );
					}

					$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id=$arr[0] AND premium_id=0" ) );
					if( $barr[0] )
					{
						$bonus = ceil( $utenka * 0.5 );
						$exp_to_log += $bonus;
						$add_exp .= ". Премиум-бои приносят вам дополнительно <b>$bonus</b> ".my_word_str( $bonus, "единицу опыта", "единицы опыта", "единиц опыта" );
						f_MQuery( "UPDATE characters SET exp = exp + $bonus WHERE player_id = $arr[0]" );
					}

                    if ($money > 0)
                    {
                        f_MQuery("UPDATE characters SET money = money + $money WHERE player_id = $arr[0]" );
                        $tm = time();
                        f_MQuery( "INSERT INTO player_log ( player_id, item_id, had, have, type, arg1, arg2, arg3, time ) VALUES ( $arr[0], 0, 0, $money, 10, 0, 0, 0, $tm )" );
                        $mmoney = ". Из праха поверженного соперника Вы с гордостью достаёте <b>$money</b> ".my_word_str( $money, "дублон", "дублона", "дублонов" );
                    }
                    else $mmoney = ". Вы осмотрели поле боя, но не нашли ни одной монетки";

					f_MQuery( "UPDATE characters SET exp = exp + $utenka WHERE player_id = $arr[0]" );
					$message = "Вы выигрываете в битве и получаете <b>$utka2</b> ".my_word_str( $utenka, "единицу опыта", "единицы опыта", "единиц опыта" )."$add_exp"."$mmoney";
					f_MQuery("INSERT INTO player_log (player_id, item_id, type, have, arg1, time) VALUES ($arr[0], -2, 999, $exp_to_log, $combat_id, ".time().")");

					/* Блок бонусов за Шамахан и победу над мобами */
					// Получаем список мобов-врагов
					$aiEn = f_MQuery( 'SELECT player_id FROM combat_players WHERE ai = 1 AND side = 1 - '.$side.' AND combat_id = '.$this->combat_id );
					while( $Mob = f_MFetch( $aiEn ) )
					{
						//$undefined->syst2( 'SELECT login FROM characters WHERE player_id = '.$Mob[player_id] );
						$shamName = f_MValue( 'SELECT login FROM characters WHERE player_id = '.$Mob[player_id] );

						if( strpos( $shamName, 'Снегов' ) !== false )
						{
							$snegWins=f_MValue("SELECT value FROM player_quest_values WHERE value_id=12201 AND player_id=".$arr[0]);
							$snegWins = ( !$snegWins ) ? 1 : $snegWins + 1;
							if ($snegWins==1)
								f_MQuery("INSERT INTO player_quest_values (player_id, value_id, value) VALUES (".$arr[0].", 12201, 1)");
							else
								f_MQuery("UPDATE player_quest_values SET value=".$snegWins." WHERE value_id=12201 AND player_id=".$arr[0]);
							if ($snegWins % 10 == 0)
							{
								$Player = new Player( $arr[0] );
								$Player->AddEffect( 6, 0, 'Новогоднее настроение', 'В благодарность от Городской Управы за спасение Нового Года. Так держать!', 'eff_snowflake.png', '13:1:15:1:16:1.', time( ) + 3 * 60 * 60 * 24 );
								$Player->syst2( 'За победу над ещё одним десятком снеговиков Городская Управа жалует тебе <b>Эффект "Новогоднее настроение"! Благодарим за спасение Нового Года!</b>' );
							}
							if ($snegWins % 100 == 0)
							{
								$Player = new Player( $arr[0] );
								$Player->AddEffect( 6, 0, 'Новогодний ажиотаж', 'В благодарность от Городской Управы за спасение Нового Года. Так держать!', 'eff_snowflake.png', '13:10:15:10:16:10.', time( ) + 3 * 60 * 60 * 24 );
								$Player->syst2( 'За победу над ещё одной сотней снеговиков Городская Управа жалует тебе <b>Эффект "Новогодний ажиотаж"! Благодарим за спасение Нового Года!</b>' );
							}
						}
						// Пробиваем всех мобов на соответствие юзернейму "Шамаханин"
						else if( strpos( $shamName, 'Шамахан' ) !== false )
						{
							$shamWins = f_MValue( 'SELECT wins FROM sham_wins WHERE player_id = '.$arr[0] );
							$shamWins = ( !$shamWins ) ? 1 : $shamWins + 1;

							if( $shamWins == 1 )
							{
								f_MQuery( 'INSERT INTO sham_wins( player_id, wins ) VALUES( '.$arr[0].', '.$shamWins.' )' );
							}
							else
							{
								f_MQuery( 'UPDATE sham_wins SET wins = '.$shamWins.' WHERE player_id = '.$arr[0] );
							}

							// За каждого 10-го убитого шамаханина даём эффект боевого духа
							if( $shamWins % 10 == 0 )
							{
								$Player = new Player( $arr[0] );
								$Player->AddEffect( 2, 0, 'Боевой Дух', 'В благодарность от Городской Управы за Защиту Теллы от шамаханских лазутчиков. Так держать!', 'morale.gif', '30:1:40:1:50:1.', time( ) + 60 * 60 * 24 );
								$Player->syst2( 'За победу над ещё одним десятком шамаханских лазутчиков Городская Управа жалует тебе <b>Эффект "Боевой Дух"! Благодарим за Защиту Теллы!</b>' );
							}
						}
						else // Просто моб
						{
							$mobId = f_MValue( 'SELECT pswrddmd5 FROM characters WHERE player_id = '.$Mob[player_id] );
							$thisMobWins = f_MValue( 'SELECT wins FROM mob_wins WHERE mob_id = '.$mobId.' AND player_id = '.$arr[0] );

							// Не даём бонусы за мобов вне Энциклопедии
							if( $mobId == -1 )
							{
								continue;
							}

							if( !$thisMobWins )
							{
								f_MQuery( 'INSERT INTO mob_wins( mob_id, player_id, wins ) VALUES( '.$mobId.', '.$arr[0].', 1 )' );
								$thisMobWins = 1;
							}
							else
							{
								f_MQuery( 'UPDATE mob_wins SET wins = wins + 1 WHERE mob_id = '.$mobId.' AND player_id = '.$arr[0] );
								$thisMobWins ++;
							}

							// Бонусятор за мобокиллы (мономобы, есличо)
							/*if( $thisMobWins % 500000 == 0 )
							{
								// +1 всех магий навсегда
								$Player->syst2( 'За победу над ещё одним десятком тысяч представителей этого вида мобов, ты полчаешь в награду Эффект!' );
								$Player->AddEffect( 10, 0, 'Сотня мобов', 'В награду за убийство ещё одного десятка тысяч мобов одного типа', 'smiley.png', $effect = "30:1:40:1:50:1.", -1 );
							}
							elseif( ( $thisMobWins - 300000 ) % 300000 == 0 )
							{
								// +1 отдача навсегда
								$Player->syst2( 'За победу над ещё одним десятком тысяч представителей этого вида мобов, ты полчаешь в награду Эффект!' );
								$Player->AddEffect( 9, 0, 'Сотня мобов', 'В награду за убийство ещё одного десятка тысяч мобов одного типа', 'smiley.png', $effect = "15:1.", -1 );
							}
							elseif( ( $thisMobWins - 200000 ) % 300000 == 0 )
							{
								// +1 удача навсегда
								$Player->syst2( 'За победу над ещё одним десятком тысяч представителей этого вида мобов, ты полчаешь в награду Эффект!' );
								$Player->AddEffect( 8, 0, 'Сотня мобов', 'В награду за убийство ещё одного десятка тысяч мобов одного типа', 'smiley.png', $effect = "13:1.", -1 );
								$effectName = '';
								$effect = '16:1.';
								$effectTime = -1;
							}*/
							if( $thisMobWins % 100000 == 0 )
							{
								// +1 крит навсегда
								$effectName = 'Легион';
								$effect = '30:1:40:1:50:1.';
								$effectTime = -1;

							// Если таки начисляем эффект
							if( $effect )
							{
								$mobName = f_MValue( 'SELECT login FROM characters WHERE player_id = '.$Mob[player_id] );
								$Player = new Player( $arr[0] );
								$Player->syst2( 'За победу над '.$thisMobWins.' представителей вида <b>'.$mobName.'</b>, ты получаешь в награду Эффект!' );
								$Player->AddEffect( 4, 0, $effectName, 'В награду за убийство '.$thisMobWins.' представителей вида <b>'.$mobName.'</b>', 'smiley.png', $effect, $effectTime );
							}
							}
							if( $thisMobWins % 10000 == 0 )
							{
								// + 100 хп навсегда
								$effectName = 'Тьма';
								$effect = '15:1:16:1:13:1.';
								$effectTime = -1;

							// Если таки начисляем эффект
							if( $effect )
							{
								$mobName = f_MValue( 'SELECT login FROM characters WHERE player_id = '.$Mob[player_id] );
								$Player = new Player( $arr[0] );
								$Player->syst2( 'За победу над '.$thisMobWins.' представителей вида <b>'.$mobName.'</b>, ты получаешь в награду Эффект!' );
								$Player->AddEffect( 4, 0, $effectName, 'В награду за убийство '.$thisMobWins.' представителей вида <b>'.$mobName.'</b>', 'smiley.png', $effect, $effectTime );
							}
							}
							if( $thisMobWins % 1000 == 0 )
							{
								// + 10 хп на месяц
								$effectName = 'Тыща';
								$effect = '101:10.';
								$effectTime = time( ) + 60 * 60 * 24 * 31;

							// Если таки начисляем эффект
							if( $effect )
							{
								$mobName = f_MValue( 'SELECT login FROM characters WHERE player_id = '.$Mob[player_id] );
								$Player = new Player( $arr[0] );
								$Player->syst2( 'За победу над '.$thisMobWins.' представителей вида <b>'.$mobName.'</b>, ты получаешь в награду Эффект!' );
								$Player->AddEffect( 4, 0, $effectName, 'В награду за убийство '.$thisMobWins.' представителей вида <b>'.$mobName.'</b>', 'smiley.png', $effect, $effectTime );
							}
							}
							if( $thisMobWins % 100 == 0 )
							{
								// +1 хп на неделю
								$effectName = 'Сотня';
								$effect = '101:10.';
								$effectTime = time( ) + 60 * 60 * 24 * 7;

							// Если таки начисляем эффект
							if( $effect )
							{
								$mobName = f_MValue( 'SELECT login FROM characters WHERE player_id = '.$Mob[player_id] );
								$Player = new Player( $arr[0] );
								$Player->syst2( 'За победу над '.$thisMobWins.' представителей вида <b>'.$mobName.'</b>, ты получаешь в награду Эффект!' );
								$Player->AddEffect( 4, 0, $effectName, 'В награду за убийство '.$thisMobWins.' представителей вида <b>'.$mobName.'</b>', 'smiley.png', $effect, $effectTime );
							}
							}
						}
					}
					/* !Блок бонусов за Шамахан */
				}
				$tm = time( );

				// ---------------------
                $sock = socket_create(AF_INET, SOCK_STREAM, 0);
                socket_connect($sock, "127.0.0.1", 1100);
                $tm = date( "H:i", time( ) );
                $msg = "say\n{$message}\n0\n{$arr[0]}\n0\n{$tm}\n";
                socket_write( $sock, $msg, strlen($msg) );
                socket_close( $sock );
                // ---------------------
    			f_MQuery( "DELETE FROM combat_statistics WHERE player_id={$arr[0]}" );
    			f_MQuery( "insert into combat_statistics select combat_id, player_id, dmg_spells, dmg_magic, dmg_given_magic, dmg_given_spells, dmg_given_resist, turns_successfull, turns_unsuccessfull, turns_draw, dmg_creatures, dmg_given_creatures, side from combat_players where player_id={$arr[0]}" );


			}

			// статистика
			$stats = "<table><tr><td>\" + rFLUl() + \"<table>";
			f_MQuery( "UPDATE combat_players SET ready=3 WHERE combat_id=$combat_id AND side=$side AND ready < 2" );

			$stats .= "<tr><td>\" + rFUcm() + \"<b>Сторона Победителя</b>\" + rFL() + \"</td><td>\" + rFUcm() + \"<b>Сторона Проигравших</b>\" + rFL() + \"</td></tr>";
			$stats .= "<tr><td valign=top>\" + rFUlt() + \"";
			$res = f_MQuery( "SELECT characters.login, characters.level, combat_statistics.* FROM combat_statistics, characters WHERE combat_id=$combat_id AND side=$side AND combat_statistics.player_id = characters.player_id" );
			while( $arr = f_MFetch( $res ) )
			{
				$stats .= "<center><b>$arr[0]</b></center><br>";
 				$stats .= "<table cellspacing=0 cellpadding=0>";
 				$stats .= "<tr><td>Нанес урона заклинаниями:&nbsp;</td><td>$arr[dmg_spells]</td></tr>";
 				$stats .= "<tr><td>Нанес урона магией:&nbsp;</td><td>$arr[dmg_magic]</td></tr>";
 				if( $arr[1] >= 4 ) $stats .= "<tr><td>Нанес урона существами:&nbsp;</td><td>$arr[dmg_creatures]</td></tr>";
 				$stats .= "<tr><td>Получил урона заклинаниями:&nbsp;</td><td>$arr[dmg_given_spells]</td></tr>";
 				$stats .= "<tr><td>Получил урона магией:&nbsp;</td><td>$arr[dmg_given_magic]</td></tr>";
 				$stats .= "<tr><td>Получил урона от стихии:&nbsp;</td><td>$arr[dmg_given_resist]</td></tr>";
 				if( $arr[1] >= 4 ) $stats .= "<tr><td>Получил урона существами:&nbsp;</td><td>$arr[dmg_given_creatures]</td></tr>";
 				$stats .= "<tr><td>Ходов попал:&nbsp;</td><td>$arr[turns_successfull]</td></tr>";
 				$stats .= "<tr><td>Ходов промахнулся:&nbsp;</td><td>$arr[turns_unsuccessfull]</td></tr>";
 				$stats .= "<tr><td>Ходов вничью:&nbsp;</td><td>$arr[turns_draw]</td></tr>";

 				$stats .= "</table>";
			}
			$stats .= "\" + rFL() + \"</td>";
			$stats .= "<td valign=top>\" + rFUlt() + \"";
			$res = f_MQuery( "SELECT characters.login, characters.level, combat_statistics.* FROM combat_statistics, characters WHERE combat_id=$combat_id AND side=$enemy AND combat_statistics.player_id = characters.player_id" );
			while( $arr = f_MFetch( $res ) )
			{
				$stats .= "<center><b>$arr[0]</b></center><br>";
 				$stats .= "<table cellspacing=0 cellpadding=0>";
 				$stats .= "<tr><td>Нанес урона заклинаниями:&nbsp;</td><td>$arr[dmg_spells]</td></tr>";
 				$stats .= "<tr><td>Нанес урона магией:&nbsp;</td><td>$arr[dmg_magic]</td></tr>";
 				if( $arr[1] >= 4 ) $stats .= "<tr><td>Нанес урона существами:&nbsp;</td><td>$arr[dmg_creatures]</td></tr>";
 				$stats .= "<tr><td>Получил урона заклинаниями:&nbsp;</td><td>$arr[dmg_given_spells]</td></tr>";
 				$stats .= "<tr><td>Получил урона магией:&nbsp;</td><td>$arr[dmg_given_magic]</td></tr>";
 				$stats .= "<tr><td>Получил урона от стихии:&nbsp;</td><td>$arr[dmg_given_resist]</td></tr>";
 				if( $arr[1] >= 4 ) $stats .= "<tr><td>Получил урона существами:&nbsp;</td><td>$arr[dmg_given_creatures]</td></tr>";
 				$stats .= "<tr><td>Ходов попал:&nbsp;</td><td>$arr[turns_successfull]</td></tr>";
 				$stats .= "<tr><td>Ходов промахнулся:&nbsp;</td><td>$arr[turns_unsuccessfull]</td></tr>";
 				$stats .= "<tr><td>Ходов вничью:&nbsp;</td><td>$arr[turns_draw]</td></tr>";

 				$stats .= "</table>";
			}
			$stats .= "\" + rFL() + \"</td></tr>";
			$stats .= "</table>\" + rFLL() + \"</td></tr></table>";
			$stats = ",[0,\"$stats\"]";

			f_MQuery( "INSERT INTO combat_ajax_data ( combat_id, data ) VALUES ( {$this->combat_id}, '$stats' )" );
			$note_id = mysql_insert_id( );
			$st = ",[23,$note_id]" . $st;

			//LogError( $stats );
			//$st = $stats.$st;
			// конец статистики

        }

		return $st;
	}

	function MakeTurn( )
	{
		// FIRST OF ALL check that combat is not over
		$rem_players = f_MValue( "SELECT count( player_id ) FROM combat_players WHERE combat_id={$this->combat_id} AND ready < 2" );
		if( !$rem_players ) return;

		$rem_sides = f_MValue( "SELECT count(distinct side) FROM combat_players where combat_id={$this->combat_id}" );
		if( $rem_sides == 1 )
		{
			$this->CheckWinners( 0 );
			$this->CheckWinners( 1 );
			return;
		}

		$this->texts = Array( );

		// камбала BEGIN
		if ($this->location == 3)
		{
			$__r1 = f_MValue("SELECT count(ai) FROM combat_players WHERE combat_id={$this->combat_id} and ai=1 and mob_id=62");
			if ($__r1 > 0)
			{
				$_r = f_MValue("SELECT count(player_id) FROM combat_players WHERE combat_id={$this->combat_id} AND side=0 AND ready<2");
				$_r1 = $_r - f_MValue("SELECT side1_num FROM combats WHERE combat_id={$this->combat_id}");
				if ($_r1 >0)
				{
					if (mt_rand(1, 5) > 1)
					{
						$mob1 = new Mob();
						$mob1->CreateMob(48, $this->location, $this->depth);
						$c_id = 56 + mt_rand(0, 2);

						f_MQuery("INSERT into combat_players(combat_id, player_id, side, ai, mob_id, bloody, card_id) values ({$this->combat_id}, {$mob1->player_id}, 1, 1, 48, 1, $c_id)");
						f_MQuery("UPDATE combats SET side1_lvl = side1_lvl + 14 where combat_id={$this->combat_id}");
						f_MQuery("UPDATE combats SET side1_num = side1_num + 1 where combat_id={$this->combat_id}");
						f_MQuery( "UPDATE characters SET regime = 100 WHERE player_id = {$mob1->player_id}" );
						f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( {$this->combat_id}, '<b>{$mob1->name}</b> вмешивается в бой<br>' )" );

					}
					$__r2 = f_MValue("SELECT side0_lvl-side1_lvl FROM combats WHERE combat_id={$this->combat_id}");
					if ($__r2 >= 16)
					{
						$mob = new Mob;
						$mob->CreateMob(62, $this->location, $this->depth);
						$c_id = mt_rand(0, 2);
						if ($c_id == 0) $c_id=436;
						if ($c_id == 1) $c_id=265;
						if ($c_id == 2) $c_id=217;
						f_MQuery("INSERT into combat_players(combat_id, player_id, side, ai, mob_id, bloody, card_id) values ({$this->combat_id}, {$mob->player_id}, 1, 1, 62, 1, $c_id)");
						f_MQuery("UPDATE combats SET side1_lvl = side1_lvl + 16 where combat_id={$this->combat_id}");
						f_MQuery("UPDATE combats SET side1_num = side1_num + 1 where combat_id={$this->combat_id}");
						f_MQuery( "UPDATE characters SET regime = 100 WHERE player_id = {$mob->player_id}" );
						f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( {$this->combat_id}, '<b>{$mob->name}</b> вмешивается в бой<br>' )" );

					}
				}
			}
		} // камбала END

		if ($this->location == 1)
		{
			$__r1 = f_MValue("SELECT count(ai) FROM combat_players WHERE combat_id={$this->combat_id} and ai=1 and mob_id=50");
			if ($__r1 > 0)
			{
				$_r = f_MValue("SELECT count(player_id) FROM combat_players WHERE combat_id={$this->combat_id} AND side=0 AND ready<2");
				$_r1 = $_r - f_MValue("SELECT side1_num FROM combats WHERE combat_id={$this->combat_id}");
				if ($_r1 >0)
				{
					if (mt_rand(1, 5) > 1)
					{
						$mob1 = new Mob();
						$mob1->CreateMob(53, $this->location, $this->depth);
						$c_id = 56 + mt_rand(0, 2);
						f_MQuery("INSERT into combat_players(combat_id, player_id, side, ai, mob_id, bloody, card_id) values ({$this->combat_id}, {$mob1->player_id}, 1, 1, 53, 1, $c_id)");
						f_MQuery("UPDATE combats SET side1_lvl = side1_lvl + 19 where combat_id={$this->combat_id}");
						f_MQuery("UPDATE combats SET side1_num = side1_num + 1 where combat_id={$this->combat_id}");
						f_MQuery( "UPDATE characters SET regime = 100 WHERE player_id = {$mob1->player_id}" );
						f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( {$this->combat_id}, '<b>{$mob1->name}</b> вмешивается в бой<br>' )" );

					}
					$__r2 = f_MValue("SELECT side0_lvl-side1_lvl FROM combats WHERE combat_id={$this->combat_id}");
					if ($__r2 >= 20)
					{
						$mob = new Mob;
						$mob->CreateMob(50, $this->location, $this->depth);
						$c_id = mt_rand(0, 2);
						if ($c_id == 0) $c_id=57;
						if ($c_id == 1) $c_id=56;
						if ($c_id == 2) $c_id=58;
						f_MQuery("INSERT into combat_players(combat_id, player_id, side, ai, mob_id, bloody, card_id) values ({$this->combat_id}, {$mob->player_id}, 1, 1, 50, 1, $c_id)");
						f_MQuery("UPDATE combats SET side1_lvl = side1_lvl + 20 where combat_id={$this->combat_id}");
						f_MQuery("UPDATE combats SET side1_num = side1_num + 1 where combat_id={$this->combat_id}");
						f_MQuery( "UPDATE characters SET regime = 100 WHERE player_id = {$mob->player_id}" );
						f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( {$this->combat_id}, '<b>{$mob->name}</b> вмешивается в бой<br>' )" );

					}
				}
			}
		}

		$this->CollectData( );

		$this->UpdateOpponents( );
		$this->ProcessCards( );
		$this->ProcessCreatures( );
		$au = $this->DispellAuras( );
		$top = $this->AlterAttributes( );

		foreach( $this->cplayers as $plr ) if( $plr->killed ) $plr->attributes[1] = 0;

		foreach( $this->cplayers as $plr ) if( $plr->forces >= 3 && ( $plr->damage_coef < 10 || $plr->damage_block > 0 ) )
		{
			$top = ",[24,'{$plr->player->login}']";
			$plr->damage_block = 0;
			$plr->damage_coef *= 10;
		}


		$st = '';
		$st .= $this->StorePlayers( );
		$st .= ",[25,".$this->turn.",'".date( "d.m.Y H:i:s" )."']";
		$st .= $this->GetHeader( );
		$st .= $au;
		$st .= $top;

		$st .= $this->StoreTexts( );
		$st .= ",[26]";

		$tp = ",[27]".$this->CheckWinners( 0 ).$this->CheckWinners( 1 ).",[18]";

		$st = AddSlashes( $tp.$st );

		f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( {$this->combat_id}, '$st' )" );

		f_MQuery( "UPDATE combat_players SET ready=0 WHERE combat_id={$this->combat_id} AND ready < 2" );
		f_MQuery( "UPDATE combat_players SET lcard=card_id WHERE combat_id={$this->combat_id}" );
		f_MQuery( "UPDATE combat_players SET card_id=-1 WHERE combat_id={$this->combat_id}" );

		$tm = time( );
		f_MQuery( "UPDATE combats SET last_turn_made = $tm, cur_turn = cur_turn + 1 WHERE combat_id = {$this->combat_id}" );


	}

	function DebugOutPlayer( $player )
	{
		echo "<u>".$player->player->login."</u><br>";
		echo $player->attributes[1].'/'.$player->attributes[101]."<br>";
		echo $player->attributes[130].'|'.$player->attributes[140].'|'.$player->attributes[150]."<br>";
		echo $player->opponent_id."<br>";
		for( $i = 0; $i < 3; ++ $i )
		{
			echo "$i. ";
			if( $player->creatures[$i] === null ) echo "Нет существа<br>";
			else echo $player->creatures[$i]->name."[{$player->creatures[$i]->attack}/{$player->creatures[$i]->defence}]<br>";
		}
	}

	function DebugOut( )
	{
		echo( "<b>SIDE 1</b><br>" );
		foreach( $this->sides[0] as $id )
		{
			$this->DebugOutPlayer( $this->players[$id] );
		}
		echo( "<br><b>SIDE 2</b><br>" );
		foreach( $this->sides[1] as $id )
		{
			$this->DebugOutPlayer( $this->players[$id] );
		}
		print( "<hr>" );
		echo( "<b>SIDE 1</b><br>" );
		foreach( $this->sides[0] as $id )
		{
			$this->DebugOutPlayer( $this->cplayers[$id] );
		}
		echo( "<br><b>SIDE 2</b><br>" );
		foreach( $this->sides[1] as $id )
		{
			$this->DebugOutPlayer( $this->cplayers[$id] );
		}
	}
};

/*$combat = new Combat( 1058 );
$combat->MakeTurn( );
$combat->DebugOut( );*/

?>
