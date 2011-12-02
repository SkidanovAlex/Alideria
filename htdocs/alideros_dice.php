<?

if( !$mid_php ) die( );

$summ_v = Array( 4=>62, 31, 18, 12, 8, 7, 6, 6, 7, 8, 12, 18, 31, 62 );

echo "<center><table><tr><td>";

ScrollLightTableStart( 'left' );

echo "<table width=560 border=0 cellspacing=0 cellpadding=0><tr>";
echo "<td><span id=total_stavka height=24>&nbsp;</span></td>";
echo "<td align=right><a target=_blank href=help.php?id=34272><b>Правила Игры</b></a></td>";
echo "</tr></table>";

echo "<div id=tbl style='width:560px;height:323px;position:relative;left:0px;top:0px;'>";

function utka( $x, $y, $w, $h, $html, $val = -1 )
{
	global $places;
	echo "<div style='position:absolute;left:{$x};top:{$y};width:{$w}px;height:{$h}px'><table cellspacing=0 cellpadding=0 width=$w height=$h border=1 bordercolor=black><tr width=$w height=$h><td align=center valign=middle width=$w height=$h>$html</td></tr></table></div>";
	if( $val != -1 ) $places .= "addplace( offs.x + $x, offs.y + $y, $val, $w, $h );";
}

utka( 0, 0, 80, 20, "<b>1:1</b>" );
utka( 80, 0, 120, 20, "<b>11:1</b>" );
utka( 200, 0, 55, 20, "<b>180:1</b>" );
utka( 255, 0, 50, 20, "<b>31:1</b>" );
utka( 305, 0, 55, 20, "<b>180:1</b>" );
utka( 360, 0, 120, 20, "<b>11:1</b>" );
utka( 480, 0, 80, 20, "<b>1:1</b>" );

utka( 0, 20, 80, 80, '<font size=+1><b>Сумма 4-10</b></font><br>Кроме троек', 0 );
utka( 80, 20, 40, 80, '<img src=images/dices/1.gif width=32 height=32 border=0><br><img src=images/dices/1.gif width=32 height=32 border=0>', 2 );
utka( 120, 20, 40, 80, '<img src=images/dices/2.gif width=32 height=32 border=0><br><img src=images/dices/2.gif width=32 height=32 border=0>', 3 );
utka( 160, 20, 40, 80, '<img src=images/dices/3.gif width=32 height=32 border=0><br><img src=images/dices/3.gif width=32 height=32 border=0>', 4 );
utka( 200, 20, 55, 27, '<img src=images/dices/1.gif width=16 height=16 border=0><img src=images/dices/1.gif width=16 height=16 border=0><img src=images/dices/1.gif width=16 height=16 border=0>', 8 );
utka( 200, 47, 55, 27, '<img src=images/dices/2.gif width=16 height=16 border=0><img src=images/dices/2.gif width=16 height=16 border=0><img src=images/dices/2.gif width=16 height=16 border=0>', 9 );
utka( 200, 74, 55, 26, '<img src=images/dices/3.gif width=16 height=16 border=0><img src=images/dices/3.gif width=16 height=16 border=0><img src=images/dices/3.gif width=16 height=16 border=0>', 10 );
utka( 255, 20, 50, 80, '<img src=images/dices/triples.gif width=40 height=70 border=0>', 14 );
utka( 305, 20, 55, 27, '<img src=images/dices/4.gif width=16 height=16 border=0><img src=images/dices/4.gif width=16 height=16 border=0><img src=images/dices/4.gif width=16 height=16 border=0>', 11 );
utka( 305, 47, 55, 27, '<img src=images/dices/5.gif width=16 height=16 border=0><img src=images/dices/5.gif width=16 height=16 border=0><img src=images/dices/5.gif width=16 height=16 border=0>', 12 );
utka( 305, 74, 55, 26, '<img src=images/dices/6.gif width=16 height=16 border=0><img src=images/dices/6.gif width=16 height=16 border=0><img src=images/dices/6.gif width=16 height=16 border=0>', 13 );
utka( 360, 20, 40, 80, '<img src=images/dices/4.gif width=32 height=32 border=0><br><img src=images/dices/4.gif width=32 height=32 border=0>', 5 );
utka( 400, 20, 40, 80, '<img src=images/dices/5.gif width=32 height=32 border=0><br><img src=images/dices/5.gif width=32 height=32 border=0>', 6 );
utka( 440, 20, 40, 80, '<img src=images/dices/6.gif width=32 height=32 border=0><br><img src=images/dices/6.gif width=32 height=32 border=0>', 7 );
utka( 480, 20, 80, 80, '<font size=+1><b>Сумма 11-17</b></font><br>Кроме троек', 1 );

for( $i = 4; $i <= 17; ++ $i )
	utka( -160 + $i * 40, 100, 40, 60, "<font size=+2><b>$i</b></font><br><b>{$summ_v[$i]}:1</b>", $i + 11 );

utka( 0, 160, 35, 80, "<b>6:1</b>" );

$le = 35;
$id = 29;
for( $i = 1; $i <= 6; ++ $i ) for( $j = $i + 1; $j <= 6; ++ $j )
{
	utka( $le, 160, 35, 80, "<img src=images/dices/$i.gif width=30 height=30 border=0><br><img src=images/dices/$j.gif width=30 height=30 border=0>", $id );
	++ $id;
	$le += 35;
}

