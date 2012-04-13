<?

include_once( 'items.php' );

if( !isset( $mid_php ) ) die( );
$stats = $player->getAllAttrNames( );

$page = $_GET['p'];
settype( $page, 'integer' );

if( isset( $_GET['type'] ) )
	$type = $_GET['type'];
else $type = -1;

if( isset( $_GET['nm'] ) )
	$fnm = $_GET['nm'];
else $fnm = '';

$get_lnk = '';
if( $type != -1 )
	$get_lnk .= "&type=$type";
if( $fnm != '' )
	$get_lnk .= "&nm=".urlencode( $fnm );


$start = $page * 20;

if( $_GET['new_lot'] )
{
	echo "<b>Выставить вещь на продажу</b> - <a href=game.php>Назад</a><br><small>Выберите вещь и количество. Начальную ставку и шаг вы сможете указать в следующем окне.</small><br><br>";

   	include_js( "js/items_renderer.js" );
	echo "<script>function doLot(id){location.href='game.php?go_on=1&id='+id+'&num='+document.getElementById('place'+id).value;}</script>";
	$res = f_MQuery( "SELECT items.*,player_items.number FROM player_items,items WHERE player_id={$player->player_id} AND items.item_id=player_items.item_id AND weared=0 AND nodrop=0" );
	echo "<script>\n";
	while( $arr = f_MFetch( $res ) )
	{
		echo "add_item( $arr[item_id], $arr[type], '$arr[name]', '".itemImage( $arr )."', '".itemFullDescr( $arr )."', $arr[number] );\n";
	}
	echo "document.write( render_items( true, 'doLot' ) );\n";
	echo "</script>\n";

	
	return;
}
else if( isset( $_GET['go_on'] ) )
{
	echo "<b>Выставить вещь на продажу</b> - <a href=game.php>Назад</a><br><small>Установите начальную ставку и шаг. Также укажите цену, за которую лот можно выкупить без торгов.</small><br><br>";

	$item_id = $_GET['id'];
	$number = $_GET['num'];
	settype( $item_id, 'integer' );
	settype( $number, 'integer' );
	$arr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$item_id AND nodrop=0" ) );
   	if( !$arr ) RaiseError( "Попытка поставить на аукцион несуществующую или непередаваемую вещь", "$item_id" );
	if( $number > 0 )
	{
    	$price = $arr['price'] * $number;
    	echo "<form action=game.php?fin=1 method=post><input type=hidden name=number value=$number><input type=hidden name=item_id value=$item_id><table><tr><td>Лот:</td><td>[$number] <b>$arr[name]</b></td></tr>";
    	echo "<tr><td>Начальная ставка:</td><td><input type=text name=start_price value=$price class=m_btn></td></tr>";
    	echo "<tr><td>Шаг:</td><td><input type=text name=step value=10 class=m_btn></td></tr>";
    	echo "<tr><td>Цена выкупа:</td><td><input type=text name=buy_price value=$price class=m_btn></td></tr>";
    	echo "<tr><td>Время торгов:</td><td><select name=duration class=m_btn><option value=1>1<option value=3>3<option value=6>6<option value=12 selected>12<option value=24>24</select> часов</td></tr>";
    	echo "<tr><td>&nbsp;</td><td><font color=darkred>Внимание!</font> Вам на руки будет выдано только <b><font color=blue>95%</font></b> стоимости покупки!<br><input type=submit value=Поставить class=s_btn></td></tr></table></form>";
		return;
	}
}
else if( $_GET['fin'] == 1 )
{
	echo "<b>Выставить вещь на продажу</b> - <a href=game.php>Назад</a><br><small>Подтвердите заявку на аукцион.</small><br><br>";
	$item_id = $_POST['item_id'];
	$number = $_POST['number'];
	$start_price = $_POST['start_price'];
	$step = $_POST['step'];
	$buy_price = $_POST['buy_price'];
	$duration = $_POST['duration'];
	settype( $item_id, 'integer' );
	settype( $number, 'integer' );
	settype( $start_price, 'integer' );
	settype( $step, 'integer' );
	settype( $buy_price, 'integer' );
	settype( $duration, 'integer' );

	$arr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$item_id AND nodrop=0" ) );
   	if( !$arr ) RaiseError( "Попытка поставить на аукцион несуществующую непередаваемую вещь", "$item_id" );
	if( $number > 0 && $start_price > 0 && $step > 0 && $buy_price > 0 )
	{
    	echo "<form action=game.php?fin=2 method=post><input type=hidden name=number value=$number><input type=hidden name=item_id value=$item_id><table><tr><td>Лот:</td><td>[$number] <b>$arr[name]</b></td></tr>";
    	echo "<tr><td>Начальная ставка:</td><td><input type=hidden name=start_price value=$start_price><img width=11 height=11 src=images/money.gif border=0> <b>$start_price</b></td></tr>";
    	echo "<tr><td>Шаг:</td><td><input type=hidden name=step value=$step><img width=11 height=11 src=images/money.gif border=0> <b>$step</b></td></tr>";
    	echo "<tr><td>Цена выкупа:</td><td><input type=hidden name=buy_price value=$buy_price><img width=11 height=11 src=images/money.gif border=0> <b>$buy_price</b></td></tr>";
    	echo "<tr><td>Время торгов:</td><td><input type=hidden name=duration value=$duration><b>$duration ".my_word_str( $duration, "час", "часа", "часов" )."</b></td></tr>";
    	echo "<tr><td>&nbsp;</td><td><font color=darkred>Внимание!</font> Вам на руки будет выдано только <b><font color=blue>95%</font></b> стоимости покупки (<b>аукцион забирает <font color=blue>5%</font></b> себе)!<br><input type=submit value=Подтвердить class=s_btn></td></tr></table></form>";
		return;
	}
}
else if( $_GET['fin'] == 2 )
{
	$item_id = $_POST['item_id'];
	$number = $_POST['number'];
	$start_price = $_POST['start_price'];
	$step = $_POST['step'];
	$buy_price = $_POST['buy_price'];
	$duration = $_POST['duration'];
	settype( $item_id, 'integer' );
	settype( $number, 'integer' );
	settype( $start_price, 'integer' );
	settype( $step, 'integer' );
	settype( $buy_price, 'integer' );
	settype( $duration, 'integer' );

	if( $duration != 1 && $duration != 3 && $duration != 6 && $duration != 12 && $duration != 24 )
	{
		RaiseError( "Попытка выставить неверное время торгов на аукционе" );
	}

	$arr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$item_id AND nodrop=0" ) );
   	if( !$arr ) RaiseError( "Попытка поставить на аукцион несуществующую или непередаваемую вещь", "$item_id" );
	if( $number > 0 && $start_price > 0 && $step > 0 && $buy_price > 0 )
	{
		// проверка на орденскую пометку
		if ( checkOrderItem ( $item_id ) != false )
		{
			echo "<font color=darkred>Выбранная вещь принадлежит ордену, выставить ее на аукцион нельзя. Извините.</font><br>";
		}
		else
		// -----8<----------
		{
    		if( !$player->DropItems( $item_id, $number ) )
    		{
    			echo "<font color=darkred>У вас нет этой вещи. Проверьте её наличие в Инвентаре и посмотрите, не одета ли она на Вас.</font><br>";
    		}
    		else
    		{
    			$player->AddToLogPost( $item_id, - $number, 15 );
        		$dd = time( ) + $duration * 60 * 60;
        		$cur_price = $start_price - $step;
        		f_MQuery( "INSERT INTO auction( player_id, item_id, number, start_price, step, immediately_price, cur_price, deadline ) VALUES ( {$player->player_id}, $item_id, $number, $start_price, $step, $buy_price, $cur_price, $dd )" );
        	}
    	}
	}
}

