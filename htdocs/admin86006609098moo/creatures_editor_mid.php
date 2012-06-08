<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );

$id = $HTTP_GET_VARS['id'];

f_MConnect( );

include( 'admin_header.php' );

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

$res = f_MQuery( "SELECT * FROM creatures WHERE creature_id=$id" );

if( !mysql_num_rows( $res ) )
	print( "<i>Нет такого существа</i><br>" );
else
{
	$arr = mysql_fetch_array( $res );
	print( "<table>" );
	print( "<form action=creatures_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<tr><td>ID:</td><td><b>$arr[creature_id]</b></td></tr>" );
	print( "<tr><td>Название:</td><td><input class=m_btn type=text name=nm value='$arr[name]'></td></tr>" );
	print( "<tr><td>Изображение:</td><td><input class=m_btn type=text name=image value='$arr[image]'></td></tr>" );
	print( "<tr><td>Атака:</td><td><input class=m_btn type=text name=attack value='$arr[attack]'></td></tr>" );
	print( "<tr><td>Защита:</td><td><input class=m_btn type=text name=defence value='$arr[defence]'></td></tr>" );
	print( "<tr><td>Ярость:</td><td><input type=checkbox name=firststrike ".( $arr['firststrike']?'checked':'' )."></td></tr>" );
	print( "<tr><td>Стремителность:</td><td><input type=checkbox name=haste ".( $arr['haste']?'checked':'' )."></td></tr>" );
	print( "<tr><td>Подавление:</td><td><input type=checkbox name=trample ".( $arr['trample']?'checked':'' )."></td></tr>" );

	print( "<tr><td>Стихия:</td><td>".create_select( 'genre', $genres, $arr['genre'] )."</td></tr>" );
/*	print( "<tr><td>Эффект при уроне по игроку:<br><small>\$slot -- в каком слоте существо</small></td><td><textarea name=effect_dmg_p cols=40 rows=12>{$arr[effect_dmg_p]}</textarea></td></tr>" );
	print( "<tr><td>Эффект при уроне по существу:<br><small>\$slot -- в каком слоте существо</small></td><td><textarea name=effect_dmg_c cols=40 rows=12>{$arr[effect_dmg_c]}</textarea></td></tr>" );
	print( "<tr><td>Эффект при смерти:<br><small>\$slot -- в каком слоте существо</small></td><td><textarea name=effect_die cols=40 rows=12>{$arr[effect_die]}</textarea></td></tr>" );
	print( "<tr><td>Эффект при получении урона:<br><small>\$slot -- в каком слоте существо</small></td><td><textarea name=effect_got_dmg cols=40 rows=12>{$arr[effect_got_dmg]}</textarea></td></tr>" );*/
	print( "<tr><td>Эффект каждый ход:<br><small>\$slot -- в каком слоте существо (от 0 до 2)</small></td><td><textarea name=effect_always cols=40 rows=12>{$arr[effect_always]}</textarea></td></tr>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Применить'></td></tr>" );
	print( "</form>" );
	print( "<form action=creatures_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<input type=hidden name=del>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Удалить'></td></tr>" );
	print( "</form>" );
}

f_MClose( );

?>
