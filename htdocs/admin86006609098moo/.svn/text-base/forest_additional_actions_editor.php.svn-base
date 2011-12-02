<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../forest_functions.php' );
include( '../arrays.php' );

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

echo "<br><a href=index.php>Назад на главную</a><br>";

if( isset( $_GET['new'] ) )
{
	f_MQuery( "INSERT INTO phrases ( text ) VALUES ( 'FAA condition' )" );
	$cond_id = mysql_insert_id( );
	f_MQuery( "INSERT INTO phrases ( text ) VALUES ( 'FAA action' )" );
	$action_id = mysql_insert_id( );

	f_MQuery( "INSERT INTO forest_additional_actions ( text, time, condition_id, action_id, loc, depth, cell_type ) VALUES ( 'Новое действие в лесу', 600, $cond_id, $action_id, 0, 1000, -1 )" );
	$id = mysql_insert_id( );
}
else if( isset( $_GET['id'] ) )
{
	$id = $_GET['id'];
	settype( $id, 'integer' );
}
else
{
	echo "<br>";

	if( isset( $_GET['del'] ) )
	{
		$del_id = $_GET['del'];
		$res = f_MQuery( "SELECT * FROM forest_additional_actions WHERE entry_id=$del_id" );
		$arr = f_MFetch( $res );
		f_MQuery( "DELETE FROM phrases WHERE phrase_id = $arr[action_id]" );
		f_MQuery( "DELETE FROM phrases WHERE phrase_id = $arr[condition_id]" );
		settype( $del_id, 'integer' );
		f_MQuery( "DELETE FROM forest_additional_actions WHERE entry_id=$del_id" );
	}

	$res = f_MQuery( "SELECT * FROM forest_additional_actions ORDER BY cell_type, loc, depth, text, entry_id" );
	if( !f_MNum( $res ) ) echo "<i>Нет ни одного действия</i><br>";
	while( $arr = f_MFetch( $res ) )
	{
		if( $arr['cell_type'] != -1 ) echo "<a href=forest_additional_actions_editor.php?id=$arr[entry_id]>[$arr[entry_id]] {$forest_names[$arr[cell_type]]} : $arr[text]</a> (<a href=forest_additional_actions_editor.php?del=$arr[entry_id]>[X]</a>)<br>";
		else echo "<a href=forest_additional_actions_editor.php?id=$arr[entry_id]>[$arr[entry_id]] {$loc_names[$arr[loc]]}/{$arr[depth]} : $arr[text]</a> (<a href=forest_additional_actions_editor.php?del=$arr[entry_id]>[X]</a>)<br>";
	}                                                                                                                                             
	?>

	<button onclick='location.href="forest_additional_actions_editor.php?new";'>Добавить</button>

	<?
	die( );
}


if( isset( $_GET['add_var'] ) )
{
	$chance = $_GET['chance'];
	settype( $chance, 'integer' );
	f_MQuery( "INSERT INTO phrases ( text ) VALUES ( 'FAAV action' )" );
	$phrase_id = mysql_insert_id( );
	f_MQuery( "INSERT INTO forest_add_act_var( entry_id, phrase_id, chance1000000 ) VALUES ( $id, $phrase_id, $chance );" );
	die( "<script>location.href='phrase_editor.php?id=$phrase_id&from=faa$id';</script>" );
}
if( isset( $_GET['ddel'] ) )
{
	$del_id = $_GET['ddel'];
	settype( $del_id, 'integer' );
	f_MQuery( "DELETE FROM forest_add_act_var WHERE phrase_id=$del_id" );
	f_MQuery( "DELETE FROM phrases WHERE phrase_id=$del_id" );
}


// edit :o)

