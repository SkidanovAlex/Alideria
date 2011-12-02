<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );
include_js( '../functions.js' );

$id = $HTTP_GET_VARS['id'];

f_MConnect( );

include( 'quest_header.php' );

if( isset( $HTTP_POST_VARS[add_part] ) )
{
	f_MQuery( "INSERT INTO quest_parts ( quest_id, text ) VALUES ( $id, '$HTTP_POST_VARS[text]' )" );
}
if( isset( $HTTP_GET_VARS[del_part] ) )
{
	$q = $HTTP_GET_VARS[del_part];
	f_MQuery( "DELETE FROM quest_parts WHERE quest_part_id = $q" );
	f_MQuery( "DELETE FROM quest_item_reqs WHERE quest_part_id = $q" );
}
if( isset( $HTTP_POST_VARS[add_item_q] ) )
{
	f_MQuery( "INSERT INTO quest_item_reqs VALUES ( $HTTP_POST_VARS[add_item_q], $HTTP_POST_VARS[item_id], $HTTP_POST_VARS[number] )" );
}
if( isset( $_GET[del_item_q] ) )
{
	$item_id = (int)$_GET['del_item'];
	$q = (int)$_GET['del_item_q'];
	f_MQuery( "DELETE FROM quest_item_reqs WHERE item_id=$item_id AND quest_part_id=$q" );
}

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

$res = f_MQuery( "SELECT * FROM quests WHERE quest_id=$id" );

if( !mysql_num_rows( $res ) )
	print( "<i>Нет такого квеста</i><br>" );
else
{
	$arr = mysql_fetch_array( $res );
	print( "<table>" );
	print( "<form action=quest_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<tr><td>ID:</td><td><b>$arr[quest_id]</b></td></tr>" );
	print( "<tr><td>Название:</td><td><input class=m_btn type=text name=nm value='$arr[name]'></td></tr>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Применить'></td></tr>" );
	print( "</form>" );
	print( "<form action=quest_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<input type=hidden name=del>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Удалить'></td></tr>" );
	print( "</form>" );
	print( "</table>" );

	if( isset( $_GET['qpd'] ) )
	{
		$qpd = (int)$_GET['qpd'];
		$v = $_GET['v'];
		f_MQuery( "UPDATE quest_parts SET text='$v' WHERE quest_part_id=$qpd" );
	}
	
	print( "<br><br><b>Части</b><br>" );
	$res = f_MQuery( "SELECT * FROM quest_parts WHERE quest_id = $id ORDER BY quest_part_id" );
	if( !mysql_num_rows( $res ) )
		print( "<i>Нет частей</i><br>" );
	else
	{
		while( $arr = mysql_fetch_array( $res ) )
		{
			print( "<hr><b>id: &nbsp;$arr[quest_part_id] ></b> <input type=text id=moo$arr[quest_part_id] value='$arr[text]'><button  onclick='location.href=\"quest_editor_mid.php?id=$id&qpd=$arr[quest_part_id]&v=\" + _(\"moo$arr[quest_part_id]\").value'>Поменять</button><br>" );
			print( "<a href=quest_editor_mid.php?id=$id&del_part=$arr[quest_part_id]>Удалить</a><br>" );
			$ires = f_MQuery( "SELECT items.*, quest_item_reqs.number FROM items, quest_item_reqs WHERE quest_part_id = $arr[quest_part_id] AND quest_item_reqs.item_id = items.item_id" );
			while( $iarr = f_MFetch( $ires ) )
				print( "[$iarr[number]] <b>$iarr[name]($iarr[item_id]):</b> <a href=quest_editor_mid.php?id=$id&del_item=$iarr[item_id]&del_item_q=$arr[quest_part_id]>Удалить</a><br>" );
				
			print( "<form action=quest_editor_mid.php?id=$id method=post><input type=hidden name=add_item_q value=$arr[quest_part_id]>Item_id: <input name=item_id type=text value='0' class=m_btn>Number: <input name=number type=text value='0' class=m_btn><input type=submit value='Добавить часть' class=m_btn></form>" );
		}
		print( "<hr>" );
	}
	
	print( "<form action=quest_editor_mid.php?id=$id method=post><input type=hidden name=add_part><textarea rows=5 cols=15 name=text></textarea><br><input type=submit value='Добавить часть' class=m_btn></form>" );
}

f_MClose( );

?>
