<?
/* @author = undefined
 * @version = 0.0.0.1
 * @date = 12 февраля 2011
 * @about = Оснастка для перемещения игроков
*/

	// Получаем логин персонажа
	$playerLogin = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['clanLeaderUsername'] ) );

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
		if( !$Player->clan_id )
		{
			echo '<span style="color: darkred; font-weight: bold;">Персонаж не состоит в Ордене</span>';
		}
		else
		{
			// Если свободен, перемещаемся в указанное место
			f_MQuery( 'UPDATE player_clans SET rank = 0, job = 0 WHERE rank = 1000 AND clan_id = '.$Player->clan_id );
			f_MQuery( 'UPDATE `player_clans` SET `rank` = 1000, `job` = 1000, `control_points` = -1 WHERE player_id = '.$Player->player_id );

			echo '<span style="color: green; font-weight: bold;">'.$Player->login.' новый Глава Ордена!</span>';
		}
	}
?>