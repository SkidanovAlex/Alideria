<?

if( !isset( $mid_php ) ) die( );

if( !isset( $external ) ) echo "<b>Логи Магазина Ордена</b> - <a href=game.php?order=shop_control_log>Логи Управления Магазином</a> - <a href=game.php?order=main>Назад</a><br>";

if( 0 == ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_CONTROL_SHOP ) )
{
	echo( "У вас нет прав работать с этим разделом Ордена<br><a href=game.php?order=main>Назад</a>" );
	return;
}

// 13 месяцев
$monthNames = array( 'Андефайнидаря', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );

// Получаем диапазон, в котором нас интересуют финансовые события
$fromDay = mktime( 0, 0, 0, ( $_GET['month'] ) ? $_GET['month'] : date( 'n' ), 1, ( $_GET['year'] ) ? $_GET['year'] : date( 'Y' ) ); // Откуда забираем
$toDay = mktime( 23, 59, 59, ( $_GET['month'] ) ? $_GET['month'] : date( 'n' ), date( 't', $fromDay ), ( $_GET['year'] ) ? $_GET['year'] : date( 'Y' ) );  // Докуда забираем

// Собираем финансовые операции	
$allDeals = array( );
$allDealsQuery = f_MQuery( "SELECT shop_log.* FROM shop_log INNER JOIN shops ON shop_log.shop_id=shops.shop_id WHERE owner_id=$clan_id AND timestamp > $fromDay AND timestamp < $toDay ORDER BY shop_log.entry_id DESC" );
while( $deal = f_MFetch( $allDealsQuery ) )
{
	$allDeals[] = $deal;
}

// А был! Рассказываем..

	$dealsCount = count( $allDeals );

	// Формируем месячный отчёт
	$report = '<table cellpadding="3" cellspacing="0" style="width: 900px;"><tbody>';
	$dayReport = '';
	$reportMonth = $monthNames[date( 'n', $allDeals[0]['timestamp'] )];
	$reportDay = date( 'd', $allDeals[0]['timestamp'] );
	$dayMoneyBalance = 0;
	$dayUMoneyBalance = 0;
	$monthMoneyPlus = 0;
	$montMoneyMinus = 0;
	$monthUMoneyPlus = 0;
	$montUMoneyMinus = 0;
	for( $i = 0; $i < $dealsCount + 1 && $dealsCount != 0; ++ $i )
	{
		// Если начинается новый день, выводим шапку нового дня
		if( $i == $dealsCount or $reportDay != date( 'd', $allDeals[$i]['timestamp'] ) )
		{
			$dayMoneyBalance = ( $dayMoneyBalance > 0 ) ? "<span style=\"color: darkgreen\">+$dayMoneyBalance</span>" : "<span style=\"color: darkred\">$dayMoneyBalance</span>";
			$dayUMoneyBalance = ( $dayUMoneyBalance > 0 ) ? "<span style=\"color: darkgreen\">+$dayUMoneyBalance</span>" : "<span style=\"color: darkred\">$dayUMoneyBalance</span>";
			// Выводим шапку дневного отчёта и непосредственно его самого
			$report .= '<tr class="title"><td>'.$reportDay.' '.$reportMonth.'</td><td></td><td><img src="/images/money.gif" alt="[Дублоны]" /> '.$dayMoneyBalance.'</td><td><img src="/images/umoney.gif" alt="[Таланты]" /> '.$dayUMoneyBalance.'</td><td>Товар</td><td></td></tr>';
			$report .= $dayReport;
			
			// Обнуляем дневной отчёт
			$reportDay = date( 'd', $allDeals[$i]['timestamp'] ); // Запоминаем дату следующего отчёта
			$dayReport = '';
			$dayMoneyBalance = 0;
			$dayUMoneyBalance = 0;
		
			// Такая вот логика. В принципе, не нагруженно, не очень режет глаза, так что для локального применения сойдёт. @by = undefined
			if( $i == $dealsCount )
			{
				break;
			}
		}	
		
		// Запоминаем общую информацию
		$dayMoneyBalance += $allDeals[$i][money];
		$dayUMoneyBalance += $allDeals[$i][umoney];

		// Генерируем информацию по конкретной сделке
		$time = Date( 'H:i:s', $allDeals[$i]['timestamp'] );
		$Player = new Player( $allDeals[$i]['player_id'] );
		$money = ( $allDeals[$i]['money'] > 0 ) ? "<span style=\"color: darkgreen\">+{$allDeals[$i][money]}</span>" : "<span style=\"color: darkred\">{$allDeals[$i][money]}</span>";
		$umoney = ( $allDeals[$i]['umoney'] > 0 ) ? "<span style=\"color: darkgreen\">+{$allDeals[$i][umoney]}</span>" : "<span style=\"color: darkred\">{$allDeals[$i][umoney]}</span>";
		if( $allDeals[$i]['money'] > 0 )
		{
			$monthMoneyPlus += $allDeals[$i]['money'];
		}
		else
		{
			$monthMoneyMinus += $allDeals[$i]['money'];		
		}
		if( $allDeals[$i]['umoney'] > 0 )
		{
			$monthUMoneyPlus += $allDeals[$i]['umoney'];
		}
		else
		{
			$monthUMoneyMinus += $allDeals[$i]['umoney'];
		}
		$itemId = $allDeals[$i][item_id];
		$itemName = f_MValue( 'SELECT `name` FROM `items` WHERE `item_id` = '.$itemId );
			
		$dayReport .= '<tr><td style="width: 75px;">'.$time.'</td><td style="width: 230px;"><script>document.write( '.$Player->Nick( ).' )</script></td><td style="width: 50px;"><img src="/images/money.gif" alt="[Дублоны]" /> '.$money.'</td><td style="width: 50px;"><img src="/images/umoney.gif" alt="[Таланты]" /> '.$umoney.'</td><td>['.abs( $allDeals[$i]['number'] ).'] <a href="/help.php?id=1010&item_id='.$itemId.'" target="_blank">'.$itemName.'</a></td></tr>';
	}
			
	$report .= '</tbody></table>';
