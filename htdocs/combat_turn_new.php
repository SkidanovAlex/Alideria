<?

include_once( 'functions.php' );
include_once( 'player.php' );
include_once( 'card.php' );
include_once( 'aura.php' );
include_once( 'creature.php' );
include_once( 'mob.php' );

f_MConnect( );

class CombatPlayer
{
	var $card;
	var $creatures;
	var $attributes;
	var $opponent_id;
	
	var $target;
	var $damage_block;
	var $damage_coef;
	
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
		$clrs = Array( "blue", "green", "red" );
		$arr0 = Array( "Вода", "Природа", "Огонь" );
		$arr1 = Array( "Воды", "Природы", "Огня" );
		$arr2 = Array( "Начало Воды", "Начало Природы", "Огненное Начало" );
		$arr3 = Array( "началу Воды", "началу Природы", "огненному Началу" );
		$arr4 = Array( "маны Воды", "маны Природы", "Огненной маны" );
	
		$login1 = "<b>" . $login1 . "</b>";
		$login2 = "<b>" . $login2 . "</b>";

		$q = mt_rand( 1, 3 );
		if( $genre1 == $genre2 )
		{
			if( $q == 1 ) $st = "$login1 и $login2 обращаются к <b><font color={$clrs[$genre1]}>{$arr3[$genre1]}</font></b>. Стихия не способна справиться с заклинаниями.";
			if( $q == 2 ) $st = "$login1 и $login2 выбрали стихию <b><font color={$clrs[$genre1]}>{$arr1[$genre1]}</font></b>, стихия не справляется с заклинаниями.";
			if( $q == 3 ) $st = "$login1 и $login2 пытаются прикоснуться к источику <b><font color={$clrs[$genre1]}>{$arr4[$genre1]}</font></b>. Источник не может справиться с заклинаниями.";
		}
		else
		{
			if( $genre2 == ($genre1 + 1) % 3 )
			{
				$t = $genre1;
				$genre1 = $genre2;
				$genre2 = $t;
				$t = $login1;
				$login1 = $login2;
				$login2 = $t;
			}
		
			if( $q == 1 ) $st = "$login2 пытается прикоснуться к источнику {$arr4[$genre2]}, но терпит поражение от магии {$arr1[$genre1]} $login1.";
			if( $q == 2 ) $st = "В противоборстве {$arr1[$genre2]} $login2 и {$arr1[$genre1]} $login1 одерживает победу {$arr0[$genre1]}.";
			if( $q == 3 ) $st = "{$arr2[$genre2]} $login2 уступает {$arr3[$genre1]} $login1.";
		}
	
		$st .= "<br>";
	
