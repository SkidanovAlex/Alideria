<?
/*
 * @author = undefined
 * @about = Подарение эффекта или медальки игрокам
 */
 
	$effectName = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['effectName'] ) );
	$effectImage = mysql_real_escape_string( $_POST['effectImage'] );
	$effectDeadline = (int)$_POST['effectDeadline'];
	$effectText = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['effectText'] ) );
	$happyPlayers = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['happyPlayers'] ) );
	$effect = mysql_real_escape_string( $_POST['effect'] );
	$effectType = (int)$_POST['effectType'];
	$effectUin = (int)$_POST['effectUin'];
	
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
		$playersList = f_MQuery( 'SELECT player_id FROM characters WHERE length( pswrddmd5 ) = 32' );
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
		
		$Player = new Player( $playerId );
		
		$Player->AddEffect( $effectUin, $effectType, $effectName, $effectText, $effectImage, $effect, $effectDeadline );
	}

	// Сообщаем об результате
	echo '<span style="color: green; font-weight: bold;">Всё в порядке! Игроки, числом '.$playersCounter.', получили эффект! Или медальку! <s>Или не получили.. Или..</s>:D</span>';
?>