<?

if( $sdk_ver_2 )
{
	include( "combat_sdk2.php" );
	exit( );
}

class sdk_t
{
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

	var $player_myself;
	var $player_opponent;
	var $log_msg;
	
	var $my_id;
	var $his_id;
	var $slot;
	var $combat_id;
	
	function sdk_t( $f_my_id, $f_his_id, $f_slot, $f_combat_id )
	{
		$this->my_id                     = $f_my_id;
		$this->his_id                    = $f_his_id;
		$this->slot                      = $f_slot;
		$this->combat_id                 = $f_combat_id;

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
		$this->player_myself             = false;
		$this->player_opponent           = false;
		$this->log_msg                   = "";
	}

	function get_player_myself( )
	{
		if( $this->player_myself === false )
			$this->player_myself = new Player( $this->my_id );
			
		return $this->player_myself;
	}

	function get_player_opponent( )
	{
		if( $this->player_opponent === false )
			$this->player_opponent = new Player( $this->his_id );
			
		return $this->player_opponent;
	}

	function get_players( $whom )
	{
		$res = Array( );
		
		if( $whom == $this->myself ) $res[] = $this->get_player_myself( );
		else if( $whom == $this->opponent ) $res[] = $this->get_player_opponent( );
		
		return $res;
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
		$res = f_MQuery( "SELECT cur_turn FROM combats WHERE combat_id = $this->combat_id" );
		$arr = f_MFetch( $res );
		return $arr[0];
	}



	// -------------------------------------------
	// - character functions
	// -------------------------------------------
	function kill( $whom )
	{
		$players = $this->get_players( $whom );

		foreach( $players as $player )
		{
			$player->SetAttr( 1, 0 );
			$this->combat_log_msg( " <b>{$player->login}</b> умирает;" );
		}
	}

	function damage( $whom, $value )
	{
		$players = $this->get_players( $whom );
		if( $value < 0 ) $value = 0;

		foreach( $players as $player )
		{
			$res = f_MQuery( "SELECT damage_block FROM combat_players WHERE player_id = {$player->player_id}" );
			$arr = f_MFetch( $res );
			if( !$arr ) RaiseError( 'Ошибка обрабоки свитков', 'Ошибка set_damage_blocking' );
			if( $arr[0] > 0 ) continue;

			$res = f_MQuery( "SELECT damage_coef FROM combat_players WHERE player_id = {$player->player_id}" );
			$arr = f_MFetch( $res );
			if( !$arr ) RaiseError( 'Ошибка обрабоки свитков', 'Ошибка set_damage_coef' );
			$value *= ($arr[0] + 1e-7);
			settype( $value, 'integer' );

			$player->AlterAttrib( 1, - $value );
			$this->combat_log_msg( " <b>{$player->login}</b> получает $value единиц повреждения;" );
		}
	}

	function heal( $whom, $value )
	{
		$players = $this->get_players( $whom );
		if( $value < 0 ) $value = 0;

		foreach( $players as $player )
		{
			$player->AlterAttrib( 1, $value );
			$this->combat_log_msg( " <b>{$player->login}</b> восстанавливает $value единиц здоровья;" );
		}
	}

	function alter_attrib( $whom, $attribute_id, $value )
	{
		$players = $this->get_players( $whom );
		
		foreach( $players as $player )
		{
			$anames = $player->getAllAttrNames( );
			$aclrs = $player->gclrs;
			
			$attribute_name = "<b><font color={$aclrs[$attribute_id]}>{$anames[$attribute_id]}</font></b>";

			$player->AlterAttrib( $attribute_id, $value );
			if( $value > 0 )
				$this->combat_log_msg( " $attribute_name у <b>{$player->login}</b> увеличивается на $value единиц;" );
			else
				$this->combat_log_msg( " $attribute_name у <b>{$player->login}</b> уменьшается на $value единиц;" );
		}
	}