		return $st;
	}

	function neutralDamage( $player, $genre )
	{
		$clrs = Array( "blue", "green", "red" );
		$arr1 = Array( "Воды", "Природы", "Огня" );

		if( $player->damage_block > 0 ) return;

		$cur = $player->player->level * 5;
		$cur *= ($player->damage_coef + 1e-7);
		settype( $cur, 'integer' );
		$cur -=  $player->attributes[132 + 10 * $genre];
		if( $cur < 0 ) $cur = 0;

		$player->attributes[1] -= $cur;
		$player->dmg_given_resist += $cur;
		return "<b>{$player->player->login}</b> получает $cur повреждения <b><font color={$clrs[$genre]}>магией {$arr1[$genre]}</font></b><br>";
	}

	function processCard( $p1, $p2, $side, $magic_dmg = true )
	{
		if( $p1->card === null )
			return "<b>{$p1->player->login}</b> не колдует заклинания<br>";
		else
		{
			$cast_str = "<b>{$p1->player->login}</b> колдует заклинание \" + ".$p1->card->Text( )." + \"";

			// расход заряда посоха
			$res = f_MQuery( "SELECT number FROM player_cards WHERE player_id={$p1->player->player_id} AND card_id={$p1->card->card_id}" );
			$arr = f_MFetch( $res );
			if( $arr && $arr[0] < 10 )
			{
				LogError( "Moo: player_id={$p1->player->player_id} AND card_id={$p1->card->card_id}" );
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
    			else return "<b>{$p1->player->login}</b> пытается использовать посох, но на посохе нет ни одного заряда";
			}
			// расход заряда - конец

			// Попроцессим свиток
			$str = $p1->card->Process2( $p1, $p2, $this->side_players[$side], $this->side_players[1 - $side], $this->combat_id, $this->turn, false );
			$p1->attributes[130 + $p1->card->genre * 10] -= $p1->card->cost;

			$bodrost = 20 * $p1->player->GetQuestValue( 500 );
			if( $p1->card->genre != 1 ) $bodrost = 0;
			if( $bodrost > 0 )
			{
				$str .= "<br><b>{$p1->player->login}</b> чувствует прилив бодрости и восстанавливает $bodrost здоровья!";
				$p1->attributes[1] += $bodrost;
			}

			$dmgst = '';
			if( $magic_dmg )
			{
                // Если удача - попроцессим еще разик
       			if( !$p1->card->unlucky && mt_rand( 1, 1000 ) <= $p1->attributes[13] * 50 / $p1->player->level )
       			{
       				$cast_str .= ". ".$cast_str." еще раз!!!";
       				$str .= "<br>".$p1->card->Process2( $p1, $p2, $this->side_players[$side], $this->side_players[1 - $side], $this->combat_id, $this->turn, true );
        			if( $bodrost > 0 )
        			{
        				$str .= "<br><b>{$p1->player->login}</b> чувствует прилив бодрости и восстанавливает $bodrost здоровья!";
        				$p1->attributes[1] += $bodrost;
        			}
       			}

                // Повреждение магией
                $krit = 1;
                if( mt_rand( 1, 1000 ) <= $p1->attributes[16] * 50 / $p1->player->level )
                	$krit = 2;
    			$dmg = $p1->attributes[131 + $p1->card->genre * 10] * $krit;

    			if( $p2->damage_block > 0 ) $dmg = 0;
    			else
    			{
    				$dmg *= ($p2->damage_coef + 1e-7);
    				settype( $dmg, 'integer' );
    				$dmg -= $p2->attributes[132 + $p1->card->genre * 10];
    		    }

    			if( $dmg > 0 )
    			{
    				$p2->attributes[1] -= $dmg;
    				$p2->dmg_given_magic += $dmg;
    				$p1->dmg_magic += $dmg;
    				$arr = Array( 'воды', 'природы', 'огня' );
    				$dmgst = "<b>{$p1->player->login}</b> наносит $dmg ".my_word_str( $dmg, 'единицу', "единицы", "единиц" ).' повреждения магией '.$arr[$p1->card->genre];
    				if( $krit == 2 ) $dmgst .= " критическим ударом!!!";
    			}
			}

			return $cast_str."<br>".$str."<br>$dmgst<br>";
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
			if( $this->players[$this->sides[1][$i]]->card ) $this->cplayers[$this->sides[0][$i]]->row .= $this->players[$this->sides[1][$i]]->card->genre;
			if( $this->players[$this->sides[0][$i]]->card ) $this->cplayers[$this->sides[1][$i]]->row .= $this->players[$this->sides[0][$i]]->card->genre;
		}
	}

	function processCreatureAttackingPlayer( &$creature, &$c1, &$c2, $dmg = -1 )
	{
		$bg = '';
		if( $c2->damage_block > 0 )
		{
			$bg .= "\" + ".$creature->Text( )."+ \" (<b>{$c1->player->login}</b>) атакует <b>{$c2->player->login}</b>, <b>{$c2->player->login}</b> не получает повреждения!<br>";
		}
		else
		{
			if( $dmg == -1 ) $dmg = $creature->attack;

    		$dmg *= ($c2->damage_coef + 1e-7);
    		settype( $dmg, 'integer' );
    		if( $dmg < 0 ) $dmg = 0;

			$c2->attributes[1] -= $dmg;
			$c2->dmg_given_creatures += $dmg;
			$c1->dmg_creatures += $dmg;
			$bg .= "\" + ".$creature->Text( )."+ \" (<b>{$c1->player->login}</b>) атакует <b>{$c2->player->login}</b>, <b>{$c2->player->login}</b> получает {$dmg} повреждения!<br>";
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
    					$bg .= "\" + ".$creature1->Text( )."+ \" (<b>{$c1->player->login}</b>) атакует \" + ".$creature2->Text( )."+ \" (<b>{$c2->player->login}</b>), \" + ".$creature2->Text( )."+ \" (<b>{$c2->player->login}</b>) получает {$creature1->attack} повреждения!<br>";
    				}
    				if( $cr2f )
    				{
    					$creature1->defence -= $creature2->attack;
    					$bg .= "\" + ".$creature2->Text( )."+ \" (<b>{$c2->player->login}</b>) атакует \" + ".$creature1->Text( )."+ \" (<b>{$c1->player->login}</b>), \" + ".$creature1->Text( )."+ \" (<b>{$c1->player->login}</b>) получает {$creature2->attack} повреждения!<br>";
    				}

    				$rem_od2 = $creature1->defence; //запомним сейчас, чтобы все было честно
    				if( !$cr1f && $creature1->defence > 0 )
    				{
    					$creature2->defence -= $creature1->attack;
    					$bg .= "\" + ".$creature1->Text( )."+ \" (<b>{$c1->player->login}</b>) атакует \" + ".$creature2->Text( )."+ \" (<b>{$c2->player->login}</b>), \" + ".$creature2->Text( )."+ \" (<b>{$c2->player->login}</b>) получает {$creature1->attack} повреждения!<br>";
					}
					
    				if( !$cr2f && $rem_od2 > 0 )
    				{
    					$creature1->defence -= $creature2->attack;
    					$bg .= "\" + ".$creature2->Text( )."+ \" (<b>{$c2->player->login}</b>) атакует \" + ".$creature1->Text( )."+ \" (<b>{$c1->player->login}</b>), \" + ".$creature1->Text( )."+ \" (<b>{$c1->player->login}</b>) получает {$creature2->attack} повреждения!<br>";
    				}

    				$c1tr = false; $c2tr = false;
    				if( $creature1->trample ) $c1tr = true;
					if( $creature2->trample ) $c2tr = true;

					if( $creature2->defence <= 0 )
					{
						if( $c1tr && $creature2->defence < 0 ) $bg .= $this->processCreatureAttackingPlayer( $creature1, $c1, $c2, -$creature2->defence );
						$dd .= " \" + ".$creature2->Text( )."+ \" (<b>{$c2->player->login}</b>) умирает.<br>";
						$c2->creatures[$j] = null;
					}
					if( $creature1->defence <= 0 )
					{
						if( $c2tr && $creature1->defence < 0 ) $bg .= $this->processCreatureAttackingPlayer( $creature2, $c2, $c1, -$creature1->defence );
						$dd .= "\" + ".$creature1->Text( )."+ \" (<b>{$c1->player->login}</b>) умирает.<br>";
						$c1->creatures[$j] = null;
					}
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
			}
			else // оба чувака поставили свиток, определим кто круче
			{
				$bg .= $this->flavorText( $p1->player->login, $p2->player->login, $p1->card->genre, $p2->card->genre );
				if( $p1->card->genre == ( $p2->card->genre + 1 ) % 3 ) 
				{	
					$p1->turns_successfull ++;
					$p2->turns_unsuccessfull ++;

					$bg .= $this->processCard( $p1, $p2, 0 );
					if( mt_rand( 1, 1000 ) <= $p2->attributes[15] * 50 / $p2->player->level )
					{
						$bg .= "<b>{$p2->player->login}</b> успевает произнести заклинание!!!<br>";
						$bg .= $this->processCard( $p2, $p1, 1, false );
					}
				}
				if( $p2->card->genre == ( $p1->card->genre + 1 ) % 3 ) 
				{
					$p2->turns_successfull ++;
					$p1->turns_unsuccessfull ++;

					$bg .= $this->processCard( $p2, $p1, 1 );
					if( mt_rand( 1, 1000 ) <= $p1->attributes[15] * 10 )
					{
						$bg .= "<b>{$p1->player->login}</b> успевает произнести заклинание!!!<br>";
						$bg .= $this->processCard( $p1, $p2, 0, false );
					}
				}
				if( $p2->card->genre == $p1->card->genre ) // свитки одинаковые, навернем магией
				{
					$p1->turns_draw ++;
					$p2->turns_draw ++;

					$bg .= $this->neutralDamage( $p1, $p1->card->genre );
					$bg .= $this->neutralDamage( $p2, $p2->card->genre );
				}
			}
			
			$this->texts[$i] .= $bg;
		}
		
		for( $i = $n; $i < $m; ++ $i )
		{
			$bg = '';
			if( $i < count( $this->sides[0] ) )
			{
				$p1 = $this->cplayers[$this->sides[0][$i]];
				$p2 = $this->cplayers[$this->sides[1][mt_rand( 0, count( $this->sides[1] ) - 1 )]];
				$bg .= "<b>{$p1->player->login}</b> не находит противника и нападает на <b>{$p2->player->login}</b><br>";
				if( $p2->card === null || $p1->card->genre == ( $p2->card->genre + 1 ) % 3 ) 
					$bg .= $this->processCard( $p1, $p2, 0 );
				else $bg .= "<b>{$p1->player->login}</b> не может прочитать заклинание<br>";
			}
			else
			{
				$p1 = $this->cplayers[$this->sides[0][mt_rand( 0, count( $this->sides[0] ) - 1 )]];
				$p2 = $this->cplayers[$this->sides[1][$i]];
				$bg .= "<b>{$p2->player->login}</b> не находит противника и нападает на <b>{$p1->player->login}</b><br>";
				if( $p1->card === null || $p2->card->genre == ( $p1->card->genre + 1 ) % 3 ) 
					$bg .= $this->processCard( $p2, $p1, 1 );
				else $bg .= "<b>{$p2->player->login}</b> не может прочитать заклинание<br>";
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
			
			$plr->attributes[222] -= $plr->player->GetQuestValue( 501 );
			$regen = min( $plr->attributes[222], max( $plr->attributes[101] - $plr->attributes[1], 0 ) );
			$plr->attributes[1] += $regen;
			
			if( $regen > 0 ) $st = '<b>'.$plr->player->login.'</b> восстанавливает <font color=blue><b>'.$regen.'</b></font> жизней.<br>'.$st;
			else if( $regen < 0 ) $st = '<b>'.$plr->player->login.'</b> теряет <font color=red><b>'.(-$regen).'</b></font> жизней.<br>'.$st;
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
    			
    			$au .= "<b>{$plr->player->login}</b> больше не чувствует ауры <b>{$aura->name}</b><br>";
    			$au .= $aura->Dispell2( $plr, $combat_id );
    			$au .= "<br>";
			}
		}
		f_MQuery( "DELETE FROM combat_auras WHERE combat_id = {$this->combat_id} AND duration <= 0" );
		
		return $au;
	}
	
	function GetHeader( )
	{
		$st = "<font color=saddlebrown >";
		$m = max( count( $this->sides[0] ), count( $this->sides[1] ) );
		for( $i = 0; $i < $m; ++ $i )
		{
			if( $i < count( $this->sides[0] ) ) $s1 = '<b>'.$this->side_players[0][$i]->player->login.'</b>&nbsp;['.$this->side_players[0][$i]->attributes[1].'/'.$this->side_players[0][$i]->attributes[101].']';
			else $s1 = '<i>Никто</i>';
			
			if( $i < count( $this->sides[1] ) ) $s2 = '<b>'.$this->side_players[1][$i]->player->login.'</b>&nbsp;['.$this->side_players[1][$i]->attributes[1].'/'.$this->side_players[1][$i]->attributes[101].']';
			else $s2 = '<i>Никто</i>';
			
			$st .= "$s1 vs $s2<br>";
		}
		$st .= '</font>';
		return $st;
	}
	
	function StorePlayers( )
	{
		$mob_st = ''; // в эту строку складывается лут от мобов
		$st = '';
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

			f_MQuery( "UPDATE combat_players SET row='{$cplayer->row}', opponent_id = {$cplayer->opponent_id}, damage_block = {$cplayer->damage_block}, damage_coef = {$cplayer->damage_coef} {$statistics} WHERE player_id = $id" );
				
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
					f_MQuery( "INSERT INTO combat_creatures( player_id, slot_id, creature_id, attack, defence, trample, haste, firststrike ) VALUES( $id, $i, {$cplayer->creatures[$i]->creature_id}, {$cplayer->creatures[$i]->attack}, {$cplayer->creatures[$i]->defence}, {$cplayer->creatures[$i]->trample}, {$cplayer->creatures[$i]->haste}, {$cplayer->creatures[$i]->firststrike} )" );
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
				$st .= "<font color=red><b>".$player->player->login."</b> проигрывает битву</font><br>";
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
				$res = f_MQuery( "SELECT ai, mob_id FROM combat_players WHERE player_id={$id}" );
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
					$moo = mobDrop2( $arr['mob_id'], $this->location, $this->depth, $this->combat_id, $pid, $login, $prem );
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
			$st .= "<img id=pair_img$note_id onClick='upload_pair( $note_id );' width=11 height=11 src=images/e_plus.gif>&nbsp;<u><font color=steelblue><b>{$this->side_players[0][$i]->player->login}</b> против <b>{$this->side_players[1][$i]->player->login}</b></font></u><br><div id=pair_div$note_id style='display: none'><i>Подождите, идет загрузка</i></div>";
		}
		
		for( $i = $n; $i < $m; ++ $i )
		{
			$this->texts[$i] = AddSlashes( $this->texts[$i] );
			f_MQuery( "INSERT INTO combat_ajax_data ( combat_id, data ) VALUES ( {$this->combat_id}, '{$this->texts[$i]}' )" );
			$note_id = mysql_insert_id( );
			
			$player = ( $i < count( $this->sides[0] ) ) ? $this->side_players[0][$i] : $this->side_players[1][$i];
			f_MQuery( "UPDATE combat_players SET note_id = $note_id WHERE player_id = {$player->player->player_id}" );
			$st .= "<img id=pair_img$note_id onClick='upload_pair( $note_id );' width=11 height=11 src=images/e_plus.gif>&nbsp;<u><font color=steelblue><b>{$player->player->login}</b> без оппонента</font></u><br><div id=pair_div$note_id style='display: none'><i>Подождите, идет загрузка</i></div>";
		}
		
		return $st;
	}
	
	function CheckWinners( $side )
	{
		$st = "";
		$enemy = 1 - $side;
		$combat_id = $this->combat_id;
		$cres = f_MQuery( "SELECT cur_turn, side{$side}_lvl, side{$side}_num, side{$enemy}_lvl, side{$enemy}_num FROM combats WHERE combat_id=$combat_id" );
		$carr = f_MFetch( $cres );
		$cur_turn = $carr[0]; 
		$res = f_MQuery( "SELECT player_id FROM combat_players WHERE combat_id={$this->combat_id} AND side=$enemy AND ready <> 2" );
		if( mysql_num_rows( $res ) == 0 )
		{
			include( "expirience.php" );
			$res = f_MQuery( "SELECT combat_players.player_id, characters.login, characters.level, characters.wear_level, combat_players.since_turn, characters.loc, characters.depth FROM characters, combat_players WHERE combat_id={$this->combat_id} AND side=$side AND characters.player_id=combat_players.player_id" );
			while( $arr = f_MFetch( $res ) )
			{
				$tm = time( );
				$st .= "<b>$arr[1]</b> выигрывает битву!<br>";
				$utenka = getExp( $arr[2], $arr[3], $cur_turn - $arr[4], $carr[1], $carr[2], $carr[3], $carr[4] );
				if( $arr[5] == 2 && $arr[6] == 1 )
					$utenka = ceil( $utenka * 1.3 );
				if( $arr[5] == 3 && $arr[6] == 5 )
					$utenka = ceil( $utenka * 3.1 );

				$barr = f_MFetch( f_MQuery( "SELECT level FROM clan_buildings WHERE clan_id=( SELECT clan_id FROM characters WHERE player_id=$arr[0] ) AND building_id=4" ) );
				if( $barr )
					$blvl = $barr[0];
				else $blvl = 0;
				$add_exp = '';

				if( $blvl )
				{
					$bonus = ceil( $utenka * ( 0.02 ) * $blvl );
					$add_exp .= ". Ваша академия приносит вам дополнительно <b>$bonus</b> ".my_word_str( $bonus, "единицу опыта", "единицы опыта", "единиц опыта" );
					f_MQuery( "UPDATE characters SET exp = exp + $bonus WHERE player_id = $arr[0]" );
				}

				$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id=$arr[0] AND premium_id=0" ) );
				if( $barr[0] )
				{
					$bonus = ceil( $utenka * 0.5 );
					$add_exp .= ". Премиум-аккаунт приносит вам дополнительно <b>$bonus</b> ".my_word_str( $bonus, "единицу опыта", "единицы опыта", "единиц опыта" );
					f_MQuery( "UPDATE characters SET exp = exp + $bonus WHERE player_id = $arr[0]" );
				}

				$money = mt_rand( 1, $utenka );
				f_MQuery( "UPDATE characters SET exp = exp + $utenka, money = money + $money WHERE player_id = $arr[0]" );
				$tm = time( );
				if( $money > 0 ) f_MQuery( "INSERT INTO player_log ( player_id, item_id, had, have, type, arg1, arg2, arg3, time ) VALUES ( $arr[0], 0, 0, $money, 10, 0, 0, 0, $tm )" );

				if( $money > 0 ) $message = "Вы выигрываете в битве и получаете <b>$utenka</b> ".my_word_str( $utenka, "единицу опыта", "единицы опыта", "единиц опыта" )." и <b>$money</b> ".my_word_str( $money, "дублон", "дублона", "дублонов" )."$add_exp";
				else $message = "Вы выигрываете в битве и получаете <b>$utenka</b> ".my_word_str( $utenka, "единицу опыта", "единицы опыта", "единиц опыта" )."$add_exp";

				// ---------------------
                $sock = socket_create(AF_INET, SOCK_STREAM, 0);
                socket_connect($sock, "127.0.0.1", 82);
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
 				$stats .= "<tr><td>Ходов в ничью:&nbsp;</td><td>$arr[turns_draw]</td></tr>";

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
 				$stats .= "<tr><td>Ходов в ничью:&nbsp;</td><td>$arr[turns_draw]</td></tr>";

 				$stats .= "</table>";
			}
			$stats .= "\" + rFL() + \"</td></tr>";
			$stats .= "</table>\" + rFLL() + \"</td></tr></table>";

			f_MQuery( "INSERT INTO combat_ajax_data ( combat_id, data ) VALUES ( {$this->combat_id}, '$stats' )" );
			$note_id = mysql_insert_id( );
			$st = "<img id=pair_img$note_id onClick='upload_pair( $note_id );' width=11 height=11 src=images/e_plus.gif>&nbsp;<u><font color=steelblue><b>Статистика боя</b></font></u><br><div id=pair_div$note_id style='display: none'><i>Подождите, идет загрузка</i></div>" . $st;

			//LogError( $stats );
			//$st = $stats.$st;
			// конец статистики

        }
		
		return $st;
	}
	
	function MakeTurn( )
	{
		$this->texts = Array( );
		
		$this->CollectData( );

		$this->UpdateOpponents( );
		$this->ProcessCards( );
		$this->ProcessCreatures( );
		$au = $this->DispellAuras( );
		$top = $this->AlterAttributes( );

		foreach( $this->cplayers as $plr ) if( $plr->killed ) $plr->attributes[1] = 0;

		foreach( $this->cplayers as $plr ) if( $plr->forces >= 3 && ( $plr->damage_coef < 10 || $plr->damage_block > 0 ) )
		{
			$top = "<b>{$plr->player->login}</b> пропустил три хода и начинает получать x10 урон<br>";
			$plr->damage_block = 0;
			$plr->damage_coef *= 10;
		}

	
		$st = '';
		$st .= $this->StorePlayers( );
		$st .= "<b>".$this->turn.". <font color=khaki>".date( "d.m.Y H:i:s" )."</b></font><br>";
		$st .= $this->GetHeader( );
		$st .= $au;
		$st .= $top;
		
		$st .= $this->StoreTexts( );
		$st .= "<table cellspacing=0 cellpadding=0 border=0 style='width:100%;height:20px;' width=100%><tr style='height:5px'><td><img width=1 height=5 src=empty.gif border=0></td></tr><tr style='height:10px'><td background='images/misc/divider.gif'><img width=1 height=10 src=empty.gif border=0></td></tr><tr style='height:5px'><td><img width=1 height=5 src=empty.gif border=0></td></tr></table>";

		$tp = "<font color=blue>".$this->CheckWinners( 0 ).$this->CheckWinners( 1 )."</font>";

		$st = AddSlashes( $tp.$st );
	
		f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( {$this->combat_id}, '$st' )" );
		
		f_MQuery( "UPDATE combat_players SET ready=0 WHERE combat_id={$this->combat_id} AND ready < 2" );
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
