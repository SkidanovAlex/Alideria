<?

include_once( 'skin.php' );
include_once( 'player.php' );

function getNick( $id )
{
	$plr = new Player( $id );
	if( !$plr->level )  return "'<i>Моб</i>'";
	return $plr->Nick2( );
}

class Match
{
	var $id;
	var $prev1;
	var $prev2;
	var $opponent;
	var $winner_goes_to;
	var $looser_goes_to;
	var $players;
	var $res;
	var $way;

	function Match( $id )
	{
		$this->players = Array( );
		$this->id = $id;
		$this->prev1 = false;
		$this->prev2 = false;
		$this->opponent = false;
		$this->winner_goes_to = false;
		$this->looser_goes_to = false;
	}
}

class Tournament
{
	var $id;
	var $n;
	var $rounds;
	var $player_ids;
	var $results;
	var $matches;

	var $main_matches;
	var $second_matches;

	var $champion, $secondPlace, $thirdPlace;

	function Tournament( $id, $ids )
	{
		$this->id = $id;

		$this->n = count( $ids );
		$this->player_ids = $ids;

		$this->main_matches = Array( );
		$this->second_matches = Array( );
		$this->matches = Array( );
		    
        // compute first row in main
		$arr = Array( );
		$num = 0;
		for( $i = 0; $i < $this->n / 2; ++ $i )
		{
			$arr[$i] = new Match( $i );
			$arr[$i]->players[] = $ids[$i * 2];
			$arr[$i]->players[] = $ids[$i * 2 + 1];
			$arr[$i]->res = Array( 0, 0 );
			$this->matches[$i] = $arr[$i];
            ++ $num;
		}

		$this->main_matches[0] = $arr;
		$this->second_matches[0] = Array( );

		for( $i = 1; ; $i += 2 )
		{
			if( count( $this->main_matches[$i - 1] ) == 1 )
			{
				$this->rounds = $i;
				break;
			}

            // from main to main
			for( $j = 0; $j < count( $this->main_matches[$i - 1] ); $j += 2 )
			{
				$m = new Match( $num );
				$m->way = 'from main to main';
				$m->prev1 = $this->main_matches[$i - 1][$j]->id;
				$m->prev2 = $this->main_matches[$i - 1][$j + 1]->id;
				$this->main_matches[$i - 1][$j]->winner_goes_to = $num;
				$this->main_matches[$i - 1][$j + 1]->winner_goes_to = $num;
				$this->main_matches[$i - 1][$j]->opponent = $this->main_matches[$i - 1][$j + 1]->id;
				$this->main_matches[$i - 1][$j + 1]->opponent = $this->main_matches[$i - 1][$j]->id;
				$this->main_matches[$i + 1][$j / 2] = $m;
				$this->matches[$num] = $m;
                ++ $num;
			}

            // from second to second 
			$arr = Array( );
            if( $i > 1 )
            {
    			for( $j = 0; $j < count( $this->second_matches[$i - 1] ); $j += 2 )
    			{
    				$m = new Match( $num );
					$m->way = 'from second to second 1';
    				$m->prev1 = $this->second_matches[$i - 1][$j]->id;
    				$m->prev2 = $this->second_matches[$i - 1][$j + 1]->id;
    				$this->second_matches[$i - 1][$j]->winner_goes_to = $num;
    				$this->second_matches[$i - 1][$j + 1]->winner_goes_to = $num;
    				$this->second_matches[$i - 1][$j]->opponent = $this->second_matches[$i - 1][$j + 1]->id;
    				$this->second_matches[$i - 1][$j + 1]->opponent = $this->second_matches[$i - 1][$j]->id;
    				$arr[] = $m;
    				$this->matches[$num] = $m;
                    ++ $num;
    			}
			}
			else // from first to second
			{
    			for( $j = 0; $j < count( $this->main_matches[$i - 1] ); $j += 2 )
    			{
    				$m = new Match( $num );
					$m->way = 'from first to second';
    				$m->prev1 = $this->main_matches[$i - 1][$j]->id;
    				$m->prev2 = $this->main_matches[$i - 1][$j + 1]->id;
    				$this->main_matches[$i - 1][$j]->looser_goes_to = $num;
    				$this->main_matches[$i - 1][$j + 1]->looser_goes_to = $num;
    				$arr[] = $m;
    				$this->matches[$num] = $m;
                    ++ $num;
    			}
			}

			if( count( $this->main_matches[$i + 1] ) == 1 )
			{
				$this->second_matches[$i + 1] = $arr;
				continue;
			}
			else $this->second_matches[$i] = $arr;

            // from first to second
    		$arr = Array( );
			for( $j = 0; $j < count( $this->main_matches[$i + 1] ); $j += 2 )
			{
				$m = new Match( $num );
				$m->way = 'from main to second';
				$m->prev1 = $this->main_matches[$i + 1][$j]->id;
				$m->prev2 = $this->main_matches[$i + 1][$j + 1]->id;
				$this->main_matches[$i + 1][$j]->looser_goes_to = $num;
				$this->main_matches[$i + 1][$j + 1]->looser_goes_to = $num;
				$arr[] = $m;
				$this->matches[$num] = $m;
                ++ $num;
			}
			// from second to second
			for( $j = 0; $j < count( $this->second_matches[$i] ); $j += 2 )
			{
				$m = new Match( $num );
				$m->way = 'from second to second';
				$m->prev1 = $this->second_matches[$i][$j]->id;
				$m->prev2 = $this->second_matches[$i][$j + 1]->id;
				$this->second_matches[$i][$j]->winner_goes_to = $num;
				$this->second_matches[$i][$j + 1]->winner_goes_to = $num;
				$this->second_matches[$i][$j]->opponent = $this->second_matches[$i][$j + 1]->id;
				$this->second_matches[$i][$j + 1]->opponent = $this->second_matches[$i][$j]->id;
				$arr[] = $m;
				$this->matches[$num] = $m;
                ++ $num;
			}
			$this->second_matches[$i + 1] = $arr;
		}
		// wildcard
		$m = new Match( $num );
		$m->prev1 = $this->main_matches[$this->rounds - 1][0]->id;
		$m->prev2 = $this->second_matches[$this->rounds - 1][0]->id;
		$this->main_matches[$this->rounds - 1][0]->looser_goes_to = $num;
		$this->second_matches[$this->rounds - 1][0]->winner_goes_to = $num;
		$this->second_matches[$this->rounds] = Array( $m );
		$this->rounds ++;
		$this->matches[$num] = $m;
        ++ $num;
		// финал
		$m = new Match( $num );
		$m->prev1 = $this->main_matches[$this->rounds - 2][0]->id;
		$m->prev2 = $this->second_matches[$this->rounds - 1][0]->id;
		$m->winner_goes_to = "champion";
		$m->looser_goes_to = "second";
		$this->main_matches[$this->rounds - 2][0]->winner_goes_to = $num;
		$this->second_matches[$this->rounds - 1][0]->winner_goes_to = $num;
		$this->main_matches[$this->rounds + 1] = Array( $m );
		$this->matches[$num] = $m;
        ++ $num;
        // встреча за третье место
		$m = new Match( $num );
		$m->prev1 = $this->second_matches[$this->rounds - 2][0]->id;
		$m->prev2 = $this->second_matches[$this->rounds - 1][0]->id;
		$m->winner_goes_to = "third";
		$this->second_matches[$this->rounds - 1][0]->looser_goes_to = $num;
		$this->second_matches[$this->rounds - 2][0]->looser_goes_to = $num;
		$this->main_matches[$this->rounds] = Array( $m );
		$this->matches[$num] = $m;
        ++ $num;
	}

