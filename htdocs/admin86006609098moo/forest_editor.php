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
	3  => "<font color=maroon><b>K</b></font>",
	4  => "<font color=grey><b>В</b></font>",
	5  => "<font color=brown><b>Б</b></font>",
	6  => "<font color=blue><b>Р</b></font>",
	7  => "<font color=yellow><b>Э</b></font>",
	8  => "<font color=white><b>Е</b></font>",
	9  => "<font color=black><b>П</b></font>",
	10 => "<font color=red><b>C</b></font>",
	11 => "<font color=red><b>@</b></font>",
	12 => "<font color=#AA6446><b>#</b></font>",
	13 => "<font color=#646464><b>*</b></font>",
	14 => "<font color=#726693><b>&</b></font>",
	20 => "<font color=#FC0FC0><b>М</b></font>",
	21 => "<font color=#FF0000><b>З</b></font>",
	22 => "<font color=#F4C430><b>У</b></font>",
	23 => "<font color=#FF0000><b>U</b></font>",
	
	100 => "<font color=brown><b>R</b></font>",
	101 => "<font color=aqua><b>R</b></font>",
	102 => "<font color=#008800><b>R</b></font>",
	110 => "<font color=#00FF00><b>R</b></font>",
	
	200 => "<font color=brown><b>G</b></font>",
	201 => "<font color=white><b>G</b></font>",
	202 => "<font color=gray><b>G</b></font>",
	203 => "<font color=#0000FF><b>G</b></font>",
	204 => "<font color=black><b>G</b></font>",
	205 => "<font color=red><b>G</b></font>",
	206 => "<font color=yellow><b>G</b></font>",
	210 => "<font color=#00FF00><b>G</b></font>"
);



include( 'admin_header.php' );
$locat = $_GET['locat'];
print( "<h1>Редактор Западного Леса</h1>" );
print( "<br><br><a href=loc_editor.php target=_top>В редактор локаций</a><br><a href=mob_editor.php target=_top>В редактор мобов</a><br><a href=index.php target=_top>На главную</a><br><br>" );

print("<a href='forest_editor.php?locat=1'>Западный Лес</a><br>");
print("<a href='forest_editor.php?locat=6'>Подземная Река</a><br>");
print("<a href='forest_editor.php?locat=7'>Подгорье</a><br>");

if (!$locat) die();

echo "<div id=map>&nbsp;</div>";

//print( "<big>Утя, тут ничего еще не работает :)<br>" );

print( "<hr>&nbsp;&nbsp;<b>Выберите тайлинг:</b><br>" );

echo "<div id=selected_tile>&nbsp;</div>";

foreach( $forest_names as $a=>$b )
{
	if (($locat==1 && $a<100) || ($locat==6 && $a>=100 && $a<200) || ($locat==7 && $a>=200 && $a<300))
	print( "&nbsp;&nbsp;".$letters[$a].": <a style='cursor:pointer' ondoubleclick='fill_tile( $a, \"$b\" )' onclick='select_tile( $a, \"$b\" )'>".$b."</a><br>" );
}
echo "<br>";

?>

<script src='../js/ajax.js'></script>
<script>

var cur_tile = 0;
var locat = 1;

function select_tile( a, b )
{
	cur_tile = a;
	document.getElementById( 'selected_tile' ).innerHTML = 'Текущий тайл: <u>' + b + '</u>';
}

function set_tile( x, y, str, t )
{
//	document.getElementById( 'tile_' + x + '_' + y ).innerHTML = str;
	var ttl = document.getElementById( 'tile_' + x + '_' + y ).title;
	ttl = ttl.substr(0, ttl.indexOf('\n')+1) + t;
	document.getElementById( 'tile_' + x + '_' + y ).title = ttl;
	document.getElementById( 'tile_' + x + '_' + y ).style.backgroundColor = str.substr(str.indexOf('=')+1, str.indexOf('>')-str.indexOf('=')-1);
}

function change_tile( x, y )
{
	query( "forest_editor_ref.php", x + "|" + y + "|" + cur_tile + "|" + locat );
}

function fill_tile( x, y )
{
	query( "forest_editor_fill.php", x + "|" + y + "|" + cur_tile );
}

document.write( "<table border=1 cellspacing=0 cellpadding=0>" );

for( i = 0; i < 100; ++ i )
{
	document.write( "<tr>" );
	for( j = 0; j < 100; ++ j )
	{
		var ii=(j+50)%100;
		var jj=(i+50)%100;
		document.write( "<td style='font-size:8;' height=10 width=10><div title='"+(ii+50)%100+"/"+jj+" | "+(ii*100+jj)+"\n' style='cursor:pointer;' onclick='change_tile( " + ii + ", " + jj + " )' id=tile_" + ii + "_" + jj + ">&nbsp;</div></td>" );
	}
	document.write( "</tr>" );
}

document.write( "</table>" );

<?
if ($locat==1)
	print("cur_tile=0;");
if ($locat==6)
	print("cur_tile=100;");
if ($locat==7)
	print("cur_tile=200;");
if (!$locat) die();
else
{
	print ("locat = ".$locat.";");
}
$ut = new ForestUtils( $locat );

for( $i = 0; $i < 100; ++ $i )
	for( $j = 0; $j < 100; ++ $j )
	{
		$val = $ut->getTile( $i, $j );
		print( "set_tile( $i, $j, '{$letters[$val]}', '{$forest_names[$val]}' );\n" );
	}

?>

</script>

<?
if (false && $locat==6 && $player->player_id==1)
{
$ut = new ForestUtils( $locat );
for( $i = 0; $i < 50; ++ $i )
	for( $j = 0; $j < 100; ++ $j )
	{
		$t1 = $ut->getTile($i, $j);
		$t2 = $ut->getTile($i+50, $j);
		$ut->setTile($i, $j, $t2);
		$ut->setTile($i+50, $j, $t1);
	}
}
?>
