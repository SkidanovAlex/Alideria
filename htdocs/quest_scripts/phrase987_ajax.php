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
if( !$arr || $arr[0] != 987 ) die( );

$arr = explode(",", $HTTP_RAW_POST_DATA, 100);
if (count($arr) != 81) die();

f_MQuery("LOCK TABLE player_mines WRITE");
$s = f_MValue("SELECT f FROM player_mines WHERE player_id={$player->player_id}");
$j = 0;
for ($i = 3; $i < strlen($s); ++ $i) if ($s[$i] >= '0' && $s[$i] <= '9')
{
    $v = (int)$arr[$j];
    if ($v < 0 || $v > 9) die("alert($v);");
    $s[$i] = chr(ord('0') + $v);
    ++ $j;
}
f_MQuery("UPDATE player_mines SET f = '$s' WHERE player_id={$player->player_id}");
f_MQuery("UNLOCK TABLES");

for ($i = 0; $i < 81; ++ $i) if ((int)$arr[$i] == 0) die("");

$player->SetTrigger(13208);
echo "solved();";

?>

