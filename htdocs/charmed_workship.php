<?

if( !$mid_php )
{
	header("Content-type: text/html; charset=windows-1251");


	include( 'functions.php' );
	include( 'player.php' );

	f_MConnect( );
    if( !check_cookie( ) )
    	die( "Неверные настройки Cookie" );

    $player = new Player( $HTTP_COOKIE_VARS['c_id'] );

	if( isset( $_GET['id'] ) )
	{
		$id = (int)$_GET['id'];
		if( $_GET['do'] )
		{
			$do = $_GET['do'];
		}
		else
			$do = 1;
		if( $id != 2 && $id != 3 )
			if ($do == 1)
				RaiseError( "Попытка зарядить неверную вещь!", "$id" );
			elseif ($do == 2)
				RaiseError( "Попытка разрядить неверную вещь!", "$id" );
		$res = f_MQuery( "SELECT i.* FROM items as i INNER JOIN player_items as p ON i.item_id=p.item_id WHERE p.player_id={$player->player_id} AND p.weared=$id" );
		$arr = f_MFetch( $res );
		if( !$arr ) die( "alert( 'У вас нет оружия в выбранном слоте' );" );
		if( !$arr['inner_spell_id'] )  die( "alert( 'В выбранном оружии нет встроенного заклинания' );" );
		if ($do == 1)
			if( $arr['charges'] == $arr['max_charges'] ) die( "alert( 'Этот посох полностью заряжен' );" );
		$item_id = $arr['item_id'];

		$cres = f_MQuery( "SELECT genre FROM cards WHERE card_id=$arr[inner_spell_id]" );
		$carr = f_MFetch( $cres );
		if( !$carr ) RaiseError( "Неизвестный встроенный спелл!", "item_id: $arr[item_id], card_id: $arr[inner_spell_id]" );

		if( $carr[0] == 0 ) { $pot_id = 1291; $paint_id = 274; $pot_img = 'res/po_sm6.gif'; $paint_img = 'res/bottle_blue.gif'; $pot_suf = ' воды';  $paint_clr = ' синей'; }
		if( $carr[0] == 1 ) { $pot_id = 1292; $paint_id = 275; $pot_img = 'res/po_sm5.gif'; $paint_img = 'res/bottle_green.gif'; $pot_suf = ' природы';  $paint_clr = ' зеленой'; }
		if( $carr[0] == 2 ) { $pot_id = 1293; $paint_id = 276; $pot_img = 'res/po_sm4.gif'; $paint_img = 'res/bottle_red.gif';  $pot_suf = ' огня';   $paint_clr = ' красной'; }

		if( !$_GET['do'] )
		{
			
			$st = '<b>Вам понадобится:</b><br><br>';
			$st .= "<img width=11 height=11 src=images/items/$pot_img> Слабое зелье $pot_suf;<br>";
			$st .= "<img width=11 height=11 src=images/items/$paint_img> 4 бутылочки $paint_clr краски;<br>";
			$st .= "<img width=11 height=11 src=images/money.gif> 200 дублонов<br>";
			$st .= "<br><a href='javascript:charge2($id)'>Зарядить</a><br>";
			$st .= "<a href='javascript:show_charge_list()'>Назад</a><br>";
			$st = AddSlashes( $st );

			echo "_( 'charge' ).innerHTML = '$st';";
			echo "show_charge_div( );";
			

		}
		else
		{
			if ($do == 1)
			{
				if( $player->NumberItems( $pot_id ) == 0 ) die( "alert( 'У вас нет слабого зелья $pot_suf' );" );
				else if( $player->NumberItems( $paint_id ) < 4 ) die( "alert( 'У вас нет краски' );" );
				else if( !$player->SpendMoney( 200 ) ) die( "alert( 'У вас недостаточно дублонов' );" );
				else if( !$player->DropItems( $pot_id, 1 ) ) RaiseError( 'Персонаж потерял пот по ходу улучшение посоха' );
				else if( !$player->DropItems( $paint_id, 4 ) ) RaiseError( 'Персонаж потерял краску по ходу улучшение посоха' );
				else
				{
					$player->AddToLogPost( 0, -200, 28 );
					$player->AddToLogPost( $pot_id, -1, 28 );
					$player->AddToLogPost( $paint_id, -4, 28 );
					f_MQuery( "UPDATE items SET charges=max_charges WHERE item_id=$item_id" );
					echo "window.top.char_ref.location.href='char_ref.php';";
					die( "alert( 'Посох успешно заряжен' );show_charge_list( );" );
				}
			}
			elseif ($do == 2)
			{
				f_MQuery( "UPDATE items SET charges=max_charges, charges_spent=0, improved=0, inner_spell_id=0 WHERE item_id=$item_id" );
				f_MQuery("DELETE FROM player_selected_cards WHERE player_id={$player->player_id} AND card_id={$arr['inner_spell_id']} AND staff=1 LIMIT 1");
				f_MQuery("UPDATE player_cards SET number=number-1 WHERE player_id={$player->player_id} AND card_id={$arr['inner_spell_id']} AND number>10");
				f_MQuery("DELETE FROM player_cards WHERE player_id={$player->player_id} AND card_id={$arr['inner_spell_id']} AND number<10");
				echo "window.top.char_ref.location.href='char_ref.php';";
				die( "alert( 'Очистка прошла успешно' );show_charge_list( );" );
			}
		}
	}
	die( "/**/" );
}

