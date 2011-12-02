<?

include_once ( 'combat_interface.php' );

function processAI( $player_id )
{
	$mob = new Player( $player_id );
	$cards = $mob->VisibleCards( );
	
	if( count( $cards ) )
	{
		$rand = mt_rand( 0, 2 );
		f_MQuery( "UPDATE combat_players SET target=$rand WHERE player_id={$player_id}" );		
		$rand = mt_rand( 0, count( $cards ) - 1 );
		CombatSetCard( $player_id, $cards[$rand] );
	}
	CombatSetReady( $player_id );
}

function processAI2( $player_id, $row )
{
	$l = strlen( $row );

	if( $l <= 2 )
	{
		processAI( $player_id );
		return;
	}

	$moo = array( 1, 1, 1 );
	$sum = 3;
	for( $i = 1; $i < $l - 1; ++ $i )
	{
		if( $row[$i - 1] == $row[$l - 2] && $row[$i] == $row[$l - 1] )
		{
			$moo[(int)$row[$i + 1]] += 1;
			$moo[( (int)$row[$i + 1] + 1 ) % 3] += 3;
			$sum += 4;
		}
	}

	$rnd = mt_rand( 0, $sum - 1 );
	if (mt_rand(0, 99) >= 20 ) // добавляем элемент рандома в выбор тактики
		if( $rnd < $moo[0] ) $genre = 0;
		else if( $rnd < $moo[0] + $moo[1] ) $genre = 1;
		else $genre = 2;
	else
		$genre = mt_rand(0, 2);

//	LogError( "ATT: $moo[0] $moo[1] $moo[2] : $genre" );

	$mob = new Player( $player_id );
	$cards = $mob->VisibleCardsGenre( $genre );
	if( count( $cards ) == 0 ) $cards = $mob->VisibleCards( );

	if( count( $cards ) )
	{
		$rand = mt_rand( 0, 2 );
		f_MQuery( "UPDATE combat_players SET target=$rand WHERE player_id={$player_id}" );		

		$rand = mt_rand( 0, count( $cards ) - 1 );
		CombatSetCard( $player_id, $cards[$rand] );
	}
	CombatSetReady( $player_id );
}

function leavecombatAI( $player_id, $combat_id )
{
	$mob = new Player( $player_id );
	$mob->LeaveCombat( $combat_id );
}

?>