if( isset( $_POST['txt'] ) )
{
	$text = $_POST['txt'];
	$flv = $_POST['flv'];
	$time = $_POST['time'];
	settype( $time, 'integer' );
	$text = htmlspecialchars( $text );
	$flv = htmlspecialchars( $flv );
	$cell_type = $_POST['cell_type'];
	$loc = $_POST['loc'];
	$depth = $_POST['depth'];
	settype( $cell_type, 'integer' );
	settype( $loc, 'integer' );
	settype( $depth, 'integer' );
	

	f_MQuery( "UPDATE forest_additional_actions SET flavor_text = '$flv', text='$text', time=$time, cell_type=$cell_type, loc=$loc, depth=$depth WHERE entry_id=$id" );
}

$res = f_MQuery( "SELECT * FROM forest_additional_actions WHERE entry_id=$id" );
$arr = f_MFetch( $res );

echo "<a href=forest_additional_actions_editor.php>Назад в редактор доп. действий</a><br><br>";

$forest_names[-1] = "Не в лесу";

if( !$arr ) die( "Нет такого действия" );
echo "<table><form action=forest_additional_actions_editor.php?id=$id method=post>";
echo "<tr><td>UIN: </td><td><b>$arr[entry_id]</b></td></tr>";
echo "<tr><td>Текст ссылки: </td><td><input type=text class=m_btn name=txt value='$arr[text]'></td></tr>";
echo "<tr><td>Доп. текст в локации: </td><td><input type=text class=m_btn name=flv value='$arr[flavor_text]'></td></tr>";
echo "<tr><td>Где: </td><td>".create_select( "cell_type", $forest_names, $arr[cell_type] )."<br>Loc (только если выше выбрано 'Не в лесу'): ".create_select( "loc", $loc_names, $arr[loc] )."<br>Depth: <input type=text class=m_btn name=depth value='$arr[depth]'></td></tr>";
echo "<tr><td>Время: </td><td><input type=text class=m_btn name=time value='$arr[time]'> сек</td></tr>";
echo "<tr><td>&nbsp;</td><td><input type=submit class=m_btn value='Поменять'></td></tr>";
echo "</form></table>";
echo "Если событие в лесу - просто надо выбрать тип клетки, если не в лесу, или нужна конкретная кора - надо выбрать \"Не в лесу\" и указать локацию и глубину<br><br>";
$frm = 'faa'.$arr['entry_id'];
echo "<a href=phrase_editor.php?id=$arr[condition_id]&from=$frm>Условие</a><br>";
echo "<a href=phrase_editor.php?id=$arr[action_id]&from=$frm>Стандартное Действие</a><br>";
echo "При выборе двух ссылок откроется редактор фраз. При переходе по первой надо указать только условие, при переходе по второй только действие по истечение времени. Текст в условии - текст, который будет показываться в процессе. Текст в действии - текст системки в конце";

echo "<br><br><b>Нестандартные действия</b><br>";


$res = f_MQuery( "SELECT forest_add_act_var.*,phrases.text FROM phrases, forest_add_act_var WHERE entry_id = $arr[entry_id] AND phrases.phrase_id = forest_add_act_var.phrase_id" );
if( !f_MNum( $res ) ) echo "<i>Нет нестандартных действий</i><br>";
$tc = 0;
while( $arr = f_MFetch( $res ) )
{
	$chance = $arr['chance1000000'] / 10000.0;
	$tc += $chance;
	echo "<a href=phrase_editor.php?id=$arr[phrase_id]&from=$frm>$arr[text]</a> [$chance%] (<a href='http://www.alideria.ru/admin/forest_additional_actions_editor.php?id=$id&ddel=$arr[phrase_id]'>[X]</a>)<br>";
}

$atc = 100 - $tc;
echo "Общий шанс на нестандартное: $tc%, на станадртное: $atc%";

?>	

<form action=forest_additional_actions_editor.php method=get>
Шанс (в 1/1000000): <input class=m_btn name=chance type=text value=500000>
<input type=hidden name=add_var value=1><input class=m_btn type=submit value='Добавить'>
<input type=hidden name=id value=<?=$id?>>
</form>
