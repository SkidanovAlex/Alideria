<?

if( !$mid_php )
{
	header("Content-type: text/html; charset=windows-1251");
	include_once( 'functions.php' );
	include_once( 'player.php' );
	include_once( 'wear_items.php' );
	include_once( 'feathers.php' );

	f_MConnect( );

    if( !check_cookie( ) )
    	die( "Неверные настройки Cookie" );

    $player = new Player( $HTTP_COOKIE_VARS['c_id'] );
    
	$moo = 0;
	$stats = $player->getAllAttrNames( );

    $msg = "";
    if( isset( $HTTP_GET_VARS[item] ) )
    {
    	$item_id = (int)$HTTP_GET_VARS[item];
    	$w = (int)$HTTP_GET_VARS[w];
    	
    	if( isset( $HTTP_GET_VARS['use'] ) )
    	{
    		if( $w ) // Снятие вещи
    		{
    			$q = UnWearItem( $w );
    			if( $q == -1 ) $msg = "При прекращении использования вещи произошла ошибка: Вы не используете эту вещь";
    			if( $q == -2 ) $msg = "При прекращении использования вещи произошла ошибка: Вы обязаны держать факел, находясь глубоко в пещерах";
    			if( $q == 0 )
    			{
    				print( "parent.char_ref.unwear( $w );" );
					print( "alter_item( $item_id, $w, -1 );" );
					print( "alter_item( $item_id, 0, 1 );" );
    				$w = 0;
    			}
    		}
    		else // Использование вещи
    		{
    			$q = WearItem( $item_id );
			if( $q == -3 && f_MValue( "SELECT type FROM items WHERE item_id=$item_id" ) == 25 )
		            	{
            				if( canUseFeather( $player ) ) echo "location.href='game.php?feather_id={$item_id}';";
		            	}
	            		else if( $q == -3 && f_MValue( "SELECT type FROM items WHERE item_id=$item_id" ) == 23 )
	            		{
		            		include_once( "instant_effects.php" );
            				if( $player->DropItems( $item_id ) )
            				{
            					if( !useInstant( $item_id ) )
		            			{
            						$player->AddItems( $item_id );
		            			}
		            			else
            					{
						print( "alter_item( $item_id, 0, -1 );" );
		            			}
            			
			            		if( $last_instant_error != "" )
	            				{
            						echo "alert( '".addslashes($last_instant_error)."' );";
						if ($item_id == 75474) // Сила Стихий
						echo "location.href='game.php'";
            					}
		            		}
		            	}
    			else if( $q < 0 ) $msg = getWearMessage( $q );
    			else if( $q > 0 )
    			{
    				$res = f_MQuery( "SELECT * FROM items WHERE item_id = $item_id" );
    				$arr = f_MFetch( $res );
    				$descr = itemFullDescr2( $arr );
    				print( "parent.char_ref.wear( $arr[item_id], '$arr[name]', '$descr', '$arr[image]', $q );" );
					print( "alter_item( $item_id, $q, 1 );" );
					print( "alter_item( $item_id, 0, -1 );" );

    				if( $player->level == 1 )
    				{
    					include_once( 'player_noobs.php' );
    					PingNoob( 3 );
    				}

    				$w = $q;
    			}
    		}
    	}
    	
    	if( isset( $HTTP_GET_VARS['drop'] ) )
    	{
    		include( 'location_functions.php' );
    		if( $w ) $msg = "При выкидывании вещи произошла ошибка: Нельзя выкинуть используемую вещь";
    		else if( $player->location == 0 && $player->depth == 33 )$msg = "При выкидывании вещи произошла ошибка: Нельзя выкинуть вещь в лабиринте";
    		else
    		{
				// проверим шмотку на принадлежность ордену
				if ( !checkOrderItem ( $item_id ) )
				{
					if( $player->regime )
						print( "При выкидывании вещи произошла ошибка: Вы заняты" );
					else if( !( $player->DropItems( $item_id ) ) )
						print( "При выкидывании вещи произошла ошибка: У вас нет этой вещи" );
					else 
					{
						$player->AddToLog( $item_id, -1, 3, $player->location, $player->depth );
						if( $player->level >= 3 ) LocationAddItems( $player->location, $player->depth, $item_id, 1 );
						print( "alter_item( $item_id, 0, -1 );" );
					}
				}
				else
				{
					print( "Вы зря пытаетесь выбросить орденскую вещь, этого делать нельзя." );
				}
				// -----8<----------
    		}
    	}
    	
    	else if( isset( $HTTP_GET_VARS['dropall'] ) )
    	{
    		include( 'location_functions.php' );
    		if( $w ) $msg = "При выкидывании вещи произошла ошибка: Нельзя выкинуть используемую вещь";
    		else if( $player->location == 0 && $player->depth == 33 )$msg = "При выкидывании вещи произошла ошибка: Нельзя выкинуть вещь в лабиринте";
    		else
    		{
    			$number = $player->NumberItems( $item_id );
    			if( $player->regime )
    				print( "При выкидывании вещи произошла ошибка: Вы заняты" );
    			else if( !( $player->DropItems( $item_id, $number ) ) )
    				print( "При выкидывании вещи произошла ошибка: У вас нет этой вещи" );
    			else
    			{
    				$player->AddToLogPost( $item_id, -$number, 3, $player->location, $player->depth );
    				if( $player->level >= 3 ) LocationAddItems( $player->location, $player->depth, $item_id, $number );
					print( "alter_item( $item_id, 0, -$number );" );
    			}
    		}
    	}
    	
    	$res = f_MQuery( "SELECT * FROM player_items WHERE item_id = $item_id AND weared = $w AND player_id={$player->player_id}" );
    	$arr = f_MFetch( $res );
    	if( $arr )
    	{
    		$ires = f_MQuery( "SELECT * FROM items WHERE item_id = $item_id" );
    		$iarr = f_MFetch( $ires );

    		if( $w && $iarr['charges_level'] != 0 && $_GET['spell_up'] != 0 )
    		{
    			$spell_id = (int)$_GET['spell_up'];
    			$qres = f_MQuery( "SELECT c.parent, c.card_id, p.number FROM cards as c INNER JOIN player_cards as p ON c.card_id=p.card_id WHERE player_id={$player->player_id} AND genre=$iarr[charges_genre] AND level=$iarr[charges_level] AND multy=0 AND c.card_id=$spell_id" );
    			$qarr = f_MFetch( $qres );
    			if( !$qarr ) RaiseError( "Попытка улучшить заклинание, которого нет в книге", "$spell_id kevek: $iarr[charges_level] ap: $iarr[charges_mk] genre: $iarr[charges_genre]" );
    			if( $qarr[2] != 10 ) echo "alert( 'Нельзя улучшить заклинание, которое встроено в одетый посох.' );";
    			else
    			{
        			if( $qarr[0] == 0 ) { $qarr[0] = $spell_id; $qarr['parent'] = $spell_id; }
        			$cres = f_MQuery( "SELECT card_id FROM cards WHERE parent=$qarr[0] AND mk=$iarr[charges_mk]" );
        			$carr = f_MFetch( $cres );
        			if( !$carr ) RaiseError( "Заклинания нужной модификации не существует", "$spell_id, $iarr[charges_mk]" );
        			f_MQuery( "UPDATE player_cards SET card_id=$carr[0] WHERE card_id=$qarr[1] AND player_id={$player->player_id}" );
        			f_MQuery( "UPDATE player_selected_cards SET card_id=$carr[0] WHERE card_id=$qarr[1] AND player_id={$player->player_id}" );
        			
    //    			$item_id = f_MValue( "SELECT items AS i INNER JOIN player_items AS p ON i.item_id=p.item_id WHERE p.player_id={$player->player_id} AND i.inner_spell_id={$qarr[1]}" );
    //    			if( $item_id ) f_MQuery( "UPDATE items SET inner_spell_id=$carr[0] WHERE inner_spell_id=$qarr[1]" );

        			// drop staff
        			f_MQuery( "UPDATE player_items SET item_id=$iarr[parent_id] WHERE player_id={$player->player_id} AND weared=$w" );
        			$player->AddToLogPost( $item_id, -1, 31 );
        			$player->AddToLogPost( $iarr['parent_id'], 1, 31, $spell_id );
        			f_MQuery( "DELETE FROM items WHERE item_id=$item_id" );
        			$item_id = $iarr['parent_id'];
        			$ires = f_MQuery( "SELECT * FROM items WHERE item_id = $item_id" );
        			$iarr = f_MFetch( $ires );
        			print( "parent.char_ref.unwear( $w );" );
        			$zres = f_MQuery( "SELECT * FROM items WHERE item_id = $item_id" );
        			$zarr = f_MFetch( $zres );
        			$descr = itemFullDescr2( $zarr );
        			print( "parent.char_ref.wear( $zarr[item_id], '$zarr[name]', '$descr', '$zarr[image]', $w );" );
    			}
    		}
//	if ($player->player_id==6825)
		if ($w && $iarr['charges_level'] != 0 && $_GET['item_spell_up'] != 0)
		{
if ($iarr[charges_mk] > 4) RaiseError("Нельзя апать на пятый уровень!!!");
			$item_spell_id = (int)$_GET['item_spell_up'];
$temmp = "SELECT c.parent, c.card_id, c.name, c.mk, i.item_id, i.name, p.weared FROM cards AS c, items AS i INNER JOIN player_items AS p ON p.item_id = i.item_id WHERE c.card_id = i.inner_spell_id AND p.weared >0 AND p.weared <> {$w} AND p.player_id ={$player->player_id} AND c.genre =$iarr[charges_genre] AND c.level =$iarr[charges_level] AND multy=0 AND i.item_id={$item_spell_id}";
			$iqres = f_MQuery( $temmp );
			$iqarr = f_MFetch($iqres);
			if ($iqarr[0]==0) $iqarr[0]=$iqarr[1];
			if (!$iqarr) 
				{$player->syst2('ping3 '.$temmp);RaiseError("Попытка улучшить заклинание в неправильном предмете $item_spell_id");}
			if ($iarr['charges_mk'] != $iqarr[3] + 1 && $iarr['charges_mk'] != $iqarr[3] - 1)
{
				RaiseError( "Предмета с заклинанием нужной модификации не существует", "$item_spell_id, $iarr[charges_mk]" );}
$temmp = "SELECT card_id FROM cards WHERE parent=$iqarr[0] AND mk=$iarr[charges_mk]";
			$icres = f_MValue( $temmp );
			if ( $icres == 0)
{
				RaiseError("Попытка апнуть закл в предмете до несуществующего");}
			else
			{

			UnWearItem($iqarr[6], true);
			print( "parent.char_ref.unwear( $iqarr[6] );" );
			f_MQuery( "UPDATE items SET inner_spell_id={$icres} WHERE item_id={$iqarr[4]}" );
			$player->AddToLogPost( $item_spell_id, -1, 31 );
			$player->AddToLogPost( $item_spell_id, -1, 31, $item_spell_id );
			//WearItem($iqarr[4]);

			// drop staff
        			f_MQuery( "UPDATE player_items SET item_id=$iarr[parent_id] WHERE player_id={$player->player_id} AND weared=$w" );
        			$player->AddToLogPost( $item_id, -1, 31 );
        			$player->AddToLogPost( $iarr['parent_id'], 1, 31, $item_spell_id );
        			f_MQuery( "DELETE FROM items WHERE item_id=$item_id" );
        			$item_id = $iarr['parent_id'];
        			$ires = f_MQuery( "SELECT * FROM items WHERE item_id = $item_id" );
        			$iarr = f_MFetch( $ires );
        			print( "parent.char_ref.unwear( $w );" );
        			$zres = f_MQuery( "SELECT * FROM items WHERE item_id = $item_id" );
        			$zarr = f_MFetch( $zres );
        			$descr = itemFullDescr2( $zarr );
        			print( "parent.char_ref.wear( $zarr[item_id], '$zarr[name]', '$descr', '$zarr[image]', $w );" );

    			}
die('location.href=\'game.php\'');
		}

    		if( $msg ) print( "alert( '".addslashes( $msg )."' );" );
    		print( "_( 'item' ).innerHTML = rFLUl() + \"" );
    		print( "<table><tr><td vAlign=top><table width=50 height=50 cellspacing=0 cellpadding=0 border=0><tr><td background=images/items/bg.gif align=center vAlign=center><img src=images/items/".itemImage( $iarr )."></td></tr></table></td><td vAlign=top>" );
    		print( "<a href=help.php?id=1010&item_id=$iarr[item_id] target=_blank><b>$iarr[name]</b></a><br>" );
    		if( !$w ) print( "<b>Количество: </b>$arr[number]<br>" );
    		else print( "В настоящий момент используется<br>" );

    		if( $iarr['effect'] && $iarr['type'] != 25 )
    		{
    			print( "<br><b>Эффект:</b><br>" );
    			print( ItemEffectStr( $iarr[effect] ) );
    		}

    		if( $iarr[req] )
    		{
    			print( "<br><b>Требует:</b><br>" );
    			print( ItemReqStr( $iarr[req] ) );
    		}
    		if( $iarr['type'] > 1 && $iarr['type'] < 20 ) echo "<b>Прочность</b>: $iarr[decay]/$iarr[max_decay]<br>";
    		echo "<b>Гос. цена: </b><img src='images/money.gif' width=11 height=11 border=0> $iarr[price]<br>";

    		$gnr = array( "вода", "природа", "огонь" );
    		if( $iarr['charges_level'] != 0 ) echo "<br><b>Заряжен на улучшение:</b><br>Уровень: $iarr[charges_level]<br>Улучшение: $iarr[charges_mk]<br>Стихия: {$gnr[$iarr[charges_genre]]}<br>";

    		if( $iarr[learn_recipe_id] )
    		{
    			print( "<br><b>Изучает рецепт:</b><br>" );
    			include( 'craft_functions.php' );
    			$rres = f_MQuery( "SELECT * FROM recipes WHERE recipe_id = $iarr[learn_recipe_id]" );
    			if( !f_MNum( $rres ) ) LogError( "Вещь $iarr[item_id] изучает несуществующий рецепт $iarr[learn_recipe_id]" );
    			print( addslashes( outRecipes( $rres ) ) );
    		}

    		print( "<ul>" );
    		if( !$w ) print( "<li><a href='javascript:drop($item_id);'>Выбросить</a>" );
    		if( !$w && $arr[number] > 1 ) print( "<li><a href='javascript:dropall($item_id);'>Выбросить все</a>" );
    		if( !$w )
    		{
    			if( $iarr[learn_spell_id] ) print( "<li><a href='javascript:i_do(\\\"item=$item_id&w=$w&use\\\")'>Выучить заклинание</a>" );
    			else if( $iarr[learn_recipe_id] ) print( "<li><a href='javascript:i_do(\\\"item=$item_id&w=$w&use\\\")'>Выучить рецепт</a>" );
    			else print( "<li><a href='javascript:i_do(\\\"item=$item_id&w=$w&use\\\")'>Использовать</a>" );
    		}
    		if( $w ) print( "<li><a href='javascript:i_do(\\\"item=$item_id&w=$w&use\\\")'>Прекратить использовать</a>" );
    		if( $w && $iarr['charges_level'] != 0 )
    		{
    			$res = f_MQuery( "SELECT c.card_id, c.name, c.mk FROM cards as c INNER JOIN player_cards as p ON c.card_id=p.card_id WHERE player_id={$player->player_id} AND genre=$iarr[charges_genre] AND level=$iarr[charges_level] AND multy=0 AND p.number=10" );
    			while( $arr = f_MFetch( $res ) )
    			{
    				$new_name = $arr['name'] . ", ап $iarr[charges_mk]";
    				if( $arr['mk'] > 0 ) $arr['name'] .= ", ап $arr[mk]";
    				if( $arr['mk'] < $iarr[charges_mk] ) echo "<li><a href='javascript:i_do(\\\"item=$item_id&w=$w&spell_up=$arr[card_id]\\\")'>Улучшить <b>$arr[name]</b> до <b>$new_name</b></a>";
    				elseif( $arr['mk'] > $iarr[charges_mk] ) echo "<li><a href='javascript:i_do(\\\"item=$item_id&w=$w&spell_up=$arr[card_id]\\\")'>Ухудшить <b>$arr[name]</b> до <b>$new_name</b></a>";
    			}
//		if ($player->player_id==6825)
		{
//			echo "<li> Метка";
			$res = f_MQuery( "SELECT c.card_id, c.name, c.mk, i.item_id, i.name FROM cards AS c, items AS i INNER JOIN player_items AS p ON p.item_id = i.item_id WHERE c.card_id = i.inner_spell_id AND p.weared >0 AND p.player_id ={$player->player_id} AND c.genre =$iarr[charges_genre] AND c.level =$iarr[charges_level] AND multy=0" );
			while ($arr = f_MFetch($res))
			{
				$new_name = $arr[1] . ", ап $iarr[charges_mk]";
				if( $arr[2] > 0 ) $arr[1] .= ", ап $arr[2]";
				if( $arr[2] < $iarr[charges_mk] ) echo "<li><a href='javascript:i_do(\\\"item=$item_id&w=$w&item_spell_up=$arr[3]\\\")'><b>$arr[4]</b>: Улучшить <b>$arr[1]</b> до <b>$new_name</b></a>";
    				elseif( $arr[2] > $iarr[charges_mk] ) echo "<li><a href='javascript:i_do(\\\"item=$item_id&w=$w&item_spell_up=$arr[3]\\\")'><b>$arr[4]</b>: Ухудшить <b>$arr[1]</b> до <b>$new_name</b></a>";
			}
		}
    		}
    		print( "<li><a href='javascript:hide_item()'>Закрыть</a><br>" );
    		print( "</ul></td></tr></table>" );
    		print( "\" + rFLL();" );
    		
    		$moo = 1;
    	}
    }
    if( $moo ) echo "_( 'item' ).style.display = '';";
    else echo "_( 'item' ).style.display = 'none';";
    ?>
    parent.char_ref.show_char( _( 'char_items' ) );
	char_set_events( );
	_( 'inv_items' ).innerHTML = get_inv_html( );
	set_inv_events( );
	<?

    die( );
}
	
