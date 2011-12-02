<?

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

include_once( "trade_functions.php" );

if( $player->regime != 101 && $player->regime != 102 ) die( "<script>parent.parent.location.href='game.php';</script>" );

f_MQuery( "LOCK TABLE items_order WRITE, clans WRITE, recipes WRITE, cards WRITE, player_log WRITE, player_items WRITE, trade_goods WRITE, characters WRITE, trades WRITE, items WRITE, attributes WRITE, player_clans WRITE" );

if( isset( $HTTP_GET_VARS['exit'] ) )
{
	f_MQuery( "DELETE FROM trades WHERE player1 = $player->player_id OR player2 = $player->player_id" );
	$player->SetRegime( 0 );
	f_MQuery( "UNLOCK TABLES" );
	die( "<script>parent.parent.location.href='game.php';</script>" );
}

$res = f_MQuery( "SELECT * FROM trades WHERE player1 = $player->player_id OR player2 = $player->player_id" );
$arr = f_MFetch( $res );
if( !$arr )
{
	?>
	
	<script>
	parent.document.getElementById( 'me' ).innerHTML = "&nbsp;";
	parent.document.getElementById( 'opp' ).innerHTML = "&nbsp;";

	<?
	if( $player->regime == 101 ) print( "parent.document.getElementById( 'stat' ).innerHTML = 'Похоже, ваш оппонент покинул сделку';" );
	if( $player->regime == 102 ) print( "parent.document.getElementById( 'stat' ).innerHTML = 'Сделка завершилась, поздравляем';" );
	?>
	
	parent.document.getElementById( 'btns' ).innerHTML = "<button class=ss_btn onClick=trade_exit()>Выход</button>";
	</script>
	
	<?

 	f_MQuery( "UNLOCK TABLES" );
    die( );
}

if( $arr['player1'] == $player->player_id )
{
	$opponent = $arr['player2'];
	$ostatus = $arr['status2'];
	$mstatus = $arr['status1'];
	$side = 1;
}
else
{
	$opponent = $arr['player1'];
	$ostatus = $arr['status1'];
	$mstatus = $arr['status2'];
	$side = 2;
}

$status_changed = false;
if( isset( $HTTP_GET_VARS['yes'] ) && $mstatus < 2 && $mstatus + 1 <= $ostatus + 1 )
{
	f_MQuery( "UPDATE trades SET status$side = status$side + 1 WHERE player$side = $player->player_id" );
	++ $mstatus;
	$status_changed = true;
}

printf( "<div name=me id=me>" );

$mres = f_MQuery( "SELECT number FROM trade_goods WHERE player_id = $player->player_id AND good_type = 0" );
$marr = f_MFetch( $mres );
if( !$marr ) $mn = 0;
else $mn = $marr[0];

$ures = f_MQuery( "SELECT number FROM trade_goods WHERE player_id = $player->player_id AND good_type = -1" );
$uarr = f_MFetch( $ures );
if( !$uarr ) $un = 0;
else $un = $uarr[0];

$qres = f_MQuery( "SELECT items.*, trade_goods.number FROM items, trade_goods WHERE player_id = $player->player_id AND items.item_id = trade_goods.good_id AND good_type = 1" );
OutCol( $mn, $qres, 2, $un );

printf( "</div>" );

printf( "<div name=opp id=opp>" );

$mres = f_MQuery( "SELECT number FROM trade_goods WHERE player_id = $opponent AND good_type = 0" );
$marr = f_MFetch( $mres );
if( !$marr ) $mn = 0;
else $mn = $marr[0];

$ures = f_MQuery( "SELECT number FROM trade_goods WHERE player_id = $opponent AND good_type = -1" );
$uarr = f_MFetch( $ures );
if( !$uarr ) $un = 0;
else $un = $uarr[0];

$qres = f_MQuery( "SELECT items.*, trade_goods.number FROM items, trade_goods WHERE player_id = $opponent AND items.item_id = trade_goods.good_id AND good_type = 1" );
OutCol( $mn, $qres, 0, $un );

printf( "</div>" );

if( $mstatus == 0 && $ostatus == 0 ) $st = "Выберите товары, которые хотите поставить, и нажмите кнопку ДА";
if( $mstatus == 0 && $ostatus == 1 ) $st = "Ваш оппонент сделал свой выбор, ждем вас";
if( $mstatus == 0 && $ostatus == 2 ) $st = "Ваш оппонент готов завершить сделку, видимо он очень вам доверяет?";
if( $mstatus == 1 && $ostatus == 0 ) $st = "Вы сделали свой выбор, ждем оппонента";
if( $mstatus == 1 && $ostatus == 1 ) $st = "Вы оба сделали свой выбор. Если все верно, нажмите кнопку ДА";
if( $mstatus == 1 && $ostatus == 2 ) $st = "Ваш оппонент готов завершить сделку, если все верно, нажмите кнопку ДА";
if( $mstatus == 2 && $ostatus == 0 ) $st = "Вы готовы завершить сделку. Не слишком ли вы доверяете оппоненту?";
if( $mstatus == 2 && $ostatus == 1 ) $st = "Вы готовы завершить сделку, ждем оппонента";
if( $status_changed && $mstatus == 2 && $ostatus == 2 )
{
	$opponent_player = new Player( $opponent );
	$st1 = ExchangeGoods( $player, $opponent_player );
	$st2 = ExchangeGoods( $opponent_player, $player );
	
	f_MQuery( "UPDATE trades SET status1=3 AND status2=3 WHERE player$side = $player->player_id" );
	
	f_MQuery( "DELETE FROM trades WHERE player$side = $player->player_id" );
	$player->SetRegime( 102 );
	$opponent_player->SetRegime( 102 );

	f_MQuery( "UNLOCK TABLES" );
	
	$tm = time( );
	f_MQuery( "INSERT INTO history_trades ( player_id1, player_id2, type, value1, value2, time ) VALUES ( {$player->player_id}, {$opponent_player->player_id}, 0, '$st1', '$st2', $tm )" );

	die( "<script>parent.refr();</script>" );
}
if( $mstatus == 3 ) $st = "Сделка завершилась, поздравляем!!!";

$z = "";
if( $mstatus <= $ostatus ) $z .= "<button class=ss_btn onClick=trade_yes()>Да</button>&nbsp;";
$z .= "<button class=ss_btn onClick=refr()>Обновить</button>&nbsp;";
$z .= "<button class=ss_btn onClick=trade_exit()>Выход</button>";

f_MQuery( "UNLOCK TABLES" );

?>

<script>
parent.document.getElementById( 'me' ).innerHTML = document.getElementById( 'me' ).innerHTML;
parent.document.getElementById( 'opp' ).innerHTML = document.getElementById( 'opp' ).innerHTML;
setTimeout( 'parent.refr();', 15000 );
parent.document.getElementById( 'stat' ).innerHTML = '<? echo $st; ?>';
parent.document.getElementById( 'btns' ).innerHTML = '<? echo $z; ?>';
</script>
