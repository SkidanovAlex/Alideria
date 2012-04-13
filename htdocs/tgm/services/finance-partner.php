<?
/* @author = undefined
 * @date = 25 ������� 2011
 * @about = ���������� �����
 */
 
	// �������� �������
	$monthNames = array( '', '������', '�������', '�����', '������', '���', '����', '����', '�������', '��������', '�������', '������', '�������' );
	
	// ������� ��������� ����� �� ��������
	$providers = array( 25 => '������� ��������', 1001 => '������� �� ������� ��������', 0 => '��������� ������ �����' );

	// �������� ��������, � ������� ��� ���������� ���������� �������
	$fromDay = mktime( 0, 0, 0, ( $_GET['month'] ) ? $_GET['month'] : date( 'n' ), 1, ( $_GET['year'] ) ? $_GET['year'] : date( 'Y' ) ); // ������ ��������
	$toDay = mktime( 23, 59, 59, ( $_GET['month'] ) ? $_GET['month'] : date( 'n' ), date( 't', $fromDay ), ( $_GET['year'] ) ? $_GET['year'] : date( 'Y' ) );  // ������ ��������
	
	// � - �������� ���� ������� � ������������ ��� ���������
	$allDeals = array( );	
	// type == 25 -- �������� � ���������� ������ ���� ������ � ���������� ���������� �� ������� ��������
	// type == 1001 -- � ���������� ���������� �� ����� ��������
	$allDealsQuery = f_MQuery( 'SELECT time, player_id, ( have - had ) AS money, arg1, type FROM player_log WHERE item_id = -1 AND have > had AND type > 2 AND time > '.$fromDay.' AND time < '.$toDay.' AND ( type = 25 or type = 1001 ) ORDER BY time DESC' );
	while( $deal = f_MFetch( $allDealsQuery ) )
	{
		$allDeals[] = $deal;
	}
	
	// ����� �����
	$talantsSum = 0;
	$dealsCount = count( $allDeals );

	// ��������� �������� �����
	$report = '<table cellpadding="3" cellspacing="0" style="width: 100%;"><tbody>';
	$dayReport = '';
	$talantsDaySum = 0;
	$providersTalantsSum = array( ); 
	$reportMonth = $monthNames[date( 'n', $allDeals[0]['time'] )];
	$reportDay = date( 'd', $allDeals[0]['time'] );
	for( $i = 0; $i < $dealsCount + 1 && $dealsCount != 0; ++ $i )
	{
		// ���� ���������� ����� ����, ������� ����� ������ ���
		if( $i == $dealsCount or $reportDay != date( 'd', $allDeals[$i]['time'] ) )
		{
			// ������� ����� �������� ������ � ��������������� ��� ������
			$report .= '<tr class="title"><td>'.$reportDay.' '.$reportMonth.'</td><td></td><td><img src="/images/umoney.gif" alt="[�������]" /> '.$talantsDaySum.'</td><td>�������</td><td>�������</td><td></td></tr>';
			$report .= $dayReport;
			
			// �������� ����� ��������
			$talantsSum += $talantsDaySum;
			// �������� ������� ��������
			$talantsDaySum = 0;
			// �������� ������� �����
			$reportDay = date( 'd', $allDeals[$i]['time'] ); // ���������� ���� ���������� ������
			$dayReport = '';
			
			// ����� ��� ������. � ��������, �� ����������, �� ����� ����� �����, ��� ��� ��� ���������� ���������� �����. @by = undefined
			if( $i == $dealsCount )
			{
				break;
			}
		}	

		// ���������� ���������� �� ���������� ������
		$time = Date( 'H:i:s', $allDeals[$i]['time'] );
		$Inviter = new Player( $allDeals[$i]['player_id'] );
		$Referal = new Player( ( $allDeals[$i][arg1] ) ? $allDeals[$i][arg1] : 69055 ); // ���� �������� ���, �� ������� ���������
		$talantsDaySum += $allDeals[$i]['money'];
		$providersTalantsSum[$allDeals[$i][type]] += $allDeals[$i]['money'];
		
		$dayReport .= '<tr><td style="width: 75px;">'.$time.'</td><td style="width: 230px;"><script>document.write( '.$Inviter->Nick( ).' )</script></td><td style="width: 50px;"><img src="/images/umoney.gif" alt="[�������]" /> '.$allDeals[$i]['money'].'</td><td><script>document.write( '.$Referal->Nick( ).' )</script></td><td>'.$providers[$allDeals[$i]['type']].'</td></tr>';
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
							// ��������� �� ������������ ����������� �������� �������
							
							arsort( $providersTalantsSum );
							
							// � ������� ��						
							foreach( $providersTalantsSum as $key=>$value )
							{
								echo '<tr><td style="width: 150px;">'.$providers[$key].':</td><td style="width: 50px;"><img src="/images/umoney.gif" /> '.$providersTalantsSum[$key].'</td></tr>';
							}
						?>
						<tr>
							<td colspan="2"><hr /></td>
						</tr>
						<tr>
							<td style="font-weight: bold;">�����:</td>
							<td style="font-weight: bold;"><img src="/images/umoney.gif" alt="[�������]" /> <?=$talantsSum?></td>
						</tr>
						<tr>
							<td>����� ��������:</td><td style="padding-left: 11px;">&nbsp;<?=$dealsCount?></td>
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
						<option value='1'>������</option>
						<option value='2'>�������</option>
						<option value='3'>����</option>
						<option value='4'>������</option>
						<option value='5'>���</option>
						<option value='6'>����</option>
						<option value='7'>����</option>
						<option value='8'>������</option>
						<option value='9'>��������</option>
						<option value='10'>�������</option>
						<option value='11'>������</option>
						<option value='12'>�������</option>
					</select>
					
					<input type="submit" value="��������" />
				</form>
			</td>
		</tr>
	<table>
<br />
<?
	// ������� �������� �����
	echo $report;
?>