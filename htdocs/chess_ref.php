<?

include( "functions.php" );
include( "chess_functions.php" );

$link = f_MConnect( );

$game_id = my_game_id( );
if( $game_id == -1 )
	die( 'Вы не в игре' );

$game = new game( $game_id );

if( $game->status >= 2 && $_GET['leave'] )
{
	f_MQuery( "UPDATE player_waste SET regime=0 WHERE player_id={$player_id}" );
	die( "<script>parent.game.location.href='waste.php?rnd=".mt_rand( )."';</script>" );
}

$clr = my_game_color( );

$y = $HTTP_GET_VARS[y];
$x = $HTTP_GET_VARS[x];
$ny = $HTTP_GET_VARS[ny];
$nx = $HTTP_GET_VARS[nx];

settype( $y, "integer" );
settype( $x, "integer" );
settype( $ny, "integer" );
settype( $nx, "integer" );

print( "<script>" );

if( $game->turn % 2 == $clr )
	$q = 1;
else
	$q = 0;

print( "parent.stats( $q, {$game->status} );" );
	
$lid = $HTTP_GET_VARS['lid'];
settype( $lid, 'integer' );

$dtm = time( ) - $game->last_turn_made;
settype( $dtm, 'integer' );
if( $game->status < 2 )
{
	if( $dtm > 300 )
		$game->set_status( 3 );
}
$dtm *= 1000;

print( 'var d0=new Date(); ' );
print( "parent.tm=d0.getTime()-$dtm;" );

$res = f_MQuery( "SELECT * FROM chess_logs WHERE game_id={$game->game_id} AND turn_id >= $lid" );

$ok = 0;
while( $arr = mysql_fetch_array( $res ) )
{
	$ok = 1;
	print( "parent.show_turn( $arr[sy], $arr[sx], $arr[ey], $arr[ex], $arr[fig] );\n" );
}

if( $ok )
	print( "parent.adrw = 0;\n" );
	
if( $game->ask_draw == ( 2 - my_game_color( ) ) )
	print( "parent.ask_draw( 1 );\n" );
if( $game->ask_draw == 4 && $game->turn % 2 == my_game_color( ) )
	print( "parent.ask_draw( {$game->ask_draw} );\n" );
if( $game->ask_draw == 5 && $game->turn % 2 == my_game_color( ) )
	print( "parent.ask_draw( {$game->ask_draw} );\n" );
if( $game->ask_draw == 6 && $game->turn % 2 != my_game_color( ) )
	print( "parent.ask_draw( {$game->ask_draw} );\n" );
if( $game->ask_draw == 7 && $game->turn % 2 != my_game_color( ) )
	print( "parent.ask_draw( {$game->ask_draw} );\n" );

print( "parent.reff( {$game->turn} );\n" );

print( "</script>" );

?>
