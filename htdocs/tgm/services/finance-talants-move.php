<?
/* @author = undefined
 * @date = 25 февраля 2011
 * @about = Финансовый отчёт
 */
 
	// Названия месяцев
	$monthNames = array( '', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );
	
	// Способы получения денег из партнёрки
	$cause = array( 0 => 'Эффект <a href="/admin86006609098moo/phrase_editor.php?id=%" target="_blank">фразы %</a>',
						 2 => 'Сделка с персонажем <a href="/player_info.php?id=%" target="_blank">%</a>',
						 6 => 'Сделка с магазином %',
						 8 => 'Подарок от админа <a href="/player_info.php?nick=%" target="_blank">%</a>',
						 10 => 'Получил за выигранный бой <a href="/combat_log.php?id=%" target="_blank">%</a>',
						 17 => 'Выучил закл в БТЗ %',
						 20 => 'Подарил подарок у NPC %',
						 21 => 'Оплатил услуги Фавна (%)',
						 22 => 'Купил через %',
						 25 => 'Левелап реферала <a href="/player_info.php?id=%" target="_blank">%</a>',
						 30 => 'Покупка форумной аватарки',
						 1001 => 'Процент от платежа реферала <a href="/player_info.php?id=%" target="_blank">%</a>',
						 1002 => 'Быстрая варка рыбы <b>(%)</b>',
						 1003 => 'Покупка в Магазинчике Артефактов <a href="/help.php?id=1010&item_id=%" target="_blank">вот такой штуки</a>'
						);

	// Получаем диапазон, в котором нас интересуют финансовые события
	$fromDay = mktime( 0, 0, 0, ( $_GET['month'] ) ? $_GET['month'] : date( 'n' ), 1, ( $_GET['year'] ) ? $_GET['year'] : date( 'Y' ) ); // Откуда забираем
	$toDay = mktime( 23, 59, 59, ( $_GET['month'] ) ? $_GET['month'] : date( 'n' ), date( 't', $fromDay ), ( $_GET['year'] ) ? $_GET['year'] : date( 'Y' ) );  // Докуда забираем
	
	// И - получаем сами события в интересующем нас диапазоне
	$allDeals = array( );	
	$allDealsQuery = f_MQuery( 'SELECT time, player_id, ( have - had ) AS money, arg1, type FROM player_log WHERE item_id = -1 AND time > '.$fromDay.' AND time < '.$toDay.' AND player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND player_id!=1835898 ORDER BY time DESC' );
	while( $deal = f_MFetch( $allDealsQuery ) )
	{
		$allDeals[] = $deal;
	}
	
	$dealsCount = count( $allDeals );

	// Формируем месячный отчёт
	$report = '<table cellpadding="3" cellspacing="0" style="width: 100%;"><tbody>';
	$dayReport = '';
	$reportMonth = $monthNames[date( 'n', $allDeals[0]['time'] )];
	$reportDay = date( 'd', $allDeals[0]['time'] );
	for( $i = 0; $i < $dealsCount + 1 && $dealsCount != 0; ++ $i )
	{
		// Если начинается новый день, выводим шапку нового дня
		if( $i == $dealsCount or $reportDay != date( 'd', $allDeals[$i]['time'] ) )
		{
			// Выводим шапку дневного отчёта и непосредственно его самого
			$report .= '<tr class="title"><td>'.$reportDay.' '.$reportMonth.'</td><td></td><td><img src="/images/umoney.gif" alt="[Таланты]" /></td><td>Причина</td><td></td></tr>';
			$report .= $dayReport;
			
			// Обнуляем дневной отчёт
			$reportDay = date( 'd', $allDeals[$i]['time'] ); // Запоминаем дату следующего отчёта
			$dayReport = '';
			
			// Такая вот логика. В принципе, не нагруженно, не очень режет глаза, так что для локального применения сойдёт. @by = undefined
			if( $i == $dealsCount )
			{
				break;
			}
		}	

		// Генерируем информацию по конкретной сделке
		$time = Date( 'H:i:s', $allDeals[$i]['time'] );
		$Player = new Player( $allDeals[$i]['player_id'] );
		
		$dayReport .= '<tr><td style="width: 75px;">'.$time.'</td><td style="width: 230px;"><script>document.write( '.$Player->Nick( ).' )</script></td><td style="width: 50px;"><img src="/images/umoney.gif" alt="[Таланты]" /> '.$allDeals[$i]['money'].'</td><td>['.$allDeals[$i][type].'] '.str_replace( '%', $allDeals[$i]['arg1'], $cause[$allDeals[$i]['type']] ).'</td></tr>';
	}
			
	$report .= '</tbody></table>';
?>
<script src="/js/ii_a.js"></script>
<script src="/js/clans.php"></script>
<table style="width: 100%;">
	<tr>
		<td>
			<h1>Пока создаётся. Может быть, здесь будет баланс пришло/ушло/передвинулось</h1>
			</td>
			<td style="vertical-align: top; text-align: right;">
				<form method="GET" id="dataSelector">
					<input type="hidden" name="service" value="<?=$serviceIdentity?>" />
					
					<select name="year">
						<option value='2011'>2011</option>
						<option value='2010'>2010</option>
						<option value='2009'>2009</option>
					</select>
				
					<select name="month">
						<option value='1'>Январь</option>
						<option value='2'>Февраль</option>
						<option value='3'>Март</option>
						<option value='4'>Апрель</option>
						<option value='5'>Май</option>
						<option value='6'>Июнь</option>
						<option value='7'>Июль</option>
						<option value='8'>Август</option>
						<option value='9'>Сентябрь</option>
						<option value='10'>Октябрь</option>
						<option value='11'>Ноябрь</option>
						<option value='12'>Декабрь</option>
					</select>
					
					<input type="submit" value="Показать" />
				</form>
			</td>
		</tr>
	<table>
<br />
<?
	// Выводим месячный отчёт
	echo $report;
?>