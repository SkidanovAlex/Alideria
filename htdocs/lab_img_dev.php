<?

include( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "lab.php" );
include_once( 'items.php' );
include_once( "lab_functions.php" );

Header("Content-Type: image/jpeg");

f_MConnect( );


if( !check_cookie( ) )
	die( );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

//$player = new Player(6825);

$lab_id = isLabLoc( $player->location, $player->depth );
if( $lab_id == -1 )
	die( );
$lab_id = 1;

$dxs = Array( -1, 0, 1, 0 );
$dys = Array( 0, -1, 0, 1 );

f_MQuery( "LOCK TABLES lab WRITE, player_labs WRITE" );
$res = f_MQuery( "SELECT cell_id, dir FROM player_labs WHERE player_id={$player->player_id} AND lab_id={$lab_id}" );
$arr = f_MFetch( $res );
if( !$arr )
{
    enterLab($lab_id);
	$dir = 0;
}
else $dir = $arr[1];
$res = f_MQuery( "SELECT x, y, z, dir FROM lab WHERE lab_id=$lab_id AND cell_id=$arr[0]" );
$arr = f_MFetch( $res );
f_MQuery( "UNLOCK TABLES" );

$cx = $arr[0];
$cy = $arr[1];
$cz = $arr[2];

$arr = Array( Array( ), Array( ), Array( ) );
$dirs = Array( Array( ), Array( ), Array( ) );
$items = Array( Array( ), Array( ), Array( ) );
$mobs = Array( Array( ), Array( ), Array( ) );

for( $i = 0; $i < 3; ++ $i )
{
	$moo = ( $i + 1 );
	if( $i == 2 ) $moo = 4;
	for( $j = - $moo; $j <= $moo; ++ $j )
	{
		$x = $cx + $i * $dxs[$dir] + $j * $dys[$dir];
		$y = $cy + $i * $dys[$dir] - $j * $dxs[$dir];
		$result = f_MQuery( "SELECT tex, cell_id, dir FROM lab WHERE lab_id=$lab_id AND x=$x AND y=$y AND z=$cz" );
		$line = f_MFetch( $result );
		if( !$line )
        {
            $arr[$i][$j + $moo] = 1; 
            $dirs[$i][$j + $moo] = 0;
        }
		else
        {
            $arr[$i][$j + $moo] = $line[0];
            $dirs[$i][$j + $moo] = $line[2];
        }

		$items[$i][$j + $moo] = Array( );
		$mobs[$i][$j + $moo] = Array( );
		if( $line )
		{
			$cell_id = $line['cell_id'];
    		$result = f_MQuery( "SELECT items.* FROM items, lab_items WHERE lab_id=$lab_id AND cell_id=$cell_id AND items.item_id=lab_items.item_id" );
    		while( $line = f_MFetch( $result ) )
    		{
    			$items[$i][$j + $moo][] = itemImage( $line );
    		}
/*    		$result = f_MQuery( "SELECT * FROM lab_mobs WHERE lab_id=$lab_id AND cell_id=$cell_id" );
    		while( $line = f_MFetch( $result ) )
    		{
    			$mobs[$i][$j + $moo][] = $line['img'];
    		}
*/    		
            // важно добавить НПЦ до монстров, чтобы НПЦ всегда показывался закытым монстром
            $result = f_MQuery("SELECT img, npc_id FROM lab_quest_npcs WHERE player_id={$player->player_id} AND lab_id={$lab_id} AND cell_id={$cell_id}");
    		while( $line = f_MFetch( $result ) )
    		{
                include_once( "phrase.php" );
                $condition_id = f_MValue("SELECT condition_id FROM npcs WHERE npc_id=$line[1]");

                if( $condition_id == -1 || allow_phrase( $condition_id, false ) )
                {
                    $mobs[$i][$j + $moo][] = $line['img'];
                }
    		}
    		$result = f_MQuery( "SELECT img FROM lab_mobs WHERE lab_id=$lab_id AND cell_id=$cell_id UNION ALL SELECT img FROM lab_quest_monsters WHERE player_id={$player->player_id} AND lab_id={$lab_id} AND cell_id={$cell_id}" );
    		while( $line = f_MFetch( $result ) )
    		{
    			$mobs[$i][$j + $moo][] = $line['img'];
    		}
    		
		}
	}
}

$bg = imagecreatefrompng( "images/lab/bg.png" );
imagealphablending( $bg, true );
$im = imagecreatetruecolor( 384, 256 );
imagealphablending( $im, true );
imagecopy( $im, $bg, 0, 0, 0, 0, 384, 256 );

