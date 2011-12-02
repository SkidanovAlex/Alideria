<?
	include_once( "no_cache.php" );
?>
<html>
<head>
	<title>Моя Алидерия</title>
	<meta http-equiv=Content-Type content="text/html; charset=windows-1251" />
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	<link rel="icon" type="image/png" href="favicon.png" />
	<link href="style2.css" rel="stylesheet" type="text/css" />
</head>
<body>
<?
	include_once( "functions.php" );
	include_once( 'player.php' );
	include_once( 'profile.php' );
	include_once( 'attrib_functions.php' );
	include_once( "items.php" );
	include_once( "skin.php" );
	include_once( "arrays.php" );
	include_once( "get_place_name.php" );
	include_once( "guild.php" );
	include_once( "card.php" );
	include_once( "pet.php" );
	include_js( "./js/skin.js" );
	include_js( "./js/skin2.js" );

	f_MConnect( );

	$my_id = -1;
	if( check_cookie( ) )
		$my_id = (int)( $HTTP_COOKIE_VARS['c_id'] );

	$nick = '';
	$player_id = -1;

	if( isset( $_GET['nick'] ) )
		$nick = $_GET['nick'];
	elseif( $my_id != -1 )
		$player_id = $my_id;
	else
		die( );
	
	if( strlen( $nick ) > 0 || $player_id != -1 )
	{
		if( strlen( $nick ) > 0 )
		{
			$result = f_MQuery( "SELECT player_id, pswrddmd5 FROM characters WHERE login='$nick'" );

			if( mysql_num_rows( $result ) == 0 )
			{
				$nick = iconv( "UTF-8", "CP1251", $nick );
				$result = f_MQuery("SELECT player_id, pswrddmd5 FROM characters WHERE login='$nick'");
			}
		}
		else
			$result = f_MQuery( "SELECT player_id, pswrddmd5 FROM characters WHERE player_id='$player_id'" );

		if( mysql_num_rows( $result ) > 0 )
		{
			include_js( './js/clans.php' );
			include_js( './js/ii.js' );
			include_js( './js/cc.js' );
			include_js( './functions.js' );
			include_js( './js/tooltips.php' );

			$line = mysql_fetch_array( $result, MYSQL_ASSOC );
			$id	= $line['player_id'];
			$is_mob = ( $line['pswrddmd5'] && strlen( $line['pswrddmd5'] ) < 32 );
			if( $is_mob )
				$mob_id = $line['pswrddmd5'];

			$player = new Player( $id );
			$profile = new Profile( $id );
			$stats = $player->getAllAttrNames( );
			?>
	<center>
	<table style="width:815px;height:100%;">
		<colgroup>
			<col width="220"><col width="*">
		<tr>
			<td colspan="2">
				<script>FLUc();</script>
				<table style="width:100%;padding:0;margin:0;border:0;">
					<tr>
						<td style="width:30%;text-align:center;">
							<script>document.write( <?=$player->Nick( )?> );</script>
						</td>
						<td style="width:70%;text-align:right;">
							<form method="GET" style="padding:0;margin:0;">
								<input name="nick" class="te_btn"> <input type="submit" class="ss_btn" value="Найти">
							</form>
						</td>
					</tr>
				</table>
				<script>FLL();</script>
			</td>
		</tr>
		<tr>
			<td style="vertical-align:top;">
				<div style="width:248px;height:321px;background:url(/images/ibg.jpg);position:relative;top:0px;left:0px;margin-bottom:5px;" id="moo">
					<img style="position:absolute;width:100px;height:225px;" id="avatar" onclick="window.open('./show_avatar.php?i=images/avatars/<?=$player->getAvatar( )?>', '_blank', 'width=200,height=450,toolbar=no,status=no,scrollbars=no,menubar=no,resizable=no');">
					<img style="position:absolute;width:64px;height:90px" id="pet_img" src="./empty.gif" />
					<div style="position:absolute;width:50px;height:50px;" id="item1">&nbsp;</div>
					<div style="position:absolute;width:50px;height:50px;" id="item2">&nbsp;</div>
					<div style="position:absolute;width:50px;height:50px;" id="item3">&nbsp;</div>
					<div style="position:absolute;width:50px;height:50px;" id="item4">&nbsp;</div>
					<div style="position:absolute;width:25px;height:25px;" id="item5">&nbsp;</div>
					<div style="position:absolute;width:25px;height:25px;" id="item6">&nbsp;</div>
					<div style="position:absolute;width:25px;height:25px;" id="item7">&nbsp;</div>
					<div style="position:absolute;width:25px;height:25px;" id="item8">&nbsp;</div>
					<div style="position:absolute;width:25px;height:25px;" id="item9">&nbsp;</div>
					<div style="position:absolute;width:50px;height:50px;" id="item10">&nbsp;</div>
					<div style="position:absolute;width:50px;height:50px;" id="item11">&nbsp;</div>
					<div style="position:absolute;width:50px;height:50px;" id="item12">&nbsp;</div>
					<div style="position:absolute;width:50px;height:50px;" id="item13">&nbsp;</div>
					<div style="position:absolute;width:50px;height:50px;" id="item14">&nbsp;</div>
					<div style="position:absolute;width:50px;height:50px;" id="item15">&nbsp;</div>
					<div style="position:absolute;width:50px;height:50px;" id="item16">&nbsp;</div>
					<div style="position:absolute;width:50px;height:50px;" id="item_drag">&nbsp;</div>
				</div>

			<?
				include_js( './js/char_inv.php' );
				include_js( './js/char_inv3.php' );
				echo '<script>';

				$res = f_MQuery( "SELECT items.*, player_items.weared FROM items, player_items WHERE player_id = $id AND items.item_id = player_items.item_id AND player_items.weared > 0" );
				while( $arr = f_MFetch( $res ) )
				{
					$descr = itemFullDescr( $arr );
					echo 'wear( '.$arr[item_id].', "'.$arr[name].'", "'.$descr.'", "'.$arr[image].'", '.$arr[weared].' );';
				}

				echo 'set_avatar( "'.str_replace( '.jpg', '.png', $player->getAvatar( ) ).'" );';
		
				$pet_arr = f_MFetch( f_MQuery( "SELECT pets.*, player_pets.level, player_pets.name as nick FROM pets INNER JOIN player_pets ON pets.pet_id=player_pets.pet_id WHERE player_pets.player_id={$player->player_id} AND chosen=1" ) );
				if( $pet_arr )
				{
					$descr = PetGetDescr( $pet_arr );
					echo 'set_pet( "'.$pet_arr[image].'", "'.$descr.'" );';
				}
				echo 'show_char( _( "moo" ) );char_set_events_noinv( );</script>';
				
				ScrollLightTableStart( );
				
				$attrImgUrl = './images/icons/attributes/';
				$attrImg = array( 'w_ic1.gif', 'wm.gif', 'wa.gif', 'wd.gif', 'luck.gif', 'e_ic4.gif' , 'e_ic1.gif', 'nm.gif', 'na.gif', 'nd.gif', 'o.gif', 'speed.gif' , 'f_ic1.gif', 'fm.gif', 'fa.gif', 'fd.gif', 'c.gif', 'r.gif' );
				$attrId = array( 30, 130, 131, 132, 13, 224, 40, 140, 141, 142, 15, 14, 50, 150, 151, 152, 16, 222 );
				$attrTitle = array( 'Магия Воды', 'Мана Воды', 'Атака Магии Воды', 'Защита Магии Воды', 'Удача', 'Восстановление Жизни', 'Магия Природы', 'Мана Природы', 'Атака Магии Природы', 'Защита Магии Природы', 'Отдача', 'Скорость', 'Магия Огня', 'Мана Огня', 'Атака Магии Огня', 'Защита Магии Огня', 'Критический Удар', 'Регенерация' );
				$trSize = 6;
				$count = count( $attrId );
				echo '<table style="width:160px;text-align:center;padding-top:10px;">';
				echo '<tr><td><img src="'.$attrImgUrl.'hp.gif" align="absmiddle" title="Жизнь" style="width:20px;height:20px;"></td><td colspan="4">'.( $player->GetAttr( 1 ) / $player->GetAttr( 101 ) ).'</td><td>'.$player->GetAttr( 101 ).'</td></tr>';
				echo '<tr>';
				for( $i = 0; $i < $count; ++ $i )
				{
					echo '<td><img src="'.$attrImgUrl.$attrImg[$i].'" title="'.$attrTitle[$i].'" style="height:20px;width:20px;"><br>'.$player->GetAttr( $attrId[$i] ).'</td>';
					if( ( $i + 1 ) % $trSize == 0 )
						echo '</tr><tr>';
				}
				echo '<td></td><td></td><td></td><td></td><td></td><td></td></tr></table>';
				ScrollLightTableEnd( );

				echo '</td><td style="vertical-align:top;height:100%;">';

				ScrollLightTableStart( );
			?>
			<table style="width:100%;height:100%;border:1px;" cellspacing="0" cellpadding="0">
				<colgroup>
					<col width=*>
				<tr>
					<td>
						<table cellspacing="0" cellpadding="0" style="width:100%;height:25px;">
							<tr>
							<?
								$page_id = 0;
								if( $_GET['page'] )
									$page_id = $_GET['page'];
									
								$prefix = '?'.( ( isset( $nick ) ) ? 'nick='.$nick : 'id='.$id ).'&page=';
								
								$titles = array( 'Персонаж', 'Описание' );
								$n = count( $titles );
								
								for( $tab_id = 0; $tab_id < $n; ++ $tab_id )
								{
									$border = 'border:1px solid #696057';
									if( $tab_id == $page_id )
										$border .= ';border-bottom:0';

									if( $tab_id != $page_id )
										$titles[$tab_id] = '<a href="'.$prefix.$tab_id.'">'.$titles[$tab_id].'</a>';
									else
										$titles[$tab_id] = '<b>'.$titles[$tab_id].'</a>';
									?>
									<td style="<?=$border?>;padding:5px 25px;">
										<?=$titles[$tab_id]?>
									</td>
									<?
								}
							?>
								<td style="border-bottom:1px solid #696057;width:100%;">&nbsp;</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td style="border:1px solid #696057;border-top:0;vertical-align:top;height:100%;">
						<div id="container;" style="margin-top:10px;padding:0 4px 10px;">
			<?
				switch( $page_id )
				{
					case 0:
						echo '<table style="width:100%;"><colgroup><col width="120px" valign="top"><col width=*>';
						if( $profile->name )
							echo '<tr><td><b>Имя:</b></td><td>'.$profile->name.'</td></tr>';
						echo '<tr><td><b>День Рождения:</b></td><td>'.date( "d.m.Y", $profile->birthday ).'</td></tr>';
						if( $profile->city )
							echo '<tr><td><b>Город:</b></td><td>'.$profile->city.'</td></tr>';
						if( $profile->quote )
							echo '<tr><td><b>Девиз:</b></td><td>'.$profile->quote.'</td></tr>';
						if( $profile->show_email )
							echo '<tr><td><b>E-mail:</b></td><td>'.$profile->email.'</td></tr>';
						if( $profile->icq )
							echo '<tr><td><b>ICQ:</b></td><td>'.$profile->icq.'</td></tr>';
						if( $profile->skype )
							echo '<tr><td><b>Skype:</b></td><td>'.$profile->skype.'</td></tr>';

						echo '<tr><td height=100%><b>Где находится:</b></td><td>';
						echo GetPlaceName( $player->location, $player->depth ).', '.$loc_names[$player->location];
						echo '</td></tr><tr><td><b>Статус:</b></td><td>';
						$ores = f_MQuery( "SELECT * FROM online WHERE player_id={$player->player_id}" );
						if( f_MNum( $ores ) )
						{
							echo '<img src="./images/locator/1.gif" style="width:11px;height:11px;" title="'.my_time_str( time( ) - f_MValue( "SELECT login_time FROM history_logon_logout WHERE player_id={$player->player_id} ORDER BY entry_id DESC LIMIT 1" ), false ).'">&nbsp;';
							$userStatus = 'В игре';
						}
						else
						{
							echo '<img src="./images/locator/0.gif" style="width:11px;height:11px;" title="'.my_time_str( time( ) - f_MValue( "SELECT logout_time FROM history_logon_logout WHERE player_id={$player->player_id} ORDER BY entry_id DESC LIMIT 1" ), false ).'">&nbsp;';
							$userStatus = 'В реале';
						}
						
						// Статус
						/*$cres = f_MQuery( "SELECT text FROM social_status WHERE player_id={$player->player_id}" );
						$carr = f_MFetch( $cres );
						if( $carr )
							echo $carr[0];*/
						else if( $player->regime >= 101 && $player->regime <= 104 || $player->regime == 106 || $player->regime == 108 || $player->regime == 110 || $player->regime == 111 || $player->regime == 250 )
						{
							$ores = f_MQuery( "SELECT * FROM online WHERE player_id={$player->player_id}" );
							if( $player->regime == 101 || $player->regime == 102 ) echo 'В сделке'; 
							if( $player->regime == 103 ) echo 'Делает вещи'; 
							if( $player->regime == 104 ) echo 'Добывает ресурсы'; 
							if( $player->regime == 106 ) echo 'Чинит вещи'; 
							if( $player->regime == 108 ) echo 'В дозоре'; 
							if( $player->regime == 110 ) echo 'Общается с NPC'; 
							if( $player->regime == 111 ) echo 'Играет в Магию'; 
							if( $player->regime == 250 ) echo 'Восстанавливается у Лекаря'; 
						}
						else
						{
							$cres = f_MQuery( "SELECT combat_id FROM combat_players WHERE player_id=$player->player_id" );
							$carr = f_MFetch( $cres );
							if( $carr )
								echo '<a href="./combat_log.php?id='.$carr[0].' target="_blank">В бою</a>';
							else
								echo $userStatus;
						}
						echo '</td></tr>';

						$gres = f_MQuery( "SELECT * FROM player_guilds WHERE player_id = {$player->player_id}" );
						if( f_MNum( $gres ) )
						{
							echo '<tr><td><b>Гильдия:</b></b></td><td>';
							$tmp = '';
							while( $garr = f_MFetch( $gres ) )
								$tmp .= '<a href="./guilds_table.php?page='.$garr['guild_id'].'" target="_blank">'.$guilds[$garr['guild_id']][0].'</a> ['.$garr['rank'].'], ';
							echo substr( $tmp, 0, strlen( $tmp ) - 2 ).'</td></tr>';
						}

						$clres = f_MQuery( "SELECT * FROM player_clans WHERE player_id = {$player->player_id}" );
						$clarr = f_MFetch( $clres );
						if( $clarr )
						{
							$clan_id = $clarr['clan_id'];
							$name = f_MFetch( f_MQuery( "SELECT name FROM clans WHERE clan_id=$clan_id" ) );
							$name = $name[0];
							echo '<tr><td><b>'.$name.':</b></td><td>';
							$rank = f_MFetch( f_MQuery( "SELECT name FROM clan_ranks WHERE clan_id=$clan_id AND rank=$clarr[rank]" ) );
							if( $rank )
							{
								$rank = $rank[0];
								echo $rank;
							}
							$job = f_MFetch( f_MQuery( "SELECT name FROM clan_jobs WHERE clan_id=$clan_id AND job=$clarr[job]" ) );
							if( $rank )
							{
								$job = $job[0];
								echo ', '.$job;
							}
							echo '</td></tr>';
						}

						echo '<tr><td colspan="2"><br><b>Статистика боев</b></td></tr>';
						$sarr = f_MFetch( f_MQuery( "SELECT * FROM player_statistics WHERE player_id={$player->player_id}" ) );
						if( !$sarr )
						{
							$sarr['pvp_w'] = 0; $sarr['pvp_l'] = 0;
							$sarr['npc_w'] = 0; $sarr['npc_l'] = 0;
						}
						echo '<tr><td><b>С игроками:</td><td>'.$sarr[pvp_w].'/'.$sarr[pvp_l].'</td></tr>';
						echo '<tr><td><b>C мобами:</td><td>'.$sarr[npc_w].'/'.$sarr[npc_l].'</td></tr>';

						//удаляем подарок
						if( $my_id == $player->player_id && isset( $_GET['drop'] ) )
						{
							$drop_id = (int)$_GET['drop'];
							f_MQuery( "DELETE FROM player_presents WHERE player_id=$my_id AND entry_id=$drop_id" );
						}
						// конец удаления подарка

						$prres = f_MQuery( "SELECT * FROM player_presents WHERE player_id={$player->player_id}" );
						if( f_MNum( $prres ) )
						{
							$dst = "";
							if( $my_id == $player->player_id )
							{
								?>
								<script>
								function drop_present( id )
								{
									if( confirm( 'Выбросить подарок?' ) )
										location.href = './player_info.php?id=<?=$my_id?>&drop=' + id;
								}
								</script>
								<?
							}
							echo '<tr><td colspan="2"><b>Подарки</b></td></tr><tr><td colspan="2">';
							while( $prarr = f_MFetch( $prres ) )
								echo ' <img '.( ( $my_id == $player->player_id ) ? ' style="cursor:pointer;" onclick="drop_present( '.$prarr[entry_id].' )" ' : '' )." onmousemove='showTooltipW( event, \"<img align=left width=150 height=150 src=images/presents/$prarr[img]><b>Подарок от $prarr[author]</b><br>".str_replace( "&quot;", "\\&quot;", str_replace( "\r", "", str_replace( "\n", "<br>", $prarr[txt] ) ) )."<br><br><small><i>Закончится через:<br>".my_time_str2( $prarr['deadline'] - time( ), false )."</i></small>\", 350 )' onmouseout='hideTooltip( )' width=75 height=75 src='images/presents/$prarr[img]'>";
							echo '</td></tr>';
						}

						if( $is_mob )
						{
							echo '<tr><td colspan="2"><br><b>Информация о монстре</b></td></tr><tr><td colspan="2">';
							include_once( 'beast.php' );
							$b = new Beast( $mob_id );
							if( $b->mob_id )
							{
								$b->ShowCards( );
								echo '<br>';
								$b->ShowDrop( );
							}
							else $b->ShowCards( $player->player_id );
							echo '</td></tr>';
						}
					
						// Наказания
						if( $player->GetPermission( 'ban' ) ||  $player->GetPermission( 'silence' ) ||
							$player->GetPermission( 'trade' ) || $player->GetPermission( 'fights' ) )
						{
							echo '<tr><td colspan="2"><br><b>Наказания</b></td></tr><tr><td colspan="2"><i>';
							if( ( $val = $player->GetPermission( "ban" ) ) )
								echo 'Персонаж заблокирован. Причина: '.$player->PermissionReason( 'ban' ).'. До снятия блокировки осталось: '.my_time_str( $val ).'.<br>';
							if( ( $val = $player->GetPermission( "silence" ) ) )
								echo 'На персонажа наложен запрет на общение в чате и на форуме. Причина: '.$player->PermissionReason( 'silence' ).'. До снятия запрета осталось: '.my_time_str( $val ).'.<br>';
							if( ( $val = $player->GetPermission( "trade" ) ) )
								echo 'На персонажа наложен запрет на торговлю и обмен. Причина: '.$player->PermissionReason( 'trade' ).'. До снятия запрета осталось: '.my_time_str( $val ).'.<br>';
							if( ( $val = $player->GetPermission( "fights" ) ) )
								echo 'Персонаж получает половину опыта и не получает деньги за бои. Причина: '.$player->PermissionReason( 'fights' ).'. До снятия запрета осталось: '.my_time_str( $val ).'.<br>';
							echo '</i></td></tr>';
						}
						// Наказания - конец
						echo '</table>';
						break;
					
					case 1:
						$descr = str_replace( "\n", '<br>', $profile->descr );
						if( trim( $descr ) )
							echo '<div style="text-align:justify">'.$descr.'</div>';
						break;
				}
				echo '</div></td></tr></table>';

				ScrollLightTableEnd( );

				echo "</td></tr>";
				echo "</table>";
		}
		else
			echo 'Нет такого игрока';
	}
	f_MClose();
?>
</body>
</html>