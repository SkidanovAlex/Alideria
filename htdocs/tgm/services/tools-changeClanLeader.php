������ ���������� ��������� ����� ��������� ������ ������, � ������� ��������� �������� �������.<br />
������ ����� �������� ����� ������ ������ � ������, � ����������� ����� ����� ����� ��� �������� �� �����, ������ �� ���������� ������� ������.<br />
<br />
�������: <input type="text" id="clanLeaderUsername" /> <input type="button" value="������� ������" onclick="doClanLeader( )" /><br />
<br />
<div id="ajaxAnswer"></div>
<script>
	var $ajaxAnswer = $( '#ajaxAnswer' );

	function doClanLeader( )
	{
		$ajaxAnswer.html( '<i>�������...</i>' );

		$.ajax
		({
			type: 'POST',
			url: 'ajaxQuery.php?service=<?=$serviceIdentity?>',
			data: 'clanLeaderUsername=' + $( '#clanLeaderUsername' ).val( ),
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