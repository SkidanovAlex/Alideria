<?

header("Content-type: text/html; charset=windows-1251");

include( '../functions.php' );

f_register_globals( );

function redir( $a )
{
	global $msg;
	$msg = "<i>".$a."</i>";
}

f_MConnect( );

$mode = 0;

function auth( $p_login, $p_pwd )
{
	global $pid;

    $p_login = AddSlashes( $p_login );
    $p_login2 = iconv("UTF-8", "CP1251", $p_login );
    if( $p_login2 === false || $p_login != iconv("CP1251", "UTF-8", $p_login2 ) )
    	;
    else $p_login = $p_login2;

    $res = f_MQuery( "SELECT player_id, login, pswrddmd5 FROM characters WHERE login='$p_login'" );

    if( f_MNum( $res ) < 1 )
    {
    	redir( "Неверный логин" );
    	return 0;
    }

    $md = md5( $p_pwd );
    $arr = f_MFetch( $res );
    if( $arr['pswrddmd5'] !== $md )
    {
    	redir( "Неверный пароль" );
    	return 0;
    }

    $arr2 = f_MFetch( f_MQuery( "SELECT ban FROM player_permissions WHERE player_id=$arr[player_id]" ) );
    if( $arr2 && $arr2[0] > time( ) )
    {
    	redir( "Ваш персонаж заблокирован, до снятия блока осталось еще ".my_time_str( $arr2[0] - time( ) ) );
    	return 0;
    }

    // Запись в логе о выходе, если нужна
    $tres = f_MQuery( "SELECT * FROM online WHERE player_id=$arr[player_id]" );
    $tarr = f_MFetch( $tres );
    if( $tarr )
    {
    	$tm = time( );
    	$ipstr = f_MEscape( htmlspecialchars(getenv( "REMOTE_ADDR" )));
    	$ipxstr = f_MEscape( htmlspecialchars( getenv( "HTTP_X_FORWARDED_FOR" )) );
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
    $ipstr = f_MEscape( htmlspecialchars( getenv( "REMOTE_ADDR" ) ));
    $ipxstr = f_MEscape( htmlspecialchars( getenv( "HTTP_X_FORWARDED_FOR" ) ));
    if( !$ipxstr ) $ipxstr = $ipstr;

    f_MQuery( "LOCK TABLE online WRITE" );
    f_MQuery( "DELETE FROM online WHERE player_id=$arr[player_id]" );
    f_MQuery( "INSERT INTO online ( player_id, session_crc, last_ping ) VALUES ( $arr[player_id], $crc, $tm )" );
    f_MQuery( "UNLOCK TABLES" );
    f_MQuery( "INSERT INTO history_logon_logout ( player_id, login_time, login_ip, login_ip_x ) VALUES ( $arr[player_id], $tm, '$ipstr', '$ipxstr' )" );

    $pid = $arr['player_id'];
    SetCookie( "c_id", $arr['player_id'] );
    SetCookie( "c_loc", $crc );

    // ---------------------
    include_once( "../player.php" );
    $player = new Player( $arr['player_id'] );

    $crc = $player->UploadInfoToJavaServer($crc);
    SetCachedValue('USER:' . $arr['player_id'] . ':scrc_key', $crc,  7200);

    //----------------------	

    return 1;
}

if( $_POST['login'] )
	$mode = auth( $_POST['login'], $_POST['pwd'] );
else if( check_cookie( ) )
{
	$mode = 1;
	$pid = $_COOKIE['c_id'];
}

if( $mode && isset( $_GET['exit'] ) )
{
	$mode = 0;
	SetCookie( "c_id", "" );
	SetCookie( "c_loc", "" );
	f_MQuery( "DELETE FROM online WHERE player_id = $pid" );

    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
    socket_connect($sock, "127.0.0.1", 1100);
    $msg = "player\nOffline_$pid\n".mt_rand()."\n$pid\n000000\n000000\n0\n1\n";
    socket_write( $sock, $msg, strlen($msg) ); 
    socket_close( $sock );
ClearCachedValue('USER:' . $pid . ':scrc_key');

	// запись в логе
	$tm = time( );
	$ipstr = f_MEscape( htmlspecialchars( getenv( "HTTP_X_REAL_IP" )));
	$ipxstr = f_MEscape( htmlspecialchars( getenv( "HTTP_X_FORWARDED_FOR" )));
	
	
	if( !$ipxstr ) $ipxstr = $ipstr;
	$ress = f_MQuery( "SELECT max( entry_id ) FROM history_logon_logout WHERE player_id = $pid" );
	$arrr = f_MFetch( $ress );
	if( $arrr )
	{
		$entry_id = $arrr[0];
		f_MQuery( "UPDATE history_logon_logout SET logout_time = $tm, logout_ip = '$ipstr', logout_ip_x = '$ipxstr', logout_reason = 'Manual Exit' WHERE entry_id = $entry_id" );
	}

}

function getGuild( $loc, $depth )
{
	if( $loc == 0 && $depth == 31 ) return 103;
	if( $loc == 2 && $depth == 36 ) return 108;
	if( $loc == 2 && $depth == 4 ) return 101;
	if( $loc == 2 && $depth == 48 ) return 102;
	return 0;
}

if( $mode )
{
	include_once( '../player.php' );
	include_once( '../guild.php' );

	$player = new Player( $pid );
	echo "<b>{$player->login}</b> - <a href=index.php?exit=1>Выйти</a><br>";

	$tm = time( );
	f_MQuery( "UPDATE online SET last_ping = $tm WHERE player_id = {$player->player_id}" );


	$loc = $player->location;
	$place = $player->depth;
	$depth = $player->depth;

	// actions
	if( $player->regime == 0 && isset( $_GET['num'] ) )
	{
		$num = (int)$_GET['num'];
		$arr = f_MFetch( f_MQuery( "SELECT number FROM player_num WHERE player_id=$pid" ) );
		if( $arr && $num == $arr[0] )
		{
			$guild_id = getGuild( $loc, $depth );
			if( $guild_id != 0 )
			{
				$guild = new Guild( $guild_id );
				if( $guild->LoadPlayer( $player->player_id ) )
				{
					$player->SetRegime( 104 );
                	// auto
                	$auto = false;
                	$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=4" ) );
                	if( $barr[0] )
		{
			$player->SetTill( time( ) + 10 * 60 * 12 );
			$player->SetTrigger(227, 1);
		}
		else
		{
			$player->SetTill( time( ) + 10 * 60 );
			$player->SetTrigger(227, 0);
		}
				}
			}
		}
		else echo "<i>Неверный код в окне</i><br>";
	}
	else if( $player->regime == 104 && ( $player->till < time( ) + 2 || isset( $_GET['cancel'] ) ) )
	{
		$hodka_time = 600;
    	$till = $player->till;
    	if( $till < time( ) ) $till = time( );
    	if( abs( $till - time( ) ) < 4 ) $till = time( );
    	$started = $till - $hodka_time;
    	
		$guild_id = getGuild( $loc, $depth );
		if( $guild_id != 0 )
		{
			$guild = new Guild( $guild_id );
			if( $guild->LoadPlayer( $player->player_id ) )
			{
				$res = f_MQuery( "SELECT items.item_id, items.price FROM lake_items, items WHERE lake_items.item_id=items.item_id AND lake_items.guild_id = $guild_id AND lake_items.rank <= {$guild->rank}" );
            	$st = "";
            	include_once( "../kopka.php" );
            	include_once( "../prof_exp.php" );
            	include_once( "../items.php" );

            	$kopka = new Kopka( );
            		
            	while( $arr = f_MFetch( $res ) )
            		$kopka->AddItem( $arr[0], $arr[1] );
            	
            	$per_hour = 200 + $guild->rating * 50;

            	// auto
            	$auto = false;
            	$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=4" ) );
            	if( $player->HasTrigger(227) ) $started = $till - $hodka_time * 12;
	$player->SetTrigger(227, 0);
            	// premium
            	$premium = false;
            	$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=1" ) );
            	if( $barr[0] ) $premium = true;

            	$total_work_time = time( ) - $started;

            	if( $total_work_time < 30 ) echo( "<i>Вы работали меньше 30 секунд... За это время едва ли можно было сделать что-то полезное.</i><br>" );
            	while( $total_work_time >= 30 )
            	{
            		$val = min( $total_work_time, $hodka_time );
            		$total_work_time -= $val;
                	$kopka->GetItemId( $val, $per_hour, $premium );
                	
                	$st = "";
					$st .= "Вы работали ".my_time_str2( $val ).". ";
                	if( !$kopka->num ) $st .= "Вы ничего не добыли";
                	else
                	{
                		$player->AddToLog( $kopka->item_id, $kopka->num, 1, $guild_id, 0 );
                		$player->AddItems( $kopka->item_id, $kopka->num );

                		$f4 = getItemNameForm( $kopka->item_id, "4" );
                		$f13 = getItemNameForm( $kopka->item_id, "13" );
                		$f2m = getItemNameForm( $kopka->item_id, "2_m" );

                		$st .= "Вы нашли <b>".my_word_form2( $kopka->num, $f4, $f13, $f2m )."</b>";
                	}
                	
                	// увеличь профу тут!
                	if( !$kopka->num )
                	{
                		if( $val == $hodka_time ) $st .= AlterProfExp( $player, 1 );
                	}
                	else $st .= AlterProfExp( $player, ceil( $kopka->item_prices[$kopka->item_id] * $kopka->avgnum * 50 / $per_hour ) );

                	echo "<i>".$st."</i><br>";
                }

				$player->SetTill( 0 );
				$player->SetRegime( 0 );

                $code=rand(1000,9999);

                $text = $code . "";

                f_MQuery( "LOCK TABLE player_num WRITE" );
                f_MQuery( "DELETE FROM player_num WHERE player_id = $player->player_id" );
                f_MQuery( "INSERT INTO player_num VALUES ( $player->player_id, $code )" );
                f_MQuery( "UNLOCK TABLES" );
			}
		}
	}

	// show
    if( $player->location != 2 && ( $player->location != 0 || $player->depth != 31 ) ) echo "<i>К сожалению, вы в локации, в которой PDA-версия не поддерживается</i>";
	else if( $player->regime == 0 )
	{
		if( isset( $_GET['dir'] ) )
		{
			$to = (int)$_GET['dir'];
			$res = f_MQuery( "SELECT count( loc1 ) FROM loc_links WHERE loc1=2 AND loc2=2 AND ( depth1=$place AND depth2=$to OR depth2=$place AND depth1=$to )" );
			$arr = f_MFetch( $res );
			if( $arr[0] )
			{
				$player->SetDepth( $to );
				$place = $to;
				$depth = $to;
			}
		}

		$res = f_MQuery( "SELECT title FROM loc_texts WHERE loc={$player->location} AND depth={$player->depth}" );
		$arr = f_MFetch( $res ); echo "[".$arr[0]."]<br>";

		$res2 = f_MQuery( "SELECT loc_links.loc2, loc_links.depth2, loc_texts.title2 FROM loc_links, loc_texts WHERE loc1 = $loc AND depth1 = $place AND loc = loc2 AND loc=loc1 AND depth = depth2 ORDER BY loc2, depth2" );
		while( $arr2 = f_MFetch( $res2 ) )
		{
			echo "<tr><td>";
			print( "<li><a href=index.php?dir=$arr2[1]>$arr2[2]</a>" );
			echo "</td></tr>";
		}

		$res2 = f_MQuery( "SELECT loc_links.loc1, loc_links.depth1, loc_texts.title2 FROM loc_links, loc_texts WHERE loc2 = $loc AND depth2 = $place AND loc = loc1 AND loc=loc2 AND depth = depth1 ORDER BY loc1, depth1" );
		while( $arr2 = f_MFetch( $res2 ) )
		{
			echo "<tr><td>";
			print( "<li><a href=index.php?dir=$arr2[1]>$arr2[2]</a>" );
			echo "</td></tr>";
		}

		$guild_id = getGuild( $loc, $depth );
		if( $guild_id != 0 )
		{
			$guild = new Guild( $guild_id );
			if( $guild->LoadPlayer( $player->player_id ) )
			{
				echo "<br><br>";
				echo "<form method=GET action=index.php><img src=captcha/code.php><br><input type=text size=4 maxlength=4 name=num><br><input type=submit value=Работать></form><br>Вес вещей: ".($player->items_weight/100.0)."/".$player->MaxWeight( );
			}
		}
	}
	else if( $player->regime == 104 )
	{
		$res = f_MQuery( "SELECT title FROM loc_texts WHERE loc={$player->location} AND depth={$player->depth}" );
		$arr = f_MFetch( $res ); echo "[".$arr[0]."]<br>";

		echo "<i>Вы работаете</i><br>";
		$rem = $player->till - time( );
		echo "Осталось: <b>".(int)($rem/60);
		echo ":"; $rem = $rem % 60;
		if( $rem < 10 ) echo "0"; echo $rem;
		echo "<br><a href=index.php>Обновить</a>";
		echo "<br><a href=index.php?cancel=1>Прекратить</a>";
		echo "";
	}
	else
	{
		echo "<i>К сожалению, вы заняты действие, которое в PDA-версия не поддерживается</i>";
	}
}
else
{

?>

<b>Алидерия</b><br><?=$msg?>
<form action=index.php method=post>
<table><tr><td>Логин:</td><td><input type=text name=login></td></tr>
<tr><td>Пароль:</td><td><input type=password name=pwd></td></tr>
<tr><td>&nbsp;</td><td><input type=submit value='Войти'></td></tr>
</form>

<?

}

?>
