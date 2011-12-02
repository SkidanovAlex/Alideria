<?

include_once( "player.php" );
include_once( "create_combat.php" );

class TeamsDistributor
{
	var $ids;
	function odd($l, $r, $w)
	{
		$w -= $l;
		$ret = array();
		$n2 = ($r - $l - 1) / 2;
		$n = ($r - $l);
		for ($i = 1; $i <= $n2; ++ $i)
		{
			$ret[] = array($this->ids[$l + ($w - $i + $n) % $n], $this->ids[$l + ($w + $i) % $n]);
		}
		return $ret;
	}
	function imba($l, $r)
	{
		$n = ($r - $l);
		$n2 = $n / 2;
		$e = ($l + $r) / 2;
		if (( $r - $l ) % 4 == 0)
		{
			$a = $this->imba($l,$e);
			$b = $this->imba($e,$r);
			$ret = array();
			for ($i = 0; $i < count($a); ++ $i)
			{
				$arr = array();
				for ($j = 0; $j < count($a[$i]); ++ $j)
					$arr[] = $a[$i][$j];
				for ($j = 0; $j < count($b[$i]); ++ $j)
					$arr[] = $b[$i][$j];
				$ret[] = $arr;
			}
			for ($i = 0; $i < $n2; ++ $i)
			{
				$arr = array();
				for ($j = 0; $j < $n2; ++ $j)
				{
					$arr[] = array($this->ids[$l + $j], $this->ids[$l + ($i + $j)%$n2 + $n2]);
				}
				$ret[] = $arr;
			}
			return $ret;
		}
		else if(($r - $l) % 2 == 0)
		{
			$ret = array();
			for ($i = 0; $i < $n2; ++ $i)
			{
				$arr = array();
				$a = $this->odd($l,$e,$i + $l);
				$b = $this->odd($e,$r,$i + $e);
				for ($j = 0; $j < count($a); ++ $j)
					$arr[] = $a[$j];
				for ($j = 0; $j < count($b); ++ $j)
					$arr[] = $b[$j];
				$arr[] = array($this->ids[$i + $l], $this->ids[$i + $e]);
				$ret[] = $arr;
			}
			for ($i = 1; $i < $n2; ++ $i)
			{
				$arr = array();
				for ($j = 0; $j < $n2; ++ $j)
				{
					$arr[] = array($this->ids[$l + $j], $this->ids[$l + ($i + $j)%$n2 + $n2]);
				}
				$ret[] = $arr;
			}
			return $ret;
		}
	}
    function doIt($ids)
    {
    	$ret = array();
    	$this->ids = $ids;
    	$n = count($ids);
		if ($n % 2 == 0)
		{
			$ret = $this->imba(0,$n);
			if ($n > 6)
				$ret = array_slice($ret, 0, 6);
		}
		else
		{
			if ($n <= 5)
			{
				$ret = array();
				for ($i = 0; $i < $n; ++ $i)
					$ret[] = $this->odd(0, $n, $i);
			}
			else
			{
				$ret = array();
				for ($i = 0; $i < 6; ++ $i)
					$ret[] = $this->odd(0, $n, $i);
				$arr = array();
				for ($i = 0; $i < 3; ++ $i)
					$arr[] = array($this->ids[$i], $this->ids[5 - $i]);
				$ret[] = $arr;
			}
		}
	    return $ret;
    }
}

class GroupTournamentStep
{
	var $mp;
	function GroupTournamentStep( )
	{
		$this->mp = array( );
	}
	function AddPair( $arr )
	{
		$this->mp[$arr['a']] = $arr['b'];
		$this->mp[$arr['b']] = $arr['a'];
	}
}

