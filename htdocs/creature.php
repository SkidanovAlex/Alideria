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
