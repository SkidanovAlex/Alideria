<?
/* @author = undefined
 * @date = 19 ������� 2011
 * @about = �������� �����
 */
 
	// �������� ����, ����� �� �����
	$wifeId = f_MValue( 'SELECT p'.( 1 - $player->sex ).' FROM player_weddings WHERE p'.$player->sex.' = '.$player->player_id );
	
	if( $wifeId ) // ���� ����� {� ��-�������� ���� ������������� �����������, ��� ����� �������������� ����� ����}
	{
		$Wife = new Player( $wifeId );
		
		echo '<br /><div id="GameStatus"></div>';

		// �������� �� �������� �� ��������
		$lol = f_MFetch( f_MQuery( 'SELECT * FROM labyrinth_of_love WHERE p'.$player->sex.' = '.$player->player_id ) );

		// ���� ��� ��� �� ��������� ���� ������
		if( !$lol )
		{
			// �������������� ���������� � ���� � ����������� ��-���������
			f_MQuery( 'INSERT INTO labyrinth_of_love( p'.$player->sex.', p'.$Wife->sex.', level, status ) VALUES( '.$player->player_id.', '.$Wife->player_id.', 1, 0 )' );
			f_MQuery( 'INSERT INTO lol_players( player_id, posX, posY ) VALUES( '.$player->player_id.', 6, 0 )' );
			f_MQuery( 'INSERT INTO lol_players( player_id, posX, posY ) VALUES( '.$Wife->player_id.', 6, 0 )' );
		}

		?>
		<div id="GameArea" style="background: url(/images/bg.gif); position: absolute; left: 273px; top: 47px; display: none;">
		<?
			// ��������� DOM-������� ���������
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

			// ����� ������� � ��������
			var isBegin = false;// �������, ����������, ��� ���� ��������
			function GameProcess( )
			{
				// ���� ���� ��� ��������, �� ��� ��� �� ����������
				if( GameArea.style.display == 'none' && isBegin == true )
				{
					Query( 'begin' );
				}
				// �� ���� ��������� ���������
				else
				{
					Query( '' );
				}
				
				setTimeout( 'GameProcess( )', 250 );
			}

			var level = [ ];
			function DrawGame( )
			{
				// ������ ���
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

			// ��������
			var hearts = [ ];
			var oldHearts = [ ]; 
			
			function DrawHearts( )
			{
				// ����������, ��� ���� ��� ��������
				if( GameArea.style.display == 'none' )
				{
					return;
				}

				// ����������, ��� ����� ������ ����������
				if( hearts.length != oldHearts.length )
				{
					// �������� ��� ������������ ������
					for( var i = 0; i < oldHearts.length; i ++ )
					{
						var heartImage = document.getElementById( 'coord' + oldHearts[i][0] + 'x' + oldHearts[i][1] );
						heartImage.src = '/images/labyrinthOfLove/ground/1.png';
					}				
				
					// ��������� ����� ������
					for( var i = 0; i < hearts.length; i ++ )
					{
						var heartImage = document.getElementById( 'coord' + hearts[i][0] + 'x' + hearts[i][1] );
						heartImage.src = '/images/labyrinthOfLove/heart.gif';
					}
					
					oldHearts = hearts;
				}
			}			
					
			// ��������� ������������� ��������������
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
					
			// ���� �������
			function moveAvatar( Gender )
			{
				// ����������, ��� ���� ��� ��������
				if( GameArea.style.display == 'none' )
				{
					return;				
				}
				
				var avatar = document.getElementById( Gender + 'Avatar' );
				var avatarCoords = [ ( avatar.offsetTop - 47 ) / 25, ( avatar.offsetLeft - 273 ) / 25 ];
				var trueAvatarCoords = ( Gender == myGender ) ? myCoords : wifeCoords;			
				
				// ����� �� ���������?
				if( avatarCoords[0] != trueAvatarCoords[0] || avatarCoords[1] != trueAvatarCoords[1] )
				{
					// ������������ ��������
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
					
					// �������������� ��������
					if( avatarCoords[1] < trueAvatarCoords[1] )
					{
						// �������� ������
						avatar.style.left = parseInt( avatar.style.left ) + 1 + 'px';
						if( avatar.src != '/images/labyrinthOfLove/' + Gender + '/right.gif' )
						{
							avatar.src = '/images/labyrinthOfLove/' + Gender + '/right.gif';
						}
					}
					else if( avatarCoords[1] > trueAvatarCoords[1] )
					{
						// �������� �����
						avatar.style.left = parseInt( avatar.style.left ) - 1 + 'px';
						if( avatar.src != '/images/labyrinthOfLove/' + Gender + '/left.gif' )
						{
							avatar.src = '/images/labyrinthOfLove/' + Gender + '/left.gif';
						}
					}
					
					// ������ ��������� �������� ��������
					setTimeout( 'moveAvatar( "' + Gender + '" );', 2 );
				}
			}
					
			function RedrawCoord( CoordX, CoordY, NewType )
			{
				var Coord = document.getElementById( 'coord' + CoordX + 'x' + CoordY );
				
				if( !Coord ) return;
				Coord.src = '/images/labyrinthOfLove/ground/' + NewType + '.png';					
			}
			
			// ������
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
				// ���������, ��� �� ������ ����
				if( isBegin == false )
				{
					return;				
				}
				
				// ��������� �����
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
				
				// ���������� �����
				GameTimer.innerHTML = ( ( hours ) ? hours + ':' : '' ) + ( ( minutes > 9 ) ? minutes : '0' + minutes ) + ':' + ( ( seconds > 9 ) ? seconds : '0' + seconds );
				
				// ��������� ��� ����� �������, �� ������
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
		echo '��������� ����� �������� ������ ������� ���������.';	
	}
?>