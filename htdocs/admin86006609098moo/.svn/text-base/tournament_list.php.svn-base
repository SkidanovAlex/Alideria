<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<div name=moo id=moo>
<?

include( "../functions.php" );
include( "../arrays.php" );

f_MConnect( );

$res = f_MQuery( "SELECT date, name, tournament_id FROM tournament_announcements ORDER BY date" );
	
while( $arr = mysql_fetch_array( $res ) )
{
	print( "<a href=tournament_editor_mid.php?id=$arr[tournament_id] target=mid>[".date("d.m.Y H:i", $arr['date'])."] $arr[name]</a><br>" );
}

f_MClose( );

?>
</div>
