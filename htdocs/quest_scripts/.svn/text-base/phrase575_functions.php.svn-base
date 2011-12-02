<?

// 0 - топовая
// затем идут сблдизи вдаль снизу вверх слева направо
// затем левая, затем две правых (141, 142 и 143)

function numToChr( $a )
{
	-- $a;
	if( $a < 10 ) return chr( ord( '0' ) + $a );
	return chr( ord( 'A' ) + $a - 10 );
}

function chrToNum( $a )
{
	$a = ord( $a );
	if( $a >= ord( '0' ) && $a <= ord( '9' ) ) return $a - ord( '0' ) + 1;
	return $a - ord( 'A' ) + 11;
}


$mh_lft = array( 0, 2, 1, 0, 0, 1, 2, 0,
			  -1, 3, 3, 3, 3, 3, 3, -1,
			  -1, -1, 4, 4, 4, 4, -1, -1,
			  -1, -1, -1, 5, 5, -1, -1, -1 );
$mh_coords = array( );
$mh_left = array( );
$mh_right = array( );
$mh_top = array( );
$mh_cur = array( );
for( $i = 0; $i < 8; ++ $i )
{
	$mh_cur[$i] = array( );
	for( $j = 0; $j < 12; ++ $j )
		$mh_cur[$i][$j] = -1;
}

$w = 26;
$h = 38;

$mh_left[0] = array( );
$mh_left[141] = array( );
$mh_left[142] = array( );
$mh_left[143] = array( );
$mh_right[0] = array( );
$mh_right[141] = array( );
$mh_right[142] = array( );
$mh_right[143] = array( );
			  
$id = 1;
for( $i = 3; $i >= 0; -- $i )
{
	for( $j = 7; $j >= 0; -- $j ) if( $mh_lft[$i * 8 + $j] != -1 )
	{
		$fst = true;
		for( $k = 0; $k < 12; ++ $k ) if( $k >= $mh_lft[$i * 8 + $j] && $k < 12 - $mh_lft[$i * 8 + $j] )
		{
			$mh_coords[$id] = array( $i, $j * $h, ( 1 + $k ) * $w );
			$mh_left[$id] = array( );
			$mh_right[$id] = array( );
			if( !$fst )
			{
				$mh_left[$id][] = $id - 1;
				$mh_right[$id - 1][] = $id;
			}
			$mh_top[$id] = $mh_cur[$j][$k];
			$mh_cur[$j][$k] = $id;
			
			// conrer cases
			if( $i == 3 ) $mh_top[$id] = 0;
			if( $i == 0 && ( $j == 3 || $j == 4 ) )
			{
				if( $k == 0 )
				{
					$mh_left[$id][] = 141;
					$mh_right[141][] = $id;
				}
				if( $k == 11 )
				{
					$mh_right[$id][] = 142;
					$mh_left[142][] = $id;
				}
			}
			
			$fst = false;
			++ $id;
		}
	}
}

$mh_right[142][] = 143;
$mh_left[143][] = 142;
$mh_coords[0] = array( 4, 3.5 * $h, 6.5 * $w );
$mh_coords[141] = array( 0, 3.5 * $h, 0 );
$mh_coords[142] = array( 0, 3.5 * $h, 13 * $w );
$mh_coords[143] = array( 0, 3.5 * $h, 14 * $w );
$mh_top[0] = -1;
$mh_top[141] = -1;
$mh_top[142] = -1;
$mh_top[143] = -1;

function mhIsVis( $i )
{
	global $data;
	global $mh_top;
	global $mh_right;
	global $mh_left;
	global $mh_coords;
	
	if( $i < 5 ) return true;

	if( $mh_top[$i] != -1 && $data[$mh_top[$i]] != '.' ) return false;
	return true;
}

function mhIsFree( $i )
{
	global $data;
	global $mh_top;
	global $mh_right;
	global $mh_left;
	global $mh_coords;

	if( $mh_top[$i] != -1 && $data[$mh_top[$i]] != '.' ) return false;
	$bl = 0;
	$br = 0;
	foreach( $mh_left[$i] as $v ) if( $data[$v] != '.' ) ++ $bl;
	foreach( $mh_right[$i] as $v ) if( $data[$v] != '.' ) ++ $br;
	if( $bl > 0 && $br > 0 ) return false;

	return true;
}

function mhGetElemHtml( $i )
{
	global $data;
	global $mh_top;
	global $mh_right;
	global $mh_left;
	global $mh_coords;
	
	$moo = '';
	if( $data[$i] == '.' ) return "";
	if( !mhIsVis( $i ) ) return "<img width=28px height=40 src=../images/misc/m/v.png>";
//	if( !mhIsVis( $i ) ) return "&nbsp;";
	if( mhIsFree( $i ) ) $moo = "onclick='mhclick({$i})'";
	$img = chrToNum( $data[$i] );
	return "<img $moo width=28px height=40 src=../images/misc/m/{$img}.png>";
}

function mhFinishCheck( )
{
	global $data;
	global $player;
	$cnt = 0;
	for( $i = 0; $i < 144; ++ $i ) if( $data[$i] != '.' ) ++ $cnt;
	if( $cnt <= 10 )
	{
		f_MQuery( "UPDATE player_talks SET talk_id=576 WHERE player_id={$player->player_id}" );
		echo "location.href='game.php';";
	}
}

?>