$dnames = Array( 1 => "Один", "Два", "Три", "Четыре", "Пять", "Шесть" );
for( $i = 1; $i <= 6; ++ $i )
{
	$w = 93;
	if( $i == 6 ) $w = 95;
	utka( $i * 93 - 93, 240, $w, 40, "<table width=".($w-4)." border=0 cellspacing=0 cellpadding=0><tr><td align=center width=57><b>$dnames[$i]</b></td><td align=right><img width=32 height=32 border=0 src=images/dices/$i.gif></td></tr></table>", 43 + $i );
}

utka( 0, 280, 186, 20, "<b>На одном кубике - 1:1</b>" );
utka( 186, 280, 186, 20, "<b>На двух кубиках - 2:1</b>" );
utka( 372, 280, 188, 20, "<b>На трех кубиках - 3:1</b>" );

utka( 0, 300, 560, 24, "<button id=btn_again class=s_btn onclick='st_again()' style='display:none;'>Повторить ставки</button>&nbsp;<button class=s_btn onclick='doIt()'>Ставки сделаны</button>" );

echo "</div>";

ScrollLightTableEnd( );

echo "</td></tr></table></center>";

include_js( 'js/event_handlers.js' );

?>

<script>

<?

$res = f_MQuery( "SELECT stavkas FROM player_casino WHERE player_id = {$player->player_id}" );
$arr = f_MFetch( $res );
if( !$arr ) $stavkas = 0;
else $stavkas = $arr[0];

echo "var left_stavkas = ".(45 - $stavkas).";\n";

?>

var total = 0;
var stavkas = new Array( );
var old_stavkas = new Array( );
var imgstr = '<table width=100% height=100% border=0 cellspacing=0 cellpadding=0><tr><td valign=middle align=center><img src=images/money.gif width=24 height=24 border=0></td></tr></table>';

function $(el)
{
   var r = { x: el.offsetLeft, y: el.offsetTop };
   if (el.offsetParent)
   {
       var tmp = $(el.offsetParent);
       r.x += tmp.x;
       r.y += tmp.y;
   }
   return r;
}

function doIt( )
{
	var st = '';
	for( i = 0; i < 50; ++ i ) if( stavkas[i] == 1 )
	{
		if( st != '' ) st += '|';
		st += i;
	}
	if( st == '' ) alert( 'Сначала сделайте ставку' );
	else query( "alideros_dice_ref.php", st );
}

function update_stavka( )
{
	document.getElementById( 'total_stavka' ).innerHTML = "<b>Общая ставка: " + total + " дублонов; Сегодня вы можете сделать еще " + left_stavkas + " ставок</b>";
}

function stavka( val )
{
	if( stavkas[val] == undefined ) stavkas[val] = 0;

	if( stavkas[val] == 0 && !left_stavkas ) { alert( 'Вы исчерпали лимит ставок на сегодня' ); return; }

	stavkas[val] = 1 - stavkas[val];
	
	if( stavkas[val] ) document.getElementById( 'moo' + val ).innerHTML = imgstr;
	else document.getElementById( 'moo' + val ).innerHTML = '&nbsp;';
	if( stavkas[val] ) { total += 20; left_stavkas --; }
	else { total -= 20; left_stavkas ++; }
	
	update_stavka( );
}

var fuck_IE = '';
function addplace( x, y, val, w, h )
{
	fuck_IE += "<div style='background:url(\"empty.gif\");z-index:1;position:absolute;left:" + x + "px;top:" + y + "px;width:" + w + "px;height:" + h + "px;cursor:pointer;' id=moo" + val + " onclick='stavka( " + val + " )' align=center>&nbsp;</div>";
}

function i_hate_IE( ) // все работает везде, если выкинуть IE :)
{
    offs = $( document.getElementById( 'tbl' ) );

    fuck_IE = document.getElementById( 'allContent' ).innerHTML;
    
    <? echo $places; ?>
    
    document.getElementById( 'allContent' ).innerHTML = fuck_IE;
}

function fin( )
{
	var dummy = 0;
	for( var i = 0; i < 50; ++ i )
	{
		if( stavkas[i] == 1 )  ++ dummy;
		old_stavkas[i] = stavkas[i];
		stavkas[i] = 0;
		document.getElementById( 'moo' + i ).innerHTML = '&nbsp;';
	}
	total = 0;
	
	if( dummy <= left_stavkas ) document.getElementById( 'btn_again' ).style.display = '';
	else document.getElementById( 'btn_again' ).style.display = 'none';
	
	update_stavka( );
}

function st_again( )
{
	for( var i = 0; i < 50; ++ i )
	{
		if( stavkas[i] == 1 ) { ++ left_stavkas; total -= 20; };
		if( old_stavkas[i] == 1 ) { -- left_stavkas; total += 20; };
		stavkas[i] = old_stavkas[i];
		if( 1 != stavkas[i] ) document.getElementById( 'moo' + i ).innerHTML = '&nbsp;';
		else document.getElementById( 'moo' + i ).innerHTML = imgstr;
	}
	
	update_stavka( );
}

update_stavka( );

addHandler( window, 'load', i_hate_IE );
//window.onload = i_hate_IE;

</script>

