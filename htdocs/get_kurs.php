<?

include( "functions.php" );

f_MConnect( );

function moo( $name, $cur_val )
{
	global $my_load_page;

	$search_div = '<td align="right">';
	$search = '<td align="right">';

	$pos = strpos( $my_load_page, $name );
	if( $pos === false ) return $cur_val;
	$t1 = strpos( $my_load_page, $search_div, $pos ) + strlen( $search_div );
	if( $t1 === false ) return $cur_val;
	$t2 = strpos( $my_load_page, '</td>', $t1 );
	if( $t2 === false ) return $cur_val;

	$divisor = substr( $my_load_page, $t1, $t2 - $t1 );

	$t1 = strpos( $my_load_page, $search, $t2 ) + strlen( $search );
	if( $t1 === false ) return $cur_val;
	$t2 = strpos( $my_load_page, '</td>', $t1 );
	if( $t2 === false ) return $cur_val;

	$val = substr( $my_load_page, $t1, $t2 - $t1 );
	$val = str_replace( ',', '.', $val );

	echo "[$divisor][$val]";

	if( $divisor ) $val /= $divisor;

	if( $val * 1.03 > $cur_val || $cur_val * 1.03 > $val ) return $val;
	return $cur_val;
}

$sessions = curl_init();
$str = date( "d/m/Y" );
curl_setopt($sessions,CURLOPT_URL,"http://www.cbr.ru/currency_base/D_print.aspx?date_req=$str" );
curl_setopt($sessions, CURLOPT_POST, 1);
curl_setopt($sessions,CURLOPT_FOLLOWLOCATION,0);
curl_setopt($sessions, CURLOPT_HEADER , 0);
curl_setopt($sessions, CURLOPT_RETURNTRANSFER,1);
$my_load_page = curl_exec($sessions);

$eur = 30;
$usd = 25;
$grn = 4;

$res = mysql_query( "SELECT * FROM kurs" );
$arr = mysql_fetch_array( $res );
if( $arr )
{
	$eur = $arr['eur'];
	$usd = $arr['usd'];
	$grn = $arr['grn'];
}

$eur2 = moo( "EUR", $eur );
$usd2 = moo( "USD", $usd );
$grn2 = moo( "UAH", $grn );

echo "EUR WAS $eur, NOW $eur2<br>";
echo "USD WAS $usd, NOW $usd2<br>";
echo "GRN WAS $grn, NOW $grn2<br>";

mysql_query( "DELETE FROM kurs" );
mysql_query( "INSERT INTO kurs VALUES( $usd2, $eur2, $grn2 )" );

?>
