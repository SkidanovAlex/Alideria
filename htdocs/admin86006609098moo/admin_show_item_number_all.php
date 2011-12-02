<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

function create_select( $nm, $arr, $val )
{
	$st = "<select name='$nm'>";
	
	$st .= "<option value=-1>Все\n";

	foreach( $arr as $key=>$value )
	{
		$st .= "<option value=$key";
		if( $key == $val ) $st .= " selected";
		$st .= ">$value\n" ;
	}
	
	$st .= '</select>';
	
	return $st;
}

echo "<a href=index.php>На главную</a><br><br>";

if (isset( $HTTP_GET_VARS['tp'] )) $t = $HTTP_GET_VARS['tp'];
else $t = -1;
if (isset( $HTTP_GET_VARS['tp_'] )) $t_ = $HTTP_GET_VARS['tp_'];
else $t_ = -1;

echo "<table>";
echo "<form action='admin_show_item_number_all.php' method=get>";
echo "<tr><td>Тип предмета:</td><td>".create_select( 'tp', $item_types, $t )."</td></tr>";
echo "<tr><td>Тип ресурса:</td><td>".create_select( 'tp_', $item_types2, $t_ )."</td></tr>";
echo "<tr><td><input type=submit value=Ok></td></tr>";
echo "</form>";
echo "</table><br><br>";

if( isset( $HTTP_GET_VARS['tp'] ))
{
	$tp = $HTTP_GET_VARS['tp'];
	
	if ($tp == -1) $t = " ORDER BY type";
	else $t = " AND type=".$tp;

	if (isset( $HTTP_GET_VARS['tp_'] ) && ($tp == 0))
	{
		$tp_ = $HTTP_GET_VARS['tp_'];
		if ($tp_ == -1) $t .= " ORDER BY type2";
		else $t .= " AND type2=".$tp_;
	}
	
	$res = f_MQuery("SELECT item_id, name, price FROM items WHERE item_id=parent_id".$t);
	echo "Всего предметов: ".f_MNum($res)."<br><br>";

	echo "<table border=2>";
	echo "<tr><td>ID предмета</td><td>Имя предмета</td><td>Гос. цена</td><td>На руках игроков</td><td>В хранилищах</td><td>В складах</td><td>В письмах</td><td>На аукционе</td><td>В магазинах кланов</td><td>В гос.магазинах</td><td>Всего</td></tr>";

	while ($arr = f_MFetch($res))	
	{
		$allnum = 0;

		$id = $arr[0];
		
		echo "<tr><td>".$arr[0]."</td><td>".$arr[1]."</td><td>".$arr[2]."</td>";

		$num = f_MValue("SELECT SUM(player_items.number) FROM player_items, items WHERE player_items.item_id=items.item_id AND items.parent_id=$id");
		echo "<td align=right>$num</td>";
		$allnum = $allnum + $num;

		$num = f_MValue("SELECT SUM(player_warehouse_items.number) FROM player_warehouse_items, items WHERE player_warehouse_items.item_id=items.item_id AND items.parent_id=$id");
		echo "<td align=right>$num</td>";
		$allnum = $allnum + $num;

		$num = f_MValue("SELECT SUM(clan_items.number) FROM clan_items, items WHERE clan_id<>56 AND clan_id<>1 AND clan_items.item_id=items.item_id AND items.parent_id=$id");
		echo "<td align=right>$num</td>";
		$allnum = $allnum + $num;

		$num = f_MValue("SELECT SUM(post_items.number) FROM post_items, items WHERE post_items.item_id=items.item_id AND items.parent_id=$id");
		echo "<td align=right>$num</td>";
		$allnum = $allnum + $num;

		$num = f_MValue("SELECT SUM(auction.number) FROM auction, items WHERE auction.item_id=items.item_id AND items.parent_id=$id");
		echo "<td align=right>$num</td>";
		$allnum = $allnum + $num;

		$num = f_MValue("SELECT SUM(shop_goods.number) FROM shop_goods, items WHERE shop_goods.shop_id>=20 AND shop_goods.item_id=items.item_id AND items.parent_id=$id");
		echo "<td align=right>$num</td>";
		$allnum = $allnum + $num;

		$num = f_MValue("SELECT SUM(shop_goods.number) FROM shop_goods, items WHERE shop_goods.shop_id<20 AND shop_goods.item_id=items.item_id AND items.parent_id=$id");
		echo "<td align=right>$num</td>";
		$allnum = $allnum + $num;

		echo "<td align=right>$allnum</td>";
	}

	echo "</table><br>";
}
f_MClose( );

?>