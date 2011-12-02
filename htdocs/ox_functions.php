<?

function create_game( $a, $b, $money )
{
	f_MQuery( "LOCK TABLE ox WRITE" );
	f_MQuery( "DELETE FROM ox WHERE p1=$a OR p2=$a OR $b<>0 AND (p1=$b OR p2=$b)" );
	$f = '';
	for( $i = 0; $i < 625; ++ $i ) $f .= " ";
	f_MQuery( "INSERT INTO ox ( p1, p2, f, winnings, ltm ) VALUES ( $a, $b, '$f', $money, ".time( )." )" );
	f_MQuery( "UNLOCK TABLES" );
}

function refr( $pid )
{
	$res = f_MQuery( "SELECT * FROM ox WHERE p1=$pid OR p2=$pid" );
	$arr = f_MFetch( $res );
	if( !$arr ) die( );

	if( $arr['won'] == 0 && $arr['ltm'] + 38 < time( ) )
	{
		$arr['won'] = ( $arr['turn'] % 2 ) + 1;
		f_MQuery( "UPDATE ox SET won=$arr[won] WHERE p1=$pid OR p2=$pid" );
	}

	$me_x = 0;
	if( $arr['p1'] == $pid ) $me_x = 1;

	echo "refr( '$arr[f]', $me_x, $arr[turn], $arr[won] );";

	if( $arr['won'] == 0 )
	{
        $dtm = time( ) - $arr['ltm'];
        $dtm *= 1000;
        print( "\n\nvar d0=new Date( );\n" );
        print( "tm = d0.getTime( ) - $dtm;\n" );
        print( "PingTimer( );\n" );
    }
}

?>
