<?

if( !$i_permit ) die( );

// жорик
if( $won && $mob_id == 28 ) checkZhorik( $this, 12, 10 );
if( $won && $mob_id == 7 ) checkZhorik( $this, 11, 10 );
if( $won && $mob_id == 11 ) checkZhorik( $this, 10, 10 );
if( $won ) checkZhorik( $this, 4, 10 );

if( $this->location == 2 && $this->depth == 1 ) checkZhorik( $this, 5, 5 ); // квест жорика бои на арене


// гонка фавна
if( $won )
{
    $convertedId = -1;
    $questRaceIds = array( 37, 24, 18, 10, 25, 38, 7, 35, 8, 29, 31, 32, 11, 13, 26, 30, 28 );
    for( $i = 0; $i < count( $questRaceIds ); ++ $i )
    	if( $questRaceIds[$i] == $mob_id )
    		$convertedId = $i;
    if( $i != -1 )
    {
    	include_once( "quest_race_update_status.php" );
    	updateQuestStatus ( $this->player_id, 2501, 1, 101 + $convertedId );
    }
}


if( $this->HasTrigger( 227 ) )
{
	if( $won )
	{
		$this->SetTrigger( 221, 1 );
		f_MQuery( "DELETE FROM player_talks WHERE player_id={$this->player_id}" );
		$this->SetRegime( 0 );
	}
	else $this->SetTrigger( 226, 1 );
	$this->SetTrigger( 227, 0 );
}

if( $this->HasTrigger( 39 ) && !$this->HasTrigger( 40 ) && $won && $mob_id == 7 )
{
	if( mt_rand( 1, 50 ) <= 15 )
	{
		$this->AddItems( 6125, 1 );
		$this->SetTrigger( 40, 1 );
		$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$this->player_id} AND quest_part_id = 52" );
		if( !mysql_num_rows( $qres ) )
		{
			f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$this->player_id}, 52 )" );
		}
		$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$this->player_id} AND quest_part_id = 53" );
		if( !mysql_num_rows( $qres ) )
		{
			$this->syst( "Информация о квесте <b>Лекарство для Шамахана</b> обновлена." );
			f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$this->player_id}, 53 )" );
		}
		$this->syst( "Эта крыса, впрочем как и все остальные, была легкой добычей. Вам не стоило особого труда загубить грызуна. Да уж, вот ведь повод для гордости - убитая крыса. Кстати, эта очень даже ничего так сохранилась, подойдет для приманки на полоскуху. Теперь самое время подумать о том, как бы сделать ловушку. Вы забираете <a href=help.php?id=1010&item_id=6125 target=_blank>Тушку крысы</a> себе. " );
	}
}

// мейн-квест 2, про прятки и кощея, бой с муреной чтобы найти яйцо
if( $this->HasTrigger( 66 ) && $this->location == 3 && $this->depth == 2 )
{
	if( !$won ) $this->syst2( 'Вот угораздило Вас послушаться Ягайлу. Это все она виновата. Нет чтоб просто на самом деле взять и спрятать иглу где-то на чердаке в городской управе. Нет, яйца мурены ей надо. Конечно, мурена злобная и сильная тварь. Вы оказываетесь у лекаря, не имея ни яйца ни чувства собственного достоинства. Ну да наш девиз: «убили раз, убьют, конечно, и два, но надо пробовать ещё».' );
	else
	{
		$this->syst2( "Ихха! Последний удар и мурена вверх животом проплывает мимо Вас. Получай, поделом тебе, злобная тварь. А Вы разорите её гнездо и заберете яйцо. Да уж, ничего так победка вышла: убили мать, теперь разорите её гнездо и подождете пока Ягайла сделает яичницу из её детей. Какое-то сомнительное геройство получилось…" );
    $this->SetTrigger( 66, 0 );
    $this->SetTrigger( 67, 1 );
		$this->AddItems( 9196, 1 );
	}
}

// квест красавицы Иши
if( $this->HasTrigger( 81 ) && $this->GetQuestValue( 40 ) < 10 && $won && $mob_id == 37 )
{
    $this->AlterQuestValue( 40, 1 );
	$val = 10 - $this->GetQuestValue( 40 );
	if( $val )	
		$this->syst2( "Еще один жук повержен, еще <b>$val</b>, и можно возвращаться к Ише с радостными новостями." );
	else
	{
		$this->syst2( "Все 10 жуков повержены, осталось вернуться к Ише." );
		$this->syst( "Информация о квесте <b>Поэтесса и Жуки</b> обновлена." );
		f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$this->player_id}, 138 )" );
	    $this->SetTrigger( 81, 0 );
	    $this->SetTrigger( 82, 1 );
	}
}

if( $this->HasTrigger( 231 ) && $this->GetQuestValue( 46 ) < 3 && $won && $mob_id == 24 )
{
	$this->AlterQuestValue( 46, 1 );
	if( $this->GetQuestValue( 46 ) == 3 )
	{
		$this->syst2( "Похоже тут ничего нету. Так Вы только всех вампиров перебьете. Нужно начать поиски сначала." );
		$this->SetTrigger( 232 );
		$this->SetTrigger( 231, 0 );
	}
	else $this->syst2( "Еще один вампир повержен." );
}

if( $this->HasTrigger( 240 ) && $this->GetQuestValue( 47 ) < 25 && $won && $mob_id == 26 ||
    $this->HasTrigger( 241 ) && $this->GetQuestValue( 47 ) < 30 && $won && $mob_id == 32 ||
    $this->HasTrigger( 242 ) && $this->GetQuestValue( 47 ) < 35 && $won && $mob_id == 36 )
{
	$this->AlterQuestValue( 47, 1 );
}

?>
