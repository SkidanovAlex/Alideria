<?
	include_once( "player.php" );
	include_once( "skin.php" );
	include_js( "/js/skin.js" );
	include_js( "/js/skin2.js" );
	include_js( "/js/clans.php" );
	include_js( "/js/ii_a.js" );

	f_MConnect( );

	if( !check_cookie( ) )
		die( "Неверные настройки Cookie" );

	$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
	
	if( $player->Rank( ) == 0 )
	{
		die( 'У вас недостаточно прав для просмотра этой страницы' );
	}
	
	$player_login = htmlspecialchars( conv_utf( $_GET['nick'] ), ENT_QUOTES );
	
	$res = f_MQuery( "SELECT player_id FROM characters WHERE login = '$player_login'" );
	if( !mysql_num_rows( $res ) )
	{
		die( 'Нет такого игрока' );
	}
	
	$arr = f_MFetch( $res );
	
	$target = new Player( $arr[0] );
	if ($target->Rank()==1 && $player->Rank()!=1) die( 'У вас недостаточно прав для просмотра этой страницы' );
?>
<html>
<head>
	<meta http-equiv=Content-Type content="text/html; charset=windows-1251" />
	<link href="style.css" rel="stylesheet" type="text/css" />
	<script src="/js/jquery/main.js"></script>
	<title><?=$player_login?> - контроль персонажа</title>
	<style>
		.toggleBlock
		{
			display: none;
			padding: 0px 0px 20px;
		}
	</style>
</head>
<body style="padding: 5px 10px 20px;">
	<h3>Контроль персонажа <?=$player_login?></h3>
	Регистрационная дата: <b><?=( $tmp = f_MValue( "SELECT `regdate` FROM `characters` WHERE `login` = '$player_login'" ) ) ? date( 'd.m.Y H:i', $tmp ) : 'Неведомо'?></b><br />
	<?=( $tmp = f_MValue( "SELECT `ref_id` FROM `player_invitations` WHERE `player_id` = {$target->player_id}" ) ) ? 'Зарегистрирован по приглашению <b>'.f_MValue( "SELECT `login` FROM `characters` WHERE `player_id` = $tmp" ).'</b>' : ''?>
	<br />
	<br />
	Если игрок на турнире повис в бою: <a href="/player_control.php?nick=<?=$player_login?>&uncombat">Вытащить</a><br />
	<br />
	<a href="/player_control.php?nick=<?=$player_login?>&dopros">Пригласить на допрос в @500</a>
	<br />
	<br />
<?
	if( isset( $_GET['dopros'] ) )
	{
		$defendant = new Player( $arr[0] );
		
		if( !f_MValue( "SELECT access_level FROM ch_channel_access WHERE player_id = ".$defendant->player_id." and channel_id = 1000000500" ) )
		{
			f_MQuery( "INSERT INTO ch_channel_access ( channel_id, player_id, access_level ) VALUES ( 1000000500, ".$defendant->player_id.", 1 )" );
		}		
		
		$defendant->syst3( 'Сим надлежит явиться в <b>@500</b> для допроса в скорейшем времени по получении извещения.<br><br>Дабы осуществить визит, вбей в чат "/proom 500", убрав двойные кавычки с обеих сторон. Игнорирование письма ведёт к рассмотру дела без учёта твоей защиты.<br><br>Искренне твой, Представитель Закона.' );
		$player->syst2( "{$defendant->login} получил".( ( $defendant->sex ) ? 'а' : '' ).' извещение явиться в @500' );
		echo "Персонаж вызван на допрос";
	}
	elseif( isset( $_GET['uncombat'] ) )
	{
		$cres = f_MQuery( "SELECT * FROM combat_players WHERE player_id=$arr[0]" );
		$carr = f_MFetch( $cres );
		$ccarr = f_MFetch( f_MQuery( "SELECT * FROM combats WHERE combat_id=$carr[0]" ) );
		if( $ccarr['turn_done'] == 1  )
		{
			f_MQuery( "UPDATE combat_players SET ready=0 WHERE combat_id=$carr[0]" );
			f_MQuery( "UPDATE combats SET turn_done=0 WHERE combat_id=$carr[0]" );
		}
		else
		{
			echo "Бой не висит... : ".$ccarr['turn_done'];
		}
	}
	
	?>
	
	<a href="javascript://" onclick="$( '#banomet' ).toggle( )"><h3>Баномёт</h3></a>
	<div id="banomet" class="toggleBlock">
	<?
	

