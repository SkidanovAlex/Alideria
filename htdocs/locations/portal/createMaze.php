<?

include_once( 'locations/portal/func.php' );

class Cell
{
	var $walls;
	var $keys;
	var $monsters;
	var $message;
	var $type;
	
	function Cell( )
	{
		$this->walls = array(0,0,0,0);
		$this->monsters = array();
		$this->message = "";
		$this->keys = 0;
		$this->type = 0;
	}
}

class Maze
{
	// function ChangeWallState( $x, $y, $dir, $state )
	// function CloseRandomWall( $x, $y )
	// function GenerateMaze_CloseRandomWalls( )
	// function GenerateMaze_RemoveUnreachableCells( )
	// function GenerateMaze_CreateRoomWithDoor( $color )
	// function GenerateMaze_CreateRoomWithDoorAndGuardian( $color, $monster_id, $inside )
	// function GenerateMaze_AddEntrance( )
	// function GenerateMaze_AddMonsters( $monster_id, $number )
	// function GenerateMaze( $monster1, $monster2, $monster3, $boss, $monsterNum )
	// function SaveMaze( $clan_id, $z )

	var $cells;
	var $w;
	var $h;
	
	function Maze($w, $h)
	{
		$this->cells = array();
		$this->h = $h;
		$this->w = $w;
		
		for( $i = 0; $i < $h; ++ $i )
		{
			$this->cells[$i] = array( );
			for( $j = 0; $j < $w; ++ $j )
			{
				$this->cells[$i][$j] = new Cell;
			}
		}
	}
	
	function ChangeWallState( $y, $x, $dir, $state, $opposite_state = -1 )
	{
		global $mdx, $mdy;
		$this->cells[$y][$x]->walls[$dir] = $state;
		$x += $mdx[$dir];
		$y += $mdy[$dir];
		$dir = ($dir + 2) % 4;
		
		if( $x >= 0 && $y >= 0 && $y < $this->h && $x < $this->w )
		{
			if( $opposite_state == -1 ) $opposite_state = $state;
			$this->cells[$y][$x]->walls[$dir] = $opposite_state;
		}
	}
	
	function CloseRandomWall( $y, $x )
	{
		$dir = mt_rand( 0, 3 );
		$this->ChangeWallState( $y, $x, $dir, 1 );
	}
	
	function GenerateMaze_CloseRandomWalls( )
	{
		// Close border
		for( $i = 0; $i < $this->w; ++ $i ) $this->ChangeWallState( 0, $i, 0, 1 );
		for( $i = 0; $i < $this->w; ++ $i ) $this->ChangeWallState( $this->h - 1, $i, 2, 1 );
		for( $i = 0; $i < $this->h; ++ $i ) $this->ChangeWallState( $i, 0, 1, 1 );
		for( $i = 0; $i < $this->h; ++ $i ) $this->ChangeWallState( $i, $this->w - 1, 3, 1 );
		
		// Close random
		for( $x = 0; $x < $this->w; ++ $x )
		{
			for( $y = 0; $y < $this->h; ++ $y )
			{
				$this->CloseRandomWall( $y, $x );
			}
		}
	}
	
	var $p;
	function dfs( $y, $x )
	{
		if( $this->p[$y][$x] ) return;
		$this->p[$y][$x] = true;
		if( $this->cells[$y][$x]->walls[0] == 0 ) $this->dfs( $y - 1, $x );
		if( $this->cells[$y][$x]->walls[1] == 0 ) $this->dfs( $y, $x - 1 );
		if( $this->cells[$y][$x]->walls[2] == 0 ) $this->dfs( $y + 1, $x );
		if( $this->cells[$y][$x]->walls[3] == 0 ) $this->dfs( $y, $x + 1 );
	}
	function GenerateMaze_RemoveUnreachableCells( )
	{
		$this->p = array( );
		for( $y = 0; $y < $this->h; ++ $y )
		{
			$this->p[$y] = array( );
			for( $x = 0; $x < $this->w; ++ $x )
				$this->p[$y][$x] = 0;
		}
		
		$this->dfs( 0, 0 );
		for( $y = 0; $y < $this->h; ++ $y )
			for( $x = 0; $x < $this->w; ++ $x )
				if( $this->p[$y][$x] == 0 )
				{
					if( $y > 0 && ( $x == 0 || mt_rand( 0, 99 ) < 50 ) ) $this->ChangeWallState( $y, $x, 0, 0 );
					else $this->ChangeWallState( $y, $x, 1, 0 );
					$this->dfs( $y, $x );
				}
	}
	
