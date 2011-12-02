<?

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

	if( !$sec && $y == 24 && $x == 2 ) return 1;
	if( !$sec && $y == 24 && $x == 6 ) return 1;

	if( $x < 0 || $y < 0 || $x >= 25 || $y >= 24 ) return 0;

	$id = $x * 25 + $y;

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

	for( ; $j < 4; ++ $j ) 
	if( $j < 4 && $num == 2 && mt_rand( 1, 1 ) == 1 && $val + ( 1 << $dirs[$j] ) != 15 && ( $val & ( 1 << $dirs[$j] ) ) == 0 )
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

	if( $y == 24 && $x == 2 ) return 1;
	if( $y == 24 && $x == 6 ) return 1;
	if( $y == -1 && $x == 7 ) return 0;

	if( $x < 0 || $y < 0 || $x >= 25 || $y >= 24 ) return -1;

	$id = $x * 25 + $y;

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

	$l = 0;
	dfs( 7, 0, 1 );
	for( $i = 0; $i < 25*25; ++ $i ) if( $f[$i] != '0' )
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

$buf = array( );
for( $i = 0; $i < 3 * 25 + 1; ++ $i )
for( $j = 0; $j < 4 * 25 + 1; ++ $j ) $buf[$i][$j] = '.';

for( $i = 0; $i < 25; ++ $i )
for( $j = 0; $j < 25; ++ $j )
{
	for( $ii = 0; $ii < 2; ++ $ii )
		for( $jj = 0; $jj < 3; ++ $jj )
			$buf[$i * 3 + $ii + 1][$j * 4 + $jj + 1] = ' ';
	$q = umoo( $f[$i * 25 + $j] );
	if( $q & 1 ) $buf[$i * 3 + 1][$j * 4 + 2] = '|';
	if( $q & 4 ) $buf[$i * 3 + 2][$j * 4 + 2] = '|';
	if( $q & 2 ) $buf[$i * 3 + 1][$j * 4 + 1] = '_';
	if( $q & 8 ) $buf[$i * 3 + 1][$j * 4 + 3] = '_';

	if( $q == 10 )$buf[$i * 3 + 1][$j * 4 + 2] = '_';

	if( $i == 2 || $i == 6 ) if( $j == 24 )
	{
		$buf[$i * 3 + 1][$j * 4 + 1] = '/';
		$buf[$i * 3 + 2][$j * 4 + 1] = '\\';
		$buf[$i * 3 + 1][$j * 4 + 2] = '(';
		$buf[$i * 3 + 2][$j * 4 + 2] = '(';

   	}
}

for( $i = 0; $i < 3 * 25 + 1; ++ $i ) {
for( $j = 0; $j < 4 * 25 + 1; ++ $j ) echo $buf[$i][$j];
echo "\n";
}

?>