if( isset( $_GET['tp'] ) )
{
	foreach( $permissions as $value ) if( $value == $_GET['tp'] )
	{
		$days = (int)$_GET['days'];
		$hours = (int)$_GET['hours'];
		$minutes = (int)$_GET['minutes'];
		$seconds = (int)$_GET['seconds'];
		$reason = htmlspecialchars( $_GET['reason'], ENT_QUOTES );
		$comms = htmlspecialchars( $_GET['comms'], ENT_QUOTES );

		if( $value == 'ban' && $player->Rank( ) != 1 && $days < 900 )
		{
			echo "<b><font color=red>Только администрация может накладывать не перманентный блок. Наказание изменено на молчанку.<br>Чтобы наложить перманентный блок - используйте 999 дней в длительности наказания</font></b>";
			$_GET['tp'] = 'silence';
			continue;
		}

		if( $days >= 0 && $days < 1000 )
			if( $hours >= 0 && $hours < 1000 )
				if( $minutes >= 0 && $minutes < 1000 )
					if( $seconds >= 0 && $seconds < 1000 )
					{
						$res = f_MQuery( "SELECT * FROM player_permissions WHERE player_id = {$target->player_id}" );
						if( mysql_num_rows( $res ) == 0 )
							f_MQuery( "INSERT INTO player_permissions ( player_id ) VALUES ( {$target->player_id} )" );
						$delta = ( ( $days * 24 + $hours ) * 60 + $minutes ) * 60 + $seconds;
						if( $delta > 0 )
						{
							$tm = time( ) + $delta;
							f_MQuery( "UPDATE player_permissions SET $value = $tm, {$value}_reason = '$reason' WHERE player_id = {$target->player_id}" );

							if( $value == "ban" ) $type = "Заблокирован";
							else if( $value == "silence" ) $type = "Наложено молчание";
							else if( $value == "trade" ) $type = "Наложен запрет на торговлю и обмен";
							else if( $value == "fights" ) $type = "Наложен запрет на бои";
							else $type = "неизвестное наказание";
							
							f_MQuery( "INSERT INTO history_punishments ( time, moderator_login, player_id, reason, duration, type, comments ) VALUES ( ".time( ).", '{$player->login}', {$target->player_id}, '$reason', $delta, '$type', '$comms' )" );
							
							if( $value == "ban" )
							{ // Выкинем в оффлайн
								$tres = f_MQuery( "SELECT * FROM online WHERE player_id={$target->player_id}" );
								$tarr = f_MFetch( $tres );
								if( $tarr )
								{
									$tm = time( );
									$ress = f_MQuery( "SELECT max( entry_id ) FROM history_logon_logout WHERE player_id = {$target->player_id}" );
									$arrr = f_MFetch( $ress );
									if( $arrr )
									{
										$entry_id = $arrr[0];
										f_MQuery( "UPDATE history_logon_logout SET logout_time = $tm, logout_ip = login_ip, logout_ip_x = login_ip_x, logout_reason = 'Ban' WHERE entry_id = $entry_id" );
									}
											
								}
                        	    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
                                socket_connect($sock, "127.0.0.1", 1100);
                                $msg = "player\nOffline_{$target->player_id}\n".mt_rand()."\n{$target->player_id}\n000000\n000000\n0\n1\n";
                                socket_write( $sock, $msg, strlen($msg) ); 
                                socket_close( $sock );
ClearCachedValue('USER:' . $target->player_id  . ':scrc_key');

								$tres = f_MQuery( "DELETE FROM online WHERE player_id={$target->player_id}" );
							}
						}
					}
	}
}

if( isset( $_GET['clear'] ) )
{
	foreach( $permissions as $value ) if( $value == $_GET['clear'] )
	{
		f_MQuery( "UPDATE player_permissions SET $value = 0 WHERE player_id = {$target->player_id}" );
		if( $value == "ban" ) $type = "Разблокирован";
		else if( $value == "silence" ) $type = "Снято молчание";
		else if( $value == "trade" ) $type = "Снят запрет на торговлю и обмен";
		else if( $value == "fights" ) $type = "Снят запрет на бои";
		else $type = "неизвестное наказание";
		f_MQuery( "INSERT INTO history_punishments ( time, moderator_login, player_id, reason, duration, type ) VALUES ( ".time( ).", '{$player->login}', {$target->player_id}, '', 0, '$type' )" );
	}
}

function moo( $a, $b )
{
	global $player_login;
	$st = '<form method="GET">';
	$st .= '<input type=hidden name=tp value=' . $b . '>';
	$st .= '<input type=hidden name=nick value="' . $player_login . '">';
	$st .= $a.'<input type=text name=days value=0 size=3 maxlength=3> дней, ';
	$st .= '<input type=text name=hours value=0 size=3 maxlength=3> часов, ';
	$st .= '<input type=text name=minutes value=0 size=3 maxlength=3> минут, ';
	$st .= '<input type=text name=seconds value=0 size=3 maxlength=3> секунд ';
	$st .= 'Причина: <input type=text name=reason value="(Укажите причину)" size=20 maxlength=100> ';
	$st .= 'Комментарии: <input type=text name=comms value="По умолчанию" size=20 maxlength=255>';
	$st .= '<input type=submit value="Карать!">';
	$st .= '</form>';
	return $st;
}

if( $target->GetPermission( "ban" ) > 0 )
{
	echo "<a href='player_control.php?nick=$player_login&clear=ban'>Снять Блокировку Персонажа</a><br />";
}
else
{
	echo moo( 'Заблокировать персонажа на: ', 'ban' );
}

