<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<div name=moo id=moo>
<?

include( "../functions.php" );

f_MConnect( );

include( 'quest_header.php' );

$res = f_MQuery( "SELECT npc_id, name FROM npcs ORDER BY name, npc_id" );

while( $arr = mysql_fetch_array( $res ) )
{
	print( "<a href=npc_editor_mid.php?id=$arr[npc_id] target=mid>$arr[name]</a><br>" );
}

f_MClose( );

?>
</div>