else if( isset( $_GET['bet'] ) )
{
	$tm = time( );
	$id = $_GET['bet'];
	settype( $id, 'integer' );
	f_MQuery( "LOCK TABLE auction WRITE" );
	$res = f_MQuery( "SELECT * FROM auction WHERE entry_id=$id AND deadline > $tm" );
	$arr = f_MFetch( $res );
	if( !$arr ) 
	{
		echo "<font color=darkred>Такого лота нет. Возможно, кто-то оказался быстрее...</font><br>";
		f_MQuery( "UNLOCK TABLES" );
	}
	else if( $arr['player_id'] == $player->player_id )
	{
		echo "<font color=darkred>Нельзя повысить ставку по своему лоту!</font><br>";
		f_MQuery( "UNLOCK TABLES" );
	}
	else if( $arr['last_bet_by'] == $player->player_id )
	{
		echo "<font color=darkred>Нельзя перебить свою ставку!</font><br>";
		f_MQuery( "UNLOCK TABLES" );
	}
	else
	{
		$price = $arr['cur_price'] + $arr['step'];
		if( $player->money >= $price )
		{
			f_MQuery( "UPDATE auction SET cur_price=$price, last_bet_by={$player->player_id} WHERE entry_id=$id" );
    		f_MQuery( "UNLOCK TABLES" );
			if( !$player->SpendMoney( $price ) ) LogError( "Игрок {$player->player_id} не смог оплатить повышение стоимости лота" );
			$player->AddToLogPost( 0, - $price, 15 );
			if( $arr['last_bet_by'] != 0 )
			{
				$plr = new Player( $arr['last_bet_by'] );
				$plr->AddMoney( $arr['cur_price'] );
				$plr->AddToLogPost( 0, $arr['cur_price'], 15 );

        		$iarr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$arr[item_id]" ) );
        		if( '' == $iarr['name13'] ) $iarr['name13'] = $iarr['name'];
        		if( '' == $iarr['name2_m'] ) $iarr['name2_m'] = $iarr['name'];
        		$nm = my_word_str( $arr['number'], $iarr['name'], $iarr['name13'], $iarr['name2_m'] );

				$plr->syst3( 'Ваша ставка по лоту &quot;<b>'.$arr[number].' '.$nm.'</b>&quot; на аукционе была перебита.' );
				$plr->syst2( '/items' );
			}
		}
		else
		{
    		echo "<font color=darkred>У вас не хватает денег.</font><br>";
    		f_MQuery( "UNLOCK TABLES" );
		}
	}
}

