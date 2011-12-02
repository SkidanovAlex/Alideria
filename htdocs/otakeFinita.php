<?
	//echo mktime (16,30, 0, date("n"), date("j"), date("Y") );
	
	// Библиотеки подключаем
	require_once( 'functions.php' );
	require_once( 'player.php' );
	require_once( 'mob.php' );
	
	f_MConnect( );	
	
	// Выводим список боёв
	for( $kind = 0; $kind < 3; ++ $kind )
	{
		$combats = f_MQuery( "SELECT combat_id FROM quest_9m WHERE kind = $kind" );
		while( $combat = f_MFetch( $combats ) )
		{
			// Ссылка на бой
			echo '<a href="/combat_log.php?id='.$combat['combat_id'].'" target="_blank">'.$combat['combat_id'].'</a> ';
			
			// Число мобов
			$mobs = f_MValue( 'SELECT COUNT( player_id ) FROM combat_players WHERE ready < 2 AND side=1 AND combat_id='.$combat['combat_id'] );
			// Число игроков
			$players = f_MValue( 'SELECT COUNT( player_id ) FROM combat_players WHERE ready < 2 AND side=0 AND combat_id='.$combat['combat_id'] );
			
			echo '['.$players.' vs '.$mobs.']<br />';

			// Удвоение числа шамахан в бою
			$player = new Player( f_MValue( 'SELECT player_id FROM combat_players WHERE ready < 2 AND side = 1 AND combat_id = '.$combat['combat_id'].' LIMIT 1' ) );
			$num = $mobs * 2;
			for( $i = 0; $i < $num; ++ $i )
			{
				$mob = new Mob;
				$shnames = array( 'Ночная Крыса', 'Мурена', 'Повелитель Медведей', 'Повелительница Волков' );
				$shava = array( 'pp5.png', 'murena.png', 'bear.png', 'wolf.png' );
				$crand = mt_rand( 0, 3 );
				$mob->CreateDungeonMob( $kind * 4 + 5, 7, $kind * 4 + 5, $kind * 4 + 5, $kind * 4 + 5, $player->location, $player->depth, $shnames[$crand], $shava[$crand] );
				$mob->AttackPlayer( $player, 12, 0, false );
			}    	
		}
	}
?>