class GroupTournamentTeam
{
	var $bet_id;
	var $pids;
	var $score;
	var $cur_step;
	var $cntd;
	var $num_in_combat;
	function GroupTournamentTeam( $arr )
	{
		$this->pids          = array( );
		$this->bet_id        = $arr['bet_id'];
		$this->score         = $arr['score'];
		$this->cur_step      = $arr['cur_step'];
		$this->cntd          = $arr['cntd'];
		$this->num_in_combat = $arr['num_in_combat'];
	}
	function LoadPlayers( $arr )
	{
		for( $i = 0; $i < 6; ++ $i )
			$this->pids[$i] = $arr["slot_{$i}"];
	}
}

class GroupTournament
{
	var $tid;
	var $name;
	var $ass;
	var $teams;
	var $steps;
	function GroupTournament( $tid )
	{
		$this->tid = $tid;
		$this->name = addslashes( f_MValue( "SELECT name FROM tournament_announcements WHERE tournament_id=$tid" ) );
		$this->ass = array( );
		$this->teams = array( );
		$this->steps = f_MValue( "SELECT max(step_id) FROM tournament_group_assignments WHERE tournament_id=$tid" ) / 2 + 1;
		for( $i = 0; $i < $this->steps * 2; ++ $i ) $this->ass[$i] = new GroupTournamentStep;
		$res = f_MQuery( "SELECT * FROM tournament_group_scores WHERE tournament_id = $tid" );
		while( $arr = f_MFetch( $res ) )
			$this->teams[$arr['bet_id']] = new GroupTournamentTeam( $arr );
		$res = f_MQuery( "SELECT * FROM tournament_group_bets WHERE tournament_id = $tid" );
		while( $arr = f_MFetch( $res ) )
			$this->teams[$arr['bet_id']]->LoadPlayers( $arr );
		$res = f_MQuery( "SELECT * FROM tournament_group_assignments WHERE tournament_id = $tid" );
		while( $arr = f_MFetch( $res ) )
		{
			$this->ass[$arr['step_id']]->AddPair( $arr );
			$this->ass[$arr['step_id'] + 1]->AddPair( $arr );
		}
	}
	function spam( $team, $str )
	{
		foreach( $team->pids as $pid )
		{
			$plr = new Player( $pid );
			$plr->syst2( $str );
		}
	}
	function PlayerOK( $pid )
	{
		if( !f_MValue( "SELECT count( player_id ) FROM online WHERE player_id=$pid" ) ) return false;
		$arr = f_MFetch( f_MQuery( "SELECT loc, depth FROM characters WHERE player_id=$pid" ) );
		if( $arr[0] != 2 || $arr[1] != 43 ) return false;
		return true;
	}
	function Process( )
	{
		foreach( $this->teams as $team ) // сначала исправим cntd
		{
			$opp_id = $this->ass[$team->cur_step]->mp[$team->bet_id];
			if( $team->num_in_combat == 0 && $opp_id && $this->teams[$opp_id]->cur_step == $team->cur_step && $this->teams[$opp_id]->num_in_combat == 0 && $this->teams[$opp_id]->cntd < $team->cntd )
			{
				$team->cntd = $this->teams[$opp_id]->cntd;
				f_MQuery( "UPDATE tournament_group_scores SET cntd = {$team->cntd} WHERE bet_id = {$team->bet_id}" );
			}
		}
		$num_finished = 0;
		foreach( $this->teams as $team )
		{
			if( $team->num_in_combat > 0 ) continue;
			else
			{
				if( !$this->ass[$team->cur_step] ) // команда закончила турнир
				{
					++ $num_finished;
					continue;
				}
				$opp_id = $this->ass[$team->cur_step]->mp[$team->bet_id];
				if( !$opp_id )
				{
					// в этом раунде у команды нет оппонента
					f_MQuery( "UPDATE tournament_group_scores SET cur_step=cur_step + 2 WHERE bet_id = {$team->bet_id}" );
					continue;
				}
				else if( $this->teams[$opp_id]->cur_step == $team->cur_step && $this->teams[$opp_id]->num_in_combat == 0 ) // убедимся, что команда оппонентов уже дошла до этого этапа
    			{
    				if( $team->cntd > 0 )
        			{
        				f_MQuery( "UPDATE tournament_group_scores SET cntd = cntd - 1 WHERE bet_id = {$team->bet_id}" );
        				$this->spam( $team, "Внимание! До старта очередного боя в рамках турнира <b>{$this->name}</b> осталось {$team->cntd} ".my_word_str( $team->cntd, "минута", "минуты", "минут" ) );
        				-- $team->cntd;
        			}
        			else if( $this->teams[$opp_id]->cntd == -1 )
        			{
        				$bet1 = $team->bet_id; $team1 = $team;
        				$bet2 = $opp_id;       $team2 = $this->teams[$opp_id];
        				
        				$arr1 = array(); $arr2 = array();
        				for( $i = 0; $i < 3; ++ $i )
        				{
        					if( $this->PlayerOK( $team1->pids[$i * 2] ) ) $arr1[] = $team1->pids[$i * 2];
        					else if( $this->PlayerOK( $team1->pids[$i * 2 + 1] ) ) $arr1[] = $team1->pids[$i * 2 + 1];
        					if( $this->PlayerOK( $team2->pids[$i * 2] ) ) $arr2[] = $team2->pids[$i * 2];
        					else if( $this->PlayerOK( $team2->pids[$i * 2 + 1] ) ) $arr2[] = $team2->pids[$i * 2 + 1];
        				}
        				
        				$ok = true;
        				$cost = 3;
        				if( $team->cur_step % 2 ) $cost = 2;
        				if( count( $arr1 ) < 3 )
        				{
        					if( count( $arr2 ) == 3 )
        					{
        						f_MQuery( "UPDATE tournament_group_scores SET score=score+$cost WHERE bet_id={$bet2}" );
        						$this->spam( $team2, "Команда ваших оппонентов не смогла собраться в полном составе. Вам защитана техническая победа." );
        					}
       						$this->spam( $team1, "Вы не смогли собраться в полном составе. Вам защитано техническое поражение." );
            				f_MQuery( "UPDATE tournament_group_scores SET cntd = 5, num_in_combat = 0, cur_step = cur_step + 1 WHERE bet_id IN( $bet1, $bet2 )" );
        					$ok = false;
        				}
        				if( count( $arr2 ) < 3 )
        				{
        					if( count( $arr1 ) == 3 )
        					{
        						f_MQuery( "UPDATE tournament_group_scores SET score=score+$cost WHERE bet_id={$bet1}" );
        						$this->spam( $team1, "Команда ваших оппонентов не смогла собраться в полном составе. Вам защитана техническая победа." );
        					}
       						$this->spam( $team2, "Вы не смогли собраться в полном составе. Вам защитано техническое поражение." );
            				f_MQuery( "UPDATE tournament_group_scores SET cntd = 5, num_in_combat = 0, cur_step = cur_step + 1 WHERE bet_id IN( $bet1, $bet2 )" );
        					$ok = false;
        				}
        				
        				if( $ok )
        				{
            				f_MQuery( "UPDATE tournament_group_scores SET cntd = 3, num_in_combat = 3, cur_step = cur_step + 1 WHERE bet_id IN( $bet1, $bet2 )" );
            				
            				if( $team->cur_step % 2 == 0 )
            				{
            					for( $hru = 0; $hru < 3; ++ $hru )
            					{
            						$arr_small_1 = array( $arr1[$hru] );
            						$arr_small_2 = array( $arr2[$hru] );
            						$combat_id = CreateCombat( $arr_small_1, $arr_small_2, 2, 43 );
                    				setCombatTimeout( $combat_id, 75 );
    	            				f_MQuery( "UPDATE combat_players SET win_action = 10, win_action_param = $bet1, log_type=-5 WHERE combat_id = $combat_id AND side=0" );
        	        				f_MQuery( "UPDATE combat_players SET win_action = 10, win_action_param = $bet2, log_type=-5 WHERE combat_id = $combat_id AND side=1" );
        	        				f_MQuery( "UPDATE tournament_group_assignments SET combat_id_$hru = $combat_id WHERE a=$bet1 AND b=$bet2 OR a=$bet2 AND b=$bet1" );
                				}
                			}
                			else
                			{
        						$combat_id = CreateCombat( $arr1, $arr2, 2, 43 );
                				setCombatTimeout( $combat_id, 75 );
	            				f_MQuery( "UPDATE combat_players SET win_action = 11, win_action_param = $bet1, log_type=-5 WHERE combat_id = $combat_id AND side=0" );
    	        				f_MQuery( "UPDATE combat_players SET win_action = 11, win_action_param = $bet2, log_type=-5 WHERE combat_id = $combat_id AND side=1" );
       	        				f_MQuery( "UPDATE tournament_group_assignments SET combat_id = $combat_id WHERE a=$bet1 AND b=$bet2 OR a=$bet2 AND b=$bet1" );
                			}

                			foreach( $arr1 as $id )
                			{
                				$val1 = f_MFetch( f_MQuery( "SELECT value FROM player_attributes WHERE player_id=$id AND attribute_id=101" ) );
                				f_MQuery( "UPDATE player_attributes SET value= $val1[0] WHERE player_id=$id AND attribute_id=1" );
            				}
                			foreach( $arr2 as $id )
                			{
                				$val1 = f_MFetch( f_MQuery( "SELECT value FROM player_attributes WHERE player_id=$id AND attribute_id=101" ) );
                				f_MQuery( "UPDATE player_attributes SET value= $val1[0] WHERE player_id=$id AND attribute_id=1" );
            				}
            				
            				$this->spam( $team1, "Начался очередной бой в рамках турнира <b>{$this->name}</b>" );
            				$this->spam( $team2, "Начался очередной бой в рамках турнира <b>{$this->name}</b>" );
            				$this->spam( $team1, "/combat" );
            				$this->spam( $team2, "/combat" );
        				}
        			} else $team->cntd = -1;
        		}
        	}
		}
		if( $num_finished == count( $this->teams ) )
		{
			echo "TOURNAMENT IS FINISHED<br>\n";

			$res = f_MQuery( "SELECT * FROM tournament_group_scores WHERE tournament_id={$this->tid} ORDER BY score DESC" );
			$pos = 0;
			$id = 0;
			$last_score = -1;
			$money = array( -1, 10000, 7500, 5000 );
			$pts = array( -1, 20, 10, 5 );
			while( $arr = f_MFetch( $res ) )
			{
				++ $id;
				if( $arr['score'] != $last_score ) $pos = $id;
				$last_score = $arr['score'];
				if( $pos > 3 ) break;
				$this->spam( $this->teams[$arr['bet_id']], "Ваша команда заняла <b>$pos</b> место на турнире <b>{$this->name}</b>. Вы получаете {$money[$pos]} дублонов. Ваш Орден получает {$pts[$pos]} очков славы." );
				f_MQuery( "UPDATE clans SET glory=glory + {$pts[$pos]} WHERE clan_id=".f_MValue( "SELECT clan_id FROM tournament_group_bets WHERE bet_id={$arr[bet_id]}" ) );
				foreach( $this->teams[$arr['bet_id']]->pids as $pid )
				{
					$plr = new Player( $pid );
					$plr->AddMoney( $money[$pos] );
				}
			}
			f_MQuery( "UPDATE tournament_announcements SET status=5 WHERE tournament_id={$this->tid}" );
			f_MQuery( "INSERT INTO tournament_results( tournament_id, champion ) VALUES ( {$this->tid}, -1 )" );
		}
	}
}