else if( isset( $_GET['buy'] ) )
{
	$tm = time( );
	$id = $_GET['buy'];
	settype( $id, 'integer' );
	f_MQuery( "LOCK TABLE auction WRITE" );
	$res = f_MQuery( "SELECT * FROM auction WHERE entry_id=$id AND deadline > $tm" );
	$arr = f_MFetch( $res );
	if( !$arr ) 
	{
		echo "<font color=darkred>Такого лота нет. Возможно, кто-то оказался быстрее Вас...</font><br>";
		f_MQuery( "UNLOCK TABLES" );
	}
	else if( $arr['player_id'] == $player->player_id )
	{
		echo "<font color=darkred>Нельзя выкупить свой же лот!</font><br>";
		f_MQuery( "UNLOCK TABLES" );
	}
	else
	{
		$price = $arr['immediately_price'];
		$ret = ceil( $price * 0.95 );
		if( $player->money >= $price )
		{
			f_MQuery( "DELETE FROM auction WHERE entry_id=$id" );
    		f_MQuery( "UNLOCK TABLES" );
			if( !$player->SpendMoney( $price ) ) LogError( "Игрок {$player->player_id} не смог оплатить повышение стоимости лота." );
			$player->AddToLogPost( 0, - $price, 15 );
			$player->AddItems( $arr['item_id'], $arr['number'] );
			$player->AddToLogPost( $arr['item_id'], $arr['number'], 15 );

        	include_once( "quest_race.php" );
        	updateQuestStatus ( $player->player_id, 2507 );
			
    		$iarr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$arr[item_id]" ) );
    		if( '' == $iarr['name13'] ) $iarr['name13'] = $iarr['name'];
    		if( '' == $iarr['name2_m'] ) $iarr['name2_m'] = $iarr['name'];
    		$nm = my_word_str( $arr['number'], $iarr['name'], $iarr['name13'], $iarr['name2_m'] );
    		if( $arr['number'] > 1 ) $nm = $arr['number'].' '.$nm; 

			if( $arr['last_bet_by'] != 0 )
			{
				$plr = new Player( $arr['last_bet_by'] );
				$plr->AddMoney( $arr['cur_price'] );
				$plr->AddToLogPost( 0, $arr['cur_price'], 15 );

				$plr->syst3( "Ваша ставка по лоту &quot;<b>".$nm."</b>&quot; на аукционе возвращена, так как <b>лот был <font color=blue>досрочно выкуплен</font></b>." );
				$plr->syst2( '/items' );
			}

			$plr = new Player( $arr['player_id'] );
			$plr->AddMoney( $ret );
			$plr->AddToLogPost( 0, $ret, 15 );
			$plr->syst3( "Игрок <b>{$player->login}</b> выкупил ваш лот (<b>{$nm}</b>) за <b>{$price}</b>. Вы получаете <b><font color=blue>{$ret}</font> ".my_word_str( $ret, "дублон", "дублона", "дублонов" )."!</b>" );
			$plr->syst2( "/items" );

			$player->syst( "Поздравляем, вы купили <b><font color=blue>{$nm}!</font></b>" );
		}
		else
		{
    		echo "<font color=darkred>У вас не хватает денег.</font><br>";
    		f_MQuery( "UNLOCK TABLES" );
		}
	}
}

