<?
/* @author = undefined
 * @about = Начисление премиумов всем игрокам 
 */
?>
Имена счастливцев: <input type="text" id="happyPlayers" /> <i>Можно разделять запятой</i><br />
Число дней: <input type="text" id="prolongDays" /><br />
Число часов: <input type="text" id="prolongHours" /><br />
Вид премиума:&nbsp;
<select id="premiumType">
	<option value='0'>Бои</option>
	<option value='1'>Добыча</option>
	<option value='2'>Крафт</option>
	<option value='3'>Работа</option>
	<option value='4'>Свобода</option>
	<option value='5'>Монстры</option>
</select>
<input type="button" onclick="updatePremiums( )" value="Подарить" /><br />
<br />
<div id="ajaxResult"></div>
<script>
$ajaxResult = $( '#ajaxResult' );

	function updatePremiums( )
	{
		$ajaxResult.html( 'Пробуем...' );
		
		$.ajax
		({
			type: 'POST',
			url: 'ajaxQuery.php?service=<?=$serviceIdentity?>',
			data: 'happyPlayers=' + $( '#happyPlayers' ).val( ) + '&prolongDays=' + $( '#prolongDays' ).val( ) + '&prolongHours' + $( '#prolongHours' ).val( ) +  '&premiumType=' + $( '#premiumType' ).val( ),
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