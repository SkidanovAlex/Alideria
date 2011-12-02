<?

if( !$mid_php ) die( );

if( $player->level < 2 ) 
{
	echo "<center><i>Вы еще слишком малы, чтобы обмениваться на Ярмарке. Возвращайтесь, когда достигнете 2-ого уровня.</i></center>";
	return;
}

$has_bet = false;

$res = f_MQuery( "SELECT * FROM market_bets WHERE player_id = {$player->player_id}" );

printf( "<hr>" );

if( f_MNum( $res ) ) $has_bet = true;

if( isset( $HTTP_GET_VARS[bt] ) )
{
	$bt = ( $HTTP_GET_VARS[bt] == 1 ) ? 1 : 0;
	if( !$has_bet && $bt )
	{
		$tm = time( );
		f_MQuery( "INSERT INTO market_bets ( player_id, time, location, depth ) VALUES ( {$player->player_id}, $tm, {$player->location}, {$player->depth} )" );
		$has_bet = true;
	}
	else if( $has_bet && !$bt )
	{
		f_MQuery( "DELETE FROM market_bets WHERE player_id = {$player->player_id}" );
		$has_bet = false;
	}
}
if( isset( $HTTP_GET_VARS[ab] ) )
{
	if( !$has_bet )
	{
		$bet_id = $HTTP_GET_VARS[ab];
		settype( $bet_id, 'integer' );
		$res = f_MQuery( "SELECT player_id FROM market_bets WHERE bet_id = $bet_id" );
		$arr = f_MFetch( $res );
		if( $arr )
		{
			f_MQuery( "LOCK TABLE characters WRITE, trade_goods WRITE, market_bets WRITE, trades WRITE" );
			f_MQuery( "DELETE FROM trade_goods WHERE player_id=$player->player_id OR player_id = $arr[player_id]" );
			f_MQuery( "DELETE FROM market_bets WHERE player_id=$arr[player_id]" );
			f_MQuery( "INSERT INTO trades ( player1, player2 ) VALUES ( $player->player_id, $arr[player_id] )" );
			f_MQuery( "UPDATE characters SET regime=101 WHERE player_id=$player->player_id OR player_id=$arr[player_id]" );
			f_MQuery( "UNLOCK TABLES" );
			die( "<script>location.href='trade_sb.php';</script>" );
		}
	}
}

if( !$has_bet ) printf( "<li><a href=game.php?bt=1>Подать заявку</a><br>" );
else printf( "<li><a href=game.php?bt=0>Отозвать заявку</a><br>" );
printf( "<li><a href=game.php>Обновить</a><br>" );
	
printf( "<br>" );

$res = f_MQuery( "SELECT characters.login, market_bets.* FROM characters, market_bets WHERE market_bets.location = {$player->location} AND market_bets.depth = {$player->depth} AND characters.player_id = market_bets.player_id ORDER BY time DESC" );

if( f_MNum( $res ) )
{
	printf( "<table border=1><colgroup><col width=32><col width=200><col width=200><tbody>" );
	while( $arr = f_MFetch( $res ) )
	{
		if( $has_bet ) $q = "";
		else $q = "<a href=game.php?ab=$arr[bet_id]>Принять заявку</a>";

		$dt = date( "H:i", $arr[time] );
		printf( "<tr><td>$dt</td><td align=center><b>$arr[login]</b></td><td align=center>$q</td></tr>" );
	}
	printf( "</table>" );
}
else printf( "<i>Не подано ни одной заявки</i>" );

if( $has_bet ) print( "<script>setTimeout( 'location.reload()', 15000 );</script>" );

?>
