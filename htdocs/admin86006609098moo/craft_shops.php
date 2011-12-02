<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );
include( '../guild.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $_GET['guild_id'] ) )
{
	$guild_id = $_GET['guild_id'];

	$res = f_MQuery( "SELECT r.*, i.name as moo, i.type as t, i.image as i, i.image_large as il, i.item_id as iid FROM recipes as r, items as i WHERE prof=$guild_id AND r.result LIKE concat( i.item_id, ':%' ) ORDER BY i.type, rank, i.effect" );
	$lt = -1;
	$st = "<?\n\n";
	while( $arr = f_MFetch( $res ) )
	{
		if( $lt != $arr['t'] )
		{
			if( $lt != -1 ) $st .= "TypeEnd( );\n";
			$st .= "TypeBegin( $arr[t] );\n";
			$lt = $arr['t'];
		}
		$img = ( $arr['il'] == '' ) ? $arr['i'] : $arr['il'];
		$st .= "\tRecipe( $arr[recipe_id], '$arr[name]', '$arr[ingridients]', '$arr[result]', '$arr[req]', $arr[rank], '$img', $arr[iid] );\n";
	}
	$st .= "TypeEnd( );\n\n?>\n";

	$f = fopen( '../craft_shops/shop'.$guild_id.'.php', 'w' );
	fwrite( $f, $st );
	fclose( $f );

	die( "<script>location.href='craft_shops.php';</script>" );
}

foreach( $guilds as $a=>$b )
{
	if( $b[3] ) echo "<a href=craft_shops.php?guild_id=$a>Перегенерить магазин гильдии $b[0]</a><br>";
}

?>