$stats = $player->getAllAttrNames( );

include_once( 'items.php' );
include_once( 'wear_items.php' );
include_once( "skin.php" );
include_js( "js/skin2.js" );

if( isset( $_GET['feather_id'] ) )
{
	include( 'feathers_ui.php' );
	return;
}

$res = f_MQuery( "SELECT items.*,player_warehouse_items.number FROM player_warehouse_items,items WHERE player_id={$player->player_id} AND items.item_id=player_warehouse_items.item_id" );
if( isset( $_GET['warehouse'] ) )
{
	include( "warehouse_show.php" );
	return;
}

if( f_MNum( $res ) ) $ware = 1;
else $ware = 0;

$total = 0;
//print( "<center><table width=90% cellspacing=0 cellpadding=0><tr><td>" );

	$res = f_MQuery( "SELECT items.*, player_items.weared, player_items.number FROM items, player_items WHERE player_id = {$player->player_id} AND items.item_id = player_items.item_id ORDER BY items.name" );
	
//	ScrollLightTableStart("left");

	print( "<div style='position:relative;top:0px;left:0px;' id=inv_parent>" );

	print( "<script src=js/inventory.php></script>\n" );

	print( "<script>" );
	print( "global_money = {$player->money};\n" );
	print( "global_umoney = {$player->umoney};\n" );
	print( "wear_level = {$player->wear_level};\n" );
	print( "total_weight = {$player->items_weight};\n" );
	print( "can_carry = '".$player->MaxWeight( )."';\n" );
	while( $arr = f_MFetch( $res ) )
	{
		$descr = itemDescr( $arr, false );
		print( "add_item( $arr[item_id], '$arr[name]', '".itemImage( $arr )."', '$descr', '$arr[number]', '$arr[weight]', $arr[weared], $arr[type], $arr[type2] );\n" );
	}

	$res = f_MQuery( "SELECT entry_id, name FROM player_sets WHERE player_id={$player->player_id} ORDER BY entry_id" );
	while( $arr = f_MFetch( $res ) ) echo "add_set( $arr[entry_id], '$arr[name]' );";
	print( "</script><div id=inv_items style='position:static;top:0px;left:0px;'>" );

	print( "<script>ware = $ware; document.write( get_inv_html( ) );\n" );
	print( "</script>" );
	print( "</div>" );

	print( "<div id=item style=\"display:none;width:300px;position:absolute;left:75px;top:50px;\">&nbsp;" );
	print( "</div>" );

	print( "</div>" );

//	ScrollLightTableEnd();

//print( "</td></tr></table>" );

	print( "<script>set_inv_events( );\n</script>" );

?>
