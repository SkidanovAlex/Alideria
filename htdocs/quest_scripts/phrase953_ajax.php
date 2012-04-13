<?

header("Content-type: text/html; charset=windows-1251");

include_once( "../no_cache.php" );
include_once( "../functions.php" );
include_once( "../player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$res = f_MQuery( "SELECT talk_id FROM player_talks WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( !$arr || $arr[0] != 953 && $arr[0] != 959 && $arr[0] != 960 ) die( );

$raw = $HTTP_RAW_POST_DATA;
$command = explode("|", $raw, 21);

if (count($command) != 21) die("alert('error1 $raw');");

$xs = array();
$ys = array();
$ss = array();

for ($i = 0; $i < 21; ++ $i)
{
    if ($i % 3 == 0) $xs[] = (int)$command[$i];
    else if ($i % 3 == 1) $ys[] = (int)$command[$i];
    else $ss[] = (int)$command[$i];
}

$mx = 1000000000; $my = 1000000000;

for ($i = 0; $i < 7; ++ $i)
{
    $mx = min($mx, $xs[$i]);
    $my = min($my, $ys[$i]);
}
for ($i = 0; $i < 7; ++ $i)
{
    $xs[$i] -= $mx;
    $ys[$i] -= $my;
}

function isEzh($v01, $v12, $vh, $v1)
{
    global $xs, $ys, $ss;
    // vh -- на какой высоте второй большой
    // v1, v2 -- кака€ плоска€ идет раньше
    $id = 0; if ($v01) $id = 1;
    if ($xs[$id] != 0 || $ys[$id] != 0 || $ss[$id] != 1) return false;
    $id = 1; if ($v01) $id = 0;
    if ($xs[$id] != 0 || $ys[$id] != 112 + $vh * 56 || $ss[$id] != 1) return false;
    if ($xs[6] != 0 || $ys[6] != 112 + $vh * 56 || $ss[6] != 2) return false;
    
    $h = array();
    for ($i = 0; $i < 4; ++ $i)
    {
        if ($i != $vh && $i != $vh + 1) $h[] = $i;
    }
    $h1 = $h[0]; $h2 = $h[1];
    if ($v1)  { $h1 = $h[1]; $h2 = $h[0]; }

    $id = 2; if ($v12) $id = 3;
    if ($xs[$id] != 56 || $ys[$id] != 112 + $h1 * 56 || $ss[$id] != 1) return false;
    if ($xs[5] != 0 || $ys[5] != 112 + $h1 * 56 || $ss[5] != 2) return false;

    $id = 3; if ($v12) $id = 2;
    if ($xs[$id] != 0 || $ys[$id] != 112 + $h2 * 56 || $ss[$id] != 3) return false;
    if ($xs[4] != 0 || $ys[4] != 56 + $h2 * 56 || $ss[4] != 8) return false;

    return true;
}

function isHare()
{
    global $xs, $ys, $ss;
    if ($xs[0] != 0 || $ys[0] != 168 || $ss[0] != 3) return false;
    if ($xs[1] != 0 || $ys[1] != 56 || $ss[1] != 7) return false;
    if ($xs[2] != 112 || $ys[2] != 112 || $ss[2] != 2) return false;
    if ($xs[3] != 80 || $ys[3] != 200 || $ss[3] != 2) return false;
    if ($xs[4] != 0 || $ys[4] != 200 || $ss[4] != 7) return false;
    if ($xs[5] != 112 || $ys[5] != 56 || $ss[5] != 2) return false;
    if ($xs[6] != 0 || $ys[6] != 0 || $ss[6] != 2) return false;
    return true;
}

function isWolf()
{
    global $xs, $ys, $ss;
    if ($xs[0] != 152 || $ys[0] != 56 || $ss[0] != 5) return false;
    if ($xs[1] != 40 || $ys[1] != 56 || $ss[1] != 5) return false;
    if ($xs[2] != 40 || $ys[2] != 0 || $ss[2] != 7) return false;
    if ($xs[3] != 16 || $ys[3] != 0 || $ss[3] != 2) return false;
    if ($xs[4] != 152 || $ys[4] != 56 || $ss[4] != 1) return false;
    if ($xs[5] != 0 || $ys[5] != 56 || $ss[5] != 1) return false;
    if ($xs[6] != 264 || $ys[6] != 56 || $ss[6] != 2) return false;
    return true;
}

$ok = false;
if ($arr[0] == 960)
{
/*    $st = '';
    for ($i = 0; $i < 7; ++ $i)
    {
        $st .= "if (\$xs[$i] != {$xs[$i]} || \$ys[$i] != {$ys[$i]} || \$ss[$i] != {$ss[$i]}) return false;\\n";
    }
    echo "alert('$st');";*/
    for ($i = 0; $i < 2; ++ $i)
    {
        $t = $xs[0]; $xs[0] = $xs[1]; $xs[1] = $t;
        $t = $ys[0]; $ys[0] = $ys[1]; $ys[1] = $t;
        $t = $ss[0]; $ss[0] = $ss[1]; $ss[1] = $t;
        for ($j = 0; $j < 2; ++ $j)
        {
            $t = $xs[2]; $xs[2] = $xs[3]; $xs[3] = $t;
            $t = $ys[2]; $ys[2] = $ys[3]; $ys[3] = $t;
            $t = $ss[2]; $ss[2] = $ss[3]; $ss[3] = $t;
            if (isWolf()) $ok = true;
        }
    }
}
if ($arr[0] == 959)
{
    for ($i = 0; $i < 2; ++ $i)
    {
        $t = $xs[0]; $xs[0] = $xs[1]; $xs[1] = $t;
        $t = $ys[0]; $ys[0] = $ys[1]; $ys[1] = $t;
        $t = $ss[0]; $ss[0] = $ss[1]; $ss[1] = $t;
        for ($j = 0; $j < 2; ++ $j)
        {
            $t = $xs[2]; $xs[2] = $xs[3]; $xs[3] = $t;
            $t = $ys[2]; $ys[2] = $ys[3]; $ys[3] = $t;
            $t = $ss[2]; $ss[2] = $ss[3]; $ss[3] = $t;
            if (isHare()) $ok = true;
        }
    }
}
if ($arr[0] == 953) // ezhik
{
    for ($i = 0; $i < 2; ++ $i)
        for ($j = 0; $j < 2; ++ $j)
            for ($k = 0; $k < 3; ++ $k)
                for ($z = 0; $z < 2; ++ $z)
                    if (isEzh($i == 1, $j == 1, $k, $z == 1)) { $ok = true; break; }
}

if ($ok )
{
    echo "done = true;";
    $player->SetTrigger(13106);
    $pid = 2047;
    if ($arr[0] == 953) $pid = 2039;
    if ($arr[0] == 959) $pid = 2043;
    echo "_('tang_fv').innerHTML = '“анграм собран! <a href=\"game.php?phrase=$pid\">ƒальше!</a>';";
}

?>

