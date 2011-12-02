<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );

$id = $HTTP_GET_VARS['id'];

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

$res = f_MQuery( "SELECT * FROM help_topics WHERE topic_id=$id" );

if( !mysql_num_rows( $res ) )
	print( "<i>Нет такого топика</i><br>" );
else
{
	$arr = mysql_fetch_array( $res );
	print( "<table>" );
	print( "<form action=help_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<tr><td>ID:</td><td><input class=m_btn type=text name=new_id value='$arr[topic_id]'></td></tr>" );
	print( "<tr><td>Родитель:</td><td><input class=m_btn type=text name=parent_id value='$arr[parent_id]'></td></tr>" );
	print( "<tr><td>Заголовок:</td><td><input class=m_btn type=text name=title value='$arr[title]'></td></tr>" );
	print( "<tr><td>URL:</td><td><input class=m_btn type=text name=url value='$arr[url]'></td></tr>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Применить'></td></tr>" );
	print( "</form>" );
	print( "<form action=help_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<input type=hidden name=del>" );
	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Удалить'></td></tr>" );
	print( "</form>" );
}

f_MClose( );

?>
