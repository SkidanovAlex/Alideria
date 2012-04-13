<?

if( !$mid_php ) die( );

$res = f_MQuery( "SELECT talk_id FROM player_talks WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

$timg = "wolf.png"; $tleft = 251; $ttop = 11;
if ($arr[0] == 953) { $timg = "ezh.png"; $tleft = 250; $ttop = 6; }
if ($arr[0] == 959) { $timg = "hare.png"; $tleft = 249; $ttop = 4; }

?>

<script>

var states = [2, 4, 6, 8, 7, 1, 1];
var xs = [50, 50, 130, 90, 130, 130, 50];
var ys = [50, 50, 50, 90, 130, 90, 170];
var ps = [8, 8, 8, 8, 8, 2, 4];
var ws1 = [160, 160, 80, 80, 112, 56, 112];
var ws2 = [112, 112, 56, 56, 80, 80, 120];
var done = false;
var maxz = 0;

function redraw()
{
    for (var i = 0; i < 7; ++ i)
    {
        var el = _('tang' + i);
        el.style.left = xs[i] + 'px';
        el.style.top = ys[i] + 'px';
        el.src = 'images/misc/tangram/' + i + '' + states[i] + '.png';
    }
}

function tang_under(x, y)
{
    // triangles
    for (var i = 0; i < 7; ++ i)
    {
        var xx = x - xs[i];
        var yy = y - ys[i];
        var st = states[i];
        var ww = (states[i] % 2 ? ws2[i] : ws1[i])
        while (st > 2)
        {
            var nx = yy;
            yy = ww - xx;
            xx = nx;
            st -= 2;
        }
        if (xx < 0 || yy < 0 || xx > ww || yy > ww) continue;
        if (i < 5)
        {
            if (yy < xx) continue;
            if (st == 2 && xx > ww / 2) continue;
            if (st == 2 && xx > ww - yy) continue;
        }
        if (i == 5)
        {
            if (st == 1 && !(yy < xx + 40 && xx < yy + 40 && xx + yy > 40 && xx + yy < 120)) continue;
        }
        return i;
    }
    return -1;
}

var tx, ty, tz, tq = -1;
var lid = -1;

function tang_down(e)
{
    var x, y;
    if (done) return;
	if( e )
	{
        if (e.pageX - document.body.scrollLeft >= document.body.offsetWidth) return;
        if (e.pageY - document.body.scrollTop >= document.body.offsetHeight) return;
		x = e.pageX;
		y = e.pageY;
	}
	else
	{
		x = window.event.clientX + _('allContent').scrollLeft;
		y = window.event.clientY + _('allContent').scrollTop;
	}
    var vpos = getAp(_('tang_bg'));
    var id = tang_under(x - vpos.x, y - vpos.y);
    if (id != -1)
    {
        tq = id; tz = 0;
        tx = x;
        ty = y;
        _('tang' + id).style.zIndex = 1;
        if (lid != -1 && lid != id)
            _('tang' + lid).style.zIndex = 0;
        lid = id;
    }
    return false;
}

function tang_move(e)
{
    var x, y;
    if (done) return;
	if( e )
	{
        if (e.pageX - document.body.scrollLeft >= document.body.offsetWidth) return;
        if (e.pageY - document.body.scrollTop >= document.body.offsetHeight) return;
		x = e.pageX;
		y = e.pageY;
	}
	else
	{
		x = window.event.clientX + _('allContent').scrollLeft;
		y = window.event.clientY + _('allContent').scrollTop;
	}
    if (tq != -1)
    {
        var dx = 0, dy = 0;
        if (tx - x >= 8) dx = -Math.floor((tx - x) / 8);
        if (x - tx >= 8) dx = Math.floor((x - tx) / 8);
        if (ty - y >= 8) dy = -Math.floor((ty - y) / 8);
        if (y - ty >= 8) dy = Math.floor((y - ty) / 8);
        if (dx || dy)
        {
            tz = 1;
            xs[tq] += dx * 8;
            ys[tq] += dy * 8;
            tx += dx * 8;
            ty += dy * 8;
            if (xs[tq] < 2) xs[tq] = 2;
            if (ys[tq] < 2) ys[tq] = 2;
            redraw();
        }
    }
    return false;
}

function moo(v)
{
    v = Math.floor(v / 6);
    v = Math.floor((v + 4) / 8);
    v = v * 8;
    return v;
}

function tang_up(e)
{
    if (tq != -1 && !tz)
    {
        states[tq] ++;
        if (states[tq] > ps[tq]) states[tq] -= ps[tq];
        if (states[tq] <= 0) states[tq] += ps[tq];
        {
            if (states[tq] % 2 == (tq < 5 ? 1 : 0))
            {
                xs[tq] += moo(ws1[tq]);
                ys[tq] +=moo(ws1[tq]);
            }
            else
            {
                xs[tq] -= moo(ws1[tq]);
                ys[tq] -=moo(ws1[tq]);
            }
        }
        redraw();
    }
    tq = -1;
    var s = "";
    for (var i = 0; i < 7; ++ i)
    {
        if (i) s += "|";
        s += xs[i] + "|" + ys[i] + "|" + states[i];
    }
    query("quest_scripts/phrase953_ajax.php", s);
    return false;
}

</script>

<div id='tang_fv' style='position:relative;top:0px;left:0px;'>&nbsp;</div>
<div id='tang_bg' style='position:relative;top:0px;left:0px;width:700px;height:400px'>

<img src='images/misc/tangram/<?=$timg?>' style='position:absolute;left:<?=$tleft?>px; top:<?=$ttop?>px;'>
<script>
for (var i = 0; i < 7; ++ i)
    document.write("<img id='tang" + i + "' style='position:absolute;left:0px;top:0px'>");
</script>


<img src='empty.gif' id='tang_ev' style='position:relative;left:0px;top:0px;width:700px;height:400px;z-index:100'>

</div>

<script>

_('tang_ev').onmousedown = tang_down;
_('tang_ev').onmousemove = tang_move;
_('tang_ev').onmouseup = tang_up;
redraw();

</script>

