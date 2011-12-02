<?

if( !$mid_php ) die( );

require_once "poker_func.php";

if ( check_ingame( ) )
{
	require "poker_game.php";
	return;
}

print_functions( );
global $player;


echo "<br><br><table width=500><tr><td><script>FLUl();</script><table width=100%>
<tr><td><script>FUcm()</script><b>".$mini_games[$game_id]."</b> - заявки на игру<script>FL()</script></td></tr>";

echo "<tr><td><script>FUlt();</script>";

echo "<small>Это покер, детка! )</small><br>";
echo "Твой баланс: {$player->money}<img src=images/money.gif><br>";

$res = f_MQuery( "SELECT * FROM waste_stats WHERE player_id={$player->player_id} AND game_id={$game_id}" );
$arr = f_MFetch( $res );
$won = ( int ) $arr['wins'];
$lost = ( int ) $arr['loses'];
$draw = ( int ) $arr['draws'];

echo "<div id=acts>";
$res = f_MQuery( "SELECT player1_id FROM waste_bets WHERE player1_id = {$player->player_id}" );
$arr = f_MFetch( $res );

if( !$arr )
{
	echo "<a href='javascript:ref()'>Обновить</a><br/><br/>";
	DrawBetSelect( "<div id='TableMoney'></div>", "UpdateCur( current_bet );", "<div id='StatDiv'></div>" );
	echo "<script>";
	?>
	function UpdateCur( val )
	{
		var small_blind = Math.round( val / 50 );
		if ( small_blind == 0 )
			small_blind = 1;
		var big_blind = small_blind * 2;
		var babos = '<img src=images/money.gif>';
		_( 'TableMoney' ).innerHTML = "<a href='javascript:CreateTable(" + val + ")'>Создать стол</a>";
		_( 'StatDiv' ).innerHTML = "<br/>Ставки: " + small_blind + babos + "/" +
			big_blind + babos + "<br/>Начальный закуп: " + val + babos + "<br/>";
	}
	<?php
	echo "SetMinMax( 50, {$player->money} );ShowBetSelect( );</script>";
}

echo "</div>";

echo "<script>FL();</script></td></tr>";

echo "<tr><td><script>FUlt();</script>";
echo "<table width=100%><tr><td width=33% align=left>Побед: <b>$won</b></td>";
echo "<td width=33% align=center>Поражений: <b>$lost</b></td>";
echo "<td align=right>Ничьих: <b>$draw</b></td></tr></table>";
echo "<script>FL();</script></td></tr>";

echo "<tr><td><script>FUlt();</script>";

echo "<div id=bets>";
//echo "<center><script>document.write( '".getPokerBets( )."' );</script></center>";
echo "</div>";

echo "<script>FL();</script></td></tr>";

echo "</table><script>FLL();</script></td></tr></table>";

print( "</center>" );

?>

<script>

function IsInTable( )
{
	return false;
}

function CreateTable(v)
{
	query( "poker_ajax.php", 'create_table ' + v );
}

ref( );
</script>

