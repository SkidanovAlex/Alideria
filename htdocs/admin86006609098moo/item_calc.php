<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">


<script>

attr_names = new Array( );
attr_stats = new Array( );
level_costs = new Array( );

level_mul = 1;
price_mul = 1;

function safeInt( a )
{
	var ret = parseInt( a );
	if( isNaN( ret ) ) return 0;
	return ret;
}

function recalc( )
{
	calc_lev = 0;
	for( i in attr_names )
	{
		calc_lev += safeInt( document.getElementById( 'attr' + i ).value ) * attr_stats[i];
	}
	divisor = level_mul * 5;
	if( divisor == 0 ) divisor = 1;
	calc_lev /= divisor;
	calc_lev += 0.995;
	calc_lev = safeInt( calc_lev );
	new_lev = 1;
	new_stats = 2;
	while( 1 )
	{
		if( new_stats >= calc_lev )
		{
			calc_lev = new_lev;
			break;
		}
		new_lev ++;
    	add = 2;
    	if( new_lev >= 10 ) add = 3;
    	if( new_lev >= 20 ) add = 4;
    	if( new_lev == 25 ) add = 6;
		add = safeInt( add );
		new_stats += add;
	}
	
	var st = 'Уровень: <b>' + calc_lev + '</b><br>';
	if( calc_lev <= 25 ) st += "Цена: <b>" + level_costs[calc_lev] * price_mul + '</b><br>';
	else st += 'Уровень слишком высок<br>';
	
	document.getElementById( 'res' ).innerHTML = st;
}

<?

include( "../functions.php" );

f_MConnect( );
$res = f_MQuery( "SELECT attribute_id, name, stats FROM attributes" );
while( $arr = f_MFetch( $res ) )
	echo "attr_names[$arr[0]] = '$arr[1]';\nattr_stats[$arr[0]] = $arr[2];\n";
	
include( "../arrays.php" );

echo "level_costs[0] = 0;\n";
foreach( $item_level_costs as $a=>$b )
	echo "level_costs[$a] = $b\n";

echo "</script>";

echo "<table border=1><tr><td valign=top>";

echo "<b>Тип вещи</b><br><br>";

echo "<div id=cur_type>Сейчас выбрано: <u>Броня</u></div><br>\n";

foreach( $item_types as $i=>$nm ) if( $i >= 2 && $i <= 13 )
{
	print( "<a style='cursor:pointer' onclick='level_mul={$item_type_stats[$i]};price_mul={$item_type_costs[$i]};recalc();document.getElementById(\"cur_type\").innerHTML = \"Сейчас выбрано: <u>$nm</u>\";'>$nm</a><br>\n" );
}

?>

</td><td valign=top>

<script>

function dw( a ) { document.write( a ); }

dw( "<table>\n" );
for( i in attr_names )
{
	dw( "<tr><td>" );
	dw( attr_names[i] );
	dw( "</td><td>" );
	dw( "<input class=te_btn id=attr" + i + " onkeyup='recalc( );' value=0>" );
	dw( "</td></tr>\n" );
}

dw( "</table>\n" );

</script>

</td><td vAlign=top>

<div id=res>&nbsp;</div>

</td></tr></table>

<script>

recalc( );

</script>


