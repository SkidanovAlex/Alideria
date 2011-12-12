<?
// Этот файл следует полностью переписать заново, как только до этого дойдут руки. Почему?
// ПОЧЕМУ?! ПОЧЕМУ?!! Да потому что стоит только начать его вычитывать, как можно совершить полный цикл фалломорфирования за одну десятую от одной сотой секунды!!11

	// Подгрузка библиотек функций и классов
	require_once( 'functions.php' );
	require_once( 'player.php' );

	// Ну да, у нас же чудеса с кодировкой
	header( 'Content-type: text/html; charset=windows-1251' );
	
	f_MConnect( );

	// Валидация сессии персонажа
	if( !check_cookie( ) )
	{
		die( 'alert( "Неверные настройки Cookie" ) ;' );
	}

	// Функция журналирования чат-сообщений @toRefact: сделать журналирование в БД, да и вообще - ИСПРАВИТЬ ЭТО УБОЖЕСТВО!!11
	function LogError2( $a, $b = false )
	{
		// Нет, я не могу не прокомментировать ещё одной строчкой - ЭТО, БЛЯДЬ, УПОРОТЫЙ КУСОК УПОРОТОГО ГОВНОКОДА, УПОРОТЫЙ, УПОРОТОГО, УПОРО.. @by undefined
		if( $b !== false ) $a .= ". Доп. информация: $b";
	
		$tm = date( 'd.m.Y H:i', time( ) );
		$s = $tm.": ".$_SERVER['PHP_SELF'].": ".getenv( "REMOTE_ADDR" )."(".getenv( "HTTP_X_FORWARDED_FOR" ).")".": "."[".$_COOKIE['c_id']."] ".$a."\n";
	
		$f = fopen(LOGS_PATH .  "log_chat.txt", "a" );
		fwrite( $f, $s );
		fclose( $f );
	}
	
	// С этим поцем нам предстоит иметь дело
	$player_id = $_COOKIE['c_id'];
	$Player = new Player( $_COOKIE['c_id'] );

	// Вот в это самое время
	$time = time( );
	
	// тактикал фейспальм. Здесь мы, всего лишь за 5 строчек, получаем $_GET['where'] в нужной кодировке..
	// даже не исправляю, чтобы было назидательно для всех, до рефакторинга
	$where = $HTTP_GET_VARS['where'];
	$where2 = iconv("UTF-8", "CP1251", $where );
	if( $where2 === false || $where != iconv("CP1251", "UTF-8", $where2 ) ) ;
	else $where = $where2;
	$where = f_MEscape(htmlspecialchars( $where ));
	
	// Получаем текст сообщения
	$msg = $_GET['msg'];
	$msg2 = iconv("UTF-8", "CP1251", $msg );
	if( $msg2 === false || $msg != iconv("CP1251", "UTF-8", $msg2 ) )
	{
		$msg = ( $msg );
	}
	else
	{
		$msg = ( $msg2 );
	}	
	$msg = htmlspecialchars( addslashes( substr( $msg, 0, 200 ) ) );

	// Защита от бага с системными сообщениями. Полная хуета, защищать надо было на чатсервере.
	$msg = str_replace( "\n", '', $msg );
	$msg = str_replace( "\r", '', $msg );
	
	// Обновляем информацию об игроке, когда от него был последний сигнал. Почему тут? А хуй его знает, это было сделано задолго до меня и нигде не документировано @by = undefined
	if (!f_MValue("SELECT last_ping FROM online WHERE player_id = ".$Player->player_id ))
		die("<script>window.top.location.href='index.php';</script>");
	f_MQuery( 'UPDATE online SET last_ping = '.$time.' WHERE player_id = '.$Player->player_id );

	// Запрет писать игрокам с молчанкой
	$permissions = f_MFetch( f_MQuery( "SELECT silence, silence_reason FROM player_permissions WHERE player_id={$player_id}" ) );
	if( $permissions && $permissions['silence'] > $time )
	{
		die( "<script>alert( 'На вас наложено молчание. Вы сможете писать в чат только через ".my_time_str( $permissions[0] - $time )."\\nПричина наказания: $permissions[1]' );</script>" );
	}

	// Программный запрет на отправление сообщений в торговый чат чаще, чем раз в 10 минут
	if( $where == 'Торговый' && substr( $msg, 0, 5 ) != '/del ' )
	{
		// Проверяем, писал ли меньше 10 минут назад
		if( $Player->GetValue( 1 ) > $time )
		{
			die( "<script>alert( 'Реклама разрешена один раз в десять минут.\\nТы сможешь рекламировать через ".my_time_str( $Player->GetValue( 1 ) - $time )."' );</script>" );
		}
		else
		{
			// Устанавливаем время, когда снова сможет писать - через 10 минут
			$Player->SetValue( 1, $time + 600 );		
		}
	}

	// Получаем информацию о постящем
	$res = f_MQuery( "SELECT login, nick_clr, text_clr, level FROM characters WHERE player_id = $player_id" );
	$arr = f_MFetch( $res );

	if( $arr[level] == 1 )
	{
		die( "<script>alert( 'Вы не сможете писать в чат, пока не достигнете второго уровня.\\nЧтобы освоиться в основных аспектах игры, следуйте Первым Шагам. Ответы на многие другие вопросы есть в Помощи (кнопка Помощь находится над списком игроков).' );</script>" );
	}
	$level = $arr['level'];
	$author = $arr[0];
	$nick_clr = $arr[nick_clr];
	$text_clr = $arr[text_clr];

	// Обрабатываем сообщение	
	$cmd_del = false;
	if( substr( $msg, 0, 5 ) == '/del ' or $msg == '/del' )
	{
		// Проверка, модератор ли удаляет
		$res = f_MQuery( "SELECT rank FROM player_ranks WHERE player_id=$player_id" );
		$arr = f_MFetch( $res );
		if( !$arr || $arr[0] == 0 )
		{
			die( '<script>alert( "Только модераторы могут удалять сообщения!" );</script>' );
		}
	
		LogError2( "Модератор $player_id удалил сообщение $msg" );
		$cmd_del = true;
	}

	$temp = mb_strtolower( ' '.$msg );
	// Зачистка от возможных обходов фильтров тегами
	$temp = str_replace( '(ж)', '', $temp );
	$temp = str_replace( '(!ж)', '', $temp );
	$temp = str_replace( '(к)', '', $temp );
	$temp = str_replace( '(!к)', '', $temp );
	$temp = str_replace( '(п)', '', $temp );
	$temp = str_replace( '(!п)', '', $temp );
	$temp = str_replace( '(з)', '', $temp );
	$temp = str_replace( '(!з)', '', $temp );

	$temp = str_replace( "бляш" , "test" , $temp );
	$temp = str_replace( "блях" , "test" , $temp );
	$temp = str_replace( "ё" , "е" , $temp );
	$temp = str_replace( "x" , "х" , $temp );
	$temp = str_replace( "e" , "е" , $temp );
	$temp = str_replace( "y" , "у" , $temp );
	$temp = str_replace( "Я" , "я" , $temp );


	$where = str_replace( "\n", "", $where );
	$where = str_replace( "\r", "", $where );

	// Фильтр мата
	if( $where != 'Орден' && strpos( $where, '@' ) !== 0 )
	{
		$mat = array( ' выеб', ' хуй', ' хуя', ' еб', ' пизд', ' бля', ' охуе', ' ниху', ' нехуя', ' нехуе', ' нехуй', ' пидор', ' пидар', 'долбоеб', 'далбаеб', 'долбаеб' );
		$mcount = count( $mat );
		for( $mi = 0; $mi < $mcount; ++ $mi )
		{
			// Поиск мата
			if( strpos( $temp, $mat[$mi] ) !== false )
			{
				$msg = 'Я дурак, ругаюсь матом, толстый, жирный, волосатый. Вместо ног - косые палки, и пятилетнего смекалка';

				// Добавление пятиминутной молчанки
				$res = f_MQuery( "SELECT * FROM player_permissions WHERE player_id = {$player_id}" );
				if( mysql_num_rows( $res ) == 0 )
				{
					f_MQuery( "INSERT INTO player_permissions ( player_id ) VALUES ( {$player_id} )" );
				}

				$tm = time( ) + 300;
				f_MQuery( "UPDATE player_permissions SET silence = $tm, silence_reason = 'Мат в чате' WHERE player_id = $player_id" );

				break;		
			}
		}
	}
	
	// channels and privates
	if( $where == 'Общий' )
	{
		$channel = 0;
		$private_to = 0;
	}
	elseif( $where == 'Орден' )
	{
		$channel = -1;
		$private_to = 0;
		
		/*$order_id = f_MValue( "SELECT `clan_id` FROM `characters` WHERE `player_id` = $_COOKIE[c_id]" );
		if( $order_id == 28 )
		{
			LogError( "Орденчат $order_id", $msg );	
		}*/
	}
	elseif( $where == 'Бой - Все' )
	{
		$channel = -2;
		$private_to = 0;
	}
	elseif( $where == 'Бой - Свои' )
	{
		$channel = -3;
		$private_to = 0;
	}
	elseif( $where == 'Торговый' )
	{
		$channel = -5;
		$private_to = 0;
	}
	elseif( $where == 'Первые Шаги' )
	{
		die( "<script>alert( 'Вы находитесь в комнате чата \"Первые Шаги\", в которой нельзя общаться. Для того чтобы поговорить с игроками, нажмите кнопку \"Общий\" в списке комнат над чатом.' );</script>" );
	}
	elseif( $where == 'Системные' )
	{
		die( "<script>alert( 'Нельзя писать в комнате, предназначенное для системных сообщений!' );</script>" );
	}
	elseif( $where[0] == '#' )
	{
		$channel = (int)substr( $where, 1 );
		$private_to = 0;
	}
	elseif( $where[0] == '@' )
	{
		$channel = (int)substr( $where, 1 );
		$channel += 1000000000;
		$private_to = 0;
	}
	else
	{
		$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$where'" );
		$arr = f_MFetch( $res );
		if( !$arr )
		{
			die( "<script>alert( 'Не удалось отослать приватное сообщение: персонажа $where не существует.' );</script>" );
		}
		$channel = 0;
		$private_to = $arr[0];
	}

	$tm = time( );

	if( $msg == '' ) return;

	if( substr( $msg, 0, 6 ) == '/room ' || substr( $msg, 0, 7 ) == '/proom ' )
	{
		$num = f_MValue( "SELECT count( channel_id ) FROM ch_channels WHERE player_id=$player_id" );
		if( $num >= 10 )
		{
			die( "<script>alert( 'Вы не можете находиться в более чем 10 комнатах одновременно!' );</script>" );
		}
		$arr = explode( ' ', $msg );
		$room_id = (int)$arr[1];
		if( $arr[0] == '/proom' )
		{
			$room_id += 1000000000;
		}
		if( $room_id >= 1000000000 )
		{
			if( !f_MValue( "SELECT access_level FROM ch_channel_access WHERE channel_id=$room_id and player_id = ".$player_id ) )
			{
				die( "<script>alert( 'У вас нет доступа в комнату ".($room_id-1000000000)."' );</script>" );
			}
		}
		elseif( $room_id < 1 || $room_id > 1000000000 )
		{
			die( "<script>alert( 'Номер комнаты должен быть между 1 и 1000000000.' );</script>" );
		}

		require_once( 'chat_channels_functions.php' );

		PlayerEnterChannel( $player_id, $room_id );

		if( $room_id < 1000000000 )
		{
			die( "<script>window.top.createPrivateRoom( '#{$room_id}' );</script>" );
		}
		else
		{
			die( "<script>window.top.createPrivateRoom( '@".($room_id-1000000000)."' );</script>" );
		}
	}
	elseif( substr( $msg, 0, 9 ) == '/protect ' )
	{
		if( $level < 4 )
		{
			die( "<script>alert( 'Только игроки 4 уровня и выше могут создавать закрытые комнаты.' );</script>" );
		}
		
		$room_id = 1000000000 + (int)trim( substr( $msg, 9 ) );

		if( f_MValue( "SELECT count( player_id ) FROM ch_channel_access WHERE player_id={$player_id} AND access_level = 100" ) >= 10 )
		{
			die( "<script>alert( 'У вас уже есть 10 комнат. Завести 11-ую нельзя.' );</script>" );
		}
		if( $room_id > 2000000000 || $room_id < 1000000001 )
		{
			die( "<script>alert( 'Номер закрытой комнаты не может быть больше 1000000000 или меньше 1!' );</script>" );
		}
		if( f_MValue( "SELECT access_level FROM ch_channel_access WHERE channel_id=$room_id and access_level = 100" ) )
		{
			die(  "<script>alert( 'У этой комнаты уже есть владелец. Укажите другой номер.' );</script>" );
		}

		f_MQuery( "INSERT INTO ch_channel_access ( channel_id, player_id, access_level ) VALUES ( $room_id, $player_id, 100 )" );

		require_once( 'chat_channels_functions.php' );
	
		PlayerEnterChannel( $player_id, $room_id );
	
		die( "<script>window.top.createPrivateRoom( '@".($room_id-1000000000)."' );</script>" );
	}
	elseif( $msg == '/myroom' )
	{
		$res = f_MQuery( "SELECT channel_id FROM ch_channel_access WHERE player_id=$player_id and access_level = 100" );
		$rooms = '';
		while( $arr = f_MFetch( $res ) )
		{
			$rooms .= ', '.($arr[0] - 1000000000);
		}
		$rooms = substr( $rooms, 2 );
		if( !$rooms )
		{
			die( "<script>alert( 'У вас нет своих комнат.' );</script>" );
		}
		else
		{
			die( "<script>alert( 'Номера ваших комнат: {$rooms}.\\nИспользуйте команду \"/proom номер_комнаты\" для входа в нужную комнату.' );</script>" );
		}
	}
	elseif( $msg == '/leaveall' )
	{
		include( 'chat_channels_functions.php' );
		$res = f_MQuery( "SELECT channel_id FROM ch_channels WHERE player_id=$player_id" );
		while( $arr = f_MFetch( $res ) )
		{
			PlayerLeaveChannel( $player_id, $arr[0] );
			if( $arr[0] < 1000000000 )
			{
				echo( "<script>window.top.closePrivateNamed( '#{$arr[0]}' );</script>" );
			}
			else
			{
				echo( "<script>window.top.closePrivateNamed( '@".($arr[0]-1000000000)."' );</script>" );
			}
		}
		
		die( );
	}
	elseif( $msg == '/leave' && $channel > 0 )
	{
		require_once( 'chat_channels_functions.php' );
		
		PlayerLeaveChannel( $player_id, $channel );
		if( $channel < 1000000000 )
		{
			die( "<script>window.top.closePrivateNamed( '#{$channel}' );</script>" );
		}
		else
		{
			die( "<script>window.top.closePrivateNamed( '@".($channel-1000000000)."' );</script>" );
		}
	}
	elseif( $msg == '/dice' )
	{
		$msg = '/me кидает кубик. Выпадает <b><font color="darkred">'.mt_rand( 1, 6 ).'</font></b>';
		//die( "<script>alert( 'Хватит кидать кубик' );</script>" );

	}
	elseif( substr( $msg, 0, 8 ) == '/invite ' )
	{
		if( $channel < 1000000000 )
		{
			die( "<script>alert( 'Приглашать можно только в закрытые комнаты!');</script>" );
		}
		if( $channel == 1000000515 or f_MValue( "SELECT access_level FROM ch_channel_access WHERE player_id=$player_id and channel_id = $channel and access_level = 100" ) )
		{
			$players = explode( ' ', $msg );
			$count = count( $players );
			if( $count <= 1 )
				die( "<script>alert( 'Укажите ники персонажей, которых вы хотите пригласить.' );</script>" );
	
			$tmp = 'login = "'.$players[1].'"';
			for( $i = 2; $i < $count; ++ $i )
			{
				$tmp .= ' or login = "'.$players[$i].'"';
			}
			$res = f_MQuery( "SELECT player_id FROM characters WHERE ".$tmp );
			while( $arr = f_MFetch( $res ) )
			{
				if( !f_MValue( "SELECT access_level FROM ch_channel_access WHERE player_id = ".$arr['player_id']." and channel_id = ".$channel ) )
				{
					f_MQuery( "INSERT INTO ch_channel_access ( channel_id, player_id, access_level ) VALUES ( $channel, ".$arr['player_id'].", 1 )" );
				}
			}

			die( "<script>alert( 'Перечисленные персонажи получили доступ в указанную комнату. Команда для входа \"/proom ".($channel-1000000000)."\"' );</script>" );
		}
		else
		{
			die( "<script>alert('Приглашать посетителей может только владелец комнаты!');</script>" );
		}
	}
	elseif( substr( $msg, 0, 6 ) == '/kick ' )
	{
		require_once( 'chat_channels_functions.php' );

		if( !f_MValue( "SELECT access_level FROM ch_channel_access WHERE player_id=$player_id and channel_id = $channel and access_level = 100" ) )
		{
			die( "Вы можете исключать персонажей только из своих закрытых комнат." );
		}

		$players = explode( ' ', $msg );
		$count = count( $players );
		if( $count <= 1 )
		{
			die( "<script>alert( 'Укажите имена персонажей, которых вы хотите лишить доступа.' );</script>" );
		}
		$tmp = 'login = "'.$players[1].'"';
		for( $i = 2; $i < $count; ++ $i )
			$tmp .= ' or login = "'.$players[$i].'"';
		$res = f_MQuery( "SELECT player_id FROM characters WHERE ".$tmp );
		while( $arr = f_MFetch( $res ) )
		{
			if( !f_MValue( "SELECT access_level FROM ch_channel_access WHERE player_id = ".$arr['player_id']." and channel_id = ".$channel." and access_level = 100" ) )
			{
				f_MQuery( "DELETE FROM ch_channel_access WHERE channel_id = ".$channel." and player_id = ".$arr['player_id'] );
				PlayerLeaveChannel( $arr['player_id'], $channel );
			}
		}
		die( "<script>alert( 'Перечисленные персонажи больше не имеют доступ в комнату.' );</script>" );

}
elseif( $msg == '/delete' )
{
	include( 'chat_channels_functions.php' );
	if( !f_MValue( "SELECT access_level FROM ch_channel_access WHERE player_id=$player_id and channel_id = $channel and access_level = 100" ) )
		die( "<script>alert( 'Вы можете удалять только свои закрытые комнаты' );</script>" );
	$res = f_MQuery( "SELECT player_id FROM ch_channel_access WHERE channel_id=$channel" );
	while( $arr = f_MFetch( $res ) )
	{
		PlayerLeaveChannel( $arr['player_id'], $channel );
	}
	//-- возможно, сюда стоит добавить функцию исключения персонажей из комнаты
	//-- я не знаю, можно ли это делать, если персонажа в ней уже нет, не вызовет ли это какой-нибудь сбой
	f_MQuery( "DELETE FROM ch_channel_access WHERE channel_id = ".$channel );
	die( "<script>window.top.closePrivateNamed( '@".($channel-1000000000)."' );</script>" );
}

