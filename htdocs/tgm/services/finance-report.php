<?
/* @author = undefined
 * @date = 25 февраля 2011
 * @about = Финансовый отчёт
 */
 
	// Источники прибыли
	$providers = array( 0 => 'SMS', 1 => 'WebMoney', 3 => 'RBK Money', 4 => '2-Pay', 173 => 'Администратор Ishamael', 174 => 'Администратор Пламени', 6825 => 'Администратор Reincarnation' );
	// Названия месяцев
	$monthNames = array( '', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );
 
	// Получаем диапазон, в котором нас интересуют финансовые события
	$fromDay = mktime( 0, 0, 0, ( $_GET['month'] ) ? $_GET['month'] : date( 'n' ), 1, ( $_GET['year'] ) ? $_GET['year'] : date( 'Y' ) ); // Откуда забираем
	$toDay = mktime( 23, 59, 59, ( $_GET['month'] ) ? $_GET['month'] : date( 'n' ), date( 't', $fromDay ), ( $_GET['year'] ) ? $_GET['year'] : date( 'Y' ) );  // Докуда забираем
	
	// И - получаем сами события в интересующем нас диапазоне
	$allDeals = array( );	
	// type != 25 -- убирает из результатов выдачи инфо о партнёрском начислении
	$allDealsQuery = f_MQuery( 'SELECT time, player_id, ( have - had ) AS money, arg1 FROM player_log WHERE item_id = -1 AND have > had AND type > 2 AND time > '.$fromDay.' AND time < '.$toDay.' AND type != 25 AND type != 1001 AND type != 1006 AND player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND player_id!=1835898 AND player_id!=3264 ORDER BY time DESC' );
	while( $deal = f_MFetch( $allDealsQuery ) )
	{
		$allDeals[] = $deal;
	}
	
	// Общие цифры
	$talantsSum = 0;
	$dealsCount = count( $allDeals );
	$moneySum = 0;
	$providersMoneySum = array( 0 => 0, 1 => 0, 3 => 0, 4 => 0, 173 => 0, 174 => 0 );
	$providersTalantsSum = array( 0 => 0, 1 => 0, 3 => 0, 4 => 0, 173 => 0, 174 => 0 );

	// Формируем месячный отчёт
	$report = '<table cellpadding="3" cellspacing="0" style="width: 100%;"><tbody>';
	$dayReport = '';
	$talantsDaySum = 0;
	$moneyDaySum = 0;
	$reportMonth = $monthNames[date( 'n', $allDeals[0]['time'] )];
	$reportDay = date( 'd', $allDeals[0]['time'] );
	for( $i = 0; $i < $dealsCount + 1 && $dealsCount != 0; ++ $i )
	{
		// Если начинается новый день, выводим шапку нового дня
		if( $i == $dealsCount or $reportDay != date( 'd', $allDeals[$i]['time'] ) )
		{
			// Выводим шапку дневного отчёта и непосредственно его самого
			$report .= '<tr class="title"><td>'.$reportDay.' '.$reportMonth.'</td><td></td><td><img src="/images/umoney.gif" alt="[Таланты]" /> '.$talantsDaySum.'</td><td>$ '.$moneyDaySum.'</td><td></td></tr>';
			$report .= $dayReport;
			
			// Повышаем общие счётчики
			$moneySum += $moneyDaySum;
			$talantsSum += $talantsDaySum;
			// Обнуляем дневные счётчики
			$talantsDaySum = 0;
			$moneyDaySum = 0;
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
		$Payer = new Player( $allDeals[$i]['player_id'] );
		$money = $allDeals[$i]['money'] * ( ( $allDeals[$i]['arg1'] == 0 ) ? 0.165 : 0.33 );
		$talantsDaySum += $allDeals[$i]['money'];
		$moneyDaySum += $money;
		$providersTalantsSum[$allDeals[$i]['arg1']] += $allDeals[$i]['money'];
		$providersMoneySum[$allDeals[$i]['arg1']] += $money;
		
		$dayReport .= '<tr><td style="width: 75px;">'.$time.'</td><td style="width: 230px;"><script>document.write( '.$Payer->Nick( ).' )</script></td><td style="width: 50px;"><img src="/images/umoney.gif" alt="[Таланты]" /> '.$allDeals[$i]['money'].'</td><td style="width: 50px;">$ '.$money.'</td><td>'.$providers[$allDeals[$i]['arg1']].'</td></tr>';
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
							
							arsort( $providersMoneySum );
							
							// И выводим их						
							foreach( $providersMoneySum as $key=>$value )
							{
								echo '<tr><td style="width: 150px;">'.$providers[$key].':</td><td style="width: 70px;"><img src="/images/umoney.gif" /> '.$providersTalantsSum[$key].'</td><td>$ '.$providersMoneySum[$key].'</td></tr>';
							}
						?>
						<tr>
							<td colspan="3"><hr /></td>
						</tr>
						<tr>
							<td style="font-weight: bold;">Всего:</td>
							<td style="font-weight: bold;"><img src="/images/umoney.gif" alt="[Таланты]" /> <?=$talantsSum?></td>
							<td style="font-weight: bold;">$ <?=$moneySum?></td>
						</tr>
						<tr>
							<td>Всего платежей:</td><td colspan="2" style="text-align: right;"><?=$dealsCount?></td>
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