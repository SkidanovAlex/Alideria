<?
if( !$mid_php ) die( );

include_once( 'items.php' );


if ($player->player_id==1)
{
	echo "Локация находится в разработке";
}
else
{
	if (isset($_GET['give']))
	{
		$iid = (int)$_GET['give'];
		$endtime = f_MValue("SELECT end_time FROM player_repairs WHERE player_id={$player->player_id} AND item_id=$iid ORDER BY end_time LIMIT 1");
		if ($endtime<time())
		{
			$player->AddItems($iid);
			$player->AddToLogPost( $iid, 1, 43);
			f_MQuery("DELETE FROM player_repairs WHERE player_id={$player->player_id} AND item_id=$iid AND end_time=$endtime LIMIT 1");
		}
	}
	if (isset($_GET['repair']))
	{
		$iid = (int)$_GET['repair'];
		$repnum = f_MValue("SELECT COUNT(*) FROM player_repairs WHERE player_id=".$player->player_id);
		$iarr = f_MFetch(f_MQuery("SELECT decay, max_decay, price, parent_id, improved, clan_marked FROM items WHERE decay+1<max_decay AND type>1 AND type<14 AND item_id=".$iid));
		if ($iarr)
		{
		$iarr[2] = $iarr[2]/10;
		if (($iarr[0]+1) < $iarr[1])
		if (($player->money>=(int)$iarr[2]) && ($player->umoney>=$repnum) && $player->NumberItems($iid))
		{
			if (!checkOrderItem($iid))
			{
			$player->AddToLogPost( 0, -$iarr[2], 43);
			$player->SpendMoney($iarr[2]);
			$player->AddToLogPost( -1, -$repnum, 43);
			$player->SpendUMoney($repnum);
			$player->AddToLogPost( $iid, -1, 43);
			$player->DropItems($iid);
			$d = $iarr[1]-1;
			$niid = 0;
			if( !$iarr[4] && !$iarr[5] )
			{
				$niid = f_MValue( "SELECT item_id FROM items WHERE parent_id=$iarr[3] AND decay=$d AND max_decay=$d AND clan_marked=0 AND improved=0" );
				if (!$niid)
				{
					$niid = copyItem($iarr[3]);
					f_MQuery("UPDATE items SET decay=$d, max_decay=$d WHERE item_id=".$niid);
				}
			}
			else
			{
				$niid = $iid;
				f_MQuery("UPDATE items SET decay=$d, max_decay=$d WHERE item_id=".$niid);
			}
			$tm = time()+60*30;
			f_MQuery("INSERT INTO player_repairs (player_id, item_id, end_time) VALUES ({$player->player_id}, {$niid}, $tm)");
			}
		}
		}
	}
	include_js( 'js/timer.js' );
	echo "Добро пожаловать в Ремонтный Цех<br><br>";
	
	$rres = f_MQuery("SELECT items.item_id, items.name, items.price, items.decay, items.max_decay, items.image, items.image_large, player_repairs.end_time FROM items, player_repairs WHERE items.item_id=player_repairs.item_id AND player_id=".$player->player_id);
	$repnum = f_MNum($rres);
	if ($repnum)
	{
		//echo "<hr>Ваши заказы на ремонт<br><br>";
		echo "<table>";
		$first = true;
		$offs = 0;
		while ($rarr = f_MFetch($rres))
		{
			if( $offs == 0 )
			{
				if( !$first ) echo "</tr>";
				echo "<tr>";
			}
			$first = false;
			echo "<td style='width:180px;height:150px;'>";
			ScrollLightTableStart2( 'center' );
			echo "<img border=0 src=../../images/items/".itemImage( $rarr )."><br>";
			echo "<a href=help.php?id=1010&item_id=$rarr[item_id] target=_blank>$rarr[name]</a><br>";
			echo "<table width=100%>";
			echo "<tr><td>Прочность:</td><td align=right>$rarr[decay]/$rarr[max_decay]</td></tr>";
			//echo '<tr><td>Цена ремонта:</td><td style="text-align: right;">'.($rarr[price]/10).' <img src="/images/money.gif" style="width: 11px; height: 11px; border: 0px;" /></td></tr>';
			echo '</table>';
			if ($rarr[end_time]>time())
				echo "<script>document.write( InsertTimer( $rarr[end_time]-".time().", 'Осталось: <b>', '</b>', 0, 'location.href=\"game.php\"' ) );</script>";
			else
				echo '<a href="/game.php?give='.$rarr[item_id].'">Забрать</a>';
			ScrollLightTableEnd( );
			echo "</td>";
			++ $offs;
			$offs %= 5;
		}
		echo "</tr></table>";
	}
	
	$rres = f_MQuery("SELECT items.item_id, items.name, items.price, items.decay, items.max_decay, items.image, items.image_large FROM items, player_items WHERE items.item_id=player_items.item_id AND player_items.weared=0 AND items.decay+1<items.max_decay AND items.type>1 AND items.type<14 AND player_items.player_id=".$player->player_id);

	if (f_MNum($rres))
	{
		echo "<hr>";
		echo "<table>";
		$first = true;
		$offs = 0;
		while ($rarr = f_MFetch($rres))
		{
			if (!checkOrderItem($rarr[item_id]))
			{
			if( $offs == 0 )
			{
				if( !$first ) echo "</tr>";
				echo "<tr>";
			}
			$first = false;
			echo "<td style='width:180px;height:150px;'>";
			ScrollLightTableStart2( 'center' );
			echo "<img border=0 src=../../images/items/".itemImage( $rarr )."><br>";
			echo "<a href=help.php?id=1010&item_id=$rarr[item_id] target=_blank>$rarr[name]</a><br>";
			echo "<table width=100%>";
			echo "<tr><td>Прочность:</td><td align=right>$rarr[decay]/$rarr[max_decay]</td></tr>";
			echo '<tr><td>Цена ремонта:</td><td style="text-align: right;">'.($rarr[price]/10).' <img src="/images/money.gif" style="width: 11px; height: 11px; border: 0px;" /></td></tr>';
			if ($repnum)
				echo '<tr><td></td><td style="text-align: right;">'.($repnum).' <img src="/images/umoney.gif" style="width: 11px; height: 11px; border: 0px;" /></td></tr>';
			echo '</table>';
			echo '<a href="/game.php?repair='.$rarr[item_id].'">Чинить</a>';
			ScrollLightTableEnd( );
			echo "</td>";
			++ $offs;
			$offs %= 5;
			}
		}
		echo "</tr></table>";
	}
	
}

?>