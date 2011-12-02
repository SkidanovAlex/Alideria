<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

print( "<h1>Редактор Пещер</h1>" );
print( "<br><br><a href=cave_editor.php target=_top>Выбрать глубину</a><br><a href=loc_editor.php target=_top>В редактор локаций</a><br><a href=mob_editor.php target=_top>В редактор мобов</a><br><a href=index.php target=_top>На главную</a><br><br>" );

if( !isset( $HTTP_GET_VARS[depth] ) )
{
	print( "<b>Укажите глубину:</b>" );
	for( $i = 0; $i <= 20; ++ $i ) print( " <a href=cave_editor.php?depth=$i>$i</a>" );
	die( );
}

$depth = $HTTP_GET_VARS['depth'];
settype( $depth, 'integer' );
if( $depth < 0 ) $depth = 0;
if( $depth > 20 ) $depth = 20;

?>

После того как персонаж завершает движение или исследование в пещерах происходит следующее:<br>
1. Если персонаж перешел на глубину, на которой прежде никогда не был, его 100% атакует Моб, у которого данная глубина указана в поле "Охраняет глубину".<br>
2. Иначе, если персонаж уже был на этой глубине, или ни один моб ее не охраняет, то<br>
2.1. С вероятностью 30% на персонажа нападает моб, который просто обитает на этой глубине (выбирается случайно)<br>
2.2. Остальные 70% можно распределить в этом редакторе на выпадение лута.<br><br>

<?

print( "<b>Пещеры, глубина $depth</b><br>" );

if( isset( $HTTP_GET_VARS[item_id] ) )
{
	$item_id = $HTTP_GET_VARS[item_id];
	
	settype( $item_id, 'integer' );
	
	f_MQuery( "INSERT INTO cave_items VALUES( $depth, $item_id )" );
}
if( isset( $HTTP_GET_VARS[del] ) )
{
	$item_id = $HTTP_GET_VARS[del];
	settype( $item_id, 'integer' );
	f_MQuery( "DELETE FROM cave_items WHERE item_id = $item_id AND depth = $depth" );
}

$res = f_MQuery( "SELECT cave_items.*, items.name, items.price FROM cave_items, items WHERE depth = $depth AND items.item_id = cave_items.item_id ORDER BY item_id" );
if( mysql_num_rows( $res ) )
{
	include_once("../kopka3.php");
	$kopka = new Kopka( );
	print( "<table border=1><tr><td align=center><b>Название вещи</b></td></tr>" );
	while( $arr = f_MFetch( $res ) )
	{
		$kopka->AddItem($arr[item_id], $arr[price]);
		print( "<tr><td>$arr[name]</td><td><a href=cave_editor.php?depth=$depth&del=$arr[item_id]>Удалить</a></td></tr>" );
	}
	print( "</table><br><br>" );
	$ret = $kopka->GetItemId( 15, 150 + $depth * 20 + ( $depth ? 75 : 0 ), false );
	foreach( $ret as $a ) echo "$a<br>";
}
else print( "<i>С этой глубиной не сопоставлено лута</i><br>" );

?>

<br>
<b>Добавить вещь</b><br>
<table>
<form action=cave_editor.php method=get>
<tr><td><input type=hidden name=depth value=<? print $depth ?>>
АйДи вещи: </td><td><input type=text name=item_id value=0></td></tr>
<tr><td>&nbsp;</td><td><input type=submit value='Добавить'></td></tr>
</form>
</table>
