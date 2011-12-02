<?

include_once( 'functions.php' );

class Cell
{
	var $x;
	var $y;
	var $z;
	var $tex;
	var $dir;
	function Cell( $z, $x, $y, $tex, $dir )
	{
		$this->x = $x;
		$this->y = $y;
		$this->z = $z;

		$this->tex = $tex;
		$this->dir = $dir;
	}
};

class Lab
{
	var $lab_id;
	var $w;
	var $h;
	var $d;
	var $cells;
	
	function Lab( $id, $d, $w, $h )
	{
		$this->lab_id = $id;
		$this->w = $w;
		$this->h = $h;
		$this->d = $d;
	}

	function Ocuppied( $z, $x, $y )
	{
		if( $z < 0 || $z >= $this->d ) return 1;
		if( $x < 0 || $x >= $this->w ) return 1;
		if( $y < 0 || $y >= $this->h ) return 1;
		return $this->cells[$z][$y][$x]->tex;
	}

	function Init( )
	{
		$this->cells = Array( );
		for( $k = 0; $k < $this->d; ++ $k )
		{
			$this->cells[$k] = Array( );
    		for( $i = 0; $i < $this->h; ++ $i )
    		{
    			$this->cells[$k][$i] = Array( );
    			for( $j = 0; $j < $this->w; ++ $j )
    				$this->cells[$k][$i][$j] = new Cell( $k, $j, $i, 0, 0 );
    		}
		}
	}

	function GenerateWall( $z )
	{
		$x = mt_rand( 0, $this->w - 1 );
		$y = mt_rand( 0, $this->h - 1 );
		$dx = 0; $dy = 0;
		while( $dx && $dy || !$dx && !$dy )
		{
			$dx = mt_rand( -1, 1 );
			$dy = mt_rand( -1, 1 );
		}
		$l = mt_rand( 1, ceil( $this->w / 5 ) );
		for( $i = 0; $i < $l; ++ $i )
		{
			$ok = true;
			for( $ddx = -1; $ddx <= 1; ++ $ddx ) 
				for( $ddy = -1; $ddy <= 1; ++ $ddy )
				{
					if( ( $ddx != - $dx || $ddy != - $dy ) && $this->Ocuppied( $z, $x + $ddx, $y + $ddy ) )
						$ok = false;
				}

			if( !$ok ) return;
			$this->cells[$z][$y][$x]->tex = 1;
			$x += $dx;
			$y += $dy;
		}
		if( mt_rand( 0, 1 ) )
		{
			$t = $dx; $dx = $dy; $dy = - $t;
		}
		else
		{
			$t = $dx; $dx = - dy; $dy = $t;
		}
		$l = mt_rand( 1, ceil( $this->w / 5 ) );
		for( $i = 0; $i < $l; ++ $i )
		{
			$ok = true;
			if( $i > 1 ) for( $ddx = -1; $ddx <= 1; ++ $ddx ) 
				for( $ddy = -1; $ddy <= 1; ++ $ddy )
				{
					if( ( $ddx != - $dx || $ddy != - $dy ) && $this->Ocuppied( $z, $x + $ddx, $y + $ddy ) )
						$ok = false;
				}

			if( !$ok ) return;
			$this->cells[$z][$y][$x]->tex = 1;
			$x += $dx;
			$y += $dy;
		}
	}

	function Generate( )
	{
		for( $z = 0; $z < $this->d; ++ $z )
		{
        	for( $i = 0; $i < $this->w * $this->h; ++ $i )
        	{
        		$this->GenerateWall( $z );
        	}

        	for( $x = 0; $x < $this->w; ++ $x )
        		for( $y = 0; $y < $this->h; ++ $y )
        		{
					$ok = true;
					for( $dx = -1; $dx <= 1; ++ $dx ) 
						for( $dy = -1; $dy <= 1; ++ $dy )
						{
							if( $this->Ocuppied( $z, $x + $dx, $y + $dy ) )
								$ok = false;
						}
				    if( $ok )
				    {
				    	$this->cells[$z][$y][$x]->tex = 2;
				    }
        		}
        }
        for( $i = 0; $i < 1000; ++ $i )
        {
        	$x = mt_rand( 1, $this->w - 1 );
        	$y = mt_rand( 0, $this->h - 1 );
			if( !$this->Ocuppied( 0, $x, $y ) && !$this->Ocuppied( 0, $x - 1, $y ) )
			{
				$this->cells[0][$y][$x]->dir = -1;
				break;
			}
        }
	}

	function Store( )
	{
		f_MQuery( "DELETE FROM lab WHERE lab_id={$this->lab_id}" );
		for( $k = 0; $k < $this->d; ++ $k )
			for( $i = 0; $i < $this->h; ++ $i )
				for( $j = 0; $j < $this->w; ++ $j )
				{
					$tex = $this->cells[$k][$i][$j]->tex;
					$dir = $this->cells[$k][$i][$j]->dir;
					f_MQuery( "INSERT INTO lab ( lab_id, tex, dir, x, y, z ) VALUES ( {$this->lab_id}, $tex, $dir, $j, $i, $k )" );
				}
	}

	function DebugOut( )
	{
		echo $this->d;
		for( $z = 0; $z < $this->d; ++ $z )
		{
			echo "Depth: <b>$z</b><br>";
    		echo "<table>";
    		for( $y = 0; $y < $this->h; ++ $y )
    		{
    			echo "<tr>";
    			for( $x = 0; $x < $this->w; ++ $x )
    			{
    				echo "<td>";
    				if( $this->cells[$z][$y][$x]->tex == 0 ) echo ".";
    				if( $this->cells[$z][$y][$x]->tex == 1 ) echo "#";
    				if( $this->cells[$z][$y][$x]->tex == 2 ) echo "X";
    				echo "</td>";
    			}
    			echo "</tr>";
    		}          
    		echo "</table>";
	 	}	
	}
};

function isLabLoc( $loc, $depth )
{
	if( $loc == 0 && $depth >= 33 && $depth <= 40  ) return 0;
	return -1;
}

?>
