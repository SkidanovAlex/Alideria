<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "waste_functions.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
$uid = $HTTP_COOKIE_VARS['c_id'];

$act = $_GET['act'];
$v = $_GET['v'];
$tm = time( );

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

if( $regime == 1 ) die( "location.href='waste.php?rnd=".mt_rand( )."';" );
if( $regime == 2 ) die( "location.href='waste.php?rnd=".mt_rand( )."';" );
if( $regime == 3 ) die( );
if( $regime == 4 ) die( "location.href='waste.php?rnd=".mt_rand( )."';" );

settype( $act, 'integer' );
settype( $v, 'integer' );

if( $act == 0 )
{
	if( $v < 0 || $v >= 1000000 ) die( "alert( 'Ставка должна быть от 0 до 100000 дублонов' );" );
	f_MQuery( "LOCK TABLE waste_bets WRITE" );
	$res = f_MQuery( "SELECT count( game_id ) FROM waste_bets WHERE player1_id=$uid OR player2_id=$uid" );
	$arr = f_MFetch( $res );
	if( !$arr[0] )
		f_MQuery( "INSERT INTO waste_bets( player1_id, player2_id, game_id, money, timestamp ) VALUES ( $uid, -1, $game_id, $v, $tm )" );
	else die( "alert( 'Сначала разберитесь с текущими заявками.' );" );
	f_MQuery( "UNLOCK TABLES" );
}
else if( $act == 1 )
{
	f_MQuery( "LOCK TABLE waste_bets WRITE" );
	$res = f_MQuery( "SELECT count( game_id ) FROM waste_bets WHERE player1_id=$uid" );
	$arr = f_MFetch( $res );
	if( $arr[0] )
		f_MQuery( "DELETE FROM waste_bets WHERE player1_id=$uid OR player2_id=$uid" );
	else f_MQuery( "UPDATE waste_bets SET player2_id=-1 WHERE player2_id={$player->player_id}" );
	f_MQuery( "UNLOCK TABLES" );
}
else if( $act == 2 )
{
	f_MQuery( "UPDATE waste_bets SET player2_id=-1 WHERE player1_id={$player->player_id}" );
}
else if( $act == 3 )
{
	f_MQuery( "LOCK TABLE waste_bets WRITE, player_waste WRITE" );
	$res = f_MQuery( "SELECT player1_id, player2_id, game_id, money FROM waste_bets WHERE player1_id=$uid" );
	$arr = f_MFetch( $res );
	if( !$arr ) { echo "alert( 'У вас не подана заявка. Вы не можете запустить игру.' );"; f_MQuery( "UNLOCK TABLES" ); }
	else if( $arr[1] == -1 ) { echo "alert( 'К сожалению, оппонент успел отказаться от игры.' );"; f_MQuery( "UNLOCK TABLES" ); }
	else
	{
		f_MQuery( "UPDATE player_waste SET regime=".($arr['game_id'] + 1)." WHERE player_id IN ( $arr[0], $arr[1] )" );
		f_MQuery( "DELETE FROM waste_bets WHERE player1_id = {$player->player_id}" );
		f_MQuery( "UNLOCK TABLES" );
		if( $game_id == 1 )
		{
    		include( 'chess_functions.php' );
    		create_game( $arr[0], $arr[1], $arr['money'] );
    	}
		if( $game_id == 3 )
		{
    		include( 'ox_functions.php' );
    		create_game( $arr[0], $arr[1], $arr['money'] );
    	}
    	else if( $game_id == 0 )
    	{
    		include( 'magic_functions.php' );
    		create_game( $arr[0], $arr[1], $arr['money'] );
    	}
		die( "location.href='waste.php?rnd=" . mt_rand( ) . "';" );
	}
}
else if( $act == 4 )
{
	$ok = false;
	f_MQuery( "LOCK TABLE waste_bets WRITE" );
	$res = f_MQuery( "SELECT count( game_id ) FROM waste_bets WHERE player1_id=$uid OR player2_id=$uid" );
	$arr = f_MFetch( $res );
	if( !$arr[0] )
	{
		$res = f_MQuery( "SELECT player2_id FROM waste_bets WHERE player1_id = $v AND game_id=$game_id" );
		$arr = f_MFetch( $res );
		if( !$arr ) echo "alert( 'Игрок успел отозвать свою заявку' );";
		else if( $arr[0] != -1 ) echo "alert( 'Кто-то успел принять заявку до вас' );";
		else { f_MQuery( "UPDATE waste_bets SET player2_id={$player->player_id} WHERE player1_id=$v" ); $ok = true; }
	}
	else die( "alert( 'Сначала разберитесь с текущими заявками.' );" );
	f_MQuery( "UNLOCK TABLES" );
	if( $ok )
	{
		$plr = new Player( $v );
		$plr->syst2( "Персонаж <b>{$player->login}</b> принял вашу заявку в мини-играх." );
	}
}
else if( $act == 100 && $game_id == 3 )
{
	f_MQuery( "LOCK TABLE waste_bets WRITE, player_waste WRITE" );
	$res = f_MQuery( "SELECT count( game_id ) FROM waste_bets WHERE player1_id=$uid" );
	$arr = f_MFetch( $res );
	if( !$arr[0] )
	{
    	f_MQuery( "UPDATE player_waste SET regime=4 WHERE player_id = {$player->player_id}" );
	  	f_MQuery( "UNLOCK TABLES" );

    	include( 'ox_functions.php' );
    	create_game( $player->player_id, 0, 0 );
    	die( "location.href='waste.php?rnd=" . mt_rand( ) . "';" );
	}
	else f_MQuery( "UNLOCK TABLES" );
}

echo "document.getElementById( 'acts' ).innerHTML = '".AddSlashes( getActions( $game_id ) )."';";
echo "document.getElementById( 'bets' ).innerHTML = '<center>".getBets( $game_id )."</center>';";

?>
