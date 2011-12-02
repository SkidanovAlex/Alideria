<?
/* @author = undefined
 * @version = 0.0.0.1
 * @date = 12 февраля 2011
 * @about = Оснастка для освобождения игроков
*/
?>
Освобождает игрока от занятий и снимает метку о разговоре с NPC.<br />
<br />
Никнейм: <input type="text" id="freedomUsername" /> <input type="button" value="Освободить" onclick="freedom( )" /> <a href="javascript://" onclick="$( '#freedomWarning' ).toggle( )">Варнинг!</a><br />
<br />
<div id="freedomWarning" style="font-size: 12px; font-style: italic; display: none;">
	Использовать с осторожностью! Только для тех ситуаций, когда игрок действительно застрял. Иначе это может привести к ужасающим последствиям и прорыву Инферно.
	<br />
</div>
<div id="freedomAnswer"></div>
<script>
	var $freedomAnswer = $( '#freedomAnswer' );

	function freedom( )
	{
		$freedomAnswer.html( '<i>Пробуем...</i>' );

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
		$freedomAnswer.html( '<span style="color: darkred; font-weight: bold;">Хьюстон, у нас проблема: ' + Answer.statusText + ' [' + Answer.status + ']' + '</span>' );
	}
</script>