function drawItems( $i, $j )
{
	global $mobs;
	global $items;
	global $im;

	$moo = $i + 1;
	if( $i == 2 ) $moo ++;
	$id = 0;

	global $player;
    $total = count($mobs[$i][$j + $moo]);
	foreach( $mobs[$i][$j + $moo] as $src )
	{
        $adjustedId = $id;
        if ($total >= 4)
        {
            if ($id == 1) $adjustedId = 4;
            else if ($id == 4) $adjustedId = 1;
        }
        if ($total >= 5)
        {
            if ($id == 2) $adjustedId = 5;
            else if ($id == 5) $adjustedId = 2;
        }
		if( $adjustedId == 0 ) { $dx = 32; $dy = 32; }
		else if( $adjustedId == 1 ) { $dx = 16; $dy = 8; }
		else if( $adjustedId == 2 ) { $dx = 48; $dy = 8; }
		else if( $adjustedId == 3 ) { $dx = 16; $dy = 48; }
		else if( $adjustedId == 4 ) { $dx = 48; $dy = 48; }
		else break;
		++ $id;

		$dst = $i * 64 + $dy;
		$w = pow( 2, 8 - ( $i * 64 + $dy ) / 64 );
		$l = 64 + 128 + $j * $w - 0.5 * $w + ( ( $dx - 50 * 64 / 256 ) * $w ) / 64;
		$t = 128 - $w / 2.5;

    	$img = imagecreatefrompng( 'images/avatars/'.$src );
    	imagecopyresampled( $im, $img, $l, $t, 0, 0, $w * 100 / 225, $w, imagesx( $img ), imagesy( $img ) );
	}

	foreach( $items[$i][$j + $moo] as $src )
	{
		if( $id == 0 ) { $dx = 32; $dy = 32; }
		else if( $id == 1 ) { $dx = 16; $dy = 16; }
		else if( $id == 2 ) { $dx = 48; $dy = 16; }
		else if( $id == 3 ) { $dx = 16; $dy = 48; }
		else if( $id == 4 ) { $dx = 48; $dy = 48; }
		else break;
		++ $id;

		$dst = $i * 64 + $dy;
		$w = pow( 2, 8 - ( $i * 64 + $dy ) / 64 );
		$l = 64 + 128 + $j * $w - 0.5 * $w + ( ( $dx - 32/1.8 ) * $w ) / 64;
		$t = 128;

    	$img = imagecreatefromgif( 'images/items/'.$src );
    	imagecopyresampled( $im, $img, $l, $t, 0, 0, $w / 1.8, $w / 1.8, imagesx( $img ), imagesy( $img ) );
	}
}

function drawV($l0, $l1, $l2, $l3, $a1, $a2, $b1, $b2, $w1, $w2, $dir)
{
    // $l0, $l4 == откуда докуда должна быть стена
    // $l1, $l2 -- откуда докуда мы ее рисуем сейчас
    // $a1, $a2 -- одна высота в l1 и l2
    // $b1, $b2 -- другая высота в l1, и l2
    // $w1, $w2 -- высота стены в l1 и l2
    // $dir -- куда переход
    global $im, $tex;

    if ($dir == 1) 
    {
        $a1 = 255 - $a1;
        $a2 = 255 - $a2;
        $b1 = 255 - $b1;
        $b2 = 255 - $b2;
    }

    for ($i = $l1; $i <= $l2; ++ $i)
    {
        $p = ($i - $l1) / ($l2 - $l1);
        $p2 = 1 - $p;
        $h1 = ceil($a1 * $p2 + $a2 * $p);
        $h2 = ceil($b1 * $p2 + $b2 * $p);
        if ($h1 > $h2) { $q = $h1; $h1 = $h2; $h2 = $q; }
        $ph = $h2 - $h1;
        $z = ($w1 * $p2 + $w2 * $p); // height of the wall at this point
        $rz = ceil(64 * ($ph / $z)); // height that needs to be copied
        $rx = ceil(63 * ($i - $l0) / ($l3 - $l0)); // column to be copied
        $sz = 0; // с какого пикселя по вертикали брать с картинки
        if ($dir == -1) $sz = 63 - $rz;
        imagecopyresampled( $im, $tex[1], $i, $h1, $rx, $sz, 1, $ph, 1, $rz );
        if( $z < 64 )
        {
            $fog = ( $z - 32 ) / 32.0; $fog = $fog * $fog;
            $black = imagecolorallocatealpha  ( $im, 0, 0, 0, min( 127, $fog * 127.0 ) );
            imageline( $im, $i, $h1, $i, $h2, $black );
        }
    }
}

