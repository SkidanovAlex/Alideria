<?

if( !$mid_php ) die( );

include_once( 'items.php' );

function secondhand_price( $arr )
{
	return floor( $arr[price] / 20.0 * $arr[decay]  / 20.0 * $arr[max_decay] / 3 );
}

if( isset( $_GET['sell'] ) && isset( $_GET['howmany'] ) )
{
	$id = $_GET['sell'];
	settype( $id, 'integer' );
	$howmany = (int)$_GET['howmany'];
	// проверка на орденскую принадлежность вещи
	if ( !checkOrderItem( $id ) )
	{
		$res = f_MQuery( "SELECT items.name, items.price, items.decay, items.max_decay, player_items.number FROM items, player_items WHERE items.item_id=player_items.item_id AND player_items.player_id={$player->player_id} AND player_items.weared = 0 AND items.item_id = $id" );
		$arr = f_MFetch( $res );
		if( $arr )
		{
			// Проверяем, чтобы у пользователя было достаточно штук вещей
			if( $howmany > $arr['number'] )
			{
				$player->syst( 'Нельзя продать больше, чем есть в инвентаре' );
			}
			elseif( $howmany < 0 )
			{
				$player->syst( 'Здесь вам не магазин, а Лавка Старьёвщика! Здесь вам покупают, а не продают! Ух!' );			
			}
			else
			{
				// Получаем стоимость
				$val = secondhand_price( $arr ) * $howmany;

				$player->AddToLog( $id, $howmany, 6, -1 );
				$player->AddToLog( 0, $val, 6, -1 );

				$player->AddMoney( $val );
				$player->DropItems( $id, $howmany );

				$player->syst( "Вы продаете <b>$howmany</b> <b>$arr[name]</b> и получаете <b>$val</b> ".my_word_str( $val, 'монету', "монеты", "монет" ) );

				// widow quest
		   	require_once( 'quest_race.php' );
		   	for( $fi = 0; $fi < $howmany; ++ $fi )
		   	{
		   		updateQuestStatus ( $player->player_id, 2504 );
		   	}
		   }
		}
	}
	else
	{
		echo "<font color=darked>Нельзя продать принадлежащую ордену вещь. Вам должно быть стыдно!</font><br>";
	}
}

$res = f_MQuery( "SELECT items.type, items.item_id, items.parent_id, items.name, items.price, items.decay, items.max_decay, items.image, items.image_large, player_items.number FROM items, player_items WHERE items.item_id=player_items.item_id AND player_items.player_id={$player->player_id} AND player_items.weared = 0 ORDER BY items.type" );

echo "<table>";
$first = true;
$offs = 0;
$last_type = -1;
while( $arr = f_MFetch( $res ) )
{
	$val = secondhand_price( $arr );
	if( $val == 0 ) continue;

	if( $arr['type'] != $last_type ) 
	{
		$last_type = $arr['type'];
		$offs = 0;
	}

	if( $offs == 0 )
	{
		if( !$first ) echo "</tr>";
		echo "<tr>";
	}
	$first = false;

	echo "<td style='width:180px;height:180px;'>";
	ScrollLightTableStart2( 'center' );
	echo "<img border=0 src=images/items/".itemImage( $arr )."><br>";
	echo "<a href=help.php?id=1010&item_id=$arr[parent_id] target=_blank>$arr[name]</a><br>";
	echo "<table width=100%>";
	if( $arr['type'] != 0 && $arr['type'] != 22 && $arr['type'] != 23 )
		echo "<tr><td>Прочность:</td><td align=right>$arr[decay]/$arr[max_decay]</td></tr>";
	echo '<tr><td>Гос.цена:</td><td style="text-align: right;">'.$arr[price].' <img src="/images/money.gif" style="width: 11px; height: 11px; border: 0px;" /></td></tr>';
	echo '<tr><td>Цена покупки:</td><td style="text-align: right;">'.$val.' <img src="/images/money.gif" style="width: 11px; height: 11px; border: 0px;" /></td></tr>';
	echo '<tr><td>Штук:</td><td style="text-align: right;"><input type="text" id="hm'.$arr[item_id].'" value="'.$arr[number].'" class="m_btn" style="width: 30px;" /></td></tr>';	
	echo '</table>';
	echo '<a href="javascript://" onclick="secondhandSell( '.$arr[item_id].' )">Продать</a>';
	ScrollLightTableEnd( );
	echo "</td>";

    ++ $offs;
    $offs %= 5;
}

echo "</tr></table>";
?>
<script>
	function secondhandSell( Sell )
	{
		if( document.getElementById( 'hm' + Sell ) )
		{
			document.location.href = '/game.php?sell=' + Sell + '&howmany=' + document.getElementById( 'hm' + Sell ).value;
		}
	}
</script>
