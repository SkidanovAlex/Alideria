<?
/* @author = undefined
 * @date = 25 февраля 2011
 * @about = Финансовый отчёт
 */
 
	// Названия месяцев
	$monthNames = array( '', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );
	
	// Способы получения денег из партнёрки
	$providers = array( 25 => 'Левелап реферала', 1001 => 'Процент от платежа реферала', 0 => 'Неведомая ёбаная хуйня' );

	// Получаем диапазон, в котором нас интересуют финансовые события
	$fromDay = mktime( 0, 0, 0, ( $_GET['month'] ) ? $_GET['month'] : date( 'n' ), 1, ( $_GET['year'] ) ? $_GET['year'] : date( 'Y' ) ); // Откуда забираем
	$toDay = mktime( 23, 59, 59, ( $_GET['month'] ) ? $_GET['month'] : date( 'n' ), date( 't', $fromDay ), ( $_GET['year'] ) ? $_GET['year'] : date( 'Y' ) );  // Докуда забираем
	
	// И - получаем сами события в интересующем нас диапазоне
	$allDeals = array( );	
	// type == 25 -- включаем в результаты выдачи инфо только о партнёрском начислении за левелап реферала
	// type == 1001 -- о партнёрском начислении за платёж реферала
	$allDealsQuery = f_MQuery( 'SELECT time, player_id, ( have - had ) AS money, arg1, type FROM player_log WHERE item_id = -1 AND have > had AND type > 2 AND time > '.$fromDay.' AND time < '.$toDay.' AND ( type = 25 or type = 1001 ) ORDER BY time DESC' );
	while( $deal = f_MFetch( $allDealsQuery ) )
	{
		$allDeals[] = $deal;
	}
	
	// Общие цифры
	$talantsSum = 0;
	$dealsCount = count( $allDeals );

	// Формируем месячный отчёт
	$report = '<table cellpadding="3" cellspacing="0" style="width: 100%;"><tbody>';
	$dayReport = '';
	$talantsDaySum = 0;
	$providersTalantsSum = array( ); 
	$reportMonth = $monthNames[date( 'n', $allDeals[0]['time'] )];
	$reportDay = date( 'd', $allDeals[0]['time'] );
	for( $i = 0; $i < $dealsCount + 1 && $dealsCount != 0; ++ $i )
	{
		// Если начинается новый день, выводим шапку нового дня
		if( $i == $dealsCount or $reportDay != date( 'd', $allDeals[$i]['time'] ) )
		{
			// Выводим шапку дневного отчёта и непосредственно его самого
			$report .= '<tr class="title"><td>'.$reportDay.' '.$reportMonth.'</td><td></td><td><img src="/images/umoney.gif" alt="[Таланты]" /> '.$talantsDaySum.'</td><td>Реферал</td><td>Причина</td><td></td></tr>';
			$report .= $dayReport;
			
			// Повышаем общие счётчики
			$talantsSum += $talantsDaySum;
			// Обнуляем дневные счётчики
			$talantsDaySum = 0;
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
		$Inviter = new Player( $allDeals[$i]['player_id'] );
		$Referal = new Player( ( $allDeals[$i][arg1] ) ? $allDeals[$i][arg1] : 69055 ); // Если реферала нет, то покажем Глашатого
		$talantsDaySum += $allDeals[$i]['money'];
		$providersTalantsSum[$allDeals[$i][type]] += $allDeals[$i]['money'];
		
		$dayReport .= '<tr><td style="width: 75px;">'.$time.'</td><td style="width: 230px;"><script>document.write( '.$Inviter->Nick( ).' )</script></td><td style="width: 50px;"><img src="/images/umoney.gif" alt="[Таланты]" /> '.$allDeals[$i]['money'].'</td><td><script>document.write( '.$Referal->Nick( ).' )</script></td><td>'.$providers[$allDeals[$i]['type']].'</td></tr>';
	}
			
	$report .= '</tbody></table>';
?>
<script src="/js/ii_a.js"></script>
<script src="/js/clans.php"></script>
<table style="width: 100%;">
	<tr>
		<td>
			<table>
				<tbody>
					<tr>
						<td style="vertical-align: top;">
						<?
							// Сортируем по прибыльности провайдеров денежных средств
							
							arsort( $providersTalantsSum );
							
							// И выводим их						
							foreach( $providersTalantsSum as $key=>$value )
							{
								echo '<tr><td style="width: 150px;">'.$providers[$key].':</td><td style="width: 50px;"><img src="/images/umoney.gif" /> '.$providersTalantsSum[$key].'</td></tr>';
							}
						?>
						<tr>
							<td colspan="2"><hr /></td>
						</tr>
						<tr>
							<td style="font-weight: bold;">Всего:</td>
							<td style="font-weight: bold;"><img src="/images/umoney.gif" alt="[Таланты]" /> <?=$talantsSum?></td>
						</tr>
						<tr>
							<td>Всего платежей:</td><td style="padding-left: 11px;">&nbsp;<?=$dealsCount?></td>
						</tr>
					</tbody>
				</table>
			</td>
			<td style="vertical-align: top; text-align: right;">
				<form method="GET" id="dataSelector">
					<input type="hidden" name="service" value="<?=$serviceIdentity?>" />
					
					<select name="year">
						<option value='2012'>2012</option>
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