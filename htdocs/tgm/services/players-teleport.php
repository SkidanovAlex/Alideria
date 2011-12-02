<?
/* @author = undefined
 * @version = 0.0.0.1
 * @date = 12 февраля 2011
 * @about = Оснастка для перемещения игроков
*/
?>
Перемещает игрока в указанную локацию, если игрок свободен.<br />
По-умолчанию стоят значения для телепортации на Главную Улицу<br />
<br />
Никнейм: <input type="text" id="teleportUsername" /> <input type="button" value="Переместить" onclick="teleportation( )" /><br />
Локация: <input type="text" id="teleportLocation" value="2" size="1" /><br />
Место: <input type="text" id="teleportDepth" value="0" size="4" />
<br />
<div id="teleportAnswer"></div>
<script>
	var $teleportAnswer = $( '#teleportAnswer' );

	function teleportation( )
	{
		$teleportAnswer.html( '<i>Пробуем...</i>' );

		$.ajax
		({
			type: 'POST',
			url: 'ajaxQuery.php?service=<?=$serviceIdentity?>',
			data: 'teleportUsername=' + $( '#teleportUsername' ).val( ) + '&teleportLocation=' + $( '#teleportLocation' ).val( ) + '&teleportDepth=' + $( '#teleportDepth' ).val( ),
			success: teleportationResult,
			error: ajaxError
		});
	}
	
	function teleportationResult( Answer )
	{
		$teleportAnswer.html( Answer );	
	}
	
	function ajaxError( Answer )
	{
		$teleportAnswer.html( '<span style="color: darkred; font-weight: bold;">Хьюстон, у нас проблема: ' + Answer.statusText + ' [' + Answer.status + ']' + '</span>' );
	}
</script>