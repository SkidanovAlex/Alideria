<?

header("Content-type: text/html; charset=windows-1251");

include_once( "functions.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "�������� ��������� Cookie" );

function LogError2( $a, $b = false )
{
	global $HTTP_COOKIE_VARS;
	global $_SERVER;
	
	if( $b !== false ) $a .= ". ���. ����������: $b";
	
	$tm = date( "d.m.Y H:i", time( ) );
	$s = $tm.": ".$_SERVER['PHP_SELF'].": ".getenv( "REMOTE_ADDR" )."(".getenv( "HTTP_X_FORWARDED_FOR" ).")".": "."[".$HTTP_COOKIE_VARS['c_id']."] ".$a."\n";
	
	$f = fopen(LOGS_PATH .  "log_chat.txt", "a" );
	fwrite( $f, $s );
	fclose( $f );
}
	
$player_id = $HTTP_COOKIE_VARS['c_id'];

$tm = time( );
f_MQuery( "UPDATE online SET last_ping = $tm WHERE player_id = {$player_id}" );

$arr2 = f_MFetch( f_MQuery( "SELECT silence, silence_reason FROM player_permissions WHERE player_id={$player_id}" ) );
if( $arr2 && $arr2[0] > time( ) )
{
	echo( "<script>alert( '�� ��� �������� ��������. �� ������� ������ � ��� ������ ����� ".my_time_str( $arr2[0] - time( ) )."\\n������� ���������: $arr2[1]' );</script>" );
	die( );
}

$res = f_MQuery( "SELECT login, nick_clr, text_clr, level FROM characters WHERE player_id = $player_id" );
$arr = f_MFetch( $res );

if( $arr[level] == 1 ) die( "<script>alert( '�� �� ������� ������ � ���, ���� �� ���������� ������� ������.\\n����� ��������� � �������� �������� ����, �������� ������ �����. ������ �� ������ ������ ������� ���� � ������ (������ ������ ��������� ��� ������� �������).' );</script>" );
$level = $arr['level'];

$author = $arr[0];
$nick_clr = $arr[nick_clr];
$text_clr = $arr[text_clr];
$msg = $HTTP_GET_VARS['msg'];
$msg2 = iconv("UTF-8", "CP1251", $msg );
if( $msg2 === false || $msg != iconv("CP1251", "UTF-8", $msg2 ) )
	$msg = ( $msg );
else $msg = ( $msg2 );

$msg = HtmlSpecialChars(addslashes(substr(($msg), 0, 200)));

$msg = str_replace( "\n", "", $msg );
$msg = str_replace( "\r", "", $msg );
$cmd_del = false;
if( substr( $msg, 0, 5 ) == '/del ' )
{
	$res = f_MQuery( "SELECT rank FROM player_ranks WHERE player_id=$player_id" );
	$arr = f_MFetch( $res );
	if( !$arr || $arr[0] == 0 ) die( '<script>alert( "������ ���������� ����� ������� ���������!" );</script>' );
	LogError2( "��������� {$player_id} ������ ��������� {$msg}" );
$cmd_del = true;
}
/*
 if (substr($msg '/end_combat') !== false)
{
		$player2 = new Player( $player_id );
		if( $player2->regime == 100 )
			$player2->LeaveCombat( );

}

  */


$temp = mb_strtolower( ' '.$msg );
$temp = str_replace( "����" , "test" , $temp );
$temp = str_replace( "����" , "test" , $temp );
$temp = str_replace( "�" , "�" , $temp );
$temp = str_replace( "x" , "�" , $temp );
$temp = str_replace( "e" , "�" , $temp );
$temp = str_replace( "y" , "�" , $temp );
$temp = str_replace( "�" , "�" , $temp );

$where = $HTTP_GET_VARS['where'];
$where2 = iconv("UTF-8", "CP1251", $where );
if( $where2 === false || $where != iconv("CP1251", "UTF-8", $where2 ) ) ;
else $where = $where2;
$where = f_MEscape(htmlspecialchars( $where ));


$where = str_replace( "\n", "", $where );
$where = str_replace( "\r", "", $where );


if( $where != '�����' && ( strpos( $temp, 'fdworld' ) !== false || strpos( $temp, 'n�olands' ) !== false || strpos( $temp, '�8355' ) !== false || strpos( $temp, 'p8355' ) !== false || strpos( $temp, ' ����' ) !== false || strpos( $temp, ' ����' ) !== false || strpos( $temp, ' ���' ) !== false || strpos( $temp, ' ���' ) !== false || strpos( $temp, ' ��' ) !== false || strpos( $temp, ' ����' ) !== false || strpos( $temp, ' ���' ) !== false || strpos( $temp, ' ����' ) !== false || strpos( $temp, ' ����' ) !== false || strpos( $temp, ' �����' ) !== false || strpos( $temp, ' �����' ) !== false || strpos( $temp, ' �����' ) !== false || strpos( $temp, ' �����' ) !== false || strpos( $temp, ' �����' ) !== false || strpos( $temp, ' �������' ) !== false || strpos( $temp, ' �������' ) !== false || strpos( $temp, ' �������' ) !== false || strpos( $temp, ' �������' ) !== false ) )
{
	//LogError( "����� '$msg' ������������� �������� ���� (where=$where)" );
	$msg = "� �����, ������� �����, �������, ������, ���������. ������ ��� - ����� �����, � ����������� ��������";
}
if( strpos( $temp, 'wglads' ) !== false || strpos( $msg, 'aloneisland' ) !== false || strpos( $msg, '�����������' ) !== false || strpos( $msg, 'klanz' ) !== false )
{
	//LogError( "����� '$msg' ������������� �������� ���� (where=$where)" );
	$msg = "� ������� ��������, � ����� ��������, ���� ������ ��������, ����������� �������. �������� ���� ��� � ���, ��� ��� ��� ��������, ���� ��� - ���, � �� ����� � ������ ��������.";
}

$temp = $msg;
if( strpos( $temp, 'alideria' ) === false && ( strpos( $temp, '���' ) !== false || strpos( $temp, '���' ) !== false || strpos( $temp, '���' ) !== false || strpos( $temp, '.ru' ) !== false || strpos( $temp, '.com' ) !== false || strpos( $temp, 'www' ) !== false || strpos( $temp, 'http://' ) !== false ) )
{
	LogError2( "����� '$msg' ������������� �� ��� (where=$where)" );
}

// channels and privates
if( $where == '�����' )
{
	$channel = 0;
	$private_to = 0;
}
else if( $where == '�����' )
{
	$channel = -1;
	$private_to = 0;
}
else if( $where == '��� - ���' )
{
	$channel = -2;
	$private_to = 0;
}
else if( $where == '��� - ����' )
{
	$channel = -3;
	$private_to = 0;
}

else if( $where == '��������' )
{
	$channel = -5;
	$private_to = 0;
}
else if( $where == '������ ����' )
{
	die( "<script>alert( '�� ���������� � ������� ���� \"������ ����\", � ������� ������ ��������. ��� ���� ����� ���������� � ��������, ������� ������ \"�����\" � ������ ������ ��� �����.' );</script>" );
}
else if( $where == '���������' )
{
	die( "<script>alert( '������ ������ � �������, ��������������� ��� ��������� ���������!' );</script>" );
}
else if( $where[0] == '#' )
{
	$channel = (int)substr( $where, 1 );
	$private_to = 0;
}
else if( $where[0] == '@' )
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

if( $msg === "" ) return;

if( substr( $msg, 0, 6 ) == '/room ' || substr( $msg, 0, 7 ) == '/proom ' )
{
	$num = f_MValue( "SELECT count( channel_id ) FROM ch_channels WHERE player_id=$player_id" );
	if( $num >= 10 )
		die( "<script>alert( '�� �� ������ ���������� � ����� ��� 10 �������� ������������!' );</script>" );
	$arr = explode( ' ', $msg );
	$room_id = (int)$arr[1];
	if( $arr[0] == '/proom' )
		$room_id += 1000000000;
	if( $room_id >= 1000000000 )
	{
		if( !f_MValue( "SELECT access_level FROM ch_channel_access WHERE channel_id=$room_id and player_id = ".$player_id ) )
			die( "<script>alert( '� ��� ��� ������� � ������� ".($room_id-1000000000)."' );</script>" );
	}
	else if( $room_id < 1 || $room_id > 1000000000 )
		die( "<script>alert( '����� ������� ������ ���� ����� 1 � 1000000000.' );</script>" );

	include( 'chat_channels_functions.php' );
	PlayerEnterChannel( $player_id, $room_id );
	if( $room_id < 1000000000 ) die( "<script>window.top.createPrivateRoom( '#{$room_id}' );</script>" );
	else die( "<script>window.top.createPrivateRoom( '@".($room_id-1000000000)."' );</script>" );
}
else if( substr( $msg, 0, 9 ) == '/protect ' )
{
	if( $level < 4 )
		die( "<script>alert( '������ ������ 4 ������ � ���� ����� ��������� �������� �������.' );</script>" );
	$room_id = 1000000000 + (int)trim( substr( $msg, 9 ) );
	if( f_MValue( "SELECT count( player_id ) FROM ch_channel_access WHERE player_id={$player_id} AND access_level = 100" ) >= 10 )
		die( "<script>alert( '� ��� ��� ���� 10 ������. ������� 11-�� ������.' );</script>" );
	if( $room_id > 2000000000 || $room_id < 1000000001 )
		die( "<script>alert( '����� �������� ������� �� ����� ���� ������ 1000000000 ��� ������ 1!' );</script>" );
	if( f_MValue( "SELECT access_level FROM ch_channel_access WHERE channel_id=$room_id and access_level = 100" ) )
		die(  "<script>alert( '� ���� ������� ��� ���� ��������. ������� ������ �����.' );</script>" );
	f_MQuery( "INSERT INTO ch_channel_access ( channel_id, player_id, access_level ) VALUES ( $room_id, $player_id, 100 )" );

	include( 'chat_channels_functions.php' );
	PlayerEnterChannel( $player_id, $room_id );
	die( "<script>window.top.createPrivateRoom( '@".($room_id-1000000000)."' );</script>" );
}
else if( $msg == '/myroom' )
{
	$res = f_MQuery( "SELECT channel_id FROM ch_channel_access WHERE player_id=$player_id and access_level = 100" );
	$rooms = '';
	while( $arr = f_MFetch( $res ) )
		$rooms .= ', '.($arr[0] - 1000000000);
	$rooms = substr( $rooms, 2 );
	if( !$rooms )
		die( "<script>alert( '� ��� ��� ����� ������.' );</script>" );
	else
		die( "<script>alert( '������ ����� ������: {$rooms}.\\n����������� ������� \"/proom �����_�������\" ��� ����� � ������ �������.' );</script>" );
}
else if( $msg == '/leaveall' )
{
	include( 'chat_channels_functions.php' );
	$res = f_MQuery( "SELECT channel_id FROM ch_channels WHERE player_id=$player_id" );
	while( $arr = f_MFetch( $res ) )
	{
		PlayerLeaveChannel( $player_id, $arr[0] );
		if( $arr[0] < 1000000000 ) echo( "<script>window.top.closePrivateNamed( '#{$arr[0]}' );</script>" );
		else echo( "<script>window.top.closePrivateNamed( '@".($arr[0]-1000000000)."' );</script>" );
	}
	die( );
}
else if( $msg == '/leave' && $channel > 0 )
{
		include( 'chat_channels_functions.php' );
		PlayerLeaveChannel( $player_id, $channel );
		if( $channel < 1000000000 ) die( "<script>window.top.closePrivateNamed( '#{$channel}' );</script>" );
		else die( "<script>window.top.closePrivateNamed( '@".($channel-1000000000)."' );</script>" );
}

else if( $msg == '/dice' )
	$msg = '/me ������ �����. �������� <b><font color="darkred">'.mt_rand( 1, 6 ).'</font></b>';
else if( substr( $msg, 0, 8 ) == '/invite ' )
{
	if( $channel < 1000000000 )
		die( "<script>alert( '���������� ����� ������ � �������� �������!');</script>" );
	if( f_MValue( "SELECT access_level FROM ch_channel_access WHERE player_id=$player_id and channel_id = $channel and access_level = 100" ) )
	{
		$players = explode( ' ', $msg );
		$count = count( $players );
		if( $count <= 1 )
			die( "<script>alert( '������� ���� ����������, ������� �� ������ ����������.' );</script>" );
		$tmp = 'login = "'.$players[1].'"';
		for( $i = 2; $i < $count; ++ $i )
			$tmp .= ' or login = "'.$players[$i].'"';
		$res = f_MQuery( "SELECT player_id FROM characters WHERE ".$tmp );
		while( $arr = f_MFetch( $res ) )
		{
			if( !f_MValue( "SELECT access_level FROM ch_channel_access WHERE player_id = ".$arr['player_id']." and channel_id = ".$channel ) )
				f_MQuery( "INSERT INTO ch_channel_access ( channel_id, player_id, access_level ) VALUES ( $channel, ".$arr['player_id'].", 1 )" );
		}
		die( "<script>alert( '������������� ��������� �������� ������ � ��������� �������. ������� ��� ����� \"/proom ".($channel-1000000000)."\"' );</script>" );
	}
	else
		die( "<script>alert('���������� ����������� ����� ������ �������� �������!');</script>" );
}
else if( substr( $msg, 0, 6 ) == '/kick ' )
{
	include( 'chat_channels_functions.php' );
	if( !f_MValue( "SELECT access_level FROM ch_channel_access WHERE player_id=$player_id and channel_id = $channel and access_level = 100" ) )
		die( "�� ������ ��������� ���������� ������ �� ����� �������� ������." );
		$players = explode( ' ', $msg );
		$count = count( $players );
		if( $count <= 1 )
			die( "<script>alert( '������� ����� ����������, ������� �� ������ ������ �������.' );</script>" );
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
else if( $msg == '/delete' )
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

require( "textedit.php" );

$msg = process_str( $msg );

//f_MQuery( "INSERT INTO ch_messages ( channel, message, author, time, fr, target, nick_clr, text_clr ) VALUES ( $channel, '$msg', '$author', $tm, $player_id, $private_to, '$nick_clr', '$text_clr' )" );


if ($player_id ==1417055 )
{
   LogErrorCustom( "MSG:" . $player_id . ":" . $msg);
}
if ($private_to ==1417055 )
{
   LogErrorCustom( "MSG:" . $player_id . ":" . $msg);
}


// ---------------------
$sock = socket_create(AF_INET, SOCK_STREAM, 0);
socket_connect($sock, "127.0.0.1", 1100);
$tm = date( "H:i", time( ) );
$msg = "say\n{$msg}\n" . ($cmd_del ? 1249423 : $player_id )  . "\n{$private_to}\n{$channel}\n{$tm}\n";
socket_write( $sock, $msg, strlen($msg) ); 
socket_close( $sock );
// ---------------------


?>

<script>
parent.chat.q();
location.href='empty.gif';
</script>
