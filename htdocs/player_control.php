<?
	include_once( "player.php" );
	include_once( "skin.php" );
	include_js( "/js/skin.js" );
	include_js( "/js/skin2.js" );
	include_js( "/js/clans.php" );
	include_js( "/js/ii_a.js" );

	f_MConnect( );

	if( !check_cookie( ) )
		die( "�������� ��������� Cookie" );

	$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
	
	if( $player->Rank( ) == 0 )
	{
		die( '� ��� ������������ ���� ��� ��������� ���� ��������' );
	}
	
	$player_login = htmlspecialchars( conv_utf( $_GET['nick'] ), ENT_QUOTES );
	
	$res = f_MQuery( "SELECT player_id FROM characters WHERE login = '$player_login'" );
	if( !mysql_num_rows( $res ) )
	{
		die( '��� ������ ������' );
	}
	
	$arr = f_MFetch( $res );
	
	$target = new Player( $arr[0] );
	if ($target->Rank()==1 && $player->Rank()!=1) die( '� ��� ������������ ���� ��� ��������� ���� ��������' );
?>
<html>
<head>
	<meta http-equiv=Content-Type content="text/html; charset=windows-1251" />
	<link href="style.css" rel="stylesheet" type="text/css" />
	<script src="/js/jquery/main.js"></script>
	<title><?=$player_login?> - �������� ���������</title>
	<style>
		.toggleBlock
		{
			display: none;
			padding: 0px 0px 20px;
		}
	</style>
</head>
<body style="padding: 5px 10px 20px;">
	<h3>�������� ��������� <?=$player_login?></h3>
	��������������� ����: <b><?=( $tmp = f_MValue( "SELECT `regdate` FROM `characters` WHERE `login` = '$player_login'" ) ) ? date( 'd.m.Y H:i', $tmp ) : '��������'?></b><br />
	<?=( $tmp = f_MValue( "SELECT `ref_id` FROM `player_invitations` WHERE `player_id` = {$target->player_id}" ) ) ? '��������������� �� ����������� <b>'.f_MValue( "SELECT `login` FROM `characters` WHERE `player_id` = $tmp" ).'</b>' : ''?>
	<br />
	<br />
	���� ����� �� ������� ����� � ���: <a href="/player_control.php?nick=<?=$player_login?>&uncombat">��������</a><br />
	<br />
	<a href="/player_control.php?nick=<?=$player_login?>&dopros">���������� �� ������ � @500</a>
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
		
		$defendant->syst3( '��� �������� ������� � <b>@500</b> ��� ������� � ��������� ������� �� ��������� ���������.<br><br>���� ����������� �����, ���� � ��� "/proom 500", ����� ������� ������� � ����� ������. ������������� ������ ���� � ��������� ���� ��� ����� ����� ������.<br><br>�������� ����, ������������� ������.' );
		$player->syst2( "{$defendant->login} �������".( ( $defendant->sex ) ? '�' : '' ).' ��������� ������� � @500' );
		echo "�������� ������ �� ������";
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
			echo "��� �� �����... : ".$ccarr['turn_done'];
		}
	}
	
	?>
	
	<a href="javascript://" onclick="$( '#banomet' ).toggle( )"><h3>������</h3></a>
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
			echo "<b><font color=red>������ ������������� ����� ����������� �� ������������ ����. ��������� �������� �� ��������.<br>����� �������� ������������ ���� - ����������� 999 ���� � ������������ ���������</font></b>";
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

							if( $value == "ban" ) $type = "������������";
							else if( $value == "silence" ) $type = "�������� ��������";
							else if( $value == "trade" ) $type = "������� ������ �� �������� � �����";
							else if( $value == "fights" ) $type = "������� ������ �� ���";
							else $type = "����������� ���������";
							
							f_MQuery( "INSERT INTO history_punishments ( time, moderator_login, player_id, reason, duration, type, comments ) VALUES ( ".time( ).", '{$player->login}', {$target->player_id}, '$reason', $delta, '$type', '$comms' )" );
							
							if( $value == "ban" )
							{ // ������� � �������
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
		if( $value == "ban" ) $type = "�������������";
		else if( $value == "silence" ) $type = "����� ��������";
		else if( $value == "trade" ) $type = "���� ������ �� �������� � �����";
		else if( $value == "fights" ) $type = "���� ������ �� ���";
		else $type = "����������� ���������";
		f_MQuery( "INSERT INTO history_punishments ( time, moderator_login, player_id, reason, duration, type ) VALUES ( ".time( ).", '{$player->login}', {$target->player_id}, '', 0, '$type' )" );
	}
}

function moo( $a, $b )
{
	global $player_login;
	$st = '<form method="GET">';
	$st .= '<input type=hidden name=tp value=' . $b . '>';
	$st .= '<input type=hidden name=nick value="' . $player_login . '">';
	$st .= $a.'<input type=text name=days value=0 size=3 maxlength=3> ����, ';
	$st .= '<input type=text name=hours value=0 size=3 maxlength=3> �����, ';
	$st .= '<input type=text name=minutes value=0 size=3 maxlength=3> �����, ';
	$st .= '<input type=text name=seconds value=0 size=3 maxlength=3> ������ ';
	$st .= '�������: <input type=text name=reason value="(������� �������)" size=20 maxlength=100> ';
	$st .= '�����������: <input type=text name=comms value="�� ���������" size=20 maxlength=255>';
	$st .= '<input type=submit value="������!">';
	$st .= '</form>';
	return $st;
}

