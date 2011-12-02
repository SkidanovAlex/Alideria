<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );

$id = $HTTP_GET_VARS['id'];
$regimes2 = Array( 0 => "Отсутствует", 1 => "Присутствует" );

f_MConnect( );

include( 'quest_header.php' );

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

$res = f_MQuery( "SELECT * FROM npcs WHERE npc_id=$id" );

if( isset( $_GET[create_condition] ) )
{
	$arr = f_MFetch( $res );
	if( $arr[condition_id] == -1 )
	{
		f_MQuery( "INSERT INTO phrases ( text ) VALUES ( 'Условие на появление NPC $id' )" );
		$q = mysql_insert_id( );
		f_MQuery( "UPDATE npcs SET condition_id=$q WHERE npc_id=$id" );
	}
	$res = f_MQuery( "SELECT * FROM npcs WHERE npc_id=$id" );
}
if( isset( $_GET[delete_condition] ) )
{
	$arr = f_MFetch( $res );
	f_MQuery( "DELETE FROM phrases WHERE phrase_id=$arr[condition_id]" );
	f_MQuery( "UPDATE npcs SET condition_id=-1 WHERE npc_id=$id" );
	$res = f_MQuery( "SELECT * FROM npcs WHERE npc_id=$id" );
}

if( isset( $HTTP_POST_VARS[add_redirect] ) )
{
	$trigger_id = $HTTP_POST_VARS[trigger_id];
	$value = $HTTP_POST_VARS[value];
	$talk_id = $HTTP_POST_VARS[talk_id];
	
	f_MQuery( "INSERT INTO talk_redirects ( npc_id, trigger_id, value, talk_id ) VALUES ( $id, $trigger_id, $value, $talk_id )" );	
}
if( isset( $HTTP_GET_VARS[del_redirect] ) )
{
	$rid = $HTTP_GET_VARS[del_redirect];
	f_MQuery( "DELETE FROM talk_redirects WHERE redirect_id = $rid" );
}

if( isset( $_GET['add_item'] ) )
{
	$item_id = $_POST[item_id];
	settype( $item_id, 'integer' );
	f_MQuery( "DELETE FROM npc_items WHERE npc_id=$id AND item_id=$item_id" );
	f_MQuery( "INSERT INTO npc_items( npc_id, item_id ) VALUES ( $id, $item_id )" );
}
else if( isset( $_GET['del_item'] ) )
{
	$item_id = $_GET[del_item];
	settype( $item_id, 'integer' );
	f_MQuery( "DELETE FROM npc_items WHERE npc_id=$id AND item_id=$item_id" );
}

if( !mysql_num_rows( $res ) )
	print( "<i>Нет такого NPC</i><br>" );
else
{
	$arr = mysql_fetch_array( $res );

	echo "<table><tr><td>";

	print( "<table>" );
	print( "<form action=npc_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<tr><td>UIN:</td><td><b>$arr[npc_id]</b> (<a href=talk_list.php?uin=$arr[npc_id] target=lst_talk>показать толки только этого NPC</a>)</td></tr>" );
	print( "<tr><td>Имя:</td><td><input class=m_btn type=text name=nm value='$arr[name]'></td></tr>" );
	print( "<tr><td>Локация:</td><td>".create_select( 'location', $locs, $arr['location'] )."</td></tr>" );
	print( "<tr><td>Глубина:</td><td><input class=m_btn type=text name=depth value='$arr[depth]'></td></tr>" );
	print( "<tr><td>Картинка:</td><td><input class=m_btn type=text name=image value='$arr[image]'> <input class=btn40 type=text name=image_w value='$arr[image_w]'>x <input class=btn40 type=text name=image_h value='$arr[image_h]'>,  <input type=checkbox name=image_right ".($arr[image_right]?'checked':'')."></td></tr>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Применить'></td></tr>" );
	print( "</form>" );
	print( "<form action=npc_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<input type=hidden name=del>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Удалить'></td></tr>" );
	print( "</form>" );
	print( "</table>" );

	print( "<br><b>Условие Появления:</b><br>" );
	if( $arr[condition_id] == -1 ) echo "Отсутствует - <a href=npc_editor_mid.php?id=$id&create_condition>Создать</a><br>";
	else echo "<a href=phrase_editor.php?id=$arr[condition_id]&from=tac$id>Редактировать</a> - <a href=npc_editor_mid.php?id=$id&delete_condition>Удалить</a><br>";
	
	print( "<br><b>Редиректы:</b><br>" );
	
	$rres = f_MQuery( "SELECT * FROM talk_redirects WHERE npc_id = $arr[npc_id] ORDER BY redirect_id" );
	
	$rok = 0;
	while( $rarr = f_MFetch( $rres ) )
	{
		if( $rarr[value] == 0 ) $str = 'Отсутствует';
		else $str = 'Присутствует';
		if( $rok ) print( "Иначе если " );
		else print( "Если " );
		print( "триггер $rarr[trigger_id] $str, перейти к толку $rarr[talk_id] (<a href=npc_editor_mid.php?id=$id&del_redirect=$rarr[redirect_id]>Удалить</a>)<br>\n" );
		$rok = 1;
	}
	if( $rok ) print( "Иначе открыть толк по умолчанию<br>\n" );
	else print( "Нет ни одного редиректа<br>\n" );
	
	print( "<br>\n" );
		
	print( "<form action=npc_editor_mid.php?id=$id method=post>" );
	print( "<input type=hidden name=add_redirect value=1>" );
	print( "Если триггер <input class=m_btn type=text name=trigger_id value=0> " );
	print( create_select( "value", $regimes2, 0 ) );
	print( ", то толк: <input class=m_btn type=text name=talk_id value=0><input type=submit value=Ok>" );
	
	print( "</form>" );

	echo "</td><td valign=top><b>Вещи, связанные с этим NPC в энциклопедии:</b><br>";

	$res = f_MQuery( "SELECT a.item_id as id, b.* FROM npc_items as a LEFT JOIN items as b ON a.item_id=b.item_id WHERE npc_id=$id" );
	if( !f_MNum( $res ) ) echo "<i>Нет таких вещей</i>";
	else while( $qarr = f_Mfetch( $res ) ) echo "$qarr[name] - <a href=npc_editor_mid.php?id=$id&del_item=$qarr[id]>Удалить</a><br>";
	echo "<form action=npc_editor_mid.php?id=$id&add_item=1 method=post><input type=text class=m_btn name=item_id><input type=submit class=ss_btn value='Добавить'></form>";

	echo "</td></tr></table>";
	
	print( "<br><br><br><b>Толк по умолчанию</b><br>" );
	$HTTP_GET_VARS[talk_id] = $arr[talk_id];
	$moo = 1;
	include( "talk_editor.php" );
}

f_MClose( );

?>
