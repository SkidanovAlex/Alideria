<?

include_once( 'creature.php' );
include_once( 'aura.php' );
include_once( "expirience.php" );

function flavorText( $login1, $login2, $genre1, $genre2 )
{
	$arr0 = Array( "Вода", "Природа", "Огонь" );
	$arr1 = Array( "Воды", "Природы", "Огня" );
	$arr2 = Array( "Начало Воды", "Начало Природы", "Огненное Начало" );
	$arr3 = Array( "началу Воды", "началу Природы", "огненному Началу" );
	$arr4 = Array( "маны Воды", "маны Природы", "Огненной маны" );
	
	$login1 = "<b>" . $login1 . "</b>";
	$login2 = "<b>" . $login2 . "</b>";

	$q = mt_rand( 1, 3 );
	if( $genre1 == $genre2 )
	{
		if( $q == 1 ) $st = "$login1 и $login2 обращаются к {$arr3[$genre1]}. Их позывы тщетны.";
		if( $q == 2 ) $st = "$login1 и $login2 выбрали стихию {$arr1[$genre1]} и не могут сконцентрироваться для произнесения заклинания.";
		if( $q == 3 ) $st = "$login1 и $login2 пытаются прикоснуться к источику {$arr4[$genre1]}. Источник не отзывается.";
	}
	else
	{
		if( $genre2 == ($genre1 + 1) % 3 )
		{
			$t = $genre1;
			$genre1 = $genre2;
			$genre2 = $t;
			$t = $login1;
			$login1 = $login2;
			$login2 = $t;
		}
		
		if( $q == 1 ) $st = "$login2 пытается прикоснуться к источнику {$arr4[$genre2]}, но терпит поражение от магии {$arr1[$genre1]} $login1.";
		if( $q == 2 ) $st = "В противоборстве {$arr1[$genre2]} $login2 и {$arr1[$genre1]} $login1 одерживает победу {$arr0[$genre1]}.";
		if( $q == 3 ) $st = "{$arr2[$genre2]} $login2 уступает {$arr3[$genre1]} $login1.";
	}
	
	$st .= "<br>";
	
	return $st;
}

function ShowAllPlayers( $combat_id, $side, $player_id )
{
	$res = f_MQuery( "SELECT * FROM combat_players WHERE combat_id=$combat_id AND side=$side AND player_id <> $player_id" );
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr[player_id] );
		$plr->ShowAttributes( );
	}
}

function CheckWinners( $combat_id, $side )
{
	$st = "";
	$enemy = 1 - $side;
	$cres = f_MQuery( "SELECT cur_turn FROM combats WHERE combat_id=$combat_id" );
	$carr = f_MFetch( $cres );
	$cur_turn = $carr[0];
	$res = f_MQuery( "SELECT player_id FROM combat_players WHERE combat_id=$combat_id AND side=$enemy AND ready <> 2" );
	if( mysql_num_rows( $res ) == 0 )
	{
		$res = f_MQuery( "SELECT combat_players.player_id, characters.login, characters.level, characters.wear_level, combat_players.since_turn FROM characters, combat_players WHERE combat_id=$combat_id AND side=$side AND ready < 2 AND characters.player_id=combat_players.player_id" );
		f_MQuery( "LOCK TABLE ch_messages WRITE, characters WRITE, combat_players WRITE" );
		while( $arr = f_MFetch( $res ) )
		{
			$tm = time( );
			$st .= "<b>$arr[1]</b> выигрывает битву!<br>";
			$utenka = getExp( $arr[2], $arr[3], $cur_turn - $arr[4] );
			f_MQuery( "UPDATE characters SET exp = exp + $utenka WHERE player_id = $arr[0]" );
			f_MQuery( "INSERT INTO ch_messages ( channel, message, author, time, target ) VALUES ( 0, 'Вы выигрываете в битве и получаете <b>$utenka</b> опыта', '', $tm, $arr[0] )" );
		}
			
		f_MQuery( "UPDATE combat_players SET ready=3 WHERE combat_id=$combat_id AND side=$side AND ready < 2" );
		f_MQuery( "UNLOCK TABLES" );
	}
	
	return $st;
}

function processCard( $a, $b, $my_id, $his_id, $slot, $combat_id, $card = false )
{
	if( $b == -1 )
		return "<b>$a</b> не колдует заклинания<br>";
	else if( $b == -2 )
		return "<b>$a</b> уже колдовал заклинание на этом ходу<br>";
	else
	{
		if( $card === false )
			$card = new Card( $b );
		
		$str = $card->Process( $my_id, $his_id, $slot, $combat_id );
		f_MQuery( "UPDATE player_attributes SET value = value - {$card->cost} WHERE player_id=$my_id AND attribute_id = 6 + {$card->genre}" ); // Лучше бы конечно через AlterAttrib
		
		return "<b>$a</b> колдует \" + ".$card->Text( )." + \".<br>". $str ."<br>";
	}
}

function CheckTurnOver( $combat_id, $side, $addit_text = '', $locked = false )
{
	global $HTTP_COOKIE_VARS;
	global $force_ai_card;


// Не писать тут код, завязанный на игроке. ибо используется в кроновом скрипте

	$q =  f_MValue( "SELECT COUNT(player_id) FROM combat_players WHERE combat_id=$combat_id AND ready = 0" );
	
	$enemy = 1 - $side;
	
	if( $q == 0 )
	{
		if( $locked ) f_MQuery( "UNLOCK TABLES" );
		f_MQuery( "LOCK TABLE combats WRITE" );
		$carr = f_MFetch( f_MQuery( "SELECT turn_done FROM combats WHERE combat_id=$combat_id" ) );
		if( $carr[0] == 0 )
		{
    		f_MQuery( "UPDATE combats SET turn_done = 1, turn_done_when=".time()." WHERE combat_id=$combat_id" );
    		f_MQuery( "UNLOCK TABLES" );
    		if( isset( $force_ai_card ) && $force_ai_card != -1 ) f_MQuery( "UPDATE combat_players SET card_id=$force_ai_card WHERE combat_id=$combat_id AND ai=1" );
			if( $addit_text != '' ) f_MQuery( "INSERT INTO combat_log( combat_id, string ) VALUES ( $combat_id, '$addit_text' )" );
    		include_once( "combat_turn.php" );
    		global $combat;
    		$combat = new Combat( $combat_id );
    		$combat->MakeTurn( );
    		f_MQuery( "UPDATE combats SET turn_done = 0 WHERE combat_id=$combat_id" );
    	} else f_MQuery( "UNLOCK TABLES" );
	} else if( $locked )
		f_MQuery( "UNLOCK TABLES" );

}

?>
