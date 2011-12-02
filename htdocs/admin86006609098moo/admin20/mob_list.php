<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<div name=moo id=moo>
<?

include( "../functions.php" );

f_MConnect( );

include( 'admin_header.php' );

$res = f_MQuery( "SELECT mob_id, name, loc FROM mobs ORDER BY loc, name" );

while( $arr = mysql_fetch_array( $res ) )
{
	print( "<a href=mob_editor_mid.php?id=$arr[mob_id] target=mid>$arr[name]</a><br>" );
}

f_MClose( );

?>
</div>