	function SaveState( )
	{
		$tid = $this->id;
		f_MQuery( "DELETE FROM tournament_net WHERE tournament_id=$tid" );
		foreach( $this->matches as $m )
		{
			foreach( $m->players as $id=>$p )
			{
				$score = (int)$m->res[$id];
				f_MQuery( "INSERT INTO tournament_net ( tournament_id, player_id, score, round ) VALUES ( $tid, $p, $score, $m->id )" );
			}
		}
	}

	function SaveResults( )
	{
		settype( $this->champion, 'integer' );
		settype( $this->secondPlace, 'integer' );
		settype( $this->thirdPlace, 'integer' );
		f_MQuery( "DELETE FROM tournament_results WHERE tournament_id={$this->id}" );
		f_MQuery( "INSERT INTO tournament_results VALUES( {$this->id}, {$this->champion}, {$this->secondPlace}, {$this->thirdPlace} )" );
	}

	function LoadState( )
	{
		$tid = $this->id;
		$res = f_MQuery( "SELECT DISTINCT * FROM tournament_net WHERE tournament_id=$tid" );
		foreach( $this->matches as $m ) 
		{
			$m->players = Array( );
			$m->res = Array( );
		}
		while( $arr = f_MFetch( $res ) )
		{
			$this->matches[$arr['round']]->players[] = $arr[player_id];
			$this->matches[$arr['round']]->res[] = $arr[score];
		}

		$res = f_MQuery( "SELECT * FROM tournament_results WHERE tournament_id=$tid" );
		$arr = f_MFetch( $res );
		if( $arr )
		{
			$this->champion = $arr['champion'];
			$this->secondPlace = $arr['second_place'];
			$this->thirdPlace = $arr['third_place'];
        }
	}

