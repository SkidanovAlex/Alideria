<?

include_once( "functions.php" );
include_once( "player.php" );
include_js( "js/tooltips.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

include_once( "trade_functions.php" );

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">
<script src=functions.js></script>

<script>

function trade_exit( )
{
	trade_ref.location.href = 'trade_ref.php?exit&rnd='+Math.random();
}

function trade_yes( )
{
	trade_ref.location.href = 'trade_ref.php?yes&rnd='+Math.random();
}

function toInt( a )
{
	a = parseInt( a );
	if( isNaN( a ) ) a = 0;
	return a;
}

function place( a )
{
	val = toInt( document.getElementById( 'place' + a ).value );
	trade_ref.location.href = 'trade_place.php?item_id=' + a + '&number=' + val + '&rnd='+Math.random();
}

function rem( a )
{
	val = - toInt( document.getElementById( 'rem' + a ).value );
	trade_ref.location.href = 'trade_place.php?item_id=' + a + '&number=' + val + '&rnd='+Math.random();
}

</script>

<center>
<?

$res = f_MQuery( "SELECT * FROM trades WHERE player1 = $player->player_id OR player2 = $player->player_id" );
$arr = f_MFetch( $res );

if( $arr )
{
	if( $arr['player1'] == $player->player_id ) $opponent = $arr['player2'];
	else $opponent = $arr['player1'];
	
	$cres = f_MQuery( "SELECT login FROM characters WHERE player_id = $opponent" );
	$carr = f_MFetch( $cres );
	
	print( "<b><u>Сделка между $player->login и $carr[0]</u></b><br>" );
}

?>
<div id=stat name=stat>&nbsp;</div>
<div id=btns name=btns>&nbsp;</div>
</center><hr>

<table width=100%><colgroup><col width=33%><col width=33%><col width=33%>
<tr><td vAlign=top>

<b>Ваши вещи:</b><br>

<?

$qres = f_MQuery( "SELECT items.*, player_items.number FROM items, player_items WHERE player_id = $player->player_id AND items.item_id = player_items.item_id AND weared=0" );
OutCol( $player->money, $qres, 1, $player->umoney );

?>

</td><td vAlign=top>

<b>Вы поставили:</b><br>
<div id=me name=me>

&nbsp;

</div>
</td>
<td vAlign=top>

<b><? print $carr[0]; ?> поставил:</b><br>
<div id=opp name=opp>

&nbsp;

</div>
</td>
</tr></table>

<iframe name=trade_ref id=trade_ref width=0 height=0></iframe>

<script>

function refr( )
{
	document.getElementById( 'stat' ).innerHTML = "<i>Идет загрузка списка поставленных товаров. Если эта надпись не исчезает долгое время, нажмите <a target=trade_ref href=trade_ref.php>сюда</a></i>";
	trade_ref.location.href='trade_ref.php?rnd='+Math.random();
}

refr( );

</script>

