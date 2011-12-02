<?

header("Content-type: text/html; charset=windows-1251");

include_once( "functions.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

function LogError2( $a, $b = false )
{
	global $HTTP_COOKIE_VARS;
	global $_SERVER;
	
	if( $b !== false ) $a .= ". Доп. информация: $b";
	
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
	echo( "<script>alert( 'На вас наложено молчание. Вы сможете писать в чат только через ".my_time_str( $arr2[0] - time( ) )."\\nПричина наказания: $arr2[1]' );</script>" );
	die( );
}

$res = f_MQuery( "SELECT login, nick_clr, text_clr, level FROM characters WHERE player_id = $player_id" );
$arr = f_MFetch( $res );

if( $arr[level] == 1 ) die( "<script>alert( 'Вы не сможете писать в чат, пока не достигнете второго уровня.\\nЧтобы освоиться в основных аспектах игры, следуйте Первым Шагам. Ответы на многие другие вопросы есть в Помощи (кнопка Помощь находится над списком игроков).' );</script>" );
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
	if( !$arr || $arr[0] == 0 ) die( '<script>alert( "Только модераторы могут удалять сообщения!" );</script>' );
	LogError2( "Модератор {$player_id} удалил сообщение {$msg}" );
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
$temp = str_replace( "бляш" , "test" , $temp );
$temp = str_replace( "блях" , "test" , $temp );
$temp = str_replace( "ё" , "е" , $temp );
$temp = str_replace( "x" , "х" , $temp );
$temp = str_replace( "e" , "е" , $temp );
$temp = str_replace( "y" , "у" , $temp );
$temp = str_replace( "Я" , "я" , $temp );

$where = $HTTP_GET_VARS['where'];
$where2 = iconv("UTF-8", "CP1251", $where );
if( $where2 === false || $where != iconv("CP1251", "UTF-8", $where2 ) ) ;
else $where = $where2;
$where = f_MEscape(htmlspecialchars( $where ));


$where = str_replace( "\n", "", $where );
$where = str_replace( "\r", "", $where );


if( $where != 'Орден' && ( strpos( $temp, 'fdworld' ) !== false || strpos( $temp, 'nеolands' ) !== false || strpos( $temp, 'р8355' ) !== false || strpos( $temp, 'p8355' ) !== false || strpos( $temp, ' выеб' ) !== false || strpos( $temp, ' выёб' ) !== false || strpos( $temp, ' хуй' ) !== false || strpos( $temp, ' хуя' ) !== false || strpos( $temp, ' еб' ) !== false || strpos( $temp, ' пизд' ) !== false || strpos( $temp, ' бля' ) !== false || strpos( $temp, ' охуе' ) !== false || strpos( $temp, ' ниху' ) !== false || strpos( $temp, ' нехуя' ) !== false || strpos( $temp, ' нехуе' ) !== false || strpos( $temp, ' нехуй' ) !== false || strpos( $temp, ' пидор' ) !== false || strpos( $temp, ' пидар' ) !== false || strpos( $temp, ' долбоеб' ) !== false || strpos( $temp, ' далбаеб' ) !== false || strpos( $temp, ' далбоеб' ) !== false || strpos( $temp, ' долбаеб' ) !== false ) )
{
	//LogError( "Фраза '$msg' заблокирована фильтром мата (where=$where)" );
	$msg = "Я дурак, ругаюсь матом, толстый, жирный, волосатый. Вместо ног - косые палки, и пятилетнего смекалка";
}
if( strpos( $temp, 'wglads' ) !== false || strpos( $msg, 'aloneisland' ) !== false || strpos( $msg, 'алонеисланд' ) !== false || strpos( $msg, 'klanz' ) !== false )
{
	//LogError( "Фраза '$msg' заблокирована фильтром мата (where=$where)" );
	$msg = "Я нелепый придурок, в реале недоумок, мозг меньше ослиного, достоинство куриное. Девчонки шлют все в лес, так чем еще заняться, удел мой - РВС, и по ночам в штанах копаться.";
}

$temp = $msg;
if( strpos( $temp, 'alideria' ) === false && ( strpos( $temp, 'БАГ' ) !== false || strpos( $temp, 'Баг' ) !== false || strpos( $temp, 'баг' ) !== false || strpos( $temp, '.ru' ) !== false || strpos( $temp, '.com' ) !== false || strpos( $temp, 'www' ) !== false || strpos( $temp, 'http://' ) !== false ) )
{
	LogError2( "Фраза '$msg' подозревается на РВС (where=$where)" );
}

// channels and privates
if( $where == 'Общий' )
{
	$channel = 0;
	$private_to = 0;
}
else if( $where == 'Орден' )
{
	$channel = -1;
	$private_to = 0;
}
else if( $where == 'Бой - Все' )
{
	$channel = -2;
	$private_to = 0;
}
else if( $where == 'Бой - Свои' )
{
	$channel = -3;
	$private_to = 0;
}

else if( $where == 'Торговый' )
{
	$channel = -5;
	$private_to = 0;
}
else if( $where == 'Первые Шаги' )
{
	die( "<script>alert( 'Вы находитесь в комнате чата \"Первые Шаги\", в которой нельзя общаться. Для того чтобы поговорить с игроками, нажмите кнопку \"Общий\" в списке комнат над чатом.' );</script>" );
}
else if( $where == 'Системные' )
{
	die( "<script>alert( 'Нельзя писать в комнате, предназначенное для системных сообщений!' );</script>" );
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
		die( "<script>alert( 'Не удалось отослать приватное сообщение: персонажа $where не существует.' );</script>" );
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
		die( "<script>alert( 'Вы не можете находиться в более чем 10 комнатах одновременно!' );</script>" );
	$arr = explode( ' ', $msg );
	$room_id = (int)$arr[1];
	if( $arr[0] == '/proom' )
		$room_id += 1000000000;
	if( $room_id >= 1000000000 )
	{
		if( !f_MValue( "SELECT access_level FROM ch_channel_access WHERE channel_id=$room_id and player_id = ".$player_id ) )
			die( "<script>alert( 'У вас нет доступа в комнату ".($room_id-1000000000)."' );</script>" );
	}
	else if( $room_id < 1 || $room_id > 1000000000 )
		die( "<script>alert( 'Номер комнаты должен быть между 1 и 1000000000.' );</script>" );

	include( 'chat_channels_functions.php' );
	PlayerEnterChannel( $player_id, $room_id );
	if( $room_id < 1000000000 ) die( "<script>window.top.createPrivateRoom( '#{$room_id}' );</script>" );
	else die( "<script>window.top.createPrivateRoom( '@".($room_id-1000000000)."' );</script>" );
}
else if( substr( $msg, 0, 9 ) == '/protect ' )
{
	if( $level < 4 )
		die( "<script>alert( 'Только игроки 4 уровня и выше могут создавать закрытые комнаты.' );</script>" );
	$room_id = 1000000000 + (int)trim( substr( $msg, 9 ) );
	if( f_MValue( "SELECT count( player_id ) FROM ch_channel_access WHERE player_id={$player_id} AND access_level = 100" ) >= 10 )
		die( "<script>alert( 'У вас уже есть 10 комнат. Завести 11-ую нельзя.' );</script>" );
	if( $room_id > 2000000000 || $room_id < 1000000001 )
		die( "<script>alert( 'Номер закрытой комнаты не может быть больше 1000000000 или меньше 1!' );</script>" );
	if( f_MValue( "SELECT access_level FROM ch_channel_access WHERE channel_id=$room_id and access_level = 100" ) )
		die(  "<script>alert( 'У этой комнаты уже есть владелец. Укажите другой номер.' );</script>" );
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
		die( "<script>alert( 'У вас нет своих комнат.' );</script>" );
	else
		die( "<script>alert( 'Номера ваших комнат: {$rooms}.\\nИспользуйте команду \"/proom номер_комнаты\" для входа в нужную комнату.' );</script>" );
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
	$msg = '/me кидает кубик. Выпадает <b><font color="darkred">'.mt_rand( 1, 6 ).'</font></b>';
else if( substr( $msg, 0, 8 ) == '/invite ' )
{
	if( $channel < 1000000000 )
		die( "<script>alert( 'Приглашать можно только в закрытые комнаты!');</script>" );
	if( f_MValue( "SELECT access_level FROM ch_channel_access WHERE player_id=$player_id and channel_id = $channel and access_level = 100" ) )
	{
		$players = explode( ' ', $msg );
		$count = count( $players );
		if( $count <= 1 )
			die( "<script>alert( 'Укажите ники персонажей, которых вы хотите пригласить.' );</script>" );
		$tmp = 'login = "'.$players[1].'"';
		for( $i = 2; $i < $count; ++ $i )
			$tmp .= ' or login = "'.$players[$i].'"';
		$res = f_MQuery( "SELECT player_id FROM characters WHERE ".$tmp );
		while( $arr = f_MFetch( $res ) )
		{
			if( !f_MValue( "SELECT access_level FROM ch_channel_access WHERE player_id = ".$arr['player_id']." and channel_id = ".$channel ) )
				f_MQuery( "INSERT INTO ch_channel_access ( channel_id, player_id, access_level ) VALUES ( $channel, ".$arr['player_id'].", 1 )" );
		}
		die( "<script>alert( 'Перечисленные персонажи получили доступ в указанную комнату. Команда для входа \"/proom ".($channel-1000000000)."\"' );</script>" );
	}
	else
		die( "<script>alert('Приглашать посетителей может только владелец комнаты!');</script>" );
}
else if( substr( $msg, 0, 6 ) == '/kick ' )
{
	include( 'chat_channels_functions.php' );
	if( !f_MValue( "SELECT access_level FROM ch_channel_access WHERE player_id=$player_id and channel_id = $channel and access_level = 100" ) )
		die( "Вы можете исключать персонажей только из своих закрытых комнат." );
		$players = explode( ' ', $msg );
		$count = count( $players );
		if( $count <= 1 )
			die( "<script>alert( 'Укажите имена персонажей, которых вы хотите лишить доступа.' );</script>" );
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
else if( $msg == '/delete' )
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
