<?

class GrainGame
{
	var $a;
	var $size;
	var $rh;
	var $lh;
	var $ok;

	function GrainGame( )
	{
		$this->a = Array( );
	}

	function CreateField( )
	{
		while( 1 )
		{
			$this->ok = 0;
			$b = Array( );
	        for( $i = 0; $i < $this->size; ++ $i )
	        {
    	    	$this->a[$i] = mt_rand( 1, 25 );
    	    	$b[$i] = $this->a[$i];
    	    }

	        for( $i = 0; $i < 1000; ++ $i )
    	    {
	        	if( $this->makeTurn( true ) ) break;
        	}
        	if( $this->ok > 0 )
        	{
		        for( $i = 0; $i < $this->size; ++ $i )
    		    	$this->a[$i] = $b[$i];
    		    break;
        	}
        }
	}

	function loseValR( $l, $r )
	{
		if( $r == $l ) return $this->a[$l];

		if( isset( $this->rh[$l * $this->size + $r] ) )
			return $this->rh[$l * $this->size + $r];

		$a = $this->loseValL( $l + 1, $r );
		$b = $this->loseValR( $l + 1, $r );

		$ret = $this->a[$l];
		if( $a == $ret ) return 0;
		if( $ret > $a ) -- $ret;
		if( $ret >= $b ) ++ $ret;
		$this->rh[$l * $this->size + $r] = $ret;
		return $ret;
	}

	function loseValL( $l, $r )
	{
		if( $r == $l ) return $this->a[$l];

		if( isset( $this->lh[$l * $this->size + $r] ) )
			return $this->lh[$l * $this->size + $r];

		$b = $this->loseValL( $l, $r - 1 );
		$a = $this->loseValR( $l, $r - 1 );

		$ret = $this->a[$r];
		if( $a == $ret ) return 0;
		if( $ret > $a ) -- $ret;
		if( $ret >= $b ) ++ $ret;
		$this->lh[$l * $this->size + $r] = $ret;
		return $ret;
	}

	function makeTurn( $silent = false ) // return = Is Game Finished
	{
		$l = 0;
		$r = $this->size - 1;

		$this->rh = Array( );
		$this->lh = Array( );

		while( $this->a[$l] == 0 && $l <= $r ) ++ $l;
		while( $this->a[$r] == 0 && $l <= $r ) -- $r;

		if( $l > $r )
		{
			if( !$silent ) echo "GAME FINISHED\n";
			return true;
		}

		if( $l == $r )
		{
			$this->a[$l] = 0;
			if( !$silent ) echo "WON\n";
			return true;
		}

		$b = $this->loseValR( $l, $r - 1 );
		$a = $this->loseValL( $l + 1, $r );

		if( $b == $this->a[$r] )
		{
			if( $this->a[$l] != $this->a[$r] )
			{
				if( !$silent ) echo "IMBA ";
				$this->ok ++;
			}
			if( !$silent ) echo "LOSE POSITION\n";
			if( $this->a[$l] > $this->a[$r] ) $this->a[$l] = $this->a[$r];
			else if( $this->r[$l] > $this->l[$r] ) $this->a[$r] = $this->a[$l];
			else if( mt_rand( 1, 2 ) == 1 ) $this->a[$l] -= mt_rand( 1, $this->a[$l] );
			else $this->a[$r] -= mt_rand( 1, $this->a[$r] );
			return false;
		}

		if( mt_rand( 1, 2 ) == 1 )
		{
			if( $b < $this->a[$r] ) $this->a[$r] = $b;
			else if( $a < $this->a[$l] ) $this->a[$l] = $a;
			else
			{
				if( !$silent ) echo "ASSERT\n";
				return true;
			}
		}
		else
		{
			if( $a < $this->a[$l] ) $this->a[$l] = $a;
			else if( $b < $this->a[$r] ) $this->a[$r] = $b;
			else
			{
				if( !$silent ) echo "ASSERT\n";
				return true;
			}
		}
		return false;
	}

	function StoreGame( $pid )
	{
		f_MQuery( "DELETE FROM grain_game WHERE player_id=$pid" );
		for( $i = 0; $i < $this->size; ++ $i )
			f_MQuery( "INSERT INTO grain_game( player_id, id, value ) VALUES ( $pid, $i, ".$this->a[$i]." )" );
	}

	function LoadGame( $pid )
	{
		$res = f_MQuery( "SELECT * FROM grain_game WHERE player_id=$pid" );
		while( $arr = f_MFetch( $res ) )
			$this->a[$arr['id']] = $arr['value'];
	}
};

/*$gg = new GrainGame( );
$gg->size = 10;
$gg->CreateField( );

for( $i = 0; $i < 1000; ++ $i )
{
	for( $i = 0; $i < $gg->size; ++ $i ) echo $gg->a[$i] . " - ";
	if( $gg->makeTurn( ) ) break;
	echo "<br>";
}
echo "<br>";
for( $i = 0; $i < $gg->size; ++ $i ) echo $gg->a[$i] . " - ";
*/
?>
