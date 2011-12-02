<?
/* @author = undefined
 * @date = 16 февраля 2011
 * @about = Начисление премиумов всем игрокам 
 */
?>
Одаривает эффектами и медалями.
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
				UIN:
			</td>
			<td>
				<input type="text" id="effectUin" value="0" />
				<span style="font-size: 10px; font-style: italic;">Подробности в <a href="/forum.php?post=84871&f=0&page=0" target="_blank">этой теме</a></span>
			</td>
		</tr>		
		<tr>
			<td>
				Тип:
			</td>
			<td>
				<input type="text" id="effectType" value="0" />
				<span style="font-size: 10px; font-style: italic;">0 - эффект, 1 - медаль</span>
			</td>
		</tr>		
		<tr>
			<td>
				На сколько:
			</td>
			<td>
				<input type="text" id="effectDeadline" value="-1" />
				<span style="font-size: 10px; font-style: italic;">Указывать в секундах, начиная с <a href="http://lmgtfy.com/?q=unix+timestamp" target="_blank">3 часов ночи 1 января 1970</a>. По-умолчанию стоит значение бесконечности</span>
			</td>
		</tr>
		<tr>
			<td>
				Название:
			</td>
			<td>
				<input type="text" id="effectName" />
			</td>
		</tr>
		<tr>
			<td>
				Описание:
			</td>
			<td>
				<textarea id="effectText"></textarea>
			</td>
		</tr>
		<tr>
			<td>
				Картинка:
			</td>
			<td>
				<input type="text" id="effectImage" value="smiley.png" />
				<span style="font-size: 10px; font-style: italic;">Нужно указывать имя картинки в папке /images/effects/, включая её расширение.</span>
			</td>
		</tr>
		<tr>
			<td>
				Эффект:
			</td>
			<td>
				<input type="text" id="effect" value="." />
				<span style="font-size: 10px; font-style: italic;">Формат такой же, как у статиков к шмоточкам.</td>
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
			data: 'happyPlayers=' + $( '#happyPlayers' ).val( ) + '&effectDeadline=' + $( '#effectDeadline' ).val( ) + '&effectImage=' + $( '#effectImage' ).val( ) +  '&effectText=' + $( '#effectText' ).val( ) + '&effectType=' + $( '#effectType' ).val( ) + '&effectName=' + $( '#effectName' ).val( ) + '&effect=' + $( '#effect' ).val( ) + '&effectUin=' + $( '#effectUin' ).val( ),
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