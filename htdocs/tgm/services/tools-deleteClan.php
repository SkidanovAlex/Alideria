<?
/* @author = undefined
 * @date = 16 февраля 2011
 * @about = Начисление премиумов всем игрокам 
 */
?>
<select id="clanId">
<?
	$clans = f_MQuery( 'SELECT clan_id,name FROM clans' );
	while( $clan = f_MFetch( $clans ) )
	{
		echo "<option value='$clan[clan_id]'>$clan[name]</option>";
	}
?>
</select>
<input type="button" onclick="deleteClan( )" value="Delete" /><br />
<br />
<div id="ajaxResult"></div>
<script>
$ajaxResult = $( '#ajaxResult' );

	function deleteClan( )
	{
		$ajaxResult.html( 'Try...' );
	
		$.ajax
		({
			type: 'POST',
			url: 'ajaxQuery.php?service=<?=$serviceIdentity?>',
			data: 'clanId=' + $( '#clanId' ).val( ),
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