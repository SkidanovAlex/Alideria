<?

if( !$mid_php )
{
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
    if( !$arr || $arr[0] != 252 ) die( );
}

//if( $player->HasTrigger( 74 ) ) die( );

$p = array( );
$pp = array( );
$f = '';
for( $i = 0; $i < 200; ++ $i )  $f .= '0';
function moo( $n )
{
	if( $n < 10 ) return $n;
	return chr( ord('a') + $n - 10 );
}
function umoo( $n )
{
	if( $n == 'a' ) return 10;
	if( $n == 'b' ) return 11;
	if( $n == 'c' ) return 12;
	if( $n == 'd' ) return 13;
	if( $n == 'e' ) return 14;
	return (int)$n;
}

$app = 0;

function dfs( $x, $y, $dir, $sec = false, $l )
{
	global $p, $pp;
	global $f;
	global $app;

	$dx = array( -1, 0, 1, 0 );
	$dy = array( 0, 1, 0, -1 );

	if( !$sec && $y == 20 && $x == 2 ) return 1;
	if( !$sec && $y == 20 && $x == 6 ) return 1;

	if( $x < 0 || $y < 0 || $x >= 10 || $y >= 20 ) return 0;

	$id = $x * 20 + $y;

	if( $sec && $f[$id] != '0' )
	{
		if( $l < 6 ) return 0;
		$dir = ( $dir + 2 ) % 4;
		$dir = ( 1 << $dir );
		$cur = umoo( $f[$id] );
		if( $cur & $dir ) return 0;
		$cur += $dir;
		if( $cur == 15 ) return 0;
		$f[$id] = moo( $cur );
		return 1;
	}

	if( !$sec )
	{
		if( $p[$id] || $pp[$id] ) return 0;
		$p[$id] = true;
		$pp[$id] = true;
	}
	else
	{
		if( $pp[$id] ) return 0;
		$pp[$id] = true;
	}

	$dirs = array( 0, 1, 2, 3 );
	for( $i = 0; $i < 3; ++ $i )
	{
		$j = mt_rand( $i, 3 );
		$t = $dirs[$i];
		$dirs[$i] = $dirs[$j];
		$dirs[$j] = $t;
	}

	$val = ( 1 << ( ( $dir + 2 ) % 4 ) );
	$num = 0;
	$mx = 2; if ($sec) $mx = 1;
	for( $j = 0; $j < 4 && $num < $mx; ++ $j )
	{
		$i = $dirs[$j];
		if( $add = dfs( $x + $dx[$i], $y + $dy[$i], $i, $sec, $l + 1 ) )
		{
			$val += ( 1 << $i );
            $num += $add;
		}
	}

	if( $j < 4 && $num == 2 && $app < 5 && mt_rand( 1, 3 ) == 1 && $val + ( 1 << $dirs[$j] ) != 15 && ( $val & ( 1 << $dirs[$j] ) ) == 0 )
	{
		if( dfs( $x + $dx[$dirs[$j]], $y + $dy[$dirs[$j]], $dirs[$j], true, 1 ) )
		{
			$val += ( 1 << $dirs[$j] );
        	++ $app;
        }
	}

	if( $num ) $f[$id] = moo( $val );
	else if( !$sec ) $pp[$id] = false;

	return $num;
}

function check( $x, $y, $dir )
{
	global $p;
	global $f;

	$dx = array( -1, 0, 1, 0 );
	$dy = array( 0, 1, 0, -1 );

	if( $y == 20 && $x == 2 ) return 1;
	if( $y == 20 && $x == 6 ) return 1;
	if( $y == -1 && $x == 7 ) return 0;

	if( $x < 0 || $y < 0 || $x >= 10 || $y >= 20 ) return -1;

	$id = $x * 20 + $y;

	if( $f[$id] == '0' ) return -1;

	$perm = umoo( $f[$id] );
	$dir = ( 2 + $dir ) % 4;
	if( !( $perm & ( 1 << $dir ) ) ) return -1;

	if( $p[$id] ) return 0;
	$p[$id] = true;

	for( $i = 0; $i < 4; ++ $i ) if( $perm & ( 1 << $i ) )
		$ret += check( $x + $dx[$i], $y + $dy[$i], $i );

	return $ret;
}

f_MQuery( "LOCK TABLE player_mines WRITE" );
$res = f_MQuery( "SELECT f, lost FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( $arr )
{
	$f = $arr['f'];
	$l = $arr['lost'];
}
else
{
	$l = 0;
	dfs( 7, 0, 1 );
	for( $i = 0; $i < 200; ++ $i ) if( $f[$i] != '0' )
	{
		$id = $i;
		$it = mt_rand( 0, 3 );
		for( $j = 0; $j < $it; ++ $j )
		{
			$q = umoo( $f[$id] );
        	$q = $q << 1;
        	if( $q & 16 )
        	{
        		$q = $q - 15;
        	}
        	$f[$id] = moo( $q );
		}
	}
	f_MQuery( "INSERT INTO player_mines( player_id, f ) VALUES ( {$player->player_id}, '$f' )" );
}
f_MQuery( "UNLOCK TABLES" );

if( $l )
{
	echo "refr( '$f' );";
	echo "winact();";
	return;
}

if( isset( $_GET['id'] ) )
{
	$id = (int)$_GET['id'];
	if( $id >= 0 && $id < 200 && $f[$id] != '0' )
	{
		$q = umoo( $f[$id] );
		$q = $q << 1;
		if( $q & 16 )
		{
			$q = $q - 15;
		}
		$f[$id] = moo( $q );
		f_MQuery( "UPDATE player_mines SET f='$f' WHERE player_id={$player->player_id}" );
		echo "cone( $id, '$f[$id]' );";
		if( check( 7, 0, 1 ) == 2 )
		{
			f_MQuery( "UPDATE player_mines SET lost=1 WHERE player_id={$player->player_id}" );
			$player->SetTrigger( 76, 1 );
			echo "winact();";
		}
		echo "cone( $id, '$f[$id]' );";
		return;
	}
}

echo "refr( '$f' );";

?>

