<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );
include( '../player.php' );
include( '../guild.php' );
include( '../attrib_relations.php' );
include_once( '../items.php' );

$id = $HTTP_GET_VARS['id'];

f_MConnect( );

$player = new Player( $_COOKIE[c_id] );

include( '../craft_functions.php' );
include( 'admin_header.php' );

$profs = Array( );
foreach( $guilds as $a=>$b ) if( $b[3] )
{
	$profs[$a] = $b[0];
}

foreach( $profs as $a=>$b )
{
	$a += 10000;
	$stats[$a] = "Ранг в гильдии ".$b;
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

$res = f_MQuery( "SELECT * FROM recipes WHERE recipe_id=$id" );

if( !mysql_num_rows( $res ) )
	print( "<i>Нет такого рецепта</i><br>" );
else
{
	$arr = mysql_fetch_array( $res );

	$item_id = $arr[result];
	if( settype( $item_id, 'integer' ) );

	$ires = f_MQuery( "SELECT image, level FROM items WHERE item_id = $item_id" );
	$iarr = f_MFetch( $ires );
	if( $iarr ){  $item_image = $iarr[0]; $item_level = $iarr[1]; }
	else { $item_image = ""; $item_level = 1; }

    if( isset( $_GET['change_image'] ) )
    {
		$ires = f_MQuery( "SELECT * FROM items WHERE learn_recipe_id = $id" );
		$iarr = f_MFetch( $ires );
		if( $iarr )
		{
			include_once( "create_image.php" );
			if( $item_image != '' ) f_MQuery( "UPDATE items SET image='auto/" .create_image_item( "items/" . $item_image ) . "' WHERE item_id=$iarr[item_id]" );
		}
    }
       
	elseif( isset( $_GET['create_item'] ) )
	{
		$fields = Array( );
		$fields["name"] = "Рецепт: $arr[name]";
		$fields["name2"] = "Рецепта: $arr[name]";
		$fields["name3"] = "Рецепту: $arr[name]";
		$fields["name4"] = "Рецепт: $arr[name]";
		$fields["name5"] = "Рецептом: $arr[name]";
		$fields["name6"] = "Рецепте: $arr[name]";
		$fields["name_m"] = "Рецепты: $arr[name]";
		$fields["name2_m"] = "Рецептов: $arr[name]";
		$fields["name3_m"] = "Рецептам: $arr[name]";
		$fields["name4_m"] = "Рецепты: $arr[name]";
		$fields["name5_m"] = "Рецептами: $arr[name]";
		$fields["name6_m"] = "Рецептах: $arr[name]";
		$fields["level"] = $item_level;
		$fields["type"] = 22;
		$fields["price"] = 50 * pow( 2, (int)( ( 1 + $item_level ) / 2 ) ) * ( 1.5 - 0.5 * ( $item_level % 2 ) );
		$fields["req"] = "";
		$fields["weight"] = 1 * $item_level;
		$fields["learn_recipe_id"] = $id;
		include_once( "create_image.php" );
		if( $item_image != '' ) $fields["image"] = "auto/".create_image_item( "items/" . $item_image );

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

	$arr1 = ParseItemStr( $arr[ingridients] );
	$arr2 = ParseItemStr( $arr[result] );
	$str1 = craftGetItemsList( $arr1 )."ГОС: $craft_cost";
	$str2 = craftGetItemsList( $arr2 )."ГОС: $craft_cost<br><br>Общее время производства: ".outCraftTime( );

	$req_str = itemReqStr( $arr['req'] );

	print( "<table>" );
	print( "<form action=craft_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<tr><td>UIN:</td><td><b>$arr[recipe_id]</b></td></tr>" );
	print( "<tr><td>Название:</td><td><input class=m_btn type=text name=nm value='$arr[name]'></td></tr>" );
	print( "<tr><td>Ранг:</td><td><input class=m_btn type=text name=rank value='$arr[rank]'></td></tr>" );
	print( "<tr><td>Уровень:</td><td><input class=m_btn type=text name=level value='$arr[level]'></td></tr>" );

	print( "<tr><td>Изучающая вещь:</td><td>" );

	$ires = f_MQuery( "SELECT * FROM items WHERE learn_recipe_id = $id" );
	$iarr = f_MFetch( $ires );
	if( !$iarr ) echo "<i>Нету</i> - <a href=craft_editor_mid.php?id=$id&create_item>Создать</a>";
	else echo "<img src=../images/items/$iarr[image]> <a href=item_editor_mid.php?id=$iarr[item_id] target=_blank><b>$iarr[name]</b></a> - <a href=craft_editor_mid.php?id=$id&change_image=1>Перегенерить картинку</a>";

	print( "</td></tr>" );


	print( "<tr><td>Ингридиенты:</td><td><textarea name=ingridients cols=20 rows=3>$arr[ingridients]</textarea></td><td>$str1</td></tr>" );
	print( "<tr><td>Результат:</td><td><textarea name=result cols=20 rows=3>$arr[result]</textarea></td><td>$str2</td></tr>" );
	print( "<tr><td>Требования:</td><td><textarea name=req cols=20 rows=3>$arr[req]</textarea></td><td vAlign=top>$req_str</td></tr>" );

	$old_arr = $arr;

$res = f_MQuery( "SELECT * FROM recipes WHERE recipe_id = $id" );
while( $arr = f_MFetch( $res ) )
{
	$item_id = $arr['result'];
	settype( $item_id, 'integer' );
	$iarr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$item_id" ) );
	$req = "";
	$a1 = ParseItemStr( $iarr['effect'] );
	$a2 = ParseItemStr( $iarr['req'] );
	$moo = Array( );
	foreach( $a1 as $a=>$b ) if( $a != 1 && $a != 101 )
	{
		foreach( $attrib_rels as $p=>$q )
		{
			$attr = -1;
			foreach( $q as $x ) if( $x == $a ) $attr = $p;
			if( $p == $a ) $attr = $p;
			if( $a == 33 || $a == 42 || $a == 51 ) $attr = $a;
			if( $attr != -1 ) $moo[$attr] = max( $moo[$attr], $b );
		}
	} else if( $a == 101 ) $moo[5] = max( $moo[5], (int)ceil($b/6) );
	foreach( $a2 as $a=>$b ) if( $a != 1 && $a != 101 )
	{
		foreach( $attrib_rels as $p=>$q )
		{
			$attr = -1;
			foreach( $q as $x ) if( $x == $a ) $attr = $p;
			if( $p == $a ) $attr = $p;
			if( $attr != -1 ) $moo[$attr] = max( $moo[$attr], $b );
		}
	} else if( $a == 101 ) $moo[5] = max( $moo[5], (int)ceil($b/6) );
	$req = "";
	foreach( $moo as $a=>$b )
	{
		if( $req != "" ) $req .= ":";
		$b = $iarr['level'] * 2;
		$req .= "$a:$b";
	}
	$req .= ".";
	$rank = 0;
	if( $iarr[level] > 3 )
	{
		if( strlen( $req ) == 0 ) ;
		else if( $req[strlen( $req ) - 1] == '.' ) $req[strlen( $req ) - 1] = ':';
		else $req = $req . ":";
		$req .= 10000 + $arr[prof];
		$req .= ":";
		$req .= ( $iarr['level'] - 3 );
		$rank = ( $iarr['level'] - 3 );
		$req .= ".";
	}
	if( $iarr[level] < 1 ) $iarr[level] = 1;
	if( $req == "." ) $req = "";
}

	$arr = $old_arr;


	echo "<tr><td>Рекомендуемые:</td><td>$req</td></tr>";
	print( "<tr><td>Гильда:</td><td>".create_select( 'prof', $profs, $arr['prof'] )."</td></tr>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Применить'></td></tr>" );
	print( "</form>" );
	print( "<form action=craft_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<input type=hidden name=del>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Удалить'></td></tr>" );
	print( "</form>" );
	print( "</table>" );
	
}

echo "<b>Аттрибуты в требованиях, соответствующие рангу</b><br>";
foreach( $profs as $a=>$b )
{
	$a += 10000;
	printf( "$a - Ранг игрока в гильдии $b<br>" );
}

f_MClose( );

?>
