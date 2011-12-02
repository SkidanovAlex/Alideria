<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "ox_functions.php" );
include_once( "waste_stats.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$pid = (int)$HTTP_COOKIE_VARS['c_id'];

if( $_GET['leave'] )
{
	f_MQuery( "LOCK TABLE player_waste WRITE, ox WRITE" );
    $res = f_MQuery( "SELECT * FROM ox WHERE p1=$pid OR p2=$pid" );
	$arr = f_MFetch( $res );
	if( $arr &&  $arr['won'] > 0 )
	{
		f_MQuery( "UPDATE player_waste SET regime=0 WHERE player_id=$pid" );
		if( $arr['p1'] == 0 || $arr['p2'] == 0 )
			f_MQuery( "DELETE FROM ox WHERE p1=$pid OR p2=$pid" );
		else if( $arr['p1'] == $pid )
			f_MQuery( "UPDATE ox SET p1=0 WHERE p1=$pid" );
		else f_MQuery( "UPDATE ox SET p2=0 WHERE p2=$pid" );
	}
	f_MQuery( "UNLOCK TABLES" );
	die( "<script>location.href='waste.php?rnd=".mt_rand( )."';</script>" );
}

function getField( $f )
{
	$arr = array( );
	$id = 0;
	for( $i = 0; $i < 20; ++ $i )
	{
		$arr[$i] = array( );
		for( $j = 0; $j < 20; ++ $j )
			$arr[$i][$j] = $f[$id ++];
	}
	return $arr;
}

function checkWinner( $arr )
{
	$draw = true;
	$dx = array( -1, 0, 1, -1, 1, -1, 0, 1 );
	$dy = array( -1, -1, -1, 0, 0, 1, 1, 1 );
	for( $i = 0; $i < 20; ++ $i )
		for( $j = 0; $j < 20; ++ $j ) if( $arr[$i][$j] != ' ' )
		{
			for( $z = 0; $z < 8; ++ $z )
			{
				$k = 0;
				$x = $i; $y = $j;
				while( $k < 5 && $x >= 0 && $y >= 0 && $x < 20 && $y < 20 && $arr[$x][$y] == $arr[$i][$j] )
				{
					++ $k;
					$x += $dx[$z];
					$y += $dy[$z];
				}
				if( $k >= 5 )
				{
					if( $arr[$i][$j] == 'x' ) return 2;
					return 1;
				}
			}
		} else $draw = false;
	if( $draw ) return 5;
	return 0;
}

function Check($x, $y, $f )
{
	if ($f [$x][$y] != ' ') return 0;

	$Xdims  = array( 0,  1,  1,  1);
	$Ydims  = array( 1,  1,  0, -1);

    if ($x == 10 && $y == 10) $zu = 2;
    else $zu = 1;
	for ($i = 0; $i < 4; $i++)
    {
    	$xx = $Xdims[$i]; $yy = $Ydims[$i];

        // Part 1
		$k = 1; $p = 1; $n = 1; $l = 0; $m = 0;
		while ($x-$p*$xx >= 0 && $y-$p*$yy >= 0 && $x-$p*$xx < 20 && $y-$p*$yy <20 && $f [$x-$p*$xx][$y-$p*$yy] == 'o') $p++;
		$n += ($p-1); while ($x-$p*$xx >= 0 && $y-$p*$yy >= 0 && $x-$p*$xx < 20 && $y-$p*$yy <20 && ($f [$x-$p*$xx][$y-$p*$yy] == ' ' || $f [$x-$p*$xx][$y-$p*$yy] == 'o')) {$p++; $l = 1; }
        $k += ($p-1); $p = 1;
		while ($x+$p*$xx >= 0 && $y+$p*$yy >= 0 && $x+$p*$xx < 20 && $y+$p*$yy <20 && $f [$x+$p*$xx][$y+$p*$yy] == 'o') $p++;
		$n += ($p-1); while ($x+$p*$xx >= 0 && $y+$p*$yy >= 0 && $x+$p*$xx < 20 && $y+$p*$yy <20 && ($f [$x+$p*$xx][$y+$p*$yy] == ' ' || $f [$x+$p*$xx][$y+$p*$yy] == 'o')) {$p++; $m = 1; }
		$k += ($p-1); if ($m) $l++;
        if ($k >= 5)
        {
            if ($n >= 5) $zu += 1073741824;
            else if ($n >= 4) $zu += 8192*$l;
            else if ($n >= 3) $zu += 128*$l;
            else if ($n >= 2) $zu += 32*$l;
            else if ($n >= 1) $zu += 4*$l;
        }

		// Part 2
		$k = 1; $p = 1; $n = 1; $l = 0; $m = 0;
		while ($x-$p*$xx >= 0 && $y-$p*$yy >= 0 && $x-$p*$xx < 20 && $y-$p*$yy <20 && $f [$x-$p*$xx][$y-$p*$yy] == 'x') $p++;
		$n += ($p-1); while ($x-$p*$xx >= 0 && $y-$p*$yy >= 0 && $x-$p*$xx < 20 && $y-$p*$yy <20 && ($f [$x-$p*$xx][$y-$p*$yy] == ' ' || $f [$x-$p*$xx][$y-$p*$yy] == 'x')) {$p++; $l = 1; }
        $k += ($p-1); $p = 1;
		while ($x+$p*$xx >= 0 && $y+$p*$yy >= 0 && $x+$p*$xx < 20 && $y+$p*$yy <20 && $f [$x+$p*$xx][$y+$p*$yy] == 'x') $p++;
		$n += ($p-1); while ($x+$p*$xx >= 0 && $y+$p*$yy >= 0 && $x+$p*$xx < 20 && $y+$p*$yy <20 && ($f [$x+$p*$xx][$y+$p*$yy] == ' ' || $f [$x+$p*$xx][$y+$p*$yy] == 'x')) {$p++; $m = 1; }
		$k += ($p-1); if ($m) $l++;
        if ($k >= 5)
        {
            if ($n >= 5) $zu += 65536*8;
            else if ($n >= 4) $zu += 1024*$l;
            else if ($n >= 3) $zu += 256*$l;
            else if ($n >= 2) $zu += 4*$l;
            else if ($n >= 1) $zu += 1*$l;
        }
    }

    return $zu;
}

function aiTurn( $arr )
{
	$m = 0;
	$x = 0; $y = 0;
    for ($i = 0; $i < 20; $i++)
        for ($j = 0; $j < 20; $j++)
        {
            $k = Check ($i, $j, $arr);
            if ($k > $m || ($k == $m && mt_rand(1,2)==1)) { $m = $k; $x = $i; $y = $j; }
        }
    return $x * 20 + $y;
}

if( isset( $_GET['id'] ) )
{
    f_MQuery( "LOCK TABLE ox WRITE" );

    $res = f_MQuery( "SELECT * FROM ox WHERE p1=$pid OR p2=$pid" );
    $arr = f_MFetch( $res );
    if( !$arr ) die( );

    $f = $arr['f'];
    $p1 = $arr['p1'];
    $p2=  $arr['p2'];
    $turns = $arr['turn'];
    $money = $arr['winnings'];
    $me_x = false;
    if( $pid == $arr['p1'] ) { $me_x = true; $opp = $arr['p2']; }
    else $opp = $arr['p1'];


	$id = (int)$_GET['id'];
    if( !$arr['won'] && $f[$id] == ' ' && ( $me_x && $arr['turn'] % 2 == 0 || !$me_x && $arr['turn'] % 2 == 1 ) )
    {
    	$f[$id] = ( $me_x ) ? 'x' : 'o';
    	f_MQuery( "UPDATE ox SET ltm=".time( ).", f='$f', turn = turn + 1 WHERE p1=$pid OR p2=$pid" );

    	$arr = getField( $f );
    	$val = checkWinner( $arr );
    	if( $val != 0 ) f_MQuery( "UPDATE ox SET won = $val WHERE p1=$pid OR p2=$pid" );
    	if( $val == 2 && $p2 != 0 ) storeGame( 3, $p1, $p2, $money, $turns >= 20 );
    	if( $val == 1 && $p2 != 0 ) storeGame( 3, $p2, $p1, $money, $turns >= 20 );
    	if( $val == 5 && $p2 != 0 ) storeDraw( 3, $p2, $p1, $money );

    	else if( $p2 == 0 )
    	{
    		$aid = aiTurn( $arr );
    		//echo "alert( $aid );";
    		$f[$aid] = 'o';
	    	f_MQuery( "UPDATE ox SET f='$f', turn = turn + 1 WHERE p1=$pid OR p2=$pid" );
	    	$arr = getField( $f );
    		$val = checkWinner( $arr );
    		if( $val != 0 ) f_MQuery( "UPDATE ox SET won = $val WHERE p1=$pid OR p2=$pid" );
    	}
    }
	f_MQuery( "UNLOCK TABLES" );
}

refr( $pid );

?>
