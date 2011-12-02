<?
/* @author = undefined
 * @about = Статистика регистраций
 */
 
	// Названия месяцев
	$monthNames = array( '', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );
 
	// Получаем диапазон, в котором нас интересуют финансовые события
	$fromDay = mktime( 0, 0, 0, ( $_GET['month'] ) ? $_GET['month'] : date( 'n' ), 1, ( $_GET['year'] ) ? $_GET['year'] : date( 'Y' ) ); // Откуда забираем
	$toDay = mktime( 23, 59, 59, ( $_GET['month'] ) ? $_GET['month'] : date( 'n' ), date( 't', $fromDay ), ( $_GET['year'] ) ? $_GET['year'] : date( 'Y' ) );  // Докуда забираем
	
	// И - получаем сами события в интересующем нас диапазоне
	$allRegs = array( );	
	// type != 25 -- убирает из результатов выдачи инфо о партнёрском начислении
	$allRegsQuery = f_MQuery( 'SELECT player_id, level, clan_id, sex, regdate FROM characters WHERE regdate > '.$fromDay.' AND regdate < '.$toDay.' ORDER BY player_id DESC' );
	while( $reg = f_MFetch( $allRegsQuery ) )
	{
		$allRegs[] = $reg;
	}
	
	// Общие цифры
	$inClan = 0;							// Счётчик клановых персов
	$levelUps = 0;		               // Число левелапов в месяц
	$refsCount = 0;                  // Число игроков приглашённых игроками, зарегистрировавшимися в этом месяце
	$regsCount = count( $allRegs );  // Счётчик персов всего

	// Формируем месячный отчёт
	$report = '<table cellpadding="3" cellspacing="0" style="width: 100%;"><tbody>';
	$dayReport = '';
	$dayLevelUps = 0;	// Число апов на 2 и выше
	$dayRegs = 0;		// Число зареганных за день
	$reportMonth = $monthNames[date( 'n', $allRegs[0]['regdate'] )];
	$reportDay = date( 'd', $allRegs[0]['regdate'] );
	for( $i = 0; $i < $regsCount + 1 && $regsCount != 0; ++ $i )
	{
		// Если начинается новый день, выводим шапку нового дня
		if( $i == $regsCount or $reportDay != date( 'd', $allRegs[$i]['regdate'] ) )
		{
			// Выводим шапку дневного отчёта и непосредственно его самого
			$report .= '<tr class="title"><td>'.$reportDay.' '.$reportMonth.'</td><td>'.$dayRegs.' '.my_word_str( $dayRegs, 'регистрация', 'регистраций', 'регистраций' ).'</td><td>Пригласил</td><td>Зарегистрирован по приглашению</td></tr>';
			$report .= $dayReport;
			
			// Повышаем общие счётчики
			$levelUps += $dayLevelUps;
			// Обнуляем дневные счётчики
			$dayRegs = 0;
			$dayLevelUps = 0;
			// Обнуляем дневной отчёт
			$reportDay = date( 'd', $allRegs[$i]['regdate'] ); // Запоминаем дату следующего отчёта
			$dayReport = '';
			
			// Такая вот логика. В принципе, не нагруженно, не очень режет глаза, так что для локального применения сойдёт. @by = undefined
			if( $i == $regsCount )
			{
				break;
			}
		}	

		// Генерируем информацию по конкретной сделке
		$time = Date( 'H:i:s', $allRegs[$i]['regdate'] );
		$Player = new Player( $allRegs[$i]['player_id'] );
		$dayLevelUps += ( $allRegs[$i]['level'] > 1 ) ? 1 : 0;
		$dayRegs ++;
		$inClan += ( $allRegs[$i]['clan_id'] ) ? 1 : 0;
		$refsCount += ( $thisInvites = f_MValue( 'SELECT COUNT(*) FROM `player_invitations` WHERE `ref_id` = '.$allRegs[$i]['player_id'] ) ) ? $thisInvites : 0;
		$inviterId = f_MValue( 'SELECT ref_id FROM player_invitations WHERE player_id = '.$allRegs[$i][player_id] );
		$Inviter = ( $inviterId ) ? new Player( $inviterId ) : false;
		
		$dayReport .= '<tr'.( ( $allRegs[$i]['level'] > 1 ) ? ' style="background: rgb(245, 222, 179);"' : '' ).'><td style="width: 75px;">'.$time.'</td><td style="width: 230px;"><script>document.write( '.$Player->Nick( ).' )</script><a href="/player_control.php?nick='.$Player->login.'" target="_blank" title="Контроль Персонажа '.$Player->login.'"><img src="/images/c.gif" style="width: 11px; height: 11px; border: 0px;"></a></td><td>'.( ( $thisInvites ) ? '<span style="font-weight: bold; color: #FF0000;">'.$thisInvites.'</span>' : 0 ).'</td><td>'.( ( $Inviter !== false ) ? '<script>document.write( '.$Inviter->Nick( ).' )</script>' : '' ).'</td></tr>';
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
							<td>Вступило в Орден:</td><td style="text-align: right;"><?=$inClan?></td>
						</tr>
						<tr>
							<td>Зарегистрировавшиеся пригласили:</td><td style="text-align: right;"><?=$refsCount?></td>
						</tr>
						<tr>
							<td colspan="2"><hr /></td>
						</tr>
						<tr>
							<td style="font-weight: bold;">Всего апнувшихся:</td><td style="font-weight: bold; text-align: right;"><?=$levelUps?></td>
						</tr>
						<tr>
							<td>Всего регистраций:</td><td style="text-align: right;"><?=$regsCount?></td>
						</tr>
					</tbody>
				</table>
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