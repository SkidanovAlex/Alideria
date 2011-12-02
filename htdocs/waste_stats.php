<?

include_once( "player.php" );

function winItem( $player_id, $game_id, $item_id )
{
	f_MQuery( "LOCK TABLE waste_items WRITE" );

	$a1 = f_MFetch( f_MQuery( "SELECT * FROM waste_items WHERE player_id=$player_id AND game_id=$game_id AND item_id=$item_id" ) );
	if( $a1[0] ) f_MQuery( "UPDATE waste_items SET number=number+1 WHERE player_id=$player_id AND game_id=$game_id AND item_id=$item_id" );
	else f_MQuery( "INSERT INTO waste_items ( player_id, game_id, item_id, number ) VALUES ( $player_id, $game_id, $item_id, 1 )" );

	f_MQuery( "UNLOCK TABLES" );
}

function storeGame( $game_id, $winner, $looser, $money, $great = false )
{
	f_MQuery( "LOCK TABLE waste_stats WRITE, waste_pairs WRITE" );

	$a1 = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM waste_stats WHERE player_id=$winner AND game_id=$game_id" ) );
	if( $a1[0] == 0 ) f_MQuery( "INSERT INTO waste_stats( player_id, game_id ) VALUES( $winner, $game_id )" );

	$a2 = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM waste_stats WHERE player_id=$looser AND game_id=$game_id" ) );
	if( $a2[0] == 0 ) f_MQuery( "INSERT INTO waste_stats( player_id, game_id ) VALUES( $looser, $game_id )" );

	f_MQuery( "UPDATE waste_stats SET wins = wins + 1 WHERE game_id=$game_id AND player_id=$winner" );
	f_MQuery( "UPDATE waste_stats SET loses = loses + 1 WHERE game_id=$game_id AND player_id=$looser" );

	$a1 = f_MFetch( f_MQuery( "SELECT count( p1 ) FROM waste_pairs WHERE p1=$winner AND p2=$looser" ) );
	if( $a1[0] )
		f_MQuery( "UPDATE waste_pairs SET w1=w1+1 WHERE p1=$winner AND p2=$looser" );
	else
	{
    	$a1 = f_MFetch( f_MQuery( "SELECT count( p1 ) FROM waste_pairs WHERE p2=$winner AND p1=$looser" ) );
    	if( $a1[0] )
    		f_MQuery( "UPDATE waste_pairs SET w2=w2+1 WHERE p2=$winner AND p1=$looser" );
    	else
    		f_MQuery( "INSERT INTO waste_pairs( game_id, p1, p2, w1 ) VALUES ( $game_id, $winner, $looser, 1 )" );
	}

	f_MQuery( "UNLOCK TABLES" );

	$plr = new Player( $winner );
	if( $game_id == 0 ) checkZhorik( $plr, 15, 3 ); // квест жорика три раза выиграть в магию

	if( $money > 0 )
	{
		$plr->AddMoney( $money * 2 );
		$plr->AddToLogPost( 0, $money * 2, 26, $game_id, $looser );
		f_MQuery( "UPDATE waste_stats SET balance=balance+$money WHERE game_id=$game_id AND player_id=$winner" );
		f_MQuery( "UPDATE waste_stats SET balance=balance-$money WHERE game_id=$game_id AND player_id=$looser" );
		$plr->syst( "Вы выиграли в игре на ставки и получили <b>$money</b> ".my_word_str( $money, "дублон", "дублона", "дублонов" )."!", false );
	}

	if( $great )
	{
		if( mt_rand( 1, 50 ) == 1 )
		{
			$plr->syst2( "Это была невероятная игра. Наблюдавшие за ней люди восторженно аплодируют вам, а один мальчуган в благодарность за невероятное зрелище протягивает вам <a href=help.php?id=1010&item_id=17 target=_blank>Медную Руду</a>!" );
			$plr->AddItems( 17, 1 );
			$plr->AddToLogPost( 17, 1, 24, 3 );
			winItem( $winner, $game_id, 17 );
		}
		else if( mt_rand( 1, 49 ) == 1 )
		{
			$plr->syst2( "Такой игры тут не видывали давно! Вокруг вас собралось большое количество народа, не привыкшего видеть что-то столь захватывающее в этих местах. Восторженная публика бросает вам цветы, вы успеваете поймать <a href=help.php?id=1010&item_id=96 target=_blank>Цикорий</a>!" );
			$plr->AddItems( 96, 1 );
			$plr->AddToLogPost( 96, 1, 24, 3 );
			winItem( $winner, $game_id, 96 );
		}
	}
}

function storeDraw( $game_id, $winner, $looser, $money )
{
	f_MQuery( "LOCK TABLE waste_stats WRITE, waste_pairs WRITE" );

	$a1 = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM waste_stats WHERE player_id=$winner AND game_id=$game_id" ) );
	if( $a1[0] == 0 ) f_MQuery( "INSERT INTO waste_stats( player_id, game_id ) VALUES( $winner, $game_id )" );

	$a2 = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM waste_stats WHERE player_id=$looser AND game_id=$game_id" ) );
	if( $a2[0] == 0 ) f_MQuery( "INSERT INTO waste_stats( player_id, game_id ) VALUES( $looser, $game_id )" );

	f_MQuery( "UPDATE waste_stats SET draws = draws + 1 WHERE game_id=$game_id AND player_id=$winner" );
	f_MQuery( "UPDATE waste_stats SET draws = draws + 1 WHERE game_id=$game_id AND player_id=$looser" );

	$a1 = f_MFetch( f_MQuery( "SELECT count( p1 ) FROM waste_pairs WHERE p1=$winner AND p2=$looser AND game_id=$game_id" ) );
	if( $a1[0] )
		f_MQuery( "UPDATE waste_pairs SET d=d+1 WHERE p1=$winner AND p2=$looser" );
	else
	{
    	$a1 = f_MFetch( f_MQuery( "SELECT count( p1 ) FROM waste_pairs WHERE p2=$winner AND p1=$looser AND game_id=$game_id" ) );
    	if( $a1[0] )
    		f_MQuery( "UPDATE waste_pairs SET d=d+1 WHERE p2=$winner AND p1=$looser" );
    	else
    		f_MQuery( "INSERT INTO waste_pairs( game_id, p1, p2, d ) VALUES ( $game_id, $winner, $looser, 1 )" );
	}

	f_MQuery( "UNLOCK TABLES" );

	if( $money > 0 )
	{
		$plr1 = new Player( $winner );
		$plr2 = new Player( $looser );

		
		$plr1->AddMoney( $money );
		$plr2->AddMoney( $money );
		$plr1->AddToLogPost( 0, $money, 26, $game_id );
		$plr2->AddToLogPost( 0, $money, 26, $game_id );
	}
}

?>
