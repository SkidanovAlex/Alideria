<?

if( !$mid_php ) die( );

?>

<div id='proceed'><li><a href='javascript:if (confirm("Вы уверены, что хотите уйти? Собирать квадрат придется с начала.")) location.href="game.php?phrase=2101";'>Уйти и вернуться позже (придется собирать квадрат сначала)</a></div>

<script>

<?
$field = mt_rand(2, 5);

f_MQuery("LOCK TABLE player_mines WRITE");
$res = f_MValue("SELECT f FROM player_mines WHERE player_id={$player->player_id}");
if ($res)
{
    $arr = explode('|', $res);
    $field = (int)$arr[0];
}

if ($field == 1)
    $s = "[[6,0,0,0,2,0,4,0,0],[0,0,9,6,7,0,0,5,2],[5,3,0,1,8,4,6,0,0],[0,2,1,3,6,0,0,7,5],[3,8,0,0,0,2,0,4,0],[0,6,0,0,0,7,2,3,8],[0,9,0,0,3,0,7,6,0],[0,5,6,7,4,0,3,0,0],[0,1,0,2,9,0,0,8,0]];";
else if ($field == 2)
    $s = "[[8,0,0,9,3,0,0,2,4],[0,3,0,2,0,0,0,0,7],[0,2,4,0,0,0,0,0,3],[0,6,0,0,0,0,3,0,0],[0,5,9,0,0,0,0,0,0],[7,1,8,0,5,0,4,6,2],[0,0,6,4,0,0,0,0,8],[0,0,7,5,9,3,0,4,6],[0,4,0,8,2,6,1,7,0]];";
else if ($field == 3)
    $s =  "[[0,6,0,0,3,0,0,0,0],[0,5,2,7,1,0,9,0,0],[0,0,4,0,6,0,5,0,0],[0,0,0,0,0,0,0,0,0],[0,0,0,8,5,0,4,0,0],[0,4,0,0,7,6,0,9,0],[0,0,0,0,0,9,6,0,0],[9,2,6,0,0,5,0,0,7],[0,8,5,6,0,7,3,0,9]];";
else if ($field == 4)
    $s =  "[[0,0,1,4,7,0,0,0,0],[0,0,3,9,0,1,0,0,0],[0,8,7,5,0,6,2,4,1],[2,0,0,0,0,0,0,7,0],[1,9,0,0,0,7,0,0,0],[6,0,0,2,0,0,9,0,0],[7,0,0,0,2,0,0,0,0],[8,1,0,0,0,5,4,3,0],[3,4,0,0,0,0,7,0,0]];";
else if ($field == 5)
    $s =  "[[3,0,4,0,0,0,0,0,9],[1,0,0,0,7,8,0,0,0],[0,2,0,3,0,9,0,5,0],[4,3,0,0,8,1,0,0,6],[9,7,5,0,0,2,0,1,0],[0,1,8,7,0,3,5,0,0],[0,0,1,0,0,0,4,6,5],[0,0,7,0,5,0,0,0,0],[5,4,3,9,0,0,0,0,0]];";

if (!$res)
{
    f_MQuery("INSERT INTO player_mines (player_id, f, lost) VALUES ({$player->player_id}, '$field|$s', 0);");
}

f_MQuery("UNLOCK TABLES");
echo "var field = $s\n";

?>

document.write("<table cellspacing=0 cellpadding=0 style='border:1px solid black'>");
for (var i = 0; i < 9; ++ i)
{
    document.write("<tr>");
    for (var j = 0; j < 9; ++ j)
    {
        var add = '';
        if (i == 2 || i == 5)  add += 'border-bottom:1px solid black;';
        if (j == 2 || j == 5) add += 'border-right:1px solid black;';

        if (!field[i][j]) document.write("<td style='" + add + "'><div style='width:25px; height:25px; text-align:center; border:1px solid black;display:table-cell; vertical-align:middle; cursor:pointer;' id='sud" + i + "" + j + "' onclick='showmoo(" + i + "," + j + ")'>&nbsp;</div></td>");
        else document.write("<td style='" + add + "'><div style='width:25px; height:25px; text-align:center; border:1px solid black;display:table-cell; vertical-align:middle;color:#404040' id='sud" + i + "" + j + "'>&nbsp;</div></td>");
    }
    document.write("</tr>");
}
document.write("</table>");

document.write('<span id="moo" style="position:absolute;left:0px;top:0px;width:260px;">');
FLUl();

document.write("<table cellspacing=0 cellpadding=0><tr>");
for (var i = 0; i <= 9; ++ i)
{
    document.write("<td><div id='hru" + i + "' onclick='engage(" + i + ")' style='cursor:pointer;width:25px;height:25px;text-align:center;display:table-cell; vertical-align:middle'><b>");
    document.write(i ? i : 'x');
    document.write("</b></div></td>");
}
document.write("</tr></table>");

FLL();
document.write("</span>");


<?

if ($res)
{
    echo "field = {$arr[1]};";
}

?>

function redr()
{
    for (var i = 0; i < 9; ++ i)
    {
        for (var j = 0; j < 9; ++ j)
        {
            _('sud' + i + '' + j).style.border = '1px solid black';
            if (field[i][j] > 0) _('sud' + i + '' + j).innerHTML = '<b>' + field[i][j] + '</b>';
            else _('sud' + i + '' + j).innerHTML = '&nbsp;';
        }
    }
    _('moo').style.display = 'none';
}

var cellx = -1, celly = -1;
var dis = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
function showmoo(y, x)
{
    if (cellx != -1)
    {
        engage(field[celly][cellx]);
        return;
    }
    redr();
    var yy = getAp(_('sud' + y + '' + x)).y;
    var xx = getAp(_('sud' + y + '' + 0)).x;
    _('moo').style.top = (yy + 30) + 'px';
    _('moo').style.left = (xx - 5) + 'px';
    _('moo').style.display = '';
    _('sud' + y + '' + x).style.border = '1px solid red';
    cellx = x;
    celly = y;

    dis = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    for (var i = 0; i < 9; ++ i)
        for (var j = 0; j < 9; ++ j) if (field[i][j]) if (i != y || j != x)
        {
            if (i == y || j == x || Math.floor(i / 3) == Math.floor(y / 3) && Math.floor(j / 3) == Math.floor(x / 3))
            {
                dis[field[i][j]] = 1;
            }
        }
    for (var i = 1; i < 10; ++ i)
    {
        _('hru' + i).style.color = (dis[i] ? 'grey' : 'black');
        _('hru' + i).style.cursor = (dis[i] ? 'default' : 'pointer');
    }
}

function engage(val)
{
    if (!dis[val]) field[celly][cellx] = val;
    cellx = -1;
    celly = -1;
    redr();
    var strs = [];
    for (var i = 0; i < 9; ++ i) strs[i] = field[i].join(',');
    query("quest_scripts/phrase987_ajax.php", strs.join(','));
}

function solved()
{
    _('proceed').innerHTML = "<li><a href='game.php?phrase=2099'>Игра пройдена! Вернуться к звездочету.</a>";
}

redr();

</script>

