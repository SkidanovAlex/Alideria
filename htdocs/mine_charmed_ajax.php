<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

if( $player->location != 0 || $player->depth != 50 ) die( );

$x1 = (int)$_GET['x1'];
$y1 = (int)$_GET['y1'];
$x2 = (int)$_GET['x2'];
$y2 = (int)$_GET['y2'];

if( abs( $x1 - $x2 ) + abs( $y1 - $y2 ) > 1 ) die( );

if( $y1 < 0 || $y1 >= 6 || $y2 < 0 || $y2 >= 6 || $x1 < 0 || $x1 >= 6 || $x2 < 0 || $x2 >= 6 ) RaiseError( "Попытка зачитить в зачарованной шахте", "$x1 $y1 $x2 $y2" );

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

if( !$arr ) die( );
$s = $arr['f'];

$id1 = $y1 * 6 + $x1;
$id2 = $y2 * 6 + $x2;

$ret = true;

if( $id1 != $id2 )
{
    if( $s[$id1] == ' ' || $s[$id2] == ' ' ) die( );

    $t = $s[$id1];
    $s[$id1] = $s[$id2];
    $s[$id2] = $t;
	$ret = false;
}

$f = array( );
$nf = array( );

$id = 0;
for( $i = 0; $i < 6; ++ $i )
{
	$f[$i] = array( );
	$nf[$i] = array( );
	for( $j = 0; $j < 6; ++ $j )
	{
		$f[$i][$j] = $s[$id];
		$nf[$i][$j] = $s[$id];
		++ $id;
	}
}

$needs = true;
while( $needs )
{
	$needs = false;
    for( $i = 0; $i < 6; ++ $i )
    {
    	for( $j = 0; $j < 6; ++ $j ) if( $f[$i][$j] != ' ' )
    	{
    		$k = 1;
    		$jj = $j + 1; while( $jj < 6 && $f[$i][$j] == $f[$i][$jj] ) { ++ $k; ++ $jj; }
       		$jj = $j - 1; while( $jj >= 0 && $f[$i][$j] == $f[$i][$jj] ) { ++ $k; -- $jj; }
       		if( $k >= 3 )
       		{
       			$nf[$i][$j] = ' ';
       			if( $f[$i][$j] == '0' )
       			{
       				$player->AddItems( 1 );
       				$player->AddToLogPost( 1, 1, 23 );
       			}
       			if( $f[$i][$j] == '4' )
       			{
       				$player->AddItems( 16 );
       				$player->AddToLogPost( 16, 1, 23 );
       			}
       			$ret = true;
       			$needs = true;
       			continue;
       		}
       		$k = 1;
    		$ii = $i + 1; while( $ii < 6 && $f[$i][$j] == $f[$ii][$j] ) { ++ $k; ++ $ii; }
       		$ii = $i - 1; while( $ii >= 0 && $f[$i][$j] == $f[$ii][$j] ) { ++ $k; -- $ii; }
    		if( $k >= 3 )
    		{
    			$nf[$i][$j] = ' ';
       			if( $f[$i][$j] == '0' )
       			{
       				$player->AddItems( 1 );
					$player->AddToLogPost( 1, 1, 23 );
       			}
       			if( $f[$i][$j] == '4' )
       			{
       				$player->AddItems( 16 );
       				$player->AddToLogPost( 16, 1, 23 );
       			}
    			$ret = true;
    			$needs = true;
    			continue;
    		}
    		$nf[$i][$j] = $nf[$i][$j];
    	} else $nf[$i][$j] = ' ';
    }

    for( $j = 0; $j < 6; ++ $j )
    {
    	for( $i = 5; $i >= 0; -- $i )
    	{
    		if( $nf[$i][$j] == ' ' )
    		{
    			for( $k = $i - 1; $k >= 0; -- $k )
    			{
    				if( $nf[$k][$j] != ' ' )
    				{
    					$nf[$i][$j] = $nf[$k][$j];
    					$nf[$k][$j] = ' ';
    					$needs = true;
    					break;
    				}
    			}
    		}
    	}
    }
    for( $i = 0; $i < 6; ++ $i )
    {
    	for( $j = 5; $j >= 0; -- $j )
    	{
    		if( $nf[$i][$j] == ' ' )
    		{
    			for( $k = $j - 1; $k >= 0; -- $k )
    			{
    				if( $nf[$i][$k] != ' ' )
    				{
    					$nf[$i][$j] = $nf[$i][$k];
    					$nf[$i][$k] = ' ';
    					$needs = true;
    					break;
    				}
    			}
    		}
    	}
    }

    if( $needs )
    {
    	for( $i = 0; $i < 6; ++ $i )
    		for( $j = 0; $j < 6; ++ $j )
    			$f[$i][$j] = $nf[$i][$j];
    }
}


if( !$ret ) ;
else
{       
    $s = '';
    for( $i = 0; $i < 6; ++ $i )
    	for( $j = 0; $j < 6; ++ $j )
    		$s .= $nf[$i][$j];
    f_MQuery( "UPDATE player_mines SET f='$s' WHERE player_id={$player->player_id}" );
}

?>

frz = false;
