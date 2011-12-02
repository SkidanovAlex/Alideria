<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">


<?

include( "../arrays.php" );

$costs = $item_level_costs;
$price_mul = Array( 2, 1, 3, 0.5 );
$stats_mul = Array( 2, 1, 1, 0.5 );

echo "<table border=1><tr><td rowspan=2>Уровень</td><td colspan=2>Оружие, Щиты</td><td colspan=2>Броня</td><td colspan=2>Ювелирка</td><td colspan=2>Плащ, Штаны, Перчатки, Обувь</td></tr>";
echo "<tr>";
for( $i = 0; $i < 4; ++ $i ) echo "<td>Статы</td><td>Цена</td>";

$lev = 1;
$stats = 2;
while( $lev <= 25 )
{
	echo "<tr><td>$lev</td>";
	
	for( $i = 0; $i < 4; ++ $i )
	{
		$st = $stats_mul[$i] * $stats;
		$pr = $price_mul[$i] * $costs[$lev];
		echo "<td>$st</td><td>$pr</td>";
	}
	
	echo "</tr>";

	$lev ++;
	$add = 2;
	if( $lev >= 10 ) $add = 3;
	if( $lev >= 20 ) $add = 4;
	if( $lev == 25 ) $add = 6;
	settype( $add, 'integer' );
	$stats += $add;
}

echo "</table>";


?>
