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

settype( $y, "integer" );
settype( $x, "integer" );
settype( $ny, "integer" );
settype( $nx, "integer" );

$fig_names[1] = 'Ладья';
$fig_names[2] = 'Конь';
$fig_names[3] = 'Слон';
$fig_names[4] = 'Ферзь';

print( "Выберите фигуру, на которую хотите заменить пешку:<br>" );

for( $i = 1; $i <= 4; ++ $i )
	print( "<img style='cursor: pointer;' onClick='window.opener.location.href=\"chess_make_turn.php?x=$x&y=$y&nx=$nx&ny=$ny&fig=$i\"; window.close( );' width=32 height=32 border=0 alt={$fig_names[$i]} src=images/chess/fig$i.png>&nbsp;" );

?>
