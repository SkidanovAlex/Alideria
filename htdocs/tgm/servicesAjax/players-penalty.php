<?
/* @author = undefined
 * @about = Оснастка для перемещения игроков
*/

	// Получаем логин персонажа
	$playerLogin = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['penaltyUsername'] ) );

	// Получаем идентификатор игрока
	$playerIdQuery = f_MQuery( 'SELECT player_id FROM characters WHERE login = "'.$playerLogin.'"' );
	$playerId = f_MFetch( $playerIdQuery );
	$playerId = $playerId['player_id'];
	
	// Проверяем идентификатор игрока
	if( !$playerId )
	{
		echo '<span style="color: darkred; font-weight: bold;">Персонаж не обнаружен</span>';
	}
	else
	{
		// Создаём экземпляр Игрока
		$Player = new Player( $playerId );
		$penaltySum = (int)$_POST['penaltySum'];
		
		// Отбираем указанную сумму денег
		f_MQuery( 'UPDATE characters SET money = money - '.$penaltySum.' WHERE player_id = '.$Player->player_id );
		// Отмечаем отобранное в логе
		$Player->AddToLog( 0, $penaltySum * -1, 1001, $Demiurg->player_id );
		$Player->syst2( 'Вас оштрафовали на <b>'.$penaltySum.'</b> '.my_word_str( $penaltySum, 'дублон', 'дублона', 'дублонов' ).'.' );
		
		echo '<span style="color: green; font-weigth: bold;">Персонаж '.$Player->login.' оштрафован на '.$penaltySum.' '.my_word_str( $penaltySum, 'дублон', 'дублона', 'дублонов' );
		echo '<br />';
		echo 'Теперь у него '.( $Player->money - $penaltySum ).' '.my_word_str( ( $Player->money - $penaltySum ), 'дублон', 'дублона', 'дублонов' ).'</span>';
	}
?>