<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['id'] ) )
{
	$id = $HTTP_GET_VARS['id'];
	$nm = $HTTP_GET_VARS['nm'];
	$prnt = $HTTP_GET_VARS['prnt'];
	$clr = $HTTP_GET_VARS['clr'];
	$stats = $HTTP_GET_VARS['stats'];
	
	settype( $id, 'integer' );
	settype( $prnt, 'integer' );
	settype( $stats, 'integer' );
	
	f_MQuery( "INSERT INTO attributes ( attribute_id, name, color, parent, stats ) VALUES( $id, '$nm', '$clr', $prnt, $stats )" );
	
	die( "<script>location.href='attrib_editor.php';</script>" );
}

if( isset( $HTTP_GET_VARS['del'] ) )
{
	$id = $HTTP_GET_VARS['del'];
	
	settype( $id, 'integer' );
	
	f_MQuery( "DELETE FROM attributes WHERE attribute_id=$id OR parent=$id" );
	
	die( "<script>location.href='attrib_editor.php';</script>" );
}

$res = f_MQuery( "SELECT * FROM attributes ORDER BY attribute_id" );

$n = 0;
$ids = Array( );
$names = Array( );
$parents = Array( );
$colors = Array( );
$p = Array( );

while( $arr = f_MFetch( $res ) )
{
	$ids[$n] = $arr['attribute_id'];
	$names[$n] = $arr['name'];
	$parents[$n] = $arr['parent'];
	$colors[$n] = $arr['color'];
	$stats[$n] = $arr['stats'];
	$stats2[$n] = ( $arr['stats'] % 5 == 0 ) ? $arr['stats'] / 5 : "{$arr['stats']}/5";
	
	++ $n;
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
	
	$st .= '<select>';
	
	return $st;
}

function dfs( $a, $d )
{
	global $ids;
	global $names;
	global $parents;
	global $colors;
	global $stats;
	global $stats2;
	global $n;
	
	for( $i = 0; $i < $d; ++ $i ) print( "&nbsp;+&nbsp;-&nbsp;" );
	if( $parents[$a] == -2 ) $la = "<i>Первичный</i>";
	else $la = "";
	print( "<font color={$colors[$a]}>[{$ids[$a]}] <b>{$names[$a]}</b></font>, $stats[$a] ($stats2[$a]) статов (<a href=attrib_editor.php?del={$ids[$a]}>Удалить</a>) $la<br>" );
	
	for( $i = 0; $i < $n; ++ $i )
		if( $parents[$i] == $ids[$a] )
			dfs( $i, $d + 1 );
}

$moo = Array( );
$moo[-3] = "(Глобальные)";
$moo[-2] = "(Первичный)";
$moo[-1] = "(Вторичный)";

print( "<b>Первичные:</b><br>" );

for( $i = 0; $i < $n; ++ $i )
	if( $parents[$i] == -2 )
		dfs( $i, 0 );

print( "<br><b>Вторичные:</b><br>" );

for( $i = 0; $i < $n; ++ $i )
	if( $parents[$i] == -1 )
	{
		$moo[$ids[$i]] = $names[$i];
		dfs( $i, 0 );
	}

print( "<br><b>Глобальные:</b><br>" );

for( $i = 0; $i < $n; ++ $i )
	if( $parents[$i] == -3 )
		dfs( $i, 0 );

f_MClose( );

?>

<br><br><table>
<form action=attrib_editor.php method=get>
<tr><td>АйДи:</td><td><input class=m_btn type=text name=id value=0></td></tr>
<tr><td>Имя:</td><td><input class=m_btn type=text name=nm></td></tr>
<tr><td>Цвет:</td><td><input class=m_btn type=text name=clr value=000000></td></tr>
<tr><td>Статы:</td><td><input class=m_btn type=text name=stats value=1></td></tr>
<tr><td>Родитель:</td><td>
<? echo create_select( "prnt", $moo, -1 ); ?>
</td></tr>
<tr><td>&nbsp;</td><td><input class=m_btn type=submit value=Добавить></td></tr>


</form>
</table>

<br><br><a href=index.php target=_top>На главную</a><br><br>