// $zap = false won't work with ajax
function showBet($arr,$zap=true)
{
	global $id;
	global $player;
	$sid = 0;
	$st = '';
	for ($i = 0; $i < 3; ++ $i)
	{
		$st .= "<tr>";
		for ($j = 0; $j < 2; ++ $j)
		{
    		$ltr = chr(ord('А') + $j * 3 + $i);
    		$st .= '<td width=90>';
    		$st .= "<b>{$ltr}.</b>&nbsp;";
    		$ok = false;
    		if( 0 < (int)$arr['slot_' . $sid] )
    		{
    			$plr = new Player( (int)$arr['slot_' . $sid] );
    			if( $plr->player_id > 0 )
    			{
    				$ok = true;
    				if( $zap ) $st .= "<b>" . $plr->login . "</b>";
    				else $st .= "<script>document.write(" . $plr->Nick() . ");</script>";
    				if( $zap && $plr->player_id == $player->player_id ) $st .= "&nbsp;[<a href='javascript:void(0)' onclick='query(\"tournament_signup.php?id=$id&remove=1\",\"\")'>X</a>]";
    			}
    		}
    		if (!$ok)
    		{
    			if($zap) $st .= "<a href='javascript:void(0)' onclick='query(\"tournament_signup.php?id=$id&apply=$sid&bid=$arr[bet_id]\",\"\")'>Записаться</a>";
    			else $st .= "<i>Свободно</i>";
    		}
    		$st .= "</td>";
    		
    		++ $sid;
    	}
    	$st .= "</tr>";
    }
	return $st;
}

