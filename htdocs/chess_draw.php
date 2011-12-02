<?

include( "functions.php" );
include( "chess_functions.php" );

$link = f_MConnect( );

$game_id = my_game_id( );
if( $game_id == -1 )
	die( '¬ы не в игре' );

$game = new game( $game_id );
$clr = my_game_color( );

$y = $HTTP_GET_VARS[y];
$x = $HTTP_GET_VARS[x];
$ny = $HTTP_GET_VARS[ny];
$nx = $HTTP_GET_VARS[nx];
$fig = $HTTP_GET_VARS[fig];

settype( $y, "integer" );
settype( $x, "integer" );
settype( $ny, "integer" );
settype( $nx, "integer" );
settype( $fig, "integer" );

if( $game->status >= 2 )
{
	print( "<script>alert( '»гра уже завершилась' );</script>" );
	die( );
}

$a = $HTTP_GET_VARS['a'];

if( $game->turn % 2 != $clr && $a < 4 )
{
	print( "<script>alert( '—ейчас не ваш ход' );</script>" );
	die( );
}

if( $a == 1 )
{
	if( $game->ask_draw )
		print( "<script>alert( '¬ы уже предлагали ничью на этом ходу' );</script>" );
	else $game->set_ask_draw( my_game_color( ) + 1 );
}
else if( $a == 2 )
{
	if( $game->empty_turns >= 100 )
	{
		print( "<script>alert( '¬аше утверждение подтвердилось, игра закончена в ничью' );</script>" );
		$game->set_ask_draw( 6 );
		$game->set_status( 4 );
		include( "waste_stats.php" );
		$varr = f_MFetch( f_MQuery( "SELECT money, player1, player2 FROM chess_opponents WHERE id={$game->game_id}" ) );
		$money = $varr[0];
		storeDraw( 1, $varr[2], $varr[1], $money );
	}
	else
		print( "<script>alert( '¬аше утрвеждение не подтвердилось' );</script>" );
}
else if( $a == 3 )
{
	// ѕопа :оќ
	$last_turn = -1;
	$la = 0;
	$moo = 0;
	$game2 = new game( -1 );
	$res = f_MQuery( "SELECT * FROM chess_logs WHERE game_id=$game_id ORDER BY turn_id" );
	while( $arr = mysql_fetch_array( $res ) )
	{
		if( $arr[turn_id] != $last_turn )
		{
			$q = $game->compare( $game2 );
			if( $q == 2 )
				++ $la;
			else if( $q == 1 )
				++ $moo;
				
			$last_turn = $arr[turn_id];
		}
		
		$game2->make_turn_safe( $arr[sy], $arr[sx], $arr[ey], $arr[ex], $arr[fig] );
	}
	$q = $game->compare( $game2 );
	if( $q == 2 )
		++ $la;
	else if( $q == 1 )
		++ $moo;
		
	if( $la >= 3 && 0 )
	{
		print( "<script>alert( '¬аше утверждение подтвердилось, игра закончена в ничью' );</script>" );
		$game->set_ask_draw( 7 );
		$game->set_status( 4 );
		include( "waste_stats.php" );
		$varr = f_MFetch( f_MQuery( "SELECT money, player1, player2 FROM chess_opponents WHERE id={$game->game_id}" ) );
		$money = $varr[0];
		storeDraw( 1, $varr[2], $varr[1], $money );
	}
/*	else if( !$moo )
		print( "<script>alert( '¬аше утрвеждение не подтвердилось, текуща€ позици€ стола встречаетс€ лишь $la раз' );</script>" );
	else
		print( "<script>alert( '¬аше утрвеждение не подтвердилось, текуща€ позици€ стола встречаетс€ лишь $la раз, и $moo раз с таким же расположением фигур но с иными возможност€ми' );</script>" ); */
	else
		print( "<script>alert( '¬аше утрвеждение не подтвердилось' );</script>" );
}
else if( $a == 4 )
{
	if( $game->ask_draw == ( 2 - my_game_color( ) ) )
	{
		$game->set_ask_draw( 4 );
		$game->set_status( 4 );
		print( "<script>parent.refr( );</script>" );
		include( "waste_stats.php" );
		$varr = f_MFetch( f_MQuery( "SELECT money, player1, player2 FROM chess_opponents WHERE id={$game->game_id}" ) );
		$money = $varr[0];
		storeDraw( 1, $varr[2], $varr[1], $money );
	}
}
else if( $a == 5 )
{
	if( $game->ask_draw == ( 2 - my_game_color( ) ) )
		$game->set_ask_draw( 5 );
}

?>
