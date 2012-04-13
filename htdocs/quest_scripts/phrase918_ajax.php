<?

header("Content-type: text/html; charset=windows-1251");

include_once( "../no_cache.php" );
include_once( "../functions.php" );
include_once( "../player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Íåâåðíûå íàñòðîéêè Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$res = f_MQuery( "SELECT talk_id FROM player_talks WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( !$arr || $arr[0] != 918 ) die( );

$res = f_MQuery("SELECT * FROM player_mines WHERE player_id={$player->player_id}");
$arr = f_MFetch($res);

if (!$arr) RaiseError("Bug!!!", "player_mines is not populated");

$mines = $arr['f'];
$lost = $arr['lost'];
$step2 = substr($mines, 1, strlen($mines) - 2);
$step3 = explode("],[", $step2);

if (count($step3) != 80) echo "alert('".count($step3)."');";

$data = array();
for ($i = 0; $i < 80; ++ $i)
{
    $data[$i] = explode(",", $step3[$i]);
}

$raw = $HTTP_RAW_POST_DATA;
if ($raw === "0")
{
    // do nothing
}
else if (!$lost)
{
    $command = explode("|", $raw, 3);
    $id = (int)$command[0];
    $x = (int)$command[1];
    $y = (int)$command[2];

    for ($i = 0; $i < 80; ++ $i)
    {
        if ($data[$i][2] == $id)
        {
            $data[$i][0] = (int)$data[$i][0] + $x;
            $data[$i][1] = (int)$data[$i][1] + $y;
        }
    }
}

if (!$lost)
{
    $remap = array();
    for ($i = 0; $i < 80; ++ $i) $remap[$i] = $i;

    function getclr($a)
    {
        global $remap;
        if ($remap[$a] == $a) return $a;
        $remap[$a] = getclr($remap[$a]);
        return $remap[$a];
    }

    for ($i = 0; $i < 80; ++ $i) $data[$i][0] = (int)$data[$i][0];
    for ($i = 0; $i < 80; ++ $i) $data[$i][1] = (int)$data[$i][1];
    for ($i = 0; $i < 80; ++ $i) $data[$i][2] = (int)$data[$i][2];

    for ($i = 0; $i < 80; ++ $i)
        for ($j = 0; $j < 80; ++ $j)
        {
            if (getclr($data[$i][2]) != getclr($data[$j][2]))
            {
                if ((abs($data[$i][0] - $data[$j][0]) < 5) && abs($data[$i][1] - $data[$j][1]) < 5 && (abs($i - $j) == 1 || abs($i - $j)== 10))
                {
                    $remap[getclr($data[$i][2])] = getclr($data[$j][2]);
                    $data[$i][1] = $data[$j][1];
                    $data[$i][0] = $data[$j][0];
                }
            }
        }
    $last = -1;
    $ok = true;
    for ($i = 0; $i < 80; ++ $i)
    {
        $data[$i][2] = getclr($data[$i][2]);
        $data[$i][0] = $data[$data[$i][2]][0];
        $data[$i][1] = $data[$data[$i][2]][1];
        if ($last == -1) $last = $data[$i][2];
        else if ($data[$i][2] != $last) $ok = false;
    }

    if ($ok)
    {
        for ($i = 0; $i < 80; ++ $i)
        {
            $data[$i][0] = 0;
            $data[$i][1] = 0;
        }
        $player->SetTrigger(12210);
        $lost = 1;
    }
}

// DO IT HERE

$state = "";
for ($i = 0; $i < 80; ++ $i)
{
    if ($i) $state .= ",";
    $state .= "[{$data[$i][0]},{$data[$i][1]},{$data[$i][2]}]";
}

f_MQuery("UPDATE player_mines SET f='$state', lost='$lost' WHERE player_id={$player->player_id}");

echo "redraw([$state], $lost);";

?>