function ProcessGroupTournament( $tid )
{
	echo "TID = $tid<br>\n";
	$gt = new GroupTournament( $tid );
	$gt->Process( );
}

function StartGroupTournament($tid)
{
	// тут неплохо бы улучшить табличку чтобы убрать пустые и тех, кто апнулся
	f_MQuery( "UPDATE tournament_group_bets SET slot_0=0 WHERE slot_0 > 0 AND (SELECT level FROM characters WHERE player_id=slot_0) > 9 AND tournament_id=$tid" );
	f_MQuery( "UPDATE tournament_group_bets SET slot_1=0 WHERE slot_1 > 0 AND (SELECT level FROM characters WHERE player_id=slot_1) > 9 AND tournament_id=$tid" );
	f_MQuery( "UPDATE tournament_group_bets SET slot_2=0 WHERE slot_2 > 0 AND (SELECT level FROM characters WHERE player_id=slot_2) > 14 AND tournament_id=$tid" );
	f_MQuery( "UPDATE tournament_group_bets SET slot_3=0 WHERE slot_3 > 0 AND (SELECT level FROM characters WHERE player_id=slot_3) > 14 AND tournament_id=$tid" );
	f_MQuery( "DELETE FROM tournament_group_bets WHERE tournament_id=$tid AND ((slot_0=0 AND slot_1=0) OR (slot_2=0 AND slot_3=0) OR (slot_4=0 AND slot_5=0))" );
	
	$res = f_MQuery( "SELECT bet_id FROM tournament_group_bets WHERE tournament_id = $tid ORDER BY rand()" );
	if( f_MNum( $res ) <= 1 )
	{
		// cancel tournament
		f_MQuery( "UPDATE tournament_announcements SET status=5 WHERE tournament_id=$tid" );
		return false;
	}
	$ids = array();
	while( $arr = f_MFetch( $res ) )
	{
		$ids[] = $arr[0];
		f_MQuery( "INSERT INTO tournament_group_scores (tournament_id, bet_id, cntd) VALUES ($tid, $arr[0], 5)" );
	}
	$tb = new TeamsDistributor;
	$ass = $tb->doIt($ids);
	$step_id = 0;
	foreach( $ass as $arr )
	{
		foreach( $arr as $pair ) f_MQuery( "INSERT INTO tournament_group_assignments ( tournament_id, step_id, a, b ) VALUES ( $tid, $step_id, {$pair[0]}, {$pair[1]} )" );
		$step_id += 2;
	}
	return true;
}

//$pd = new TeamsDistributor;

//print_r($pd->doIt(array(1,2,3,4,5,6,7,8,9,10,11)));

//echo "Moo!";

?>
