<?
/* @author = undefined
 * @date = 19 февраля 2011
 * @about = Лабиринт Любви
 */
 
	// Получаем инфу, женат ли игрок
	$wifeId = f_MValue( 'SELECT p'.( 1 - $player->sex ).' FROM player_weddings WHERE p'.$player->sex.' = '.$player->player_id );
	
	if( $wifeId ) // Если женат {Я по-прежнему пишу шовинистичные комментарии, так проще воспринимается лично мною}
	{
		$Wife = new Player( $wifeId );
		
		echo '<br /><div id="GameStatus"></div>';

		// Забираем их значения из таблички
		$lol = f_MFetch( f_MQuery( 'SELECT * FROM labyrinth_of_love WHERE p'.$player->sex.' = '.$player->player_id ) );

		// Если они ещё не приходили сюда доселе
		if( !$lol )
		{
			// Инициализируем счастливых в Базе с параметрами по-умолчанию
			f_MQuery( 'INSERT INTO labyrinth_of_love( p'.$player->sex.', p'.$Wife->sex.', level, status ) VALUES( '.$player->player_id.', '.$Wife->player_id.', 1, 0 )' );
			f_MQuery( 'INSERT INTO lol_players( player_id, posX, posY ) VALUES( '.$player->player_id.', 6, 0 )' );
			f_MQuery( 'INSERT INTO lol_players( player_id, posX, posY ) VALUES( '.$Wife->player_id.', 6, 0 )' );
		}

		?>
		<div id="GameArea" style="background: url(/images/bg.gif); position: absolute; left: 273px; top: 47px; display: none;">
		<?
			// Генерация DOM-матрицы лабиринта
			for( $i = 0; $i < 13; ++ $i )
			{
				for( $j = 0; $j < 23; ++ $j )
				{
					echo '<img src="/images/labyrinthOfLove/ground/0.png" id="coord'.$i.'x'.$j.'" style="width: 25px; height: 25px; border: 0px;" />';
				}
				echo '<br />';
			}
		?>
		</div>
		<div id="GameTimer" style="position: absolute; z-index: 1004; left: 273px; top: 378px; font-weight: bold; color: #651010; display: none;"></div>
		<img id="maleAvatar" src="/images/labyrinthOfLove/male/default.gif" style="width: 25px; height: 25px; border: 0px; position: absolute; display: none; <?=( $player->sex == 0 ) ? 'z-index: 1001;' : 'z-index: 1000;'?>" />
		<img id="femaleAvatar" src="/images/labyrinthOfLove/female/default.gif" style="width: 25px; height: 25px; border: 0px; position: absolute; display: none; <?=( $player->sex == 1 ) ? 'z-index: 1001;' : 'z-index: 1000;'?>" />
		<script>
			function Query( Data )
			{
				query( 'ajaxQuery.php?module=labyrinthOfLove', Data );
			}
					
			var GameStatus = document.getElementById( 'GameStatus' );
			var GameArea = document.getElementById( 'GameArea' );

			// Обмен данными с сервером
			var isBegin = false;// Триггер, отмечающий, что игра началась
			function GameProcess( )
			{
				// Если игра уже началась, но это ещё не отображено
				if( GameArea.style.display == 'none' && isBegin == true )
				{
					Query( 'begin' );
				}
				// Во всех остальных ситуациях
				else
				{
					Query( '' );
				}
				
				setTimeout( 'GameProcess( )', 250 );
			}

			var level = [ ];
			function DrawGame( )
			{
				// Рисуем фон
				for( var i = 0; i < level.length; i ++ )
				{
					for( var j = 0; j < level[i].length; j ++ )
					{
						document.getElementById( 'coord' + i + 'x' + j ).src = '/images/labyrinthOfLove/ground/' + level[i][j] + '.png';
					}
				}

				GameArea.style.width = document.body.offsetWidth - 275 + 'px';
				GameArea.style.display = '';
			}

			// Сердчеки
			var hearts = [ ];
			var oldHearts = [ ]; 
			
			function DrawHearts( )
			{
				// Убеждаемся, что игра уже началась
				if( GameArea.style.display == 'none' )
				{
					return;
				}

				// Убеждаемся, что число сердец изменилось
				if( hearts.length != oldHearts.length )
				{
					// Скрываем уже отрисованные сердца
					for( var i = 0; i < oldHearts.length; i ++ )
					{
						var heartImage = document.getElementById( 'coord' + oldHearts[i][0] + 'x' + oldHearts[i][1] );
						heartImage.src = '/images/labyrinthOfLove/ground/1.png';
					}				
				
					// Проявляем новые сердца
					for( var i = 0; i < hearts.length; i ++ )
					{
						var heartImage = document.getElementById( 'coord' + hearts[i][0] + 'x' + hearts[i][1] );
						heartImage.src = '/images/labyrinthOfLove/heart.gif';
					}
					
					oldHearts = hearts;
				}
			}			
					
			// Отрисовка анимированных сперматозоидов
			var myCoords = [ 0, 0 ];
			var wifeCoords = [ 0, 0 ];
			var myGender = '<?=( $player->sex == 0 ) ? 'male' : 'female' ?>';
			var wifeGender = '<?=( $Wife->sex == 0 ) ? 'male' : 'female' ?>';
			var myAvatar = document.getElementById( myGender + 'Avatar' );
			var wifeAvatar = document.getElementById( wifeGender + 'Avatar' );					
			function DrawAvatars( )
			{
				myAvatar.style.left = 273 + 25 * myCoords[1] + 'px';
				myAvatar.style.top = 47 + 25 * myCoords[0] + 'px';
				wifeAvatar.style.left = 273 + 25 * wifeCoords[1] + 'px';
				wifeAvatar.style.top = 47 + 25 * wifeCoords[0] + 'px';
				
				myAvatar.style.display = '';
				wifeAvatar.style.display = '';						
			}
					
			// Полёт Аватара
			function moveAvatar( Gender )
			{
				// Убеждаемся, что игра уже началась
				if( GameArea.style.display == 'none' )
				{
					return;				
				}
				
				var avatar = document.getElementById( Gender + 'Avatar' );
				var avatarCoords = [ ( avatar.offsetTop - 47 ) / 25, ( avatar.offsetLeft - 273 ) / 25 ];
				var trueAvatarCoords = ( Gender == myGender ) ? myCoords : wifeCoords;			
				
				// Нужно ли двигаться?
				if( avatarCoords[0] != trueAvatarCoords[0] || avatarCoords[1] != trueAvatarCoords[1] )
				{
					// Вертикальное движение
					if( avatarCoords[0] < trueAvatarCoords[0] )
					{
						avatar.style.top = parseInt( avatar.style.top ) + 1 + 'px';
						if( avatar.src != '/images/labyrinthOfLove/' + Gender + '/default.gif' )
						{
							avatar.src = '/images/labyrinthOfLove/' + Gender + '/default.gif';
						}
					}
					else if( avatarCoords[0] > trueAvatarCoords[0] )
					{
						avatar.style.top = parseInt( avatar.style.top ) - 1 + 'px';
						if( avatar.src != '/images/labyrinthOfLove/' + Gender + '/default.gif' )
						{
							avatar.src = '/images/labyrinthOfLove/' + Gender + '/default.gif';
						}								
					}
					
					// Горизонтальное движение
					if( avatarCoords[1] < trueAvatarCoords[1] )
					{
						// Движение вправо
						avatar.style.left = parseInt( avatar.style.left ) + 1 + 'px';
						if( avatar.src != '/images/labyrinthOfLove/' + Gender + '/right.gif' )
						{
							avatar.src = '/images/labyrinthOfLove/' + Gender + '/right.gif';
						}
					}
					else if( avatarCoords[1] > trueAvatarCoords[1] )
					{
						// Движение влево
						avatar.style.left = parseInt( avatar.style.left ) - 1 + 'px';
						if( avatar.src != '/images/labyrinthOfLove/' + Gender + '/left.gif' )
						{
							avatar.src = '/images/labyrinthOfLove/' + Gender + '/left.gif';
						}
					}
					
					// Запуск следующей итерации движения
					setTimeout( 'moveAvatar( "' + Gender + '" );', 2 );
				}
			}
					
			function RedrawCoord( CoordX, CoordY, NewType )
			{
				var Coord = document.getElementById( 'coord' + CoordX + 'x' + CoordY );
				
				if( !Coord ) return;
				Coord.src = '/images/labyrinthOfLove/ground/' + NewType + '.png';					
			}
			
			// Таймер
			var beginTime = 0;
			var serverTime = 0;
			var GameTimer = document.getElementById( 'GameTimer' );
			function DrawTimer( )
			{
				GameTimer.style.display = '';
				
				StepTimer( );
			}
			
			function StepTimer( )
			{
				// Проверяем, идёт ли сейчас игра
				if( isBegin == false )
				{
					return;				
				}
				
				// Вычисляем время
				var date = new Date( );
				var timestamp = serverTime - beginTime;
				
				var hours = Math.round( timestamp / 3600 - 0.5 );
				var minutes = Math.round( ( timestamp / 60 ) % 60 - 0.5 );
				var seconds = Math.round( timestamp % 60 );
				
				if( seconds == 60 )
				{
					seconds = 0;
					minutes ++;
				}
				
				// Отображаем время
				GameTimer.innerHTML = ( ( hours ) ? hours + ':' : '' ) + ( ( minutes > 9 ) ? minutes : '0' + minutes ) + ':' + ( ( seconds > 9 ) ? seconds : '0' + seconds );
				
				// Следующий шаг через секунду, всё просто
				serverTime ++;
				setTimeout( 'StepTimer( )', 1000 );
			}
					
			function sendRequest( )
			{
				Query( 'init' );
			}
					
			function confirmRequest( )
			{
				Query( 'confirm' );	
			}
					
			function goGoGo( changeX, changeY )
			{
				Query( 'go@' + changeX + '@' + changeY );
			}
			
			document.onkeydown = function( event )
			{
				var e = event || window.event;
				var k = e.keyCode;
				if( k == 37 ) goGoGo( 0, -1 );
				if( k == 39 ) goGoGo( 0, 1 );
				if( k == 38 ) goGoGo( -1, 0 );
				if( k == 40 ) goGoGo( 1, 0 );
			}
			
			GameProcess( );
		</script>
		<?
	}
	else
	{
		echo 'Лабиринты Любви доступны только женатым влюблённым.';	
	}
?>