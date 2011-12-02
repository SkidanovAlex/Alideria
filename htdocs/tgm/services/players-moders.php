<?
/* @author = undefined
 * @date = 17 ������� 2011
 * @about = ������ ���������
 */
 
	// ����������� ������ �� ��
	$moderList = f_MQuery( 'SELECT player_id FROM player_ranks WHERE rank = 2 OR rank = 5' );
?>
<script src="/js/ii_a.js"></script>
<script src="/js/clans.php"></script>
<table>
	<tbody>
	<?
		// ������� ������ ���������
		while( $moder = f_MFetch( $moderList ) )
		{
			// ��������� ����������
			$Moderator = new Player( $moder['player_id'] );
			
			// �������� ������ � ����� ��������� ���������� ����������
			$lastPing = f_MValue( 'SELECT last_ping FROM online WHERE player_id = '.$Moderator->player_id );			
			$time = time( ); // ����� ��� ����������
						
			if( !$lastPing ) // ��������� �������?
			{
				// ��������� �������, ��������, ��������� �����
				$offlineTime = $time - f_MValue( 'SELECT login_time FROM history_logon_logout WHERE player_id='.$Moderator->player_id.' ORDER BY entry_id DESC LIMIT 1' );
				
				$status = '<span style="color: darkred; font-weight: bold;">offline '.my_time_str( $offlineTime ).'</span>';
			}
			else
			{
				// ��������� ������, �������� ����� ������� � ����� ��������� ���������� {����� ��������� ���������� ����� ���������, ����� �������� - �� ������� � ���� ��� ����� �� ����� ����� � ������ ���� ��� �� ����������}
				
				$idle = $time - $lastPing; // ����� �������
				$onlineTime = $time - f_MValue( 'SELECT login_time FROM history_logon_logout WHERE player_id = '.$Moderator->player_id.' ORDER BY entry_id DESC LIMIT 1' );

				$status = '<span style="color: green; font-weight: bold;">online '.my_time_str( $onlineTime ).'</span>';
				// ���� ���� ����� ������� ������ ����� ������
				if( $iddle > 60 )
				{
					$status .= ' <span style="font-style: italic;">{��������� ������: '.my_time_str( $idle ).' �����}</span>';
				}
			}
			echo '<tr><td><input type="checkbox" class="ModeratorSelector" id="'.$Moderator->player_id.'" /></td><td><script>document.write( '.$Moderator->Nick( ).' )</script></td><td>'.$status.'</td></tr>';
		}
	?>
	</tbody>
</table>
<br />
<br />
������: <input type="text" id="taskText" size="70" /> <input type="button" value="��������� ���������" onclick="sendTask( )" /><br />
<br />
<div id="ajaxAnswer"></div>
<script>
	var $ajaxAnswer = $( '#ajaxAnswer' );
	
	function sendTask( )
	{
		// ������������ ������ �������
		var $moderCollection = $( '.ModeratorSelector:checked' );
		var count = $moderCollection.size( );
		
		// ���������, ������ �� ���� ���-������
		if( count == 0 )
		{
			// ������, ����� �� �������
			$ajaxAnswer.html( '<b>������ ����� ������� ���� �� ������ ����������</b>' );		
		}

		// ��������, ��� ���������� � ��������
		$ajaxAnswer.html( '<i>�������...</i>' );
		
		// ��������� ������ �������
		var moderList = [];
		for( var i = 0; i < count; i ++ )
		{
			moderList.push( $moderCollection.eq( i ).attr( 'id' ) );
		}
		moderList = moderList.join( ',' );

		$.ajax
		({
			type: 'POST',
			url: 'ajaxQuery.php?service=<?=$serviceIdentity?>',
			data: 'moderList=' + moderList + '&taskText=' + $( '#taskText' ).val( ),
			success: ajaxResult,
			error: ajaxError
		});
	}
	
	function ajaxResult( Answer )
	{
		$ajaxAnswer.html( Answer );	
	}
	
	function ajaxError( Answer )
	{
		$ajaxAnswer.html( '<span style="color: darkred; font-weight: bold;">�������, � ��� ��������: ' + Answer.statusText + ' [' + Answer.status + ']' + '</span>' );
	}
</script>