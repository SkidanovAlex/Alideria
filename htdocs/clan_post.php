<?
	require_once( 'textedit.php' );

	if( !isset( $mid_php ) )
		die( );

	if( 0 == ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_SEND_POST ) )
	{
		echo( 'У вас нет прав работать с этим разделом Ордена<br><a href=game.php?order=main>Назад</a>' );
		return;
	}
	echo '<b>Почта Ордена</b> - <a href=game.php?order=main>Назад</a><br><br>';


	if( isset( $_POST['txt'] ) && strlen( trim( $_POST['txt'] ) ) > 0 )
	{
		$st = $_POST['txt'];
		
		$title = ( $_POST['title'] ) ? $_POST['title'] : 'Рассылка Ордену';
		
		$str = substr( $st, 0, 2000 );
		$str = mysql_real_escape_string( $str );
		$msg = substr( $st, 0, 201 );
		$msg = mysql_real_escape_string( $msg );
		if( strlen( $msg ) == 201 ) $msg = substr( $msg, 0, 180 )."...";

		$res = f_MQuery( "SELECT player_id FROM player_clans WHERE clan_id=$clan_id" );
		while( $arr = f_MFetch( $res ) )
		{
			$plr = new Player( $arr[0] );
			$plr->syst2( str_replace( "\r", " ", str_replace( "\n", " ", process_str( htmlspecialchars( $msg, ENT_QUOTES ) ) ) ) );
			f_MQuery( "INSERT INTO post( sender_id, receiver_id, title, content, money, np, deadline ) VALUES ( $player->player_id, $arr[0], '$title', '$str', '0', '0', '0' )" );
		}

		echo "<b><font color=darkgreen>Сообщение послано</font></b><br><a href=game.php?order=post>Послать еще одно сообщение</a>";
		}
	else
	{
	?>
		<script>
			/*
				Подсчёт вхождений подстроки в строку
			*/
			function jspsAllMatches( workString, searchSubstring )
			{
				var nSS = 0;
				var nWSLen = workString.length;
				var nSSLen = searchSubstring.length;
				var nP = workString.indexOf( searchSubstring );
				
				while( nP != -1 )
				{
					nSS++;
					nP += nSSLen;
					nP = workString.indexOf( searchSubstring, nP );
				}
				
				return nSS;
			}
			function calcSymbols( )
			{
				// Подсчёт символов в письме
				var text = document.getElementById( 'text' ).value;
				var symbols = text.length;
					symbols += jspsAllMatches( text, '\n' );
				
				var leftSymbolsTD = document.getElementById( 'leftSymbols' );
				leftSymbolsTD.innerHTML = 2048 - symbols;	
				if( symbols > 2048 )
					leftSymbolsTD.style.color = 'darkred';
				else
					leftSymbolsTD.style.color = 'black';
			}
		</script>
		<form action="game.php?order=post" method="POST">
			<table>
				<tr>
					<td>Заголовок:</td>
					<td>
						<input class="m_btn" name="title" id="title" />
					</td>
				</tr>
				<tr>
					<td style="vertical-align: top;">Текст:</td>
					<td>
						<table cellpadding="0px" cellspacing="0px">
							<tr>
								<td>
									<textarea cols="20" rows="4" style="border:1px solid black;" name="txt" id="text" onkeyup="calcSymbols( );"></textarea>
								</td>
								<td id="leftSymbols" style="font-weight: bold; vertical-align: bottom; padding-left: 4px;">
									2048
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input type="submit" class="s_btn" value="Отправить">
					</td>
				</tr>
			</table>
		</form>
	<?
	}
?>
