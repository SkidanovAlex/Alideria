<?
/* @author = undefined
 * @date = 16 ������� 2011
 * @about = ���������� ��������� ���� ������� 
 */
?>
��������� ��������� ����� ������ ������� ���� �������.<br />
<table celspacing="0" cellpadding="1">
	<tbody>
		<tr>
			<td style="verical-align: top;">
				����:
			</td>
			<td style="vertical-align: top;">
				<input type="text" id="happyPlayers" />
				<span style="font-size: 10px; font-style: italic;">����� ��������� ��������� ��������� �������, �������� �� ������� ��� �������. ����� ������� ���� �������, ������ ��������� ����� ��������� ���� "%" ��� �������</span>
			</td>
		</tr>
		<tr>
			<td>
				�� �������:
			</td>
			<td>
				<input type="text" id="presentDeadline" value="-1" />
				<span style="font-size: 10px; font-style: italic;">��������� � ��������. ��-��������� ����� �������� �������������</span>
			</td>
		</tr>
		<tr>
			<td>
				�����:
			</td>
			<td>
				<textarea id="presentText"></textarea>
			</td>
		<tr>
			<td>
				��������:
			</td>
			<td>
				<input type="text" id="presentImage" />
				<span style="font-size: 10px; font-style: italic;">����� ��������� ��� �������� � ����� /images/presents/, ������� � ����������.</span>
			</td>
		</tr>
		<tr>
			<td>
				�� ����:
			</td>
			<td>
				<input type="text" id="fromWho" value="���������" />
				<span style="font-size: 10px; font-style: italic;">����� ��������� ����� ���, ���������� ��� ������ "������� ��..."</td>
			</td>
		</tr>
	</tbody>
</table>
<input type="button" onclick="gift( )" value="��������!" /><br />
<br />
<div id="ajaxResult"></div>
<script>
$ajaxResult = $( '#ajaxResult' );

	function gift( )
	{
		$ajaxResult.html( '�������...' );
		
		$.ajax
		({
			type: 'POST',
			url: 'ajaxQuery.php?service=<?=$serviceIdentity?>',
			data: 'happyPlayers=' + $( '#happyPlayers' ).val( ) + '&presentDeadline=' + $( '#presentDeadline' ).val( ) + '&presentImage=' + $( '#presentImage' ).val( ) +  '&presentText=' + $( '#presentText' ).val( ) + '&fromWho=' + $( '#fromWho' ).val( ),
			success: ajaxResult,
			error: ajaxError
		});
	}
	
	function ajaxResult( Answer )
	{
		$ajaxResult.html( Answer );	
	}	
	
	function ajaxError( Answer )
	{
		$ajaxResult.html( '<span style="color: darkred; font-weight: bold;">�������, � ��� ��������: ' + Answer.statusText + ' [' + Answer.status + ']' + '</span>' );
	}
</script>