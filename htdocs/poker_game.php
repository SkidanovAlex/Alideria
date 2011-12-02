<?php

if( !$mid_php ) die( );

require_once "poker_func.php";
require_once "poker_class.php";

if ( !isset( $player ) )
	return;

print_functions( );

$table_id = get_player_table( );
$table = new PokerTable( $table_id );

echo "<a style=\"cursor:pointer;\" onclick=\"ref()\" >Обновиться</a><br/>";
echo "<a style=\"cursor:pointer;\" onclick=\"LeaveTable()\" >Покинуть стол</a><br/>";

echo "<table width=990px height=360px border=0><tr height=100% valign=top><td width=230px>";

echo "<table width=220 height=317px><tr><td><script>FLUl();</script><table width=100% height=100%>
<tr><td><script>FUcm()</script><b>Статистика по игре в Алидерийский покер</b><script>FL()</script></td></tr>";

echo "<tr height=50px><td><script>FUlt();</script>";

echo "<table width=100% border=0>";
echo "<tr><td width=100px>Баланс:</td><td align=right><span id='EBalance'></span></td></tr>";
echo "<tr><td>Сыграно партий:</td><td align=right><span id='GamesPlayed'>0</span></td></tr>";
echo "</table>";

echo "<script>FL();</script></td></tr>";

echo "<tr height=200px><td><script>FUlt();</script>";
echo "&nbsp;";

DrawBetSelect( "<div id='LeftRaise'></div>", "UpdateRaise( current_bet );" );

echo "<script>FL();</script></td></tr>";

echo "</table><script>FLL();</script></td></tr></table>";

echo "</td>";

echo "<td width=525>";

