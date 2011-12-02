Делает указанного персонажа новым Верховным Главой Ордена, в котором указанный персонаж состоит.<br />
Старый глава получает самое низкое звание в Ордене, в последствии новый Глава может его поменять на любое, исходя из внутренних решений Ордена.<br />
<br />
Никнейм: <input type="text" id="clanLeaderUsername" /> <input type="button" value="Сделать Главой" onclick="doClanLeader( )" /><br />
<br />
<div id="ajaxAnswer"></div>
<script>
	var $ajaxAnswer = $( '#ajaxAnswer' );

	function doClanLeader( )
	{
		$ajaxAnswer.html( '<i>Пробуем...</i>' );

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
		$ajaxAnswer.html( '<span style="color: darkred; font-weight: bold;">Хьюстон, у нас проблема: ' + Answer.statusText + ' [' + Answer.status + ']' + '</span>' );
	}
</script>