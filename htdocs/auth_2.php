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
if( $arr['pswrddmd5'] !== $md)
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

/* Временно убрал подтверждение -- не понятно зачем это надо

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
*/

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

$gl_msg = '';
$res_1 = f_MQuery("SELECT player_id FROM online WHERE player_id!=".$arr[player_id]);
while ($arr_1 = f_MFetch($res_1))
{
	if ( f_MValue("SELECT login_ip FROM history_logon_logout WHERE player_id={$arr_1[0]} ORDER BY entry_id DESC LIMIT 1") == $ipstr)
	{
	$res_2 = f_MQuery("SELECT player_id, login FROM characters WHERE player_id=".$arr_1[0]);
	$arr_2 = f_MFetch($res_2);
	if($arr_2)
	{
		$arr_ch = f_MFetch(f_MQuery("SELECT checked FROM coincidence_ip WHERE (player_id_1 = ".$arr_1[0]." AND player_id_2 = ".$arr[player_id].") OR (player_id_2 = ".$arr_1[0]." AND player_id_1 = ".$arr[player_id].")"));
		if(!$arr_ch)
		{
			$gl_msg .= $arr_2[1].', ';
			f_MQuery("INSERT INTO coincidence_ip (player_id_1, player_id_2, ip) VALUES (".$arr_1[0].", ".$arr[player_id].", '".$ipstr."')");
		}
		else
		{
			$checked = $arr_ch[0];
			if($checked == 0)
				$gl_msg .= $arr_2[1].', ';
		}
	}
	}
}
include_once( "player.php" );
if ($gl_msg !== '')
{
	$pl_r = new Player(6825);
	$pl_r->syst3($gl_msg.$p_login.' - совпадение IP в '.date("d.m.Y H:i", time()), 1);
	$pl_r = new Player(21020);
	$pl_r->syst3($gl_msg.$p_login.' - совпадение IP в '.date("d.m.Y H:i", time()), 1);
	$pl_r = new Player(807113);
	$pl_r->syst3($gl_msg.$p_login.' - совпадение IP в '.date("d.m.Y H:i", time()), 1);
	$pl_r = new Player(159836);
	$pl_r->syst3($gl_msg.$p_login.' - совпадение IP в '.date("d.m.Y H:i", time()), 1);
	$pl_r = new Player(136119);
	$pl_r->syst3($gl_msg.$p_login.' - совпадение IP в '.date("d.m.Y H:i", time()), 1);
}

f_MQuery( "LOCK TABLE online WRITE" );
f_MQuery( "DELETE FROM online WHERE player_id=$arr[player_id]" );
f_MQuery( "INSERT INTO online ( player_id, session_crc, last_ping ) VALUES ( $arr[player_id], $crc, $tm )" );
f_MQuery( "UNLOCK TABLES" );
f_MQuery( "INSERT INTO history_logon_logout ( player_id, login_time, login_ip, login_ip_x ) VALUES ( $arr[player_id], $tm, '$ipstr', '$ipxstr' )" );
// ---------------------

$player = new Player( $arr['player_id'] );
$player->SetTrigger(12345, 0);

if (false && (int)date("d")>=16 && (int)date("d")<=19 && (int)date("m")==2)
if (!f_MValue("SELECT COUNT(*) FROM player_triggers WHERE player_id={$player->player_id} AND trigger_id>=13100 AND trigger_id<=13110"))
{
	$player->SetTrigger(13100);
	$player->syst3("Повестка! Вам надлежит явиться для дачи показаний к Сыщику на второй этаж Городской Управы");
}

$player->checkWearLevel();
if($player_id==6825) $player->RecalcStats();

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