	function GenerateMaze_CreateRoomWithDoor( $color )
	{
		$arr = array( );
		for( $y = 0; $y < $this->h; ++ $y )
			for( $x = 0; $x < $this->w; ++ $x )
			{
				$cnt1 = 0; $cnt0 = 0;
				for( $dir = 0; $dir < 4; ++ $dir )
				{
					$wallType = $this->cells[$y][$x]->walls[$dir];
					if( $wallType == 1 ) ++ $cnt1;
					else if( $wallType == 0 ) ++ $cnt0;
				}
				if( $cnt1 == 3 && $cnt0 == 1 ) $arr[] = array( $y, $x );
			}
		
		if( count( $arr ) == 0 ) return false;
		$id = mt_rand( 0, count( $arr ) - 1 );
		
		$x = $arr[$id][1];
		$y = $arr[$id][0];
		
		for( $dir = 0; $dir < 4; ++ $dir )
		{
			$wallType = $this->cells[$y][$x]->walls[$dir];
			if( $wallType == 0 )
			{
				// $color=0 means that we don't want to have a door
				if( $color != 0 ) $this->ChangeWallState( $y, $x, $dir, 5, $color );
				$arr[$id][] = $dir;
			}
		}
		
		return $arr[$id];
	}
	
	function GenerateMaze_CreateRoomWithDoorAndGuardian( $color, $monster_id, $inside )
	{
		global $mdy, $mdx;
		$ret = $this->GenerateMaze_CreateRoomWithDoor( $color );
		$y = $ret[0];
		$x = $ret[1];
		$dir = $ret[2];
		if( !$inside )
		{
			$y += $mdy[$dir];
			$x += $mdx[$dir];
		}
		$this->cells[$y][$x]->monsters[] = array( $monster_id, /*speed=*/0 );
		return $ret;
	}
	
	function GenerateMaze_AddEntrance( )
	{
		$arr = array( );
		for( $y = 0; $y < $this->h; ++ $y )
			for( $x = 0; $x < $this->w; ++ $x )
			{
				$cnt0 = 0;
				for( $dir = 0; $dir < 4; ++ $dir )
				{
					$wallType = $this->cells[$y][$x]->walls[$dir];
					if( $wallType == 0 ) ++ $cnt0;
				}
				if( $cnt0 >= 3 ) $arr[] = array( $y, $x );
			}
		
		if( count( $arr ) == 0 ) return false;
		$id = mt_rand( 0, count( $arr ) - 1 );
		
		$x = $arr[$id][1];
		$y = $arr[$id][0];

		$this->cells[$y][$x]->type = 1;	
		
		return $arr[$id];
	}
	
	function GenerateMaze_AddMonsters( $monster_id, $number )
	{
		$availableCells = array( );
		for( $y = 0; $y < $this->h; ++ $y )
			for( $x = 0; $x < $this->w; ++ $x )
			{
				if( $this->cells[$y][$x]->keys == 0 && $this->cells[$y][$x]->type == 0 && count( $this->cells[$y][$x]->monsters ) == 0 )
				{
					$availableCells[] = array( $y, $x );
				}
			}
		$availableNumber = count( $availableCells );
		if( $availableNumber < $number ) $number = count( $availableNumber );
		for( $i = 0; $i < $number; ++ $i )
		{
			$id = mt_rand( $i, $availableNumber - 1 );
			$y = $availableCells[$id][0];
			$x = $availableCells[$id][1];
			$this->cells[$y][$x]->monsters[] = array( $monster_id, /*speed=*/mt_rand(1,5) );
			$availableCells[$id][0] = $availableCells[$i][0];
			$availableCells[$id][1] = $availableCells[$i][1];
		}
	}
	
	function GenerateMaze( $monster1, $monster2, $monster3, $boss, $monsterNum )
	{
        $this->GenerateMaze_CloseRandomWalls( );
        $this->GenerateMaze_RemoveUnreachableCells( );

        // room without door with red first key inside
        $coord = $this->GenerateMaze_CreateRoomWithDoorAndGuardian( $monster1, 0, /*inside=*/false );
        $this->cells[$coord[0]][$coord[1]]->keys = 2;
        
        // room with second first key inside
        $coord = $this->GenerateMaze_CreateRoomWithDoorAndGuardian( $monster2, 2, /*inside=*/false );
        $this->cells[$coord[0]][$coord[1]]->keys = 4;
        
        // room with third first key inside
        $coord = $this->GenerateMaze_CreateRoomWithDoorAndGuardian( $monster3, 3, /*inside=*/false );
        $this->cells[$coord[0]][$coord[1]]->keys = 8;
        
        // room with boss
        $coord = $this->GenerateMaze_CreateRoomWithDoorAndGuardian( $boss, 4, /*inside=*/true );
        $this->cells[$coord[0]][$coord[1]]->type = 2;
        
        $this->GenerateMaze_AddEntrance( 1 );
        $this->GenerateMaze_AddMonsters( $monster1, $monsterNum );
        $this->GenerateMaze_AddMonsters( $monster2, $monsterNum );
        $this->GenerateMaze_AddMonsters( $monster3, $monsterNum );
	}
	