if( $target->GetPermission( "ban" ) > 0 )
{
	echo "<a href='player_control.php?nick=$player_login&clear=ban'>����� ���������� ���������</a><br />";
}
else
{
	echo moo( '������������� ��������� ��: ', 'ban' );
}

if( $target->GetPermission( "silence" ) > 0 )
{
	echo "<a href='player_control.php?nick=$player_login&clear=silence'>����� �������� � ���������</a><br />";
}
else
{
	echo moo( '�������� �������� ��: ', 'silence' );
}

if( $target->GetPermission( "trade" ) > 0 )
{
	echo "<a href='player_control.php?nick=$player_login&clear=trade'>����� ������ �� �������� � ���������</a><br />";
}
else
{
	echo moo( '�������� ������ �� �������� ��: ', 'trade' );
}

if( $target->GetPermission( "fights" ) > 0 )
{
	echo "<a href='player_control.php?nick=$player_login&clear=fights'>����� ������ �� ��� � ���������</a><br />";
}
else
{
	echo moo( '�������� ������ �� ��� ��: ', 'fights' );
}


echo "</div><a href=\"javascript://\" onclick=\"$( '#journals' ).toggle( )\"><h3>�������</h3></a><div id=\"journals\" class=\"toggleBlock\">";

print( "<a href=history_visits.php?player_id={$target->player_id} target=_blank>���������� � ���������� ����</a><br>" );
print( "<a href=history_punishments.php?player_id={$target->player_id} target=_blank>���������� � ����������</a><br>" );
print( "<a href=history_trades.php?player_id={$target->player_id} target=_blank>���������� � �������</a><br>" );
print( "<a href=history_post.php?player_id={$target->player_id} target=_blank>���������� � �������� ���������</a><br>" );
print( "<a href=history_fights.php?player_id={$target->player_id} target=_blank>���������� � ����</a><br>" );

if( $player->Rank( ) == 1 )
{
	print( "<a href=history_payments.php?player_id={$target->player_id} target=_blank>���������� � ������</a><br>" );
	
	echo '<br />';
	echo "<a href='/admin86006609098moo/player_log.php?login=$player_login&item_id=-3' target='_blank'>���� �� ��� � ��������</a><br />";
	echo "<a href='/admin86006609098moo/player_log.php?login=$player_login&item_id=-2' target='_blank'>�������� �����, �������� � �����</a><br />";
	echo "<a href='/admin86006609098moo/player_log.php?login=$player_login&item_id=0' target='_blank'>�������� �����</a><br />";
	echo "<a href='/admin86006609098moo/player_log.php?login=$player_login&item_id=-1' target='_blank'>�������� ��������</a><br />";	
}

echo "</div>";


if( true )
{
	print( "<a href=\"javascript://\" onclick=\"$( '#sovpadenija' ).toggle( )\"><h3>����������</h3></a><div id=\"sovpadenija\" class=\"toggleBlock\">" );
   echo "<div style='width:300px; text-align:center;'>" . ScrollLightTableStart() . "<table width='100%' border='0'>";
   $i = 0;
   
  	$cres = f_MQuery( "SELECT distinct player_id,login_ip FROM `history_logon_logout` WHERE login_ip != '127.0.0.1' AND login_ip=(SELECT login_ip FROM `history_logon_logout` WHERE player_id='{$arr[0]}' ORDER BY login_time DESC LIMIT 0,1) AND player_id!=172 AND player_id!='{$arr[0]}' ORDER BY login_time DESC" ); //  
	while($carr = f_MFetch( $cres ))
	{
	   echo "<tr><td>";
		$plr = new Player( $carr[player_id] );
		echo "<script>document.write( ".$plr->Nick( )." );</script><a href=\"/player_control.php?nick={$plr->login}\" target=\"_blank\" title=\"�������� ��������� {$plr->login}\"><img src=\"/images/c.gif\" style=\"width: 11px; height: 11px; border: 0px;\" /></a> IP: ";
		echo ( $player->Rank( ) == 2 ) ? md5( $carr[login_ip].'DksDS$kfdsl04$3dfgkl' ) : $carr[login_ip];
      echo "</td></tr>";
      $i++;
	}

	if (!$i)
	{
	   echo "��� �����������";
	}
	
	echo "</table>" . ScrollLightTableEnd() . "</div></div>";

	// ��������
	
	echo "<a href=\"javascript://\" onclick=\"$( '#referals' ).toggle( )\"><h3>��������</h3></a><div id=\"referals\" class=\"toggleBlock\" style='width:300px; text-align:center;'>".GetScrollTableStart( );
	$res = f_MQuery( "SELECT player_id FROM player_invitations WHERE ref_id={$target->player_id}" );
	
	if( !f_MNum( $res ) )
	{
		echo "����� ������ �� ���������";
	}
		
	while( $arr = f_MFetch( $res ) )
   {
   	$plr = new Player( $arr[0] );
   	echo "<script>document.write( ".$plr->Nick( )." );</script><a href=\"/player_control.php?nick={$plr->login}\" target=\"_blank\" title=\"�������� ��������� {$plr->login}\"><img src=\"/images/c.gif\" style=\"width: 11px; height: 11px; border: 0px;\" /></a><br />";
   }

	echo  GetScrollTableEnd( )."</div>";
}
?>
</body>
</html>