$ws = Array( 256, 128, 64, 32 );
$ls = Array( 0, - 64, - 96, -112 );
$ts = Array( 0, 64, 96, 112 );
for( $i = 2; $i >= 0; -- $i )
{
	$moo = ( $i + 1 );
	if( $i == 2 ) $moo = 4;
	$w = $ws[$i];
	for( $j = 0; $j < $moo; ++ $j ) if( $arr[$i][$j] != 0 )
	{
		$l = $ls[$i] + $w * $j - $w;
		imagecopyresampled( $im, $tex[$arr[$i][$j]], 64 + $l, $ts[$i], 0, 0, $w, $w, 64, 64 );
		$ol = -1;
		for( $k = 0; $k < 128; ++ $k )
		{
			$dst = $i * 64 + $k / 2.0;
			$tt = ( $w / 4 ) * ( $k ) / 128.0;
			$h = $w - $tt * 2;
			$ww = pow( 2, 8 - ( $i * 64 + $k / 2 ) / 64 );
			$ll = round( 128 + ( $j - $moo + 0.5 ) * $ww );
			if( $ol == $ll ) continue; $ol = $ll;
			imagecopyresampled( $im, $tex[$arr[$i][$j]], 64 + $ll, $ts[$i] + ceil( $tt ), floor( $k / 2 ), 0, 1, $h, 1, 64 );
			if( $dst > 128 )
			{
				$fog = ( 192 - $dst ) / 64.0; $fog = $fog * $fog;
				$black = imagecolorallocatealpha  ( $im, 0, 0, 0, min( 127, $fog * 127.0 ) );
				imageline( $im, 64 + $ll, $ts[$i] + ceil( $tt ), 64 + $ll, $ts[$i] + ceil( $tt ) + $h, $black );
			}
		}
	}
	else
    {
        if ($dirs[$i][$j] != 0)
        {
		    $l1 = 64 + $ls[$i] + $w * $j - $w;
            $l2 = 64 + $ls[$i + 1] + $w / 2 * ($j + (1 << $i)) - $w / 2;
            $l3 = $l1 + $w;
            $l4 = $l2 + $w / 2;

            $h1 = $ts[$i];
            $h2 = $ts[$i + 1];

            drawV($l1, $l1, $l2, $l2, $h1, $h1, $h1, $h2, $ws[$i], $ws[$i + 1], $dirs[$i][$j]);
            drawV($l2, $l2, $l3, $l4, $h1, $h1, $h2, $h2, $ws[$i + 1], $ws[$i + 1], $dirs[$i][$j]);
            drawV($l2, $l3, $l4, $l4, $h1, $h2, $h2, $h2, $ws[$i + 1], $ws[$i + 1], $dirs[$i][$j]);
        }
        drawItems( $i, $j - $moo );
    }
	for( $j = $moo * 2; $j > $moo; -- $j ) if( $arr[$i][$j] != 0 )
	{
		$l = $ls[$i] + $w * $j - $w;
		imagecopyresampled( $im, $tex[$arr[$i][$j]], 64 + $l, $ts[$i], 0, 0, $w, $w, 64, 64 );
		$ol = -1;
		for( $k = 0; $k < 128; ++ $k )
		{
			$dst = $i * 64 + $k / 2.0;
			$tt = ( $w / 4 ) * ( $k ) / 128.0;
			$h = $w - $tt * 2;
			$ww = pow( 2, 8 - ( $i * 64 + $k / 2 ) / 64 );
			$ll = round( 128 + ( $j - $moo - 0.5 ) * $ww );
			if( $ol == $ll ) continue; $ol = $ll;
			imagecopyresampled( $im, $tex[$arr[$i][$j]], 64 + $ll, $ts[$i] + ceil( $tt ), floor( $k / 2 ), 0, 1, $h, 1, 64 );
			if( $dst > 128 )
			{
				$fog = ( 192 - $dst ) / 64.0; $fog = $fog * $fog;
				$black = imagecolorallocatealpha  ( $im, 0, 0, 0, min( 127, $fog * 127.0 ) );
				imageline( $im, 64 + $ll, $ts[$i] + ceil( $tt ), 64 + $ll, $ts[$i] + ceil( $tt ) + $h, $black );
			}
		}
	}
    else
    {
        if ($dirs[$i][$j] != 0)
        {
		    $l2 = 64 + $ls[$i] + $w * $j - $w;
            $l1 = 64 + $ls[$i + 1] + $w / 2 * ($j + (1 << $i)) - $w / 2;
            $l4 = $l2 + $w;
            $l3 = $l1 + $w / 2;

            $h1 = $ts[$i];
            $h2 = $ts[$i + 1];

            drawV($l1, $l1, $l2, $l3, $h2, $h1, $h2, $h2, $ws[$i + 1], $ws[$i + 1], $dirs[$i][$j]);
            drawV($l1, $l2, $l3, $l3, $h1, $h1, $h2, $h2, $ws[$i + 1], $ws[$i + 1], $dirs[$i][$j]);
            drawV($l3, $l3, $l4, $l4, $h1, $h1, $h2, $h1, $ws[$i + 1], $ws[$i], $dirs[$i][$j]);
        }
        drawItems( $i, $j - $moo );
    }
	$j = $moo;
	if( $arr[$i][$j] != 0 )
	{
		$l = $ls[$i] + $w * $j - $w;
		imagecopyresampled( $im, $tex[$arr[$i][$j]], 64 + $l, $ts[$i], 0, 0, $w, $w, 64, 64 );
	}
    else
    {
        if ($dirs[$i][$j] != 0)
        {
		    $l1 = 64 + $ls[$i] + $w * $j - $w;
            $l2 = $l1 + $w / 4;
            $l3 = $l2 + $w / 2;
            $l4 = $l1 + $w;

            $h1 = $ts[$i];
            $h2 = $ts[$i + 1];

            drawV($l1, $l1, $l2, $l2, $h1, $h1, $h1, $h2, $ws[$i], $ws[$i + 1], $dirs[$i][$j]);
            drawV($l2, $l2, $l3, $l3, $h1, $h1, $h2, $h2, $ws[$i + 1], $ws[$i + 1], $dirs[$i][$j]);
            drawV($l3, $l3, $l4, $l4, $h1, $h1, $h2, $h1, $ws[$i + 1], $ws[$i], $dirs[$i][$j]);
        }

        drawItems( $i, 0 );
    }
}
//imagecopy( $im, $tex[3], 128 - 25 - 90, 128, 0, 0, 50, 50 );
//imagecopyresampled( $im, $tex[4], 128 - 12, 128, 0, 0, 25, 25, 50, 50 );
//imagecopyresampled( $im, $tex[3], 128 - 50, 128, 0, 0, 100, 100, 50, 50 );

