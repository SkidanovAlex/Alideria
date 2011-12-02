<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

echo "<a href=index.php>На главную</a><br><br>";

if( isset( $HTTP_GET_VARS['id'] ))
{
	$id = $HTTP_GET_VARS['id'];
if ($id > 0)
{

	$allnum = 0;
	$name = f_MFetch(f_MQuery("SELECT name FROM items WHERE item_id=".$id));
	echo "Предмет под номером $id&nbsp;$name[0]<br><br>";

	echo "<table border=1>";
	echo "<tr><td>Местонахождение</td><td>Количество</td></tr>";
	
	$num = f_MValue("SELECT SUM(player_items.number) FROM player_items, items WHERE player_items.item_id=items.item_id AND items.parent_id=$id");
	echo "<tr><td>На руках игроков:</td><td align=right><a href='admin_show_item_number.php?id=".$id."&t=1'>$num</a></td></tr>";
	$allnum = $allnum + $num;

	$num = f_MValue("SELECT SUM(player_warehouse_items.number) FROM player_warehouse_items, items WHERE player_warehouse_items.item_id=items.item_id AND items.parent_id=$id");
	echo "<tr><td>В хранилищах:</td><td align=right><a href='admin_show_item_number.php?id=".$id."&t=2'>$num</a></td></tr>";
	$allnum = $allnum + $num;

	$num = f_MValue("SELECT SUM(clan_items.number) FROM clan_items, items WHERE clan_id<>56 AND clan_id<>1 AND clan_items.item_id=items.item_id AND items.parent_id=$id");
	echo "<tr><td>В складах кланов:</td><td align=right><a href='admin_show_item_number.php?id=".$id."&t=3'>$num</a></td></tr>";
	$allnum = $allnum + $num;

	$num = f_MValue("SELECT SUM(post_items.number) FROM post_items, items WHERE post_items.item_id=items.item_id AND items.parent_id=$id");
	echo "<tr><td>В письмах:</td><td align=right><a href='admin_show_item_number.php?id=".$id."&t=4'>$num</a></td></tr>";
	$allnum = $allnum + $num;

	$num = f_MValue("SELECT SUM(auction.number) FROM auction, items WHERE auction.item_id=items.item_id AND items.parent_id=$id");
	echo "<tr><td>На аукционе:</td><td align=right><a href='admin_show_item_number.php?id=".$id."&t=5'>$num</a></td></tr>";
	$allnum = $allnum + $num;

	$num = f_MValue("SELECT SUM(shop_goods.number) FROM shop_goods, items WHERE shop_goods.shop_id>=20 AND shop_goods.item_id=items.item_id AND items.parent_id=$id");
	echo "<tr><td>В магазинах кланов:</td><td align=right><a href='admin_show_item_number.php?id=".$id."&t=6'>$num</a></td></tr>";
	$allnum = $allnum + $num;

	$num = f_MValue("SELECT SUM(shop_goods.number) FROM shop_goods, items WHERE shop_goods.shop_id<20 AND shop_goods.item_id=items.item_id AND items.parent_id=$id");
	echo "<tr><td>В гос. магазинах:</td><td align=right><a href='admin_show_item_number.php?id=".$id."&t=7'>$num</a></td></tr>";
	$allnum = $allnum + $num;

	echo "<tr><td>&nbsp;</td><td align=right>&nbsp;</td></tr>";
	echo "<tr><td>Всего:</td><td align=right>$allnum</td></tr>";

	echo "</table><br>";
}
elseif ($id == 0)
{
	echo "Дублоны<br><br>";

	$allnum = 0;
	echo "<table border=1>";
	echo "<tr><td>Местонахождение</td><td>Количество</td></tr>";
	
	$num = f_MValue("SELECT SUM(money) FROM characters WHERE money!=1000 AND length(pswrddmd5)>7 AND player_id!=6786 AND player_id!=286464 AND player_id!=868239 AND player_id!=6825 AND player_id!=67573");
	echo "<tr><td>На руках игроков:</td><td align=right><a href='admin_show_item_number.php?id=".$id."&t=1'>$num</a></td></tr>";
	$allnum = $allnum + $num;

	$num = f_MValue("SELECT SUM(money) FROM clans WHERE clan_id != 1 AND clan_id != 56");
	echo "<tr><td>В складах кланов:</td><td align=right><a href='admin_show_item_number.php?id=".$id."&t=3'>$num</a></td></tr>";
	$allnum = $allnum + $num;

	$num = f_MValue("SELECT SUM(money) FROM post");
	echo "<tr><td>В письмах:</td><td align=right><a href='admin_show_item_number.php?id=".$id."&t=4'>$num</a></td></tr>";
	$allnum = $allnum + $num;

	$num = f_MValue("SELECT SUM(money) FROM shops WHERE shop_id>=20");
	echo "<tr><td>В магазинах кланов:</td><td align=right><a href='admin_show_item_number.php?id=".$id."&t=6'>$num</a></td></tr>";
	$allnum = $allnum + $num;

	$num = f_MValue("SELECT SUM(money) FROM shops WHERE shop_id<20");
	echo "<tr><td>В гос. магазинах:</td><td align=right><a href='admin_show_item_number.php?id=".$id."&t=7'>$num</a></td></tr>";
	$allnum = $allnum + $num;

	echo "<tr><td>&nbsp;</td><td align=right>&nbsp;</td></tr>";
	echo "<tr><td>Всего:</td><td align=right>$allnum</td></tr>";

	echo "</table><br>";
}
elseif ($id == -1)
{
	echo "Таланты";
	
	$allnum = 0;
	echo "<table border=1>";
	echo "<tr><td>Местонахождение</td><td>Количество</td></tr>";
	
	$num = f_MValue("SELECT SUM(umoney) FROM characters WHERE player_id!=6786 AND player_id!=286464 AND player_id!=868239 AND player_id!=6825 AND player_id!=67573");
	echo "<tr><td>На руках игроков:</td><td align=right><a href='admin_show_item_number.php?id=".$id."&t=1'>$num</a></td></tr>";
	$allnum = $allnum + $num;

	echo "<tr><td>&nbsp;</td><td align=right>&nbsp;</td></tr>";
	echo "<tr><td>Всего:</td><td align=right>$allnum</td></tr>";

	echo "</table><br>";
}
}


