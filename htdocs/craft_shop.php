<?

if( !isset( $mid_php ) ) die( );

include_js( 'js/skin2.js' );

$pos = 0;

echo "<b>Покупка рецептов</b>";
print( "&nbsp;-&nbsp;<a href=game.php>Вернуться к работе</a>" );
print( "<br><br>" );

function TypeBegin( $id )
{
	global $pos;

	$pos = 0;
	echo "<table>";
}

function Recipe( $id, $name, $ing, $res, $req, $rank, $img, $item_id )
{
	global $pos, $player;
	global $item_types, $item_types2;

	$res = f_MQuery( "SELECT * FROM recipes WHERE recipe_id=$id" );
	$arr = f_MFetch( $res );
	$item_level = $arr['level'];
	$price = 50 * pow( 2, (int)( ( 1 + $item_level ) / 2 ) ) * ( 1.5 - 0.5 * ( $item_level % 2 ) );

	$res = f_MQuery( "SELECT * FROM items WHERE item_id=$item_id" );
	$arr = f_MFetch( $res );

	$res = f_MQuery( "SELECT * FROM recipes WHERE recipe_id=$id" );
	$st = outRecipes2( $res );
	$st .= "<br>";
	$st .= "<table><tr><td width=80 align=center><img src=images/items/$img><br>$arr[name]</td><td width=500>";
			$st .= "<b>Тип: </b>{$item_types[$arr[type]]}<br>";
			if( $arr['type'] == 0 ) $st .= "<b>Подтип: </b> {$item_types2[$arr[type2]]}<br>";
			if( $arr[level] ) $st .= "<b>Уровень: </b>$arr[level]<br>";
			$st .= "<b>Вес: </b>".($arr[weight])/100.0."<br>";
			$st .= "<b>Гос.Цена: </b>".($arr[price])."<br><br>";
			$st .= itemDescr( $arr, false );
	$st .= "</td></tr></table>";

	if( $pos == 0 ) echo "<tr>";
	echo "<td style='width:80px;height:80px;' align=center valign=middle>";

	$q = "<span id=b$id><a href='javascript:buy($id)'>Купить</a></span>";
	$res = f_MQuery( "SELECT count( recipe_id ) FROM player_recipes WHERE player_id={$player->player_id} AND recipe_id=$id" );
	$arr = f_MFetch( $res );
	if( $arr[0] > 0 ) $q = '&nbsp;';

	echo "<img onmousemove=\"showTooltip( event, '".$st."' )\" onmouseout='hideTooltip()' src=images/items/$img><br><img width=11 height=11 src=images/money.gif> $price<br>$q";
	echo "</td>";

	++ $pos;
	if( $pos == 6 )
	{
		echo "</tr>";
		$pos = 0;
	}
}

function TypeEnd( )
{
	global $pos;
	if( $pos != 0 ) { while( $pos != 6 ) { echo "<td>&nbsp;</td>"; ++ $pos; } echo "</tr>"; }
	echo "</table><br><br>";
}

include( "craft_shops/shop$guild_id.php" );

?>
<script>
function buy( id )
{
	query( 'craft_buy.php?id=' + id, '' );
}
</script>