?>
<link rel="stylesheet" type="text/css" href="/css/default.css" /> 
<script src="/js/ii_a.js"></script>
<script src="/js/clans.php"></script>
<div style="background: url(/images/chat/chat_bg.gif);">
<table style="width: 100%;">
	<tr>
		<td>
			<table style="width: 200px;">
				<tr>
					<td style="font-weight: bold;">Доход: </td><td style="color: darkgreen; text-align: right;">+<?=$monthMoneyPlus?> <img src="/images/money.gif" title="Дублонов" /></td>
					<td style="font-weight: bold;">Доход: </td><td style="color: darkgreen; text-align: right;">+<?=$monthUMoneyPlus?> <img src="/images/umoney.gif" title="Талантов" /></td>
				</tr>
				<tr>
					<td style="font-weight: bold;">Расход: </td><td style="color: darkred; text-align: right;"><?=$monthMoneyMinus?> <img src="/images/money.gif" title="Дублонов" /></td>
					<td style="font-weight: bold;">Расход: </td><td style="color: darkred; text-align: right;"><?=$monthUMoneyMinus?> <img src="/images/umoney.gif" title="Талантов" /></td>
				</tr>
				<tr>
					<td colspan="2"><hr /></td>
				</tr>
				<tr>
					<td style="font-weight: bold;">Баланс: </td><td style="color: darkblue; text-align: right;"><?=( $monthMoneyPlus + $monthMoneyMinus )?> <img src="/images/money.gif" title="Дублонов" /></td>
					<td style="font-weight: bold;">Баланс: </td><td style="color: darkblue; text-align: right;"><?=( $monthUMoneyPlus + $monthUMoneyMinus )?> <img src="/images/umoney.gif" title="Талантов" /></td>
				</tr>
			</table>
			</td>
			<td style="vertical-align: top; text-align: right;">
				<form method="GET" id="dataSelector">
					<input type="hidden" name="log1" value="1" />
					
					<select name="year">
						<option value='2011'>2011</option>
						<option value='2012'>2012</option>
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
	if( count( $allDeals ) == 0 )
	{
		echo "<i>Логи пусты</i>";
	}
	else
	{
		echo $report;
	}
?>
</div>