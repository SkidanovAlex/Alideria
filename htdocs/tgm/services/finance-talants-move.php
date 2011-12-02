<?
/* @author = undefined
 * @date = 25 ������� 2011
 * @about = ���������� �����
 */
 
	// �������� �������
	$monthNames = array( '', '������', '�������', '�����', '������', '���', '����', '����', '�������', '��������', '�������', '������', '�������' );
	
	// ������� ��������� ����� �� ��������
	$cause = array( 0 => '������ <a href="/admin86006609098moo/phrase_editor.php?id=%" target="_blank">����� %</a>',
						 2 => '������ � ���������� <a href="/player_info.php?id=%" target="_blank">%</a>',
						 6 => '������ � ��������� %',
						 8 => '������� �� ������ <a href="/player_info.php?nick=%" target="_blank">%</a>',
						 10 => '������� �� ���������� ��� <a href="/combat_log.php?id=%" target="_blank">%</a>',
						 17 => '������ ���� � ��� %',
						 20 => '������� ������� � NPC %',
						 21 => '������� ������ ����� (%)',
						 22 => '����� ����� %',
						 25 => '������� �������� <a href="/player_info.php?id=%" target="_blank">%</a>',
						 30 => '������� �������� ��������',
						 1001 => '������� �� ������� �������� <a href="/player_info.php?id=%" target="_blank">%</a>',
						 1002 => '������� ����� ���� <b>(%)</b>',
						 1003 => '������� � ����������� ���������� <a href="/help.php?id=1010&item_id=%" target="_blank">��� ����� �����</a>'
						);

	// �������� ��������, � ������� ��� ���������� ���������� �������
	$fromDay = mktime( 0, 0, 0, ( $_GET['month'] ) ? $_GET['month'] : date( 'n' ), 1, ( $_GET['year'] ) ? $_GET['year'] : date( 'Y' ) ); // ������ ��������
	$toDay = mktime( 23, 59, 59, ( $_GET['month'] ) ? $_GET['month'] : date( 'n' ), date( 't', $fromDay ), ( $_GET['year'] ) ? $_GET['year'] : date( 'Y' ) );  // ������ ��������
	
	// � - �������� ���� ������� � ������������ ��� ���������
	$allDeals = array( );	
	$allDealsQuery = f_MQuery( 'SELECT time, player_id, ( have - had ) AS money, arg1, type FROM player_log WHERE item_id = -1 AND time > '.$fromDay.' AND time < '.$toDay.' AND player_id!=6825 AND player_id!=868239 AND player_id!=67573 AND player_id!=1835898 ORDER BY time DESC' );
	while( $deal = f_MFetch( $allDealsQuery ) )
	{
		$allDeals[] = $deal;
	}
	
	$dealsCount = count( $allDeals );

	// ��������� �������� �����
	$report = '<table cellpadding="3" cellspacing="0" style="width: 100%;"><tbody>';
	$dayReport = '';
	$reportMonth = $monthNames[date( 'n', $allDeals[0]['time'] )];
	$reportDay = date( 'd', $allDeals[0]['time'] );
	for( $i = 0; $i < $dealsCount + 1 && $dealsCount != 0; ++ $i )
	{
		// ���� ���������� ����� ����, ������� ����� ������ ���
		if( $i == $dealsCount or $reportDay != date( 'd', $allDeals[$i]['time'] ) )
		{
			// ������� ����� �������� ������ � ��������������� ��� ������
			$report .= '<tr class="title"><td>'.$reportDay.' '.$reportMonth.'</td><td></td><td><img src="/images/umoney.gif" alt="[�������]" /></td><td>�������</td><td></td></tr>';
			$report .= $dayReport;
			
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
		$Player = new Player( $allDeals[$i]['player_id'] );
		
		$dayReport .= '<tr><td style="width: 75px;">'.$time.'</td><td style="width: 230px;"><script>document.write( '.$Player->Nick( ).' )</script></td><td style="width: 50px;"><img src="/images/umoney.gif" alt="[�������]" /> '.$allDeals[$i]['money'].'</td><td>['.$allDeals[$i][type].'] '.str_replace( '%', $allDeals[$i]['arg1'], $cause[$allDeals[$i]['type']] ).'</td></tr>';
	}
			
	$report .= '</tbody></table>';
?>
<script src="/js/ii_a.js"></script>
<script src="/js/clans.php"></script>
<table style="width: 100%;">
	<tr>
		<td>
			<h1>���� ��������. ����� ����, ����� ����� ������ ������/����/�������������</h1>
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