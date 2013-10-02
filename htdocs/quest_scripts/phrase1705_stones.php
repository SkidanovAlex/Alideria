<?

if( !$mid_php ) die( );

?>
<b>Ent:</b> Rules here
<table><tr><td vAlign=top>
<div id='here'>&nbsp;</div>
</td><td vAlign=top>
<br>
<div id='proceed'><li> <a href=game.php?phrase=2422>Leave</a></div>
<div id='tmr'>&nbsp;</div>
</td></tr></table>

<script src='js/skin2.js'></script>
<script src='js/timer2.js'></script>
<script>

var cx = -1, cy = -1, won = false;
var field = [[1, 1, 1], [0, 0, 0], [0, 0, 0], [2, 2, 2]];
var valid_move = function(x, y) {
    if (cx == -1) return false;
    if (field[x][y] != 0) return false;
    return Math.abs(cx - x) == 2 && Math.abs(cy - y) == 1 ||
        Math.abs(cx - x) == 1 && Math.abs(cy - y) == 2;
}

var redraw = function() {
    var st = '<table>';

    if (field[0][0] == 2 && field[0][1] == 2 && field[0][2] == 2)
    if (field[3][0] == 1 && field[3][1] == 1 && field[3][2] == 1)
    {
        won = true;
        _('proceed').innerHTML = '<li> <a href=game.php?phrase=2423>Proceed</a>';
        _('tmr').style.display = 'none';
    }

    for (var i = 0; i < 4; ++ i) {
        st += '<tr>';
        for (var j = 0; j < 3; ++ j) {
            st += '<td>';

            if (cx == -1 || valid_move(i, j) || cx == i && cy == j) {
                st += rFLUl();
            }
            else st += rFUlt();
            
            if (field[i][j] > 0 && cx == -1) {
                var onc = 'cx = ' + i + '; cy = ' + j + '; redraw();';
            }
            else if (cx != -1 && valid_move(i, j)) {
                var onc = 'field[' + i + '][' + j + '] = field[cx][cy]; field[cx][cy] = 0; cx = -1; cy = -1; redraw();';
            }
            else {
                var onc = 'cx = -1; cy = -1; redraw();';
            }

            st += '<div style="cursor:pointer;width:50px;height:50px" onclick="' + onc + '">';
            if (field[i][j] == 1) {
                st += '<img src="images/items/res/akvamarin.gif" width=50 height=50>';
            }
            else if (field[i][j] == 2) {
                st += '<img src="images/items/res/granat.gif" width=50 height=50>';
            }
            else st += '&nbsp;';
            st += '</div>';

            if (cx == -1 || valid_move(i, j) || cx == i && cy == j) {
                st += rFLL();
            }
            else st += rFL();

            st += '</td>';
        }
        st += '</tr>';
    }

    st += '</table>';

    _('here').innerHTML = st;
}

function restart() {
    field = [[1, 1, 1], [0, 0, 0], [0, 0, 0], [2, 2, 2]];
    cx = cy = -1;
    _('tmr').innerHTML = NewTimer(180, '<b>', '</b>', 0, 'if (!won) { alert("You lost"); restart(); }');
    redraw();
}

restart();

</script>

