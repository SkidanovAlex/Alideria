<?
/* @author = undefined
 * @date = 19 ������� 2011
 * @about = �������� �����, ��������� ���������� ����
 */

	// ����� ����� ���������
	$level = array( );
	$hearts = array ( );
	$exits = array( );
	$level[0] = array( );  // ������, ��� ������� ��� ���������� ���������
	$hearts[0] = array( ); // ������, ��� ������� ��� ���������� ���������
	$exits[0] = array( );  // ������, ��� ������� ��� ���������� ���������
	// I �������
	$level[1] = array( array( 0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0 ), array( 0,1,1,0,1,1,1,1,0,1,1,1,0,1,1,1,0,1,1,1,1,1,0 ), array( 0,0,1,0,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0 ), array( 0,1,1,1,1,1,1,1,1,1,0,1,1,1,1,1,1,1,1,1,1,1,0 ), array( 0,1,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,0,0,0,0,0,0 ), array( 0,1,1,1,1,1,0,1,0,1,1,1,1,1,1,1,0,1,1,1,1,1,0 ), array( 0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,1,0,1,0,1,0,1,0 ), array( 0,1,1,1,0,1,1,1,1,1,1,1,0,1,1,1,1,1,0,0,0,1,0 ), array( 0,0,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,0,0,1,0,0,0 ), array( 0,1,0,1,1,1,0,1,0,1,0,1,1,1,1,1,1,0,1,1,1,1,0 ), array( 0,1,0,1,0,0,0,1,0,1,0,0,0,1,0,1,0,0,0,1,0,1,0 ), array( 0,1,1,1,0,1,1,1,1,1,0,1,1,1,0,1,1,1,1,1,0,1,0 ), array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0 ) );
	$hearts[1] = array( array( 1, 7 ), array( 1, 9 ), array( 5, 11 ), array( 9, 1 ), array( 21, 1 ), array( 11, 11 ), array( 19, 6 ), array( 7, 5 ), array( 19, 11 ) );
	$exits[1] = array( 12, 21 );
	// II �������
	$level[2] = array( array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0), array( 0,1,1,1,1,1,1,1,0,1,0,1,1,1,0,1,1,1,1,1,1,1,0), array( 0,1,0,0,0,1,0,0,0,1,0,1,0,1,1,1,0,0,0,0,0,1,0), array( 0,1,0,1,1,1,1,1,0,1,1,1,0,1,0,1,1,0,1,1,1,1,0), array( 0,0,0,1,0,1,0,1,0,0,0,1,0,1,0,1,0,0,0,1,0,0,0), array( 0,1,1,1,0,0,0,1,0,1,1,1,0,1,0,1,1,1,0,1,0,1,0), array( 0,0,0,1,0,1,0,0,0,1,0,1,0,1,0,0,0,1,0,0,0,1,0), array( 0,1,0,1,0,1,1,1,0,0,0,1,0,1,1,1,1,1,1,1,1,1,0), array( 0,1,0,1,0,0,0,1,0,1,1,0,0,0,0,1,0,1,0,0,0,0,0), array( 0,1,0,1,1,1,0,1,0,1,0,1,1,1,1,1,0,1,0,1,0,1,0), array( 0,1,0,1,0,1,0,1,0,1,0,1,0,0,1,0,0,1,0,1,0,1,0), array( 0,1,1,1,0,1,1,1,1,1,1,1,0,1,1,1,0,1,1,1,1,1,0), array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0 ) );
	$hearts[2] = array( array( 1, 3 ), array( 7, 1 ), array( 5, 6 ), array( 17, 7 ), array( 1, 7 ), array( 21, 11 ), array( 21, 5 ), array( 9, 1 ), array( 9, 5 ), array( 21, 5 ) );	
	$exits[2] = array( 12, 6 );
	// III �������
	$level[3] = array( array( 0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0), array( 0,1,1,0,1,1,1,0,1,1,0,1,1,1,0,1,1,1,0,1,1,1,0), array( 0,1,0,0,1,0,0,0,0,1,0,0,0,1,0,0,0,1,0,1,0,0,0), array( 0,1,1,1,1,1,1,1,0,1,1,1,0,1,0,1,0,1,1,1,1,1,0), array( 0,0,0,0,0,0,0,1,0,0,0,1,0,1,0,1,0,1,0,0,0,1,0), array( 0,1,0,1,1,1,0,1,1,1,0,1,0,1,0,1,1,1,1,1,0,1,0), array( 0,1,0,1,0,0,0,1,0,1,1,1,0,1,0,0,0,0,0,1,0,1,0), array( 0,1,0,1,1,1,1,1,0,1,0,1,1,1,0,1,1,1,1,1,0,1,0), array( 0,1,0,1,0,0,0,0,0,1,0,1,0,0,0,1,0,1,0,0,0,0,0), array( 0,1,1,1,1,1,1,1,1,1,0,1,0,1,1,1,0,1,0,1,1,1,0), array( 0,0,0,0,0,1,0,1,0,0,0,1,1,1,0,0,0,1,0,1,0,1,0), array( 0,1,1,1,1,1,0,1,0,1,1,1,0,1,0,1,1,1,1,1,0,1,0), array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0 ) );
	$hearts[3] = array( array( 2, 1 ), array( 5, 1 ), array( 5, 5 ), array( 21, 11 ), array( 21, 1 ), array( 11, 1 ), array( 7, 11 ), array( 15, 3 ), array( 8, 1 ), array( 21, 7 ) );	
	$exits[3] = array( 12, 3 );
	// IV �������
	$level[4] = array( array( 0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0), array( 0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0), array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0), array( 0,1,1,1,1,1,1,1,0,1,0,1,0,1,0,1,0,1,1,1,0,1,0), array( 0,1,0,0,0,0,0,1,1,1,1,1,1,1,1,1,0,1,0,1,0,1,0), array( 0,1,1,1,1,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0), array( 0,0,0,0,0,1,0,1,1,1,1,1,1,1,1,1,0,1,0,1,0,1,0), array( 0,1,1,1,1,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0), array( 0,1,0,0,0,0,0,1,1,1,1,1,1,1,1,1,0,1,0,1,0,1,0), array( 0,1,1,1,1,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0), array( 0,0,0,0,0,1,0,1,1,1,1,1,1,1,1,1,1,1,0,1,1,1,0), array( 0,1,1,1,1,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0), array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0 ) );
	$hearts[4] = array( array( 2, 1 ), array( 5, 1 ), array( 5, 5 ), array( 21, 11 ), array( 21, 1 ), array( 11, 1 ), array( 7, 11 ), array( 15, 3 ), array( 8, 1 ), array( 21, 7 ) );	
	$exits[4] = array( 12, 21 );
	// V �������
	$level[5] = array( array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0), array( 0,1,0,1,1,1,0,1,1,1,0,1,1,1,1,1,1,1,0,1,0,1,0), array( 0,1,1,1,0,1,1,1,0,1,0,1,0,0,0,1,0,0,0,1,0,1,0), array( 0,1,0,1,0,0,1,0,1,1,0,1,1,1,1,1,0,1,0,1,1,1,0), array( 0,0,0,0,0,0,1,0,1,0,0,1,0,0,0,0,0,1,0,0,1,0,0), array( 0,1,1,1,1,1,1,0,1,0,0,1,1,1,1,1,1,1,1,1,1,1,0), array( 0,1,0,0,0,0,0,1,1,1,1,0,1,0,0,0,0,1,0,0,1,0,0), array( 0,0,0,1,1,0,1,1,0,0,1,0,1,1,0,1,0,1,0,0,1,1,0), array( 0,1,1,1,0,0,1,0,0,0,1,0,0,1,1,1,1,1,0,1,0,1,0), array( 0,1,0,1,1,0,1,1,1,1,1,1,0,1,0,0,0,1,0,1,0,1,0), array( 0,1,0,0,1,0,0,0,0,1,0,1,0,0,0,1,0,1,0,1,0,1,0), array( 0,1,0,1,1,1,1,1,1,1,0,1,1,1,1,1,1,1,0,1,1,1,0), array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0 ) );
	$hearts[5] = array( array( 1, 1 ), array( 1, 6 ), array( 1, 11 ), array( 21, 11 ), array( 3, 11 ), array( 19, 1 ), array( 13, 9 ), array( 19, 8 ), array( 4, 7 ), array( 20, 5 ) );	
	$exits[5] = array( 12, 6 );
	
	// ������ �������� �������� ����� ��������
	$levelUp = array( 1, 2, 3, 4, 5, 1 );
	 

 	// ��������, ��� ��� � ������� � ��� � ��������� �����
 	$wifeId = f_MValue( 'SELECT p'.( 1 - $Player->sex ).' FROM player_weddings WHERE p'.$Player->sex.' = '.$Player->player_id );
	
	if( $wifeId ) // ���� ����� {� ��-�������� ���� ������������� �����������, ��� ����� �������������� ����� ����}
	{
		$Wife = new Player( $wifeId );
		
		// ���� ���� ������
		if( f_MValue( 'SELECT session_crc FROM online WHERE player_id = '.$Wife->player_id ) )
		{
			// ���������, � ����� �� ��� �����?
			if( $Wife->location == 2 && $Player->location == 2 && $Wife->depth == 1004 && $Player->depth == 1004 )
			{
				// ��� �����, ������ ����� ���������
				
 				$cmd = explode( '@', $HTTP_RAW_POST_DATA ); // ������ �� �������
				$lol = f_MFetch( f_MQuery( 'SELECT * FROM labyrinth_of_love WHERE p'.$Player->sex.' = '.$Player->player_id ) ); // ������� ��������� ��� � ���� ���� � �� ����������			
 				
 				// ���������� ������� �� �������
				switch( $cmd[0] )
				{
					// ����� ������
					case 'init':
					{
						// ������ ����� _������_ ������ ����� � ��� ����� �� �������
						if( $lol['status'] == 0 )
						{
							// ����� ������
							f_MQuery( 'UPDATE labyrinth_of_love SET status = '.( 1 + $Player->sex ).' WHERE p'.$Player->sex.' = '.$Player->player_id );
						}
						elseif( $lol['status'] == 1 + $Wife->sex ) // ���� ���� ��� ������ ������ �� ����
						{
							// ������ ������������ � ������
							f_MQuery( 'UPDATE labyrinth_of_love SET status = 3, begin_time = '.time( ).' WHERE p'.$Player->sex.' = '.$Player->player_id );
						}
						break;	
					}
					case 'confirm':
					{
						if( $lol['status'] == 1 + $Wife->sex ) // ���� ������������� ���� ������ �� �������
						{
							// ������ ������������ � ������
							f_MQuery( 'UPDATE labyrinth_of_love SET status = 3, begin_time = '.time( ).' WHERE p'.$Player->sex.' = '.$Player->player_id );
						}
						break;					
					}
					case 'begin':
					{
						// ���� �������� ��� �� ���� �������� � �����, �������� � ����� ��������
						if( !f_MValue( 'SELECT id FROM lol_hearts WHERE labyrinth_id = '.$lol[id].' LIMIT 1' ) && $lol['status'] == 3 )
						{
							$count = count( $hearts[$lol[level]] );
							for( $i = 0; $i < $count; ++ $i )
							{
								f_MQuery( 'INSERT INTO lol_hearts( labyrinth_id, posX, posY ) VALUES( '.$lol[id].', '.$hearts[$lol[level]][$i][0].', '.$hearts[$lol[level]][$i][1].' )' );							
							}
						}
						
						// � ������� ������ ����������� �������� ���						
						?>
							GameStatus.innerHTML = '';
							GameStatus.style.display = 'none';
							<?
								// ����� ������� ������ ���������
								$count = count( $level[$lol['level']] );
								$st = 'level = [ [ ';
								for( $i = 0; $i < $count; ++ $i )
								{
									$st .= implode( ', ', $level[$lol['level']][$i] ).( ( $count - $i > 1 ) ? ' ], [ ' : ' ] ];' );
								}
								echo $st;
								
								// ������������ � �����
								if( $Player->sex == 0 )
								{
									// ��� ��������� ������
									echo 'myGender = "male"; wifeGender="female";';						
								}
								else
								{
									// ��� ������ �����, ������������� ��������� �������� {��������, ���������� � ������, ������ ������� ������ � �����, � ����� - ��������}
									echo 'myGender = "female"; wifeGender = "male";';					
								}											

							$myCoords = f_MFetch( f_MQuery( 'SELECT * FROM lol_players WHERE player_id = '.$Player->player_id ) );
							$wifeCoords = f_MFetch( f_MQuery( 'SELECT * FROM lol_players WHERE player_id = '.$Wife->player_id ) );
						
							echo 'myCoords = ['.$myCoords[posY].', '.$myCoords[posX].'];';
							echo 'wifeCoords = ['.$wifeCoords[posY].', '.$wifeCoords[posX].'];';
							echo 'DrawAvatars( );';
							echo 'DrawGame( );';
							echo 'beginTime = '.$lol[begin_time].';';
							echo 'serverTime = '.time( ).';';
							echo 'DrawTimer( );';
							
							break;			
					}
					// �������
					case 'go':
					{
						// ����������� ����������
						$myCoords = f_MFetch( f_MQuery( 'SELECT * FROM lol_players WHERE player_id = '.$Player->player_id ) );
						$microtime = microtime( true );

						if( $myCoords[last_time] + 0.050 > $microtime )
						{
							// ���� ��� �� ������ ���������� ������� ����� ������
							break;
						}

						// ����� - ��� ��������� ����, ������ ������ ������ �� �����
						if( $myCoords[posX] == $exits[$lol[level]][1] and $myCoords[posY] == $exits[$lol[level]][0] )
						{
							break;
						}

						// ��� ������ ���������� ���������? ���� ������ ���������?
						if( $cmd[1] != 0 )
						{
							// ������ �����-����
							
							$myCoords[posY] += ( $cmd[1] > 0 ) ? 1 : -1;
							
							// ���������� �� �������������
							if( $myCoords[posY] < 0 or ( $level[$lol['level']][$myCoords[posY]][$myCoords[posX]] == 0 and ( $myCoords[posX] != $exits[$lol[level]][1] or $myCoords[posY] != $exits[$lol[level]][0] or $lol[status] < 4 ) ) )
							{
								$myCoords[posY] -= ( $cmd[1] > 0 ) ? 1 : -1;
							}
						}
						elseif( $cmd[2] != 0 )
						{
							// ������ �����-������
							$myCoords[posX] += ( $cmd[2] > 0 ) ? 1 : -1;
							
							if( $myCoords[posX] < 0 or $level[$lol['level']][$myCoords[posY]][$myCoords[posX]] == 0 )
							{
								$myCoords[posX] -= ( $cmd[2] > 0 ) ? 1 : -1;
							}
						}
						
						// ������� �� ����������
						f_MQuery( 'UPDATE lol_players SET posX = '.$myCoords[posX].', posY = '.$myCoords[posY].', last_time = '.$microtime.' WHERE player_id = '.$Player->player_id );
	
						// ����� �� �� � �����		
						if( $myCoords[posX] == $exits[$lol[level]][1] and $myCoords[posY] == $exits[$lol[level]][0] )
						{
							// ���������� �������, ��� ���-�� ������, � ��.
							f_MQuery( 'UPDATE labyrinth_of_love SET status = status + 1 WHERE p'.$Player->sex.' = '.$Player->player_id );
						}

						// �������������� ��������
						if( f_MValue( 'SELECT id FROM lol_hearts WHERE labyrinth_id = '.$lol[id].' AND posX = '.$myCoords[posX].' AND posY = '.$myCoords[posY] ) )
						{
							f_MQuery( 'DELETE FROM lol_hearts WHERE labyrinth_id = '.$lol[id].' AND posX = '.$myCoords[posX].' AND posY = '.$myCoords[posY] );
							
							// ���������, � �� ��������� �� ��� ���� ������?
							if( !f_MValue( 'SELECT id FROM lol_hearts WHERE labyrinth_id = '.$lol[id] ) )
							{
								// ���� ���������, �� ������ ������, ��� ������ �����-����
								f_MQuery( 'UPDATE labyrinth_of_love SET status = 4 WHERE p'.$Player->sex.' = '.$Player->player_id );
							}
						}
						
						// ������� ���������� ����������
						$myCoords = f_MFetch( f_MQuery( 'SELECT * FROM lol_players WHERE player_id = '.$Player->player_id ) );

						echo 'myCoords = ['.$myCoords[posY].', '.$myCoords[posX].'];';
						echo 'moveAvatar( myGender );';
						
						break;
					}
					default:
					{
						// ���� �������� ��� �������, ��������� � ����������� ������� � �.�.
						
						// ����������� �������� ������
						if( $lol[status] == 0 )
						{
							// ���������� ������ ��������� ���������
							?>
							GameStatus.innerHTML = "<a href=\"javascript://\" onclick=\"sendRequest( )\">������ ������</a>";
							GameStatus.style.display = '';
							GameArea.style.display = 'none';
							maleAvatar.style.display = 'none';
							femaleAvatar.style.display = 'none';
							GameTimer.style.display = 'none';
							isBegin = false;
							<?
						}
						
						// ����������� ������������ ������
						elseif( $lol[status] == 1 + $Wife->sex )
						{
							?>
							GameStatus.innerHTML = "<a href=\"javascript://\" onclick=\"confirmRequest( )\">������� ������</a>";
							<?
						}
						
						// �������� ������������� ������ �������
						elseif( $lol[status] == 1 + $Player->sex )
						{
							?>
							GameStatus.innerHTML = "<i>�������, ���� <b><?=$Wife->login?></b> ���������� ������.</i>";
							<?
						}
						
						// ���� ��� �������� � ���
						elseif( $lol[status] > 2 )
						{
							// ������� ����������, ��� ���� � �������
							echo 'isBegin = true;';
							// ������� ��������� �������� ����������
							//$myCoords = f_MFetch( f_MQuery( 'SELECT * FROM lol_players WHERE player_id = '.$Player->player_id ) );
							$wifeCoords = f_MFetch( f_MQuery( 'SELECT * FROM lol_players WHERE player_id = '.$Wife->player_id ) );

							//echo 'myCoords = ['.$myCoords[posY].', '.$myCoords[posX].'];';
							echo 'wifeCoords = ['.$wifeCoords[posY].', '.$wifeCoords[posX].'];';
							//echo 'moveAvatar( myGender );';
							echo 'moveAvatar( wifeGender );';

							// ���������, ������ �� �����
							if( $lol['status'] > 3 )
							{
								// ��, ������
								
								// ���������, ��� �� � ������
								if( $lol[status] == 6 )
								{
									// ��, ���, ������� �� ��������� � ��������� �� ��������� �������
									f_MQuery( 'UPDATE labyrinth_of_love SET status = 0, level = '.$levelUp[$lol[level]].' WHERE p'.$Player->sex.' = '.$Player->player_id );
									
									$time = time( ) - $lol[begin_time];							
									// ���� ��������� ������ ������
									if( $lol[best_time] > $time )
									{
										// ������� ��� � �������
										f_MQuery( 'UPDATE labyrinth_of_love SET best_time = '.( time( ) - $lol[begin_time] ).' WHERE id = '.$lol[id] );
									}
									// ���������� ��������� � ���� ������ ���������									
									f_MQuery( 'UPDATE lol_players SET  posY = 0 WHERE player_id = '.$Player->player_id.' OR player_id = '.$Wife->player_id );
									
									// � ������ �� ���������
									$time = Date( 'i:s', $time );
									$Player->syst2( '�� ������ ���� ������� �� <b>'.$time.'</b>' );
									$Wife->syst2( '�� ������ ���� ������� �� <b>'.$time.'</b>' );
								}
								else
								{
									// ���, �� ���, ������ �����
									echo 'RedrawCoord( '.$exits[$lol[level]][0].', '.$exits[$lol[level]][1].', 2 );';
									// � ������� ����, ��� �������� ��� �������
									echo 'hearts = []; DrawHearts( );';
								}
							}
							else
							{
								// ������� ����������� ������
								$leftHearts = f_MQuery( 'SELECT posX, posY FROM lol_hearts WHERE labyrinth_id = '.$lol[id] );
								$hrts = array( );						
								while( $hrts[] = f_MFetch( $leftHearts ) )
								{
									continue;
								}
								$st = 'hearts = [ [ ';
								$count = count( $hrts ) - 1;
								for( $i = 0; $i < $count; ++ $i )
								{
									$st .= $hrts[$i][posY].', '.$hrts[$i][posX].( ( $count - $i > 1 ) ? ' ], [ ' : ' ] ];' );
								}
								echo ( ( $st != 'hearts = [ [ ' ) ? $st : 'hearts = [];' ).'DrawHearts( );';
							}
						}
						break;
					}
				}
 			}
 			else
 			{
				// ���� ���� ��� �� �����
				?>
					GameStatus.innerHTML = '<i><b><?=$Wife->login?></b> <?=( $Wife->sex ) ? '������' : '������' ?> ���� �����, ����� ����� ���� ������</i>';
					GameArea.style.display = 'false';
				<? 			
 			}
		}
		else
		{
			// ���� ���� �������
			?>
				GameStatus.innerHTML = '<i><b><?=$Wife->login?></b> <?=( $Wife->sex ) ? '������' : '������' ?> ���� ������, ����� ����� ���� ������</i>';
				GameArea.style.display = 'false';
			<?		
		}
	}
?>