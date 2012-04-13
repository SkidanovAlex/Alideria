<?

if (!$mid_php) die();

f_MQuery("LOCK TABLE player_mines WRITE");
$res = f_MQuery("SELECT * FROM player_mines WHERE player_id={$player->player_id}");
if (!f_MNum($res))
{
    $state = "";
    for ($i = 0; $i < 80; ++ $i)
    {
        $rx = ($i%10) * 70;
        $ry = floor($i/10) * 40;
        $x = mt_rand(0, 640) - $rx;
        $y = mt_rand(0, 280) - $ry;
        if ($i) $state .= ",";
        // X, Y and set id
        $state .= "[{$x},{$y},{$i}]";
    }
    f_MQuery("INSERT INTO player_mines (player_id, lost, f) VALUES ({$player->player_id}, 0, '{$state}')");
}
f_MQuery("UNLOCK TABLES");

?>

<div id='leave'><li> <a href='game.php?phrase=1909'>Уйти (пазл придется собирать сначала)</a></div>
<div>&nbsp;</div>
<div>&nbsp;</div>
<div id='moo'><img src='images/misc/ny2011/ice_town.png' style='position:absolute;display:none;z-index:100' id='answer'>&nbsp;</div>
<script>


var offx = [];
var offy = [];
var xs = [];
var ys = [];
var clr = [];
var done = 0;

var redraw = function(data, lost)
{
    if (lost) _('leave').innerHTML = '<li> <a href=\'game.php?phrase=1903\'>Картинка собрана! Вернуться к Главарю Снеговиков.</a>';
    if (lost) _('answer').style.display = '';
    if (lost) done = 1;
    var id = 0;
    ap = getAp(_('moo'));
    for (var i = 0; i < 8; ++ i)
        for (var j = 0; j < 10; ++ j)
        {
            _('puzzle' + id).style.left = (data[id][0] + offx[id]) + 'px';
            _('puzzle' + id).style.top = (data[id][1] + offy[id]) + 'px';
            xs[id] = data[id][0];
            ys[id] = data[id][1];
            clr[id] = data[id][2];
            ++ id;
        }
}

var id = 0;
for (var i = 0; i < 8; ++ i)
    for (var j = 0; j < 10; ++ j)
    {
        var img = document.createElement('img');
        img.style.left = '-1000px';
        img.style.top = '-1000px';
        img.id = 'puzzle' + id;
        img.style.position = 'absolute';
        img.src = 'images/misc/ny2011/IMG-' + id + '.png';
        ++ id;
        document.getElementById('moo').appendChild(img);
    }

setTimeout(function() {
    for (var id = 0 ; id < 80; ++ id)
    {
        ap = getAp(_('moo'));
        var i = Math.floor(id / 10);
        var j = id % 10;
        offx[id] = j * 70 + ap.x;
        offy[id] = i * 40 + ap.y;
        _('answer').style.left = ap.x + 20 + 'px';
        _('answer').style.top = ap.y + 20 + 'px';
    }

    query('quest_scripts/phrase918_ajax.php', '0');
}, 100);

var dz = -1, dx, dy, sx, sy;
var begin_drag = function(e) {
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
    for (var i = 0; i < 80; ++ i)
    {
        if (offx[i] + xs[i] + 20 <= x && offx[i] + xs[i] + 90 >= x && 20 + offy[i] + ys[i] <= y && offy[i] + ys[i] + 60 >= y)
        {
            dz = i;
            dx = sx = x;
            dy = sy = y;
        }
    }

    return false;
}

var do_drag = function(e) {
    var x, y;
	if( e )
	{
		x = e.pageX;
		y = e.pageY;
	}
	else
	{
		x = window.event.clientX + _('allContent').scrollLeft;
		y = window.event.clientY + _('allContent').scrollTop;
	}

    if (dz != -1)
    {
        for (var i = 0; i < 80; ++ i) if (clr[i] == clr[dz])
        {
            xs[i] += x - dx;
            ys[i] += y - dy;
            _('puzzle' + i).style.left = (xs[i] + offx[i]) + 'px';
            _('puzzle' + i).style.top = (ys[i] + offy[i]) + 'px';
        }
        dx = x; dy = y;
        return false;
    }
}

var end_drag = function(e) {
    var x, y;
	if( e )
	{
		x = e.pageX;
		y = e.pageY;
	}
	else
	{
		x = window.event.clientX + _('allContent').scrollLeft;
		y = window.event.clientY + _('allContent').scrollTop;
	}

    if (dz != -1)
    {
        query('quest_scripts/phrase918_ajax.php', clr[dz] + '|' + (x - sx) + '|' + (y - sy));
        dz = -1;
        return false;
    }
}

document.onmousedown = begin_drag;
document.onmousemove = do_drag;
document.onmouseup = end_drag;
document.body.onselect = function(e) { return false; }

</script>