if( $target->GetPermission( "silence" ) > 0 )
{
	echo "<a href='player_control.php?nick=$player_login&clear=silence'>Снять Молчанку с Персонажа</a><br />";
}
else
{
	echo moo( 'Наложить молчанку на: ', 'silence' );
}

if( $target->GetPermission( "trade" ) > 0 )
{
	echo "<a href='player_control.php?nick=$player_login&clear=trade'>Снять Запрет на Торговлю с Персонажа</a><br />";
}
else
{
	echo moo( 'Наложить запрет на торговлю на: ', 'trade' );
}

if( $target->GetPermission( "fights" ) > 0 )
{
	echo "<a href='player_control.php?nick=$player_login&clear=fights'>Снять Запрет на Бои с Персонажа</a><br />";
}
else
{
	echo moo( 'Наложить запрет на бои на: ', 'fights' );
}


echo "</div><a href=\"javascript://\" onclick=\"$( '#journals' ).toggle( )\"><h3>Журналы</h3></a><div id=\"journals\" class=\"toggleBlock\">";

print( "<a href=history_visits.php?player_id={$target->player_id} target=_blank>Информация о посещениях игры</a><br>" );
print( "<a href=history_punishments.php?player_id={$target->player_id} target=_blank>Информация о наказаниях</a><br>" );
print( "<a href=history_trades.php?player_id={$target->player_id} target=_blank>Информация о сделках</a><br>" );
print( "<a href=history_post.php?player_id={$target->player_id} target=_blank>Информация о почтовых переводах</a><br>" );
print( "<a href=history_fights.php?player_id={$target->player_id} target=_blank>Информация о боях</a><br>" );

if( $player->Rank( ) == 1 )
{
	print( "<a href=history_payments.php?player_id={$target->player_id} target=_blank>Информация о донате</a><br>" );
	
	echo '<br />';
	echo "<a href='/admin86006609098moo/player_log.php?login=$player_login&item_id=-3' target='_blank'>Опыт за бои и марафоны</a><br />";
	echo "<a href='/admin86006609098moo/player_log.php?login=$player_login&item_id=-2' target='_blank'>Движение денег, талантов и вещей</a><br />";
	echo "<a href='/admin86006609098moo/player_log.php?login=$player_login&item_id=0' target='_blank'>Движение денег</a><br />";
	echo "<a href='/admin86006609098moo/player_log.php?login=$player_login&item_id=-1' target='_blank'>Движение талантов</a><br />";	
}

echo "</div>";


if( true )
{
	print( "<a href=\"javascript://\" onclick=\"$( '#sovpadenija' ).toggle( )\"><h3>Совпадения</h3></a><div id=\"sovpadenija\" class=\"toggleBlock\">" );
   echo "<div style='width:300px; text-align:center;'>" . ScrollLightTableStart() . "<table width='100%' border='0'>";
   $i = 0;
   
  	$cres = f_MQuery( "SELECT distinct player_id,login_ip FROM `history_logon_logout` WHERE login_ip != '127.0.0.1' AND login_ip=(SELECT login_ip FROM `history_logon_logout` WHERE player_id='{$arr[0]}' ORDER BY login_time DESC LIMIT 0,1) AND player_id!=172 AND player_id!='{$arr[0]}' ORDER BY login_time DESC" ); //  
	while($carr = f_MFetch( $cres ))
	{
	   echo "<tr><td>";
		$plr = new Player( $carr[player_id] );
		echo "<script>document.write( ".$plr->Nick( )." );</script><a href=\"/player_control.php?nick={$plr->login}\" target=\"_blank\" title=\"Контроль Персонажа {$plr->login}\"><img src=\"/images/c.gif\" style=\"width: 11px; height: 11px; border: 0px;\" /></a> IP: ";
		echo ( $player->Rank( ) == 2 ) ? md5( $carr[login_ip].'DksDS$kfdsl04$3dfgkl' ) : $carr[login_ip];
      echo "</td></tr>";
      $i++;
	}

	if (!$i)
	{
	   echo "Нет пересечений";
	}
	
	echo "</table>" . ScrollLightTableEnd() . "</div></div>";

	// Рефералы
	
	echo "<a href=\"javascript://\" onclick=\"$( '#referals' ).toggle( )\"><h3>Рефералы</h3></a><div id=\"referals\" class=\"toggleBlock\" style='width:300px; text-align:center;'>".GetScrollTableStart( );
	$res = f_MQuery( "SELECT player_id FROM player_invitations WHERE ref_id={$target->player_id}" );
	
	if( !f_MNum( $res ) )
	{
		echo "Игрок никого не пригласил";
	}
		
	while( $arr = f_MFetch( $res ) )
   {
   	$plr = new Player( $arr[0] );
   	echo "<script>document.write( ".$plr->Nick( )." );</script><a href=\"/player_control.php?nick={$plr->login}\" target=\"_blank\" title=\"Контроль Персонажа {$plr->login}\"><img src=\"/images/c.gif\" style=\"width: 11px; height: 11px; border: 0px;\" /></a><br />";
   }

	echo  GetScrollTableEnd( )."</div>";
}
?>
</body>
</html>