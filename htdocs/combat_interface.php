<?

include_once( "player.php" );
include_once( "card.php" );
include_once( "items.php" );

function CombatSetCard( $player_id, $a, $turn = false )
{
	$player = new Player( $player_id );
	
	$res = f_MQuery( "SELECT combat_id, side FROM combat_players WHERE player_id={$player->player_id} AND ready <> 2" );
	$arr = f_MFetch( $res );
	
	if( !$arr )
		return false;
	
	$combat_id = $arr[0];
	$side = $arr[1];

	$cres = f_MQuery( "SELECT last_turn_made, cur_turn FROM combats WHERE combat_id=$combat_id" );
	$carr = f_MFetch( $cres );
	if( $turn !== false && $carr['cur_turn'] != $turn && time( ) - $carr[0] < 30 )
	{
		return false;
	}

	if( $a == -1 )
	{
		f_MQuery( "UPDATE combat_players SET card_id=$a WHERE player_id={$player->player_id}" );
		return true;
	}

	$res = f_MQuery( "SELECT card_id FROM player_cards WHERE player_id={$player->player_id} AND card_id=$a" );
	$arr = f_MFetch( $res );
	
	if( !$arr )
		return;

	$a = $arr[0];
		
	$card = new Card( $a );
	$card->LoadPlayer( $player );
	if( $card->cost > 0 )
	{
    	$mana = $player->GetAttr( 130 + 10 * $card->genre );
    	if( $mana < $card->cost )
    	{
    		print( "alert( '” вас не хватает маны на это заклинание.' );" );
    		return false;
    	}
	}
	
	if( !( $player->CheckItemReq( $card->req ) ) || $player->level < $card->level )
	{
		print( "alert( '“ребовани€ дл€ использовани€ этого заклинани€ слишком велики.' );" );
		return false;
	}
	
	f_MQuery( "UPDATE combat_players SET card_id=$a WHERE player_id={$player->player_id}" );
	return true;
}

function CombatSetReady( $player_id, $turn = false )
{
	$player = new Player( $player_id );

	f_MQuery( "LOCK TABLE combats WRITE, combat_players WRITE" );

	$res = f_MQuery( "SELECT combat_id, side, ready FROM combat_players WHERE player_id={$player->player_id} AND ready < 2" );
	$arr = f_MFetch( $res );

	if( !$arr )
	{
		f_MQuery( "UNLOCK TABLES" );
		die( "»грок не в бою" );
	}

	$combat_id = $arr[0];
	$side = $arr[1];

	$cres = f_MQuery( "SELECT last_turn_made, cur_turn FROM combats WHERE combat_id=$combat_id" );
	$carr = f_MFetch( $cres );
	if( $turn !== false && $carr['cur_turn'] != $turn && time( ) - $carr[0] < 30 )
	{
		f_MQuery( "UPDATE combat_players SET card_id=-1, ready=0 WHERE player_id=$player_id AND ready < 2" );
		f_MQuery( "UNLOCK TABLES" );
		return;
	}

	if( $arr[2] == 0 )
	{
    	f_MQuery( "UPDATE combat_players SET ready=1 WHERE player_id={$player->player_id}" );

    	include_once( 'combat_functions.php' );
    	CheckTurnOver( $combat_id, $side, '', true );
    }
    else
    {
//    	LogError( "Ётот чувак бы щас продублировал ход" );
    	f_MQuery( "UNLOCK TABLES" );
    }
}

?>
