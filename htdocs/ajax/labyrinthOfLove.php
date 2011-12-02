<?
/* @author = undefined
 * @date = 19 февраля 2011
 * @about = Лабиринт Любви, серверная реализация игры
 */

	// Здесь задаём Лабиринты
	$level = array( );
	$hearts = array ( );
	$exits = array( );
	$level[0] = array( );  // Пустое, так удобней для индексного обращения
	$hearts[0] = array( ); // Пустое, так удобней для индексного обращения
	$exits[0] = array( );  // Пустое, так удобней для индексного обращения
	// I уровень
	$level[1] = array( array( 0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0 ), array( 0,1,1,0,1,1,1,1,0,1,1,1,0,1,1,1,0,1,1,1,1,1,0 ), array( 0,0,1,0,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0 ), array( 0,1,1,1,1,1,1,1,1,1,0,1,1,1,1,1,1,1,1,1,1,1,0 ), array( 0,1,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,0,0,0,0,0,0 ), array( 0,1,1,1,1,1,0,1,0,1,1,1,1,1,1,1,0,1,1,1,1,1,0 ), array( 0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,1,0,1,0,1,0,1,0 ), array( 0,1,1,1,0,1,1,1,1,1,1,1,0,1,1,1,1,1,0,0,0,1,0 ), array( 0,0,0,0,0,1,0,0,0,1,0,0,0,1,0,0,0,0,0,1,0,0,0 ), array( 0,1,0,1,1,1,0,1,0,1,0,1,1,1,1,1,1,0,1,1,1,1,0 ), array( 0,1,0,1,0,0,0,1,0,1,0,0,0,1,0,1,0,0,0,1,0,1,0 ), array( 0,1,1,1,0,1,1,1,1,1,0,1,1,1,0,1,1,1,1,1,0,1,0 ), array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0 ) );
	$hearts[1] = array( array( 1, 7 ), array( 1, 9 ), array( 5, 11 ), array( 9, 1 ), array( 21, 1 ), array( 11, 11 ), array( 19, 6 ), array( 7, 5 ), array( 19, 11 ) );
	$exits[1] = array( 12, 21 );
	// II уровень
	$level[2] = array( array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0), array( 0,1,1,1,1,1,1,1,0,1,0,1,1,1,0,1,1,1,1,1,1,1,0), array( 0,1,0,0,0,1,0,0,0,1,0,1,0,1,1,1,0,0,0,0,0,1,0), array( 0,1,0,1,1,1,1,1,0,1,1,1,0,1,0,1,1,0,1,1,1,1,0), array( 0,0,0,1,0,1,0,1,0,0,0,1,0,1,0,1,0,0,0,1,0,0,0), array( 0,1,1,1,0,0,0,1,0,1,1,1,0,1,0,1,1,1,0,1,0,1,0), array( 0,0,0,1,0,1,0,0,0,1,0,1,0,1,0,0,0,1,0,0,0,1,0), array( 0,1,0,1,0,1,1,1,0,0,0,1,0,1,1,1,1,1,1,1,1,1,0), array( 0,1,0,1,0,0,0,1,0,1,1,0,0,0,0,1,0,1,0,0,0,0,0), array( 0,1,0,1,1,1,0,1,0,1,0,1,1,1,1,1,0,1,0,1,0,1,0), array( 0,1,0,1,0,1,0,1,0,1,0,1,0,0,1,0,0,1,0,1,0,1,0), array( 0,1,1,1,0,1,1,1,1,1,1,1,0,1,1,1,0,1,1,1,1,1,0), array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0 ) );
	$hearts[2] = array( array( 1, 3 ), array( 7, 1 ), array( 5, 6 ), array( 17, 7 ), array( 1, 7 ), array( 21, 11 ), array( 21, 5 ), array( 9, 1 ), array( 9, 5 ), array( 21, 5 ) );	
	$exits[2] = array( 12, 6 );
	// III уровень
	$level[3] = array( array( 0,0,0,0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0), array( 0,1,1,0,1,1,1,0,1,1,0,1,1,1,0,1,1,1,0,1,1,1,0), array( 0,1,0,0,1,0,0,0,0,1,0,0,0,1,0,0,0,1,0,1,0,0,0), array( 0,1,1,1,1,1,1,1,0,1,1,1,0,1,0,1,0,1,1,1,1,1,0), array( 0,0,0,0,0,0,0,1,0,0,0,1,0,1,0,1,0,1,0,0,0,1,0), array( 0,1,0,1,1,1,0,1,1,1,0,1,0,1,0,1,1,1,1,1,0,1,0), array( 0,1,0,1,0,0,0,1,0,1,1,1,0,1,0,0,0,0,0,1,0,1,0), array( 0,1,0,1,1,1,1,1,0,1,0,1,1,1,0,1,1,1,1,1,0,1,0), array( 0,1,0,1,0,0,0,0,0,1,0,1,0,0,0,1,0,1,0,0,0,0,0), array( 0,1,1,1,1,1,1,1,1,1,0,1,0,1,1,1,0,1,0,1,1,1,0), array( 0,0,0,0,0,1,0,1,0,0,0,1,1,1,0,0,0,1,0,1,0,1,0), array( 0,1,1,1,1,1,0,1,0,1,1,1,0,1,0,1,1,1,1,1,0,1,0), array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0 ) );
	$hearts[3] = array( array( 2, 1 ), array( 5, 1 ), array( 5, 5 ), array( 21, 11 ), array( 21, 1 ), array( 11, 1 ), array( 7, 11 ), array( 15, 3 ), array( 8, 1 ), array( 21, 7 ) );	
	$exits[3] = array( 12, 3 );
	// IV уровень
	$level[4] = array( array( 0,0,0,1,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0), array( 0,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,1,0), array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0), array( 0,1,1,1,1,1,1,1,0,1,0,1,0,1,0,1,0,1,1,1,0,1,0), array( 0,1,0,0,0,0,0,1,1,1,1,1,1,1,1,1,0,1,0,1,0,1,0), array( 0,1,1,1,1,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0), array( 0,0,0,0,0,1,0,1,1,1,1,1,1,1,1,1,0,1,0,1,0,1,0), array( 0,1,1,1,1,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0), array( 0,1,0,0,0,0,0,1,1,1,1,1,1,1,1,1,0,1,0,1,0,1,0), array( 0,1,1,1,1,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0), array( 0,0,0,0,0,1,0,1,1,1,1,1,1,1,1,1,1,1,0,1,1,1,0), array( 0,1,1,1,1,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0,1,0), array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0 ) );
	$hearts[4] = array( array( 2, 1 ), array( 5, 1 ), array( 5, 5 ), array( 21, 11 ), array( 21, 1 ), array( 11, 1 ), array( 7, 11 ), array( 15, 3 ), array( 8, 1 ), array( 21, 7 ) );	
	$exits[4] = array( 12, 21 );
	// V уровень
	$level[5] = array( array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,1,0), array( 0,1,0,1,1,1,0,1,1,1,0,1,1,1,1,1,1,1,0,1,0,1,0), array( 0,1,1,1,0,1,1,1,0,1,0,1,0,0,0,1,0,0,0,1,0,1,0), array( 0,1,0,1,0,0,1,0,1,1,0,1,1,1,1,1,0,1,0,1,1,1,0), array( 0,0,0,0,0,0,1,0,1,0,0,1,0,0,0,0,0,1,0,0,1,0,0), array( 0,1,1,1,1,1,1,0,1,0,0,1,1,1,1,1,1,1,1,1,1,1,0), array( 0,1,0,0,0,0,0,1,1,1,1,0,1,0,0,0,0,1,0,0,1,0,0), array( 0,0,0,1,1,0,1,1,0,0,1,0,1,1,0,1,0,1,0,0,1,1,0), array( 0,1,1,1,0,0,1,0,0,0,1,0,0,1,1,1,1,1,0,1,0,1,0), array( 0,1,0,1,1,0,1,1,1,1,1,1,0,1,0,0,0,1,0,1,0,1,0), array( 0,1,0,0,1,0,0,0,0,1,0,1,0,0,0,1,0,1,0,1,0,1,0), array( 0,1,0,1,1,1,1,1,1,1,0,1,1,1,1,1,1,1,0,1,1,1,0), array( 0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0 ) );
	$hearts[5] = array( array( 1, 1 ), array( 1, 6 ), array( 1, 11 ), array( 21, 11 ), array( 3, 11 ), array( 19, 1 ), array( 13, 9 ), array( 19, 8 ), array( 4, 7 ), array( 20, 5 ) );	
	$exits[5] = array( 12, 6 );
	
	// Массив значений перехода между уровнями
	$levelUp = array( 1, 2, 3, 4, 5, 1 );
	 

 	// Проверка, что оба в онлайне и оба в Лабиринте Любви
 	$wifeId = f_MValue( 'SELECT p'.( 1 - $Player->sex ).' FROM player_weddings WHERE p'.$Player->sex.' = '.$Player->player_id );
	
	if( $wifeId ) // Если женат {Я по-прежнему пишу шовинистичные комментарии, так проще воспринимается лично мною}
	{
		$Wife = new Player( $wifeId );
		
		// Если жена онлайн
		if( f_MValue( 'SELECT session_crc FROM online WHERE player_id = '.$Wife->player_id ) )
		{
			// Проверяем, а здесь ли она стоит?
			if( $Wife->location == 2 && $Player->location == 2 && $Wife->depth == 1004 && $Player->depth == 1004 )
			{
				// Оба рядом, скрипт можно выполнять
				
 				$cmd = explode( '@', $HTTP_RAW_POST_DATA ); // Запрос от клиента
				$lol = f_MFetch( f_MQuery( 'SELECT * FROM labyrinth_of_love WHERE p'.$Player->sex.' = '.$Player->player_id ) ); // Текущее положение дел у этой пары с их Лабиринтом			
 				
 				// Выполнение команды от клиента
				switch( $cmd[0] )
				{
					// Подаёт заявку
					case 'init':
					{
						// Заявку можно _подать_ только когда её ещё никто не подавал
						if( $lol['status'] == 0 )
						{
							// Подаём заявку
							f_MQuery( 'UPDATE labyrinth_of_love SET status = '.( 1 + $Player->sex ).' WHERE p'.$Player->sex.' = '.$Player->player_id );
						}
						elseif( $lol['status'] == 1 + $Wife->sex ) // Если Жена уже подала заявку до Мужа
						{
							// Просто подтверждаем её заявку
							f_MQuery( 'UPDATE labyrinth_of_love SET status = 3, begin_time = '.time( ).' WHERE p'.$Player->sex.' = '.$Player->player_id );
						}
						break;	
					}
					case 'confirm':
					{
						if( $lol['status'] == 1 + $Wife->sex ) // Если действительно есть заявка от супруги
						{
							// Просто подтверждаем её заявку
							f_MQuery( 'UPDATE labyrinth_of_love SET status = 3, begin_time = '.time( ).' WHERE p'.$Player->sex.' = '.$Player->player_id );
						}
						break;					
					}
					case 'begin':
					{
						// Если сердечек ещё не было запихато в лабик, запихуем в лабик сердчеки
						if( !f_MValue( 'SELECT id FROM lol_hearts WHERE labyrinth_id = '.$lol[id].' LIMIT 1' ) && $lol['status'] == 3 )
						{
							$count = count( $hearts[$lol[level]] );
							for( $i = 0; $i < $count; ++ $i )
							{
								f_MQuery( 'INSERT INTO lol_hearts( labyrinth_id, posX, posY ) VALUES( '.$lol[id].', '.$hearts[$lol[level]][$i][0].', '.$hearts[$lol[level]][$i][1].' )' );							
							}
						}
						
						// И выводим всякий управляющий клиентом код						
						?>
							GameStatus.innerHTML = '';
							GameStatus.style.display = 'none';
							<?
								// Вывод массива уровня Лабиринта
								$count = count( $level[$lol['level']] );
								$st = 'level = [ [ ';
								for( $i = 0; $i < $count; ++ $i )
								{
									$st .= implode( ', ', $level[$lol['level']][$i] ).( ( $count - $i > 1 ) ? ' ], [ ' : ' ] ];' );
								}
								echo $st;
								
								// Определяемся с полом
								if( $Player->sex == 0 )
								{
									// Для настоящих Мужчин
									echo 'myGender = "male"; wifeGender="female";';						
								}
								else
								{
									// Для няшных самок, ПРИНАДЛЕЖАЩИХ НАСТОЯЩИМ МУЖЧИНАМ {Шовинизм, заявленный в начале, должен достичь апогея к концу, а затем - катарсис}
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
					// Переход
					case 'go':
					{
						// Запрашиваем координаты
						$myCoords = f_MFetch( f_MQuery( 'SELECT * FROM lol_players WHERE player_id = '.$Player->player_id ) );
						$microtime = microtime( true );

						if( $myCoords[last_time] + 0.050 > $microtime )
						{
							// Если ещё не прошло достаточно времени между ходами
							break;
						}

						// Выход - это финальная цель, дальше ходить никуда не нужно
						if( $myCoords[posX] == $exits[$lol[level]][1] and $myCoords[posY] == $exits[$lol[level]][0] )
						{
							break;
						}

						// Как именно изменились параметры? Куда сходил члендовек?
						if( $cmd[1] != 0 )
						{
							// Сходил вверх-вниз
							
							$myCoords[posY] += ( $cmd[1] > 0 ) ? 1 : -1;
							
							// Защищаемся от махинирования
							if( $myCoords[posY] < 0 or ( $level[$lol['level']][$myCoords[posY]][$myCoords[posX]] == 0 and ( $myCoords[posX] != $exits[$lol[level]][1] or $myCoords[posY] != $exits[$lol[level]][0] or $lol[status] < 4 ) ) )
							{
								$myCoords[posY] -= ( $cmd[1] > 0 ) ? 1 : -1;
							}
						}
						elseif( $cmd[2] != 0 )
						{
							// Сходил влево-вправо
							$myCoords[posX] += ( $cmd[2] > 0 ) ? 1 : -1;
							
							if( $myCoords[posX] < 0 or $level[$lol['level']][$myCoords[posY]][$myCoords[posX]] == 0 )
							{
								$myCoords[posX] -= ( $cmd[2] > 0 ) ? 1 : -1;
							}
						}
						
						// Переход на координаты
						f_MQuery( 'UPDATE lol_players SET posX = '.$myCoords[posX].', posY = '.$myCoords[posY].', last_time = '.$microtime.' WHERE player_id = '.$Player->player_id );
	
						// Попал ли ты в выход		
						if( $myCoords[posX] == $exits[$lol[level]][1] and $myCoords[posY] == $exits[$lol[level]][0] )
						{
							// Запоминаем триггер, что кто-то прошёл, в БД.
							f_MQuery( 'UPDATE labyrinth_of_love SET status = status + 1 WHERE p'.$Player->sex.' = '.$Player->player_id );
						}

						// Собирательство сердечек
						if( f_MValue( 'SELECT id FROM lol_hearts WHERE labyrinth_id = '.$lol[id].' AND posX = '.$myCoords[posX].' AND posY = '.$myCoords[posY] ) )
						{
							f_MQuery( 'DELETE FROM lol_hearts WHERE labyrinth_id = '.$lol[id].' AND posX = '.$myCoords[posX].' AND posY = '.$myCoords[posY] );
							
							// Проверяем, а не последнее ли это было сердце?
							if( !f_MValue( 'SELECT id FROM lol_hearts WHERE labyrinth_id = '.$lol[id] ) )
							{
								// Если последнее, то ставим статус, что открыт выход-арка
								f_MQuery( 'UPDATE labyrinth_of_love SET status = 4 WHERE p'.$Player->sex.' = '.$Player->player_id );
							}
						}
						
						// Выводим обновлённые координаты
						$myCoords = f_MFetch( f_MQuery( 'SELECT * FROM lol_players WHERE player_id = '.$Player->player_id ) );

						echo 'myCoords = ['.$myCoords[posY].', '.$myCoords[posX].'];';
						echo 'moveAvatar( myGender );';
						
						break;
					}
					default:
					{
						// Сюда приходят все запросы, связанные с обновлением статуса и т.п.
						
						// Предложение подавать заявку
						if( $lol[status] == 0 )
						{
							// Выставляем заодно дефолтные настройки
							?>
							GameStatus.innerHTML = "<a href=\"javascript://\" onclick=\"sendRequest( )\">Подать заявку</a>";
							GameStatus.style.display = '';
							GameArea.style.display = 'none';
							maleAvatar.style.display = 'none';
							femaleAvatar.style.display = 'none';
							GameTimer.style.display = 'none';
							isBegin = false;
							<?
						}
						
						// Предложение подтверждать заявку
						elseif( $lol[status] == 1 + $Wife->sex )
						{
							?>
							GameStatus.innerHTML = "<a href=\"javascript://\" onclick=\"confirmRequest( )\">Принять заявку</a>";
							<?
						}
						
						// Ожидание подтверждения заявки любимой
						elseif( $lol[status] == 1 + $Player->sex )
						{
							?>
							GameStatus.innerHTML = "<i>Подожди, пока <b><?=$Wife->login?></b> подтвердит заявку.</i>";
							<?
						}
						
						// Игра уже началась и идёт
						elseif( $lol[status] > 2 )
						{
							// Выводим информацию, что игра в порядке
							echo 'isBegin = true;';
							// Выводим положение аватаров персонажей
							//$myCoords = f_MFetch( f_MQuery( 'SELECT * FROM lol_players WHERE player_id = '.$Player->player_id ) );
							$wifeCoords = f_MFetch( f_MQuery( 'SELECT * FROM lol_players WHERE player_id = '.$Wife->player_id ) );

							//echo 'myCoords = ['.$myCoords[posY].', '.$myCoords[posX].'];';
							echo 'wifeCoords = ['.$wifeCoords[posY].', '.$wifeCoords[posX].'];';
							//echo 'moveAvatar( myGender );';
							echo 'moveAvatar( wifeGender );';

							// Проверяем, открыт ли выход
							if( $lol['status'] > 3 )
							{
								// Да, открыт
								
								// Проверяем, оба ли в выходе
								if( $lol[status] == 6 )
								{
									// Да, оба, выходим из лабиринта и переходим на следующий уровень
									f_MQuery( 'UPDATE labyrinth_of_love SET status = 0, level = '.$levelUp[$lol[level]].' WHERE p'.$Player->sex.' = '.$Player->player_id );
									
									$time = time( ) - $lol[begin_time];							
									// Если поставили личный рекорд
									if( $lol[best_time] > $time )
									{
										// Заносим его в таблицу
										f_MQuery( 'UPDATE labyrinth_of_love SET best_time = '.( time( ) - $lol[begin_time] ).' WHERE id = '.$lol[id] );
									}
									// Перемещаем влюблённых в верх нового лабиринта									
									f_MQuery( 'UPDATE lol_players SET  posY = 0 WHERE player_id = '.$Player->player_id.' OR player_id = '.$Wife->player_id );
									
									// И спамим им системкой
									$time = Date( 'i:s', $time );
									$Player->syst2( 'Вы прошли этот уровень за <b>'.$time.'</b>' );
									$Wife->syst2( 'Вы прошли этот уровень за <b>'.$time.'</b>' );
								}
								else
								{
									// Нет, не оба, рисуем выход
									echo 'RedrawCoord( '.$exits[$lol[level]][0].', '.$exits[$lol[level]][1].', 2 );';
									// И выводим инфу, што сердечки все собраны
									echo 'hearts = []; DrawHearts( );';
								}
							}
							else
							{
								// Выводим несобранные сердца
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
				// Если жены нет на месте
				?>
					GameStatus.innerHTML = '<i><b><?=$Wife->login?></b> <?=( $Wife->sex ) ? 'должна' : 'должен' ?> быть рядом, чтобы можно было играть</i>';
					GameArea.style.display = 'false';
				<? 			
 			}
		}
		else
		{
			// Если жена оффлайн
			?>
				GameStatus.innerHTML = '<i><b><?=$Wife->login?></b> <?=( $Wife->sex ) ? 'должна' : 'должен' ?> быть онлайн, чтобы можно было играть</i>';
				GameArea.style.display = 'false';
			<?		
		}
	}
?>