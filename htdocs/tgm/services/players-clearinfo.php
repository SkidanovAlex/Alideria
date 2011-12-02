<?
/* @author = undefined
 * @date = 17 февраля 2011
 * @about = Стирает личную инфу персонажу или персонажам
 */
?>
Очищает личную информацию пользователя.<br />
<br />
Никнейм: <input type="text" id="username" size="10" /> <input type="button" value="Очистить" onclick="clearInfo( )" /><br />
<br />
<div id="ajaxAnswer"></div>
<script>
	var $ajaxResult = $( '#ajaxAnswer' );
	
	function clearInfo( )
	{
		var username = $( '#username' ).val( );
		if( !username )
		{
			alert( 'Укажи никнейм очищаемого' );
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
		$ajaxResult.html( '<span style="color: darkred; font-weight: bold;">Хьюстон, у нас проблема: ' + Answer.statusText + ' [' + Answer.status + ']' + '</span>' );
	}
</script>