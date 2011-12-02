<?
/* @author = undefined
 * @date = 5 марта 2011
 * @about = Атака Шамахан на Стеклодувных Цех.
 */
 
 	// Если персонаж слишком слаб, не взял квест Дракона или уже после 18.30, войти в Стеклодувный Цех нельзя
 	// и персонажа возвращает опять в Мастерские.
	$player->SetTrigger( 5002, 0 );
	$player->SetDepth( 12, true ); // Телепортим персонажика в Мастерские
	die( '<script>document.location = "/game.php";</script>' );	

	// Просто код, которого уже нет : )	
	$player->SetTrigger( 5002, 1 );
 
 	// Подключаем необходимые библиотеки
 	require_once( 'create_combat.php' );
 	require_once( 'mob.php' );
 	
	// Юзаем код квеста девятого мая
	$kind = 2;
	if( $player->level < 11 ) $kind = 0;
	elseif( $player->level < 16 ) $kind = 1;
	
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
    		
    		// Атака ещё 1-2 мобов
			$combat_id = 0;
			$num = 1 + ( ( mt_rand( 0, 1 ) == 0 ) ? 1 : 0 );
			for( $i = 0; $i < $num; ++ $i )
			{
				$mob = new Mob;
				$shnames = array( "Шамаханин-боец", "Шамаханин-капитан", "Шамаханин-генерал" );
				$shava = array( "sham1.png", "sham2.png", "sham3.png" );
				$mob->CreateDungeonMob( $kind * 4 + 5, 7, $kind * 4 + 5, $kind * 4 + 5, $kind * 4 + 5, $player->location, $player->depth, $shnames[$kind], $shava[$kind] );
				$mob->AttackPlayer( $player, 12, 0, false );
			}    		
		}
	}
	if( $best_id == -1 )
	{
		f_MQuery( "UNLOCK TABLES" );
		$combat_id = 0;
		$num = 3;
		for( $i = 0; $i < $num; ++ $i )
		{
			$mob = new Mob;
				$shnames = array( "Шамаханин-боец", "Шамаханин-капитан", "Шамаханин-генерал" );
				$shava = array( "sham1.png", "sham2.png", "sham3.png" );
			$mob->CreateDungeonMob( $kind * 4 + 5, 7, $kind * 4 + 5, $kind * 4 + 5, $kind * 4 + 5, $player->location, $player->depth, $shnames[$kind], $shava[$kind] );
			$mob->AttackPlayer( $player, 12, 0, false );
			$combat_id = $mob->combat_id;
		}
		setCombatTimeout( $combat_id, 60 );
		f_MQuery( "INSERT INTO quest_9m VALUES ( $combat_id, $kind )" );
	}	
 
 
	// Тормозим выполнение скрипта далее
	die( '<script>document.location = "/combat.php";</script>' );
?>