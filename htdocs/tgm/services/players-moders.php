<?
/* @author = undefined
 * @date = 17 февраля 2011
 * @about = Список модерации
 */
 
	// Запрашиваем список из БД
	$moderList = f_MQuery( 'SELECT player_id FROM player_ranks WHERE rank = 2 OR rank = 5' );
?>
<script src="/js/ii_a.js"></script>
<script src="/js/clans.php"></script>
<table>
	<tbody>
	<?
		// Выводим список модерации
		while( $moder = f_MFetch( $moderList ) )
		{
			// Экземпляр модератора
			$Moderator = new Player( $moder['player_id'] );
			
			// Получаем статус и время последней активности модератора
			$lastPing = f_MValue( 'SELECT last_ping FROM online WHERE player_id = '.$Moderator->player_id );			
			$time = time( ); // Часто где пригодится
						
			if( !$lastPing ) // Модератор оффлайн?
			{
				// Модератор оффлайн, получаем, насколько долго
				$offlineTime = $time - f_MValue( 'SELECT login_time FROM history_logon_logout WHERE player_id='.$Moderator->player_id.' ORDER BY entry_id DESC LIMIT 1' );
				
				$status = '<span style="color: darkred; font-weight: bold;">offline '.my_time_str( $offlineTime ).'</span>';
			}
			else
			{
				// Модератор онлайн, получаем время онлайна и время последней активности {время последней активности можно учитывать, чтобы понимать - он реально в сети или вышел не через выход и сессия пока что не сбросилась}
				
				$idle = $time - $lastPing; // Время простоя
				$onlineTime = $time - f_MValue( 'SELECT login_time FROM history_logon_logout WHERE player_id = '.$Moderator->player_id.' ORDER BY entry_id DESC LIMIT 1' );

				$status = '<span style="color: green; font-weight: bold;">online '.my_time_str( $onlineTime ).'</span>';
				// Если есть время простоя больше одной минуты
				if( $iddle > 60 )
				{
					$status .= ' <span style="font-style: italic;">{Последний отклик: '.my_time_str( $idle ).' назад}</span>';
				}
			}
			echo '<tr><td><input type="checkbox" class="ModeratorSelector" id="'.$Moderator->player_id.'" /></td><td><script>document.write( '.$Moderator->Nick( ).' )</script></td><td>'.$status.'</td></tr>';
		}
	?>
	</tbody>
</table>
<br />
<br />
Задача: <input type="text" id="taskText" size="70" /> <input type="button" value="Разослать выбранным" onclick="sendTask( )" /><br />
<br />
<div id="ajaxAnswer"></div>
<script>
	var $ajaxAnswer = $( '#ajaxAnswer' );
	
	function sendTask( )
	{
		// Формирование строки запроса
		var $moderCollection = $( '.ModeratorSelector:checked' );
		var count = $moderCollection.size( );
		
		// Проверяем, выбран ли хоть кто-нибудь
		if( count == 0 )
		{
			// Значит, никто не отмечен
			$ajaxAnswer.html( '<b>Сперва нужно выбрать хотя бы одного модератора</b>' );		
		}

		// Отмечаем, что приступили к рассылке
		$ajaxAnswer.html( '<i>Пробуем...</i>' );
		
		// Формируем строку запроса
		var moderList = [];
		for( var i = 0; i < count; i ++ )
		{
			moderList.push( $moderCollection.eq( i ).attr( 'id' ) );
		}
		moderList = moderList.join( ',' );

		$.ajax
		({
			type: 'POST',
			url: 'ajaxQuery.php?service=<?=$serviceIdentity?>',
			data: 'moderList=' + moderList + '&taskText=' + $( '#taskText' ).val( ),
			success: ajaxResult,
			error: ajaxError
		});
	}
	
	function ajaxResult( Answer )
	{
		$ajaxAnswer.html( Answer );	
	}
	
	function ajaxError( Answer )
	{
		$ajaxAnswer.html( '<span style="color: darkred; font-weight: bold;">Хьюстон, у нас проблема: ' + Answer.statusText + ' [' + Answer.status + ']' + '</span>' );
	}
</script>