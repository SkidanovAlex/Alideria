<?

header("Content-type: text/html; charset=windows-1251");

include( '../functions.php' );
include( '../items.php' );
include( "../guild.php" );

f_MConnect( );

$item_id = $HTTP_RAW_POST_DATA;
settype( $item_id, 'integer' );

$st = '';

// название, состав, номер и гильдия для рецепта, где встречается указанный предмет ($item_id) 
$blah = f_MQuery( "SELECT name, ingridients, recipe_id, prof FROM recipes WHERE ingridients LIKE '$item_id:%' OR ingridients LIKE '%:$item_id:%' OR ingridients LIKE '%:$item_id.'" );
while ( $rec = f_MFetch( $blah ) )
{
	if( contains( array_keys( ParseItemStr( $rec['ingridients'] ) ), $item_id ) )
		$st .= "<li>Используется в гильдии <b>".$guilds[$rec['prof']][0]."</b> в рецепте: <a href=help.php?id=1015&recipe_id=".$rec['recipe_id']." target=_blank>".$rec['name']."</a></li>";
}

if( $st == '' ) $st = '<i>Предмет не используется ни в одном рецепте. Вроде бы.</i>';

echo "loaded_using[$item_id] = true; document.getElementById( 'dvu$item_id' ).innerHTML = '";


echo "$st";


echo "';";

?>
