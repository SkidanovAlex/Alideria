�������� ������ �� ��������� ����� ��������.<br />
<br />
�������: <input type="text" id="penaltyUsername" /> <input type="button" value="�����������" onclick="penalty( )" /><br />
��������: <input type="text" id="penaltySum" /><br />
<br />
<div id="ajaxAnswer"></div>
<script>
	var $ajaxAnswer = $( '#ajaxAnswer' );

	function penalty( )
	{
		$ajaxAnswer.html( '<i>�������...</i>' );

		$.ajax
		({
			type: 'POST',
			url: 'ajaxQuery.php?service=<?=$serviceIdentity?>',
			data: 'penaltyUsername=' + $( '#penaltyUsername' ).val( ) + '&penaltySum=' + $( '#penaltySum' ).val( ),
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