$tm = time( );
if( $fnm != '' ) $res = f_MQuery( "SELECT auction.* FROM auction, items WHERE LOWER(items.name) LIKE LOWER('%{$fnm}%') AND items.item_id=auction.item_id AND deadline > $tm ORDER BY deadline LIMIT $start, 20" );
else if( $type == -1 ) $res = f_MQuery( "SELECT * FROM auction WHERE deadline > $tm ORDER BY deadline LIMIT $start, 20" );
else $res = f_MQuery( "SELECT auction.* FROM auction, items WHERE items.type = $type AND items.item_id=auction.item_id AND deadline > $tm ORDER BY deadline LIMIT $start, 20" );

echo "<b>Текущие лоты</b> - <a href=game.php?new_lot=1>Выставить вещь на продажу</a><br><br>";


function show_forms( )
{
	global $item_types;
	global $type;
	global $fnm;
	echo "<form action=game.php method=get>";
	echo "<b>Фильтр по типу вещей</b><br>";
	$item_types[-1] = "Нет фильтра";
	echo create_select_global( 'type', $item_types, $type );
	echo "<br><input class=s_btn type=submit value='Показать'>";
	echo "</form>";

	echo "<br>";

	echo "<form action=game.php method=get>";
	echo "<b>Фильтр по названию</b><br>";
	echo "<input class=m_btn name=nm value='$fnm' type=text>";
	echo "<br><input class=s_btn type=submit value='Показать'>";
	echo "</form>";

}

if( !f_MNum( $res ) ) { echo "<i>Нет ни одного лота.</i><br><br><br>"; show_forms( ); }

