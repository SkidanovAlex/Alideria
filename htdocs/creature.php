<?

include_once( 'functions.php' );

class Creature
{
	var $creature_id;
	var $name;
	var $attack;
	var $defence;
	var $genre;
	var $trample;
	var $haste;
	var $firststrike;
	var $image;

    var $effect_dmg_p;
    var $effect_dmg_c;
    var $effect_die;
    var $effect_got_dmg;
    var $effect_always;

	var $just_summoned;
	
	function Creature( $id )
	{
		$res = f_MQuery( "SELECT * FROM creatures WHERE creature_id=$id" );
		$arr = f_MFetch( $res );
		
		$this->creature_id = $arr['creature_id'];
		$this->name = $arr['name'];
		$this->attack = $arr['attack'];
		$this->defence = $arr['defence'];
		$this->genre = $arr['genre'];
		$this->trample = (int)$arr['trample'];
		$this->haste = (int)$arr['haste'];
		$this->firststrike = (int)$arr['firststrike'];
		$this->image = $arr['image'];

        $this->effect_dmg_p = $arr['effect_dmg_p'];
        $this->effect_dmg_c = $arr['effect_dmg_c'];
        $this->effect_die = $arr['effect_die'];
        $this->effect_got_dmg = $arr['effect_got_dmg'];
        $this->effect_always = $arr['effect_always'];

		$this->just_summoned = false;
	}
	
	function Summon( $player_id, $slot )
	{
		f_MQuery( "DELETE FROM combat_creatures WHERE player_id=$player_id AND slot_id=$slot" );
		f_MQuery( "INSERT INTO combat_creatures ( player_id, slot_id, creature_id, attack, defence ) VALUES ( $player_id, $slot, {$this->creature_id}, {$this->attack}, {$this->defence} )" );
	}
	
	function Summon2( $player, $slot )
	{
		$this->just_summoned = true;
		$player->creatures[$slot] = $this;
	}

	function Process2( $str, $me, $he, $we, $they, $combat_id, &$turn, $slot )
	{
		global $global_dmg;
		$global_dmg = 0;

		include_once( 'combat_sdk2.php' );
		$sdk = new sdk_t( $me, $he, $we, $they, $turn, $combat_id, $this->genre, false );

        $func = @create_function( '$sdk, $slot', $str );
        $func( $sdk, $slot );
        $me->dmg_creatures += $sdk->dmg;
			
		return text_sex_parse( '[', '|', ']', text_sex_parse( '{', '|', '}', str_replace( "*victim*", $he->player->login, str_replace( "*player*", $me->player->login, $this->cast_description ) ), $me->player->sex ), $he->player->sex ) . $sdk->log_msg;
	}
	
	function Text( )
	{
		return "crn( '{$this->name}', {$this->genre} )";
	}
	
	function Stats( )
	{
		return "cr( '{$this->name}', {$this->genre}, {$this->attack}, {$this->defence} )";
	}
};

?>
