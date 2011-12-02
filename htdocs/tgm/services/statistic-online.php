<?
/* @author = Ishamael, undefined
 * @version = 1.0.0.1
 * @date = 11 ������� 2011
 * @about = ���������� ������� �������
*/
	// ��������� �������� ������� ������� �� ��������� 24 ����
	$playersOnlineQuery = f_MQuery( 'SELECT * FROM online_graph ORDER BY entry_id DESC limit 50' );
	$playersOnline = array( );
	while( $playersOnline[] = f_MFetch( $playersOnlineQuery ) );
	array_pop( $playersOnline ); // ������-�� � ����� ����������� ������ ������� ���������
	
	// ��������� ������ �������� � ������ ���������� �������� �� �������
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
	
	// ��������� �������� ����� ������������� ������
	$currentOnline = f_MFetch( f_MQuery( 'SELECT COUNT(player_id) FROM online' ) );
	$currentOnline = $currentOnline['COUNT(player_id)'];
	
	// ��������� ���������� ����� ������� �������� � ����� �� �����
	$time = time( );
	$yesterdayCurrentOnline = f_MFetch( f_MQuery( 'SELECT value,timestamp FROM online_graph WHERE timestamp > '.( $time - 1800000 ).' and timestamp < '.( $time + 1800000 ).' LIMIT 1' ) );
	$yesterdayCurrentOnline = $yesterdayCurrentOnline['value'];
?>
����� ������� ������ �� ��������� 24 ����.<br />
<br />
<b>������ ���������:</b> <span style="color: green;"><?=$maxOnline['value']?></span> � <?=Date( 'H:i', $maxOnline['timestamp'] )?><br />
<b>������ ���������:</b> <span style="color: darkred;"><?=$minOnline['value']?></span> � <?=Date( 'H:i', $minOnline['timestamp'] )?></span><br />
<b>������ ������:</b> <span style="color: darkblue"><?=$currentOnline?></span>, ����� �������������� � ��� �� ����� ���� <span style="color: darkblue;"><?=$yesterdayCurrentOnline?></span><br />
<br />
<a href="javascript://" onclick="$( '#moreOnlineStatistic' ).toggle( )">���������</a><br />
<div id="moreOnlineStatistic" style="display: none;">
	<hr />
	<table style="border: 0px;" id="onlineStatisticTable">
		<thead>
			<th style="font-weight: bold;">�����</th>
			<th style="font-weight: bold;">������</th>
			</tr>
		</thead>
		<tbody>
<?
	// ����� ��������� ����������
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