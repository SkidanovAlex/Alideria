<?

include_once( "mob.php" );

if( !$mid_php ) die( );

if( isset( $_GET['shamahan'] ) )
{
	$kind = 2;
	if( $player->level <= 9 ) $kind = 0;
	else if( $player->level <= 13 ) $kind = 1;
	
	f_MQuery( "LOCK TABLE combats WRITE, combat_players WRITE, quest_9m WRITE" );
	
	$best_val = -3;
	$best_id = -1;
	$res = f_MQuery( "SELECT * FROM quest_9m WHERE kind = $kind" );
	while( $arr = f_MFetch( $res ) )
	{
		if( f_MValue( "SELECT turn_done FROM combats WHERE combat_id = $arr[0]" ) == 1 ) continue;
		$v1 = f_MValue( "SELECT COUNT( player_id ) FROM combat_players WHERE ready < 2 AND side=0 AND combat_id=$arr[0]" );
		$v2 = f_MValue( "SELECT COUNT( player_id ) FROM combat_players WHERE ready < 2 AND side=1 AND combat_id=$arr[0]" );
		if( !$v1 || !$v2 ) continue;
		 
		$v = $v2 - $v1;
		if( $v > $best_val )
		{
			$best_val = $v;
			$best_id = $arr[0];
		}
	}
	
	if( $best_id != -1 )
	{
		$target_id = f_MValue( "SELECT player_id FROM combat_players WHERE ready < 2 AND combat_id={$best_id} AND side=1" );
		if( !$target_id ) $best_id = -1;
		else
		{
    		f_MQuery( "UNLOCK TABLES" );
    		include_once( "create_combat.php" );
    		ccAttackPlayer( $player->player_id, $target_id, 0, false, false );
    		f_MQuery( "UPDATE combat_players SET win_action = 12 WHERE player_id={$player->player_id}" );
		}
	}
	if( $best_id == -1 )
	{
		f_MQuery( "UNLOCK TABLES" );
		$combat_id = 0;
		$num = 10;
		if( $kind == 2 ) $num = 8;
		for( $i = 0; $i < $num; ++ $i )
		{
			$mob = new Mob;
				$shnames = array( "Шамаханин-боец", "Шамаханин-капитан", "Шамаханин-генерал" );
				$shava = array( "sham1.png", "sham1.png", "sham1.png" );
			$mob->CreateDungeonMob( $kind * 4 + 5, 7, $kind * 4 + 5, $kind * 4 + 5, $kind * 4 + 5, $player->location, $player->depth, $shnames[$kind], $shava[$kind] );
			$mob->AttackPlayer( $player, 12, 0, false );
			$combat_id = $mob->combat_id;
		}
		setCombatTimeout( $combat_id, 60 );
		f_MQuery( "INSERT INTO quest_9m VALUES ( $combat_id, $kind )" );
	}
	die( "<script>location.href='combat.php';</script>" );
}

echo "<center><br>";

echo "<big><b>Вы одержали побед над Шамаханцами:</b></big><br>";
echo "<big><big><big><b><font color='darkred'>".($player->GetQuestValue( 50 ))."</font></b></big></big></big><br><br>";
echo "<a href='game.php?shamahan=1'>В атаку!</a><br><br>";

$lvls = "14+";
if( $player->level <= 9 ) $lvls = "5-9";
else if( $player->level <= 13 ) $lvls = "10-13";
echo "<small>Вы попадете в бой с 10 шамаханцами. Все игроки <b>$lvls</b> уровней, идущие в атаку, будут попадать в ваш бой.</small><br>";

echo "</center>";

?>
