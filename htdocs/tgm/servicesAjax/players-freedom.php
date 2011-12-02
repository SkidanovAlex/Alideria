<?
/* @author = undefined
 * @version = 0.0.0.1
 * @date = 12 февраля 2011
 * @about = Оснастка для освобождения игроков (снимает метки о занятости и беседе с мобом)
*/

	// Получаем логин персонажа
	$playerLogin = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['freedomUsername'] ) );

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
		
		// Если игрок занят, снимаем метку о занятости
		if( $Player->regime !=0 )
		{
			$Player->SetRegime( 0 );
		}
		// Если игрок разговаривает с мобом, снимаем метку про разговор
		if( $Player->till != 0 )
		{
			$Player->SetTill( 0 );
		}

		echo '<span style="color: green; font-weight: bold;">Персонаж освобождён!</span>';
	}
?>