<?
// ���� ���� ������� ��������� ���������� ������, ��� ������ �� ����� ������ ����. ������?
// ������?! ������?!! �� ������ ��� ����� ������ ������ ��� ����������, ��� ����� ��������� ������ ���� ����������������� �� ���� ������� �� ����� ����� �������!!11

	// ��������� ��������� ������� � �������
	require_once( 'functions.php' );
	require_once( 'player.php' );

	// �� ��, � ��� �� ������ � ����������
	header( 'Content-type: text/html; charset=windows-1251' );
	
	f_MConnect( );

	// ��������� ������ ���������
	if( !check_cookie( ) )
	{
		die( 'alert( "�������� ��������� Cookie" ) ;' );
	}

	// ������� �������������� ���-��������� @toRefact: ������� �������������� � ��, �� � ������ - ��������� ��� ���������!!11
	function LogError2( $a, $b = false )
	{
		// ���, � �� ���� �� ����������������� ��� ����� �������� - ���, �����, �������� ����� ��������� ���������, ��������, ���������, �����.. @by undefined
		if( $b !== false ) $a .= ". ���. ����������: $b";
	
		$tm = date( 'd.m.Y H:i', time( ) );
		$s = $tm.": ".$_SERVER['PHP_SELF'].": ".getenv( "REMOTE_ADDR" )."(".getenv( "HTTP_X_FORWARDED_FOR" ).")".": "."[".$_COOKIE['c_id']."] ".$a."\n";
	
		$f = fopen(LOGS_PATH .  "log_chat.txt", "a" );
		fwrite( $f, $s );
		fclose( $f );
	}
	
	// � ���� ����� ��� ��������� ����� ����
	$player_id = $_COOKIE['c_id'];
	$Player = new Player( $_COOKIE['c_id'] );

	// ��� � ��� ����� �����
	$time = time( );
	
	// �������� ���������. ����� ��, ����� ���� �� 5 �������, �������� $_GET['where'] � ������ ���������..
	// ���� �� ���������, ����� ���� ������������ ��� ����, �� ������������
	$where = $HTTP_GET_VARS['where'];
	$where2 = iconv("UTF-8", "CP1251", $where );
	if( $where2 === false || $where != iconv("CP1251", "UTF-8", $where2 ) ) ;
	else $where = $where2;
	$where = f_MEscape(htmlspecialchars( $where ));
	
	// �������� ����� ���������
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

	// ������ �� ���� � ���������� �����������. ������ �����, �������� ���� ���� �� ����������.
	$msg = str_replace( "\n", '', $msg );
	$msg = str_replace( "\r", '', $msg );
	
	// ��������� ���������� �� ������, ����� �� ���� ��� ��������� ������. ������ ���? � ��� ��� �����, ��� ���� ������� ������� �� ���� � ����� �� ��������������� @by = undefined
	if (!f_MValue("SELECT last_ping FROM online WHERE player_id = ".$Player->player_id ))
		die("<script>window.top.location.href='index.php';</script>");
	f_MQuery( 'UPDATE online SET last_ping = '.$time.' WHERE player_id = '.$Player->player_id );

	// ������ ������ ������� � ���������
	$permissions = f_MFetch( f_MQuery( "SELECT silence, silence_reason FROM player_permissions WHERE player_id={$player_id}" ) );
	if( $permissions && $permissions['silence'] > $time )
	{
		die( "<script>alert( '�� ��� �������� ��������. �� ������� ������ � ��� ������ ����� ".my_time_str( $permissions[0] - $time )."\\n������� ���������: $permissions[1]' );</script>" );
	}

	// ����������� ������ �� ����������� ��������� � �������� ��� ����, ��� ��� � 10 �����
	if( $where == '��������' && substr( $msg, 0, 5 ) != '/del ' )
	{
		// ���������, ����� �� ������ 10 ����� �����
		if( $Player->GetValue( 1 ) > $time )
		{
			die( "<script>alert( '������� ��������� ���� ��� � ������ �����.\\n�� ������� ������������� ����� ".my_time_str( $Player->GetValue( 1 ) - $time )."' );</script>" );
		}
		else
		{
			// ������������� �����, ����� ����� ������ ������ - ����� 10 �����
			$Player->SetValue( 1, $time + 600 );		
		}
	}

	// �������� ���������� � ��������
	$res = f_MQuery( "SELECT login, nick_clr, text_clr, level FROM characters WHERE player_id = $player_id" );
	$arr = f_MFetch( $res );

	if( $arr[level] == 1 )
	{
		die( "<script>alert( '�� �� ������� ������ � ���, ���� �� ���������� ������� ������.\\n����� ��������� � �������� �������� ����, �������� ������ �����. ������ �� ������ ������ ������� ���� � ������ (������ ������ ��������� ��� ������� �������).' );</script>" );
	}
	$level = $arr['level'];
	$author = $arr[0];
	$nick_clr = $arr[nick_clr];
	$text_clr = $arr[text_clr];

	// ������������ ���������	
	$cmd_del = false;
	if( substr( $msg, 0, 5 ) == '/del ' or $msg == '/del' )
	{
		// ��������, ��������� �� �������
		$res = f_MQuery( "SELECT rank FROM player_ranks WHERE player_id=$player_id" );
		$arr = f_MFetch( $res );
		if( !$arr || $arr[0] == 0 )
		{
			die( '<script>alert( "������ ���������� ����� ������� ���������!" );</script>' );
		}
	
		LogError2( "��������� $player_id ������ ��������� $msg" );
		$cmd_del = true;
	}

	$temp = mb_strtolower( ' '.$msg );
	// �������� �� ��������� ������� �������� ������
	$temp = str_replace( '(�)', '', $temp );
	$temp = str_replace( '(!�)', '', $temp );
	$temp = str_replace( '(�)', '', $temp );
	$temp = str_replace( '(!�)', '', $temp );
	$temp = str_replace( '(�)', '', $temp );
	$temp = str_replace( '(!�)', '', $temp );
	$temp = str_replace( '(�)', '', $temp );
	$temp = str_replace( '(!�)', '', $temp );

	$temp = str_replace( "����" , "test" , $temp );
	$temp = str_replace( "����" , "test" , $temp );
	$temp = str_replace( "�" , "�" , $temp );
	$temp = str_replace( "x" , "�" , $temp );
	$temp = str_replace( "e" , "�" , $temp );
	$temp = str_replace( "y" , "�" , $temp );
	$temp = str_replace( "�" , "�" , $temp );


	$where = str_replace( "\n", "", $where );
	$where = str_replace( "\r", "", $where );

	// ������ ����
	if( $where != '�����' && strpos( $where, '@' ) !== 0 )
	{
		$mat = array( ' ����', ' ���', ' ���', ' ��', ' ����', ' ���', ' ����', ' ����', ' �����', ' �����', ' �����', ' �����', ' �����', '�������', '�������', '�������' );
		$mcount = count( $mat );
		for( $mi = 0; $mi < $mcount; ++ $mi )
		{
			// ����� ����
			if( strpos( $temp, $mat[$mi] ) !== false )
			{
				$msg = '� �����, ������� �����, �������, ������, ���������. ������ ��� - ����� �����, � ����������� ��������';

				// ���������� ������������ ��������
				$res = f_MQuery( "SELECT * FROM player_permissions WHERE player_id = {$player_id}" );
				if( mysql_num_rows( $res ) == 0 )
				{
					f_MQuery( "INSERT INTO player_permissions ( player_id ) VALUES ( {$player_id} )" );
				}

				$tm = time( ) + 300;
				f_MQuery( "UPDATE player_permissions SET silence = $tm, silence_reason = '��� � ����' WHERE player_id = $player_id" );

				break;		
			}
		}
	}
	
	// channels and privates
	if( $where == '�����' )
	{
		$channel = 0;
		$private_to = 0;
	}
	elseif( $where == '�����' )
	{
		$channel = -1;
		$private_to = 0;
		
		/*$order_id = f_MValue( "SELECT `clan_id` FROM `characters` WHERE `player_id` = $_COOKIE[c_id]" );
		if( $order_id == 28 )
		{
			LogError( "�������� $order_id", $msg );	
		}*/
	}
	elseif( $where == '��� - ���' )
	{
		$channel = -2;
		$private_to = 0;
	}
	elseif( $where == '��� - ����' )
	{
		$channel = -3;
		$private_to = 0;
	}
	elseif( $where == '��������' )
	{
		$channel = -5;
		$private_to = 0;
	}
	elseif( $where == '������ ����' )
	{
		die( "<script>alert( '�� ���������� � ������� ���� \"������ ����\", � ������� ������ ��������. ��� ���� ����� ���������� � ��������, ������� ������ \"�����\" � ������ ������ ��� �����.' );</script>" );
	}
	elseif( $where == '���������' )
	{
		die( "<script>alert( '������ ������ � �������, ��������������� ��� ��������� ���������!' );</script>" );
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
			die( "<script>alert( '�� ������� �������� ��������� ���������: ��������� $where �� ����������.' );</script>" );
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
			die( "<script>alert( '�� �� ������ ���������� � ����� ��� 10 �������� ������������!' );</script>" );
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
				die( "<script>alert( '� ��� ��� ������� � ������� ".($room_id-1000000000)."' );</script>" );
			}
		}
		elseif( $room_id < 1 || $room_id > 1000000000 )
		{
			die( "<script>alert( '����� ������� ������ ���� ����� 1 � 1000000000.' );</script>" );
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
			die( "<script>alert( '������ ������ 4 ������ � ���� ����� ��������� �������� �������.' );</script>" );
		}
		
		$room_id = 1000000000 + (int)trim( substr( $msg, 9 ) );

		if( f_MValue( "SELECT count( player_id ) FROM ch_channel_access WHERE player_id={$player_id} AND access_level = 100" ) >= 10 )
		{
			die( "<script>alert( '� ��� ��� ���� 10 ������. ������� 11-�� ������.' );</script>" );
		}
		if( $room_id > 2000000000 || $room_id < 1000000001 )
		{
			die( "<script>alert( '����� �������� ������� �� ����� ���� ������ 1000000000 ��� ������ 1!' );</script>" );
		}
		if( f_MValue( "SELECT access_level FROM ch_channel_access WHERE channel_id=$room_id and access_level = 100" ) )
		{
			die(  "<script>alert( '� ���� ������� ��� ���� ��������. ������� ������ �����.' );</script>" );
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
			die( "<script>alert( '� ��� ��� ����� ������.' );</script>" );
		}
		else
		{
			die( "<script>alert( '������ ����� ������: {$rooms}.\\n����������� ������� \"/proom �����_�������\" ��� ����� � ������ �������.' );</script>" );
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
		$msg = '/me ������ �����. �������� <b><font color="darkred">'.mt_rand( 1, 6 ).'</font></b>';
		//die( "<script>alert( '������ ������ �����' );</script>" );

	}
	elseif( substr( $msg, 0, 8 ) == '/invite ' )
	{
		if( $channel < 1000000000 )
		{
			die( "<script>alert( '���������� ����� ������ � �������� �������!');</script>" );
		}
		if( $channel == 1000000515 or f_MValue( "SELECT access_level FROM ch_channel_access WHERE player_id=$player_id and channel_id = $channel and access_level = 100" ) )
		{
			$players = explode( ' ', $msg );
			$count = count( $players );
			if( $count <= 1 )
				die( "<script>alert( '������� ���� ����������, ������� �� ������ ����������.' );</script>" );
	
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

			die( "<script>alert( '������������� ��������� �������� ������ � ��������� �������. ������� ��� ����� \"/proom ".($channel-1000000000)."\"' );</script>" );
		}
		else
		{
			die( "<script>alert('���������� ����������� ����� ������ �������� �������!');</script>" );
		}
	}
	elseif( substr( $msg, 0, 6 ) == '/kick ' )
	{
		require_once( 'chat_channels_functions.php' );

		if( !f_MValue( "SELECT access_level FROM ch_channel_access WHERE player_id=$player_id and channel_id = $channel and access_level = 100" ) )
		{
			die( "�� ������ ��������� ���������� ������ �� ����� �������� ������." );
		}

		$players = explode( ' ', $msg );
		$count = count( $players );
		if( $count <= 1 )
		{
			die( "<script>alert( '������� ����� ����������, ������� �� ������ ������ �������.' );</script>" );
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
		die( "<script>alert( '������������� ��������� ������ �� ����� ������ � �������.' );</script>" );

}
elseif( $msg == '/delete' )
{
	include( 'chat_channels_functions.php' );
	if( !f_MValue( "SELECT access_level FROM ch_channel_access WHERE player_id=$player_id and channel_id = $channel and access_level = 100" ) )
		die( "<script>alert( '�� ������ ������� ������ ���� �������� �������' );</script>" );
	$res = f_MQuery( "SELECT player_id FROM ch_channel_access WHERE channel_id=$channel" );
	while( $arr = f_MFetch( $res ) )
	{
		PlayerLeaveChannel( $arr['player_id'], $channel );
	}
	//-- ��������, ���� ����� �������� ������� ���������� ���������� �� �������
	//-- � �� ����, ����� �� ��� ������, ���� ��������� � ��� ��� ���, �� ������� �� ��� �����-������ ����
	f_MQuery( "DELETE FROM ch_channel_access WHERE channel_id = ".$channel );
	die( "<script>window.top.closePrivateNamed( '@".($channel-1000000000)."' );</script>" );
}

require_once( 'textedit.php' );

// ��������� BB-�����
$msg = process_str( $msg );

// @515-���
if( $_GET['where'] == '@515' )
{
	if( mt_rand( 1, 50 ) == 1 )
	{
		$msg = '� �����, ������� �����, �������, ������, ���������. ������ ��� - ����� �����, � ����������� ��������';	
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
		$msg1 = "say\n"."<b>{$Player->login}</b> ��� <b>{$plr_to}</b>: "."{$msg}\n"."6825"."\n0\n1000100007\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} ��� {$plr_to}: {$msg}\n";
	}
	else
	{
		$msg1 = "say\n"."<b>{$Player->login}</b> ��� ������ {$channel}: "."{$msg}\n"."6825"."\n0\n1000100007\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} ��� ������ {$channel}: {$msg}\n";
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
		$msg1 = "say\n"."<b>{$Player->login}</b> ��� <b>{$plr_to}</b>: "."{$msg}\n"."6825"."\n0\n1000067573\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} ��� {$plr_to}: {$msg}\n";
	}
	else
	{
		$msg1 = "say\n"."<b>{$Player->login}</b> ��� ������ {$channel}: "."{$msg}\n"."6825"."\n0\n1000067573\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} ��� ������ {$channel}: {$msg}\n";
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
		$msg1 = "say\n"."<b>{$Player->login}</b> ��� <b>{$plr_to}</b>: "."{$msg}\n"."6825"."\n0\n1000136119\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} ��� {$plr_to}: {$msg}\n";
	}
	else
	{
		$msg1 = "say\n"."<b>{$Player->login}</b> ��� ������ {$channel}: "."{$msg}\n"."6825"."\n0\n1000136119\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} ��� ������ {$channel}: {$msg}\n";
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
		$msg1 = "say\n"."<b>{$Player->login}</b> ��� <b>{$plr_to}</b>: "."{$msg}\n"."6825"."\n0\n1000159836\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} ��� {$plr_to}: {$msg}\n";
	}
	else
	{
		$msg1 = "say\n"."<b>{$Player->login}</b> ��� ������ {$channel}: "."{$msg}\n"."6825"."\n0\n1000159836\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} ��� ������ {$channel}: {$msg}\n";
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
		$msg1 = "say\n"."<b>{$Player->login}</b> ��� <b>{$plr_to}</b>: "."{$msg}\n"."6825"."\n0\n1000220065\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} ��� {$plr_to}: {$msg}\n";
	}
	else
	{
		$msg1 = "say\n"."<b>{$Player->login}</b> ��� ������ {$channel}: "."{$msg}\n"."6825"."\n0\n1000220065\n{$tm}\n";
		$msg2 = "{$tm}: {$Player->login} ��� ������ {$channel}: {$msg}\n";
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