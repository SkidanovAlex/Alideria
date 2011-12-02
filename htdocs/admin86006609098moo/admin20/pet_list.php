<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<div name=moo id=moo>
<?

include( "../functions.php" );

f_MConnect( );

include( 'admin_header.php' );

$res = f_MQuery( "SELECT pet_id, name FROM pets ORDER BY name" );

while( $arr = mysql_fetch_array( $res ) )
{
	print( "<a href=pet_editor_mid.php?id=$arr[pet_id] target=mid>$arr[name]</a><br>" );
}

f_MClose( );

?>
</div>
