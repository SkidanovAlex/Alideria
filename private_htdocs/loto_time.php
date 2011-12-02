<?
require_once("time_functions.php");


include_once( 'player.php' );

f_MConnect( );

$res = f_MQuery( "SELECT * FROM loto_numbers" );
$num = f_MNum( $res );

if( $num == 7 )
{
	$num = 0;
	f_MQuery( "DELETE FROM loto_numbers" );
}

f_MQuery( "INSERT INTO loto_numbers VALUES( $num, ".mt_rand( 0, 9 )." )" );

$coef = Array( 0, 0, 2, 5, 20, 300, 10000, 1000000 );

if( $num == 6 )
{
	$nums = Array( );
	$res = f_MQuery( "SELECT * FROM loto_numbers ORDER BY id" );
	$n = 0;
	while( $arr = f_MFetch( $res ) )
		$nums[$n ++] = $arr['val'];

	f_MQuery( "DELETE FROM loto_past" );
	$res = f_MQuery( "SELECT * FROM loto_players" );
	while( $arr = f_MFetch( $res ) )
	{
		$val = $arr['val'];
    	$moo = 0;
    	for( $i = 0; $i < 7; ++ $i )
    	{
    		if( $nums[6 - $i] == $val % 10 ) ++ $moo;
    		$val = floor( $val / 10 );
    	}

    	$q = 100 * $coef[$moo];
    	if( $q != 0 )
    	{
    		$plr = new Player( $arr[player_id] );
			$plr->AddToLog( 0, $q, 5, 3 );
       		$plr->AddMoney( $q );
    	}
		f_MQuery( "UPDATE statistics SET casino_balance = casino_balance + 100 - $q" );
    	f_MQuery( "INSERT INTO loto_past VALUES( $arr[player_id], $arr[val], $q )" );
	}
	f_MQuery( "DELETE FROM loto_players" );
}

?>
