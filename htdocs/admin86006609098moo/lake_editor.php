<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

print( "<h1>Редактор Добывающих Локаций</h1>" );
print( "<br><br><a href=lake_editor.php target=_top>Выбрать гильдию</a><br><a href=loc_editor.php target=_top>В редактор локаций</a><br><a href=index.php target=_top>На главную</a><br><br>" );

if( !isset( $_GET['guild_id'] ) )
{
	echo "<b>Выбери гильдию:</b><br>";
	echo "<a href=lake_editor.php?guild_id=101>Рыбаков</a><br>";
	echo "<a href=lake_editor.php?guild_id=102>Собирателей</a><br>";
	echo "<a href=lake_editor.php?guild_id=103>Шахтеров</a><br>";
	echo "<a href=lake_editor.php?guild_id=108>Охотников</a><br>";
	die( );
}

$guild_id = $_GET['guild_id'];
settype( $guild_id, 'integer' );

if( isset( $HTTP_GET_VARS[item_id] ) )
{
	$item_id = $HTTP_GET_VARS[item_id];
	$rank = $HTTP_GET_VARS[rank];
	
	settype( $item_id, 'integer' );
	settype( $rank, 'integer' );
	
	f_MQuery( "INSERT INTO lake_items VALUES( $item_id, $guild_id, $rank )" );
}
if( isset( $HTTP_GET_VARS[del] ) )
{
	$item_id = $HTTP_GET_VARS[del];
	settype( $item_id, 'integer' );
	f_MQuery( "DELETE FROM lake_items WHERE guild_id = $guild_id AND item_id = $item_id" );
}

$res = f_MQuery( "SELECT lake_items.*, items.name, items.price FROM lake_items, items WHERE items.item_id = lake_items.item_id AND guild_id = $guild_id ORDER BY item_id" );
if( mysql_num_rows( $res ) )
{
	print( "<table border=1><tr><td align=center><b>Название вещи</b></td><td align=center><b>Минимальный ранг</b></td></tr>" );
	while( $arr = f_MFetch( $res ) )
	{
		print( "<tr><td>$arr[name]</td><td align=right>$arr[rank]</td><td><a href=lake_editor.php?guild_id=$guild_id&del=$arr[item_id]>Удалить</a></td></tr>" );
	}
	print( "</table>" );
}
else print( "<i>Сейчас нет вещей для этой гильдии</i><br>" );

?>

<br>
<b>Добавить вещь</b><br>
<table>
<form action=lake_editor.php method=get>
<input type=hidden name=guild_id value=<? echo $guild_id; ?>>
<tr><td>АйДи вещи: </td><td><input type=text name=item_id value=0></td></tr>
<tr><td>Минимальный ранг: </td><td><input type=text name=rank value=0></td></tr>
<tr><td>&nbsp;</td><td><input type=submit value='Добавить'></td></tr>
</form>
</table>
