<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "arrays.php" );
include_once( "skin.php" );
include_once( "waste_functions.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "<script>window.top.location.href='index.php';</script>" );

$mid_php = 1;
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

function outc( $id, $x, $y, $xx, $yy, $val, $align = 'center' )
{
	$w = $xx - $x;
	$h = $yy - $y;

	echo "<div style='position:absolute; left:{$x}px; top:{$y}px; width:{$w}px; height:{$h}px;'>";
	echo "<table width=$w height=$h cellspacing=0 cellpadding=0 border=0>";
	echo "<tr><td align=$align valign=middle id=$id>";
	echo "<b><font color=white>$val</font></b>";
	echo "</td></tr>";
	echo "</table>";
	echo "</div>";
}


UpdateTitle( );

$tm = time( );
f_MQuery( "UPDATE online SET last_ping = $tm WHERE player_id = {$player->player_id}" );

$mini_games = Array( 0 => "Магия", 1 => "Шахматы", 3 => "5 в Ряд", 2 => "Камушки", /* 4 => "Покер", */ 5 => "Марблс", 100 => "Вернуться" );

f_MQuery( "LOCK TABLE player_waste WRITE" );
$res = f_MQuery( "SELECT * FROM player_waste WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( $arr )
{
	$game_id = $arr['game_id'];
	$regime = $arr['regime'];
}
else
{
	$game_id = 0;
	$regime = 0;
	f_MQuery( "INSERT INTO player_waste ( player_id, game_id, regime ) VALUES ( {$player->player_id}, 0, 0 )" );
}

f_MQuery( "UNLOCK TABLES" );

if( isset( $_GET['i'] ) )
{
	if( $regime == 0 && !f_MNum( f_MQuery( "SELECT * FROM waste_bets WHERE player1_id={$player->player_id} OR player2_id={$player->player_id}" ) ) )
	{
    	$game_id = $_GET['i'];
    	settype( $game_id, 'integer' );
    	if( ( $game_id < 0 || $game_id >= count( $mini_games ) - 1 ) && $game_id != 5 )
    		RaiseError( "Попытка поиграть в несуществующую мини-игру", "$game_id" );
    	f_MQuery( "UPDATE player_waste SET game_id = $game_id WHERE player_id={$player->player_id}" );
	}
}

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">

<?

include_js( "js/skin.js" );
include_js( "js/ajax.js" );
include_js( "js/clans.php" );
include_js( "js/ii.js" );
include_js( "js/skin2.js" );
include_js( "functions.js" );
include_js( 'js/timer2.js' );

if( $player->regime == 104 || $player->regime >= 300 || $player->regime == 108 )
	outc( 'rgame', 4, 5, 300, 40, '<script>document.write( NewTimer( '.( $player->till - time( ) ).', "<font color=black>Таймер в игре: <b>", "</b></font>", 0, "_( '."'moor'".' ).innerHTML = '."'<font color=darkred><b>Работа завершена</b></font>'".'" ) );</script>', 'left');

if( $player->regime == 103 )
{
	$res = f_MQuery( "SELECT deadline FROM player_craft WHERE player_id={$player->player_id}" );
	$arr = f_MFetch( $res );
	if( $arr )
		outc( 'rgame', 4, 5, 300, 40, '<script>document.write( NewTimer( '.( $arr[0] - time( ) ).', "<font color=black>Таймер в игре: <b>", "</b></font>", 0, "_( '."'moor'".' ).innerHTML = '."'<font color=darkred><b>Работа завершена</b></font>'".'" ) );</script>', 'left');
}
if( $player->regime == 111 )
{
	die( "<script>location.href='game.php';</script>" );
}

print( "<div style='position:relative;top:0px;left:0px;'>" );
print( "<center>" );
print( "<table cellspacing=0 cellpadding=0 border=0><tr>" );

for( $i = 0; $i < count( $mini_games ); ++ $i )
{
	print( "<td><img border=0 width=17 height=9 src=images/top/a.png></td>" );
	print( "<td><img border=0 width=92 height=9 src=images/top/e.png></td>" );
}
print( "<td><img border=0 width=17 height=9 src=images/top/a.png></td>" );

print( "</tr><tr>" );

foreach( $mini_games as $a => $b )
{
	if( $a ) print( "<td><img border=0 width=17 height=21 src=images/top/d.png></td>" );
	else print( "<td><img border=0 width=17 height=21 src=images/top/b.png></td>" );
	print( "<td width=92 height=21 background=images/top/f.png align=center valign=middle>" );
	if( $a == $game_id )
		print( "<b>$b</b>" );
	else if( $a != 100 ) print( "<a href=waste.php?i=$a>$b</a>" );
	else print( "<a href=game.php>$b</a>" );
	print( "</td>" );
}
print( "<td><img border=0 width=17 height=21 src=images/top/c.png></td></tr></table>" );


if( $regime == 1 )
{
	echo "</center><br><span style='position:relative;left:0px;top:0px;'>";
	include_once( 'magic_panel.php' );
	echo "</span>";
}
else if( $regime == 2 )
{
	echo "</center><br><span style='position:relative;left:0px;top:0px;'>";
	include_once( 'chess_panel.php' );
	echo "</span>";
}
else if( $game_id == 2 )
{
	include_once( 'mine_charmed_maxi.php' );
}
else if( $regime == 4 )
{
	include_once( 'ox.php' );
}
else if( $game_id == 4 )
{
	if( $player->Rank( ) == 1 or $player->login == 'Теняшечка' )
	{
		include_once( "poker_arena.php" );
	}
	else
	{
		echo 'Покер находится на реконструкции.';	
	}
}
else if( $game_id == 5 )
{
	include_once( "marbles.php" );
}
else
{




echo "<br><br><table width=500><tr><td><script>FLUl();</script><table width=100%><tr><td><script>FUcm()</script><b>".$mini_games[$game_id]."</b> - заявки на игру<script>FL()</script></td></tr>";

echo "<tr><td><script>FUlt();</script>";

echo "<small>".$game_descrs[$game_id]."</small><br>";

$res = f_MQuery( "SELECT * FROM waste_stats WHERE player_id={$player->player_id} AND game_id={$game_id}" );
$arr = f_MFetch( $res );
$won = ( int ) $arr['wins'];
$lost = ( int ) $arr['loses'];
$draw = ( int ) $arr['draw'];

echo "<div id=acts>";
echo getActions( $game_id );
echo "</div>";

echo "<script>FL();</script></td></tr>";

echo "<tr><td><script>FUlt();</script>";
echo "<table width=100%><tr><td width=33% align=left>Побед: <b>$won</b></td>";
echo "<td width=33% align=center>Поражений: <b>$lost</b></td>";
echo "<td align=right>Ничьих: <b>$draw</b></td></tr></table>";
echo "<script>FL();</script></td></tr>";

echo "<tr><td><script>FUlt();</script>";

echo "<div id=bets>";
echo "<center><script>document.write( '".getBets( $game_id )."' );</script></center>";
echo "</div>";

echo "<script>FL();</script></td></tr>";

echo "</table><script>FLL();</script></td></tr></table>";

print( "</center>" );
print( "</div>" );

?>

<script>

function act(id)
{
	query( "waste_ref.php?act="+id, '' );
}

function act2(id,v)
{
	query( "waste_ref.php?act="+id+"&v="+v, '' );
}

function ref( )
{
	query( "waste_ref.php?act=10", "" );
}

setInterval( 'ref( );', 15000 );

</script>

<?
}
?>
