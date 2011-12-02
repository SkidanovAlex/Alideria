<?
/* @author = undefined
 * @date = 16 ������� 2011
 * @about = ���������� �� ����� ������� �������, ���� ������ ���
 */
?>
<table>
	<thead>
		<th>�������</th>
		<th>���������</th>
		<th>����� ���������� ������</th>
		<th>����������� ������</th>
		<th>����� ������� � ������</th>
	</thead>
	<tbody>
	<?
		$locationsVisits = f_MQuery( 'SELECT * FROM `location_visits` WHERE `loc` = 2 ORDER BY `visits` DESC' );
		$maxLocationVisits = f_MValue( 'SELECT `visits` FROM `location_visits` ORDER BY `visits` DESC LIMIT 0, 1' ) / 3;
		$minLocationVisits = f_MValue( 'SELECT `visits` FROM `location_visits` ORDER BY `visits` ASC LIMIT 0, 1' );
		$monthName = array( '����������', '������', '�������', '�����', '������', '���', '����', '����', '�������', '��������', '�������', '������', '�������' );
					
		while( $locationVisits = f_MFetch( $locationsVisits ) )
		{
			$depthName = f_MValue( "SELECT `title` FROM `loc_texts` WHERE `loc` = $locationVisits[loc] AND `depth` = $locationVisits[depth]" );
			$visits = $locationVisits[visits];
			$P = round( 100 - ( ( $visits - $minLocationVisits ) / ( $maxLocationVisits - $minLocationVisits + 1 ) ) * 100 );
			$P = ( $P > 0 ) ? "<span style=\"color: darkred; font-weight: bold;\">$P%</span>" : '0%';
			$lastVisitTime = $locationVisits[last_visit_time];
			$lastVisitTime = date( 'H:i', $lastVisitTime ).( ( date( 'd', $lastVisitTime ) != date( 'd' ) or date( 'm', $lastVisitTime ) != date( 'm' ) or date( 'Y', $lastVisitTime) != date( 'Y' ) ) ? ' '.date( 'd', $lastVisitTime ) : '' ).( ( date( 'm', $lastVisitTime ) != date( 'm' ) ) ? ' '.$monthName[date( 'n', $lastVisitTime )] : '' ).( ( date( 'Y', $lastVisitTime ) != date( 'Y' ) ) ? ' '.date( 'Y', $lastVisitTime ).' ����' : '' );
			$maxSham = round( ( time( ) - $locationVisits[last_visit_time] ) / 3600 - 0.5 );
			$maxSham = ( $maxSham < 1 ) ? 0 : $maxSham;

			echo "<tr><td>$depthName</td><td>$visits</td><td>$lastVisitTime</td><td>$P</td><td>$maxSham</td></tr>";
		}
	?>
	</tbody>
</table>