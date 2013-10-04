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
    if( !$arr || $arr[0] != 1789 ) die( );

    $x = (int)($HTTP_GET_VARS['x']);
    $y = (int)($_GET['y']);

    function inside($x, $y) {
        if ($x < 0 || $y < 0 || $x >= 6 || $y >= 6) return false;
        return true;
    }
    if (!inside($x, $y)) die();

    $dx = array(-1, 0, 1, 0);
    $dy = array(0, -1, 0, 1);  


    $res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
    $arr = f_MFetch( $res );

    if (!$arr || $arr['lost']) die();

    $st = $arr['f'];
    $had = false;
    $has = false;
    for ($i = 0; $i < 36; ++ $i) if ($st[$i] == '?') { $st[$i] = '*'; $had = true; }
    for ($i = 0; $i < 36; ++ $i) if ($st[$i] == 'X') { $had = true; }
    if ($st[$x * 6 + $y] == 'x' || $st[$x * 6 + $y] == 'X') {
        $st[$x * 6 + $y] = '!';
        f_MQuery("UPDATE player_mines SET lost=1 WHERE player_id={$player->player_id}");
        $arr['lost'] = 1;
        $player->SetTrigger(285);
        $hint = "<b><font color=darkgreen>Вы нашли одуванчик!!!</font></b>";
    }
    else if ($st[$x * 6 + $y] == '.') die();
    else {
        for ($dir = 0; $dir < 4; ++ $dir) {
            $xx = $x + $dx[$dir];
            $yy = $y + $dy[$dir];
            if (inside($xx, $yy) && ($st[$xx * 6 + $yy] == 'x' || $st[$xx * 6 + $yy] == 'X')) {
                $has = true;
                break;
            }
        }

        $hint = $has ? "<b><font color=green>Тут одуванчика нет, но он в одной из четырех соседних ячеек.</font></a>" : "<b><font color=darkred>Одуванчика нет ни в этой, ни в одной из четырех соседних ячеек.</font></b>";
        
        $st[$x * 6 + $y] = '.';
        for ($dir = 0; $dir < 4; ++ $dir) {
            $xx = $x + $dx[$dir];
            $yy = $y + $dy[$dir];
            if (inside($xx, $yy) && ($st[$xx * 6 + $yy] == ' ' || $st[$xx * 6 + $yy] == '*')) {
                $st[$xx * 6 + $yy] = (($has && (!$had || $st[$xx * 6 + $yy] == '*')) ? '?' : '-');
            }
            if (inside($xx, $yy) && $st[$xx * 6 + $yy] == 'x') {
                $st[$xx * 6 + $yy] = 'X';
            }
        }
    }
    if ($has) {
        for ($i = 0; $i < 36; ++ $i) if ($st[$i] == '*') $st[$i] = '-';
    }
    else {
        for ($i = 0; $i < 36; ++ $i) if ($st[$i] == '*') $st[$i] = '?';
    }

    f_MQuery("UPDATE player_mines SET f='$st' WHERE player_id={$player->player_id}");

    $num = 0;
    for ($i = 0; $i < 36; ++ $i) if ($st[$i] == '.') ++ $num;

    if ($num >= 4) {
        f_MQuery("UPDATE player_mines SET lost=2 WHERE player_id={$player->player_id}");
        $arr['lost'] = 2;
        for ($i = 0; $i < 36; ++ $i) if ($st[$i] == 'x' || $st[$i] == 'X') $st[$i] = '!';
        f_MQuery("UPDATE player_mines SET f='$st' WHERE player_id={$player->player_id}");
    }

    for ($i = 0; $i < 36; ++ $i) if ($st[$i] == 'x') $st[$i] = ' ';
    for ($i = 0; $i < 36; ++ $i) if ($st[$i] == 'X') $st[$i] = '?';
    echo "field = [";
    for ($i = 0; $i < 36; ++ $i) {
        if ($i) echo ',';
        echo "'{$st[$i]}'";
    }
    echo "];\n";
    echo "_('hint').innerHTML = '$hint';";
    echo "redraw({$arr[lost]});";

    die();
}

?>
<b>Энт:</b> Ты в саду на зачарованной поляне. Как видишь, он разделен на ячейки. Только на одной из них есть одуванчики. Ты можешь выбрать четыре ячейки. Если на выбранной ячейке есть одучанчик, ты его сразу получишь. Иначе, если его нет, я скажу тебе, есть ли одуванчики в одной из четырех соседних с ней ячеек.<br>

<script src='js/skin2.js'></script>
<table><tr><td vAlign=top>
<div id='here'>&nbsp;</div>
</td><td vAlign=top>
<br>
<div id='hint'>&nbsp;</div>
<div id='proceed'>&nbsp;</div>
</td></tr></table>

<script>
<?


f_MQuery( "LOCK TABLE player_mines WRITE" );

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

if( !$arr )
{
    $st = "";
    for ($i = 0; $i < 36; ++ $i) $st .= " ";
    $st[mt_rand(0, 35)] = 'x';

	f_MQuery( "INSERT INTO player_mines( player_id, f ) VALUES ( {$player->player_id}, '$st' )" );
}

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

f_MQuery( "UNLOCK TABLES" );

$st = $arr['f'];
echo "//$st\n";
for ($i = 0; $i < 36; ++ $i) if ($st[$i] == 'x') $st[$i] = ' ';
for ($i = 0; $i < 36; ++ $i) if ($st[$i] == 'X') $st[$i] = '?';
echo "var field = [";
for ($i = 0; $i < 36; ++ $i) {
    if ($i) echo ',';
    echo "'{$st[$i]}'";
}
echo "];\n";

?>

var doit = function(x, y) {
    query("quest_scripts/phrase1789_oduvan.php?x=" + x + "&y=" + y, '');
}

var redraw = function(v) {
    if (v == 2) _('proceed').innerHTML = '<li> <a href="game.php?phrase=2554">*Уйти*</a>';
    if (v == 1) _('proceed').innerHTML = '<li> <a href="game.php?phrase=2555">*Дальше*</a>';
    if (v == 2) _('hint').innerHTML = "<b><font color=red>Вы истратили все попытки, но не нашли одуванчик</font></b>";
    if (v == 1) _('hint').innerHTML = "<b><font color=darkgreen>Вы нашли одуванчик!!!</font></b>";
    var st = '<table>';
    for (var i = 0; i < 6; ++ i) {
        st += "<tr>";
        for (var j = 0; j < 6; ++ j) {
            st += '<td>';
            st += rFLUl();
            var onc = 'doit(' + i + ', ' + j + ')';
            var addt = '';
            if (field[i * 6 + j] == ' ') {
            }
            else if (field[i * 6 + j] == '!') {
                addt = 'background-color: green';
            }
            else if (field[i * 6 + j] == '?') {
                addt = 'background-color: yellow';
            }
            else if (field[i * 6 + j] == '-') {
                addt = 'background-color: red';
            }
            else {
                addt = 'background-color: brown';
                onc = '';
            }
            st += '<div style="cursor:pointer;width:50px;height:50px;'+addt+'" onclick="' + onc + '">';
            st += '</div>';
            st += rFLL();
            st += '</td>';
        }
        st += "</tr>";
    }
    st += '</table>';

    _('here').innerHTML = st;
}
redraw(<?=$arr['lost']?>);
</script>
