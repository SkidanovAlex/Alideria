<?

include_once( 'player.php' );

f_MConnect( );

f_MQuery( "LOCK TABLES lottery WRITE" );
$res = f_MQuery( "SELECT * FROM lottery" );
if( !f_MNum( $res ) )
{
	$str = "Никто не участвовал в предыдущей лотерее.";
	f_MQuery( "UNLOCK TABLES" );
}	
else
{
	$val = 0;
	$plrs = Array( );
	$vals = Array( );
	$n = 0;
	while( $arr = f_MFetch( $res ) )
	{
		$val += $arr['value'];
		$plrs[$n] = $arr['player_id'];
		$vals[$n] = $arr['value'];
        ++ $n;
	}

	$winnings = round( $val * 0.8 );
	$rand = mt_rand( 1, $val );
	$cur = 0;
	$player_id = -1;
	for( $i = 0; $i < $n; ++ $i )
	{
		$cur += $vals[$i];
		if( $cur >= $rand )
		{
			$player_id = $plrs[$i];
			break;
		}
	}

	f_MQuery( "DELETE FROM lottery" );
	f_MQuery( "UNLOCK TABLES" );

	include( 'lottery_update.php' );
	update_lottery( );

	if( $player_id == -1 ) RaiseError( "Лотерею выиграл никто" );
	else
	{
		$plr = new Player( $player_id );
		$plr->AddToLog( 0, $winnings, 5, 2 );
		$plr->AddMoney( $winnings );
		f_MQuery( "UPDATE statistics SET casino_balance = casino_balance + $val - $winnings" );
		$str = "Персонаж <script>document.write( ".$plr->Nick( )." );</script> выиграл <b>$winnings</b> дублонов!!!";
	}
}

	$file = fopen( 'lottery_winner.html', 'w+' );
	fwrite( $file, $str );
	fclose( $file );


?>