$stats = $player->getAllAttrNames( );

echo "<center><table width=80%><tr>";

echo "<td width=35% valign=top>";

echo "<script>FUct();</script>";
echo "<b>Зарядка посохов</b>";
echo "<script>FL();</script>";

echo "</td><td width=65%>";

echo "<script>FUct();</script>";
echo "<b>Встраивание Заклинаний</b>";
echo "<script>FL();</script>";

echo "</td></tr><tr><td height=200>";
// staff charging

echo "<script>FUct();</script>";
	echo "<div id=charge_list>";
	$a1 = $a2 = false;
	echo "<br><table cellspacing=0 cellpadding=0><tr><td background=images/items/bg.gif style='width:50px;height:50px;'>";
		$res = f_MQuery( "SELECT i.image FROM items as i INNER JOIN player_items as p ON i.item_id=p.item_id WHERE p.player_id={$player->player_id} AND p.weared=2" );
		$arr = f_MFetch( $res );
		if( $arr )
		{
			echo "<img border=0 width=50 height=50 src=images/items/$arr[0]>";
			$a1 = true;
		}
   	echo "</td><td><img width=20 height=1 src=empty.gif></td><td background=images/items/bg.gif style='width:50px;height:50px;'>";
		$res = f_MQuery( "SELECT i.image FROM items as i INNER JOIN player_items as p ON i.item_id=p.item_id WHERE p.player_id={$player->player_id} AND p.weared=3" );
		$arr = f_MFetch( $res );
		if( $arr )
		{
			echo "<img border=0 width=50 height=50 src=images/items/$arr[0]>";
			$a2 = true;
		}
   	echo "</td></tr><tr><td align=center>";
   		if( $a1 )  echo "<a href='javascript:charge(2)'>Дальше</a><br><a href='#' onclick='if( confirm( \"Освободить оружие от заклинания?\" ) ) location.href=\"javascript:disspellfrom(2)\";'>Очистить</a>";
   	echo "</td><td></td><td align=center>";
   		if( $a2 )  echo "<a href='javascript:charge(3)'>Дальше</a><br><a href='#' onclick='if( confirm( \"Освободить оружие от заклинания?\" ) ) location.href=\"javascript:disspellfrom(3)\";'>Очистить</a>";
   	echo "</td></tr></table>";
   	echo "<br><small>Вы можете зарядить любой посох или другое оружие со встроенным заклинанием, которое вы держите в руках.</small>";
   	echo "</div>";
   	echo "<div id=charge style='display:none'>&nbsp;";
   	echo "</div>";
echo "<script>FL();</script>";

echo "</td>";
// staff charging end

// weapon improving
echo "<td width=65% height=200 valign=top>";

echo "<script>FUct();</script>";

