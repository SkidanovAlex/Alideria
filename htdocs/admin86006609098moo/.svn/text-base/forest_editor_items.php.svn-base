<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );
include_once( '../forest_functions.php' );

f_MConnect( );

include( 'admin_header.php' );

print( "<h1>Редактор Леса</h1>" );
print( "<br><br><a href=forest_editor_items.php target=_top>Выбрать подлокацию</a><br><a href=loc_editor.php target=_top>В редактор локаций</a><br><a href=mob_editor.php target=_top>В редактор мобов</a><br><a href=index.php target=_top>На главную</a><br><br>" );

if( !isset( $HTTP_GET_VARS[depth] ) )
{
	print( "<b>Укажите подлокацию:</b>" );
	for( $i = 0; $i <= 20; ++ $i ) print( "&nbsp;&nbsp;&nbsp;<a href=forest_editor_items.php?depth=$i>{$forest_names[$i]}</a>" );
	die( );
}

$depth = $HTTP_GET_VARS['depth'];
settype( $depth, 'integer' );
if( $depth < 0 ) $depth = 0;
if( $depth >= count( $forest_names ) ) $depth = count( $forest_names ) - 1;

?>

<?

print( "<b>Лес, {$forest_names[$depth]}</b><br>" );

if( isset( $HTTP_GET_VARS[item_id] ) )
{
	$item_id = $HTTP_GET_VARS[item_id];
	$number = $HTTP_GET_VARS[number];
	$chance = $HTTP_GET_VARS[chance];
	
	settype( $item_id, 'integer' );
	settype( $number, 'integer' );
	settype( $chance, 'integer' );
	
	f_MQuery( "INSERT INTO forest_items VALUES( $depth, $item_id, $number, $chance )" );
}
if( isset( $HTTP_GET_VARS[del] ) )
{
	$item_id = $HTTP_GET_VARS[del];
	settype( $item_id, 'integer' );
	f_MQuery( "DELETE FROM forest_items WHERE item_id = $item_id AND cell_type = $depth" );
}

$res = f_MQuery( "SELECT forest_items.*, items.name, items.price FROM forest_items, items WHERE cell_type = $depth AND items.item_id = forest_items.item_id ORDER BY item_id" );
if( mysql_num_rows( $res ) )
{
	$total_chance = 0;
	$average_income = 0;
	print( "<table border=1><tr><td align=center><b>Название вещи</b></td><td align=center><b>Кол-во</b></td><td align=center><b>Шанс</b></td></tr>" );
	while( $arr = f_MFetch( $res ) )
	{
		print( "<tr><td>$arr[name]</td><td align=right>$arr[number]</td><td align=right>$arr[chance]%</td><td><a href=forest_editor_items.php?depth=$depth&del=$arr[item_id]>Удалить</a></td></tr>" );
		$total_chance += $arr[chance];
		$average_income += $arr[chance] * $arr[price] * $arr[number];
	}
	$average_income /= 100;
	print( "<tr><td colspan=2><b>Общий шанс на лут:</b></td><td align=right><b>$total_chance%</b></td></tr>" );
	print( "<tr><td colspan=2><b>Средняя прибыль:</b></td><td align=right><b>$average_income</b></td></tr>" );
	print( "</table>" );
}
else print( "<i>С этой подлокацией не сопоставлено лута</i><br>" );

?>

<br>
<b>Добавить вещь</b><br>
<table>
<form action=forest_editor_items.php method=get>
<tr><td><input type=hidden name=depth value=<? print $depth ?>>
АйДи вещи: </td><td><input type=text name=item_id value=0></td></tr>
<tr><td>Количество: </td><td><input type=text name=number value=1></td></tr>
<tr><td>Шанс (%): </td><td><input type=text name=chance value=10></td></tr>
<tr><td>&nbsp;</td><td><input type=submit value='Добавить'></td></tr>
</form>
</table>
