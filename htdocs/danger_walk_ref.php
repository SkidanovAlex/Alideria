<?

$mid_php = 1;

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "danger_walk_functions.php" );
include_once( "game_functions.php" );
include_once( "phrase.php" );
include_once( "prof_exp.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$noob = false;
if( $player->level == 1 )	
{
	include_once( "noob.php" );
    $noob = 0;
    $res = f_MQuery( "SELECT a, b FROM noob WHERE player_id={$player->player_id}" );
    $arr = f_MFetch( $res );
    if( $arr ) { $noob = $arr[0]; $noob_param = $arr[1]; }
}


$depth = $place = $player->depth;
$loc = $player->location;
$till = $player->till;
$regime = $player->regime;
$tm = time( );

if( $loc != 0 || $depth > 20 ) die( );
if( $regime == 100 ) die( "location.href='combat.php';" );

if( $player->regime == 107 && isset( $_GET['num'] ) )
{
	$res = f_MQuery( "SELECT number FROM player_num WHERE player_id = {$player->player_id}" );
	$arr = f_MFetch( $res );
	if( !$arr || $arr[0] == $_GET['num'] )
	{
		$player->SetRegime( 0 );
		$regime = 0;
		f_MQuery( "DELETE FROM player_num WHERE player_id={$player->player_id}" );
	}
	else $player->syst( "Вы ввели неправильный код", false );
}

if( $till && $tm >= $till - 2 ) // Нападения и события
{
	f_MQuery( "UPDATE characters SET go_till = 0 WHERE player_id = {$player->player_id}" );
	$till = 0;

	if( $regime == 4 ) $regime = 0; // хак

	$st = "<br>";
	if( $regime == -1 )
	{
		$player->SetDepth( $depth - 1 );
		$st .= "Вы закончили передвижение<br>";
	}
	else if( $regime == 1 )
	{
		$player->SetDepth( $depth + 1 );
		$st .= "Вы закончили передвижение<br>";

		if( $noob == 18 )
		{
			f_MQuery( "UPDATE noob SET a=19, b=0 WHERE player_id={$player->player_id}" );
			$noob = 19; $noob_param = 0;
		}
	}
	else if( $regime == 0 )
	{
		$st .= "Вы закончили исследование<br>";
		if( $player->depth == 5 ) checkZhorik( $player, 9, 3 ); // квест жорика пять раз исследовать пятую глубину
	}
	else if( $regime == 2 )
	{
		$st .= "Вы закончили поиск камней<br>";
		createStoneTable( );
		$player->SetRegime( 3 );
		echo "location.href='game.php';";
		//include( "stone_table.php" );
		return;
	}
	$depth = $player->depth;

	$player->SetRegime( 0 );
	
	if( $depth == -1 )
	{
		if( $loc == 0 )
		{
			$player->SetLocation( 2 );
			$player->SetDepth( 5 );
			die( "location.href='game.php';" );
		}
		if( $loc == 1 )
		{
			$player->SetLocation( 2 );
			$player->SetDepth( 3 );
			die( "location.href='game.php';" );
		}
	}

	if( $regime == 1 || $regime == -1 )
	{
		echo "_( 'depth' ).innerHTML = '<b>$depth</b>';";
		$res = f_MQuery( "SELECT text FROM loc_texts WHERE loc=$loc AND depth=$depth" );
		$arr = f_MFetch( $res );
		echo "_( 'loc_desc' ).innerHTML = '".AjaxStr( $arr[0] )."';";
	}

	$res = f_MQuery( "SELECT * FROM player_depths WHERE player_id={$player->player_id} AND loc=$loc" );
	$arr = f_MFetch( $res );
	
	if( $regime >= -1 && $regime <= 1 )
	{
		if( !$arr ) $maxdepth = 0;
		else $maxdepth = $arr[depth];

		$attack = 0;
		$event = 0;
		$win_action = 0;
		$win_action_param = 0;
		if( $player->depth > $maxdepth )
		{
			$res = f_MQuery( "SELECT mob_id FROM mobs WHERE loc=$loc AND defend_depth = {$player->depth}" );
			$arr = f_MFetch( $res );
			if( $arr )
			{
				$attack = $arr[mob_id];
				$win_action = 1;
				$win_action_param = $depth;
			}
			else $event = 1;
		}
		
		else
		{
			$rand = mt_rand( 1, 10 );
			if( $rand <= 3 )
			{
				$res = f_MQuery( "SELECT mob_id FROM mobs WHERE loc=$loc AND min_depth <= {$player->depth} AND max_depth >= {$player->depth} ORDER BY rand() LIMIT 1" );
				$arr = f_MFetch( $res );
				if( $arr )
					$attack = $arr[0];
				else $event = 1;
			}
			else
				$event = 1;
		}
		
		if( $attack )
		{
			include( "mob.php" );

			if( $player->level == 1 )
			{
				include_once( 'player_noobs.php' );
				PingNoob( 6 );
			}

			$mob = new Mob;
			$mob->CreateMob( $attack, $loc, $player->depth );
			$mob->AttackPlayer( $player->player_id, $win_action, $win_action_param, true /* нападаем кроваво */ );
			$rnd = 4;
			while( $mob->level + $rnd - 3 < $player->level && mt_rand( 1, $rnd ) == 1 )
			{
				$mob = new Mob;
    			$mob->CreateMob( $attack, $loc, $player->depth );
    			$mob->AttackPlayer( $player->player_id, $win_action, $win_action_param, true /* нападаем кроваво */ );
    			$rnd += 2;
			}

			$ava = f_MValue( "SELECT avatar FROM mobs WHERE mob_id={$attack}" );
			$ava = "images/avatars/".str_replace( ".jpg", ".png", $ava );
			$st = "<table><tr><td valign=top><img width=100 height=225 src=$ava></td><td valign=top><font color=darkred><b>Внимание!</b></font><br>Вас атакует <a href=help.php?id=1016&beast_id=$attack target=_blank>{$mob->name}</a><br><br><a href=combat.php><b>В бой!!!</b></a></td></tr></table>";
			echo "removeHandler( document, 'click', hideLastMsg );";
			
//		die( "<script>location.href = 'combat.php';</script>" );
		}
		else if( $event )
		{
			// События
			include( 'walk_events.php' );
			$st .= walk_event( $loc, $depth, $regime, /*tags=*/false );
			if( $player->depth > 0 && mt_rand( 1,12 ) == 5 || mt_rand( 1,40 ) == 5 )
			{
				$player->SetRegime( 107 );
			}
			$st .= "<br><a href='javascript:hideLastMsg()'>Закрыть</a><br><br>";
		}
	}

	if( $st != "" )
	{
		echo "showCaveMsg( '".AjaxStr( $st )."' );";
	}
}
else if( $till && isset( $_GET['cancel'] ) )
{
	echo "window.top.document.title = window.top.tstr;";
	$player->SetRegime( 0 );
	$player->SetTill( 0 );
	$regime = 0;
	$till = 0;
}

$cant_move_wo_ = $player->depth >= 2 && !$player->HasWearedItem( 8 );

if( $player->regime == 0 && !$till && isset( $HTTP_GET_VARS['dir'] ) && !$attack )
{
	$dir = $HTTP_GET_VARS['dir'];
	settype( $dir, "integer" );
	if( $dir < -1 ) $dir = -1;

	if( isStoneDepth( $depth ) && $dir == 2 ) $dir = 2; // :o)
	else if( $depth == 1 && $dir == 2 )
	{
		$player->SetDepth( 33 );
		die( "location.href='game.php';" );
	}
	else if( $dir > 1 ) $dir = 1;
	else if( $dir == 0 ) $dir = 4; // хак

	if( $dir == 1 && $depth == 10 && $player->player_id != 173 ) $dir = 4;  // временно спуститься глубже нельзя
	
	if( $dir == 1 && $cant_move_wo_ )
	{
		echo "<i>Вы не можете пройти глубже без факела в руке</i><br><br>";
	}
	else
	{
		$player->SetRegime( $dir );
	
		$till = $tm + 15;
		if( $dir == 2 && $depth == 0 )
			if ($player->player_id==6825)
				$till = $tm + 15;
			else
				$till = $tm + 10 * 60;
		if( $dir == 2 && $depth == 5 )
			$till = $tm + 60 * 60;
		if( $dir == 2 && $depth == 10 )
			$till = $tm + 5 * 60 * 60;
		$player->SetTill( $till );

		$regime = $player->regime;
		$till = $player->till;
	}
}

echo "_( 'd_acts' ).innerHTML = '".AjaxStr( getPossibleDirections( ) )."';";
echo "_( 'd_content' ).innerHTML = '".AjaxStr( getMidContent( ) )."';";
echo $exec;

ob_start( );
ShowAdditionalActions( );
ShowNPCs( );
$here_you_can = ob_get_contents();
ob_end_clean( );

echo "_( 'here_you_can' ).innerHTML = '".AjaxStr( $here_you_can )."';";

?>