require_once( 'textedit.php' );

// Обработка BB-кодов
$msg = process_str( $msg );

// @515-мод
if( $_GET['where'] == '@515' )
{
	if( mt_rand( 1, 50 ) == 1 )
	{
		$msg = 'Я дурак, ругаюсь матом, толстый, жирный, волосатый. Вместо ног - косые палки, и пятилетнего смекалка';	
	}
}

if (($channel==0 && $private_to==0) || $channel==-5);
else
if ($Player->clan_id==7 || f_MValue("SELECT clan_id FROM characters WHERE player_id=".$private_to)==7)
{
	$sock = socket_create(AF_INET, SOCK_STREAM, 0);
	socket_connect($sock, "127.0.0.1", 1100);
	$tm = date( "H:i", time( ) );
	if ($channel == -1)
	{
		$msg1 = "say\n"."<b>{$Player->login}:</b> "."{$msg}\n"."6825"."\n0\n1000100007\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login}: {$msg}\n";
	}
	elseif ($private_to!=0)
	{
		$plr_to = f_MValue("SELECT login FROM characters WHERE player_id=".$private_to);
		$msg1 = "say\n"."<b>{$Player->login}</b> для <b>{$plr_to}</b>: "."{$msg}\n"."6825"."\n0\n1000100007\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} для {$plr_to}: {$msg}\n";
	}
	else
	{
		$msg1 = "say\n"."<b>{$Player->login}</b> для канала {$channel}: "."{$msg}\n"."6825"."\n0\n1000100007\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} для канала {$channel}: {$msg}\n";
	}

	socket_write( $sock, $msg1, strlen($msg1) ); 
	socket_close( $sock );

	$f = fopen("/srv/www/alideria/logs/log_juk.txt", "a" );
	fwrite( $f, $msg2 );
	fclose( $f );
}
elseif ($Player->player_id == 21020 || $private_to == 21020)
{
	$sock = socket_create(AF_INET, SOCK_STREAM, 0);
	socket_connect($sock, "127.0.0.1", 1100);
	$tm = date( "H:i", time( ) );
	if ($private_to!=0)
	{
		$plr_to = f_MValue("SELECT login FROM characters WHERE player_id=".$private_to);
		$msg1 = "say\n"."<b>{$Player->login}</b> для <b>{$plr_to}</b>: "."{$msg}\n"."6825"."\n0\n1000067573\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} для {$plr_to}: {$msg}\n";
	}
	else
	{
		$msg1 = "say\n"."<b>{$Player->login}</b> для канала {$channel}: "."{$msg}\n"."6825"."\n0\n1000067573\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} для канала {$channel}: {$msg}\n";
	}

	socket_write( $sock, $msg1, strlen($msg1) ); 
	socket_close( $sock );

	$f = fopen("/srv/www/alideria/logs/log_duncan.txt", "a" );
	fwrite( $f, $msg2 );
	fclose( $f );
}
elseif ($Player->player_id == 136119 || $private_to == 136119)
{
	$sock = socket_create(AF_INET, SOCK_STREAM, 0);
	socket_connect($sock, "127.0.0.1", 1100);
	$tm = date( "H:i", time( ) );
	if ($private_to!=0)
	{
		$plr_to = f_MValue("SELECT login FROM characters WHERE player_id=".$private_to);
		$msg1 = "say\n"."<b>{$Player->login}</b> для <b>{$plr_to}</b>: "."{$msg}\n"."6825"."\n0\n1000136119\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} для {$plr_to}: {$msg}\n";
	}
	else
	{
		$msg1 = "say\n"."<b>{$Player->login}</b> для канала {$channel}: "."{$msg}\n"."6825"."\n0\n1000136119\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} для канала {$channel}: {$msg}\n";
	}

	socket_write( $sock, $msg1, strlen($msg1) ); 
	socket_close( $sock );

	$f = fopen("/srv/www/alideria/logs/log_gogol.txt", "a" );
	fwrite( $f, $msg2 );
	fclose( $f );
}
elseif ($Player->player_id == 159836 || $private_to == 159836)
{
	$sock = socket_create(AF_INET, SOCK_STREAM, 0);
	socket_connect($sock, "127.0.0.1", 1100);
	$tm = date( "H:i", time( ) );
	if ($private_to!=0)
	{
		$plr_to = f_MValue("SELECT login FROM characters WHERE player_id=".$private_to);
		$msg1 = "say\n"."<b>{$Player->login}</b> для <b>{$plr_to}</b>: "."{$msg}\n"."6825"."\n0\n1000159836\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} для {$plr_to}: {$msg}\n";
	}
	else
	{
		$msg1 = "say\n"."<b>{$Player->login}</b> для канала {$channel}: "."{$msg}\n"."6825"."\n0\n1000159836\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} для канала {$channel}: {$msg}\n";
	}

	socket_write( $sock, $msg1, strlen($msg1) ); 
	socket_close( $sock );

	$f = fopen("/srv/www/alideria/logs/log_sarg.txt", "a" );
	fwrite( $f, $msg2 );
	fclose( $f );
}
elseif ($Player->player_id == 220065 || $private_to == 220065)
{
	$sock = socket_create(AF_INET, SOCK_STREAM, 0);
	socket_connect($sock, "127.0.0.1", 1100);
	$tm = date( "H:i", time( ) );
	if ($private_to!=0)
	{
		$plr_to = f_MValue("SELECT login FROM characters WHERE player_id=".$private_to);
		$msg1 = "say\n"."<b>{$Player->login}</b> для <b>{$plr_to}</b>: "."{$msg}\n"."6825"."\n0\n1000220065\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} для {$plr_to}: {$msg}\n";
	}
	else
	{
		$msg1 = "say\n"."<b>{$Player->login}</b> для канала {$channel}: "."{$msg}\n"."6825"."\n0\n1000220065\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} для канала {$channel}: {$msg}\n";
	}

	socket_write( $sock, $msg1, strlen($msg1) ); 
	socket_close( $sock );

	$f = fopen("/srv/www/alideria/logs/log_ld.txt", "a" );
	fwrite( $f, $msg2 );
	fclose( $f );
}


// ---------------------
$sock = socket_create(AF_INET, SOCK_STREAM, 0);
socket_connect($sock, "127.0.0.1", 1100);
$tm = date( "H:i", time( ) );
$msg = "say\n{$msg}\n".( ( $cmd_del ) ? 1249423 : $player_id )."\n{$private_to}\n{$channel}\n{$tm}\n";
socket_write( $sock, $msg, strlen($msg) ); 
socket_close( $sock );
// ---------------------


?>
<script>parent.chat.q();location.href='/empty.gif';</script>