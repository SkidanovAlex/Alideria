<?

header("Content-type: text/html; charset=windows-1251");

include( '../functions.php' );

f_MConnect( );

$item_id = $HTTP_RAW_POST_DATA;
settype( $item_id, 'integer' );

$st = '';

$res = f_MQuery( "SELECT npcs.* FROM npc_items, npcs WHERE item_id=$item_id AND npcs.npc_id=npc_items.npc_id" );
while( $arr = f_MFetch( $res ) )
{
	$st .= "<li>�������� �� NPC <b>$arr[name]</b><br>";
}

$res = f_MQuery( "SELECT shops.name FROM shops INNER JOIN shop_goods ON shops.shop_id=shop_goods.shop_id WHERE shop_goods.item_id=$item_id" );
while( $arr = f_MFetch( $res ) )
{
	$st .= "<li>������ � �������� &quot;<b>$arr[name]</b>&quot;<br>";
}

if ($item_id != 77083)
{
	$res = f_MQuery( "SELECT mobs.name FROM mobs INNER JOIN mob_items ON mobs.mob_id=mob_items.mob_id WHERE item_id=$item_id" );
	while( $arr = f_MFetch( $res ) )
	{
		$st .= "<li>���� �� ������� <b>$arr[name]</b><br>";
	}
}


include( "../guild.php" );

$res = f_MQuery( "SELECT name, prof, rank, recipe_id FROM recipes WHERE result LIKE '$item_id:%'" );
while( $arr = f_MFetch( $res ) )
{
	$st .= "<li>������� � ������� <b>".$guilds[$arr['prof']][0]."</b> � ������� � ������ �� ����� <b>$arr[rank]</b> (<a href=help.php?id=1015&recipe_id=$arr[recipe_id]>������</a>)<br>";
}

$res = f_MQuery( "SELECT guild_id as prof, rank FROM lake_items WHERE item_id=$item_id" );
while( $arr = f_MFetch( $res ) )
{
	$st .= "<li>������ � ������� <b>".$guilds[$arr['prof']][0]."</b> ������� � ������ �� ����� <b>$arr[rank]</b><br>";
}

$res = f_MQuery( "SELECT count( cell_type ) FROM forest_items WHERE item_id=$item_id" );
$arr = f_MFetch( $res );
if( $arr[0] > 0 || $item_id == 36 ) $st .= "<li>����� � �������� ����<br>";

$ds = "";
$res = f_MQuery( "SELECT depth FROM cave_items WHERE item_id=$item_id ORDER BY depth" );
while( $arr = f_MFetch( $res ) )
{
	$ds .= ", $arr[0]";
}
if( $ds != "" ) $st .= "<li>����� � ������� �� <b>". substr( $ds, 1 ) . "</b> �������<br>";



if( $st == '' ) $st = '<i>����� ������ ��� ����� ��� ����, �� ��������� ���������� ������������</i>';
//if( $st == '' ) $st = '<i>�� ������� �� ������ ������� �������� ����</i>';

include( 'skin.php' );

echo "loaded[$item_id] = true; document.getElementById( 'dvi$item_id' ).innerHTML = '";


echo "$st";


echo "';";

?>