	function set_attrib( $whom, $attribute_id, $value )
	{
		$players = $this->get_players( $whom );

		foreach( $players as $player )
		{
			$player->SetAttr( $attribute_id, $value );
			$this->combat_log_msg( " $attribute_id у <b>{$player->login}</b> становится $value;" );
		}
	}
	
	function aura( $whom, $aura_id, $duration )
	{
		$players = $this->get_players( $whom );

		foreach( $players as $player )
		{
			include_once( 'aura.php' );
			$aura = new Aura( $aura_id );
			$this->combat_log_msg( " На <b>$player->login</b> накладывается аура <b>&quot;{$aura->name}&quot;</b>." );
			$this->combat_log_msg( $aura->Enchant( $player->player_id, $this->combat_id, $duration ) );
		}
	}

	function set_damage_blocking( $whom, $value )
	{
		$players = $this->get_players( $whom );

		foreach( $players as $player )
		{
			$res = f_MQuery( "SELECT damage_block FROM combat_players WHERE player_id = {$player->player_id}" );
			$arr = f_MFetch( $res );
			if( !$arr ) RaiseError( 'Ошибка обрабоки свитков', 'Ошибка set_damage_blocking' );
			if( $arr[0] == 1 && $value == -1 ) $this->combat_log_msg( " <b>$player->login</b> вновь начинает получать повреждения." );
			if( $arr[0] == 0 && $value == 1 ) $this->combat_log_msg( " <b>$player->login</b> прекращает получать повреждения." );
			$arr[0] += $value;
			if( $arr[0] >= 0 )
			{
				f_MQuery( "UPDATE combat_players SET damage_block = $arr[0] WHERE player_id = {$player->player_id}" );
			}
		}
	}

	function set_damage_coef( $whom, $value )
	{
		$players = $this->get_players( $whom );

		foreach( $players as $player )
		{
			$res = f_MQuery( "SELECT damage_coef FROM combat_players WHERE player_id = {$player->player_id}" );
			$arr = f_MFetch( $res );
			if( !$arr ) RaiseError( 'Ошибка обрабоки свитков', 'Ошибка set_damage_coef' );
			$arr[0] *= $value;
			f_MQuery( "UPDATE combat_players SET damage_coef = $arr[0] WHERE player_id = {$player->player_id}" );
			$this->combat_log_msg( " <b>$player->login</b> теперь получает <b>x$arr[0]</b> повреждения." );
		}
	}


	// -------------------------------------------
	// - character info functions
	// -------------------------------------------
	function get_attrib_value( $whom, $attribute_id )
	{
		if( $whom == $this->myself ) $player = $this->get_player_myself( );
		else if( $whom == $this->opponent ) $player = $this->get_player_opponent( );
		else return;
		
		if( $attribute_id == 0 ) return $player->level;
		return $player->GetAttr( $attribute_id );
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
			$creature->Summon( $this->my_id, $slot );
		}
		else for( $slot = 0; $slot < 3; ++ $slot )
		{
			$creature = new Creature( $creature_id );
			$creature->Summon( $this->my_id, $slot );
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
			
		$creature_id = $this->has_creature_in_slot( $whom, $where );
		if( $creature_id === false )
			return;

		if( $whom == $this->myself ) $player_id = $this->my_id;
		else if( $whom == $this->opponent ) $player_id = $this->his_id;
		else return;
		
		$slot = $this->slot;
		
		if( $where >= 0 && $where <= 2 )
			$slot = $where;

		f_MQuery( "DELETE FROM combat_creatures WHERE player_id = $player_id AND slot_id = $slot" );
		
		$creature = new Creature( $creature_id );
		$this->combat_log_msg( " \" + ".$creature->Text( )." + \" умирает;" );
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
			
		$creature_id = $this->has_creature_in_slot( $whom, $where );
		if( $creature_id === false )
			return;

		if( $whom == $this->myself ) $player_id = $this->my_id;
		else if( $whom == $this->opponent ) $player_id = $this->his_id;
		else return;
		
		if( $attrib == $this->creature_defence )
		{
			$column_name = "defence";
			$attrib_name = "Защита";
		}
		else if( $attrib == $this->creature_attack )
		{
			$column_name = "attack";
			$attrib_name = "Атака";
		}
		else return;
		
		$slot = $this->slot;
		
		if( $where >= 0 && $where <= 2 )
			$slot = $where;

		f_MQuery( "UPDATE combat_creatures SET $column_name = $column_name + $value WHERE player_id = $player_id AND slot_id = $slot" );
		
		$creature = new Creature( $creature_id );
		
		if( $value > 0 )
			$this->combat_log_msg( " $attrib_name (\" + ".$creature->Text( )." + \") увеличивается на $value;" );
		else
		{
			$nvalue = - $value;
			$this->combat_log_msg( " $attrib_name (\" + ".$creature->Text( )." + \") уменьшается на $nvalue;" );
		}
		
		if( $attrib == $this->creature_defence && $value < 0 )
		{
			$res = f_MQuery( "SELECT defence FROM combat_creatures WHERE player_id = $player_id AND slot_id = $slot" );
			$arr = f_MFetch( $res );
			if( $arr[0] <= 0 )
			{
				$this->combat_log_msg( " \" + ".$creature->Text( )." + \" умирает;" );
				f_MQuery( "DELETE FROM combat_creatures WHERE player_id = $player_id AND slot_id = $slot" );
			}
		}
	}

