<?

class sdk_t
{
	var $me;
	var $he;
	var $we;
	var $they;
	
	var $myself;
	var $opponent;
	var $friends;
	var $enemies;

	var $current_creature;
	var $all_creatures;
	var $creature_slot_1;
	var $creature_slot_2;
	var $creature_slot_3;

	var $creature_attack;
	var $creature_defence;

	var $log_msg;
	var $turn;
	
	var $slot;
	var $combat_id;
	var $dcast;

	var $genre;

	var $dmg;
	
	function sdk_t( $f_me, $f_he, $f_we, $f_they, &$f_turn, $f_combat_id, $genre = 0, $f_dcast = false )
	{
		$this->me                        = $f_me;
		$this->he                        = $f_he;
		$this->we                        = $f_we;
		$this->they                      = $f_they;
		$this->slot                      = $f_me->target;

		$this->myself                    = 101;
		$this->opponent                  = 102;
		$this->friends                   = 103;
		$this->enemies                   = 104;
		$this->current_creature          = -1;
		$this->all_creatures             = -2;
		$this->creature_slot_1           = 0;
		$this->creature_slot_2           = 1;
		$this->creature_slot_3           = 2;
		$this->creature_attack           = 1001;
		$this->creature_defence          = 1002;
		$this->log_msg                   = "";
		
		$this->turn = &$f_turn;
		$this->combat_id = $f_combat_id;
		$this->dcast = $f_dcast;
		$this->genre = $genre;
	}

	function get_players( $whom )
	{
		$res = Array( );
		
		if( $whom == $this->myself ) $res[] = $this->me;
		else if( $whom == $this->opponent ) $res[] = $this->he;
		else if( $whom == $this->friends ) $res = $this->we;
		else if( $whom == $this->enemies ) $res = $this->they;
		
		return $res;
	}

	function get_actual_player( $whom )
	{
		global $combat;
		
		if( $whom == $this->myself ) return $combat->cplayers[$this->me->player->player_id];
		else if( $whom == $this->opponent ) return $combat->cplayers[$this->he->player->player_id];
		
		RaiseError( "Ќеверный аргумент в combat_sdk2->get_actual_player: whom=$whom" );
	}

	function clear_log_msg( )
	{
		$this->log_msg = "";
	}

	function combat_log_msg( $a )
	{
		$this->log_msg .= $a;
	}


	// -------------------------------------------
	// - combat info functions
	// -------------------------------------------
	function get_turn_number( )
	{
		return $this->turn;
	}



	// -------------------------------------------
	// - character functions
	// -------------------------------------------
	function kill( $whom )
	{
		$players = $this->get_players( $whom );
		foreach( $players as $player )
		{
			$player->killed = true;
			$this->combat_log_msg( " <b>{$player->player->login}</b> умирает;" );
		}
	}

	function damage( $whom, $value )
	{
		global $global_dmg;
		$players = $this->get_players( $whom );
		if( $value < 0 ) $value = 0;

		foreach( $players as $player )
		{
			$cur = combat_calcDamage( false, $player, $value, $this->genre );
			$player->attributes[1] -= $cur;
			$player->dmg_given_spells += $cur;
			$this->dmg += $cur;
			if( $this->opponent == $whom ) $global_dmg += $cur;
			$this->combat_log_msg( " <b>{$player->player->login}</b> получает $cur повреждени€;" );
		}
	}

	function heal( $whom, $value )
	{
		$players = $this->get_players( $whom );
		if( $value < 0 ) $value = 0;

		foreach( $players as $player )
		{
			$player->attributes[1] += $value;
			$this->combat_log_msg( " <b>{$player->player->login}</b> восстанавливает $value здоровь€;" );
		}
	}