if( $player->level >= 5 )
{
	if( !isset( $_GET['weapon_id'] ) )
	{
		$res = f_MQuery( "SELECT items.* FROM player_items INNER JOIN items ON player_items.item_id = items.item_id WHERE items.type = 2 AND player_items.player_id = {$player->player_id} AND weared = 0 AND inner_spell_id = 0" );
		$rows = floor( ( f_MNum( $res ) + 7 ) / 8 );
		if( $rows == 0 ) echo "<i>У вас нет ни одного оружия или щита</i>";
		else
		{
			echo "<small><b>Выберите вещь, в которую вы хотите встроить заклинание.</b><br><br></small>";
			echo "<table cellspacing=0 cellpadding=0>";
			for( $i = 0; $i < $rows; ++ $i )
			{
				if( $i ) echo "<tr><td colspan=15><img width=1 height=5 src='empty.gif'></td></tr>";
				echo "<tr>";
				for( $j = 0; $j < 8; ++ $j )
				{
					if( $j ) echo "<td><img width=5 height=1 src=empty.gif></td>";
					echo "<td background=images/items/bg.gif style='width:50px;height:50px;'>";
					$arr = f_MFetch( $res );
					if( $arr ) echo "<img style='cursor:pointer' onclick='location.href=\"game.php?weapon_id={$arr[item_id]}\"' onmouseout=\"hideTooltip(event)\" onmousemove=\"showTooltipW(event,'".addslashes(itemFullDescr( $arr ))."',300)\" width=50 height=50 src='images/items/$arr[image]'>";
					else echo "&nbsp;";
					echo "</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
		}
	}
	else if( !isset( $_GET['scroll_id'] ) )
	{
		$res = f_MQuery( "SELECT items.* FROM player_items INNER JOIN items ON player_items.item_id = items.item_id WHERE items.type = 21 AND player_items.player_id = {$player->player_id} AND weared = 0 AND items.item_id NOT IN( 46193, 46608 )" );
		$rows = floor( ( f_MNum( $res ) + 7 ) / 8 );
		if( $rows == 0 ) echo "<i>У вас нет ни одного свитка с заклинанием</i>";
		else
		{
			echo "<small><b>Выберите заклинание, которое вы хотите встроить.</b><br><br></small>";
			echo "<table cellspacing=0 cellpadding=0>";
			for( $i = 0; $i < $rows; ++ $i )
			{
				if( $i ) echo "<tr><td colspan=15><img width=1 height=5 src='empty.gif'></td></tr>";
				echo "<tr>";
				for( $j = 0; $j < 8; ++ $j )
				{
					if( $j ) echo "<td><img width=5 height=1 src=empty.gif></td>";
					echo "<td background=images/items/bg.gif style='width:50px;height:50px;'>";
					$arr = f_MFetch( $res );
					if( $arr )
					{
						$carr = f_MFetch( f_MQuery( "SELECT * FROM cards WHERE card_id = $arr[learn_spell_id]" ) );
						echo "<img style='cursor:pointer' onclick='location.href=\"game.php?weapon_id={$_GET[weapon_id]}&scroll_id={$arr[item_id]}\"' onmouseout=\"hideTooltip(event)\" onmousemove=\"showTooltipW(event,'".addslashes(str_replace('"',"'","<b>{$carr[name]}</b><br>{$carr[descr2]}<br><i>{$carr[descr]}</i>"))."',300)\" width=50 height=50 src='images/items/$arr[image]'>";
					}
					else echo "&nbsp;";
					echo "</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
			echo "<br><br><a href='game.php'>Назад к выбору оружия</a>";
		}
	}
	else
	{
		$scroll_id = (int)$_GET['scroll_id'];
		$weapon_id = (int)$_GET['weapon_id'];
		$card_id = f_MValue( "SELECT learn_spell_id FROM items WHERE item_id={$scroll_id}" );
		if( !$card_id ) die( "Произошла ошибка - свиток не изучает никакое заклинание" );
		
		$item_lvl = f_MValue( "SELECT level FROM items WHERE item_id={$weapon_id}" );
		
		$card_arr = f_MFetch( f_MQuery( "SELECT * FROM cards WHERE card_id={$card_id}" ) );
		$card_genre = $card_arr['genre'];
		$card_lvl = $card_arr['level'];
		$card_name = $card_arr['name'];
		if( $card_genre < 0 || $card_genre > 2 ) die( "Произошла ошибка - стихия заклинания неверная" );
		
		$price = array( );
		$price[0] = $item_lvl * 1000;
		
		// сначала определим пот
		if( $card_lvl < 8 )
		{
			$pot_id = 1291 + $card_genre;
			$price[$pot_id] = 3 * max( 1, $card_lvl - 4 );
		}
		else if( $card_lvl < 11 )
		{
			$pot_id = 1294 + $card_genre;
			$price[$pot_id] = 3 * max( 1, $card_lvl - 7 );
		}
		else if( $card_lvl < 14 )
		{
			$pot_id = 13970 + $card_genre;
			$price[$pot_id] = 3 * max( 1, $card_lvl - 10 );
		}
		else
		{
			$pot_id = 44602 + $card_genre;
			$price[$pot_id] = 3 * max( 1, $card_lvl - 13 );
		}
		
		// далее камушек
		$cheap_stones = array( 41, 103, 42 );
		$exp_stones = array( 43, 105, 104 );
		
		$num1 = 2 + $card_lvl * 3 + floor( $item_lvl / 5 );
		$num2 = floor( $num1 / 5 );
		$num1 %= 5;
		if( !$num1 ) ++ $num1;
		
		if( $num1 ) $price[$cheap_stones[$card_genre]] = $num1;
		if( $num2 ) $price[$exp_stones[$card_genre]] = $num2;
		
		if( !isset( $_GET['confirm'] ) )
		{
			echo "Вы хотите встроить заклинание <b>$card_name</b> в вещь <b>".f_MValue( "SELECT name FROM items WHERE item_id={$weapon_id}" )."</b><br><br>Для того, чтобы это сделать, вам понадобится:<br><br>";
			echo "<table cellspacing=0 cellpadding=0><tr>";
			
			$first = true;
			foreach( $price as $a => $b )
			{
				if( !$first ) echo "<td><img width='5' height='1' src='empty.gif'></td>";
				$first = false;
			
				if( !$a ) { $src = 'images/money.gif'; $title = 'Дублоны'; }
				else { $src = "images/items/".f_MValue( "SELECT image FROM items WHERE item_id=$a" ); $title = f_MValue( "SELECT name FROM items WHERE item_id=$a" ); }
				echo "<td style='width:50px;height:50px;' align=center valign=middle background=images/items/bg.gif><img src='$src' alt='$title' title='$title'></td>";
			}
			
			echo "</tr><tr>";
			
			$first = true;
			foreach( $price as $a => $b )
			{
				if( !$first ) echo "<td><img width='5' height='1' src='empty.gif'></td>";
				$first = false;
			
				echo "<td align=center><b><small>$b</small></b></td>";
			}
			
			echo "</tr></table>";
			
			echo "<br><br><a href='game.php?weapon_id={$_GET[weapon_id]}&scroll_id={$scroll_id}&confirm=1'>Встроить заклинание</a>";
			
			echo "<br><br><a href='game.php?weapon_id={$_GET[weapon_id]}'>Назад к выбору заклинания</a>";
		}
		else
		{
			$err = '';
			foreach( $price as $a=>$b )
			{
				if( !$a )
				{
					if( $player->money < $b ) $err .= "У вас недостаточно золота<br>";
					else $player->AddToLogPost( $a, -$b, 28 );
				}
				else
				{
					if( $player->NumberItems( $a ) < $b ) $err .= "Недостаточно ".f_MValue( "SELECT name2_m FROM items WHERE item_id={$a}" )."<br>";
					else $player->AddToLogPost( $a, -$b, 28 );
				}
			}
			if( !$player->NumberItems( $weapon_id ) ) $err .= "У вас нет оружия, в которое вы хотите встроить заклинание<br>";
			if( !$player->NumberItems( $scroll_id ) ) $err .= "У вас нет свитка, который вы хотите встроить<br>";

			if( $err != "" )
			{
				echo "<br><font color=darkred>$err</font><br><br><a href='game.php'>Назад</a>";
			}
			else
			{
    			foreach( $price as $a=>$b )
    			{
    				if( !$a )
    				{
    					$player->SpendMoney( $b );
    					$player->AddToLogPost( $a, -$b, 28 );
    				}
    				else
    				{
    					$player->DropItems( $a, $b );
    					$player->AddToLogPost( $a, -$b, 28 );
    				}
    			}
    			if( $player->DropItems( $weapon_id ) )
    			{
    				$player->AddToLogPost( $weapon_id, -1, 28 );
        			if( $player->DropItems( $scroll_id ) )
        			{
        				$player->AddToLogPost( $scroll_id, -1, 28 );
            			$item_id = copyItem( $weapon_id, true );
            			f_MQuery( "UPDATE items SET inner_spell_id = $card_id WHERE item_id=$item_id" );
            			$player->AddItems( $item_id );
            			$player->AddToLogPost( $item_id, 1, 28 );
            		}
        		}
    			
    			echo "<b><font color='darkgreen'>Заклинание успешно встроено</font></b><br><br><a href='game.php'>Назад</a>";
			}
		}
	}
}
else
{
	echo "<i>Встраивать заклинания могут только игроки пятого уровня и выше</i>";
}

echo "<script>FL();</script>";

echo "</td>";

// weapon improving end

echo "</tr></table></center>";

?>

<script>

function charge( id )
{
	query( 'charmed_workship.php?id=' + id, '' );
}

function charge2( id )
{
	query( 'charmed_workship.php?do=1&id=' + id, '' );
}

function disspellfrom(id)
{
	query( 'charmed_workship.php?do=2&id=' + id, '' );
}

function show_charge_div( )
{
	_( 'charge' ).style.display = '';
	_( 'charge_list' ).style.display = 'none';
}

function show_charge_list( )
{
	_( 'charge' ).style.display = 'none';
	_( 'charge_list' ).style.display = '';
}

</script>