	function set_creature_attrib( $whom, $where, $attrib, $value )
	{
		if( $where == -2 )
		{
			$this->set_creature_attrib( $whom, 0, $attrib, $value );
			$this->set_creature_attrib( $whom, 1, $attrib, $value );
			$this->set_creature_attrib( $whom, 2, $attrib, $value );
			return;
		}
			
		$creature_id = $this->has_creature_in_slot( $whom, $where );
		if( $creature_id === false )
			return;

		if( $whom == $this->myself ) $player_id = $this->my_id;
		else if( $whom == $this->opponent ) $player_id = $this->his_id;
		else return;
		
		if( $attrib == $this->creature_defence )
		{
			$column_name = "defence";
			$attrib_name = "Защита";
		}
		else if( $attrib == $this->creature_attack )
		{
			$column_name = "attack";
			$attrib_name = "Атака";
		}
		else return;
		
		$slot = $this->slot;
		
		if( $where >= 0 && $where <= 2 )
			$slot = $where;

		f_MQuery( "UPDATE combat_creatures SET $column_name = $value WHERE player_id = $player_id AND slot_id = $slot" );
		
		$creature = new Creature( $creature_id );
		
		if( $value > 0 )
			$this->combat_log_msg( " $attrib_name (\" + ".$creature->Text( )." + \") становится $value;" );
		
		if( $attrib == $this->creature_defence && $value < 0 )
		{
			$res = f_MQuery( "SELECT defence FROM combat_creatures WHERE player_id = $player_id AND slot_id = $slot" );
			$arr = f_MFetch( $res );
			if( $arr[0] <= 0 )
			{
				$this->combat_log_msg( " \" + ".$creature->Text( )." + \" умирает;" );
				f_MQuery( "DELETE FROM combat_creatures WHERE player_id = $player_id AND slot_id = $slot" );
			}
		}
	}

	// -------------------------------------------
	// - creatures info functions
	// -------------------------------------------
	function has_creature_in_slot( $whom, $where )
	{
		if( $whom == $this->myself ) $player_id = $this->my_id;
		else if( $whom == $this->opponent ) $player_id = $this->his_id;
		else return;
		
		$slot = $this->slot;
		
		if( $where >= 0 && $where <= 2 )
			$slot = $where;

		$res = f_MQuery( "SELECT creature_id FROM combat_creatures WHERE player_id = $player_id AND slot_id = $slot" );
		$arr = f_MFetch( $res );
		if( $arr ) return $arr[0];
		return false;
	}
}

?>
