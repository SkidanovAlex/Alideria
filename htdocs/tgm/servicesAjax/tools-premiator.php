<?
/* @author = undefined
 * @date = 16 февраля 2011
 * @about = Начисление премиумов игрокам 
 */
 
	$presentTime = (int)$_POST['prolongDays'] * 86400 + (int)$_POST['prolongHours'] * 3600;          // Подаренное время премиума
	$premiumType = (int)$_POST['premiumType'];                                                       // Вид подаренного премиума
	$presentDeadline = time( ) + $presentTime;                                                       // Время отключения подарочных премиумов в случае, когда у игрока не было премиума и он не был заморожен. Важно вынести в одну переменную, потому что таких вычислений иначе бы было больше 9000
	$happyPlayers = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['happyPlayers'] ) );  // Те, кого одариваем премиумами
	
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
		
		// Проверяем, есть ли уже премиум такого типа
		if( f_MValue( 'SELECT * FROM premiums WHERE player_id = '.$playerId.' AND premium_id = '.$premiumType ) )
		{
			// Да, такой премиум у персонажа есть, увеличиваем его продолжительность на принятую продолжительность
			f_MQuery( 'UPDATE premiums SET deadline = deadline + '.$presentTime.' WHERE player_id = '.$playerId.' AND premium_id = '.$premiumType ); 
		
		}
		elseif( f_MValue( 'SELECT * FROM frozen_premiums WHERE player_id = '.$playerId.' AND premium_id = '.$premiumType ) )
		{
			// Да, такой премиум у персонажа есть, но он заморожен. Всё равно увеличиваем его на принятую длину
			f_MQuery( 'UPDATE frozen_premiums SET duration = duration + '.$presentTime.' WHERE player_id = '.$playerId.' AND premium_id = '.$premiumType );
		}
		else
		{
			// Нет, у игрока нет премиума такого типа
			
			// Но есть ли у него хоть какие-то замороженные премиумы?
			if( $available = f_MValue( 'SELECT available FROM frozen_premiums WHERE player_id = '.$playerId ) )
			{
				// Да, есть, добавляем подарки в замороженные
				f_MQuery( 'INSERT INTO frozen_premiums( player_id, premium_id, duration, available ) VALUES( '.$playerId.', '.$premiumType.', '.$presentTime.', '.$available[0].' )' );				
			}
			else
			{
				// Нет, нету, добавляем подарки в активные
				f_MQuery( 'INSERT INTO premiums( player_id, premium_id, deadline ) VALUES( '.$playerId.', '.$premiumType.', '.$presentDeadline.' )' );
			}	
		}
	}

	// Сообщаем об результате
	echo '<span style="color: green; font-weight: bold;">Всё в порядке! Игроки, числом '.$playersCounter.', получили премиумов!</span>';
?>