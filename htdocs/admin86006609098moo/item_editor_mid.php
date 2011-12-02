<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );
include_once( '../items.php' );

$id = $HTTP_GET_VARS['id'];

f_MConnect( );

include( 'admin_header.php' );

$repair = array('Никто', 'Портной', 'Ювелир', 'Кузнец');

$kinds = array('Стандартный', "Ткань", "Хрупкий", "Металл", "Огненный");

function create_select( $nm, $arr, $val )
{
	$st = "<select name='$nm'>";
	
	foreach( $arr as $key=>$value )
	{
		$st .= "<option value=$key";
		if( $key == $val ) $st .= " selected";
		$st .= ">$value" ;
	}
	
	$st .= '</select>';
	
	return $st;
}

$res = f_MQuery( "SELECT * FROM items WHERE item_id=$id" );

if( !mysql_num_rows( $res ) )
	print( "<i>Нет такой вещи</i><br>" );
else
{
	$arr = mysql_fetch_array( $res );

	if( isset( $_GET['unwear_all'] ) )
	{
		include_once( "../player.php" );
		include_once( "../wear_items.php" );
		$ures = f_MQuery( "SELECT player_id, weared, player_items.item_id, number FROM player_items, items WHERE items.parent_id=$id AND items.item_id=player_items.item_id" );
		while( $uarr = f_MFetch( $ures ) )
		{
			$player = new Player( $uarr['player_id'] );
			$player->syst2( "В связи с исправлением ошибки в вещи <b>$arr[name]</b> администрация была вынуждена снять ее и заменить на новую (если ваша была повреждена)." );
			$player->syst2( "/items" );
			if( $uarr['weared'] != 0 ) UnWearItem( $uarr['weared'] );
			if( $uarr[2] != $id )
			{
				$rres = f_MQuery( "SELECT * FROM player_items WHERE weared=0 AND player_id=$uarr[0] AND item_id=$id" );
				if( f_MNum( $rres ) ) f_MQuery( "UPDATE player_items SET number = number + $uarr[3] WHERE weared=0 AND player_id=$uarr[0] AND item_id=$id" );
				else f_MQuery( "UPDATE player_items SET item_id=$id WHERE item_id=$uarr[2]" );
				f_MQuery( "DELETE FROM items WHERE item_id=$uarr[2]" );
			}
		}
	}
	if( isset( $_GET['just_unwear'] ) )
	{
		include_once( "../player.php" );
		include_once( "../wear_items.php" );
		$ures = f_MQuery( "SELECT player_id, weared, player_items.item_id, number FROM player_items, items WHERE items.parent_id=$id AND items.item_id=player_items.item_id" );
		while( $uarr = f_MFetch( $ures ) ) if( $uarr['weared'] != 0 ) 
		{
			$player = new Player( $uarr['player_id'] );
			$player->syst2( "В связи с модификацией работы вещи <b>$arr[name]</b> вещи была снята с вас." );
			$player->syst2( "/items" );
			UnWearItem( $uarr['weared'] );
		}
	}

	$q = ParseItemStr( $arr[effect] );
	$res2 = f_MQuery( "SELECT attribute_id, stats, name FROM attributes" );
	while( $arr2 = f_MFetch( $res2 ) )
	{
		$attr_stats[$arr2[attribute_id]] = $arr2[stats];
		$stats[$arr2[attribute_id]] = $arr2[name];
	}
	$calc_lev = 0;
	foreach( $q as $a=>$b )
	{
		$calc_lev += $attr_stats[$a] * $b;
	}
	$eff_str = ItemEffectStr( $arr[effect] );
	$req_str = ItemReqStr( $arr[req] );
	$divisor = $item_type_stats[$arr[type]] * 5;
	if( $divisor == 0 ) $divisor = 1;
	$calc_lev /= $divisor;
	$calc_lev += 0.995;
	settype( $calc_lev, 'integer' );
	$new_lev = 1;
	$new_stats = 2;
	while( 1 )
	{
		if( $new_stats >= $calc_lev )
		{
			$calc_lev = $new_lev;
			break;
		}
		$new_lev ++;
    	$add = 2;
    	if( $new_lev >= 10 ) $add = 3;
    	if( $new_lev >= 20 ) $add = 4;
    	if( $new_lev == 25 ) $add = 6;
		settype( $add, 'integer' );
		$new_stats += $add;
	}
	print( "<table>" );
	print( "<form action=item_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<tr><td>UIN:</td><td><b>$arr[item_id]</b></td></tr>" );
	print( "<tr><td></td><td colspan=2>Если в вещи есть баг,<br> ее можно снять со всех игроков<br> и заменить неповрежденной.<br> Это необходимо делать<br> всякий раз, внося поправки в вещи,<br> которые могут быть одеты<br> на других игроках.<br><a href=item_editor_mid.php?unwear_all=1&id=$id>Снять со всех игроков и сбросить в оригинал</a><br>Либо можно <a href=item_editor_mid.php?just_unwear=1&id=$id>Просто снять</a> не сбрасывая к оригиналу</td></tr>" );
	print( "<tr><td>Название:</td><td>" );

	print( "<table><tr><td>&nbsp;</td><td>Ед.число</td><td>Мн.число</td></tr>" );
	print( "<tr><td>Именительный (что?)</td><td><input class=m_btn type=text name=nm value='$arr[name]'></td><td><input class=m_btn type=text name=nm_m value='$arr[name_m]'></td></tr>" );
	print( "<tr><td>Родительный (чего?)</td><td><input class=m_btn type=text name=nm2 value='$arr[name2]'></td><td><input class=m_btn type=text name=nm2_m value='$arr[name2_m]'></td></tr>" );
	print( "<tr><td>Дательный (чему?)</td>  <td><input class=m_btn type=text name=nm3 value='$arr[name3]'></td><td><input class=m_btn type=text name=nm3_m value='$arr[name3_m]'></td></tr>" );
	print( "<tr><td>Винительный (кого?что?)</td><td><input class=m_btn type=text name=nm4 value='$arr[name4]'></td><td><input class=m_btn type=text name=nm4_m value='$arr[name4_m]'></td></tr>" );
	print( "<tr><td>Творительный (чем?)</td><td><input class=m_btn type=text name=nm5 value='$arr[name5]'></td><td><input class=m_btn type=text name=nm5_m value='$arr[name5_m]'></td></tr>" );
	print( "<tr><td>Предложный (о чем?)</td><td><input class=m_btn type=text name=nm6 value='$arr[name6]'></td><td><input class=m_btn type=text name=nm6_m value='$arr[name6_m]'></td></tr>" );
	print( "<tr><td>2,3,4 (чего?)</td><td><input class=m_btn type=text name=nm13 value='$arr[name13]'></td><td>&nbsp;</td></tr>" );
	print( "</table>" );
	                                                                   
	print( "</td></tr>" );
	$word_forms = Array( 0 => "Не указано", "Мужской род, ед. число", "Женский род, ед. число", "Средний род, ед. число", "Множественное число" );
	print( "<tr><td>Род, число:</td><td><b>".create_select( 'word_form', $word_forms, $arr['word_form'] )."</b></td></tr>" );
	print( "<tr><td>Кто чинит:</td><td><b>".create_select( 'repair', $repair, $arr['repair'] )."</b></td></tr>" );
	print( "<tr><td>Тип вещи:</td><td><b>".create_select( 'kind', $kinds, $arr['kind'] )."</b>Влияет на текст поломки</td></tr>" );
	print( "<tr><td>Нестандартный текст поломки:</td><td><input class=m_btn type=text name=kind_text value='$arr[kind_text]'></td></tr>" );
	print( "<tr><td>Картинка:</td><td><input class=m_btn type=text name=image value='$arr[image]'></td></tr>" );
	print( "<tr><td>Картинка большая:</td><td><input class=m_btn type=text name=image_large value='$arr[image_large]'></td></tr>" );
	print( "<tr><td>Описание:</td><td><textarea name=descr cols=20 rows=3>$arr[descr]</textarea></td></tr>" );
	print( "<tr><td>Эффект:</td><td vAlign=top><textarea name=effect cols=20 rows=3>$arr[effect]</textarea></td><td vAlign=top>$eff_str</td></tr>" );
	print( "<tr><td>Требования:</td><td vAlign=top><textarea name=req cols=20 rows=3>$arr[req]</textarea></td><td vAlign=top>$req_str</td></tr>" );
	print( "<tr><td>Изучает&nbsp;Спелл:</td><td><input class=m_btn type=text name=learn_spell_id value='$arr[learn_spell_id]'></td></tr>" );
	print( "<tr><td>Изучает&nbsp;Рецепт:</td><td><input class=m_btn type=text name=learn_recipe_id value='$arr[learn_recipe_id]'></td></tr>" );
	print( "<tr><td>Встроенный спелл:</td><td><input class=m_btn type=text name=inner_spell_id value='$arr[inner_spell_id]'></td></tr>" );

	print( "<tr><td>Зарядов:</td><td><input class=m_btn type=text name=charges value='$arr[charges]'></td></tr>" );

	print( "<tr><td>Расчетный уровень:</td><td><b>$calc_lev</b></td></tr>" );
	print( "<tr><td>Уровень:</td><td><input class=m_btn type=text name=level value='$arr[level]'></td></tr>" );
	print( "<tr><td>Тип:</td><td>".create_select( 'type', $item_types, $arr['type'] )."</td></tr>" );
	print( "<tr><td>Подтип:</td><td>".create_select( 'type2', $item_types2, $arr['type2'] )."</td></tr>" );

	print( "<tr><td>Цена:</td><td><input class=m_btn type=text name=price value='$arr[price]'></td></tr>" );
	print( "<tr><td>Вес:</td><td><input class=m_btn type=text name=weight value='$arr[weight]'>(В игре будет в 100 раз меньше)</td></tr>" );
	print( "<tr><td>Прочность:</td><td><input class=m_btn type=text name=decay value='$arr[decay]'>/<input class=m_btn type=text name=max_decay value='$arr[max_decay]'></td></tr>" );	
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Применить'></td></tr>" );
	print( "</form>" );
	print( "<form action=item_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<input type=hidden name=del>" );
	print( "<tr><td>Удалить вещь:<br>хер ты попадешь по кнопке случайно</td><td><input class=m_btn type=submit value='Удалить' style='width:5px;height:5px;'></td></tr>" );
	print( "</form>" );
	print( "</table>" );
	
}

f_MClose( );

?>