	function SaveMaze( $clan_id, $z )
	{
		global $monsters;
		
		f_MQuery( "DELETE FROM portal_monsters WHERE clan_id=$clan_id AND z=$z" );
		f_MQuery( "DELETE FROM portal_maze WHERE clan_id=$clan_id AND z=$z" );
		f_MQuery( "DELETE FROM portal_revealed_cells WHERE clan_id=$clan_id AND z=$z" );
		
		// Export maze
		for( $y = 0; $y < $this->h; ++ $y )
			for( $x = 0; $x < $this->w; ++ $x )
			{
            	$l  = ( $this->cells[$y][$x]->walls[3] ); $l <<= 3;
            	$l |= ( $this->cells[$y][$x]->walls[2] ); $l <<= 3;
            	$l |= ( $this->cells[$y][$x]->walls[1] ); $l <<= 3;
            	$l |= ( $this->cells[$y][$x]->walls[0] );
            	$type = $this->cells[$y][$x]->type;
            	$keys = $this->cells[$y][$x]->keys;
				f_MQuery( "INSERT INTO portal_maze( x, y, z, type, keys_mask, clan_id, walls ) VALUES ( $x, $y, $z, $type, $keys, $clan_id, $l )" );
				$cell_id = mysql_insert_id( );
				
				foreach( $this->cells[$y][$x]->monsters as $monster )
				{
					$monster_id = $monster[0];
					$respawn = $monsters[$monster_id][4];
					$died = time( ) - mt_rand( 0, $respawn );
					$speed = $monster[1];
					if( $speed == 0 ) $died = time( ) - $respawn - 1;
					f_MQuery( "INSERT INTO portal_monsters( cell_id, monster_id, speed, respawn, died, clan_id, z ) VALUES ( $cell_id, $monster_id, $speed, $respawn, $died, $clan_id, $z )" );
				}
			}
	}
	
	function CheckMaze( $clan_id, $z )
	{
		global $portalLifeTime;
		
		f_MQuery( "LOCK TABLE portal_state WRITE" );
		$created = f_MValue( "SELECT created FROM portal_state WHERE clan_id=$clan_id AND z=$z" );
		if( $created == -1 )
		{
			f_MQuery( "UNLOCK TABLES" );
			return false;
		}
		$expires = $created + $portalLifeTime;
		if( $expires > time( ) )
		{
			f_MQuery( "UNLOCK TABLES" );
			return true;
		}
		
		f_MQuery( "DELETE FROM portal_state WHERE clan_id=$clan_id AND z=$z" );
		f_MQuery( "INSERT INTO portal_state( clan_id, z, created ) VALUES ( $clan_id, $z, -1 )" );
		f_MQuery( "UNLOCK TABLES" );
		
		$firstMonster = ($z - 1) * 4;
		$this->GenerateMaze( $firstMonster + 0, $firstMonster + 1, $firstMonster + 2, $firstMonster + 3, 40 );
		$this->SaveMaze( $clan_id, $z );

		f_MQuery( "UPDATE portal_state SET created=".time( )." WHERE clan_id=$clan_id AND z=$z" );
		return true;
	}
}
/*
?>

<script>

var z = [];
<? for( $i = 0; $i < 50; ++ $i ) { ?>
z[<?=$i?>] = [];
<? for( $j = 0; $j < 50; ++ $j ) { ?>
<?
	$l = ( $maze->cells[$i][$j]->walls[3] ); $l <<= 3;
	$l |= ( $maze->cells[$i][$j]->walls[2] ); $l <<= 3;
	$l |= ( $maze->cells[$i][$j]->walls[1] ); $l <<= 3;
	$l |= ( $maze->cells[$i][$j]->walls[0] );

	echo "z[$i][$j] = $l;";
?>

<? } ?>
<? } ?>
portal.r(z);
pt_map.appendChild( portal.renderer.render() );


</script>

<?


echo "Moo!";
*/
?>
