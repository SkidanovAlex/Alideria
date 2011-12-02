<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );
include( '../forest_functions.php' );

f_MConnect( );

include( 'admin_header.php' );

$id = $HTTP_GET_VARS['id'];
$stats = $player->getAllAttrNames( );

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

$res = f_MQuery( "SELECT * FROM mobs WHERE mob_id=$id" );

if( !mysql_num_rows( $res ) )
	print( "<i>Нет такого моба</i><br>" );
else
{
    if( isset( $_GET['add_item'] ) )
    {
    	$item_id = $_POST[item_id];
    	$number = $_POST[number];
    	$chance = $_POST[chance];

    	settype( $item_id, 'integer' );
    	settype( $number, 'integer' );
    	settype( $chance, 'integer' );

    	f_MQuery( "DELETE FROM mob_items WHERE mob_id=$id AND item_id=$item_id" );
    	f_MQuery( "INSERT INTO mob_items( mob_id, item_id, number, chance ) VALUES ( $id, $item_id, $number, $chance )" );
    }
    else if( isset( $_GET['del_item'] ) )
    {
    	$item_id = $_GET[del_item];
    	settype( $item_id, 'integer' );
    	f_MQuery( "DELETE FROM mob_items WHERE mob_id=$id AND item_id=$item_id" );
    }


    $arr = mysql_fetch_array( $res );
	print( "<table><tr><td valign=top><table>" );
	print( "<form action=mob_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<tr><td>Название:</td><td><input class=m_btn type=text name=nm value='$arr[name]'></td></tr>" );
	print( "<tr><td>Аватара:</td><td><input class=m_btn type=text name=avatar value='$arr[avatar]'></td></tr>" );
	print( "<tr><td>Описание:</td><td><textarea name=descr cols=20 rows=3>$arr[descr]</textarea></td></tr>" );
	print( "<tr><td>Уровень:</td><td><input class=m_btn type=text name=level value='$arr[level]'></td></tr>" );
	print( "<tr><td>Локация:</td><td>".create_select( 'loc', $locs, $arr['loc'] )."</td></tr>" );
	print( "<tr><td>Минимальная Глубина:</td><td><input class=m_btn type=text name=min_depth value='$arr[min_depth]'></td></tr>" );
	print( "<tr><td>Максимальная Глубина:</td><td><input class=m_btn type=text name=max_depth value='$arr[max_depth]'></td></tr>" );
	print( "<tr><td>Охраняет Глубину:</td><td><input class=m_btn type=text name=def_depth value='$arr[defend_depth]'></td></tr>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Применить'></td></tr>" );
	print( "</form>" );
	print( "<form action=mob_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<input type=hidden name=del>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Удалить'></td></tr>" );
	print( "</form>" );
	print( "</table>" );
	print( "<td><td valign=top>При создании моба для леса  охраняемая глубина обозначает следующее:<br>" );
		
	foreach( $forest_names as $a=>$b )
	{
		print( "$a: $b<br>" );
	}
	
	print( "Минимальная и максимальная глубина не имеют значения</td></tr></table>" );
	
	print( "<br><br><b>Свитки</b><br>" );
	$res = f_MQuery( "SELECT mob_cards.*, cards.name FROM mob_cards, cards WHERE mob_id = $id AND cards.card_id=mob_cards.card_id" );
	if( !mysql_num_rows( $res ) )
		print( "<i>Нет карточек</i><br>" );
	else
	{
		while( $arr = mysql_fetch_array( $res ) )
			print( "<a href=mob_editor_cards.php?mob_id=$id&card_id=$arr[card_id]&del=1>$arr[name]</a><br>" );
	}
	
	print( "<form action=mob_editor_cards.php method=get>" );
	print( "<input type=hidden name=mob_id value=$id>" );
	print( "<select name=card_id>" );
	$res = f_MQuery( "SELECT card_id, name, mk FROM cards ORDER BY name" );
	while( $arr = mysql_fetch_array( $res ) )
		print( "<option value=$arr[card_id]>$arr[name], $arr[mk]" );
	print( "</select>" );
	print( "<input class=m_btn type=submit value='Добавить'>" );
	print( "</form>" );


	print( "<br><br><b>Аттрибуты</b><br>" );
	$res = f_MQuery( "SELECT * FROM mob_attributes WHERE mob_id = $id" );
	if( !mysql_num_rows( $res ) )
		print( "<i>Нет аттрибутов</i><br>" );
	else
	{
		while( $arr = mysql_fetch_array( $res ) )
			print( "<a href=mob_editor_attribs.php?mob_id=$id&attrib_id=$arr[attribute_id]&del=1>{$stats[$arr[attribute_id]]}: $arr[value]</a><br>" );
	}
	
	print( "<form action=mob_editor_attribs.php method=get>" );
	print( "<input type=hidden name=mob_id value=$id>" );
	print( create_select( 'attrib_id', $stats, 0 ) );
	print( "<input class=m_btn type=text value='0' name=value>" );
	print( "<input class=m_btn type=submit value='Добавить'><br>" );

	print( "</form>" );


	echo "<br><br><b>Дроп</b><br>";

	$res = f_MQuery( "SELECT a.item_id as id, a.number, a.chance, b.* FROM mob_items as a LEFT JOIN items as b ON a.item_id=b.item_id WHERE mob_id=$id" );
	if( !f_MNum( $res ) ) echo "<i>Нет дропа че-то</i><br>";
	else while( $qarr = f_Mfetch( $res ) ) echo "[$qarr[number]] $qarr[name] (".($qarr['chance']/100)."%) - <a href=mob_editor_mid.php?id=$id&del_item=$qarr[id]>Удалить</a><br>";
	echo "<form action=mob_editor_mid.php?id=$id&add_item=1 method=post>ID:<input type=text class=m_btn name=item_id><br>Num:<input type=text class=m_btn name=number><br>Chance*100:<input type=text class=m_btn name=chance><br><input type=submit class=ss_btn value='Добавить'></form>";
}

f_MClose( );

?>
