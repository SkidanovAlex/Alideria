<?

// при каждом действии, которое потенциально может быть связано с квестами, будет вызываться эта функция
// выставляет выполнившему действие игроку нужные значения quest_value
// проверяет, не победил ли случайно этот игрок
// работает только тогда, когда игрок совершил квестовое действие, а не какое-то другое
// $event_value по умолчанию 1, нужно задавать другие значения для лечения в харчевне и готовки в столовой 
// например, в файле выхода из боя нужно вызвать updateQuestStatus ( 12345, 2501 );
// а в столовой ордена - updateQuestStatus ( 12345, 2513, { 2, 4, 6, 10 - одно из этих чисел в зависимости от кол-ва еды } );
function updateQuestStatus ( $player_id, $event_id, $event_value = 1, $event_detail = -1 )
{
	if( $event_detail == -1 ) $res = f_MQuery( "SELECT * FROM quest_race WHERE race_type=$event_id" );
	else $res = f_MQuery( "SELECT * FROM quest_race WHERE race_type=$event_id AND race_details=$event_detail" );
	if ( mysql_num_rows( $res ) == 0 )
		return false;
	
	// до этого места дойдем только тогда, когда текущее событие является частью квеста
	$plr = new Player( $player_id );
	
	// проверим участвует ли игрок
	if( !$plr->HasTrigger( 262 ) ) return false;
	// проверим надо ли ему еще выполнять это задание
	if( $plr->GetQuestValue( $event_id ) >= f_MValue( "SELECT race_amount FROM quest_race WHERE race_type = $event_id" ) ) return false;
	
	// если игрок только начал выполнение квеста, зададим ему начальное quest_value
	// вероятно, что я фигово уяснил принцип работы GetQuestValue, нужно проверить выполнение этих условий ниже
	if ( $plr->GetQuestValue( $event_id ) == 0 )
	{
		$plr->SetQuestValue( $event_id, $event_value );
	}
	// или прибавим сколько-то к его прогрессу
	else
	{
		$plr->AlterQuestValue( $event_id, $event_value );
	}

	// теперь проверим, каков прогресс игрока после сделанных обновлений
	// если игрок завершил все задания, включим ему обработку победы
	if ( getPlayerProgress( $player_id ) )
	{
		//$winValue = processPlayerWin( $player_id, $plr->level );
		
		$plr->SetTrigger( 260 );
		$plr->syst2( 'Все задания Фавна выполнены. Скорее возвращайся к нему!' );
		
		return 10;
	}
	else $plr->syst2( 'Информация о задании Фавна обновлена!' );

	// 5 -> обычное успешное завершение работы функции, игрок по-прежнему находится в процессе выполнения задания
	return 5;
}

// проверим успехи выбранного игрока
// true - если все задания выполнены
// false - если не все
function getPlayerProgress ( $player_id )
{
	$value = true;
	$plr = new Player( $player_id );
	
	// получаем все типы заданий из текущего квеста
	$res = f_MQuery( "SELECT race_type, race_amount FROM quest_race" );
	$i = 0;
	$raceTypes = array();
	$raceAmounts = array();
	while ( $arr = f_MFetch( $res ) )
	{
		$raceTypes[$i] = $arr['race_type'];
		$raceAmounts[$i] = $arr['race_amount'];
		$i ++;
	}
	
	// для каждого типа проверим процесс выполнения
	for ( $i = 0; $i < count( $raceTypes ); $i ++ )
	{
		// соответственно если хотя бы один из параметров меньше нужного, $value станет false
		if ( $plr->GetQuestValue( $raceTypes[$i] ) < $raceAmounts[$i] )
			$value = false;
	}
	
	return $value;
}



?>
