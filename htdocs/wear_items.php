<?

	function HasItemInSlot( $slot ) // по конкретному слоту
	{
		global $player;
		$res = f_MQuery( "SELECT * FROM player_items WHERE player_id = {$player->player_id} AND weared=$slot" );
		if( mysql_num_rows( $res ) )
			if ($slot==25)
				return -1;
			else
				return 1;
		return 0;
	}
	
	// 0 - успешно
	// -1 - шмотки нет
	// -2 - заняты все слоты
	// -3 - ничего нельзя сделать
	// -5 - нет в игре такой вещи
	// -6 - требования не айс
	// -7 - указанный в качестве параметра слот не подходит для вещи
	// -8 - персонаж уже знает заклинание, которое пытается выучить
	// -9 - персонаж уже знает рецепт, которое пытается выучить
	// -10 - персонаж уже знает заклинание, встроенное в вещь
	// -11 - При использования вещи произошла ошибка: Нельзя одеть сломанную вещь
	function WearItem( $item_id, $type = -1 )
	{
		global $player;
		
		$res = f_MQuery( "SELECT * FROM items WHERE item_id = $item_id" );
		$arr = f_MFetch( $res );
		if( !$arr ) return -5;
		if( $arr['decay'] <= 0 ) return -11;
		
		if( $type == -1 ) $type = $arr[type];
		else // проверим на совместимость слоты
		{
			$ok = false;
			do
			{
				if( $type == $arr[type] )
				{
					$ok = true;
					break;
				}
				if( $arr[type] == 1 ) $arr[type] = 14;
				else if ($arr[type] == 30 ) $arr[type] = 17;
				else if ($arr[type] == 35 ) $arr[type] = 21;
				else if ($arr[type] == 31 ) $arr[type] = 25;
				else if( $arr[type] == 13 ) break;
				else if( $arr[type] == 16 ) break;
				else if( $arr[type] == 20 ) break;
				else if( $arr[type] == 24 ) break;
				else ++ $arr[type];
			} while( $arr[type] == 3 || $arr[type] == 5 || $arr[type] == 7 || $arr[type] == 14 || $arr[type] == 15 || $arr[type] == 16 || ($arr[type] >= 17 && $arr[type] <= 24) || $arr[type] == 25 );
			if( !$ok ) return -7;
		}

		if( $arr[learn_spell_id] != 0 ) // изучаем спелл
		{
			if( !$player->CheckItemReq( $arr[req] ) )
				return -6;
				
			$sres = f_MQuery( "SELECT count( card_id ) FROM player_cards WHERE player_id={$player->player_id} AND ( card_id=$arr[learn_spell_id] OR card_id IN ( SELECT card_id FROM cards WHERE mk != 5 AND parent = $arr[learn_spell_id] ) )" );
			$sarr = f_MFetch( $sres );
			if( $sarr[0] ) return -8;
			
			if( $player->DropItems( $item_id ) )
			{
				f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( {$player->player_id}, $arr[learn_spell_id], 10 )" );
				
				return 0;
			}
			return -1;
		}

		if( $arr[learn_recipe_id] != 0 ) // изучаем рецепт
		{
			if( !$player->CheckItemReq( $arr[req] ) )
				return -6;

			$sres = f_MQuery( "SELECT * FROM player_recipes WHERE player_id = {$player->player_id} AND recipe_id = $arr[learn_recipe_id]" );
			if( f_MNum( $sres ) ) return -9;
			
			if( $player->DropItems( $item_id ) )
			{
				f_MQuery( "INSERT INTO player_recipes ( player_id, recipe_id ) VALUES ( {$player->player_id}, $arr[learn_recipe_id] )" );
				
				return 0;
			}
			return -1;
		}

		if( $type == 24 )
		{
			if( $player->DropItems( $item_id ) )
			{
				$mul = 1;
				include( 'item_effect.php' );
				return 29;
			}	
			return -1;
		}

		if( ($type > 0 && $type < 20) || $type == 30 || $type == 35 || $type == 31 )
		{
			$ok = false;
			if ($type == 30) $type = 17;
			if ($type == 35) $type = 21;
			if ($type == 31) $type = 25;
			do
			{
				if( HasItemInSlot( $type ) <= 0 )
				{
					$ok = true;
					break;
				}
				if( $type == 1 ) $type = 14;
				else if ($type == 30 ) $type = 18;
				else if ($type == 35 ) $type = 22;
				else if( $type == 13 ) break;
				else if( $type == 16 ) break;
				else if( $type == 20 ) break;
				else if( $type == 24 ) break;
				else ++ $type;
			} while( $type == 3 || $type == 5 || $type == 7 || $type == 14 || $type == 15 || $type == 16 || ($type >= 17 && $type <= 24) );
			if( !$ok ) return -2;
			
			
			if( $player->level < $arr[level] )
				return -6;
			if( !$player->CheckItemReq( $arr[req] ) )
				return -6;
			if( $player->DropItems( $item_id ) )
			{
				if ($type == 25)
				{
					if (HasItemInSlot(25) == 0)
					{
						$item_id1 = copyItem($item_id, true);
						f_MQuery( "INSERT INTO player_items ( player_id, item_id, number, weared ) VALUES ( {$player->player_id}, $item_id1, 1, $type )" );
						return 25;
					}
					else
					{
						f_MQuery("UPDATE items SET decay=decay+".$arr[decay]." WHERE item_id=(SELECT item_id FROM player_items WHERE player_id=".$player->player_id." AND weared=25)");
						return 0;
					}
				}
				f_MQuery( "INSERT INTO player_items ( player_id, item_id, number, weared ) VALUES ( {$player->player_id}, $item_id, 1, $type )" );
				if( $type == 1 || $type >= 14 && $type <= 24 )
				{
					$expires = time( ) + $arr['level'] * 30 * 60;
					f_MQuery( "INSERT INTO player_potions ( player_id, slot_id, expires ) VALUES ( {$player->player_id}, $type, $expires )" );
					if ( $type == 1 || $type >= 14 && $type <= 16)
						checkZhorik( $player, 14, 5 ); // квест жорика выпить элики
			   	}
				f_MQuery( "UPDATE characters SET items_weight =items_weight + $arr[weight], wear_level = wear_level + $arr[level] WHERE player_id = {$player->player_id}" );
				$player->items_weight += $arr[weight];
				$player->wear_level += $arr[level];
				$mul = 1;
				include( 'item_effect.php' );


				if( $arr[inner_spell_id] != 0 ) // встроенный спелл
				{
					f_MQuery( "LOCK TABLE player_cards WRITE" );
					$sres = f_MQuery( "SELECT * FROM player_cards WHERE player_id = {$player->player_id} AND card_id = $arr[inner_spell_id]" );
					if( f_MNum( $sres ) ) 
					{
						f_MQuery( "UPDATE player_cards SET number = number + 1 WHERE player_id = {$player->player_id} AND card_id = $arr[inner_spell_id]" );
					}
					else
						f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( {$player->player_id}, $arr[inner_spell_id], 1 )" );
					f_MQuery( "UNLOCK TABLES" );
					f_MQuery( "LOCK TABLE player_selected_cards WRITE" );
					f_MQuery( "DELETE FROM player_selected_cards WHERE player_id={$player->player_id} AND card_id={$arr[inner_spell_id]}" );
					f_MQuery( "INSERT INTO player_selected_cards( player_id, card_id, staff ) VALUES ( {$player->player_id}, $arr[inner_spell_id], 1 )" );
					f_MQuery( "UNLOCK TABLES" );
					if (f_MValue("SELECT COUNT(*) FROM player_cards WHERE (number=1 OR number=11) AND player_id=".$player->player_id." AND card_id=".$arr['inner_spell_id'])==1)
					{
						$arr_s = f_MFetch(f_MQuery("SELECT * FROM cards WHERE card_id=".$arr['inner_spell_id']));
						$descr_s = cardGetSmallIcon( $arr_s );
						echo "parent.char_ref.add_spell_s(".$descr_s.");";
					}
//					$player->syst2("/items");
				}
				
				if( $arr[inner_base_spell_id] != 0 ) // встроенный базовый спелл
				{
					f_MQuery( "LOCK TABLE player_cards WRITE" );
					$sres = f_MQuery( "SELECT * FROM player_cards WHERE player_id = {$player->player_id} AND card_id = $arr[inner_base_spell_id]" );
					if( f_MNum( $sres ) ) 
					{
						f_MQuery( "UPDATE player_cards SET number = number + 1 WHERE player_id = {$player->player_id} AND card_id = $arr[inner_base_spell_id]" );
					}
					else
						f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( {$player->player_id}, $arr[inner_base_spell_id], 1 )" );
					f_MQuery( "UNLOCK TABLES" );
					f_MQuery( "LOCK TABLE player_selected_cards WRITE" );
					f_MQuery( "DELETE FROM player_selected_cards WHERE player_id={$player->player_id} AND card_id={$arr[inner_base_spell_id]}" );
					f_MQuery( "INSERT INTO player_selected_cards( player_id, card_id, staff ) VALUES ( {$player->player_id}, $arr[inner_base_spell_id], 1 )" );
					f_MQuery( "UNLOCK TABLES" );
					if (f_MValue("SELECT COUNT(*) FROM player_cards WHERE (number=1 OR number=11) AND player_id=".$player->player_id." AND card_id=".$arr['inner_base_spell_id'])==1)
					{
						$arr_s = f_MFetch(f_MQuery("SELECT * FROM cards WHERE card_id=".$arr['inner_base_spell_id']));
						$descr_s = cardGetSmallIcon( $arr_s );
						echo "parent.char_ref.add_spell_s(".$descr_s.");";
					}
//					$player->syst2("/items");
				}

				return $type;
			}
		}
		else return -3;
		return -1;
	}
	
	function UnWearItem( $slot, $ignore_fakel = false )
	{
		global $player;
		
		if( $slot == 0 ) return 0;
		if ($slot == 25) return -3;
		$res = f_MQuery( "SELECT player_items.*, items.parent_id FROM player_items, items WHERE player_id = {$player->player_id} AND items.item_id=player_items.item_id AND weared=$slot" );
		$arr = f_MFetch( $res );
		if( $arr )
		{
			if( !$ignore_fakel && $arr['parent_id'] == 8 && $player->location == 0 && $player->depth >= 3 && $player->depth <= 20 )
				return -2; // уже глубже -2
			if( !$ignore_fakel && $arr['parent_id'] == 8 && $player->location == 0 && $player->depth == 2 && $player->regime == 1 )
				return -2; // спускается ниже -2

			f_MQuery( "DELETE FROM player_items WHERE player_id = {$player->player_id} AND weared=$slot" );
			if( $slot == 1 || $slot >= 14 && $slot <= 24 )
				f_MQuery( "DELETE FROM player_potions WHERE player_id={$player->player_id} AND slot_id=$slot" );
			if( $slot != 1 && ( $slot < 14 || $slot > 25 ) ) $player->AddItems( $arr['item_id'] );
			$res = f_MQuery( "SELECT * FROM items WHERE item_id = $arr[item_id]" );
			$arr = f_MFetch( $res );
			f_MQuery( "UPDATE characters SET items_weight =items_weight - $arr[weight], wear_level = wear_level - $arr[level] WHERE player_id = {$player->player_id}" );
			$player->items_weight -= $arr[weight];
			$player->wear_level -= $arr[level];
			$mul = -1;
			include( 'item_effect.php' );

			if( $arr[inner_spell_id] != 0 )
			{
				f_MQuery( "LOCK TABLES player_cards WRITE, player_selected_cards WRITE" );
				$sres = f_MQuery( "SELECT number FROM player_cards WHERE player_id = {$player->player_id} AND card_id = $arr[inner_spell_id]" );
				$sarr = f_MFetch( $sres );
				if ($sarr[0]!=10)
				{
					if( $sarr[0] > 1 ) 
						f_MQuery( "UPDATE player_cards SET number = number - 1 WHERE player_id = {$player->player_id} AND card_id = $arr[inner_spell_id]" );
					else
					{
						f_MQuery( "DELETE FROM player_cards WHERE player_id = {$player->player_id} AND card_id = $arr[inner_spell_id]" );
					}
				}
				if ( $sarr[0] == 1 || $sarr[0] == 11)
				{
					$res_s = f_MQuery("SELECT * FROM player_selected_cards WHERE player_id = {$player->player_id} AND staff=1 ORDER BY entry_id");
					$_s = true;
					$_i = 0;
					while ($_s && $arr_s = f_MFetch($res_s))
					{
//						$arr_s = f_MFetch($res_s);
						$_i++;
						if ($arr_s[card_id] == $arr[inner_spell_id]) $_s = false;
					}
					f_MQuery( "DELETE FROM player_selected_cards WHERE player_id = {$player->player_id} AND card_id = $arr[inner_spell_id] AND staff=1 LIMIT 1" );
					echo "parent.char_ref.del_spell_s(".($_i-1).");";
				}
				f_MQuery( "UNLOCK TABLES" );
//				$player->syst2("/items");
			}
			
			if( $arr[inner_base_spell_id] != 0 )
			{
				f_MQuery( "LOCK TABLES player_cards WRITE, player_selected_cards WRITE" );
				$sres = f_MQuery( "SELECT number FROM player_cards WHERE player_id = {$player->player_id} AND card_id = $arr[inner_base_spell_id]" );
				$sarr = f_MFetch( $sres );
				if ($sarr[0]!=10)
				{
					if( $sarr[0] > 1 ) 
						f_MQuery( "UPDATE player_cards SET number = number - 1 WHERE player_id = {$player->player_id} AND card_id = $arr[inner_base_spell_id]" );
					else
					{
						f_MQuery( "DELETE FROM player_cards WHERE player_id = {$player->player_id} AND card_id = $arr[inner_base_spell_id]" );
					}
				}
				if ( $sarr[0] == 1 || $sarr[0] == 11)
				{
					$res_s = f_MQuery("SELECT * FROM player_selected_cards WHERE player_id = {$player->player_id} AND staff=1 ORDER BY entry_id");
					$_s = true;
					$_i = 0;
					while ($_s && $arr_s = f_MFetch($res_s))
					{
//						$arr_s = f_MFetch($res_s);
						$_i++;
						if ($arr_s[card_id] == $arr[inner_base_spell_id]) $_s = false;
					}
					f_MQuery( "DELETE FROM player_selected_cards WHERE player_id = {$player->player_id} AND card_id = $arr[inner_base_spell_id] AND staff=1 LIMIT 1" );
					echo "parent.char_ref.del_spell_s(".($_i-1).");";
				}
				f_MQuery( "UNLOCK TABLES" );
//				$player->syst2("/items");
			}

			return 0;
		}
		else return -1;
	}

	function getWearMessage( $q )
	{
		$msg = '';
		if( $q == -1 ) $msg = "При использования вещи произошла ошибка: У вас нет этой вещи";
		if( $q == -2 ) $msg = "При использования вещи произошла ошибка: У вас занято место для этой вещи";
		if( $q == -3 ) $msg = "При использования вещи произошла ошибка: С этой вещью ничего нельзя сделать";
		if( $q == -5 ) $msg = "При использования вещи произошла ошибка: Такой вещи не существует в игре";
		if( $q == -6 ) $msg = "При использования вещи произошла ошибка: Требования для использования вещи слишком велики";
		if( $q == -7 ) $msg = "При использования вещи произошла ошибка: Вы одеваете вещь на неверное место";
		if( $q == -8 ) $msg = "При использования вещи произошла ошибка: Вы уже знаете заклинание, которое пытаетесь изучить";
		if( $q == -9 ) $msg = "При использования вещи произошла ошибка: Вы уже знаете рецепт, который пытаетесь изучить";
		if( $q == -10 ) $msg = "При использования вещи произошла ошибка: Вы пытаетесь взять посох с заклинанием, которое есть в Книге Заклинаний";
		if( $q == -11 ) $msg = "При использования вещи произошла ошибка: Нельзя одеть сломанную вещь";
		return $msg;
	}

?>
