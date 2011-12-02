<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

$id = $HTTP_GET_VARS['id'];
$stats = $player->getAllAttrNames( );

if( !file_exists( "../spell_effects/spell$id.php" ) )
	$effect_str = "";
else
	$effect_str = file_get_contents( "../spell_effects/spell$id.php" );

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

function create_select_summon( $nm, $val, $genre )
{
	$res = f_MQuery( "SELECT 0, 'Не призывает' UNION SELECT creature_id, name FROM creatures WHERE genre=$genre" );
	
	$st = "<select name='$nm'>";
	
	while( $arr = f_MFetch( $res ) )
	{
		$st .= "<option value={$arr[0]}";
		if( $arr[0] == $val ) $st .= " selected";
		$st .= ">{$arr[1]}" ;
	}
	
	$st .= '<select>';
	
	return $st;
}

$res = f_MQuery( "SELECT * FROM cards WHERE card_id=$id" );

if( !mysql_num_rows( $res ) )
	print( "<i>Нет такой карточки</i><br>" );
else
{
    $arr = mysql_fetch_array( $res );

    if( isset( $_GET['gen_names'] ) )
    {
    	include( "names_changer.php" );
    	changeNames( );
    }

    if( isset( $_GET['change_image'] ) )
    {
		$ires = f_MQuery( "SELECT * FROM items WHERE learn_spell_id = $id" );
		$iarr = f_MFetch( $ires );
		if( $iarr )
		{
			include_once( "create_image.php" );
			if( $arr['image_large'] != '' ) f_MQuery( "UPDATE items SET image='auto/" .create_image_spell( "spells/" . $arr['image_large'] ) . "' WHERE item_id=$iarr[item_id]" );
		}
    }
       
	elseif( isset( $_GET['create_item'] ) )
	{
		$fields = Array( );
		$fields["name"] = "Заклинание: $arr[name]";
		$fields["name2"] = "Заклинания: $arr[name]";
		$fields["name3"] = "Заклинанию: $arr[name]";
		$fields["name4"] = "Заклинание: $arr[name]";
		$fields["name5"] = "Заклинанием: $arr[name]";
		$fields["name6"] = "Заклинании: $arr[name]";
		$fields["name_m"] = "Заклинания: $arr[name]";
		$fields["name2_m"] = "Заклинаний: $arr[name]";
		$fields["name3_m"] = "Заклинаниям: $arr[name]";
		$fields["name4_m"] = "Заклинания: $arr[name]";
		$fields["name5_m"] = "Заклинаниями: $arr[name]";
		$fields["name6_m"] = "Заклинаниях: $arr[name]";
		$fields["level"] = $arr['level'];
		$fields["type"] = 21;
		$fields["price"] = 50 * pow( (int)( ( 1 + $arr['level'] ) / 2 ), 2 ) * ( 1.5 - 0.5 * ( $arr['level'] % 2 ) );
		$fields["req"] = $arr["req"];
		$fields["weight"] = 1 * $arr['level'];
		$fields["learn_spell_id"] = $id;
		include_once( "create_image.php" );
		if( $arr['image_large'] != '' ) $fields["image"] = "auto/".create_image_spell( "spells/" . $arr['image_large'] );

		$astr = "";
		$bstr = "";
		foreach( $fields as $a=>$b ) 
		{
			$astr .= ", $a";
			$bstr .= ", '$b'";
		}

		$astr = substr( $astr, 1 );
		$bstr = substr( $bstr, 1 );
		f_MQuery( "INSERT INTO items($astr ) VALUES ($bstr )" );
		$q = mysql_insert_id( );
		echo "$q<br>";
		f_MQuery( "UPDATE items SET parent_id = item_id WHERE item_id = $q" );
	}

	print( "<table>" );
	print( "<form action=editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<tr><td>ID:</td><td><b>$arr[card_id]</b></td></tr>" );

	print( "<tr><td>Название:</td><td>" );
	print( "<table><tr><td>&nbsp;</td><td>Ед.число</td><td>Мн.число</td></tr>" );
	print( "<tr><td>Именительный (что?)</td><td><input class=m_btn type=text name=nm value='$arr[name]'></td><td><input class=m_btn type=text name=nm_m value='$arr[name_m]'></td></tr>" );
	print( "<tr><td>Родительный (чего?)</td><td><input class=m_btn type=text name=nm2 value='$arr[name2]'></td><td><input class=m_btn type=text name=nm2_m value='$arr[name2_m]'></td></tr>" );
	print( "<tr><td>Дательный (чему?)</td>  <td><input class=m_btn type=text name=nm3 value='$arr[name3]'></td><td><input class=m_btn type=text name=nm3_m value='$arr[name3_m]'></td></tr>" );
	print( "<tr><td>Винительный (кого?что?)</td><td><input class=m_btn type=text name=nm4 value='$arr[name4]'></td><td><input class=m_btn type=text name=nm4_m value='$arr[name4_m]'></td></tr>" );
	print( "<tr><td>Творительный (чем?)</td><td><input class=m_btn type=text name=nm5 value='$arr[name5]'></td><td><input class=m_btn type=text name=nm5_m value='$arr[name5_m]'></td></tr>" );
	print( "<tr><td>Предложный (о чем?)</td><td><input class=m_btn type=text name=nm6 value='$arr[name6]'></td><td><input class=m_btn type=text name=nm6_m value='$arr[name6_m]'></td></tr>" );
	print( "</table>" );
	print( "</td></tr>" );

	print( "<tr><td>Картинка small:</td><td><input class=m_btn type=text name=image_small value='$arr[image_small]'></td></tr>" );
	print( "<tr><td>Картинка large:</td><td><input class=m_btn type=text name=image_large value='$arr[image_large]'></td></tr>" );
	print( "<tr><td>Изучающая вещь:</td><td>" );

	$ires = f_MQuery( "SELECT * FROM items WHERE learn_spell_id = $id" );
	$iarr = f_MFetch( $ires );
	if( !$iarr ) echo "<i>Нету</i> - <a href=editor.php?id=$id&create_item>Создать</a>";
	else echo "<img src=../images/items/$iarr[image]> <a href=item_editor_mid.php?id=$iarr[item_id] target=_blank><b>$iarr[name]</b></a> - <a href=editor.php?id=$id&change_image=1>Перегенерить картинку</a>";

	$genres[3] = "Нейтральная";
	print( "</td></tr>" );
	print( "<tr><td>Статус:</td><td><input class=m_btn type=text name=status value='$arr[status]'><br><small>0 - обычный, 1 - мобовский, в энце показан не будет</small></td></tr>" );
	print( "<tr><td>MK:</td><td><input class=m_btn type=text name=mk value='$arr[mk]'></td></tr>" );
	print( "<tr><td>Multy:</td><td><input class=m_btn type=text name=multy value='$arr[multy]'></td></tr>" );
	print( "<tr><td>Описание:</td><td><textarea name=descr cols=60 rows=7>$arr[descr]</textarea></td></tr>" );
	print( "<tr><td>Описание эффекта:</td><td><textarea name=descr2 cols=60 rows=7>$arr[descr2]</textarea></td></tr>" );
	print( "<tr><td>Эффект в бою:</td><td><textarea name=cast_description cols=60 rows=7>$arr[cast_description]</textarea><br>*player* - имя игрока<br>*victim* - имя оппонента<br>{муж|жен} - зависимость от пола игрока<br>[муж|жен] - зависимость от пола оппонента</td></tr>" );
	print( "<tr><td>Цена:</td><td><input class=m_btn type=text name=price value='$arr[price]'></td></tr>" );
	print( "<tr><td>Стихия:</td><td>".create_select( 'genre', $genres, $arr['genre'] )."</td></tr>" );
	print( "<tr><td>Уровень:</td><td><input class=m_btn type=text name=level value='$arr[level]'></td></tr>" );
	print( "<tr><td>Мана:</td><td><input class=m_btn type=text name=cost value='$arr[cost]'></td></tr>" );
	print( "<tr><td>Эффект:<br><a target=_blank href=combat_sdk.html>описание sdk</a></td><td><textarea name=effect cols=40 rows=12>$effect_str</textarea></td></tr>" );
	print( "<tr><td>Требования:</td><td><textarea name=req cols=20 rows=3>$arr[req]</textarea></td></tr>" );
	$uns = array( "Действует", "Не действует" );
	print( "<tr><td>Удача:</td><td>".create_select( 'unlucky', $uns, $arr['unlucky'] )."</td></tr>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Применить'></td></tr>" );
	print( "</form>" );
	print( "<form action=editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<input type=hidden name=del>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Удалить'></td></tr>" );
	print( "</form>" );
	print( "</table>" );
	if( $arr['mk'] == 0 )
	{
		echo "<br><a href=editor.php?id=$id&addmk=1>Добавить MK</a><br>";
		if( $_GET['addmk'] )
		{
			function copySpell( $id )
            {
            	$res = f_MQuery( "SELECT max(mk) FROM cards WHERE parent=$id" );
            	$arr = f_MFetch( $res );
            	$mk = (int)$arr[0];++ $mk;

            	f_MQuery( "LOCK TABLE cards WRITE" );
            	$res = f_MQuery( "SELECT * FROM cards WHERE card_id=$id" );
            	if( !f_MNum( $res ) ) RaiseError( "Ошибка при копировании вещи - вещи не существует", "item_id: $item_id" );
            	$arr = f_MFetch( $res );

            	$req = (10*$arr['genre']+30).':'.floor((2+$mk)*$arr[level]/2).'.';
            	$mana = ceil( $arr['cost'] * ( 1 + 0.2 * $mk ) );
            	
            	$fields = "parent,mk,req,cost";
            	$values = "$id,$mk,'$req',$mana";

            	for( $i = 0; $i < mysql_num_fields( $res ); ++ $i )
            	{
            		$meta = mysql_fetch_field( $res, $i );
            		if( !$meta ) continue;
            		if( $meta->name != 'card_id' && $meta->name != 'parent' && $meta->name != 'req' && $meta->name != 'mk' && $meta->name != 'cost' )
            		{
            			$fields .= ", ".$meta->name;
            			$values .= ", '".AddSlashes( $arr[$meta->name] )."'";
            		}
            	}
            	f_MQuery( "INSERT INTO cards ( $fields ) VALUES ( $values )" );
            	$new_id = mysql_insert_id( );

            	return $new_id;
            }
			copySpell( $id );
		}
	}
	$res = f_MQuery( "SELECT * FROM cards WHERE parent > 0 AND ( parent=$id OR parent=$arr[parent] ) ORDER BY mk" );
	while( $arr = f_MFetch( $res ) )
		echo "<a href=editor.php?id=$arr[card_id]>MK $arr[mk]</a><br>";

}

f_MClose( );

?>
