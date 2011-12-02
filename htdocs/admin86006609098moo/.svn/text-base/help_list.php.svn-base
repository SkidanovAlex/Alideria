<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<div name=moo id=moo>
<?

include( "../functions.php" );

f_MConnect( );

include( 'admin_header.php' );

$p = Array( );
function rec( $id, $s )
{
	global $p;
	if( $p[$id] )
	{
		print( "<b>ВНИМАНИЕ! Топик $id имеет себя в качестве родителя</b><br>" );
		return;
	}
	$p[$id] = 1;
	$res = f_MQuery( "SELECT topic_id, title FROM help_topics WHERE parent_id = $id ORDER BY topic_id" );

	while( $arr = mysql_fetch_array( $res ) )
	{
		print( "$s<a href=help_editor_mid.php?id=$arr[topic_id] target=mid>$arr[title]</a><br>" );
		rec( $arr[topic_id], $s . "_" );
	}
}

rec( 0, "" );

f_MClose( );

?>
</div>
