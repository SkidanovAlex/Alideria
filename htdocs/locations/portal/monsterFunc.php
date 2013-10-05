<?

include_once( "functions.php" );
include_once( "locations/portal/func.php" );

class PortalMonster
{
	var $x, $y, $z;
	var $monster_id, $speed, $entry_id;
	var $needsUpdate;
	
	function PortalMonster( $entry_id, $monster_id, $speed, $x, $y, $z )
	{
		$this->entry_id = $entry_id;
		$this->monster_id = $monster_id;
		$this->speed = $speed;
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
		
		$this->needsUpdate = false;
	}
}

class PortalMonsterTarget
{
	var $x, $y, $z;
	
	function PortalMonsterTarget( $x, $y, $z )
	{
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;
	}
}

class PortalMonstersController
{
	var $clan_id;
	var $monsters;
	var $targets;
	
	function PortalMonstersController( $clan_id )
	{
		$this->clan_id = $clan_id;
		$this->monsters = array( );
		$this->targets = array( );
		
		$res = f_MQuery( "SELECT portal_maze.x, portal_maze.y, portal_monsters.* FROM portal_maze INNER JOIN portal_monsters ON portal_maze.cell_id=portal_monsters.cell_id WHERE portal_monsters.clan_id=$clan_id AND player_id=-1 AND died+respawn < ".time() );
		while( $arr = f_MFetch( $res ) )
		{
			$this->monsters[] = new PortalMonster( $arr['entry_id'], $arr['monster_id'], $arr['speed'], $arr['x'], $arr['y'], $arr['z'] );
		}
		
		$this->targets[] = new PortalMonsterTarget( 30, 30, 1 );
	}
	
	function ProcessMonster( $monster )
	{
		$targetId = -1;
		$targetDist = 1000000000;
		foreach( $this->targets as $i=>$target ) if( $target->z == $monster->z )
		{
			$dist = abs($monster->x - $target->x) + abs( $monster->y - $target->y );
			if( $dist <= 5 * $monster->speed ) // если дистанция достижима за пять минут, рассматриваем цель
			{
				if( $targetDist > $dist ) // если при этом мы таргетим что-то, что дальше, то переключаемся
				{
					$targetId = $i;
					$targetDist = $dist;
				}
			}
		}
		
		if( $targetId == -1 ) // если нет цели, то медленно гуляем...
		{
			if( mt_rand( 1, 5 ) <= $monster->speed )
			{
				$dir = mt_rand( 0, 3 ); // пока занесем любое направление, в TryPerformMove оно првоерится на валидность.
				                        // монстры могут ходить сквозь стены
				$this->TryPerformMonsterMove( $monster, $dir );
			}
		}
		else // монстр имеет цель, будем двигаться к ней столько шагов, какая скорость у монстра
		{
			echo "!";
			for( $i = 0; $i < $monster->speed; ++ $i )
			{
				$dir = -1;
				if( $target->y < $monster->y ) $dir = 0;
				if( $target->y > $monster->y ) $dir = 2;
				if( $target->x < $monster->x ) $dir = 1;
				if( $target->x > $monster->x ) $dir = 3;
				$this->TryPerformMonsterMove( $monster, $dir );
			}
		}
	}
	
	function TryPerformMonsterMove( $monster, $dir )
	{
		global $mdx, $mdy, $portalSize;
		if( $dir < 0 || $dir >= 4 ) return false;
		$nx = $monster->x + $mdx[$dir];
		$ny = $monster->y + $mdy[$dir];
		if( $nx < 0 || $ny < 0 || $nx >= $portalSize || $ny >= $portalSize ) return false;
		
		$monster->x = $nx;
		$monster->y = $ny;
		$monster->needsUpdate = true;
		
		return true;
	}
	
	function Process( )
	{
		foreach( $this->monsters as $monster )
		{
			echo "$monster->x, $monster->y";
			$this->ProcessMonster( $monster );
			echo "= $monster->x, $monster->y<br>";
		}
	}
	
	function Save( )
	{
		foreach( $this->monsters as $monster )
		{
			if( $monster->needsUpdate )
			{
				$cell_id = f_MValue( "SELECT cell_id FROM portal_maze WHERE clan_id={$this->clan_id} AND x={$monster->x} AND y={$monster->y} AND z={$monster->z}" );
				if( $cell_id ) f_MQuery( "UPDATE portal_monsters SET cell_id=$cell_id WHERE clan_id={$this->clan_id} AND entry_id={$monster->entry_id}" );
				else echo "-";
			}
		}
	}
}

?>