	function DebugOut( )
	{
		echo "{$this->rounds} Rounds<br><br>";
		foreach( $this->matches as $id=>$m )
		{
			echo "[<b>Match {$id}</b>] {$m->way}<br>";
			echo "Between advancers from {$m->prev1} AND {$m->prev2}<br>";
			echo "With: {$m->opponent}<br>";
			echo "Advances to {$m->winner_goes_to}, loser to {$m->looser_goes_to}<br>";
			echo "<br>";
		}
	}

	function RandomMatch( )
	{
		foreach( $this->matches as $m )
		{
			do
			{
				$a = mt_rand( 0, 2 );
				$b = mt_rand( 0, 2 );
			} while( $a == 2 && $b == 2 || $a < 2 && $b < 2 );

			$m->res[0] = $a;
			$m->res[1] = $b;
			if( $a == 2 )
			{
				if( 0 != (int)$m->winner_goes_to ) $this->matches[$m->winner_goes_to]->players[] = $m->players[0];
				else if( $m->winner_goes_to == 'champion' ) $this->champion = $m->players[0];
				else if( $m->winner_goes_to == 'third' ) $this->thirdPlace = $m->players[0];

				if( (int)$m->looser_goes_to ) $this->matches[$m->looser_goes_to]->players[] = $m->players[1];
				else if( $m->looser_goes_to == 'second' ) $this->secondPlace = $m->players[1];
			}
			else
			{
				if( 0 != (int)$m->winner_goes_to ) $this->matches[$m->winner_goes_to]->players[] = $m->players[1];
				else if( $m->winner_goes_to == 'champion' ) $this->champion = $m->players[1];
				else if( $m->winner_goes_to == 'third' ) $this->thirdPlace = $m->players[1];

				if( (int)$m->looser_goes_to ) $this->matches[$m->looser_goes_to]->players[] = $m->players[0];
				else if( $m->looser_goes_to == 'second' ) $this->secondPlace = $m->players[0];
			}
		}
	}

