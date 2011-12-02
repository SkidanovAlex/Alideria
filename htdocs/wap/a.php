<? header('Content-Type: text/vnd.wap.wml'); echo "<?"; ?>xml version="1.0" encoding="utf-8"?>
<!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN"
   "http://www.wapforum.org/DTD/wml_1.1.xml" >
<wml>
<?

ob_start();

include( '../functions.php' );

f_register_globals( );

function redir( $a )
{
	global $msg;
	$msg = $a."<br/>";
}

f_MConnect( );

$mode = 0;

function auth( $p_login, $p_pwd )
{
	global $pid;
	global $_GET;

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
    	$ipstr = getenv( "REMOTE_ADDR" );
    	$ipxstr = getenv( "HTTP_X_FORWARDED_FOR" );
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
    $ipstr = getenv( "REMOTE_ADDR" );
    $ipxstr = getenv( "HTTP_X_FORWARDED_FOR" );
    if( !$ipxstr ) $ipxstr = $ipstr;

    f_MQuery( "LOCK TABLE online WRITE" );
    f_MQuery( "DELETE FROM online WHERE player_id=$arr[player_id]" );
    f_MQuery( "INSERT INTO online ( player_id, session_crc, last_ping ) VALUES ( $arr[player_id], $crc, $tm )" );
    f_MQuery( "UNLOCK TABLES" );
    f_MQuery( "INSERT INTO history_logon_logout ( player_id, login_time, login_ip, login_ip_x ) VALUES ( $arr[player_id], $tm, '$ipstr', '$ipxstr' )" );

    $pid = $arr['player_id'];
    $_GET['c_id'] = $arr['player_id'];
    $_GET["c_loc"] = $crc;

    // ---------------------
    include_once( "../player.php" );
    $player = new Player( $arr['player_id'] );

    $player->UploadInfoToJavaServer( );
    //----------------------	

    return 1;
}

if( $_POST['login'] )
{
	$mode = auth( $_POST['login'], $_POST['pwd'] );

    $c_log = "".$_GET['c_loc'];
    if( $c_log[0] == '-' ) $c_log[0] = 'A';
}
else
{
    $c_log = $_GET['c_loc'];
    if( $c_log[0] == '-' ) $c_log[0] = 'A';

	$HTTP_COOKIE_VARS['c_id'] = $_COOKIE['c_id'] = $_GET['c_id'];
	$HTTP_COOKIE_VARS['c_loc'] = $_COOKIE['c_loc'] = $_GET['c_loc'];

    if($HTTP_COOKIE_VARS['c_loc'][0] == 'A')
    {
    	$HTTP_COOKIE_VARS['c_loc'][0] = '-';
    	$_COOKIE['c_loc'][0] = '-';
    }

	if( check_cookie( ) )
    {
    	$mode = 1;
    	$pid = $_COOKIE['c_id'];
    }
}

