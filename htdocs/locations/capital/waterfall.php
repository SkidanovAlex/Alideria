<?
/* @author = undefined
 * @version = 0.0.0.9
 * @date = 13 ������� 2011
 * @about = ������� "�������", �������. ����� ����� ������� � ���������� �������.
 */
 

  	$time = time( ); // ������� ����� ������������ ������ ��� �����, ���������� ���������
 	// ����� ��������� � ������� ���� �������� ����, ��� ��� ��� ��������
 	$wishingToDivorce = f_MQuery( 'SELECT * FROM wishing_to_divorce WHERE divorce_time < '.$time );
 	while( $wishing = f_MFetch( $wishingToDivorce ) )
 	{
 		// ���������, ����������� �� ���� � ��������� ���������
 		$labId = f_MValue( 'SELECT id FROM labyrinth_of_love WHERE p0 = '.$wishing[p0].' OR p1 = '.$wishing[p1] );
 		if( $labId )
 		{
 			f_MQuery( 'DELETE FROM labyrinth_of_love WHERE id = '.$labId );
 			f_MQuery( 'DELETE FROM lol_hearts WHERE labyrinth_id = '.$labId );
 			$pair = f_MFetch( f_MQuery( 'SELECT p0,p1 FROM player_weddings WHERE p0 = '.$wishing[p0].' OR p1 = '.$wishing[p1] ) );
 			f_MQuery( 'DELETE FROM lol_players WHERE player_id = '.$pair[p0] );
 			f_MQuery( 'DELETE FROM lol_players WHERE player_id = '.$pair[p1] );
 		}
 	}
	f_MQuery( "DELETE wishing_to_divorce, player_weddings, player_triggers FROM wishing_to_divorce, player_weddings, player_triggers WHERE ( player_weddings.p0 = wishing_to_divorce.p0 OR player_weddings.p1 = wishing_to_divorce.p1 ) AND wishing_to_divorce.divorce_time < $time AND ( player_triggers.trigger_id = 2011 AND player_triggers.player_id = player_weddings.p0 )" );
	f_MQuery( "DELETE wishing_to_divorce, player_weddings FROM wishing_to_divorce, player_weddings WHERE ( player_weddings.p0 = wishing_to_divorce.p0 OR player_weddings.p1 = wishing_to_divorce.p1 ) AND wishing_to_divorce.divorce_time < $time" );

