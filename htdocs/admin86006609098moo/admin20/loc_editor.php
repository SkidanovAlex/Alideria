<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

$sloc = 0;
if( isset( $HTTP_POST_VARS[loc] ) )
{
	$loc = HtmlSpecialChars( $HTTP_POST_VARS[loc], ENT_QUOTES );
	$depth = HtmlSpecialChars( $HTTP_POST_VARS[depth] );
	$text = HtmlSpecialChars( $HTTP_POST_VARS[text] );
	$name = HtmlSpecialChars( $HTTP_POST_VARS[nm] );
	$name2 = HtmlSpecialChars( $HTTP_POST_VARS[nm2] );
	
	if( $name || $text || $name2 )
	{
		$res = f_MQuery( "SELECT * FROM loc_texts WHERE loc = $loc AND depth = $depth" );
		if( f_MNum( $res ) )
			f_MQuery( "UPDATE loc_texts SET text='$text', title='$name', title2 = '$name2' WHERE loc = $loc AND depth = $depth" );
		else f_MQuery( "INSERT INTO loc_texts VALUES ( $loc, $depth, '$text', '$name', '$name2', 0, '' )" );
	}
	else
	{
		f_MQuery( "DELETE FROM loc_texts WHERE loc = $loc AND depth = $depth" );
		f_MQuery( "DELETE FROM loc_links WHERE ( loc1 = $loc AND depth1 = $depth ) OR ( loc2 = $loc AND depth2 = $depth )" );
	}
	$sloc = $loc;
}

if( isset( $HTTP_POST_VARS[loc1] ) )
{
	$loc1 = $HTTP_POST_VARS[loc1];
	$depth1 = $HTTP_POST_VARS[depth1];
	$loc2 = $HTTP_POST_VARS[loc2];
	$depth2 = $HTTP_POST_VARS[depth2];
	
	f_MQuery( "DELETE FROM loc_links WHERE loc1 = $loc1 AND depth1 = $depth1 AND loc2 = $loc2 AND depth2 = $depth2" );
	f_MQuery( "INSERT INTO loc_links VALUES ( $loc1, $depth1, $loc2, $depth2 )" );
	
	$sloc = $loc1;
}

if( isset( $HTTP_GET_VARS[dell1] ) )
{
	$loc1 = $HTTP_GET_VARS[dell1];
	$depth1 = $HTTP_GET_VARS[delp1];
	$loc2 = $HTTP_GET_VARS[dell2];
	$depth2 = $HTTP_GET_VARS[delp2];

	f_MQuery( "DELETE FROM loc_links WHERE loc1 = $loc1 AND depth1 = $depth1 AND loc2 = $loc2 AND depth2 = $depth2" );

	$sloc = $loc1;
}

if( isset( $HTTP_POST_VARS[loc_shop] ) )
{
	$loc = $HTTP_POST_VARS[loc_shop];
	$depth = $HTTP_POST_VARS[depth_shop];
	$name = $HTTP_POST_VARS[shop_name];
	
	f_MQuery( "INSERT INTO shops ( owner_id, buy_mul, sell_mul, regime, location, place, name, cost ) VALUES ( -1, 50, 100, 1, $loc, $depth, '$name', 0 )" );
	$sloc = $loc;
}

if( isset( $HTTP_GET_VARS[del_shop] ) )
{
	$shop_id = $HTTP_GET_VARS[del_shop];
	$res = f_MQuery( "SELECT location FROM shops WHERE shop_id = $shop_id" );
	$arr = f_MFetch( $res );
	$sloc = $arr[0];
	f_MQuery( "DELETE FROM shops WHERE shop_id = $shop_id" );
	f_MQuery( "DELETE FROM shop_goods WHERE shop_id = $shop_id" );
}

if( isset( $HTTP_POST_VARS[loc_status] ) )
{
	$loc = $HTTP_POST_VARS[loc_status];
	$depth= $HTTP_POST_VARS[depth_status];
	$status = $HTTP_POST_VARS[status];
	
	f_MQuery( "UPDATE loc_texts SET status=$status WHERE loc = $loc AND depth = $depth" );
	$sloc = $loc;
}