?>

<table>
<form action='admin_show_item_number.php' method=get>
<tr><td>ID предмета:</td><td><input type=text name=id> (-1 для талантов, 0 для дублонов)</td></tr>
<tr><td><input type=submit value=Ok></td></tr>
</form>
</table><br><br>

<?

if (isset( $_GET['id']) && isset($_GET['t']))
{
	$t = $_GET['t'];
	$id = $_GET['id'];
	if ($t == 1)
	{
		echo "На руках игроков<br><br>";
		echo "<table border=1>";
		echo "<tr><td>Имя игрока</td><td>Количество</td></tr>";
		if ($id>0)
			$res = f_MQuery("SELECT player_items.player_id, player_items.number FROM player_items, items WHERE player_items.item_id=items.item_id AND items.parent_id=$id ORDER BY player_items.number DESC");
		elseif ($id==0)
			$res = f_MQuery("SELECT player_id, money FROM characters WHERE length(pswrddmd5)>7 AND money!=0 AND money!=1000 ORDER BY money DESC LIMIT 1000");
		elseif ($id == -1)
			$res = f_MQuery("SELECT player_id, umoney FROM characters WHERE umoney!=0 ORDER BY umoney DESC");
		while ($arr = f_MFetch($res))
		{
			$plr = new Player($arr[0]);
			echo "<tr><td>{$plr->login}</td><td>$arr[1]</td></tr>";
		}
		echo "</table>";
	}
	if ($t == 2)
	{
		echo "В хранилищах игроков<br><br>";
		if ($id>0)
		{
			echo "<table border=1>";
			echo "<tr><td>Имя игрока</td><td>Количество</td></tr>";
			$res = f_MQuery("SELECT player_warehouse_items.player_id, player_warehouse_items.number FROM player_warehouse_items, items WHERE player_warehouse_items.item_id=items.item_id AND items.parent_id=$id ORDER BY player_warehouse_items.number DESC");
			while ($arr = f_MFetch($res))
			{
				$plr = new Player($arr[0]);
				echo "<tr><td>{$plr->login}</td><td>$arr[1]</td></tr>";
			}
			echo "</table>";
		}
		else
			echo "В хранилищах могут быть только вещи.";
	}
	if ($t == 3)
	{
		echo "На складах кланов<br><br>";
		echo "<table border=1>";
		echo "<tr><td>Имя клана</td><td>Количество</td></tr>";
		if ($id > 0)
			$res = f_MQuery("SELECT clans.name, clan_items.number FROM clans, clan_items, items WHERE clans.clan_id=clan_items.clan_id AND clans.clan_id<>56 AND clans.clan_id<>1 AND clan_items.item_id=items.item_id AND items.parent_id=$id ORDER BY clan_items.number DESC");
		elseif ($id == 0)
			$res = f_MQuery("SELECT name, money FROM clans ORDER BY money DESC");
		while ($arr = f_MFetch($res))
			echo "<tr><td>{$arr[0]}</td><td>$arr[1]</td></tr>";
		echo "</table>";
	}
	if ($t == 4)
	{
		echo "В письмах<br><br>";
		echo "<table border=1>";
		echo "<tr><td>Имя отправителя</td><td>Имя получателя</td><td>Количество</td></tr>";
		if ($id > 0)
			$res = f_MQuery("SELECT post.sender_id, post.receiver_id, post_items.number FROM post, post_items, items WHERE post.entry_id=post_items.entry_id AND post_items.item_id=items.item_id AND items.parent_id=$id ORDER BY post_items.number DESC");
		elseif ($id == 0)
			$res = f_MQuery("SELECT sender_id, receiver_id, money FROM post WHERE money!=0 ORDER BY money DESC");
		while ($arr = f_MFetch($res))
		{
			$plr1 = new Player($arr[0]);
			$plr2 = new Player($arr[1]);
			echo "<tr><td>{$plr1->login}</td><td>{$plr2->login}</td><td>$arr[2]</td></tr>";
		}
		echo "</table>";
	}
	if ($t == 5)
	{
		echo "На аукционе<br><br>";
		echo "<table border=1>";
		echo "<tr><td>Имя игрока</td><td>Количество</td></tr>";
		$res = f_MQuery("SELECT auction.player_id, auction.number FROM auction, items WHERE auction.item_id=items.item_id AND items.parent_id=$id ORDER BY auction.number DESC");
		while ($arr = f_MFetch($res))
		{
			$plr = new Player($arr[0]);
			echo "<tr><td>{$plr->login}</td><td>$arr[1]</td></tr>";
		}
		echo "</table>";
	}
	if ($t == 6)
	{
		echo "В магазинах кланов<br><br>";
		echo "<table border=1>";
		echo "<tr><td>Имя магазина</td><td>Количество</td></tr>";
		if ($id > 0)
			$res = f_MQuery("SELECT shops.name, shop_goods.number FROM shops, shop_goods, items WHERE shops.shop_id=shop_goods.shop_id AND shop_goods.shop_id>=20 AND shop_goods.item_id=items.item_id AND items.parent_id=$id ORDER BY shop_goods.number DESC");
		elseif ($id == 0)
			$res = f_MQuery("SELECT name, money FROM shops WHERE shop_id >= 20 ORDER BY money DESC");
		while ($arr = f_MFetch($res))
			echo "<tr><td>{$arr[0]}</td><td>$arr[1]</td></tr>";
		echo "</table>";
	}
	if ($t == 7)
	{
		echo "В гос. магазинах<br><br>";
		echo "<table border=1>";
		echo "<tr><td>Имя магазина</td><td>Количество</td></tr>";
		if ($id > 0)
			$res = f_MQuery("SELECT shops.name, shop_goods.number FROM shops, shop_goods, items WHERE shops.shop_id=shop_goods.shop_id AND shop_goods.shop_id<20 AND shop_goods.item_id=items.item_id AND items.parent_id=$id ORDER BY shop_goods.number DESC");
		elseif ($id == 0)
			$res = f_MQuery("SELECT name, money FROM shops WHERE shop_id < 20 ORDER BY money DESC");
		while ($arr = f_MFetch($res))
			echo "<tr><td>{$arr[0]}</td><td>$arr[1]</td></tr>";
		echo "</table>";
	}
}

f_MClose( );
?>