ImageJpeg($im);
/**/
/**/
$im = imagecreate( 384, 256 );
imagealphablending( $im, true );
$black = imagecolorallocate(  $im, 0, 0, 0 );
imagefill( $im, 0, 0, $black );

$floor = imagecreatefrompng( 'images/lab/floor.png' );
$ceil = imagecreatefrompng( 'images/lab/ceil.png' );

$size = 64;
for( $y = 128 + 17; $y < 256; ++ $y )
{
	$dst = floor( 64 * ( 7 - log( $y - 128 ) / log( 2 ) ) );
	$w = ( ( $y - 128 ) * 2 );
	$l = 255 - $y;

	for( $x = 0; $x < 31; ++ $x )
	{                           
		imagecopyresampled( $im, $floor, 64 + $l + $w * ( $x - 14 ), $y, 0, $dst % 64, $w + 1, 1, 64, 1 );
	}
	if( $dst > $size * 2 )
	{
		$fog = ( $size * 3 - $dst ) / $size; $fog *= $fog;
		for( $j = 0; $j < 384; ++ $j )
		{
            $col = imagecolorat($im, $j, $y);
            $arcol = imagecolorsforindex($im, $col);

            imagesetpixel($im, $j, $y,
              imagecolorresolve($im, ceil( $arcol["red"] * $fog ),  ceil( $arcol["green"] * $fog ), ceil( $arcol["blue"] * $fog ) ) );
		}
	}

}

for( $y = 128 + 17; $y < 256; ++ $y )
{
	$dst = floor( 64 * ( 7 - log( $y - 128 ) / log( 2 ) ) );
	$w = ( ( $y - 128 ) * 2 );
	$l = 255 - $y;

	for( $x = 0; $x < 31; ++ $x )
	{                           
		imagecopyresampled( $im, $ceil, 64 + $l + $w * ( $x - 14 ), 255 - $y, 0, $dst % 64, $w + 1, 1, 64, 1 );
	}
	if( $dst > 128 )
	{
		$fog = ( 192 - $dst ) / 64.0; $fog *= $fog;
		for( $j = 0; $j < 384; ++ $j )
		{
            $col = imagecolorat($im, $j, 255 - $y);
            $arcol = imagecolorsforindex($im, $col);

            imagesetpixel($im, $j, 255 - $y,
              imagecolorresolve($im, ceil( $arcol["red"] * $fog ),  ceil( $arcol["green"] * $fog ), ceil( $arcol["blue"] * $fog ) ) );
		}
	}

}
imagepng( $im );


/**/

?>
