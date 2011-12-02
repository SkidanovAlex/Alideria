<?

header("Content-type: text/html; charset=windows-1251");

include_once( "../no_cache.php" );
include_once( "../functions.php" );
include_once( "../player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$res = f_MQuery( "SELECT talk_id FROM player_talks WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( !$arr || $arr[0] != 274 ) die( );

$id = $HTTP_RAW_POST_DATA;

settype( $id, 'integer' );

if( $id < -1 || $id >= 42 ) RaiseError( "Попытка выбрать неверную клетку в мире невесомости" );

f_MQuery( "LOCK TABLE player_mines WRITE" );

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

$default_field = '00030633................';

if( !$arr )
{
	f_MQuery( "UNLOCK TABLES" );
	RaiseError( 'Нет начальных данных' );
}
else
{
	$st_f = $arr['f'];
	if ( $st_f[32] == '1' )
	{
		$st_f[32] = '0';
		$st_f[28] = '3';
	}
	$f = substr( $st_f, 0, 24 );
}

if ( $id < 0 )
{
	$f = $default_field;
	$id = 24;
}

$dirx = array( 1, 0, -1, 0 );
$diry = array( 0, -1, 0, 1 );

$walls = array(
	array(
	 array( 1, 0, 0, 0, 0, 1, 0 ),
	 array( 0, 1, 0, 0, 1, 0, 0 ),
	 array( 0, 0, 0, 0, 0, 0, 0 ),
	 array( 0, 0, 0, 0, 0, 0, 0 ),
	 array( 0, 1, 0, 0, 1, 0, 0 ),
	 array( 1, 0, 0, 0, 0, 1, 0 )
	),
	array(
	 array( 0, 0, 0, 0, 0, 0, 0 ),
	 array( 0, 1, 0, 0, 0, 1, 0 ),
	 array( 0, 0, 1, 0, 1, 0, 0 ),
	 array( 0, 0, 0, 0, 0, 0, 0 ),
	 array( 0, 0, 1, 0, 1, 0, 0 ),
	 array( 0, 1, 0, 0, 0, 1, 0 )
	),
	array(
	 array( 0, 1, 0, 0, 0, 0, 1 ),
	 array( 0, 0, 1, 0, 0, 1, 0 ),
	 array( 0, 0, 0, 0, 0, 0, 0 ),
	 array( 0, 0, 0, 0, 0, 0, 0 ),
	 array( 0, 0, 1, 0, 0, 1, 0 ),
	 array( 0, 1, 0, 0, 0, 0, 1 )
	),
	array(
	 array( 0, 1, 0, 0, 0, 1, 0 ),
	 array( 0, 0, 1, 0, 1, 0, 0 ),
	 array( 0, 0, 0, 0, 0, 0, 0 ),
	 array( 0, 0, 1, 0, 1, 0, 0 ),
	 array( 0, 1, 0, 0, 0, 1, 0 ),
	 array( 0, 0, 0, 0, 0, 0, 0 )
	)
);

if( substr_compare( $f, '34', 6, 2 ) == 0 )
{
	for ( $i = 4 * 2; $i < 9 * 2; ++ $i )
		$f[$i] = '.';
	f_MQuery( "UNLOCK TABLES" );
	$player->SetTrigger( 117, 0 );
	$player->SetTrigger( 114 );
	echo "setTimeout( 'eval( \"location.href=\\'game.php?phrase=633\\';\" );', 3000 );";
	echo "out( '$f' );"; //win here
}
else
{
	$empty_arr = array( 0, 0, 0, 0, 0, 0, 0 );
    $field = array( );
	for ( $i = 0; $i < 6; ++ $i )
	{
		$field[] = $empty_arr;
	}

    function to_num( $c )
    {
    	if ( $c == '.' )
    		return -1;
    	return ord( $c ) - ord( '0' );
    }

    $posx = array( );
    $posy = array( );

	function in_field( $y, $x )
	{
		return $y >= 0 && $y < 6 && $x >= 0 && $x < 7;
	}

    for ( $i = 0; $i < 9; ++ $i )
    {
    	$cy = to_num( $f[$i * 2] );
    	$cx = to_num( $f[$i * 2 + 1] );
        $posy[] = $cy;
        $posx[] = $cx;
        if ( $i < 4 && in_field( $cy, $cx ) )
	    	$field[$cy][$cx] = $i + 1;
    }

    function set_f( $id, $y, $x )
    {
    	global $f;
    	$f[$id * 2] = chr( $y + ord( '0' ) );
    	$f[$id * 2 + 1] = chr( $x + ord( '0' ) );
    }

	function can_move( $y, $x, $dir )
	{
		global $dirx;
		global $diry;
		global $field;
		global $walls;
        $nx = $x + $dirx[$dir];
        $ny = $y + $diry[$dir];
        if ( !in_field( $ny, $nx ) )
        	return false;
    	if ( $field[$ny][$nx] != 0 )
    		return false;
    	if ( $walls[$dir][$y][$x] )
    		return false;
    	return true;
	}
	$selx = $id % 7;
	$sely = ( $id - $selx ) / 7;
	for ( $i = 4 * 2; $i < 9 * 2; ++ $i )
		$f[$i] = '.';
	$oldf = $f;

	if ( $field[$sely][$selx] > 0 )
	{
		set_f( 4, $sely, $selx );
		for ( $i = 0; $i < 4; ++ $i )
		{
			if ( can_move( $sely, $selx, $i ) )
			{
	            $nx = $selx + $dirx[$i];
	            $ny = $sely + $diry[$i];
	            set_f( 5 + $i, $ny, $nx );
			}
			else
			{
				$f[( 5 + $i ) * 2] = '.';
				$f[( 5 + $i ) * 2 + 1] = '.';
			}
		}
	    echo "out( '$f' );";
	}
	else
	{
		for ( $i = 0; $i < 4; ++ $i )
		{
			if ( $sely == $posy[5 + $i] && $selx == $posx[5 + $i] )
			{
				$old_y = $posy[4];
				$old_x = $posx[4];
				$old_sel_id = $field[$old_y][$old_x];
				if ( $old_sel_id == 0 )
					continue;
                $ny = $old_y;
                $nx = $old_x;
                $cnt = 0;
				while ( can_move( $ny, $nx, $i ) )
				{
					$ny += $diry[$i];
					$nx += $dirx[$i];
					++ $cnt;
				}
				set_f( $old_sel_id - 1, $ny, $nx );
				$nid = $ny * 7 + $nx;
				echo "move( '$oldf', $old_y, $old_x, $i, $cnt, '$nid' );";
				break;
			}
		}
		if ( $i == 4 )
		{
			echo "out( '$f' );";
		}
	}

	$st_f = $f . substr( $st_f, 24 );
    f_MQuery( "UPDATE player_mines SET f='$st_f' WHERE player_id={$player->player_id}" );
	f_MQuery( "UNLOCK TABLES" );
}


?>
