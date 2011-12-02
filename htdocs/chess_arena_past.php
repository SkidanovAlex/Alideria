<?

include( 'functions.php' );
include( 'chess_functions.php' );

f_MConnect( );

if( in_game( ) )
{
	die( '<script>parent.location.href="chess_panel.php";</script>' );
}
print( "<div id=moo name=moo>" );

$res = f_MQuery( "SELECT * FROM chess_asks WHERE player1=$player_id OR player2=$player_id" );
if( mysql_num_rows( $res ) )
	die( );
	
$res = f_MQuery( "SELECT * FROM chess_opponents WHERE status >= 2 ORDER BY last_turn_made DESC LIMIT 20" );
if( !mysql_num_rows( $res ) )
	print( "<i>Сейчас не идет ни одной партии</i><br>" );
else
{
	print( "<table>" );
	
	print( "<tr><td><b>Завершенные партии в клубе:</b></td></tr>" );
	
	while( $arr = mysql_fetch_array( $res ) )
		print( "<tr><td>[<a target=_blank href=chess_log.php?id=$arr[id]>лог</a>]&nbsp;".nick_by_id( $arr[player1] )." <b><i>vs</i></b> ".nick_by_id( $arr[player2] )."</td></tr>" );
	
	print( "</table>" );
}

?>

</div>

<script>

parent.document.getElementById( 'chs' ).innerHTML = document.getElementById( 'moo' ).innerHTML;
parent.moo_clear( );

</script>
