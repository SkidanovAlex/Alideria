<?php
//poker_class.php

define( 'SEAT_COUNT', 6 );
define( 'MAX_TIME_TO_MOVE', 30 );
define( 'SHOWDOWN_TIME', 7 );
define( 'PER_DECK', 52 );
define( 'WINNER_SHOWTIME', 4000 );
define( 'WINNER_SECTIME', 4 );

//states
//0 - not moved
//1 - moved
//2 - pass
//3 - all-in

//phases
//0 - pre-flop
//1 - flop
//2 - turn
//3 - river
//4 - showdown

class Combination
{	var $cards;
	var $straight;
	var $flash;
	var $kare;
	var $set;
	var $high_pair;
	var $low_pair;
	var $kickers;
	var $combo_name;
	var $pos;

	function Combination( $c )
	{
		sort( $c );
		$this->cards = $c;
		$vals = array( );
		$suits = array( );
		for ( $i = 0; $i < 5; ++ $i )
		{			$vals[$i] = $c[$i] >> 2;
			$suits[$i] = $c[$i] & 3;		}
		//straight test
		for ( $i = 1; $i < 5; ++ $i )
		{			if ( !( $vals[$i - 1] == $vals[$i] - 1 ||
				$i == 5 && $vals[$i - 1] == 3 && $vals[$i] == 12 ) )// A-2
				break;
		}
		$this->straight = ( $i == 5 );		//flash test
		for ( $i = 1; $i < 5; ++ $i )
		{
			if ( $suits[$i - 1] != $suits[$i] )
				break;
		}
		$this->flash = ( $i == 5 );
		$this->kare = -1;
		$this->set = -1;
		$this->high_pair = -1;
		$this->low_pair = -1;
		$this->kickers = array( );
		$cnt = 1;
		$c[5] = -1;
		for ( $i = 1; $i < 6; ++ $i )
		{
			if ( $vals[$i - 1] == $vals[$i] )
				++ $cnt;
			else
			{				if ( $cnt == 2 )
				{					if ( $this->high_pair < 0 || $this->high_pair < $vals[$i - 1] )
					{						$this->low_pair = $this->high_pair;
						$this->high_pair = $vals[$i - 1];					}
					else
					{						$this->low_pair = $vals[$i - 1];					}				}
				else
				if ( $cnt == 3 )
				{					$this->set = $vals[$i - 1];				}
				else
				if ( $cnt == 4 )
				{					$this->kare = $vals[$i - 1];				}
				$cnt = 1;			}		}
		if ( $this->straight )
		{			if ( $vals[0] == 0 && $vals[4] == 12 )
			{
				$this->kickers[4] = $vals[4];				for ( $i = 0; $i < 4; ++ $i )
					$this->kickers[$i] = $vals[4 - $i];
			}
			else
			{
				for ( $i = 0; $i < 5; ++ $i )
					$this->kickers[$i] = $vals[5 - $i];
			}		}
		else
		if ( $this->flash )
		{			for ( $i = 0; $i < 5; ++ $i )
				$this->kickers[$i] = $vals[5 - $i];
		}
		else
		if ( $this->kare >= 0 )
		{			for ( $i = 0; $i < 4; ++ $i )
				$this->kickers[$i] = $this->kare;
			$kn = 4;
			for ( $i = 4; $i >= 0; -- $i )
				if ( $vals[$i] != $this->kare )
					$this->kickers[$kn++] = $vals[$i];
		}
		else
		if ( $this->set >= 0 )
		{
			for ( $i = 0; $i < 3; ++ $i )
				$this->kickers[$i] = $this->set;
			$kn = 3;
			for ( $i = 4; $i >= 0; -- $i )
				if ( $vals[$i] != $this->set )
					$this->kickers[$kn++] = $vals[$i];
		}
		else
		if ( $this->high_pair >= 0 )
		{			for ( $i = 0; $i < 2; ++ $i )
				$this->kickers[$i] = $this->high_pair;
			$kn = 2;
			if ( $this->low_pair >= 0 )
			{				for ( $i = 0; $i < 2; ++ $i )
					$this->kickers[$kn++] = $this->low_pair;
			}
			for ( $i = 4; $i >= 0; -- $i )
				if ( $vals[$i] != $this->high_pair && $vals[$i] != $this->low_pair )
					$this->kickers[$kn++] = $vals[$i];
		}
		else
		{			for ( $i = 0; $i < 5; ++ $i )
				$this->kickers[$i] = $vals[5 - $i];
		}
		//now lets create kickers by best combo
        if ( $this->flash && $this->straight && $vals[0] == 8 ) // 10-...-A zomg imba
        {        	$this->pos = 0;        	$this->combo_name = 'Флеш-рояль';
        }
        else
        if ( $this->flash && $this->straight )
        {        	$this->pos = 1;
        	$this->combo_name = 'Стрит-флеш';
        }
        else
        if ( $this->kare >= 0 )
        {
        	$this->pos = 2;
        	$this->combo_name = 'Каре';
        }
        else
        if ( $this->set >= 0 && $this->high_pair >= 0 )
        {
        	$this->pos = 3;
        	$this->combo_name = 'Фулл-хаус';
        }
        else
        if ( $this->flash )
        {
        	$this->pos = 4;
        	$this->combo_name = 'Флеш';
        }
        else
        if ( $this->straight )
        {
        	$this->pos = 5;
        	$this->combo_name = 'Стрит';
        }
        else
        if ( $this->set >= 0 )
        {
        	$this->pos = 6;
        	$this->combo_name = 'Тройка';
        }
        else
        if ( $this->high_pair >= 0 && $this->low_pair >= 0 )
        {
        	$this->pos = 7;
        	$this->combo_name = 'Две пары';
        }
        else
        if ( $this->high_pair >= 0 )
        {
        	$this->pos = 8;
        	$this->combo_name = 'Пара';
        }
        else
        {
        	$this->pos = 9;
        	$this->combo_name = 'Старшая карта';
        }
  	}}

class PokerTable
{
	var $table_id;
    var $seats;
    var $locked;
    var $waste_locked, $lock_waste_stats;
    var $money;
    var $draw_id;
    var $card_set, $phase, $current_seat, $current_ping;
    var $cards1, $cards2, $bets, $states;

