<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<div name=moo id=moo>
<?

include( "../functions.php" );

f_MConnect( );

include( 'quest_header.php' );

$res = f_MQuery( "SELECT quest_id, name FROM quests ORDER BY quest_id" );

while( $arr = mysql_fetch_array( $res ) )
{
	print( "<a href=quest_editor_mid.php?id=$arr[quest_id] target=mid>$arr[name]</a><br>" );
}

f_MClose( );

?>
</div>
