<?
/* @author = undefined
 * @about = ���������� ��������� ���� ������� 
 */
?>
����� �����������: <input type="text" id="happyPlayers" /> <i>����� ��������� �������</i><br />
����� ����: <input type="text" id="prolongDays" /><br />
����� �����: <input type="text" id="prolongHours" /><br />
��� ��������:&nbsp;
<select id="premiumType">
	<option value='0'>���</option>
	<option value='1'>������</option>
	<option value='2'>�����</option>
	<option value='3'>������</option>
	<option value='4'>�������</option>
	<option value='5'>�������</option>
</select>
<input type="button" onclick="updatePremiums( )" value="��������" /><br />
<br />
<div id="ajaxResult"></div>
<script>
$ajaxResult = $( '#ajaxResult' );

	function updatePremiums( )
	{
		$ajaxResult.html( '�������...' );
		
		$.ajax
		({
			type: 'POST',
			url: 'ajaxQuery.php?service=<?=$serviceIdentity?>',
			data: 'happyPlayers=' + $( '#happyPlayers' ).val( ) + '&prolongDays=' + $( '#prolongDays' ).val( ) + '&prolongHours' + $( '#prolongHours' ).val( ) +  '&premiumType=' + $( '#premiumType' ).val( ),
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