<?
/* @author = undefined
 * @date = 16 ������� 2011
 * @about = ���������� ��������� ���� ������� 
 */
?>
��������� ��������� � ��������.
<table celspacing="0" cellpadding="1">
	<tbody>
		<tr>
			<td style="verical-align: top;">
				����:
			</td>
			<td style="vertical-align: top;">
				<input type="text" id="happyPlayers" />
				<span style="font-size: 10px; font-style: italic;">����� ��������� ��������� ��������� �������, �������� �� ������� ��� �������. ����� ������� ���� �������, ������ ��������� ����� ��������� ���� "%" ��� �������</span>
			</td>
		</tr>
		<tr>
			<td>
				UIN:
			</td>
			<td>
				<input type="text" id="effectUin" value="0" />
				<span style="font-size: 10px; font-style: italic;">����������� � <a href="/forum.php?post=84871&f=0&page=0" target="_blank">���� ����</a></span>
			</td>
		</tr>		
		<tr>
			<td>
				���:
			</td>
			<td>
				<input type="text" id="effectType" value="0" />
				<span style="font-size: 10px; font-style: italic;">0 - ������, 1 - ������</span>
			</td>
		</tr>		
		<tr>
			<td>
				�� �������:
			</td>
			<td>
				<input type="text" id="effectDeadline" value="-1" />
				<span style="font-size: 10px; font-style: italic;">��������� � ��������, ������� � <a href="http://lmgtfy.com/?q=unix+timestamp" target="_blank">3 ����� ���� 1 ������ 1970</a>. ��-��������� ����� �������� �������������</span>
			</td>
		</tr>
		<tr>
			<td>
				��������:
			</td>
			<td>
				<input type="text" id="effectName" />
			</td>
		</tr>
		<tr>
			<td>
				��������:
			</td>
			<td>
				<textarea id="effectText"></textarea>
			</td>
		</tr>
		<tr>
			<td>
				��������:
			</td>
			<td>
				<input type="text" id="effectImage" value="smiley.png" />
				<span style="font-size: 10px; font-style: italic;">����� ��������� ��� �������� � ����� /images/effects/, ������� � ����������.</span>
			</td>
		</tr>
		<tr>
			<td>
				������:
			</td>
			<td>
				<input type="text" id="effect" value="." />
				<span style="font-size: 10px; font-style: italic;">������ ����� ��, ��� � �������� � ���������.</td>
			</td>
		</tr>
	</tbody>
</table>
<input type="button" onclick="gift( )" value="��������!" /><br />
<br />
<div id="ajaxResult"></div>
<script>
$ajaxResult = $( '#ajaxResult' );

	function gift( )
	{
		$ajaxResult.html( '�������...' );
		
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
		$ajaxResult.html( '<span style="color: darkred; font-weight: bold;">�������, � ��� ��������: ' + Answer.statusText + ' [' + Answer.status + ']' + '</span>' );
	}
</script>