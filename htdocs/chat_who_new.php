<?

include_once( "functions.php" );
include_once( "player.php" );
include_once( "chat_functions.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player_id = $HTTP_COOKIE_VARS['c_id'];
$cregime = -1;

$player = new Player( $player_id );

if( isset( $HTTP_GET_VARS['r'] ) )
{
	$r = $HTTP_GET_VARS['r'];
	settype( $r, 'integer' );
	if( $r >= 0 && ( $r <= 2 || $r == 3 && $player->clan_id != 0 )  )
	{
		f_MQuery( "UPDATE characters SET chat_who_regime = $r WHERE player_id = $player_id" );
		$cregime = $r;
	}
}

$cres = f_MQuery( "SELECT chat_who_regime, loc, depth FROM characters WHERE player_id = $player_id" );
$carr = f_MFetch( $cres );
$cregime = $carr[0];
$cloc = $carr[1];
$cdepth = $carr[2];

if( $cregime < 0 || $cregime > 3 ) $cregime = 0;

if( $player->clan_id == 0 && $cregime == 3 ) $cregime = 2;

if( $cregime == 1 ) $res = f_MQuery( "SELECT characters.login, characters.level, characters.player_id, characters.nick_clr, characters.clan_id FROM characters, online WHERE online.player_id=characters.player_id AND characters.loc = $cloc" );
else if( $cregime == 2 ) $res = f_MQuery( "SELECT characters.login, characters.level, characters.player_id, characters.nick_clr, characters.clan_id FROM characters, online WHERE online.player_id=characters.player_id AND characters.loc = $cloc AND characters.depth = $cdepth" );
else if( $cregime == 3 ) $res = f_MQuery( "SELECT characters.login, characters.level, characters.player_id, characters.nick_clr, characters.clan_id FROM characters, online WHERE online.player_id=characters.player_id AND characters.clan_id = {$player->clan_id}" );

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">
<HTML>
<HEAD>
</HEAD>

<script src='functions.js'></script>

<table onmousedown='hide_m();' class="chat_who" height=100% width=100% name=tbl id=tbl><tr><td valign=top>
<center>

<?
$sort_type = (int)$_GET['s'];

?>

<a style='cursor: pointer' onclick='show_mr()'>Фильтр</a> | <a style='cursor: pointer' onClick='location.href="chat_who.php?s="+sort_type;'>Обновить</a> | <a style='cursor: pointer' onclick='show_ms()'>А-Я</a></center>
<div id=moo name=moo>
</div>
</td></tr></table>
</BODY>
</HTML>

<script>

var names = new Array( );
var clrs = new Array( );
var levs = new Array( );
var clans = new Array( );
var namesnum = 0;

var sort_type = <?=$sort_type?>;

function nick( a )
{
	parent.chat_in.document.getElementById( 'inp' ).value = a + ', ' + parent.chat_in.document.getElementById( 'inp' ).value;
	parent.chat_in.Cursor( );
}

function resort( )
{
	if( !sort_type )
		names.sort( );
	else if( sort_type == 1 )
		names.sort( function(x,y){ if( levs[x] < levs[y] ) return 1; else if( levs[x] > levs[y] ) return -1; return 0; } );
	else if( sort_type == 2 )
		names.sort( function(x,y){ if( levs[x] < levs[y] ) return -1; else if( levs[x] > levs[y] ) return 1; return 0; } );
	else if( sort_type == 4 )
		names.sort( function(x,y){ if( clans[x] < clans[y] ) return 1; else if( clans[x] > clans[y] ) return -1; return 0; } );
	else if( sort_type == 5 )
		names.sort( function(x,y){ if( clans[x] < clans[y] ) return -1; else if( clans[x] > clans[y] ) return 1; return 0; } );

	else
		names.sort( function(x,y){ if( x < y ) return 1; else if( x > y ) return -1; return 0; } );
}

function add_plr( a, b, c, d )
{
	names[namesnum ++] = a;
	resort( );

	clrs[a] = b;
	levs[a] = c;
	clans[a] = d;
}

function oi( c )
{
	window.open('player_info.php?nick='+c,'_blank','scrollbars=yes,width=730,height=610,resizable=yes');
}


<?

if( $player->Rank( ) == 1 || $player->Rank( ) == 2 )
{
	?>
	
function oc( c )
{
	window.open('player_control.php?nick='+c,'_blank','scrollbars=yes,width=730,height=610,resizable=yes');
}

	<?
}

?>

function refr( )
{
	st = '';
	for( i = 0; i < namesnum; ++ i )
	{
		c = names[i];

		onc = "oi('" + c + "')";
//		st += '<a onClick="parent.createPrivateRoom( ' + "'" + c + "'" + ' );" style="cursor:pointer" title="Приват с персонажем ' + c + '"><img width=11 height=11></a> [Lvl:' + levs[names[i]] + ']&nbsp;<a style="cursor: pointer" onClick="nick(\'' + names[i] + '\')"><b><font color=' + clrs[names[i]] + '>' + c + '</font>&nbsp;<a onClick="' + onc + '" title="Информация о Персонаже ' + c + '" style="cursor: pointer"><img border=0 src=images/i.gif width=11 height=11></a>';
		st += '<a onClick="parent.createPrivateRoom( ' + "'" + c + "'" + ' );parent.chat_in.Cursor();" style="cursor:pointer" title="Приват с персонажем ' + c + '"><img width=11 height=11 src=images/pr.gif></a> ' + window.top.ii( levs[names[i]], names[i], clrs[names[i]], clans[names[i]] );
<?

if( $player->Rank( ) == 1 || $player->Rank( ) == 2 )
{
	?>
	
		onc = "oc('" + c + "')";
		st += '<a onClick="' + onc + '" title="Контроль Персонажа ' + c + '" style="cursor: pointer"><img border=0 src=images/c.gif width=11 height=11></a>';

	<?
}

?>
		st += '</b></a><br>';
	}
	st += '<center>Всего: <b>' + namesnum + '</b></center>';

	document.getElementById( 'moo' ).innerHTML = st;
	hide_m();
}

function moomoo( )
{
	return false;
}

<?

if( $cregime == 0 ) echo chat_who_global_list();//include( "chat_who_global.html" );
else print( chat_who_list( $res ) );

?>

function hide_m( )
{
	document.getElementById( 'regimes' ).style.display = 'none';
	document.getElementById( 'sorts' ).style.display = 'none';
}

function show_mr()
{
	if( _( 'regimes' ).style.display == '' )
		_( 'regimes' ).style.display = 'none';
	else
	{
		_( 'regimes' ).style.display = '';
	}
	return false;
}
function show_ms()
{
	if( _( 'sorts' ).style.display == '' )
		_( 'sorts' ).style.display = 'none';
	else
	{
		_( 'sorts' ).style.display = '';
	}
	return false;
}

resort( );
refr( );

</script>

<div class="chat_who" id=regimes style='width:100px;border:1px solid black; background-color: ; position:absolute; left:0px;top:20px;display:none;'>
<center>
<?

$cregimes = Array( 0 => "В игре", "В локации", "Рядом" );
if( $player->clan_id != 0 ) $cregimes[3] = "В Ордене";
foreach( $cregimes as $a=>$b )
{
	if( $a == $cregime )
	{
		$a1 = "<b>";
		$a2 = "</b>";
	}
	else
	{
		$a1 = "<a style='cursor: pointer' onClick='location.href=\"chat_who_new.php?r=$a\";'>";
		$a2 = "</a>";
	}
	
	if( $a ) $a1 = "<br>".$a1;
	
	print( "$a1$b$a2" );
}

?>
</center>
</div>

<div class="chat_who" id=sorts style='width:100px;border:1px solid black; background-color: ; position:absolute; right:0px;top:20px;display:none;'>
<center><a style='cursor: pointer' onClick='if(sort_type==0)sort_type=3;else sort_type=0;resort();refr();'>По имени</a><br><a style='cursor: pointer' onClick='if(sort_type==1)sort_type=2;else sort_type=1;resort();refr();'>По уровню</a><br><a style='cursor: pointer' onClick='if(sort_type==4)sort_type=5;else sort_type=4;resort();refr();'>По ордену</a><br></center>
</div>
