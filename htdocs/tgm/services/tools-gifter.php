<?
/* @author = undefined
 * @date = 16 февраля 2011
 * @about = Начисление премиумов всем игрокам 
 */
?>
Начисляет указанное число секунд подарка всем игрокам.<br />
<table celspacing="0" cellpadding="1">
	<tbody>
		<tr>
			<td style="verical-align: top;">
				Кому:
			</td>
			<td style="vertical-align: top;">
				<input type="text" id="happyPlayers" />
				<span style="font-size: 10px; font-style: italic;">Можно указывать несколько никнеймов игроков, разделяя их запятой без пробела. Чтобы указать всех игроков, вместо никнеймов нужно поставить знак "%" без кавычек</span>
			</td>
		</tr>
		<tr>
			<td>
				На сколько:
			</td>
			<td>
				<input type="text" id="presentDeadline" value="-1" />
				<span style="font-size: 10px; font-style: italic;">Указывать в секундах. По-умолчанию стоит значение бесконечности</span>
			</td>
		</tr>
		<tr>
			<td>
				Текст:
			</td>
			<td>
				<textarea id="presentText"></textarea>
			</td>
		<tr>
			<td>
				Картинка:
			</td>
			<td>
				<input type="text" id="presentImage" />
				<span style="font-size: 10px; font-style: italic;">Нужно указывать имя картинки в папке /images/presents/, включая её расширение.</span>
			</td>
		</tr>
		<tr>
			<td>
				От кого:
			</td>
			<td>
				<input type="text" id="fromWho" value="Демиургов" />
				<span style="font-size: 10px; font-style: italic;">Можно указывать любое имя, подходящее под формат "Подарок от..."</td>
			</td>
		</tr>
	</tbody>
</table>
<input type="button" onclick="gift( )" value="Подарить!" /><br />
<br />
<div id="ajaxResult"></div>
<script>
$ajaxResult = $( '#ajaxResult' );

	function gift( )
	{
		$ajaxResult.html( 'Пробуем...' );
		
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
		$ajaxResult.html( '<span style="color: darkred; font-weight: bold;">Хьюстон, у нас проблема: ' + Answer.statusText + ' [' + Answer.status + ']' + '</span>' );
	}
</script>