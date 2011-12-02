<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<div name=moo id=moo>
<?

include( "../functions.php" );

f_MConnect( );

$res = f_MQuery( "SELECT recipe_id, name FROM recipes ORDER BY name" );

while( $arr = mysql_fetch_array( $res ) )
{
	print( "<a href=craft_editor_mid.php?id=$arr[recipe_id] target=mid>$arr[name]</a><br>" );
}

f_MClose( );

?>
</div>
