<?
/* @author = undefined
 * @version = 1.0.0.1
 * @date = 12 февраля 2011
 * @about = основной контролирующий звездолёт, который в основном контролирует
 */

	// Информация о кодировке нашего многонационального сервера
	Header( 'Content-type: text/html; charset=windows-1251' );

	// Подключение заголовочных файлов
	require_once( '../functions.php' );	// Клоака большинства технических функций игры
	require_once( '../player.php' );		// Душераздирающий класс, призванный выполнять действия над Пользователем

	// Проверка на принадлежность к Сану
	if( !check_cookie( ) )
	{
		die( );	
	}
	else
	{
		$Demiurg = new Player( $_COOKIE['c_id'] );

		if( $Demiurg->Rank( ) != 1 )
		{
			die( );
		}
	}
	
	// Выполнение AJAX-реализации сервиса
	$serviceIdentity = preg_replace( '/[^a-zA-Z0-9_-]+/', '', $_GET['service'] ); // Защита от локальных инклудов
	if( !include_once( 'servicesAjax/'.$serviceIdentity.'.php' ) )
	{
		echo '<span style="color: darkred; font-weight: bold;">Отсутствует AJAX-реализация модуля "'.$serviceIdentity.'"</span>';	
	}
?>