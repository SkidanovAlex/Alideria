<?
/* @author = undefined
 * @date = 16 февраля 2011
 * @about = Подарение подарка игрокам
 */
 
	$fromWho = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['fromWho'] ) );                                        // От кого подарок?
	$presentImage = mysql_real_escape_string( $_POST['presentImage'] );                              // Картинка подарка
	$presentDeadline = (int)$_POST['presentDeadline'];                                               // На сколько дарится подарок?
	$presentText = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['presentText'] ) );    // Сопровождающий подарок текст
	$happyPlayers = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['happyPlayers'] ) );  // Те, кого одариваем
	
	$playersCounter = 0; // Просто для статистики

	// Определяем, кому дарим премиумы
	if( strpos( $happyPlayers, ',' ) ) // Если для многих игроков
	{
		// Превращаем перечисление в кусок строки запроса
		$happyPlayers = implode( '" OR login = "', explode( ',', $happyPlayers ) );
		
		// Вынимаем нужных кадриков из базы
		$playersList = f_MQuery( 'SELECT player_id FROM characters WHERE login = "'.$happyPlayers.'"' );
	}
	elseif( $happyPlayers == '%' ) // Если премиумы дарятся всем игрокам
	{
		// Получаем список всех игроков (у мобов вместо хэша пароля стоит цифра, равная их типу)
		$playersList = f_MQuery( 'SELECT player_id FROM characters WHERE length( pswrddmd5 ) = 32 AND sex = 1' );
	}
	else // Прислали никнейм одного счастливчика
	{
		// Получаем этого самого радостного человека
		$playersList = f_MQuery( 'SELECT player_id FROM characters WHERE login = "'.$happyPlayers.'"' );
	}

	// Одариваем премиумами по списку
	while( $playerId = f_MFetch( $playersList ) )
	{
		$playersCounter ++; // Ведём учёт числа обдарованных игроков
		
		$playerId = $playerId['player_id']; // Психоделично, да и необходимо
		
		f_MQuery( 'INSERT INTO player_presents( player_id, img, txt, author, deadline ) VALUES ( '.$playerId.', "'.$presentImage.'", "'.$presentText.'" ,"'.$fromWho.'", '.$presentDeadline.' )' );
	}

	// Сообщаем об результате
	echo '<span style="color: green; font-weight: bold;">Всё в порядке! Игроки, числом '.$playersCounter.', получили подарок!</span>';
?>