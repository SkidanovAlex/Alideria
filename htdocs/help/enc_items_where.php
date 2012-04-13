<?

header("Content-type: text/html; charset=windows-1251");

include( '../functions.php' );

f_MConnect( );

$item_id = $HTTP_RAW_POST_DATA;
settype( $item_id, 'integer' );
//$item_id=f_MValue("SELECT parent_id FROM items WHERE item_id=$item_id LIMIT 1");
$st = '';

$res = f_MQuery( "SELECT npcs.* FROM npc_items, npcs WHERE item_id=$item_id AND npcs.npc_id=npc_items.npc_id" );
while( $arr = f_MFetch( $res ) )
{
	$st .= "<li>Получить от NPC <b>$arr[name]</b><br>";
}

$res = f_MQuery( "SELECT shops.name FROM shops INNER JOIN shop_goods ON shops.shop_id=shop_goods.shop_id WHERE shop_goods.item_id=$item_id" );
while( $arr = f_MFetch( $res ) )
{
	$st .= "<li>Купить в магазине &quot;<b>$arr[name]</b>&quot;<br>";
}

if (!($item_id == 77083 || $item_id == 76397 ))
{
	$res = f_MQuery( "SELECT mobs.mob_id, mobs.name, mob_items.chance FROM mobs, mob_items WHERE mobs.mob_id=mob_items.mob_id AND mob_items.item_id=$item_id" );
	while( $arr = f_MFetch( $res ) )
	{
		$arr[2] = $arr[2]/100;
		$st .= "<li>Дроп из монстра <b><a href=\"help.php?id=1016&beast_id={$arr[0]}\">{$arr[1]}</a></b> с шансом <b>{$arr[2]}%</b><br>";
	}
}


include( "../guild.php" );

$res = f_MQuery( "SELECT name, prof, rank, recipe_id FROM recipes WHERE result LIKE '$item_id:%'" );
while( $arr = f_MFetch( $res ) )
{
	$st .= "<li>Сделать в гильдии <b>".$guilds[$arr['prof']][0]."</b> у мастера с рангом не менее <b>$arr[rank]</b> (<a href=help.php?id=1015&recipe_id=$arr[recipe_id]>рецепт</a>)<br>";
}

$res = f_MQuery( "SELECT guild_id as prof, rank FROM lake_items WHERE item_id=$item_id" );
while( $arr = f_MFetch( $res ) )
{
	$st .= "<li>Добыть в гильдии <b>".$guilds[$arr['prof']][0]."</b> мастеру с рангом не менее <b>$arr[rank]</b><br>";
}

$res = f_MQuery( "SELECT count( cell_type ) FROM forest_items WHERE item_id=$item_id" );
$arr = f_MFetch( $res );
if( $arr[0] > 0 || $item_id == 36 ) $st .= "<li>Найти в Западном Лесу<br>";

$ds = "";
$res = f_MQuery( "SELECT depth FROM cave_items WHERE item_id=$item_id ORDER BY depth" );
while( $arr = f_MFetch( $res ) )
{
	$ds .= ", $arr[0]";
}
if( $ds != "" ) $st .= "<li>Найти в Пещерах на <b>". substr( $ds, 1 ) . "</b> глубине<br>";



if( $st == '' ) $st = '<i>Игрок должен сам найти эту вещь, не пользуясь подсказкой энциклопедии</i>';
//if( $st == '' ) $st = '<i>Не найдено ни одного способа получить вещь</i>';

include( 'skin.php' );

echo "loaded[$item_id] = true; document.getElementById( 'dvi$item_id' ).innerHTML = '";


echo "$st";


echo "';";

?>
