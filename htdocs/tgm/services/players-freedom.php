<?
/* @author = undefined
 * @version = 0.0.0.1
 * @date = 12 ������� 2011
 * @about = �������� ��� ������������ �������
*/
?>
����������� ������ �� ������� � ������� ����� � ��������� � NPC.<br />
<br />
�������: <input type="text" id="freedomUsername" /> <input type="button" value="����������" onclick="freedom( )" /> <a href="javascript://" onclick="$( '#freedomWarning' ).toggle( )">�������!</a><br />
<br />
<div id="freedomWarning" style="font-size: 12px; font-style: italic; display: none;">
	������������ � �������������! ������ ��� ��� ��������, ����� ����� ������������� �������. ����� ��� ����� �������� � ��������� ������������ � ������� �������.
	<br />
</div>
<div id="freedomAnswer"></div>
<script>
	var $freedomAnswer = $( '#freedomAnswer' );

	function freedom( )
	{
		$freedomAnswer.html( '<i>�������...</i>' );

		var freedomUsername = $( '#freedomUsername' ).val( );
		$.ajax
		({
			type: 'POST',
			url: 'ajaxQuery.php?service=<?=$serviceIdentity?>',
			data: 'freedomUsername=' + freedomUsername,
			success: freedomResult,
			error: ajaxError
		});
	}
	
	function freedomResult( Answer )
	{
		$freedomAnswer.html( Answer );	
	}
	
	function ajaxError( Answer )
	{
		$freedomAnswer.html( '<span style="color: darkred; font-weight: bold;">�������, � ��� ��������: ' + Answer.statusText + ' [' + Answer.status + ']' + '</span>' );
	}
</script>