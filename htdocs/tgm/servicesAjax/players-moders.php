<?
/* @author = undefined
 * @date = 17 февраля 2011
 * @about = Рассылка задач модерации
*/

	$moderList = explode( ',', $_POST['moderList'] ); // Список идентификаторов модераторов, которым отправляется задача
	$taskText = 'Задача от Демиурга '.$Demiurg->login.'<br /><br />'.mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['taskText'] ) ); // Текст задачи

	// Рассылаем задачи
	$count = count( $moderList );
	for( $i = 0; $i < $count; ++ $i )
	{
		$Moder = new Player( (int)$moderList[$i] );
		
		$Moder->syst2( $taskText );
		$Moder->syst3( $taskText );	
	}
?>
<span style="color: green; font-weight: bold;">Модерация получила свои задачи</span>