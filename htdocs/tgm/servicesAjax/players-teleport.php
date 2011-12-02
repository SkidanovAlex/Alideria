<?
/* @author = undefined
 * @version = 0.0.0.1
 * @date = 12 февраля 2011
 * @about = Оснастка для перемещения игроков
*/

	// Получаем логин персонажа
	$playerLogin = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['teleportUsername'] ) );

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
		
		// Если игрок не свободен (или разговаривает с мобом?)
		if( $Player->regime != 0 || $Player->till != 0 )
		{
			echo '<span style="color: darkred; font-weight: bold;">Персонаж сейчас занят. Возможно, у него сломан режим.</span>';
		}
		else
		{
			// Если свободен, перемещаемся в указанное место
			$Player->SetLocation( (int)$_POST['teleportLocation'], true );
			$Player->SetDepth( (int)$_POST['teleportDepth'], true );
			
			echo '<span style="color: green; font-weight: bold;">Персонаж доставлен в целости и сохранности!</span>';
		}
	}
?>