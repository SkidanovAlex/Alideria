<?
/* @author = undefined
 * @version = 0.0.0.9
 * @date = 13 февраля 2011
 * @about = Локация "Водопад", Столица. Здесь дарят подарки и происходят разводы.
 */
 

  	$time = time( ); // Текущее время используется дофига где после, переменная оправдана
 	// Любое обращение к локации тоже разводит всех, кто это уже заслужил
 	$wishingToDivorce = f_MQuery( 'SELECT * FROM wishing_to_divorce WHERE divorce_time < '.$time );
 	while( $wishing = f_MFetch( $wishingToDivorce ) )
 	{
 		// Проверяем, участвовала ли пара в Лабиринте Влюблённых
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
	if (isset($_GET['cancel1'])) // отмена запроса женитьбы
	{
		f_MQuery("DELETE FROM player_wedding_bets WHERE p0=".$player->player_id);
	}
*/
 	// Если игрок уже прошёл квест на стихию и может жениться или разводиться
	if( $player->HasTrigger( 51 ) or $player->HasTrigger( 50 ) or $player->HasTrigger( 49 ) or $player->login == 'test2' )
	{
		// Заглушка.
		if( !$player->HasTrigger( 223 ) && $player->sex == 0 )
		{
			$player->SetTrigger( 223 );		
		}
		
		if( $player->sex == 1 && $player->HasTrigger( 223 ) )
		{
			$player->SetTrigger( 223, 0 );		
		}
		
		// Женат ли игрок?
		$wifeSex = 1 - $player->sex;
		$warr = f_MFetch( f_MQuery( "SELECT p{$wifeSex} FROM player_weddings WHERE p{$player->sex} = {$player->player_id}" ) );

		// Если женат
		if( $warr[0] )
		{
  			$Wife = new Player( $warr[0] ); // Я по-умолчанию шовинистически пишу код для мужчин, не зная лучшего кросспольного синонима
  			echo '<br />Ты в браке с <script>document.write( '.$Wife->Nick( ).' );</script><br /><br />';

			// Желает ли развестись его половинка?
			$wishingToDivorce = f_MFetch( f_MQuery( 'SELECT * FROM wishing_to_divorce WHERE p'.$player->sex.' = '.$player->player_id.' or p'.$Wife->sex.' = '.$Wife->player_id ) );

			if( isset( $wishingToDivorce['id'] ) == true ) // Если кто-то из пары желает развестись
			{
				include_js( 'js/daytimer.js' ); // Подключаем библиотеку с JS-таймером
				
				if( $player->player_id != $wishingToDivorce['initiator_id'] ) // Если развестись хочет его половинка
				{
					if( $wishingToDivorce['is_agree'] == 0 && $_GET['divorce'] == 'agree' ) // Если согласен на развод
					{
						// Ставим подтверждение, что гражданин согласен и устанавливаем ещё недельку ожидания					
						f_MQuery( 'UPDATE wishing_to_divorce SET p'.$player->sex.' = '.$player->player_id.', divorce_time = '.( $time + 604800 ).', is_agree = true WHERE id = '.$wishingToDivorce['id'] );
						echo 'Развод состоится через <b>неделю</b>.';
					}
					elseif( $wishingToDivorce['is_agree'] == 0 ) // Если ещё не соглашался
					{
						echo '<script>document.write( '.$Wife->Nick( ).' );</script> желает развестись. <a href="/game.php?divorce=agree">Согласиться на развод</a>.<br /><script>document.write( InsertTimer( '.( $wishingToDivorce['divorce_time'] - $time ).', "Развод состоится через <b>", "</b>", 0, "location.reload( );" ) );</script>';
					}
					else // Если уже согласился и просто рефрешит в ожидании чуда
					{
						echo '<script>document.write( InsertTimer( '.( $wishingToDivorce['divorce_time'] - $time ).', "Развод состоится через <b>", "</b>", 0, "location.reload( );" ) );</script>';
					}
				}
				else // Если развестись хочет этот игрок
				{
					if( $_GET['divorce'] == 'discard' ) // Если отказывается от заявления на развод
					{
						// Удаляем из заявлений о разводе, очевидно же
						f_MQuery( 'DELETE FROM wishing_to_divorce WHERE p'.$player->sex.' = '.$player->player_id );
					}
					else // Если не отказывается от заявления на развод, просто зашёл посмотреть
					{
						echo 'Ты собираешься развестись. <script>document.write( InsertTimer( '.( $wishingToDivorce['divorce_time'] - $time ).', "Развод состоится через <b>", "</b>", 0, "location.reload( );" ) );</script><br />Кстати, ещё не поздно <a href="/game.php?divorce=discard">отказаться</a> от развода.';
					}				
				}
			}
			elseif( $_GET['divorce'] == 'init' ) // Если желает развестись
  			{
  				if( $player->SpendMoney( 30000 ) == true ) // Может ли оплатить развод
  				{
	  				// Добавляем в таблицу желающих развестись
  					f_MQuery( 'INSERT INTO wishing_to_divorce(p'.$player->sex.',divorce_time,initiator_id) VALUES('.$player->player_id.','.( $time + 1209600 ).','.$player->player_id.')' );

  					// Поздравляем с правильным выбором
  					echo 'Заявление на развод подано. Развод произойдёт через <b>две недели.</b><br />';
  					$Wife->syst3( '<b>'.$player->login.'</b> подал'.( ( $player->sex ) ? 'а' : '' ).' заявление на развод.' );
  				}
  				else
  				{
  					echo 'Развод стоит <b>30000 дублонов</b>, которых у тебя нет.';
  				}
  			}
  			else // Раз никто не желает, надо предложить
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
  				<div style="position: absolute; z-index: 1001; top: 175px; right: 5px;"><a href="javascript://" onclick="showDivorceConfirm( )" id="divorceInitHref">Развестись</a></div>
  				<div id="divorceConfirm" style="display: none;">
  					Ты точно желаешь развестись?<br />
  					<i>Подача заявления на развод стоит <b>30000</b> дублонов</i><br />
  					<br />
  					<table>
  						<tr>
  							<td style="background: url(/images/top/f.png); width: 92px; height: 21px; text-align: center; cursor: pointer;" onclick="document.location.href = '/game.php?divorce=init'">Да</td>
  							<td style="background: url(/images/top/f.png); width: 92px; height: 21px; text-align: center; cursor: pointer;" onclick="hideDivorceConfirm( )">Нет</td>
  						</tr>
  					</table>
  				</div>
  			<?
			}
		}
		else
		{
			// Блок могущих пожениться
			
			if( $player->HasTrigger( 223 ) && !f_MValue( 'SELECT * FROM player_weddings WHERE p0 = '.$player->player_id ) ) // Если прошёл квест на женитьбу и ещё не женат
			{
				if( !f_MValue( 'SELECT * FROM player_wedding_bets WHERE p0 = '.$player->player_id ) ) // Если ещё не подавал заявление о браке
				{
					// Проверяем на желабельность жениться
					if( isset( $_GET['wifename'] ) == true ) // Если указал имя жены
					{
						// Проверяем, есть ли такая жена
     					$wifeId = f_MValue( 'SELECT player_id FROM characters WHERE login="'.mysql_real_escape_string( $_GET['wifename'] ).'" AND sex=1' );
	     				if( !$wifeId )
   	  				{
     						echo 'Девушки по имени <b>'.htmlspecialchars( $_GET['wifename'], ENT_QUOTES ).'</b> не существует.<br /><a href="/game.php">Попробовать другое</a>';
     					}
     					else
						{
							// Раз жена есть, пробуем поженить
						
							$Wife = new Player( $wifeId ); // Создаём экземпляр жены. Это очень удобно *smile*
		     				if( f_MValue( 'SELECT * FROM player_weddings WHERE p1 = '.$Wife->player_id ) ) // Если девушка уже вышла замуж
   		  				{
     							echo '<script>document.write( '.$Wife->Nick( ).' )</script> уже замужем.';
     						}
     						elseif( f_MValue( 'SELECT * FROM player_wedding_bets WHERE p1 = '.$Wife->player_id ) ) // Если девушку уже ангажируют
     						{
     							echo '<script>document.write( '.$Wife->Nick( ).' )</script> уже находится в состоянии принятия решения в отношении кого-то другого.';
     						}
     						else
     						{
	     						// Добавляем в желающих пожениться эту пару
   	  						f_MQuery( 'INSERT INTO player_wedding_bets( p0, p1 ) VALUES ( '.$player->player_id.', '.$wifeId.' )' );
	
								// Оповещаем суженную
								$prtext = 'Дорогая <b>'.$Wife->login.'</b>, в мире нет никого для меня ближе, чем ты. Я буду счастлив провести с тобой остаток своей жизни. Ты станешь моей женой?<br /><br /><img src="/images/wedring.gif" />';								
								f_MQuery( "INSERT INTO post( sender_id, receiver_id, title, content, money, np, deadline ) VALUES ( 69055, $Wife->player_id, 'Предложение руки и сердца от ".$player->login."', '$prtext', '0', '0', '0' )" );
	
     							echo '<script>document.write( '.$Wife->Nick( ).' )</script> получила твоё предложение пожениться. Подожди, пока она согласится : )<br /><a href="/game.php">Проверить, может уже?</a>';
     						}
     					}
					}
					else // Если имя жены не указывал
					{
						// Предлагаем ввести имя жены
						?>
						<script>
							function doMarry( )
							{
								var wifename = document.getElementById( 'wifename' ).value;
								if( !wifename )
								{
									alert( 'Нужно указать имя невесты : )' );
									return;
								}							
								
								document.location.href = '/game.php?wifename=' + wifename;
							}
						</script>
						<table cellpaddgin="0" cellspacing="0">
  							<tr>
  								<td style="padding-right: 5px;">Скажи имя невесты: <input type="text" id="wifename" class="c_btn" /></td>
  								<td style="background: url(/images/top/f.png); width: 92px; height: 21px; text-align: center; cursor: pointer;" onclick="doMarry( )">Женюсь!</td>
	  						</tr>
  						</table>
						<?				
					}
				}
				else // Если уже подал заявление
				{
					$Wife = new Player( f_MValue( 'SELECT p1 FROM player_wedding_bets WHERE p0 = '.$player->player_id ) ); // Создаём экземпляр жены
					
					// Проверяем, согласна ли самочка
					if( f_MValue( 'SELECT moo FROM player_wedding_bets WHERE p0 = '.$player->player_id ) == 0 )
					{
						echo '<script>document.write( '.$Wife->Nick( ).' )</script> в процессе принятия решения.<br>';
//						echo "<a href='game.php?cancel1=1'>Отказаться от свадьбы</a>";
					}
					else
					{
						echo '<script>document.write( '.$Wife->Nick( ).' )</script> приняла ваше предложение! Проздравляем!<br />Теперь зовите священника, пусть он скрепит вас брачными узами!';
					}
					
				}
			}
			else // Если квест на женитьбу не проходил
			{
				if( $player->sex == 1 ) // А вдруг вообще самка?
				{
					if( $pid = f_MValue( 'SELECT p0 FROM player_wedding_bets WHERE p1 = '.$player->player_id ) )
					{
						// Если самочку ожидает брак

						$Guy = new Player( $pid ); // Экземпляр счастливца
						
						if( f_MValue( 'SELECT moo FROM player_wedding_bets WHERE p1 = '.$player->player_id ) == 1 )
						{
							echo 'Ты скоро станешь женой <script>document.write( '.$Guy->Nick( ).' )</script><br />Осталось дождаться, пока священник скрепит вас брачными узами.';
						}						
						elseif( isset( $_GET['marry'] ) == true )	// Приняла ли она решение?
						{
							// Решение принято
							
							// Она согласна?
							if( $_GET['marry'] == 'true' )
							{
								// Да
								f_MQuery( 'UPDATE player_wedding_bets SET moo = 1 WHERE p1 = '.$player->player_id );
								$Guy->syst2( $player->login.' приняла ваше предложение пожениться!' );
							}
							else
							{
								// Нет
								f_MQuery( 'DELETE FROM player_wedding_bets WHERE p1 = '.$player->player_id );
								$Guy->syst2( $player->login.' отказала вам в браке :(' );								
								$Guy->SetTrigger( 2011, 0 );				
							}
						}
						else
						{
							// Ещё телится
							echo '<script>document.write( '.$Guy->Nick( ).' )</script> предлагает вам пожениться.<br />';
							?>
							<br />
							<table>
  								<tr>
  									<td style="background: url(/images/top/f.png); width: 92px; height: 21px; text-align: center; cursor: pointer;" onclick="document.location.href='/game.php?marry=true'">Согласиться</td>
  									<td style="background: url(/images/top/f.png); width: 92px; height: 21px; text-align: center; cursor: pointer;" onclick="document.location.href='/game.php?marry=false'">Отказаться</td>
	  							</tr>
  							</table>
							<?
						}
					}
				}
			}
		}
		
		// Если ещё не дарил кольцо
		if( f_MValue( 'SELECT * FROM player_weddings WHERE p0 = '.$player->player_id ) && !$player->HasTrigger( 2011 ) )
		{
			// Если хочет купить колечек
			if( isset( $_GET['marryRing'] ) == true )
			{
				if( $_GET['marryRing'] == 1 )
				{
					if( $player->SpendMoney( 20000 ) )
					{
						// Купил два простых кольца
						$player->AddItems( 69380, 1 ); // Обычное кольцо жениха
						$player->AddItems( 69379, 1 ); // Обычное кольцо невесты
						$player->SetTrigger( 2011 );   // Отмечаем, что кольца уже купил
						echo '<br />Кольца у тебя в инвентаре!';
					}
					else
					{
						echo '<br />У тебя не хватает дублонов';								
					}
				}
				else
				{
					// Купил два навороченных кольца
					if( $player->SpendUMoney( 20 ) )
					{
						$player->AddItems( 69381, 1 ); // Дорогое кольцо жениха
						$player->AddItems( 69382, 1 ); // Дорогое кольцо невесты
						$player->SetTrigger( 2011 );   // Отмечаем, что кольца уже купил
						echo '<br />Кольца у тебя в инвентаре!';
					}
					else
					{
						echo '<br />У тебя не хватает талантов';								
					}
				}
			}
			else
			{
				// Если пока не хочет, значит надо предложить
				?>
				<br /><br />
				<b>Ты можешь купить любую пару колец!</b>
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
						<td style="background: url(/images/top/f.png); width: 92px; height: 21px; text-align: center; cursor: pointer;" onclick="document.location.href='/game.php?marryRing=1'"><b>Купить</b></td>
						<td style="background: url(/images/top/f.png); width: 92px; height: 21px; text-align: center; cursor: pointer;" onclick="document.location.href='/game.php?marryRing=2'"><b>Купить</b></td>
					</tr>
				</table>
				<?
			}
		}
		
		// Блок могущих поженить
		if( !f_MValue( 'SELECT * FROM player_wedding_bets WHERE p'.$player->sex.' = '.$player->player_id ) && f_MValue( "SELECT level FROM clan_buildings WHERE building_id=1 AND clan_id={$player->clan_id}" ) > 10 ) // Если левел алтаря позволяет женить и женитель не женится сам
		{
			require_once( 'clan.php' ); // Подключаем функцию получения прав игрока в Ордене
			
			// Может ли конкретно этот игрок женить?
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
  							$plr1->syst2( "Вы только что связали себя узами брака с <b>{$plr2->login}</b>! Поздравляем!!!" );
  							$plr2->syst2( "Вы только что связали себя узами брака с <b>{$plr1->login}</b>! Поздравляем!!!" );
							glashSay( "Только что {$plr1->login} и {$plr2->login} связали себя узами брака! Церемонию провёл {$player->login}." );
						}
					}
				}

				// Выводим желающих пожениться
				$res = f_MQuery( 'SELECT p0, p1 FROM player_wedding_bets WHERE moo=1' );
				$st = '<br />';
				while( $arr = f_MFetch( $res ) )
				{
					$login1 = f_MValue( "SELECT login FROM characters WHERE player_id=$arr[p0]" );
					$login2 = f_MValue( "SELECT login FROM characters WHERE player_id=$arr[p1]" );
					$st .= "Игроки $login1 и $login2 хотят пожениться. <a href=game.php?marry=$arr[p0]>Поженить их</a><br /><br />";
				}
				echo $st;
			}
		}
		
		// Выводим рейтинг влюблённых пар
		echo '<br />Десятка самых влюблённых пар!<br /><table border="1" style="background-color: e1c7a4;" celpadding="3px;"><tbody>';
		$counter = 0; // Счётчик счастливых пар		
		$bestLovers = f_MQuery( 'SELECT p0,p1,best_time FROM labyrinth_of_love WHERE best_time < 3600 ORDER BY best_time LIMIT 10' );
		while( $pair = f_MFetch( $bestLovers ) )
		{
			$counter ++;
			
			$He = new Player( $pair[p0] );
			$She = new Player( $pair[p1] );
			
			echo '<tr><td style="background:url(/images/bg.gif);">'.rome_number( $counter ).'</td><td style="background: url(/images/bg.gif);"><script>document.write( '.$He->Nick( ).' )</script> и <script>document.write( '.$She->Nick( ).' )</script></td><td style="background: url(/images/bg.gif);">'.Date( 'i:s', $pair[best_time] ).'</td></tr>';
		}
		echo '</tbody></table>';
	}
	else
	{
		echo "Вы до сих пор не выбрали свою стихию и слишком невзрачен Ваш наряд.<br>Вряд ли для свадьбы годится серый цвет.";
	}
?>