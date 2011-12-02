<?

include_once( "mob.php" );

function ta_now( )
{
	global $player;

	if( $player->level < 2 ) return false;

//	return false;
	$res = f_MQuery( "SELECT combat_id FROM ta_combats WHERE combat_id >= 0" );
	$arr = f_MFetch( $res ); if( !$arr ) return false;

	$day = date( "N", time( ) );
	$hour = date( "G", time( ) );
//	LogError( "$day, $hour" );

	if( $day % 7 == 0 && ( $hour == 18 || $hour == 19 ) ) ;
	else if( $day == 3 && ( $hour == 19 || $hour == 20 ) ) ;
//	if( $player->player_id == 173 ) ;
	else
		return false;

	return true;
}

function ta_check( $type )
{
	$time = time( );
	$day = date( "N", $time );
	$hour = date( "G", $time );
	$minute = date( "i", $time );

	if( $day % 7 == 0 && $hour == 18 || $day == 3 && $hour == 19 )
	{
		if( $type == 0 && $minute < 15 ) return 3;
		if( $type == 1 && $minute < 25 ) return 3;
		if( $type == 2 && $minute < 15 ) return 3;
		if( $type == 3 && $minute < 5 ) return 3;
		if( $type == 4 && $minute < 5 ) return 3;
		if( $type == 5 && $minute < 10 ) return 3;
	}

	$res = f_MQuery( "SELECT combat_id FROM ta_combats WHERE type=$type" );
	$arr = f_MFetch( $res );

	if( $arr[0] == -2 )
	{
		return 2;
	}

	if( $arr[0] )
	{
     	$res = f_MQuery( "SELECT player_id FROM combat_players WHERE combat_id=$arr[0] AND ready < 2 AND side=1" );
     	$arr = f_MFetch( $res );
     	if( !$arr )
     	{
     		return 0;
     	}
	}

	return 1;
}

function ta_attack( $type )
{
	global $player;

	$combat_id = 0;

	f_MQuery( "LOCK TABLE ta_combats WRITE" );
	$res = f_MQuery( "SELECT combat_id FROM ta_combats WHERE type=$type" );
	$arr = f_MFetch( $res );

	if( $arr[0] == -1 ) return false;
	if( $arr[0] == 0 ) f_MQuery( "UPDATE ta_combats SET combat_id = -1 WHERE type=$type" );
	else $combat_id = $arr[0];

	f_MQuery( "UNLOCK TABLES" );

	if( $combat_id == 0 )
	{
		$mobs = array( );
		// cave
		if( $type == 0 ) $mobs = array( 10 => 8, 22 => 5 );
		if( $type == 1 ) $mobs = array( 7 => 20, 8 => 7, 18 => 5, 10 => 3, 34 => 5 );
		if( $type == 2 ) $mobs = array( 27 => 1, 7 => 5 );
		// river
		if( $type == 3 ) $mobs = array( 28 => 10, 29 => 3, 30 => 2 );
		if( $type == 4 ) $mobs = array( 31 => 3, 32 => 1 );
		if( $type == 5 ) $mobs = array( 33 => 1, 29 => 2, 28 => 1 );
		// forest
		if( $type == 6 ) $mobs = array( 13 => 6, 11 => 10 );


		foreach( $mobs as $mob_id=>$count ) for( $i = 0; $i < $count; ++ $i )
		{
			$mob = new Mob( );
			$mob->CreateMob( $mob_id, $player->location, $player->depth );
			$mob->AttackPlayer( $player->player_id, 6 );
			$combat_id = $mob->combat_id;
		}
		setCombatTimeout( $combat_id, 40 );
		f_MQuery( "UPDATE ta_combats SET combat_id = $combat_id WHERE type=$type" );
	}
	else
	{
		$res = f_MQuery( "SELECT player_id FROM combat_players WHERE combat_id=$combat_id AND ready < 2 AND side=1" );
		$arr = f_MFetch( $res );
		if( !$arr ) return false;
		include_once( "create_combat.php" );
		if( !ccAttackPlayer( $player->player_id, $arr[0], 0 ) )
			return false;
		f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( $combat_id, '<b>{$player->login}</b> вмешивается в бой<br>' )" );
		f_MQuery( "UPDATE combat_players SET win_action=6 WHERE player_id={$player->player_id}" );
	}

	return true;
}

function ta_output( $type, $txt, $def, $bad, $early )
{
	global $combat_last_error;
	$mode = ta_check( $type );
    if( $mode == 1 )
    {
    	if( isset( $_GET['assault'] ) && $_GET['assault'] == $type )
    	{
    		if( ta_attack( $type ) )
    			die( "<script>location.href='combat.php';</script>" );
    		else echo "<font color=darkred>$combat_last_error</font><br>";
    	}
    	echo "<li><a href=game.php?assault=$type>Напасть на $txt!</a>";
    	$res = f_MQuery( "SELECT combat_id FROM ta_combats WHERE type=$type" );
    	$arr = f_MFetch( $res );
    	if( $arr[0] ) echo " (<a href=combat_log.php?id=$arr[0] target=_blank>смотреть бой</a>)";
    }
    else if( $mode == 2 ) echo "<li><small><i><font color=green>$def</font></i></small>";
    else if( $mode == 3 ) echo "<li><small><i>$early</i></small>";
    else if( $mode == 0 ) echo "<li><small><i><font color=darkred>$bad</font></i></small>";
}

?>
