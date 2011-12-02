<?

include( 'functions.php' );

$link = f_MConnect( );

f_MQuery( "CREATE TABLE chess_opponents ( id int not null auto_increment, primary key( id ), player1 int, player2 int, status int, cur_turn int, last_turn_made int, empty_turns int, ask_draw int )" );
f_MQuery( "CREATE TABLE chess_tables ( game_id int, fig_id int, color int, x int, y int, moved int )" );
f_MQuery( "CREATE TABLE chess_logs ( game_id int, turn_id int, sx int, sy int, ex int, ey int, fig int )" );
f_MQuery( "CREATE TABLE chess_asks ( id int not null auto_increment, primary key( id ), player1 int, player2 int, time int )" );

mysql_close( $link );

?>
