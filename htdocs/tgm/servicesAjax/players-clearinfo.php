<?
/* @author = undefined
 * @date = 17 февраля 2011
 * @about = Оснастка для очистки личной инфы игрокам
 */

	// Получаем логин персонажа
	$playerLogin = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['playerLogin'] ) );

   // Забираем айди такого игрока из БД
	$playerId = f_MValue( 'SELECT player_id FROM characters WHERE login = "'.$playerLogin.'"' );
	
	// Проверяем, есть ли такой персонаж
	if( !$playerId )
	{
		// Нету
		echo '<span style="color: darkred; font-weight: bold;">Нет такого персонажа</span>';
	}
	else
	{
		// Есть	
		f_MQuery( 'UPDATE player_profile SET descr = "" WHERE player_id = '.$playerId );
		
		echo '<span style="color: green; font-weight: bold;">Готово! <a href="/player_info.php?nick='.$playerLogin.'" target="_blank">(убедиться)</a></span>';
	}
?>