	function Render( )
	{
		$w = 200;
		$h = 50;
		$xs = 20;
		$ys = 5;
		$st = "";

		$st .= "<script>\n";
		$st .= "var FU = '".AddSlashes(GetScrollLightTableStart2())."';\n";
		$st .= "var FL = '".AddSlashes(GetScrollLightTableEnd())."';\n";
		$st .= "var win_go = new Array( );\nvar lose_go = new Array( );";
		$st .= "function out( s1, s2, r1, r2, x, y, id ) { document.write( '<div onmouseout=\"hideR(' + id + ')\" onmouseover=\"showR(' + id + ')\" style=\"width:{$w}px;height:{$h}px;position:absolute;left:' + x + 'px;top:' + y + 'px\">' + FU + '<table width=100% height=100%><tr><td width=".($w-20).">' + s1 + '</td><td width=20 align=right><b>' + r1 + '</b></td></tr><tr><td width=".($w-20).">' + s2 + '</td><td width=20 align=right><b>' + r2 + '</b></td></table>' + FL + '</div>' ); }\n";
		$st .= "function showR( id ) { if( win_go[id] ) { document.getElementById( 'win_div' ).style.left = win_go[id][0] + 'px'; document.getElementById( 'win_div' ).style.top = win_go[id][1] + 'px'; document.getElementById( 'win_div' ).style.display = ''; } else document.getElementById( 'win_div' ).style.display='none'; if( lose_go[id] ) { document.getElementById( 'lose_div' ).style.left = lose_go[id][0] + 'px'; document.getElementById( 'lose_div' ).style.top = lose_go[id][1] + 'px'; document.getElementById( 'lose_div' ).style.display = ''; } else document.getElementById( 'lose_div' ).style.display='none'; }";
		$st .= "function hideR( id ) { document.getElementById( 'win_div' ).style.display='none'; document.getElementById( 'lose_div' ).style.display='none'; }";

		$st .= "document.write( \"<div id=win_div style='display:none;position:absolute;border:2px solid green;'><div id=win_d style='width:{$w}px;height:{$h}px;'>&nbsp;</div></div> \" );\n";
		$st .= "document.write( \"<div id=lose_div style='display:none;position:absolute;border:2px solid red;'><div id=win_d style='width:{$w}px;height:{$h}px;'>&nbsp;</div></div> \" );\n";

		$X = Array( );
		$Y = Array( );

		$start = 0;
		$dy = $ys;
		$pwr = 1;
		for( $i = 0; $i <= $this->rounds + 1; ++ $i )
		{
/*			if( $i % 2 == 1 )
			{
				$start += floor( ( $dy + $h ) / 2 );
				$dy = $dy * 2 + $h;
			}*/
			if( $i % 2 == 1 && $i != $this->rounds - 1 ) $pwr *= 2;
			if( $i == $this->rounds - 3 ) continue;

			if( $i >= 3 && $i < $this->rounds )
			{
				$start = ( $this->n / 4 - count( $this->second_matches[$i] ) ) * ( $h + $ys );
			}      

			if( $i < $this->rounds - 2 ) $x = $i * ( $w + $xs ) + $xs;
			else $x = ( $i - 1 ) * ( $w + $xs ) + $xs;
			$y = $start;
//			if( $i >= $this->rounds ) $y += $h + $ys;

			if( $i == $this->rounds )
				$st .= "document.write( '<div style=\"position:absolute;left:{$x}px;top:".($y-20)."px;width:{$w}px;height:{$h}px;\"><center>Встреча за третье место</center></div>' );";
			if( $i == $this->rounds + 1 )
				$st .= "document.write( '<div style=\"position:absolute;left:{$x}px;top:".($y-20)."px;width:{$w}px;height:{$h}px;\"><center><b>Финал</b></center></div>' );";

			for( $j = 0; $j < count( $this->main_matches[$i] ); ++ $j )
			{
				$a = $b = '"<i>Неизвестен</i>"';
				$r1 = $r2 = 0;
				if( count( $this->main_matches[$i][$j]->players ) >= 1 )
				{
					$plr = new Player( $this->main_matches[$i][$j]->players[0] );
					if( !$plr->level ) $a = '"<i>Моб</i>"';
					else $a = $plr->Nick2( );
					$r1 = $this->main_matches[$i][$j]->res[0];
				}
				if( count( $this->main_matches[$i][$j]->players ) >= 2 )
				{
					$plr = new Player( $this->main_matches[$i][$j]->players[1] );
					if( !$plr->level ) $b = '"<i>Моб</i>"';
					else $b = $plr->Nick2( );
					$r2 = $this->main_matches[$i][$j]->res[1];
				}
				$X[$this->main_matches[$i][$j]->id] = $x;
				$Y[$this->main_matches[$i][$j]->id] = $y;
				$st .= "out( $a, $b, $r1, $r2, {$x}, {$y}, {$this->main_matches[$i][$j]->id} );\n";
				$y += $dy + $h;
			}
			$y = $this->n / 2 / $pwr * ( $h + $ys ) + $start;
			for( $j = 0; $j < count( $this->second_matches[$i] ); ++ $j )
			{
				$a = $b = '"<i>Неизвестен</i>"';
				$r1 = $r2 = 0;
				if( count( $this->second_matches[$i][$j]->players ) >= 1 )
				{
					$plr = new Player( $this->second_matches[$i][$j]->players[0] );
					if( !$plr->level ) $a = '"<i>Моб</i>"';
					else $a = $plr->Nick2( );
					$r1 = $this->second_matches[$i][$j]->res[0];
				}
				if( count( $this->second_matches[$i][$j]->players ) >= 2 )
				{
					$plr = new Player( $this->second_matches[$i][$j]->players[1] );
					if( !$plr->level ) $b = '"<i>Моб</i>"';
					else $b = $plr->Nick2( );
					$r2 = $this->second_matches[$i][$j]->res[1];
				}
				$X[$this->second_matches[$i][$j]->id] = $x;
				$Y[$this->second_matches[$i][$j]->id] = $y;
				$st .= "out( $a, $b, $r1, $r2, {$x}, {$y}, {$this->second_matches[$i][$j]->id} );\n";
				$y += $dy + $h;
			}
		}
		$x = ( $this->rounds - 1 ) * ( $w + $xs ) + $xs;
		$y = $start + $h + $ys;
		$a1 = ( $this->champion ) ? getNick( $this->champion ) : "'<i>Неизвестен</i>'";
		$a2 = ( $this->secondPlace ) ? getNick( $this->secondPlace ) : "'<i>Неизвестен</i>'";
		$a3 = ( $this->thirdPlace ) ? getNick( $this->thirdPlace ) : "'<i>Неизвестен</i>'";
		$W = $w * 2 + $xs;
		$st .= "document.write( '<div style=\"width:{$W}px;position:absolute;left:{$x}px;top:{$y}px\">' + FU + '<table width=100% height=100%><tr><td>Победитель</td><td align=right><b>' + $a1 + '</b></td></tr><tr><td>Второе место</td><td align=right><b>' + $a2 + '</b></td></tr><tr><td>Третье Место</td><td align=right><b>' + $a3 + '</b></td></tr></table>' + FL + '</div>' );";

		foreach( $this->matches as $m )
		{
			if( (int)$m->winner_goes_to ) $st .= "win_go[{$m->id}] = new Array( ".($X[$m->winner_goes_to]-2).", ".($Y[$m->winner_goes_to]-2)." );\n";
			if( (int)$m->looser_goes_to ) $st .= "lose_go[{$m->id}] = new Array( ".($X[$m->looser_goes_to]-2).", ".($Y[$m->looser_goes_to]-2)." );\n";
		}

		$st .= "</script>";

		return $st;
	}
}

/*

?>


<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">

<?
include_js( 'js/ii.js' );

f_MConnect( );
$arr = Array( 172, 173, 174, 3264, 8412, 8371, 8086, 6866 ,186, 736, 738, 739, 741, 742, 743, 744 );
$t = new Tournament( 0,$arr );
//$t->DebugOut( );
$t->RandomMatch( );
echo $t->Render( );

/**/
?>