	function alter_attrib( $whom, $attribute_id, $value )
	{
		$players = $this->get_players( $whom );
		
		foreach( $players as $player )
		{
			$anames = $player->player->getAllAttrNames( );
			$aclrs = $player->player->gclrs;
			
			$attribute_name = "<b><font color={$aclrs[$attribute_id]}>{$anames[$attribute_id]}</font></b>";

			$player->attributes[$attribute_id] += $value;
			if( $value > 0 )
				$this->combat_log_msg( " $attribute_name у <b>{$player->player->login}</b> увеличиваетс€ на $value;" );
			else
				$this->combat_log_msg( " $attribute_name у <b>{$player->player->login}</b> уменьшаетс€ на ".(-$value).";" );
		}
	}

	function set_attrib( $whom, $attribute_id, $value )
	{
		$players = $this->get_players( $whom );

		foreach( $players as $player )
		{
			$anames = $player->player->getAllAttrNames( );
			$aclrs = $player->player->gclrs;
			
			$attribute_name = "<b><font color={$aclrs[$attribute_id]}>{$anames[$attribute_id]}</font></b>";

			$player->attributes[$attribute_id] = $value;

			$this->combat_log_msg( " $attribute_name у <b>{$player->player->login}</b> становитс€ $value;" );
		}
	}
	
	function aura( $whom, $aura_id, $duration )
	{
		$players = $this->get_players( $whom );

		foreach( $players as $player )
		{
			include_once( 'aura.php' );
			$aura = new Aura( $aura_id );
			$player->auras[] = $aura;
			$this->combat_log_msg( " Ќа <b>{$player->player->login}</b> накладываетс€ аура <b>{$aura->name}</b>." );
			$this->combat_log_msg( $aura->Enchant2( $player, $this->combat_id, $duration ) );
			if( $aura_id == 16 ) $player->nature_shield = true;
			if( $aura_id == 6 ) $player->poison_dream = true;
		}
	}

	function set_damage_blocking( $whom, $value )
	{
		$players = $this->get_players( $whom );

		foreach( $players as $player )
		{
			if( $player->damage_block == 1 && $value == -1 ) $this->combat_log_msg( " <b>{$player->player->login}</b> вновь начинает получать повреждени€." );
			if( $player->damage_block == 0 && $value == 1 ) $this->combat_log_msg( " <b>{$player->player->login}</b> прекращает получать повреждени€." );
			$player->damage_block += $value;
		}
	}

	function set_damage_coef( $whom, $value )
	{
		$players = $this->get_players( $whom );

		if( $value > 0 ) -- $value;
		if( $value < 0 ) ++ $value;

		foreach( $players as $player )
		{
			$player->damage_coef += $value;
			$this->combat_log_msg( " <b>{$player->player->login}</b> теперь получает <b>x{$player->damage_coef}</b> повреждени€." );
		}
	}


	// -------------------------------------------
	// - character info functions
	// -------------------------------------------
	function get_attrib_value( $whom, $attribute_id )
	{
		$player = $this->get_actual_player( $whom );
		
		if( $attribute_id == 0 ) return $player->player->level;
		return $player->attributes[$attribute_id];
	}




	// -------------------------------------------
	// - creatures functions
	// -------------------------------------------
	function summon( $creature_id, $where = -1 )
	{
		if( $where != -2 )
		{
			$slot = $this->slot;
			
			if( $where >= 0 && $where <= 2 )
				$slot = $where;
			
			$creature = new Creature( $creature_id );
			$creature->Summon2( $this->me, $slot );
			$this->combat_log_msg( " \" + ".$creature->Text( )." + \" входит в бой;" );
		}
		else for( $slot = 0; $slot < 3; ++ $slot )
		{
			$creature = new Creature( $creature_id );
			$creature->Summon2( $this->me, $slot );
			$this->combat_log_msg( " \" + ".$creature->Text( )." + \" входит в бой;" );
		}
	}