else
{
	if( $fnm != '' ) $res2 = f_MQuery( "SELECT count( auction.entry_id ) FROM auction, items WHERE LOWER(items.name) LIKE LOWER('%{$fnm}%') AND items.item_id=auction.item_id AND deadline > $tm" );
	else if( $type == -1 ) $res2 = f_MQuery( "SELECT count( entry_id ) FROM auction" );
	else $res2 = f_MQuery( "SELECT count( auction.entry_id ) FROM auction, items WHERE items.type = $type AND items.item_id=auction.item_id AND deadline > $tm" );
	$arr2 = f_MFetch( $res2 );
	$pnum = $arr2[0];

	echo "<script>FLUl();</script>";
	include_js( 'js/timer.js' );

	echo "<table><tr><td>";
	echo "<table>";

	while( $arr = f_MFetch( $res ) )
	{
		$num = '';
		if( $arr['number'] > 1 ) $num = "{$arr[number]} ";
		$iarr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$arr[item_id]" ) );
		$plr = new Player( $arr['player_id'] );
		if( '' == $iarr['name13'] ) $iarr['name13'] = $iarr['name'];
		if( '' == $iarr['name2_m'] ) $iarr['name2_m'] = $iarr['name'];

		$dc_str = '';
		if( $iarr['type'] > 1 && $iarr['type'] < 20 ) $dc_str = "<br><b>Уровень:</b> $iarr[level]<br><b>Прочность</b>: $iarr[decay]/$iarr[max_decay]";
		if( $iarr['charges_level'] ) $dc_str .= "<br><b>Заряжает:</b> $iarr[charges_level] ур. $iarr[charges_mk] ап.";
		if( $iarr['improved'] )
		{
			$stats = $player->getAllAttrNames( );
			$dc_str .= "<br>".ItemEffectStr( $iarr['effect'] );
		}


		echo "<tr><td width=150 align=center><img src=images/items/".itemImage( $iarr )."><br><a href=help.php?id=1010&item_id=$iarr[parent_id] target=_blank>{$num}".my_word_str( $arr['number'], $iarr['name'], $iarr['name13'], $iarr['name2_m'] )."</a>$dc_str</td>";
		echo "<td><table>";
		echo "<tr><td>Автор лота: </td><td colspan=2><script>document.write( ".$plr->Nick( )." );</script></td></tr>";
		if( $arr['last_bet_by'] != 0 )
		{
			echo "<tr><td>Текущая ставка: </td><td><img width=11 height=11 src=images/money.gif border=0> <b>$arr[cur_price]</b></td><td>&nbsp;</td></tr>";
			$bet = new Player( $arr['last_bet_by'] );
			echo "<tr><td>Автор ставки: </td><td colspan=2><script>document.write( ".$bet->Nick( )." );</script></td></tr>";
		}
		else echo "<tr><td>Стартовая цена: </td><td><img width=11 height=11 src=images/money.gif border=0> <b>$arr[start_price]</b></td><td>&nbsp;</td></tr>";
		echo "<tr><td>Шаг: </td><td><img width=11 height=11 src=images/money.gif border=0> <b>$arr[step]</b></td><td>&nbsp;</td></tr>";
		echo "<tr><td>След. ставка: </td><td><img width=11 height=11 src=images/money.gif border=0> <b>".( $arr['cur_price'] + $arr['step'] )."</b> </td><td><a href=game.php?bet=$arr[entry_id]&p={$page}{$get_lnk}>Ставка</a></td></tr>";
		echo "<tr><td>Цена покупки: </td><td><img width=11 height=11 src=images/money.gif border=0> <b>".( $arr['immediately_price'] )."</b> </td><td><a href=game.php?buy=$arr[entry_id]&p={$page}{$get_lnk}>Купить</a></td></tr>";
		$dd = $arr['deadline'] - time( );
		echo "<tr><td>До конца торгов: </td><td colspan=2><script>document.write( InsertTimer( $dd, '<b>', '</b>', 1, '' ) );</script></td></tr>";
		echo "</table>";
		echo "</td>";
		echo "</tr>";
	}

	echo "</table>";
	echo "</td><td vAlign=top>";

	show_forms( );

	echo "</td></tr></table>";

	echo "<script>FLL();</script>";

	if( $pnum > 20 )
	{
		echo "<center>Страница: ";
		for( $i = 0; $i * 20 < $pnum; ++ $i )
		{
			$ii = $i + 1;
			if( $i == $page ) echo "<b>$ii</b> ";
			else echo "<a href=game.php?p={$i}{$get_lnk}>$ii</a> ";
		}
		echo "</center>";
	}
}

?>
