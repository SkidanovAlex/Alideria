<?
/* @author = Ishamael, undefined
 * @version = 1.0.0.1
 * @date = 11 февраля 2011
 * @about = Статистика онлайна игроков
*/
	// Получение значений онлайна игроков за последние 24 часа
	$playersOnlineQuery = f_MQuery( 'SELECT * FROM online_graph ORDER BY entry_id DESC limit 50' );
	$playersOnline = array( );
	while( $playersOnline[] = f_MFetch( $playersOnlineQuery ) );
	array_pop( $playersOnline ); // Почему-то в конец добавляется лишний нулевой экземпляр
	
	// Получение самого большого и самого маленького значения из онлайна
	$minOnline = array( value => $playersOnline[0]['value'] );
	$maxOnline = array( value => $playersOnline[0]['value'] );
	$count = count( $playersOnline );
	for( $i = 0; $i < $count; ++ $i )
	{
		if( $maxOnline['value'] < $playersOnline[$i]['value'] )
		{
			$maxOnline = $playersOnline[$i];
		}
		elseif( $minOnline['value'] > $playersOnline[$i]['value'] )
		{
			$minOnline = $playersOnline[$i];
		}
	}
	
	// Получение текущего числа пользователей онлайн
	$currentOnline = f_MFetch( f_MQuery( 'SELECT COUNT(player_id) FROM online' ) );
	$currentOnline = $currentOnline['COUNT(player_id)'];
	
	// Получение вчерашнего числа онлайна примерно в такое же время
	$time = time( );
	$yesterdayCurrentOnline = f_MFetch( f_MQuery( 'SELECT value,timestamp FROM online_graph WHERE timestamp > '.( $time - 1800000 ).' and timestamp < '.( $time + 1800000 ).' LIMIT 1' ) );
	$yesterdayCurrentOnline = $yesterdayCurrentOnline['value'];
?>
Число игроков онлайн за последние 24 часа.<br />
<br />
<b>Лучший результат:</b> <span style="color: green;"><?=$maxOnline['value']?></span> в <?=Date( 'H:i', $maxOnline['timestamp'] )?><br />
<b>Худший результат:</b> <span style="color: darkred;"><?=$minOnline['value']?></span> в <?=Date( 'H:i', $minOnline['timestamp'] )?></span><br />
<b>Сейчас онлайн:</b> <span style="color: darkblue"><?=$currentOnline?></span>, вчера приблизительно в это же время было <span style="color: darkblue;"><?=$yesterdayCurrentOnline?></span><br />
<br />
<a href="javascript://" onclick="$( '#moreOnlineStatistic' ).toggle( )">Подробнее</a><br />
<div id="moreOnlineStatistic" style="display: none;">
	<hr />
	<table style="border: 0px;" id="onlineStatisticTable">
		<thead>
			<th style="font-weight: bold;">Время</th>
			<th style="font-weight: bold;">Онлайн</th>
			</tr>
		</thead>
		<tbody>
<?
	// Вывод подробной статистики
	foreach( $playersOnline as $online )
	{
		$value = $online['value'];
		$timestamp = $online['timestamp'];
		
		if( $value > 150 )
		{
			$color = 'green';
		}
		elseif( $value < 75 )
		{
			$color = 'darkred';
		}
		else
		{
			$color = 'black';		
		}
		
		echo '<tr><td>'.date( '[H:i]', $timestamp ).'</td><td style="color: '.$color.'; font-weight: bold; text-align: right;">'.$value.'</td></tr>';
	}
?>
		</tbody>
	</table>
</div>