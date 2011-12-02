<?

include( 'functions.php' );

$id = $HTTP_GET_VARS[id];
$turn = $HTTP_GET_VARS[turn];

settype( $id, 'integer' );
settype( $turn, 'integer' );

f_MConnect( );
$res = f_MQuery( "SELECT * FROM chess_logs WHERE game_id=$id AND turn_id=$turn" );

print( "<script>" );
if( !mysql_num_rows( $res ) )
{
	die( );
}

while( $arr = mysql_fetch_array( $res ) )
{
	print( "parent.show_turn( $arr[sy], $arr[sx], $arr[ey], $arr[ex], $arr[fig] );" );	
}
print( "parent.cur_turn ++;" );
print( "</script>" );

?>