/*
	if (isset($_GET['cancel1'])) // ������ ������� ��������
	{
		f_MQuery("DELETE FROM player_wedding_bets WHERE p0=".$player->player_id);
	}
*/
 	// ���� ����� ��� ������ ����� �� ������ � ����� �������� ��� �����������
	if( $player->HasTrigger( 51 ) or $player->HasTrigger( 50 ) or $player->HasTrigger( 49 ) or $player->login == 'test2' )
	{
		// ��������.
		if( !$player->HasTrigger( 223 ) && $player->sex == 0 )
		{
			$player->SetTrigger( 223 );		
		}
		
		if( $player->sex == 1 && $player->HasTrigger( 223 ) )
		{
			$player->SetTrigger( 223, 0 );		
		}
		
		// ����� �� �����?
		$wifeSex = 1 - $player->sex;
		$warr = f_MFetch( f_MQuery( "SELECT p{$wifeSex} FROM player_weddings WHERE p{$player->sex} = {$player->player_id}" ) );

		// ���� �����
		if( $warr[0] )
		{
  			$Wife = new Player( $warr[0] ); // � ��-��������� �������������� ���� ��� ��� ������, �� ���� ������� ������������� ��������
  			echo '<br />�� � ����� � <script>document.write( '.$Wife->Nick( ).' );</script><br /><br />';

			// ������ �� ���������� ��� ���������?
			$wishingToDivorce = f_MFetch( f_MQuery( 'SELECT * FROM wishing_to_divorce WHERE p'.$player->sex.' = '.$player->player_id.' or p'.$Wife->sex.' = '.$Wife->player_id ) );

			if( isset( $wishingToDivorce['id'] ) == true ) // ���� ���-�� �� ���� ������ ����������
			{
				include_js( 'js/daytimer.js' ); // ���������� ���������� � JS-��������
				
				if( $player->player_id != $wishingToDivorce['initiator_id'] ) // ���� ���������� ����� ��� ���������
				{
					if( $wishingToDivorce['is_agree'] == 0 && $_GET['divorce'] == 'agree' ) // ���� �������� �� ������
					{
						// ������ �������������, ��� ��������� �������� � ������������� ��� �������� ��������					
						f_MQuery( 'UPDATE wishing_to_divorce SET p'.$player->sex.' = '.$player->player_id.', divorce_time = '.( $time + 604800 ).', is_agree = true WHERE id = '.$wishingToDivorce['id'] );
						echo '������ ��������� ����� <b>������</b>.';
					}
					elseif( $wishingToDivorce['is_agree'] == 0 ) // ���� ��� �� ����������
					{
						echo '<script>document.write( '.$Wife->Nick( ).' );</script> ������ ����������. <a href="/game.php?divorce=agree">����������� �� ������</a>.<br /><script>document.write( InsertTimer( '.( $wishingToDivorce['divorce_time'] - $time ).', "������ ��������� ����� <b>", "</b>", 0, "location.reload( );" ) );</script>';
					}
					else // ���� ��� ���������� � ������ �������� � �������� ����
					{
						echo '<script>document.write( InsertTimer( '.( $wishingToDivorce['divorce_time'] - $time ).', "������ ��������� ����� <b>", "</b>", 0, "location.reload( );" ) );</script>';
					}
				}
				else // ���� ���������� ����� ���� �����
				{
					if( $_GET['divorce'] == 'discard' ) // ���� ������������ �� ��������� �� ������
					{
						// ������� �� ��������� � �������, �������� ��
						f_MQuery( 'DELETE FROM wishing_to_divorce WHERE p'.$player->sex.' = '.$player->player_id );
					}
					else // ���� �� ������������ �� ��������� �� ������, ������ ����� ����������
					{
						echo '�� ����������� ����������. <script>document.write( InsertTimer( '.( $wishingToDivorce['divorce_time'] - $time ).', "������ ��������� ����� <b>", "</b>", 0, "location.reload( );" ) );</script><br />������, ��� �� ������ <a href="/game.php?divorce=discard">����������</a> �� �������.';
					}				
				}
			}
			elseif( $_GET['divorce'] == 'init' ) // ���� ������ ����������
  			{
  				if( $player->SpendMoney( 30000 ) == true ) // ����� �� �������� ������
  				{
	  				// ��������� � ������� �������� ����������
  					f_MQuery( 'INSERT INTO wishing_to_divorce(p'.$player->sex.',divorce_time,initiator_id) VALUES('.$player->player_id.','.( $time + 1209600 ).','.$player->player_id.')' );

  					// ����������� � ���������� �������
  					echo '��������� �� ������ ������. ������ ��������� ����� <b>��� ������.</b><br />';
  					$Wife->syst3( '<b>'.$player->login.'</b> �����'.( ( $player->sex ) ? '�' : '' ).' ��������� �� ������.' );
  				}
  				else
  				{
  					echo '������ ����� <b>30000 ��������</b>, ������� � ���� ���.';
  				}
  			}
  			else // ��� ����� �� ������, ���� ����������
  			{
  			?>
  				<script>
  					function showDivorceConfirm( )
  					{
  						document.getElementById( 'divorceConfirm' ).style.display = '';
  						document.getElementById( 'divorceInitHref' ).style.display = 'none';
  					}
  					
  					function hideDivorceConfirm( )
  					{
  						document.getElementById( 'divorceConfirm' ).style.display = 'none';
  						document.getElementById( 'divorceInitHref' ).style.display = '';
  					}
  				</script>
  				<div style="position: absolute; z-index: 1001; top: 175px; right: 5px;"><a href="javascript://" onclick="showDivorceConfirm( )" id="divorceInitHref">����������</a></div>
  				<div id="divorceConfirm" style="display: none;">
  					�� ����� ������� ����������?<br />
  					<i>������ ��������� �� ������ ����� <b>30000</b> ��������</i><br />
  					<br />
  					<table>
  						<tr>
  							<td style="background: url(/images/top/f.png); width: 92px; height: 21px; text-align: center; cursor: pointer;" onclick="document.location.href = '/game.php?divorce=init'">��</td>
  							<td style="background: url(/images/top/f.png); width: 92px; height: 21px; text-align: center; cursor: pointer;" onclick="hideDivorceConfirm( )">���</td>
  						</tr>
  					</table>
  				</div>
  			<?
			}
		}
		else
		{
			// ���� ������� ����������
			
			if( $player->HasTrigger( 223 ) && !f_MValue( 'SELECT * FROM player_weddings WHERE p0 = '.$player->player_id ) ) // ���� ������ ����� �� �������� � ��� �� �����
			{
				if( !f_MValue( 'SELECT * FROM player_wedding_bets WHERE p0 = '.$player->player_id ) ) // ���� ��� �� ������� ��������� � �����
				{
					// ��������� �� ������������� ��������
					if( isset( $_GET['wifename'] ) == true ) // ���� ������ ��� ����
					{
						// ���������, ���� �� ����� ����
     					$wifeId = f_MValue( 'SELECT player_id FROM characters WHERE login="'.mysql_real_escape_string( $_GET['wifename'] ).'" AND sex=1' );
	     				if( !$wifeId )
   	  				{
     						echo '������� �� ����� <b>'.htmlspecialchars( $_GET['wifename'], ENT_QUOTES ).'</b> �� ����������.<br /><a href="/game.php">����������� ������</a>';
     					}
     					else
						{
							// ��� ���� ����, ������� ��������
						
							$Wife = new Player( $wifeId ); // ������ ��������� ����. ��� ����� ������ *smile*
		     				if( f_MValue( 'SELECT * FROM player_weddings WHERE p1 = '.$Wife->player_id ) ) // ���� ������� ��� ����� �����
   		  				{
     							echo '<script>document.write( '.$Wife->Nick( ).' )</script> ��� �������.';
     						}
     						elseif( f_MValue( 'SELECT * FROM player_wedding_bets WHERE p1 = '.$Wife->player_id ) ) // ���� ������� ��� ����������
     						{
     							echo '<script>document.write( '.$Wife->Nick( ).' )</script> ��� ��������� � ��������� �������� ������� � ��������� ����-�� �������.';
     						}
     						else
     						{
	     						// ��������� � �������� ���������� ��� ����
   	  						f_MQuery( 'INSERT INTO player_wedding_bets( p0, p1 ) VALUES ( '.$player->player_id.', '.$wifeId.' )' );
	
								// ��������� ��������
								$prtext = '������� <b>'.$Wife->login.'</b>, � ���� ��� ������ ��� ���� �����, ��� ��. � ���� �������� �������� � ����� ������� ����� �����. �� ������� ���� �����?<br /><br /><img src="/images/wedring.gif" />';								
								f_MQuery( "INSERT INTO post( sender_id, receiver_id, title, content, money, np, deadline ) VALUES ( 69055, $Wife->player_id, '����������� ���� � ������ �� ".$player->login."', '$prtext', '0', '0', '0' )" );
	
     							echo '<script>document.write( '.$Wife->Nick( ).' )</script> �������� ��� ����������� ����������. �������, ���� ��� ���������� : )<br /><a href="/game.php">���������, ����� ���?</a>';
     						}
     					}
					}
					else // ���� ��� ���� �� ��������
					{
						// ���������� ������ ��� ����
						?>
						<script>
							function doMarry( )
							{
								var wifename = document.getElementById( 'wifename' ).value;
								if( !wifename )
								{
									alert( '����� ������� ��� ������� : )' );
									return;
								}							
								
								document.location.href = '/game.php?wifename=' + wifename;
							}
						</script>
						<table cellpaddgin="0" cellspacing="0">
  							<tr>
  								<td style="padding-right: 5px;">����� ��� �������: <input type="text" id="wifename" class="c_btn" /></td>
  								<td style="background: url(/images/top/f.png); width: 92px; height: 21px; text-align: center; cursor: pointer;" onclick="doMarry( )">������!</td>
	  						</tr>
  						</table>
						<?				
					}
				}
				else // ���� ��� ����� ���������
				{
					$Wife = new Player( f_MValue( 'SELECT p1 FROM player_wedding_bets WHERE p0 = '.$player->player_id ) ); // ������ ��������� ����
					
					// ���������, �������� �� �������
					if( f_MValue( 'SELECT moo FROM player_wedding_bets WHERE p0 = '.$player->player_id ) == 0 )
					{
						echo '<script>document.write( '.$Wife->Nick( ).' )</script> � �������� �������� �������.<br>';
//						echo "<a href='game.php?cancel1=1'>���������� �� �������</a>";
					}
					else
					{
						echo '<script>document.write( '.$Wife->Nick( ).' )</script> ������� ���� �����������! ������������!<br />������ ������ ����������, ����� �� ������� ��� �������� �����!';
					}
					
				}
			}
			else // ���� ����� �� �������� �� ��������
			{
				if( $player->sex == 1 ) // � ����� ������ �����?
				{
					if( $pid = f_MValue( 'SELECT p0 FROM player_wedding_bets WHERE p1 = '.$player->player_id ) )
					{
						// ���� ������� ������� ����

						$Guy = new Player( $pid ); // ��������� ����������
						
						if( f_MValue( 'SELECT moo FROM player_wedding_bets WHERE p1 = '.$player->player_id ) == 1 )
						{
							echo '�� ����� ������� ����� <script>document.write( '.$Guy->Nick( ).' )</script><br />�������� ���������, ���� ��������� ������� ��� �������� �����.';
						}						
						elseif( isset( $_GET['marry'] ) == true )	// ������� �� ��� �������?
						{
							// ������� �������
							
							// ��� ��������?
							if( $_GET['marry'] == 'true' )
							{
								// ��
								f_MQuery( 'UPDATE player_wedding_bets SET moo = 1 WHERE p1 = '.$player->player_id );
								$Guy->syst2( $player->login.' ������� ���� ����������� ����������!' );
							}
							else
							{
								// ���
								f_MQuery( 'DELETE FROM player_wedding_bets WHERE p1 = '.$player->player_id );
								$Guy->syst2( $player->login.' �������� ��� � ����� :(' );								
								$Guy->SetTrigger( 2011, 0 );				
							}
						}
						else
						{
							// ��� �������
							echo '<script>document.write( '.$Guy->Nick( ).' )</script> ���������� ��� ����������.<br />';
							?>
							<br />
							<table>
  								<tr>
  									<td style="background: url(/images/top/f.png); width: 92px; height: 21px; text-align: center; cursor: pointer;" onclick="document.location.href='/game.php?marry=true'">�����������</td>
  									<td style="background: url(/images/top/f.png); width: 92px; height: 21px; text-align: center; cursor: pointer;" onclick="document.location.href='/game.php?marry=false'">����������</td>
	  							</tr>
  							</table>
							<?
						}
					}
				}
			}
		}
		
		// ���� ��� �� ����� ������
		if( f_MValue( 'SELECT * FROM player_weddings WHERE p0 = '.$player->player_id ) && !$player->HasTrigger( 2011 ) )
		{
			// ���� ����� ������ �������
			if( isset( $_GET['marryRing'] ) == true )
			{
				if( $_GET['marryRing'] == 1 )
				{
					if( $player->SpendMoney( 20000 ) )
					{
						// ����� ��� ������� ������
						$player->AddItems( 69380, 1 ); // ������� ������ ������
						$player->AddItems( 69379, 1 ); // ������� ������ �������
						$player->SetTrigger( 2011 );   // ��������, ��� ������ ��� �����
						echo '<br />������ � ���� � ���������!';
					}
					else
					{
						echo '<br />� ���� �� ������� ��������';								
					}
				}
				else
				{
					// ����� ��� ������������ ������
					if( $player->SpendUMoney( 20 ) )
					{
						$player->AddItems( 69381, 1 ); // ������� ������ ������
						$player->AddItems( 69382, 1 ); // ������� ������ �������
						$player->SetTrigger( 2011 );   // ��������, ��� ������ ��� �����
						echo '<br />������ � ���� � ���������!';
					}
					else
					{
						echo '<br />� ���� �� ������� ��������';								
					}
				}
			}
			else
			{
				// ���� ���� �� �����, ������ ���� ����������
				?>
				<br /><br />
				<b>�� ������ ������ ����� ���� �����!</b>
				<table>
					<tr>
						<td style="text-align: center;">
							<img src="/images/items/Weding/ring-w1.gif" />
							<br /><br />
							<b>20000</b> <img src="/images/money.gif" />
						</td>
						<td style="text-align: center;">
							<img src="/images/items/Weding/ring-m2.gif" />
							<br /><br />
							<b>20</b> <img src="/images/umoney.gif" />
						</td>
					</tr>
					<tr>
						<td style="background: url(/images/top/f.png); width: 92px; height: 21px; text-align: center; cursor: pointer;" onclick="document.location.href='/game.php?marryRing=1'"><b>������</b></td>
						<td style="background: url(/images/top/f.png); width: 92px; height: 21px; text-align: center; cursor: pointer;" onclick="document.location.href='/game.php?marryRing=2'"><b>������</b></td>
					</tr>
				</table>
				<?
			}
		}
		
		// ���� ������� ��������
		if( !f_MValue( 'SELECT * FROM player_wedding_bets WHERE p'.$player->sex.' = '.$player->player_id ) && f_MValue( "SELECT level FROM clan_buildings WHERE building_id=1 AND clan_id={$player->clan_id}" ) > 10 ) // ���� ����� ������ ��������� ������ � �������� �� ������� ���
		{
			require_once( 'clan.php' ); // ���������� ������� ��������� ���� ������ � ������
			
			// ����� �� ��������� ���� ����� ������?
			if( getPlayerPermitions( $player->clan_id, $player->player_id ) & $CAN_MARRY )
			{
				if( isset( $_GET['marry'] ) == true )
				{
					$pid = (int)$_GET['marry'];
					$arr = f_MFetch( f_MQuery( "SELECT p0, p1 FROM player_wedding_bets WHERE moo=1 AND p0=$pid" ) );
					if( $arr )
					{
						if( $arr['p0'] != $player->player_id && $arr['p1'] != $player->player_id )
						{
  							f_MQuery( "DELETE FROM player_wedding_bets WHERE p0=$pid" );
 							f_MQuery( "INSERT INTO player_weddings( p0, p1 ) VALUES ( $arr[p0], $arr[p1] )" );
  							$plr1 = new Player( $arr['p0'] );
  							$plr2 = new Player( $arr['p1'] );
  							$plr1->SetTrigger( 224, 1 );
  							$plr2->SetTrigger( 224, 1 );
  							$plr1->SetTrigger( 223, 0 );
 							$plr2->SetTrigger( 223, 0 );
  							$plr1->syst2( "�� ������ ��� ������� ���� ����� ����� � <b>{$plr2->login}</b>! �����������!!!" );
  							$plr2->syst2( "�� ������ ��� ������� ���� ����� ����� � <b>{$plr1->login}</b>! �����������!!!" );
							glashSay( "������ ��� {$plr1->login} � {$plr2->login} ������� ���� ����� �����! ��������� ����� {$player->login}." );
						}
					}
				}

				// ������� �������� ����������
				$res = f_MQuery( 'SELECT p0, p1 FROM player_wedding_bets WHERE moo=1' );
				$st = '<br />';
				while( $arr = f_MFetch( $res ) )
				{
					$login1 = f_MValue( "SELECT login FROM characters WHERE player_id=$arr[p0]" );
					$login2 = f_MValue( "SELECT login FROM characters WHERE player_id=$arr[p1]" );
					$st .= "������ $login1 � $login2 ����� ����������. <a href=game.php?marry=$arr[p0]>�������� ��</a><br /><br />";
				}
				echo $st;
			}
		}
		
		// ������� ������� ��������� ���
		echo '<br />������� ����� ��������� ���!<br /><table border="1" style="background-color: e1c7a4;" celpadding="3px;"><tbody>';
		$counter = 0; // ������� ���������� ���		
		$bestLovers = f_MQuery( 'SELECT p0,p1,best_time FROM labyrinth_of_love WHERE best_time < 3600 ORDER BY best_time LIMIT 10' );
		while( $pair = f_MFetch( $bestLovers ) )
		{
			$counter ++;
			
			$He = new Player( $pair[p0] );
			$She = new Player( $pair[p1] );
			
			echo '<tr><td style="background:url(/images/bg.gif);">'.rome_number( $counter ).'</td><td style="background: url(/images/bg.gif);"><script>document.write( '.$He->Nick( ).' )</script> � <script>document.write( '.$She->Nick( ).' )</script></td><td style="background: url(/images/bg.gif);">'.Date( 'i:s', $pair[best_time] ).'</td></tr>';
		}
		echo '</tbody></table>';
	}
	else
	{
		echo "�� �� ��� ��� �� ������� ���� ������ � ������� ��������� ��� �����.<br>���� �� ��� ������� ������� ����� ����.";
	}
?>