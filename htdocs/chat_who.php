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

if( $cregime == 1 ) $res = f_MQuery( "SELECT characters.login, characters.level, characters.player_id, characters.nick_clr, characters.clan_id, characters.sex FROM characters, online WHERE online.player_id=characters.player_id AND characters.loc = $cloc" );
else if( $cregime == 2 ) $res = f_MQuery( "SELECT characters.login, characters.level, characters.player_id, characters.nick_clr, characters.clan_id, characters.sex FROM characters, online WHERE online.player_id=characters.player_id AND characters.loc = $cloc AND characters.depth = $cdepth" );
else if( $cregime == 3 ) $res = f_MQuery( "SELECT characters.login, characters.level, characters.player_id, characters.nick_clr, characters.clan_id, characters.sex FROM characters, online WHERE online.player_id=characters.player_id AND characters.clan_id = {$player->clan_id}" );

?>
<html style="height: 101%">
<head>
<meta http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style4.css" rel="stylesheet" type="text/css">
</head>
<body>

<script src='functions.js'></script>

<table onmousedown='hide_m();' class="chat_who" height=101% width=100% name=tbl id=tbl><tr><td valign=top>
<center>

<?
$sort_type = (int)$_GET['s'];

?>

<table border=0 width=100%><tr><td width=33% align=right>
<a style='cursor: pointer' onclick='show_mr()'>Фильтр</a>&nbsp;&nbsp;</td><td width=33% align=center><a style='cursor: pointer' onClick='location.href="chat_who.php?s="+sort_type;'>Обновить</a></td><td width=33% align=left>&nbsp;&nbsp;<a style='cursor: pointer' onclick='show_ms()'>А-Я</a></td></tr></table></center>
<div id=moo name=moo>
</div>
</td></tr></table>
</body>
</html>

<script>

var names = new Array( );
var clrs = new Array( );
var levs = new Array( );
var clans = new Array( );
var sex = new Array( );
var status_text = new Array( );
var status_image = new Array( );
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
		names.sort( ); //names.sort( function(x,y){ if( String.toLowerCase( x.toString( ) ) < String.toLowerCase( y.toString( ) ) ) return 1; return -1; } );
	else if( sort_type == 1 )
		names.sort( function(x,y){ if( levs[x] < levs[y] ) return 1; else if( levs[x] > levs[y] ) return -1; return 0; } );
	else if( sort_type == 2 )
		names.sort( function(x,y){ if( levs[x] < levs[y] ) return -1; else if( levs[x] > levs[y] ) return 1; return 0; } );
	else if( sort_type == 4 )
		names.sort( function(x,y){ if( clans[x] < clans[y] ) return 1; else if( clans[x] > clans[y] ) return -1; return 0; } );
	else if( sort_type == 5 )
		names.sort( function(x,y){ if( clans[x] < clans[y] ) return -1; else if( clans[x] > clans[y] ) return 1; return 0; } );

	else
		names.sort( function(x,y) { return x > y ? -1 : 1; } ); //names.sort( function(x,y){ if( String.toLowerCase( x.toString( ) ) < String.toLowerCase( y.toString( ) ) ) return -1; return 1; } );

}

function add_plr( a, b, c, d, Gender, st_t, st_i )
{
	names[namesnum ++] = a;
	resort( );

	clrs[a] = b;
	levs[a] = c;
	clans[a] = d;
	sex[a] = Gender;
	status_text[a] = st_t;
	status_image[a] = st_i;
}

function refr( )
{
	st = '';
	for( i = 0; i < namesnum; ++ i )
	{
		c = names[i];

		st += '<a href="javascript://" onclick="parent.createPrivateRoom( ' + "'" + c + "'" + ' );parent.chat_in.Cursor();" title="Приват с персонажем ' + c + '"><img src="/images/pr.gif" style="width: 11px; height: 11px; border: 0px;" /></a> ';
<?
if ($player->Rank()==1)
{
?>
		if (status_image[c]!='')
		st += '<img id=st_'+i+' src="'+status_image[c]+'" height=13 width=13 onmouseover="showStatus(\''+status_image[c]+'\', \''+status_text[c]+'\');" onmouseout="hideShowStatus();">';
<?
}
?>
		st +=  window.top.ii( levs[names[i]], names[i], clrs[names[i]], clans[names[i]], sex[names[i]] );
<?

if( $player->Rank( ) == 1 || $player->Rank( ) == 2 || $player->Rank( ) == 5 )
{
	?>
	
		st += '<a href="/player_control.php?nick=' + c + '" target="_blank" title="Контроль Персонажа ' + c + '"><img src="/images/c.gif" style="width: 11px; height: 11px; border: 0px;" /></a>';

	<?
}

?>
		st += '</b></a><br>';
	}
	st = '<center>Всего: <b>' + namesnum + '</b></center><br>' + st;

	document.getElementById( 'moo' ).innerHTML = st;
	hide_m();
}

function moomoo( )
{
	return false;
}

<?

if( $cregime == 0 )echo chat_who_global_list();//include( "chat_who_global.html" );
else print( chat_who_list( $res ) );

?>

function hide_m( )
{
	document.getElementById( 'regimes' ).style.display = 'none';
	document.getElementById( 'sorts' ).style.display = 'none';
<?
if ($player->Rank() == 1)
	echo "hideStatus();";
?>
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

</script>

<div class="chat_who" id=regimes style='width:100px;border:1px solid black; background-color:black ; position:absolute; left:0px;top:20px;display:none;'>
<center>
<?

$cregimes = Array( 0 => "В игре", "В локации", "Рядом" );
if( $player->clan_id != 0 ) $cregimes[3] = "В ордене";
foreach( $cregimes as $a=>$b )
{
	if( $a == $cregime )
	{
		$a1 = "<b>";
		$a2 = "</b>";
	}
	else
	{
		$a1 = "<a style='cursor: pointer' onClick='location.href=\"chat_who.php?r=$a\";'>";
		$a2 = "</a>";
	}
	
	if( $a ) $a1 = "<br>".$a1;
	
	print( "$a1$b$a2" );
}

?>
</center>
</div>

<?
if ($player->Rank()==1)
	include_once("status.php");
?>

<div class="chat_who" id=sorts style='width:100px;border:1px solid black; background-color:black ; position:absolute; right:0px;top:20px;display:none;'>
<center><a style='cursor: pointer' onClick='if(sort_type==0)sort_type=3;else sort_type=0;resort();refr();'>По имени</a><br><a style='cursor: pointer' onClick='if(sort_type==1)sort_type=2;else sort_type=1;resort();refr();'>По уровню</a><br><a style='cursor: pointer' onClick='if(sort_type==4)sort_type=5;else sort_type=4;resort();refr();'>По ордену</a><br></center>
</div>

<script>

resort( );
refr( );
function RefreshList( )
{
	if( document.location.search == '?s=' + sort_type )
	{
		document.location.reload( );
	}
	else
	{
		document.location = "chat_who.php?s=" + sort_type;
	}
}
<?
$chat_ref_t = 1000*f_MValue("SELECT chat_ref_online FROM characters WHERE player_id=$player_id");
echo "setTimeout( 'RefreshList( )', ".$chat_ref_t." )";
?>
</script>