	function kill_creature( $whom, $where )
	{
		if( $where == -2 )
		{
			$this->kill_creature( $whom, 0 );
			$this->kill_creature( $whom, 1 );
			$this->kill_creature( $whom, 2 );
			return;
		}
			
		if( $whom == $this->myself ) $player = $this->me;
		else if( $whom == $this->opponent ) $player = $this->he;
		else return;
		
		$slot = $this->slot;
		
		if( $where >= 0 && $where <= 2 )
			$slot = $where;

		if( $player->creatures[$slot] != null )
		{
			$this->combat_log_msg( " \" + ".$player->creatures[$slot]->Text( )." + \" умирает;" );
			$player->creatures[$slot] = null;
		}
	}
	
	function alter_creature_attrib( $whom, $where, $attrib, $value )
	{
		if( $where == -2 )
		{
			$this->alter_creature_attrib( $whom, 0, $attrib, $value );
			$this->alter_creature_attrib( $whom, 1, $attrib, $value );
			$this->alter_creature_attrib( $whom, 2, $attrib, $value );
			return;
		}
			
		$slot = $this->slot;
		
		if( $where >= 0 && $where <= 2 )
			$slot = $where;

		if( $whom == $this->myself ) $player = $this->me;
		else if( $whom == $this->opponent ) $player = $this->he;
		else return;
		
		$creature = $player->creatures[$slot];
		if( $creature === null ) return;

		if( $attrib == $this->creature_defence )
		{
			$attrib_name = "«ащита";
			$creature->defence += $value;
		}
		else if( $attrib == $this->creature_attack )
		{
			$attrib_name = "јтака";
			$creature->attack += $value;
		}
		else return;
		
		if( $value > 0 )
			$this->combat_log_msg( " $attrib_name (\" + ".$creature->Text( )." + \") увеличиваетс€ на $value;" );
		else
		{
			$nvalue = - $value;
			$this->combat_log_msg( " $attrib_name (\" + ".$creature->Text( )." + \") уменьшаетс€ на $nvalue;" );
		}
		
		if( $creature->defence < 0 )
		{
			$this->combat_log_msg( " \" + ".$creature->Text( )." + \" умирает;" );
			$player->creatures[$slot] = null;
		}
	}

	function set_creature_attrib( $whom, $where, $attrib, $value )
	{
		if( $where == -2 )
		{
			$this->alter_creature_attrib( $whom, 0, $attrib, $value );
			$this->alter_creature_attrib( $whom, 1, $attrib, $value );
			$this->alter_creature_attrib( $whom, 2, $attrib, $value );
			return;
		}
			
		$slot = $this->slot;
		
		if( $where >= 0 && $where <= 2 )
			$slot = $where;

		if( $whom == $this->myself ) $player = $this->me;
		else if( $whom == $this->opponent ) $player = $this->he;
		else return;
		
		$creature = $player->creatures[$slot];
		if( $creature === null ) return;

		if( $attrib == $this->creature_defence )
		{
			$attrib_name = "«ащита";
			$creature->defence = $value;
		}
		else if( $attrib == $this->creature_attack )
		{
			$attrib_name = "јтака";
			$creature->attack = $value;
		}
		else return;
		
		if( $value > 0 )
			$this->combat_log_msg( " $attrib_name (\" + ".$creature->Text( )." + \") становитс€ $value;" );
		
		if( $creature->defence < 0 )
		{
			$this->combat_log_msg( " \" + ".$creature->Text( )." + \" умирает;" );
			$player->creatures[$slot] = null;
		}
	}

	// -------------------------------------------
	// - creatures info functions
	// -------------------------------------------
	function has_creature_in_slot( $whom, $where )
	{
		if( $whom == $this->myself ) $player = $this->me;
		else if( $whom == $this->opponent ) $player = $this->he;
		else return;
		
		$slot = $this->slot;
		
		if( $where >= 0 && $where <= 2 )
			$slot = $where;

		if( $player->creatures[$slot] !== null ) return $player->creatures[$slot]->creature_id;
		return false;
	}
}

?>
