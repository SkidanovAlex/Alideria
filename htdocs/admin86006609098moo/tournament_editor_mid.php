<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );

$id = $HTTP_GET_VARS['id'];
settype( $id, 'integer' );

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

$res = f_MQuery( "SELECT * FROM tournament_announcements WHERE tournament_id=$id" );

if( !mysql_num_rows( $res ) )
	print( "<i>Нет такойго турнира</i><br>" );
else
{
	$arr = mysql_fetch_array( $res );

	print( "<table>" );
	print( "<form action=tournament_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<tr><td>UIN:</td><td><b>$arr[tournament_id]</b> - <a href=tournament_editor_stop.php?id=$id>Остановить все бои в рамках турнира</a></td></tr>" );
	print( "<tr><td>Название:</td><td>" );

	echo "<input class=m_btn type=text name=nm value='$arr[name]'>";
	print( "</td></tr>" );
	print( "<tr><td>Тип:</td><td><input class=m_btn type=text name=type value='$arr[type]'><br><small>0 - боевой, 1 - магия, 2 - групповой клановый</small></td></tr>" );
	print( "<tr><td>Минимальный Уровень:</td><td><input class=m_btn type=text name=min_level value='$arr[min_level]'></td></tr>" );
	print( "<tr><td>Максимальный Уровень:</td><td><input class=m_btn type=text name=max_level value='$arr[max_level]'></td></tr>" );
	print( "<tr><td>Призовые:</td><td><input class=m_btn type=text name=prize value='$arr[prize]'></td></tr>" );

	$day = date( "d", $arr['date'] );
	$month = date( "m", $arr['date'] );
	$year = date( "Y", $arr['date'] );
	$hour = date( "H", $arr['date'] );
	$minute = date( "i", $arr['date'] );

	print( "<tr><td>Дата проведения:</td><td>" );

	echo "<input class=btn40 type=text name=bdd value='$day'>.<input class=btn40 type=text name=bdm value='$month'>.<input class=btn80 type=text name=bdy value='$year'> <input class=btn40 type=text name=bdh value='$hour'>:<input class=btn40 type=text name=bdi value='$minute'>";

	echo "</td></tr>";

	print( "<tr><td>&nbsp;</td><td><input class=m_btn type=submit value='Применить'></td></tr>" );
	print( "</form>" );
	print( "<form action=tournament_editor_apply.php method=post>" );
	print( "<input type=hidden name=id value=$id>" );
	print( "<input type=hidden name=del>" );
	print( "<tr><td>Удалить турнир:<br>хер ты попадешь по кнопке случайно</td><td><input class=m_btn type=submit value='Удалить' style='width:5px;height:5px;'></td></tr>" );
	print( "</form>" );
	print( "</table>" );
	
}

f_MClose( );

?>