echo "<font color=white><table width=100% border=0 cellpadding=0>";
echo "<tr height=317px><td width=100%>";
echo "<div id='TableDiv' style=\"position:relative; width: 524px; height: 317px; color: #FFFFFF; background-image:url('images/poker/my_bg.jpg');\">";
$div_x = array( 380, 550, 630, 550, 380, 270 );
$div_y = array( 230, 230, 320, 420, 420, 320 );
$div_offset_x = 260;
$div_offset_y = 220;
for ( $i = 0; $i < 6; ++ $i )
{
	$y = $div_y[$i] - $div_offset_y;
	$x = $div_x[$i] - $div_offset_x;
	echo "<div style=\"position:absolute; top: {$y}px; left: {$x}px;\">
	<div id='PlayerTop$i'></div>
	<div id='PlayerHand$i'></div>
	</div>";}
$div_x = array( 70, 240, 390, 240, 70, 30 );
$div_y = array( 50, 50, 200, 230, 230, 190 );
$div_offset_x = 0;
$div_offset_y = 0;
for ( $i = 0; $i < 6; ++ $i )
{
	$y = $div_y[$i] - $div_offset_y;
	$x = $div_x[$i] - $div_offset_x;
	echo "<div id='ArrowDiv$i' style=\"position:absolute; top: {$y}px; left: {$x}px;\"></div>";
}
echo "<div id='CornerTimer' style=\"position:absolute; top: 275px; left: 10px;\"></div>";
echo "<font size=+1><div id='ComboNameDiv' style=\"position:absolute; top: 95px; left: 140px;width:200;text-align:center;\">Загрузка</div></font>";
echo "<div id='TableMid' style=\"position:absolute; top: 120px; left: 140px;\"></div>";
echo "<div style=\"position:absolute; top: 190px; left: 220px;background-image:url('images/poker/bank.png');width:27px;height:25px;\"></div>";
echo "<font size=+1><div id='Bank' style=\"position:absolute; top: 211px; left: 215px;width:40px;text-align:center;\">500</div></font>";
echo "<div id='FlyingBank' style=\"position:absolute; top: 0px; left: 0px;display:none;\">";
echo "<div style=\"position:absolute; top 0px; left 0px; background-image:url('images/poker/bank.png');width:27px;height:25px;\"></div>";
echo "<font size=+1><div id='FlyingBankValue' style=\"position:absolute; top: 21px; left: -5px;width:40px;text-align:center;\">500</div></font>";
echo "</div>";
echo "</div>";
echo "</td></tr><tr><td>";
echo "<table width=100% height=100% border=0 cellpadding=0>";
echo "<tr height=35px valign=center align=center>
<td id='Button0' width=97px style=\"background-image:url('images/poker/button0.png');\"></td>
<td id='Button1' width=134px style=\"background-image:url('images/poker/button1.png');\"></td>
<td id='Button2' width=148px style=\"background-image:url('images/poker/button2.png');\"></td>
<td id='Button3' width=136px style=\"background-image:url('images/poker/button3.png');\"></td>
</tr>";
echo "</table></font>";

echo "</td></tr>";
echo "</table>";

echo "</td>";

echo "<td width=235px align=right>";

echo "<table width=220 height=317px><tr><td><script>FLUl();</script><table width=100% height=100%>
<tr><td><script>FUcm()</script><table width=100% height=100%><tr height=285>
<td width=100%><b>Знаете ли вы?</b><br/>";
echo "<div id='DrawIdDiv'>";
if ( $table->draw_id >= 0 )
{	echo "Это раздача номер {$table->draw_id}.";}
echo "</div>";
$babos = "<img src=images/money.gif>";
echo "Малый блайнд составляет {$table->small_blind}{$babos}.<br/>";
$big_blind = $table->small_blind * 2;
echo "Большой блайнд равен {$big_blind}{$babos}.<br/>";
echo "</td></tr></table><script>FL()</script></td></tr>";
echo "</table><script>FLL();</script></td></tr></table>";

echo "</td></tr></table>";
echo "<a style=\"cursor:pointer;\" onclick=\"ref()\" >Обновиться</a><br/>";
?>
<script>
var ticks_timer_id = 0;
var win_draw_id = 0;
var played_games = 0;
var min_raise = 0;
var max_raise = 0;
var all_in = 0;
var current_draw_id = -1;

function CurrentDrawId( new_draw_id )
{
	if ( current_draw_id != new_draw_id )
	{
		current_draw_id = new_draw_id;
		var s = '';
		if ( current_draw_id != -1 )			s = 'Это раздача номер ' + current_draw_id + '.';
		else
			s = '';
		_( 'DrawIdDiv' ).innerHTML = s;
	}
}

function CanShowWinners( id )
{	if ( win_draw_id < id )
	{
		win_draw_id = id;
		++ played_games;
		_( 'GamesPlayed' ).innerHTML = '' + played_games;
		return true;	}
	return false;}

function TimerTicks( sec_left )
{
	if ( ticks_timer_id > 0 )
		clearTimeout( ticks_timer_id );
	if ( sec_left <= 0 )
	{
		_( 'CornerTimer' ).innerHTML = '';
		setTimeout( 'ref( );', 999 );
		return;
	}
	ticks_timer_id = setTimeout( 'TimerTicks( ' + (sec_left - 1) + ' );', 1000 );
	_( 'CornerTimer' ).innerHTML = '<font size=+2>' + sec_left + ' сек</font>';}

function UpdateRaise( val )
{
    var s;
	if ( all_in > call && max_raise <= call )
		s = '<a href="javascript:Call( )">Ответить(' + call + ')</a>';
	else
	if ( val >= all_in )
		s = '<a href="javascript:AllIn( )">Поставить все(' + all_in + ')</a>';
	else
		s = '<a href="javascript:Raise( ' + val + ' )">Повысить на ' + val + '</a>';
	_( 'Button' + 2 ).innerHTML = s;
	_( 'LeftRaise' ).innerHTML = s;
}

function YourMove( _call, _min_raise, _max_raise, _all_in )
{
	max_raise = Math.min( _max_raise, _all_in );
	min_raise = Math.min( _min_raise, _all_in );
	all_in = _all_in;
	call = _call;

	_( 'Button' + 0 ).innerHTML = '<a href="javascript:Fold( )">Сбросить</a>';

	if ( call >= all_in )
		_( 'Button' + 1 ).innerHTML = '<a href="javascript:AllIn( )">Поставить все(' + all_in + ')</a>';
	else
	if ( call == 0 )
		_( 'Button' + 1 ).innerHTML = '<a href="javascript:Call( )">Принять</a>';
	else
		_( 'Button' + 1 ).innerHTML = '<a href="javascript:Call( )">Ответить(' + call + ')</a>';

	SetMinMax( min_raise, max_raise );
	ShowBetSelect( );

	if ( all_in > call && max_raise <= call )
		_( 'Button' + 3 ).innerHTML = '<a href="javascript:Call( )">Ответить(' + call + ')</a>';
	else
	if ( all_in > max_raise )
		_( 'Button' + 3 ).innerHTML = '<a href="javascript:Raise( ' + max_raise + ' )">Повысить на ' + max_raise + '</a>';
	else
		_( 'Button' + 3 ).innerHTML = '<a href="javascript:AllIn( )">Поставить все(' + all_in + ')</a>';

	ShowCombo( 'Ваш ход' );
}

function NotYourMove( )
{
	var i;
	ShowCombo( '' );
	HideBetSelect( );
	current_bet = 0;	for ( i = 0; i < 4; ++ i )
	{		_( 'Button' + i ).innerHTML = '';	}}

function ShowMoveArrow( pos )
{
	var i;	for ( i = 0; i < 6; ++ i )
	{		_( 'ArrowDiv' + i ).innerHTML = '';	}
	if ( pos >= 0 && pos < 6 )
	{
		var s;
		if ( pos % 3 == 2 )
			s = 'arrow_up';
		else
			s = 'arrow_right';
		_( 'ArrowDiv' + pos ).innerHTML = '<img src=images/poker/' + s + '.gif>';	}
}

function IsInTable( )
{	return true;}

function ShowCard( id )
{	try
	{
		_( 'CardImg' + id ).style.opacity='100';
	}
	catch(err)
	{

	}
	try
	{
		_( 'CardImg' + id ).filters.alpha.opacity='100';
	}
	catch(err)
	{

	}
}

function FadeCard( id )
{
	try
	{
		_( 'CardImg' + id ).style.opacity='0.5';
	}
	catch(err)
	{

	}
	try
	{
		_( 'CardImg' + id ).filters.alpha.opacity='50';
	}
	catch(err)
	{

	}
}

function BankFly( sx, sy, ex, ey, cur_time, fly_time )
{
	var el = _( 'FlyingBank' );
	if ( cur_time >= fly_time )
	{
		el.style.display = 'none';		return;
	}
	var vx = ex - sx;
	var vy = ey - sy;
	var x = sx + Math.round( vx * ( cur_time / fly_time ) );	var y = sy + Math.round( vy * ( cur_time / fly_time ) );
	el.style.left = '' + x + 'px';
	el.style.top = '' + y + 'px';
	cur_time += 20;
	setTimeout( 'BankFly( ' + sx + ', ' + sy + ', ' + ex + ', ' + ey + ', ' + cur_time + ', ' + fly_time + ' )', 20 );
}

function StartBankFly( count, sx, sy, ex, ey, fly_time )
{	var el = _( 'FlyingBank' );
	el.style.display = '';
	_( 'FlyingBankValue' ).innerHTML = '' + count;
	BankFly( sx, sy, ex, ey, 0, fly_time );
}

function FadeAllCard( n )
{
	var i;
	for ( i = 0; i < n; ++ i )
	{		FadeCard( i );	}}

function ShowCombo( s )
{	_( 'ComboNameDiv' ).innerHTML = '' + s;}

function ShowBank( s )
{
	_( 'Bank' ).innerHTML = '' + s;
}

function Call( )
{	query( 'poker_ajax.php', 'call ' );}

function Raise( val )
{
	query( 'poker_ajax.php', 'raise ' + val );
}

function Fold( )
{
	query( 'poker_ajax.php', 'fold ' );
}

function AllIn( )
{
	query( 'poker_ajax.php', 'all_in ' );
}

ref( );
</script>