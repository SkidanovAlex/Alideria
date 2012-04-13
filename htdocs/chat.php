<?

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );

header("Content-type: text/html; charset=windows-1251");

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
$player->UploadInfoToJavaServer( );

?>
<html>
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style_chat.css" rel="stylesheet" type="text/css">
<script src=js/ajax.js></script>
</head>
<body>
<table cellpadding=0 cellspacing=0 border=0 width=100% height=100%>
<tr>
<td width=5 bgcolor=#e0c3a0 background="images/chat/chat_border_left.gif"><img src="empty.gif" width=5 height=5></td>
<td width=100% bgcolor=#e0c3a0 background="images/chat/chat_bg.gif" valign=top>

<div id=ch name=ch style="height: 101%">&nbsp;</div>

<div id=menu_bk name=menu_bk style='position: absolute; visibility: hidden; top: -1000px;'>
<table border=1 bgcolor=#000000 bordercolor=#000000><tr><td bgcolor=#000000>
<div id=menu_bk_div>&nbsp;</div>
</td></tr></table>
</div>

<div id=menu name=menu style='position: absolute; visibility: hidden; top: -1000px;'>
<table border=1 bgcolor=#BBBBBB><tr><td bgcolor=#BBBBBB>
<div id=menu_div>&nbsp;</div>
</td></tr></table>
</div>

</td>
<td width=5 bgcolor=#e0c3a0 background="images/chat/chat_border_right.gif"><img src="empty.gif" width=5 height=5></td>
</tr>
</table>

<script>

var lid = 0;
var lim = 30;
var msgs = new Array( );
var msgn = 0;

function syst( tm, msg ) // old-style
{
	parent.syst( tm, msg );
}

var tpk;
function menu_mover( ) { document.oncontextmenu = bcm; clearTimeout( tpk ); }
function menu_mout( ) { tpk = setTimeout( 'document.oncontextmenu = null;', 150 ); }
function item_mover( a ) { a.style.backgroundColor = 'navy'; a.style.color = 'white'; };
function item_mout( a ) { a.style.backgroundColor = '#BBBBBB'; a.style.color = 'black'; };
function wd() { if( self.innerWidth ) res = self.innerWidth; else res = document.body.clientWidth; return res; }
function hg() { if( self.innerHeight ) res = self.innerHeight; else res = document.body.clientHeight; return res; }
function show_menu( l1, e, msgId )
{
	cur_login = l1; k = document.getElementById( 'menu' ); k2 = document.getElementById( 'menu_bk' );
	kk = document.getElementById( 'menu_div' ); kk2 = document.getElementById( 'menu_bk_div' );
	if( e.pageX ) { l2 = e.pageX; t = e.pageY; }
	else { l2 = event.x+document.body.scrollLeft; t = event.y+document.body.scrollTop; }
	if( l2 + k.offsetWidth > wd()+document.body.scrollLeft ) l2 -= k.offsetWidth;
	if( t + k.offsetHeight > hg()+document.body.scrollTop ) t -= k.offsetHeight;
	
	st = "<table cellspacing=0 cellpadding=0 border=0>";

	st += "<tr><td id=ft2 onmousemove='item_mover(this)' onmouseout='item_mout(this)' onClick='f2()' style='cursor: pointer; background-color: #BBBBBB;'>Открыть приват с персонажем " + l1 + "</td></tr>";
	st += "<tr><td id=ft3 onmousemove='item_mover(this)' onmouseout='item_mout(this)' onClick='f3()' style='cursor: pointer; background-color: #BBBBBB;'>Добавить персонажа " + l1 + " в игнор</td></tr>";
	st += "<tr><td id=ft4 onmousemove='item_mover(this)' onmouseout='item_mout(this)' onClick='f4()' style='cursor: pointer; background-color: #BBBBBB;'>Информация о персонаже " + l1 + "</td></tr>";

	<? if( $player->Rank( ) == 1 || $player->Rank( ) == 2 || $player->Rank( ) == 5 || $player->Rank( ) == 3 ) { ?>

		st += "<tr><td id=ft5 onmousemove='item_mover(this)' onmouseout='item_mout(this)' onClick='f5()' style='cursor: pointer; background-color: #BBBBBB;'>Контроль персонажа " + l1 + "</td></tr>";

	<? } ?>

	st += "</table>";



	kk.innerHTML = st;
	kk2.innerHTML = st;
	k.style.left = l2 + 'px'; k.style.top = t + 'px'; k.style.visibility = 'visible';
	k2.style.left = ( l2 + 5 ) + 'px'; k2.style.top = ( t + 5 ) + 'px'; k2.style.visibility = 'visible';
	return false;
}
function f2() { hm(); parent.createPrivateRoom( cur_login ); }
function f3() { hm(); if( confirm( 'Добавить персонажа '+ cur_login + ' в игнор?' ) ) query('ch_add_to_ignore.php?nick='+cur_login,''); }
function f4() { hm(); window.open( 'player_info.php?nick=' + cur_login ); }

	<? if( $player->Rank( ) > 0 ) { ?>

function f5() { hm(); window.open( 'player_control.php?nick=' + cur_login ); }

	<? } ?>

function bcm() { return false; }
function hm() 
{
	document.getElementById( 'menu' ).style.visibility = 'hidden';
	document.getElementById( 'menu_bk' ).style.visibility = 'hidden';
}
document.onclick = hm;

function refr( )
{
	var st = '';
	for( i = 0, j = msgn - 1; i < lim && j >= 0; ++ i, -- j )
		st += msgs[j] + '<br>';
		
	if( msgn > lim )
		st += '<br>&nbsp;&nbsp;<a href="javascript:" onClick="lim=1000000;refr();">Показать весь лог</a><br>';
		
	document.getElementById( 'ch' ).innerHTML = st;
}

var tm;

function ok( ) { window.top.ok( ); }
function upal( ) { window.top.upal( ); }

function q( )
{
	clearTimeout( tm );
	query( 'chat_inf.php', '' + lid );
	tm = setTimeout( 'q();', window.top.chat_timeout );
}

q( );

</script>
</body>
</html>