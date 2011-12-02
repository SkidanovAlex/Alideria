<script>begin_help( 'Таблица Опыта' );</script>

<center><table border=1><tr><td align=center width=100><b>Уровень</b></td><td width=100 align=center><b>Опыт</b></td></tr>

<?

include_once( 'arrays.php' );

function moo( $a )
{
	$res = "";
	while( $a >= 1000 )
	{
		$res = ( ( $a ) % 10 ) . $res;
		$a /= 10;
		settype( $a, 'integer' );
		$res = ( ( $a ) % 10 ) . $res;
		$a /= 10;
		settype( $a, 'integer' );
		$res = ( ( $a ) % 10 ) . $res;
		$a /= 10;
		settype( $a, 'integer' );
		$res = " ".$res;
	}
	$res = $a.$res;
	return $res;
}

foreach( $exp_table as $a=>$b ) print( "<tr><td align=right>".($a+1)."</td><td align=right>".moo( $b )."</td></tr>" );

?>

</table>

<script>end_help( );</script>

