<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );

$genres[-1] = "Без Стихии";

$id = $HTTP_GET_VARS['id'];

if( !file_exists( "../spell_effects/aura$id.php" ) )
	$effect_str = "";
else
	$effect_str = file_get_contents( "../spell_effects/aura$id.php" );

if( !file_exists( "../spell_effects/aura{$id}dispell.php" ) )
	$dispell_str = "";
else
	$dispell_str = file_get_contents( "../spell_effects/aura{$id}dispell.php" );

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

$res = f_MQuery( "SELECT * FROM auras WHERE aura_id=$id" );

if( !mysql_num_rows( $res ) )
	print( "<i>Нет такой ауры</i><br>" );
else
{
	$arr = f_MFetch( $res );
	$id = $arr[aura_id];
	print( "<table>" );
	print( "<form action=auras_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<tr><td>UIN:</td><td><b>$arr[aura_id]</b></td></tr>" );
	print( "<tr><td>Название:</td><td><input class=m_btn type=text name=nm value='$arr[name]'></td></tr>" );
	print( "<tr><td>Картинка:</td><td><input class=m_btn type=text name=icon value='$arr[icon]'></td></tr>" );
	print( "<tr><td>Уровень:</td><td><input class=m_btn type=text name=level value='$arr[level]'></td></tr>" );
	print( "<tr><td>Стихия:</td><td>".create_select( 'genre', $genres, $arr['genre'] )."</td></tr>" );
	print( "<tr><td>Эффект наложения:<br><a target=_blank href=combat_sdk.html>описание sdk</a></td><td><textarea name=effect cols=40 rows=12>$effect_str</textarea></td></tr>" );
	print( "<tr><td>Эффект снятия:</td><td><textarea name=dispell cols=40 rows=12>$dispell_str</textarea></td></tr>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Применить'></td></tr>" );
	print( "</form>" );
	print( "<form action=auras_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<input type=hidden name=del>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Удалить'></td></tr>" );
	print( "</form>" );
	print( "</table>" );
	
}

f_MClose( );

?>
