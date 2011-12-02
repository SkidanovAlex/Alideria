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
if( !$arr || $arr[0] != 280 ) die( );

$id = $HTTP_RAW_POST_DATA;

settype( $id, 'integer' );

if( $id < -1 || $id >= 63 ) RaiseError( "Попытка выбрать неверную клетку в мире илююзий" );

f_MQuery( "LOCK TABLE player_mines WRITE" );

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

$default_field = '000050060332071030020...';

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
		$st_f[28] = '1';
	}
	$f = substr( $st_f, 0, 24 );
}

if ( $id < 0 )
{
	$f = $default_field;
	$id = 9 * 3 + 4;
}

$dirx = array( 1, 1, 0, -1, -1, -1, 0, 1 );
$diry = array( 0, -1, -1, -1, 0, 1, 1, 1 );

$field = array(
	array( 0, 0, 0, 0, 1, 0, 0 ,0, 0 ),
	array( 0, 0, 0, 1, 1, 1, 0 ,0, 0 ),
	array( 0, 0, 1, 1, 1, 1, 1 ,0, 0 ),
	array( 1, 1, 1, 1, 1, 1, 1 ,1, 1 ),
	array( 0, 0, 1, 1, 1, 1, 1 ,0, 0 ),
	array( 0, 0, 0, 1, 1, 1, 0 ,0, 0 ),
	array( 0, 0, 0, 0, 1, 0, 0 ,0, 0 ) );

$cnt = 0;
for ( $i = 0; $i < 21; ++ $i )
{
	$y = floor( $i / 3 );
	$cx = ( $i % 3 ) * 3;
    $let = ord( $f[$i] ) - ord( '0' );
	for ( $j = 0; $j < 3; ++ $j )
	{
		$x = $cx + $j;
		if ( $let & ( 1 << $j ) )
		{
			++ $cnt;
			$field[$y][$x] = 2;
		}
	}
}

if ( $cnt == 1 )
{
	$f[21] = '.';
	$f[22] = '.';
	f_MQuery( "UNLOCK TABLES" );
	$player->SetTrigger( 115, 0 );
	$player->SetTrigger( 114 );
	echo "setTimeout( 'eval( \"location.href=\\'game.php?phrase=641\\';\" );', 3000 );";
	echo "out( '$f' );"; //win here
}
else
{

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
		global $field;
		return ( $y >= 0 && $y < 7 && $x >= 0 && $x < 9 && $field[$y][$x] );
	}

	function setf( $y, $x, $val )
	{
		global $f;
		$id = $y * 3 + floor( $x / 3 );
		$cur = to_num( $f[$id] );
		if ( $val )
		{
			$cur |= 1 << ( $x % 3 );
		}
		else
		{
			$cur &= ~( 1 << ( $x % 3 ) );
		}
		$f[$id] = chr( $cur + ord( '0' ) );
	}

    $old_sely = to_num( $f[21] );
    $old_selx = to_num( $f[22] );

	$selx = $id % 9;
	$sely = floor( $id / 9 );
    $show = false;
    $f[21] = '.';
    $f[22] = '.';

	if ( $field[$sely][$selx] == 1 )
	{
        if ( in_field( $old_sely, $old_selx ) && $field[$old_sely][$old_selx] == 2 )
        {
			for ( $i = 0; $i < 8; ++ $i )
			{
	            $y2 = $old_sely + 2 * $diry[$i];
	            $x2 = $old_selx + 2 * $dirx[$i];
	            if ( $sely == $y2 && $selx == $x2 )
	            {
	            	$y1 = $old_sely + $diry[$i];
    	        	$x1 = $old_selx + $dirx[$i];
    	        	if ( in_field( $y1, $x1 ) && $field[$y1][$x1] == 2 )
    	        	{
    	        		setf( $y1, $x1, 0 );
    	        		setf( $old_sely, $old_selx, 0 );
    	        		setf( $y2, $x2, 1 );
                        $show = true;
						echo "move( '$f', $old_sely, $old_selx, $i, 2, '$id' );";
    	        		break;
    	        	}
	            }
	        }
        }
	}
	else
	if ( $field[$sely][$selx] == 2 )
	{
		$f[21] = chr( $sely + ord( '0' ) );
		$f[22] = chr( $selx + ord( '0' ) );
	}

    if ( !$show )
    {
		echo "out( '$f' );";
    }

	$st_f = $f . substr( $st_f, 24 );
    f_MQuery( "UPDATE player_mines SET f='$st_f' WHERE player_id={$player->player_id}" );
	f_MQuery( "UNLOCK TABLES" );
}


?>
