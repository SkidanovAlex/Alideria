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

$res = f_MQuery( "SELECT * FROM pets WHERE pet_id=$id" );

if( !mysql_num_rows( $res ) )
	print( "<i>Нет такого пета</i><br>" );
else
{
    $arr = mysql_fetch_array( $res );
	print( "<table><tr><td valign=top><table>" );
	print( "<form action=pet_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<tr><td>Название:</td><td><input class=m_btn type=text name=nm value='$arr[name]'></td></tr>" );
	print( "<tr><td>Картинка:</td><td><input class=m_btn type=text name=image value='$arr[image]'></td></tr>" );
	print( "<tr><td>&nbsp;</td><td><img src=../images/pets/{$arr[image]}.jpg></td></tr>" );
	print( "<tr><td>Стихия:</td><td>".create_select( 'genre', $genres, $arr['genre'] )."</td></tr>" );
	print( "<tr><td>Описание:</td><td><textarea name=descr cols=20 rows=3>$arr[descr]</textarea></td></tr>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Применить'></td></tr>" );
	print( "</form>" );
	print( "<form action=pet_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<input type=hidden name=del>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Удалить'></td></tr>" );
	print( "</form>" );
	print( "</table>" );
}

f_MClose( );

?>
