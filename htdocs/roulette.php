<?

if( !$mid_php ) die( );

$numbers = Array( 0, 32, 15, 19, 4, 21, 2, 25, 17, 34, 6, 27, 13, 36, 11, 30, 8, 23, 10, 5, 24, 16, 33, 1, 20, 14, 31, 9, 22, 18, 29, 7, 28, 12, 35, 3, 26 );
$colors = Array( -1 );

for( $i = 0; $i < 36; ++ $i ) $colors[$numbers[$i + 1]] = $i % 2;

echo "<center><table style='position:relative;top:0px;'><tr><td>";

ScrollLightTableStart( 'left' );

echo "<table width=624 border=0 cellspacing=0 cellpadding=0><tr>";
echo "<td><span id=total_stavka height=24>&nbsp;</span></td>";
echo "<td align=right><a target=_blank href=help.php?id=34264><b>Правила Игры</b></a></td>";
echo "</tr></table>";
echo "<table border=1 bordercolor=black cellspacing=0 cellpadding=0 style='margin-right:24;margin-top:24;'>";

for( $j = 2; $j >= 0; -- $j )
{
	echo "<tr height=48>";
	for( $i = 0; $i < 12; ++ $i )
	{
		$num = ($i*3+$j+1);
		echo "<td id=place".$num." align=center valign=middle width=48 height=48><font size=+3><b><font color=".( $colors[$num] ? "'black'" : "'red'" ).">".$num."</b></font></td>";
	}
	echo "</tr>";
}

echo "<tr height=48>";
for( $i = 0; $i < 3; ++ $i )
{
	echo "<td align=center colspan=4><font size=+2><b>";
	if( $i == 0 ) echo "Первые 12";
	if( $i == 1 ) echo "Вторые 12";
	if( $i == 2 ) echo "Третьи 12";
	echo "</b></font></td>";
}
	
echo "</tr><tr height=48>";
for( $i = 0; $i < 6; ++ $i )
{
	echo "<td align=center colspan=2><font size=+1><b>";
	if( $i == 0 ) echo "1 - 18";
	if( $i == 1 ) echo "Четный";
	if( $i == 2 ) echo "<font color=red>Красный</font>";
	if( $i == 3 ) echo "Черный";
	if( $i == 4 ) echo "Нечетный";
	if( $i == 5 ) echo "19 - 36";
	echo "</b></font></td>";
}
	
echo "</tr><tr><td colspan=12 align=center><button id=btn_again class=s_btn onclick='st_again()' style='display:none;'>Повторить ставки</button>&nbsp;<button class=s_btn onclick='doIt()'>Ставки сделаны</button></td></tr>";

echo "</table>";

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
	for( i = 1; i < 140; ++ i ) if( stavkas[i] == 1 )
	{
		if( st != '' ) st += '|';
		st += i;
	}
	if( st == '' ) alert( 'Сначала сделайте ставку' );
	else query( "roulette_ref.php", st );
}

function update_stavka( )
{
	document.getElementById( 'total_stavka' ).innerHTML = "<b>Общая ставка: " + total + " дублонов; Сегодня вы можете сделать еще " + left_stavkas + " ставок</b>";
}

function stavka( val )
{
//	alert( val );
	if( stavkas[val] == undefined ) stavkas[val] = 0;

	if( stavkas[val] == 0 && left_stavkas<=0 ) { alert( 'Вы исчерпали лимит ставок на сегодня' ); return; }

	stavkas[val] = 1 - stavkas[val];
	
	if( stavkas[val] ) document.getElementById( 'moo' + val ).innerHTML = '<img src=images/money.gif width=24 height=24 border=0>';
	else document.getElementById( 'moo' + val ).innerHTML = '&nbsp;';
	if( stavkas[val] ) { total += 20; left_stavkas --; }
	else { total -= 20; left_stavkas ++; }
	
	update_stavka( );
}