if( $mode && isset( $_GET['exit'] ) )
{
	$mode = 0;
	f_MQuery( "DELETE FROM online WHERE player_id = $pid" );

    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
    socket_connect($sock, "127.0.0.1", 1100);
    $msg = "player\nOffline_$pid\n".mt_rand()."\n$pid\n000000\n000000\n0\n1\n";
    socket_write( $sock, $msg, strlen($msg) ); 
    socket_close( $sock );

	// запись в логе
	$tm = time( );
	$ipstr = getenv( "REMOTE_ADDR" );
	$ipxstr = getenv( "HTTP_X_FORWARDED_FOR" );
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
?>
  <card id="main" title="Alideria">
<?
	
	include_once( '../player.php' );
	include_once( '../guild.php' );

	$player = new Player( $pid );
	echo "<p>";
	echo "<b>{$player->login}</b> - <anchor>Выйти<go href='a.php?exit=1&amp;c_id=$pid&amp;c_loc=$c_log'/></anchor><br/>\n";

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
					$player->SetTill( time( ) + 10 * 60 );
				}
			}
		}
		else echo "<i>Неверный код в окне</i><br/>";
	}
	else if( $player->regime == 104 && $player->till < time( ) + 2 )
	{
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

            	// premium
            	$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=1" ) );
            	if( $barr[0] ) $per_hour *= 1.5;

            	$kopka->GetItemId( 10 * 60, $per_hour );
            	
            	if( !$kopka->num ) $st .= "<i>Вы ничего не добыли</i><br/>";
            	else
            	{
            		$player->AddToLog( $kopka->item_id, $kopka->num, 1, $guild_id, 0 );
            		$player->AddItems( $kopka->item_id, $kopka->num );

            		$f4 = getItemNameForm( $kopka->item_id, "4" );
            		$f13 = getItemNameForm( $kopka->item_id, "13" );
            		$f2m = getItemNameForm( $kopka->item_id, "2_m" );

            		$st = "<i>Вы нашли <b>".my_word_form2( $kopka->num, $f4, $f13, $f2m )."</b></i><br/>";
            	}
            	
            	echo $st;

            	// увеличь профу тут!
            	if( !$kopka->num )
            		AlterProfExp( $player, 1 );
            	else AlterProfExp( $player, ceil( $kopka->item_prices[$kopka->item_id] * $kopka->num * 50 / $per_hour ) );

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
    if( $player->location != 2 && ( $player->location != 0 || $player->depth != 31 ) ) echo "<i>К сожалению, вы в локации, в которой WAP-версии не поддерживается</i><br/>";
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
		$arr = f_MFetch( $res ); echo "[".$arr[0]."]<br/>";

		$res2 = f_MQuery( "SELECT loc_links.loc2, loc_links.depth2, loc_texts.title2 FROM loc_links, loc_texts WHERE loc1 = $loc AND depth1 = $place AND loc = loc2 AND loc=loc1 AND depth = depth2 ORDER BY loc2, depth2" );
		while( $arr2 = f_MFetch( $res2 ) )
		{
			print( "<anchor>$arr2[2]<go href='a.php?dir=$arr2[1]&amp;c_id=$pid&amp;c_loc=$c_log'/></anchor><br/>\n" );
		}

		$res2 = f_MQuery( "SELECT loc_links.loc1, loc_links.depth1, loc_texts.title2 FROM loc_links, loc_texts WHERE loc2 = $loc AND depth2 = $place AND loc = loc1 AND loc=loc2 AND depth = depth1 ORDER BY loc1, depth1" );
		while( $arr2 = f_MFetch( $res2 ) )
		{
			print( "<anchor>$arr2[2]<go href='a.php?dir=$arr2[1]&amp;c_id=$pid&amp;c_loc=$c_log'/></anchor><br/>\n" );
		}

		$guild_id = getGuild( $loc, $depth );
		if( $guild_id != 0 )
		{
			$guild = new Guild( $guild_id );
			if( $guild->LoadPlayer( $player->player_id ) )
			{
				echo "<br/><br/>";
				echo "<img src='captcha/code.php?c_id=$pid&amp;c_loc=$c_log' alt='code'/><br/>";
				echo "<input type='text' size='4' maxlength='4' name='num'/><br/>";
				echo "<br/>Вес вещей: ".($player->items_weight/100.0)."/".$player->MaxWeight( )."<br/>";
?>
<do type="accept" label="Работать!">
<go href="a.php" method="get">
<postfield name="num" value="$num"/>
<postfield name="c_id" value="<?=$pid?>"/>
<postfield name="c_loc" value="<?=$c_log?>"/>
</go>
</do>
<?			}
		}
	}
	else if( $player->regime == 104 )
	{
		$res = f_MQuery( "SELECT title FROM loc_texts WHERE loc={$player->location} AND depth={$player->depth}" );
		$arr = f_MFetch( $res ); echo "[".$arr[0]."]<br/>";

		echo "<i>Вы работаете</i><br/>";
		$rem = $player->till - time( );
		echo "Осталось: <b>".(int)($rem/60);
		echo ":"; $rem = $rem % 60;
		if( $rem < 10 ) echo "0"; echo $rem;
		echo "</b><br/><anchor>Обновить<go href='a.php?c_id=$pid&amp;c_loc=$c_log'/></anchor>";
		echo "";
	}
	else
	{
		echo "<i>К сожалению, вы заняты действием, которое в WAP-версии не поддерживается</i><br/>";
	}
	
	echo "</p>";
}
else
{

?>

<template>
<do type="accept" label="Войти">
<go href="a.php" method="post">
<postfield name="login" value="$login"/>
<postfield name="pwd" value="$pwd"/>
</go>
</do>
</template>
<card id="main" title="Alideria">
<p align="center">
<?=$msg?>
<b>Логин</b><br/>
<input type='text' name='login'/><br/>
<b>Пароль</b><br/>
<input type='password' name='pwd'/><br/>
</p>

<?

}

$ret = ob_get_contents();
ob_end_clean();
echo iconv("cp1251",'utf-8',$ret);

?>
  </card>
</wml>
