<?

include( "functions.php" );
include( "chess_functions.php" );

$link = f_MConnect( );

$game_id = my_game_id( );
if( $game_id == -1 )
	die( 'Вы не в игре' );

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
	print( "<script>alert( 'Игра уже завершилась, нельзя ходить' );</script>" );
	die( );
}

if( $game->turn % 2 != $clr )
{
	print( "<script>alert( 'Сейчас не ваш ход' );</script>" );
	die( );
}

if( $game->clrs[$y][$x] == $clr )
	$game->make_turn( $y, $x, $ny, $nx, $fig );
	
print( "<script>parent.refr( );</script>" );

print( "moo" );

?>
