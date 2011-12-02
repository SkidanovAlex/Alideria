<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<div name=moo id=moo>
<?

include( "../functions.php" );

f_MConnect( );

include( 'admin_header.php' );

$res = f_MQuery( "SELECT creature_id, name, genre FROM creatures ORDER BY genre, name" );

while( $arr = mysql_fetch_array( $res ) )
{
	print( "<a href=creatures_editor_mid.php?id=$arr[creature_id] target=mid>($arr[genre]) $arr[name]</a><br>" );
}

f_MClose( );

?>
</div>