    /*function Lock( $lock_waste_players = false, $lock_waste_stats = false )
    {
    	return;
    	++ $this->locked;
    	if ( $this->locked > 1 && !( $lock_waste_players && !$this->waste_locked ) &&
    		!( ( $lock_waste_stats || $lock_waste_players ) && !$this->lock_waste_stats ) )
    		return;
    	if ( $lock_waste_players )
    	{
    		$this->waste_locked = true;
    		$this->lock_waste_stats = true;
    		$add = "waste_bets WRITE, poker_join_history WRITE, waste_stats WRITE, ";
    	}
    	else
    	if ( $lock_waste_stats )
    	{    		$this->waste_locked = false;
    		$this->lock_waste_stats = true;
    		$add = 'waste_stats WRITE, ';
    	}
		f_MQuery( "LOCK TABLES poker_table_players WRITE, $add
			poker_table_stats WRITE, poker_draw_player WRITE, poker_table_draw WRITE,
			poker_player_pings WRITE;" );
    }*/


    function Lock( $lock_waste_players = false, $lock_waste_stats = false )
    {
    	++ $this->locked;
    	if ( $this->locked > 1 )
    		//return;
   		$add = "waste_bets WRITE, poker_join_history WRITE, waste_stats WRITE, ";
   	
		f_MQuery( "LOCK TABLES poker_table_players WRITE, $add
			poker_table_stats WRITE, poker_draw_player WRITE, poker_table_draw WRITE,
			poker_player_pings WRITE;" );
    }

    function Unlock( )
    {
    	-- $this->locked;
    	if ( $this->locked > 0 )
    		return;
    	$this->waste_locked = false;
    	$this->lock_waste_stats = false;
		f_MQuery( "UNLOCK TABLES;" );
    }

    function Load( )
    {
    	$this->seats = array( );
    	$this->money = array( );
    	$res = f_MQuery( "SELECT player_id, seat, money FROM poker_table_players WHERE table_id = '{$this->table_id}';" );
		while ( $line = f_MFetch( $res ) )
		{
			$this->seats[$line[1]] = $line[0];
			$this->money[$line[1]] = $line[2];
		}

	    $res = f_MQuery( "SELECT current_draw_id, last_seat, start_money, small_blind FROM poker_table_stats WHERE table_id = {$this->table_id};" );
		if ( !( $arr = f_MFetch( $res ) ) )
			RaiseError( "No current_draw_id in table poker_table_stats" );
 		$this->draw_id = (int)$arr[0];
 		$this->last_seat = (int)$arr[1];
 		$this->start_money = (int)$arr[2];
 		$this->small_blind = (int)$arr[3];
    }

    function ClearDraw( )
    {		$this->card_set = 0;
		$this->phase = -1;
		$this->current_seat = 0;
		$this->current_ping = 0;
		$this->last_bet = 0;

 		$this->cards1 = array( );
 		$this->cards2 = array( );
 		$this->bets = array( );
 		$this->states = array( );
 		$this->wins = array( );
 		$this->best_combo = array( );
    }

    function LoadDraw( )
    {
    	$this->ClearDraw( );		$res = f_MQuery( "SELECT card_set, phase, current_seat, current_ping, last_bet FROM poker_table_draw WHERE
			draw_id = '{$this->draw_id}';" );
		if ( !( $arr = f_MFetch( $res ) ) )
			RaiseError( "No draw in table poker_table_draw" );
		$this->card_set = $arr[0];
		$this->phase = $arr[1];
		$this->current_seat = $arr[2];
		$this->current_ping = $arr[3];
		$this->last_bet = $arr[4];

 		$res = f_MQuery( "SELECT seat, card1, card2, bet, state, wins, best_combo FROM poker_draw_player WHERE
 			draw_id = '{$this->draw_id}';" );
 		while ( $arr = f_MFetch( $res ) )
 		{
 			$cur_seat = $arr[0];
 			$this->cards1[$cur_seat] = $arr[1];
 			$this->cards2[$cur_seat] = $arr[2];
 			$this->bets[$cur_seat] = $arr[3];
 			$this->states[$cur_seat] = $arr[4];
 			$this->wins[$cur_seat] = $arr[5];
 			if ( $arr[6] >= 0 )
 			{				$this->best_combo[$cur_seat] = array( );
				$cs = $arr[6];
				for ( $i = 0; $i < 5; ++ $i )
				{
					$this->best_combo[$cur_seat][$i] = $cs % PER_DECK;
					$cs /= PER_DECK;
				}
 			}
 		}
    }

	function PokerTable( $table_id )
	{

		$this->waste_locked = false;
   		$this->lock_waste_stats = false;
		$this->locked = 0;
		$this->table_id = $table_id;
		$this->seats = array( );
		settype( $table_id, 'integer' );
		$this->Process( );
	}

	function AddPlayer( $player_id )
	{
		//Fix to unlocked characters
		$cur_locked = $this->locked;
		$cur_waste_locked = $this->waste_locked;
    	$cur_lock_waste_stats = $this->lock_waste_stats;
		$this->Lock( true );
		$this->Load( );
		if ( $this->locked )
		{
			$this->locked = 1;
			$this->Unlock( );
		}
       	$cur_player = new Player( $player_id );
       	$rec_money = $this->start_money;
       	if ( $cur_player->SpendMoney( $rec_money ) )
       	{        	$cur_player->AddToLogPost( 0, -$rec_money, 37 );
       	}
       	else
       	{       		return false;       	}

		$this->Lock( true );
		$this->Load( );

		$result = false;

		$res = f_MQuery( "SELECT 1 FROM poker_table_players WHERE player_id = '$player_id'" );
        if ( f_MNum( $res ) )
        {        	$result = false;
        }
        else
        {        	$res = f_MQuery( "SELECT 1 FROM waste_bets WHERE player1_id = '$player_id'" );
	        if ( f_MNum( $res ) )
	        {
	        	$result = false;
			}
	        else
	        {
		 	    for ( $i = 0; $i < SEAT_COUNT; ++ $i )
		        {
		            if ( !isset( $this->seats[$i] ) )
		            {
		            	//Need to take money
	                    $this->seats[$i] = $player_id;
	                    f_MQuery( "INSERT INTO waste_bets ( game_id, player1_id ) VALUES( '4', '$player_id' ); " );
	                    f_MQuery( "INSERT INTO poker_table_players ( table_id, player_id, seat, money ) VALUES ( '{$this->table_id}', '$player_id', '$i', '{$this->start_money}' );" );
	                    $tm = time( );
	                    f_MQuery( "INSERT INTO poker_join_history ( table_id, player_id, seat, start_draw_id, start_time ) VALUES
	                     ( '{$this->table_id}', '$player_id', '$i', '{$this->draw_id}', '$tm');" );
	                    $result = true;
	                    break;
		            }
		        }
			}
		}

		if ( !$cur_locked )
		{			$this->Unlock( );
		}
		else
		{			$this->locked = $cur_locked;		}

		if ( !$result )
		{
			if ( $this->locked )
			{				$this->locked = 1;
				$this->Unlock( );			}
			$cur_player->AddMoney( $rec_money );
			$cur_player->AddToLogPost( 0, $rec_money, 37 );
			if ( $cur_locked )
			{				$this->Lock( $cur_waste_locked, $cur_lock_waste_stats );
				$this->locked = $cur_locked;			}
		}
		return $result;
	}

	function RemovePlayer( $player_id )
	{
		$cur_locked = $this->locked;
		$cur_waste_locked = $this->waste_locked;
    	$cur_lock_waste_stats = $this->lock_waste_stats;
		$this->Lock( true );
		$this->Load( );

		$result = false;
		$ret_money = 0;

		$res = f_MQuery( "SELECT 1 FROM poker_table_players WHERE player_id = '$player_id'" );
        if ( !f_MNum( $res ) )
        {
        	$result = false;
        }
        else
        {
        	$res = f_MQuery( "SELECT 1 FROM waste_bets WHERE player1_id = '$player_id'" );
	        if ( !f_MNum( $res ) )
	        {
	        	$result = false;
			}
	        else
			{
				for ( $i = 0; $i < SEAT_COUNT; ++ $i )
				{
					if ( isset( $this->seats[$i] ) && $this->seats[$i] == $player_id )
					{
						//Need to return money
						$ret_money = $this->money[$i];
						f_MQuery( "DELETE FROM poker_table_players WHERE table_id = {$this->table_id} AND seat = '$i';" );
						f_MQuery( "DELETE FROM waste_bets WHERE game_id = 4 AND player1_id ='$player_id';" );
	                    $res = f_MQuery( "SELECT max( join_id ) FROM poker_join_history WHERE player_id = '$player_id'" );
	                    if ( !( $arr = f_MFetch( $res ) ) )
                			RaiseError( "No join_id in table poker_join_history" );
	                    $join_id = $arr[0];
	                    $tm = time( );
	                    f_MQuery( "UPDATE poker_join_history set end_draw_id = '{$this->draw_id}', end_time = '$tm' WHERE
	                    	join_id = '$join_id';" );
	                  	unset( $this->seats[$i] );
	                  	isset( $this->money[$i] );
						$result = true;
						break;
					}
				}
			}
		}

		$this->Unlock( );

		if ( $ret_money > 0 )
		{			if ( $cur_locked )
			{				$this->locked = 1;
				$this->Unlock( );			}
			$cur_player = new Player( $player_id );
			$cur_player->AddMoney( $ret_money );
			$cur_player->AddToLogPost( 0, $ret_money, 37 );
			if ( $cur_locked )
			{
				$this->Lock( $cur_waste_locked, $cur_lock_waste_stats );
				$this->locked = $cur_locked;
			}
		}

		return $result;
	}

	function KickNoobs( )
	{		//кикает игроков со стола в очередную раздачу
		//изза пинга, либо за банкротство
		$list = '(' . implode( ',', $this->seats ) . ')';
		$res = f_MQuery( "SELECT player_id, last_ping FROM poker_player_pings WHERE player_id in $list;" );
		$player_pings = array( );
		while ( $arr = f_MFetch( $res ) )
		{			$player_pings[$arr[0]] = $arr[1];		}
		$last_round_start_time = 0;
		if ( $this->draw_id >= 0 )
		{			$last_round_start_time = f_MValue( "SELECT start_time FROM poker_table_draw WHERE draw_id = {$this->draw_id}" );
		}
		$to_kick = array( );
		foreach ( $this->seats as $pos => $player_id )
		{			if ( $this->money[$pos] < $this->small_blind ||
				!isset( $player_pings[$player_id] ) ||
				$player_pings[$player_id] < $last_round_start_time )
			{				$to_kick[] = $player_id;			}		}
		foreach ( $to_kick as $id => $val )
		{			$this->RemovePlayer( $val );
		}
	}

	function ModifyMoneyBy( $pos, $add )
	{		if ( isset( $this->seats[$pos] ) && $add != 0 )
		{
			$this->money[$pos] += $add;
			f_MQuery( "UPDATE poker_table_players SET money = {$this->money[$pos]} WHERE table_id = '{$this->table_id}' AND seat = '$pos';" );		}
	}

	function CreateNewDraw( $last_seat )
	{
		$this->KickNoobs( );		$cur_seat = $last_seat;
		$this->ClearDraw( );
		for ( $i = 0; $i < SEAT_COUNT; ++ $i )
		{			++ $cur_seat;			if ( $cur_seat >= SEAT_COUNT || $cur_seat < 0 )
				$cur_seat = 0;
			if ( isset( $this->seats[$cur_seat] ) )
				break;
		}
		if ( $i == SEAT_COUNT )
		{
	 		$this->last_seat = $last_seat;
	 		$this->draw_id = -1;
	  		f_MQuery( "UPDATE poker_table_stats SET current_draw_id = '{$this->draw_id}', last_seat = '{$this->last_seat}' WHERE
	  			table_id = '{$this->table_id}';" );
			return;
		}
		//we've found a dealer
  		$small_blind = $this->small_blind;
  		$big_blind = $small_blind * 2;
  		//check player count (that have money to bet blinds)
  		$player_count = 0;
 		for ( $j = 0; $j < SEAT_COUNT; ++ $j )
 		{ 			$pos = ( $j + $cur_seat ) % SEAT_COUNT;
			if ( $id == 1 )
				$bet = $small_blind;
			else
			if ( $id == 2 )
				$bet = $big_blind;
			else
				$bet = 0;

 			if ( isset( $this->seats[$pos] ) )
 			{ 				if ( $this->money[$pos] >= $bet )
 				{
 					++ $player_count;
 					++ $id;
 				}
 			}
 		}
 		$draw_id = -1;
 		if ( $player_count == 2 )
 		{ 			$player_count = 0;
	  		for ( $j = 0; $j < SEAT_COUNT; ++ $j )
	 		{
	 			$pos = ( $j + $cur_seat ) % SEAT_COUNT;
				if ( $id == 0 )
					$bet = $small_blind;
				else
				if ( $id == 1 )
					$bet = $big_blind;

	 			if ( isset( $this->seats[$pos] ) )
	 			{
	 				if ( $this->money[$pos] >= $bet )
	 				{
	 					++ $player_count;
	 					++ $id;
	 				}
	 			}
	 		}	 	}

		if ( $player_count >= 2 ) //check count
		{	  		$draw_id = f_MValue( "SELECT IFNULL( max( draw_id ) + 1, 1 ) FROM poker_table_draw;" );
	  		//lets create deck
	  		$deck = array( );
	  		//permutation
	  		for ( $i = 0; $i < PER_DECK; ++ $i )
	  			$deck[$i] = $i;
	  		//reshuffle (standart)
	  		for ( $i = 0; $i < PER_DECK - 1; ++ $i )
	  		{
	  			$id = mt_rand( $i, PER_DECK - 1 );
	  			$tmp = $deck[$i];
	  			$deck[$i] = $deck[$id];
	  			$deck[$id] = $tmp;
	  		}

	  		$id = 0;
	  		$start_seat_id = 3 % $player_count;
	 		if ( $player_count == 2 )
	 			$start_seat_id = 0;
	 		$this->last_seat = $cur_seat;
	 		$this->draw_id = $draw_id;
	  		f_MQuery( "UPDATE poker_table_stats SET current_draw_id = '{$this->draw_id}', last_seat = '{$this->last_seat}' WHERE
	  			table_id = '{$this->table_id}';" );
	  		//draw cards to players
	  		$start_seat = $cur_seat;
	 		for ( $j = 0; $j < SEAT_COUNT; ++ $j )
	 		{
	 			$pos = ( $j + $cur_seat ) % SEAT_COUNT;				if ( $player_count == 2 )
				{					if ( $id == 0 )
						$bet = $small_blind;
					else
					if ( $id == 1 )
						$bet = $big_blind;				}
				else
				{
					if ( $id == 1 )
						$bet = $small_blind;
					else
					if ( $id == 2 )
						$bet = $big_blind;
					else
						$bet = 0;
				}

	 			if ( isset( $this->seats[$pos] ) && $this->money[$pos] >= $bet )
	 			{
	 				if ( $id == $start_seat_id )
	 				{	 					$start_seat = $pos;	 				}	 				$card1 = $deck[$id];
	 				$card2 = $deck[$id + $player_count];

					$this->ModifyMoneyBy( $pos, -$bet );

					$cur_state = 0;
					if ( $this->money[$pos] == 0 )
						$cur_state = 3;			 		f_MQuery( "INSERT INTO poker_draw_player ( draw_id, seat, card1, card2, bet, state, wins ) VALUES
		 				( '$draw_id', '$pos', '$card1', '$card2', '$bet', $cur_state, 0 );" );
		 	 		++ $id;
	 			}	 		}
	 		$id *= 2; // skip second cards
	 		//draw middle
	 		$t = 1;
	 		$mid = 0;
	 		for ( $i = 0; $i < 5; ++ $i )
	 		{	 			$mid += $t * $deck[$id++];
	 			$t *= PER_DECK;	 		}
	 		//init table_draw
	 		$tm = time( );
	 		f_MQuery( "INSERT INTO poker_table_draw ( draw_id, table_id, start_time, card_set, phase, current_seat, current_ping ) VALUES
	 			( '$draw_id', '{$this->table_id}', '$tm', '$mid', '0', '$start_seat', '$tm' );" );
	 	}
	 	else
	 	{	 		//not enough players
	 		$this->last_seat = $last_seat;
	 		$this->draw_id = -1;
	  		f_MQuery( "UPDATE poker_table_stats SET current_draw_id = '{$this->draw_id}', last_seat = '{$this->last_seat}' WHERE
	  			table_id = '{$this->table_id}';" );
	 	}
 	}

	function PreWin( )
	{
		$winn_id = -1;		foreach ( $this->states as $pos => $val )
		{
			if ( $val != 2 ) // not fold (pass)
			{				//its a winner
				$winn_id = $pos;
				break;			}
		}
		if ( $winn_id < 0 )
			RaiseError( "No winner in pre_win, draw_id = {$this->draw_id}" );
		$sum = 0;
		foreach ( $this->bets as $pos => $val )
		{			$sum += $val;		}
		$pos = $winn_id;
		$this->wins = array( );
		$this->wins[$pos] = $sum;
		f_MQuery( "UPDATE poker_draw_player SET wins = $sum WHERE draw_id = '{$this->draw_id}' AND seat = '$pos';" );
		$this->ModifyMoneyBy( $pos, $sum );

		$this->phase = 4;
		$this->current_ping = time( ) + WINNER_SECTIME;
 		f_MQuery( "UPDATE poker_table_draw set current_ping = {$this->current_ping},
 			phase = {$this->phase} WHERE draw_id = '{$this->draw_id}';" );
 		$this->UpdateStats( );
	}

	function UpdateStats( )
	{ 		$this->Lock( false, true );
		foreach( $this->bets as $pos => $val )
		{
			if ( !isset( $this->seats[$pos] ) )
				continue;
			$win = 0;
			if ( isset( $this->wins[$pos] ) )
				$win = $this->wins[$pos];
			if ( $win > $val )
			{
				f_MQuery( "INSERT waste_stats set wins = 1, player_id = {$this->seats[$pos]}, game_id = 4 ON DUPLICATE KEY UPDATE
					wins = wins + 1;" );
			}
			else
			if ( $win == $val )
			{
				f_MQuery( "INSERT waste_stats set draws = 1, player_id = {$this->seats[$pos]}, game_id = 4 ON DUPLICATE KEY UPDATE
					draws = draws + 1;" );
			}
			else
			{
				f_MQuery( "INSERT waste_stats set loses = 1, player_id = {$this->seats[$pos]}, game_id = 4 ON DUPLICATE KEY UPDATE
					loses = loses + 1;" );
			}
		}
		$this->Unlock( );
	}

	function IsBetter( $c1, $c2 )
	{
		$comb1 = new Combination( $c1 );
		$comb2 = new Combination( $c2 );
		if ( $comb1->pos != $comb2->pos )
			return $comb1->pos < $comb2->pos;
		for ( $i = 0; $i < 5; ++ $i )
		{			if ( $comb1->kickers[$i] != $comb2->kickers[$i] )
				return $comb1->kickers[$i] > $comb2->kickers[$i];		}
		return false;	}

	function EndGame( )
	{
		//для каждого играющего определяем лучшую комбинацию
		$this->best_combo = array( );		$cards = array( );
		$cs = $this->card_set;
		for ( $i = 0; $i < 5; ++ $i )
		{
			$cards[$i] = $cs % PER_DECK;
			$cs /= PER_DECK;
		}
		$positions = array( );
		$ids = array( );
		$n = 0;
		foreach ( $this->states as $pos => $val )
		{			if ( $val != 2 ) // not fold (pass)
			{
				$positions[$n] = $n;
				$ids[$n] = $pos;
				++ $n;				$cards[5] = $this->cards1[$pos];				$cards[6] = $this->cards2[$pos];
				unset( $this->best_combo[$pos] );
				for ( $i = 0; $i < 7; ++ $i ) // перебираем карты которые не возьмем
				{
					for ( $j = 0; $j < $i; ++ $j )
					{
						$cur = array( );
						$cn = 0;						for ( $k = 0; $k < 7; ++ $k )
						{							if ( $k != $i && $k != $j )
							{								$cur[$cn++] = $cards[$k];							}						}
						//собрали массив карт, определяем лучшую
						if ( !isset( $this->best_combo[$pos] )
							|| $this->IsBetter( $cur, $this->best_combo[$pos] ) )
						{							$this->best_combo[$pos] = $cur;						}					}
				}
			}
		}
		//нашли лучшие комбинации игроков
		//теперь надо посортить их по мощи
		for ( $i = 0; $i < $n; ++ $i )
		{
			for ( $j = $i + 1; $j < $n; ++ $j )
			{				if ( $this->IsBetter( $this->best_combo[$ids[$positions[$j]]], $this->best_combo[$ids[$positions[$i]]] ) )
				{					$t = $positions[$i];
					$positions[$i] = $positions[$j];
					$positions[$j] = $t;				}			}
		}
		//раздаем деньги
		$bet_wins = array( );
		for ( $i = 0; $i < $n; ++ $i )
			$bet_wins[$ids[$i]] = 0;
		$remain = $this->bets;
		$given = 0; // это сколько еще не раздали
		for ( $i = 0; $i < $n; ++ $i )
		{			$j = $i;
			while ( $j + 1 < $n && !$this->IsBetter( $this->best_combo[$ids[$positions[$j]]], $this->best_combo[$ids[$positions[$j + 1]]] ) )
			{				++ $j;			}
			//теперь надо поделить выйгрышь среди игроков с $i по $j включительно( т.к. у них одинаковые комбинации )
			//будем выбирать среди них самую мелкую сумму и раздавать
			while ( true )
			{
				$mval = -1;
				$cnt = 0;				for ( $k = $i; $k <= $j; ++ $k )
				{
					$pos = $ids[$positions[$k]];
					$diff = $this->bets[$pos] - $given;					if ( $diff > 0 )
					{						if ( $cnt == 0 || $diff < $mval )
						{							$mval = $diff;
							++ $cnt;
						}
						else
						if ( $diff >= $mval )
						{							++ $cnt;						}					}				}
				if ( $cnt == 0 )
					break;
				// нашли текущий минимум в $mval
				$sum = 0;
				foreach ( $remain as $pos => $val )
				{					if ( $val > 0 )
					{
						if ( $val > $mval )
						{							$remain[$pos] -= $mval;
							$sum += $mval;						}
						else
						{							$sum += $val;
							$remain[$pos] = 0;
						}					}
				}
				// теперь в $sum содержится сумму которую будем делить на $cnt человек
				$per_person = floor( $sum / $cnt );
				for ( $k = $i; $k <= $j; ++ $k )
				{
					$pos = $ids[$positions[$k]];
					$diff = $this->bets[$pos] - $given;
					if ( $diff > 0 )
					{
						$cur = $per_person;
						if ( $cnt == 1 )
							$cur = $sum;
						$sum -= $cur;
						-- $cnt;						$bet_wins[$pos] += $cur;
					}
				}
				$given += $mval;			}
			$i = $j;
		}
		//бабос раздали, теперь все кто получил бабос, должны засветить карты
		$this->wins = $bet_wins;
		foreach ( $this->best_combo as $pos => $val )
		{
	 		$t = 1;
	 		$mid = 0;
	 		$id = 0;
	 		for ( $i = 0; $i < 5; ++ $i )
	 		{
	 			$mid += $t * $val[$id++];
	 			$t *= PER_DECK;
	 		}
			f_MQuery( "UPDATE poker_draw_player SET best_combo = {$mid} WHERE draw_id = '{$this->draw_id}' AND seat = '$pos';" );
		}
		$winners_count = 0;
		foreach ( $bet_wins as $pos => $val )
		{			if ( $val > 0 )
			{				f_MQuery( "UPDATE poker_draw_player SET wins = {$bet_wins[$pos]} WHERE draw_id = '{$this->draw_id}' AND seat = '$pos';" );
				$this->ModifyMoneyBy( $pos, $val );
				++ $winners_count;
			}
		}
		$this->phase = 4;
		$this->current_ping = time( ) + $winners_count * WINNER_SECTIME;
 		f_MQuery( "UPDATE poker_table_draw set current_ping = {$this->current_ping},
 			phase = {$this->phase} WHERE draw_id = '{$this->draw_id}';" );
 		$this->UpdateStats( );
	}

 	function MakeNextMove( )
 	{
 		//функция передает ход следующему игроку
 		//проверить состояние стола, чтобы начать следующий раунд или
 		//закончить партию
 		//а также обновить пинг у текущего draw 		$this->Lock( );
 		$this->Load( );
 		$this->LoadDraw( );

		if ( $this->phase < 4 )
		{
			$moved_count = 0;
			$all_in_count = 0;
			foreach ( $this->states as $key => $val )
			{
				if ( $val <= 1 )
					++ $moved_count;
				else
				if ( $val == 3 ) // all-in
					++ $all_in_count;
			}
			if ( $moved_count == 1 && $all_in_count == 0 ||
				$moved_count == 0 && $all_in_count == 1 ) // just win gg
			{
				$this->PreWin( );
				$this->Unlock( );
				return;
 			}
		}

		$max_bet = max( $this->bets );
 		// еще надо проверить фазу игры (может уже вин и надо новую начинать)
 		if ( $this->phase < 4 && ( $this->states[$this->current_seat] > 1 ||
 			$this->states[$this->current_seat] == 1 && $this->bets[$this->current_seat] == $max_bet ) ) //oke next move
 		{
			$found = false;
			//найдем следующего чувака
			$pos = 0;
			for ( $i = 1; $i < SEAT_COUNT; ++ $i )
			{
				$pos = ( $this->current_seat + $i ) % SEAT_COUNT;				if ( isset( $this->states[$pos] ) && (
					$this->states[$pos] == 0 ||
					$this->states[$pos] == 1 && $this->bets[$pos] < $max_bet ) )
				{					$found = true;
					break;				}
			}
			if ( $found )
			{				$this->current_seat = $pos;
				$this->current_ping = time( );
		 		f_MQuery( "UPDATE poker_table_draw set current_seat = {$this->current_seat}, current_ping = {$this->current_ping} WHERE
		 			draw_id = '{$this->draw_id}';" );
			}
			else
			{				//тут есть 3 варианта
				// 1) просто следующий раунд (2 и больше активных игроков)
				// 2) игра закончилась, так как (макс 1 акт игрок)
				// 3) выйграл блеффовальшик (все кроме 1 пассанули)
				// вначале посчитаем количество активных игроков
				$moved_count = 0;
				$all_in_count = 0;
				foreach ( $this->states as $key => $val )
				{					if ( $val == 1 )
						++ $moved_count;
					else
					if ( $val == 3 ) // all-in
						++ $all_in_count;				}
				if ( $moved_count >= 2 && $this->phase < 3 ) // so next round
				{					//нужно установить всем кто moved - not moved
					//увеличить фазу
					for ( $i = 1; $i < SEAT_COUNT; ++ $i )
					{
						$pos = ( $this->last_seat + $i ) % SEAT_COUNT;
						if ( isset( $this->states[$pos] ) &&
							$this->states[$pos] == 1 )
						{
							break;
						}
					}
					$this->current_seat = $pos;
					$this->current_ping = time( );
					++ $this->phase;

			 		f_MQuery( "UPDATE poker_table_draw set current_seat = {$this->current_seat}, current_ping = {$this->current_ping},
			 			phase = {$this->phase}, last_bet = {$max_bet} WHERE
			 			draw_id = '{$this->draw_id}';" );
					foreach ( $this->states as $key => $val )
					{
						if ( $val == 1 )
						{							$this->states[$key] = 0;
					 		f_MQuery( "UPDATE poker_draw_player SET state = 0 WHERE
									draw_id = '{$this->draw_id}' AND seat = '$key';" );						}
					}
				}
				else
				if ( $moved_count == 1 && $all_in_count == 0 ||
					$moved_count == 0 && $all_in_count == 1 ) // just win gg
				{					$this->PreWin( );				}
				else //its end game
				{					$this->EndGame( );				}
			} 		}
 		else
 		{ 			//не понятно что делать 		}
 		$this->Unlock( ); 	}

 	function PlayerPass( $pos )
 	{ 		//1) set state to PASS

		$this->states[$pos] = 2;
 		f_MQuery( "UPDATE poker_draw_player SET state = 2 WHERE
				draw_id = '{$this->draw_id}' AND seat = '$pos';" );

		//2) next player and check ( for game end, next round, etc. )

		$this->MakeNextMove( );
 	}

	function Process( )
	{
		$this->Lock( );
		$tm = time( );
		global $player;
		f_MQuery( "INSERT INTO poker_player_pings SET player_id = '{$player->player_id}', last_ping = '$tm' ON DUPLICATE KEY UPDATE
			last_ping = '$tm'" );
			
		$moved = true;
		$countA = 0;
		while ( $moved  )
		{
			$moved = false;
			$this->Load( );
			//processing
		    //recieving a lot of data
			$draw_id = $this->draw_id;
			$last_seat = $this->last_seat;
			$players_on_table = count( $this->seats );
			if ( $draw_id < 0 )
			{				if ( $players_on_table < 2 )
				{					$this->Unlock( );
					return;
				}
				$this->CreateNewDraw( $last_seat );
				$moved = true;
				echo ( ++ $countA."<br>" );
			}
			else
			{
				die ( 'ololo4' );
				$this->LoadDraw( );
				if ( $this->phase < 4 )
				{					if ( $tm - $this->current_ping > MAX_TIME_TO_MOVE )
					{						$this->PlayerPass( $this->current_seat );
						$moved = true;					}
				}
				else
				{					if ( $tm - $this->current_ping > SHOWDOWN_TIME )
					{
						$this->CreateNewDraw( $last_seat );
						$moved = true;
					}
				}
			}
		}
		die( 'ololo5' );
		$this->Unlock( );
	}

	function CardImg( $id, $card_id, $half_op = false )
	{
		$img = '';
		if ( $id < 0 || $id > PER_DECK )
		{			$img = 'closed.png';		}
		else
		{
			$val = $id >> 2;			$type = $id & 3;
			if ( $type == 0 )
				$img = 'p';
			else
			if ( $type == 1 )
				$img = 'b';
			else
			if ( $type == 2 )
				$img = 'k';
			else
			if ( $type == 3 )
				$img = 'c';

			$img .= $val . ".png";		}
		$add = "";
		if ( $half_op )
		{			$add = " style=\\'-ms-filter:\"progid:DXImageTransform.Microsoft.Alpha(Opacity=50)\"; filter: alpha(opacity=50);opacity: .5;\\'";		}
		return "<img id=CardImg{$card_id} src=images/poker/cards/{$img}{$add}>";	}

	function GetMinRaise( )
	{		return max( ( max( $this->bets ) - $this->last_bet ) * 2, $this->small_blind * 2 );	}

	function GetMaxRaise( $your_pos = -1 )
	{
		$max_delta = 0;
		foreach ( $this->money as $pos => $val )
		{			if ( $pos != $your_pos && $this->states[$pos] != 2 )
			{				$max_delta = max( $max_delta, $val + $this->bets[$pos] );			}		}		return $max_delta - $this->bets[$your_pos];
	}

	function ActionStart( )
	{		$this->Lock( );
		$this->Load( );
		if ( $this->draw_id <= 0 )
			return false;
		$this->LoadDraw( );
		$pos = $this->current_seat;
		global $player;
		if ( !isset( $this->seats[$pos] ) || $this->seats[$pos] != $player->player_id )
			return false;
		if ( $this->phase == 4 )
			return false;
		if ( !isset( $this->states[$pos] ) || $this->states[$pos] >= 2 )
			return false;
		return true;
	}

	function ActionFinish( )
	{		$this->Unlock( );
	}

	function ActionFold( )
	{        if ( $this->ActionStart( ) )
        {	        $this->PlayerPass( $this->current_seat );
        }
        $this->ActionFinish( );
	}

	function ActionCall( )
	{        if ( $this->ActionStart( ) )
        {			$max_bet = max( $this->bets );
			$pos = $this->current_seat;
			$call = $max_bet - $this->bets[$pos];
			if ( $this->money[$pos] > $call )
			{				$this->ModifyMoneyBy( $pos, -$call );
				$this->bets[$pos] += $call;
				$this->states[$pos] = 1;		 		f_MQuery( "UPDATE poker_draw_player SET state = {$this->states[$pos]}, bet = {$this->bets[$pos]} WHERE
					draw_id = '{$this->draw_id}' AND seat = '$pos';" );
				$this->MakeNextMove( );
			}
        }
        $this->ActionFinish( );
	}

	function ActionRaise( $raise )
	{        if ( $this->ActionStart( ) )
        {			$pos = $this->current_seat;
        	$min_raise = $this->GetMinRaise( );
        	$max_raise = $this->GetMaxRaise( $raise );
        	$min_raise = min( $max_raise, $min_raise );
			if ( $raise > 0 && $raise >= $min_raise && $raise <= $max_raise && $this->money[$pos] > $raise )
			{
				$this->ModifyMoneyBy( $pos, -$raise );
				$this->bets[$pos] += $raise;
				$this->states[$pos] = 1;
		 		f_MQuery( "UPDATE poker_draw_player SET state = {$this->states[$pos]}, bet = {$this->bets[$pos]} WHERE
					draw_id = '{$this->draw_id}' AND seat = '$pos';" );
				$this->MakeNextMove( );
			}
        }
        $this->ActionFinish( );
	}

	function ActionAllIn( )
	{
        if ( $this->ActionStart( ) )
        {
			$pos = $this->current_seat;
			$all_in = $this->money[$pos];
        	$max_raise = $this->GetMaxRaise( $raise );
			if ( $all_in >= 0 && $all_in <= $max_raise )
			{
				$this->ModifyMoneyBy( $pos, -$all_in );
				$this->bets[$pos] += $all_in;
				$this->states[$pos] = 3;
		 		f_MQuery( "UPDATE poker_draw_player SET state = {$this->states[$pos]}, bet = {$this->bets[$pos]} WHERE
					draw_id = '{$this->draw_id}' AND seat = '$pos';" );
				$this->MakeNextMove( );
			}
        }
        $this->ActionFinish( );
	}

	function ActionLeave( )
	{
		$this->Lock( );
		$this->Load( );
		global $player;
		if ( $this->draw_id <= 0 )
		{			$this->RemovePlayer( $player->player_id );
			$this->Unlock( );
			return;		}
		$this->LoadDraw( );
		if ( $this->phase < 4 )
		{			foreach ( $this->states as $pos => $val )
			{				if ( isset( $this->seats[$pos] ) && $this->seats[$pos] == $player->player_id )
				{					if ( $val != 2 )
					{						$this->PlayerPass( $pos );
						break;					}				}			}		}
		$this->RemovePlayer( $player->player_id );
		$this->Unlock( );
	}

	function IsPreWin( )
	{
		if ( $this->phase < 4 )
			return false;
		$cnt = 0;
		foreach ( $this->states as $pos => $val )
		{			if ( $val != 2 )
				++ $cnt;		}
		return $cnt == 1;
	}

	function Draw( )
	{
		$this->Lock( );
		$this->Load( );
		$s = '';
		$babos = '<img src=images/money.gif>';
		global $player;
		if ( !isset( $player ) )
			RaiseError( 'Нет глобального игрока - и это странно' );
		if ( $this->draw_id >= 0 )
		{			$this->LoadDraw( );
		}
		$this->Unlock( );
		$card_id = 0;
		$hands = array( );
		$win_timer = 1000;
		$s .= "CurrentDrawId( $this->draw_id );";
		if ( $this->draw_id >= 0 )
		{
			$cards = array( );
			$cs = $this->card_set;
			for ( $i = 0; $i < 5; ++ $i )
			{
				$cards[$i] = $cs % PER_DECK;
				$cs /= PER_DECK;
			}
			$pre_win = $this->IsPreWin( );
			if ( $this->phase == 4 )
				$s .= "if ( CanShowWinners( {$this->draw_id} ) ) {";
			$bank = 0;
			$s .= "ShowCombo( '' );";
			foreach( $this->bets as $pos => $val )
			{				$bank += $val;
			}
			$s .= "ShowBank( $bank );";
			switch ( $this->phase )
			{				case 0: // pre-flop
				{					$s .= "_( 'TableMid' ).innerHTML = '';";

					break;				}
				case 1: // flop
				{
					$add = '';
					for ( $i = 0; $i < 3; ++ $i )
					{						if ( $i )
							$add .= ' ';
						$add .= $this->CardImg( $cards[$i], $card_id++ );
					}
					$s .= "_( 'TableMid' ).innerHTML = '$add';";
					break;
				}
				case 2: // turn
				{
					$add = '';
					for ( $i = 0; $i < 4; ++ $i )
					{
						if ( $i )
							$add .= ' ';
						$add .= $this->CardImg( $cards[$i], $card_id++ );
					}
					$s .= "_( 'TableMid' ).innerHTML = '$add';";
					break;
				}
				case 3: // river
				{
					$add = '';
					for ( $i = 0; $i < 5; ++ $i )
					{
						if ( $i )
							$add .= ' ';
						$add .= $this->CardImg( $cards[$i], $card_id++ );
					}
					$s .= "_( 'TableMid' ).innerHTML = '$add';";
					break;
				}
				case 4: // showdown
				{
					$add = '';
					if ( $pre_win )
					{					}
					else
					{
						for ( $i = 0; $i < 5; ++ $i )
						{
							if ( $i )
								$add .= ' ';
							$add .= $this->CardImg( $cards[$i], $card_id++ );
						}
						$s .= "_( 'TableMid' ).innerHTML = '$add';";
					}
					break;
				}
				default: //unknown phase
				{					$s .= "_( 'TableMid' ).innerHTML = '';";
					break;
				}
			}
			$card_count = $card_id + count( $this->states ) * 2;
			for ( $i = 0; $i < SEAT_COUNT; ++ $i )
			{				if ( isset( $this->states[$i] ) )
				{
					$op = ( $this->states[$i] == 2 );
					$add = '';
					$hands[$i] = $card_id;
					if ( $this->seats[$i] == $player->player_id ||
					( $this->phase == 4 && isset( $this->wins[$i] ) && $this->wins[$i] > 0 && !$pre_win ||
						!$pre_win && $this->phase == 4 && $this->states[$i] == 3 ) )
					{						$add .= $this->CardImg( $this->cards1[$i], $card_id ++, $op );
						$add .= ' ';
						$add .= $this->CardImg( $this->cards2[$i], $card_id ++, $op );
					}
					else
					{
						$add .= $this->CardImg( -1, $card_id ++, $op );						$add .= ' ';
						$add .= $this->CardImg( -1, $card_id ++, $op );
					}
					if ( $this->phase < 4 )
					{
						if ( isset( $this->bets[$i] ) )
						{							$add_bet = $this->bets[$i] - $this->last_bet;
							if ( $add_bet > 0 )
							{
								$add .= "<br/><div style=\"width:80px;text-align:center;\"><font size=+1>$add_bet</font>$babos</div>";							}						}					}
					$s .= "_('PlayerHand{$i}').innerHTML = '$add';";
					if ( $this->phase == 4 )
					{
//						if ( $pre_win )
						{

						}
//						else
						{
							if ( isset( $this->wins[$i] ) && $this->wins[$i] > 0 )
							{
								if ( isset( $this->best_combo[$i] ) && $this->best_combo[$i] >= 0 )
								{
									$combo = new Combination( $this->best_combo[$i] );
									$cn = 0;
									$c_ids = array( );
									for ( $id = 0; $id < 5; ++ $id )
									{
										for ( $j = 0; $j < 5; ++ $j )
										{
											if ( $cards[$id] == $combo->cards[$j] )
												break;
										}
										if ( $j < 5 )
										{
											$c_ids[$cn++] = $id;
										}
									}

									for ( $j = 0; $j < 5; ++ $j )
									{
										if ( $this->cards1[$i] == $combo->cards[$j] )
										{
											$c_ids[$cn++] = $hands[$i];
										}
										else
										if ( $this->cards2[$i] == $combo->cards[$j] )
										{
											$c_ids[$cn++] = $hands[$i] + 1;
										}
									}
									if ( $cn != 5 )
										$s .= "alert( 'Bug with winner combo' );";
									$ww = $win_timer - 20;
									$s .= "setTimeout( 'FadeAllCard( $card_count )', $ww );";
									for ( $j = 0; $j < 5; ++ $j )
									{
										$s .= "setTimeout( 'ShowCard( {$c_ids[$j]} )', $win_timer );";
									}
									$s .= "setTimeout( 'ShowCombo( \"{$combo->combo_name}\" )', $win_timer );";
								}
								else
								{
									$ww = $win_timer - 20;
									$s .= "setTimeout( 'FadeAllCard( $card_count )', $ww );";
									$h1 = $hands[$i] + 1;
									$s .= "setTimeout( 'ShowCard( {$hands[$i]} )', $win_timer );";
									$s .= "setTimeout( 'ShowCard( {$h1} )', $win_timer );";
								}
								$cur_win = $this->wins[$i];
								$div_x = array( 360, 550, 630, 550, 380, 270 );
								$div_y = array( 230, 230, 320, 420, 420, 320 );
								$div_offset_x = 260 - 30;
								$div_offset_y = 220 - 30;
								$dx = $div_x[$i] - $div_offset_x;
								$dy = $div_y[$i] - $div_offset_y;
								$fly_time = 1300;
								$s .= "setTimeout( 'StartBankFly( $cur_win, 220, 190, $dx, $dy, $fly_time )', $win_timer );";
								$bank -= $cur_win;
								if ( $bank == 0 )
									$bt = '""';
								else
									$bt = $bank;
								$s .= "setTimeout( 'ShowBank( $bt )', $win_timer );";
								$win_timer += WINNER_SHOWTIME;
							}
						}
					}
				}
				else
				{					$s .= "_('PlayerHand{$i}').innerHTML = '<br/><br/><br/>Место<br/>свободно';";
				}
			}
			if ( $this->phase == 4 )
			{				$s .= "setTimeout( 'ShowCombo( \"\" )', $win_timer );";
				$s .= "setTimeout( 'FadeAllCard( $card_count )', $win_timer );";
				$s .= " } ";
			}
		}
		$balance = 0;
		//draw player names and money
		$your_seat = -1;
		for ( $i = 0; $i < SEAT_COUNT; ++ $i )
		{
			if ( isset( $this->seats[$i] ) )
			{
				$add = '';
				if ( $this->seats[$i] == $player->player_id )
				{
					$your_seat = $i;
					$balance = $this->money[$i];				}
				if ( isset( $this->bets[$i] ) )
				{					$add .= "<br/>Bet={$this->bets[$i]}";				}
				$cur_player = new Player( $this->seats[$i] );
				$nick = $cur_player->Nick( );
				$s .= "_('PlayerTop{$i}').innerHTML = {$nick} + \"({$this->money[$i]}{$babos})\";";
				if ( !isset( $this->states[$i] ) )
					$s .= "_('PlayerHand{$i}').innerHTML = '';";
			}
			else
			{
				$s .= "_('PlayerTop{$i}').innerHTML = '';";
				$s .= "_('PlayerHand{$i}').innerHTML = '<br/><br/><br/>Место<br/>свободно';";
			}
		}
		$s .= "_('EBalance').innerHTML = \"$balance{$babos}\";";
		if ( $this->draw_id >= 0 )
		{
			if ( $this->phase < 4 )
			{				if ( $this->current_seat == $your_seat )
				{
					$max_bet = max( $this->bets );
					$call = $max_bet - $this->bets[$your_seat];
					$all_in = $this->money[$your_seat];
					$min_raise = $this->GetMinRaise( );
					$max_raise = $this->GetMaxRaise( $your_seat );
					if ( $min_raise > $max_raise )
						$min_raise = $max_raise;
					$s .= "YourMove( $call, $min_raise, $max_raise, $all_in );";				}
				else
				{					$s .= "NotYourMove( );";				}
				$sec_left = $this->current_ping + MAX_TIME_TO_MOVE - time( );
				$s .= "ShowMoveArrow( {$this->current_seat} );";
			}
			else
			{				$sec_left = $this->current_ping + SHOWDOWN_TIME - time( );
				$s .= "NotYourMove( );";
				$s .= "ShowMoveArrow( -1 );";
			}
			if ( $sec_left >= 0 )
				$s .= "TimerTicks( $sec_left );";
		}
		else
		{
			$s .= "ShowCombo( 'Ожидаем других игроков' );";
			$s .= "_( 'TableMid' ).innerHTML = '';";
			$s .= "NotYourMove( );";
			$s .= "ShowMoveArrow( -1 );";
		}
		echo $s;
	}
};

?>