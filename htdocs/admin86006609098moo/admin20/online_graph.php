<?

include( "../functions.php" );
include_once( '../player.php' );

f_MConnect( );

$res = f_MQuery( "SELECT * FROM online_graph ORDER BY entry_id DESC limit 50" );
while( $arr = f_MFetch( $res ) )
{
	$clr = 'black';
	if( $arr['value'] >= 100 ) $clr = 'green';
	if( $arr['value'] <= 50 ) $clr = 'darkred';
	echo date( "d M Y, H:i", $arr['timestamp'] ).": <font color=$clr><b>$arr[value]</b></font><br>";
}

?>
