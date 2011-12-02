<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );
include_once( '../forest_functions.php' );

f_MConnect( );

$letters = Array(
	0  => "<font color=lime><b>О</b></font>",
	1  => "<font color=green><b>Д</b></font>",
	2  => "<font color=aqua><b>З</b></font>",
	3  => "<font color=maroon><b>Х</b></font>",
	4  => "<font color=grey><b>В</b></font>",
	5  => "<font color=brown><b>Б</b></font>",
	6  => "<font color=blue><b>Р</b></font>",
	7  => "<font color=yellow><b>Э</b></font>",
	8  => "<font color=white><b>Е</b></font>",
	9  => "<font color=black><b>П</b></font>",
	10 => "<font color=red><b>C</b></font>"
);

include( 'admin_header.php' );

print( "<h1>Редактор Западного Леса</h1>" );
print( "<br><br><a href=loc_editor.php target=_top>В редактор локаций</a><br><a href=mob_editor.php target=_top>В редактор мобов</a><br><a href=index.php target=_top>На главную</a><br><br>" );

echo "<div id=map>&nbsp;</div>";

//print( "<big>Утя, тут ничего еще не работает :)<br>" );

print( "<hr>&nbsp;&nbsp;<b>Выберите тайлинг:</b><br>" );

echo "<div id=selected_tile>&nbsp;</div>";

foreach( $forest_names as $a=>$b )
{
	print( "&nbsp;&nbsp;".$letters[$a].": <a style='cursor:pointer' ondoubleclick='fill_tile( $a, \"$b\" )' onclick='select_tile( $a, \"$b\" )'>".$b."<br>" );
}


?>

<script src='../js/ajax.js'></script>
<script>

var cur_tile = 0;

function select_tile( a, b )
{
	cur_tile = a;
	document.getElementById( 'selected_tile' ).innerHTML = 'Текущий тайл: <u>' + b + '</u>';
}

function set_tile( x, y, str )
{
	document.getElementById( 'tile_' + x + '_' + y ).innerHTML = str;
}

function change_tile( x, y )
{
	query( "forest_editor_ref.php", x + "|" + y + "|" + cur_tile );
}

function fill_tile( x, y )
{
	query( "forest_editor_fill.php", x + "|" + y + "|" + cur_tile );
}

document.write( "<table cellspacing=0 cellpadding=0>" );

for( i = 0; i < 100; ++ i )
{
	document.write( "<tr>" );
	for( j = 0; j < 100; ++ j )
	{
		document.write( "<td><div onclick='change_tile( " + i + ", " + j + " )' id=tile_" + i + "_" + j + ">&nbsp;</div></td>" );
	}
	document.write( "</tr>" );
}

document.write( "</table>" );

<?

$ut = new ForestUtils( 1 );

for( $i = 0; $i < 100; ++ $i )
	for( $j = 0; $j < 100; ++ $j )
	{
		$val = $ut->getTile( $i, $j );
		print( "set_tile( $i, $j, '{$letters[$val]}' );\n" );
	}

?>

</script>