var fuck_IE = '';
function addplace( x, y, val, w, h )
{
	x += 12;
	y += 12;
	fuck_IE += "<div style='background:url(\"empty.gif\");z-index:1;position:absolute;left:" + x + "px;top:" + y + "px;width:" + w + "px;height:" + h + "px;cursor:pointer;' id=moo" + val + " onclick='stavka( " + val + " )' align=center>&nbsp;</div>";
}

function i_hate_IE( ) // все работает везде, если выкинуть IE :)
{
    offs = $( document.getElementById( 'place3' ) );
    tmp = $( document.getElementById( 'place2' ) );
    sizey = tmp.y - offs.y;
    tmp = $( document.getElementById( 'place6' ) );
    sizex = tmp.x - offs.x;
    
    fuck_IE = document.getElementById( 'allContent' ).innerHTML;
    
    for( var j = 0; j < 3; ++ j )
    	for( var i = 0; i < 12; ++ i )
    	{
    		var num = ( 3 - j ) + i * 3;
    		addplace( offs.x + i * sizex, offs.y + j * sizey, num, 24, 24 );
    	}

    for( var j = 0; j < 3; ++ j )
    	for( var i = 0; i < 12; ++ i )
    	{
    		var num = ( 3 - j ) + i * 3;
    		addplace( offs.x + i * sizex + sizex / 2, offs.y + j * sizey, num + 36, 24, 24 );
    	}

    for( var j = 0; j < 3; ++ j )
    	for( var i = 0; i < 12; ++ i )
    	{
    		var num = ( 3 - j ) + i * 3;
    		addplace( offs.x + i * sizex, offs.y + j * sizey - sizey / 2, num + 72, 24, 24 );
    	}

    for( var j = 0; j < 2; ++ j )
    	for( var i = 0; i < 11; ++ i )
    	{
    		var num = ( 2 - j ) + i * 2;
    		addplace( offs.x + i * sizex + sizex / 2, offs.y + j * sizey + sizey / 2, num + 108, 24, 24 );
    	}
    	
    addplace( offs.x + 0 * sizex / 2, offs.y + 3 * sizey, 131, sizex*4-24, 24 );
    addplace( offs.x + 8 * sizex / 2, offs.y + 3 * sizey, 132, sizex*4-24, 24 );
    addplace( offs.x + 16 * sizex / 2, offs.y + 3 * sizey, 133, sizex*4-24, 24 );

    addplace( offs.x + 0 * sizex / 2, offs.y + 4 * sizey, 134, sizex*2-24, 24 );
    addplace( offs.x + 4 * sizex / 2, offs.y + 4 * sizey, 135, sizex*2-24, 24 );
    addplace( offs.x + 8 * sizex / 2, offs.y + 4 * sizey, 136, sizex*2-24, 24 );
    addplace( offs.x + 12 * sizex / 2, offs.y + 4 * sizey, 137, sizex*2-24, 24 );
    addplace( offs.x + 16 * sizex / 2, offs.y + 4 * sizey, 138, sizex*2-24, 24 );
    addplace( offs.x + 20 * sizex / 2, offs.y + 4 * sizey, 139, sizex*2-24, 24 );
    
    document.getElementById( 'allContent' ).innerHTML = fuck_IE;
}

function fin( )
{
	var dummy = 0;
	for( var i = 1; i < 140; ++ i )
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
	for( var i = 1; i < 140; ++ i )
	{
		if( stavkas[i] == 1 ) { ++ left_stavkas; total -= 20; };
		if( old_stavkas[i] == 1 ) { -- left_stavkas; total += 20; };
		stavkas[i] = old_stavkas[i];
		if( 1 != stavkas[i] ) document.getElementById( 'moo' + i ).innerHTML = '&nbsp;';
		else document.getElementById( 'moo' + i ).innerHTML = '<img src=images/money.gif width=24 height=24 border=0>';
	}
	
	update_stavka( );
}

update_stavka( );

addHandler( window, 'load', i_hate_IE );
//window.onload = i_hate_IE;

</script>

