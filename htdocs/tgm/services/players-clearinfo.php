<?
/* @author = undefined
 * @date = 17 ������� 2011
 * @about = ������� ������ ���� ��������� ��� ����������
 */
?>
������� ������ ���������� ������������.<br />
<br />
�������: <input type="text" id="username" size="10" /> <input type="button" value="��������" onclick="clearInfo( )" /><br />
<br />
<div id="ajaxAnswer"></div>
<script>
	var $ajaxResult = $( '#ajaxAnswer' );
	
	function clearInfo( )
	{
		var username = $( '#username' ).val( );
		if( !username )
		{
			alert( '����� ������� ����������' );
			return;		
		}
		
		$.ajax
		({
			type: 'POST',
			url: 'ajaxQuery.php?service=<?=$serviceIdentity?>',
			data: 'playerLogin=' + username,
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