if( isset( $HTTP_POST_VARS[loc_img] ) )
{
	$loc = $HTTP_POST_VARS[loc_img];
	$depth= $HTTP_POST_VARS[depth_img];
	$img = $HTTP_POST_VARS[img];
	
	f_MQuery( "UPDATE loc_texts SET img='$img' WHERE loc = $loc AND depth = $depth" );
	$sloc = $loc;
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

print( "<br><br><a href=index.php target=_top>На главную</a><br><br>" );
$ok = 0;
foreach( $locs as $a=>$b )
{
	if( $ok ) print( " :: " );
	$ok = 1;
	print( "<a href=loc_editor.php?sloc=$a><b>$b</b></a>" );
}
print( "<br>" );

if( isset( $HTTP_GET_VARS['sloc'] ) )
	$sloc = $HTTP_GET_VARS['sloc'];

settype( $sloc, 'integer' );
$res = f_MQuery( "SELECT * FROM loc_texts WHERE loc = $sloc ORDER BY loc, depth" );

$statuses[0] = '(Обычная локация)';
$statuses[1] = 'Арена';
$statuses[2] = 'Рынок';
$statuses[3] = 'Озеро';
$statuses[4] = 'Зал Кланов';
$statuses[5] = 'Зал Гильдий';
$statuses[6] = 'Торговые ряды';

foreach( $prof_locs as $a=>$b ) $statuses[- $a] = $b;

print( "<br>" );
while( $arr = f_MFetch( $res ) )
{
	print( "<b>{$locs[$arr[loc]]} локация $arr[depth]: </b> Название: <b>$arr[title]</b> Название перехода: <b>$arr[title2]</b><br> $arr[text]<br>" );
	if( $arr['status'] )
	{
		print( "Статус: " );
		print( "<u>".$statuses[$arr['status']]."</u><br>" );
	}
	
	if( $arr['img'] == '' ) $arr['img'] = 'Нет картинки';
	print( "Картинка: <u>$arr[img]</u><br>" );
	
	$okk = 0;
	$res2 = f_MQuery( "SELECT shop_id, name FROM shops WHERE owner_id = -1 AND location = $arr[loc] AND place = $arr[depth]" );
	while( $arr2 = f_MFetch( $res2 ) )
	{
		print( "Магазин: <u>{$arr2[name]}</u> [<a href=loc_editor.php?del_shop=$arr2[shop_id]>X</a>]<br>" );
		$okk = 1;
	}
	$res2 = f_MQuery( "SELECT loc_links.loc2, loc_texts.title2, loc_links.depth2 FROM loc_links, loc_texts WHERE loc1 = $arr[loc] AND depth1 = $arr[depth] AND loc = loc2 AND depth = depth2 ORDER BY loc2, depth2" );
	while( $arr2 = f_MFetch( $res2 ) )
	{
		print( "> <i>{$arr2[1]} ({$locs[$arr2[0]]})</i> <a href=loc_editor.php?dell1=$arr[loc]&delp1=$arr[depth]&dell2=$arr2[0]&delp2=$arr2[2]>Удалить</a><br>" );
		$okk = 1;
	}
	$res2 = f_MQuery( "SELECT loc_links.loc1, loc_texts.title2, loc_links.depth1 FROM loc_links, loc_texts WHERE loc2 = $arr[loc] AND depth2 = $arr[depth] AND loc = loc1 AND depth = depth1 ORDER BY loc1, depth1" );
	while( $arr2 = f_MFetch( $res2 ) )
	{
		print( "> <i>{$arr2[1]} ({$locs[$arr2[0]]})</i> <a href=loc_editor.php?dell2=$arr[loc]&delp2=$arr[depth]&dell1=$arr2[0]&delp1=$arr2[2]>Удалить</a><br>" );
		$okk = 1;
	}
	if( $arr[loc] == 0 )
	{
		print( "<a href=cave_editor.php?depth=$arr[depth]>Открыть редактор пещер для этой глубины</a><br>" );
		$okk = 1;
	}
	if( $okk ) printf( "<br>" );
	printf( "<br>" );
}

print( "<table width=90%><tr><td valign=top width=50%>" );	
print( "<form action=loc_editor.php method=post>" );
print( create_select( 'loc', $locs, $sloc ) );
print( "<input type=text name=depth size=2>" );
print( " Название: <input type=text name=nm size=30>" );
print( "<br>К чему? (напр. К Рынку): <input type=text name=nm2 size=30>" );
print( "<br><textarea rows=20 cols=50 name=text></textarea>" );
print( "<br><input type=submit>" );
print( "</form>" );
print( "</td><td valign=top width=50%>" );
print( "<b>Добавить переход</b><br>" );
print( "<form action=loc_editor.php method=post>" );
print( "Из: ".create_select( 'loc1', $locs, $sloc ) );
print( "<input type=text name=depth1 size=2><br>" );
print( "В: ".create_select( 'loc2', $locs, $sloc ) );
print( "<input type=text name=depth2 size=2>" );
print( "<br><input type=submit></form><br>" );
print( "<br><b>Добавить магазин</b><br>" );
print( "<form action=loc_editor.php method=post>" );
print( "Куда: ".create_select( 'loc_shop', $locs, $sloc ) );
print( "<input type=text name=depth_shop size=2><br>" );
print( "Название: <input type=text name=shop_name size=30>" );
print( "<br><input type=submit></form><br>" );
print( "<br><b>Изменить статус</b><br>" );
print( "<form action=loc_editor.php method=post>" );
print( "Где: ".create_select( 'loc_status', $locs, $sloc ) );
print( "<input type=text name=depth_status size=2><br>" );
print( "Статус: ".create_select( 'status', $statuses, 0 ) );
print( "<br><input type=submit></form><br>" );
print( "<br><b>Поставить картинку</b><br>" );
print( "<form action=loc_editor.php method=post>" );
print( "Где: ".create_select( 'loc_img', $locs, $sloc ) );
print( "<input type=text name=depth_img size=2><br>" );
print( "Картинка: <input type=text name=img>" );
print( "<br><input type=submit></form><br>" );
print( "</td></tr></table>" );

f_MClose( );

?>

