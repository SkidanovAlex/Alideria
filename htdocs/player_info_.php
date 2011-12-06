<?

include_once( "no_cache.php" );

?>
<meta http-equiv=Content-Type content="text/html; charset=windows-1251" />
<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
<link rel="icon" type="image/png" href="favicon.png">
<link href="style2.css" rel="stylesheet" type="text/css">
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
include_once( "feathers.php" );
include_js( "js/skin.js" );
include_js( "js/skin2.js" );

f_MConnect();

$my_id = -1;
if( check_cookie( ) )
	$my_id = (int)( $HTTP_COOKIE_VARS['c_id'] );
if ($my_id != 6825)
	die();

$nick = '';
$player_id = -1;

if( isset( $_GET['id'] ) )
{
	$player_id = $_GET['id'];
	settype( $player_id, 'integer' );
}

else if(isset($_GET['nick']))
{
	$nick = $_GET['nick'];
	$nick = htmlspecialchars( $nick, ENT_QUOTES );
}

if($nick != '' || $player_id != -1)
{
	if( $nick != '' )
	{
    	$result = f_MQuery("SELECT player_id, pswrddmd5 FROM characters WHERE login='$nick' ORDER BY player_id DESC limit 1");

    	if( mysql_num_rows($result) == 0 )
    	{
    		$nick = iconv("UTF-8", "CP1251", $nick );
    		$result = f_MQuery("SELECT player_id, pswrddmd5 FROM characters WHERE login='$nick' ORDER BY player_id DESC limit 1");
    	}
    }
    else $result = f_MQuery("SELECT player_id, pswrddmd5 FROM characters WHERE player_id='$player_id'");

	if(mysql_num_rows($result) > 0)
	{
		include_js( 'js/clans.php' );
		include_js( 'js/ii_a.js' );
		include_js( 'js/cc.js' );
		include_js( 'functions.js' );
		include_js( 'js/tooltips.php' );

		$line	= mysql_fetch_array($result, MYSQL_ASSOC);
		$id		= $line['player_id'];
		$is_mob = ( $line['pswrddmd5'] && strlen( $line['pswrddmd5'] ) < 32 );
		if( $is_mob ) $mob_id = $line['pswrddmd5'];


		$player = new Player($id);
		$profile = new Profile($id);
		$stats = $player->getAllAttrNames( );
	
		echo "<title>{$player->login} - Алидерия</title>";
		
		echo "<center><br><table width=90%><colgroup><col width=10><col width=*><tr><td>&nbsp;</td><td><script>FLUc();document.write(".$player->Nick( ).");FLL();</script></td></tr></table><br><table width=90%><colgroup><col width=10><col width=220><col width=*><tbody>";
		echo "<tr>";
		echo "<td vAlign=top>";

		echo "</td>";
		echo "<td vAlign=top>";

		?>

		<div style='width:248px;height:321px;background:url(images/ibg.jpg);position:relative;top:0px;left:0px;' id=moo name=moo width=248 height=200>

			<img style='position: absolute;' width=100 height=225 id=avatar name=avatar onclick="window.open('/show_avatar.php?i=images/avatars/<?=$player->getAvatar( )?>', '_blank', 'width=200,height=450,toolbar=no,status=no,scrollbars=no,menubar=no,resizable=no');">
			<img style='position: absolute;' width=64 height=90 id=pet_img name=pet_img src='empty.gif'>

			<div style='position: absolute;' width=50 height=50 id=item1 name=item1>&nbsp;</div>
			<div style='position: absolute;' width=50 height=50 id=item2 name=item2>&nbsp;</div>
			<div style='position: absolute;' width=50 height=50 id=item3 name=item3>&nbsp;</div>
			<div style='position: absolute;' width=50 height=50 id=item4 name=item4>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=item5 name=item5>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=item6 name=item6>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=item7 name=item7>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=item8 name=item8>&nbsp;</div>
			<div style='position: absolute;' width=25 height=25 id=item9 name=item9>&nbsp;</div>
			<div style='position: absolute;' width=50 height=50 id=item10 name=item10>&nbsp;</div>
			<div style='position: absolute;' width=50 height=50 id=item11 name=item11>&nbsp;</div>
			<div style='position: absolute;' width=50 height=50 id=item12 name=item12>&nbsp;</div>
			
			<div onmousemove=show_pots(1) style='position: absolute;top:285px;left:30px;' width=25 height=25 id=pot1 name=pot1>
				<img src='images/items/bg/bg25pot.gif'>
				<div onmouseout=hide_pots(1) id=pots1 name=pots1 style='position: absolute;top:-10px;left:-10px;display: none;'>
					<img src='images/rect/panel.jpg'>
					<div style='position: absolute;' width=50 height=50 id=item13 name=item13>&nbsp;</div>
					<div style='position: absolute;' width=50 height=50 id=item14 name=item14>&nbsp;</div>
					<div style='position: absolute;' width=50 height=50 id=item15 name=item15>&nbsp;</div>
					<div style='position: absolute;' width=50 height=50 id=item16 name=item16>&nbsp;</div>
				</div>
			</div>

			<div onmousemove=show_pots(2) style='position: absolute;top:285px;left:55px;' width=25 height=25 id=pot2 name=pot2>
				<img src='images/items/pot_sq/ten_talismana.png'>
				<div onmouseout=hide_pots(2) id=pots2 name=pots2 style='position: absolute;top:-10px;left:-10px;display: none;'>
					<img src='images/rect/panel.jpg'>
					<div style='position: absolute;' width=50 height=50 id=item17 name=item17>&nbsp;</div>
					<div style='position: absolute;' width=50 height=50 id=item18 name=item18>&nbsp;</div>
					<div style='position: absolute;' width=50 height=50 id=item19 name=item19>&nbsp;</div>
					<div style='position: absolute;' width=50 height=50 id=item20 name=item20>&nbsp;</div>
				</div>
			</div>

			<div onmousemove=show_pots(3) style='position: absolute;top:285px;left:80px;' width=25 height=25 id=pot3 name=pot3>
				<img src='images/items/pot_sq/ten_medaliona.png'>
				<div onmouseout=hide_pots(3) id=pots3 name=pots3 style='position: absolute;top:-10px;left:-10px;display: none;'>
					<img src='images/rect/panel.jpg'>
					<div style='position: absolute;' width=50 height=50 id=item21 name=item21>&nbsp;</div>
					<div style='position: absolute;' width=50 height=50 id=item22 name=item22>&nbsp;</div>
					<div style='position: absolute;' width=50 height=50 id=item23 name=item23>&nbsp;</div>
					<div style='position: absolute;' width=50 height=50 id=item24 name=item24>&nbsp;</div>
				</div>
			</div>

			<div style='position: absolute;' width=50 height=50 id=item_drag name=item_drag>&nbsp;</div>
		</div>

		<?
		
		include_js( 'js/char_inv_.php' );
		include_js( 'js/char_inv3.php' );
		
		?>

		<script>

		<?

		$res = f_MQuery( "SELECT items.*, player_items.weared FROM items, player_items WHERE player_id = $id AND items.item_id = player_items.item_id AND player_items.weared > 0" );
		while( $arr = f_MFetch( $res ) )
		{
			$descr = itemFullDescr( $arr );
			if ($player->player_id==6825 && ($arr[weared]>13 || $arr[weared] == 1))
				$im = $arr[image_large];
			else
				$im = $arr[image];
			print( "\twear( $arr[item_id], '$arr[name]', '$descr', '$im', $arr[weared] );\n" );
		}

		echo "set_avatar( '".str_replace( ".jpg", ".png", $player->getAvatar( ) )."' );";
		
		$pet_arr = f_MFetch( f_MQuery( "SELECT pets.*, player_pets.level, player_pets.name as nick FROM pets INNER JOIN player_pets ON pets.pet_id=player_pets.pet_id WHERE player_pets.player_id={$player->player_id} AND chosen=1" ) );
		if( $pet_arr )
		{
			$descr = PetGetDescr( $pet_arr );
			echo "set_pet( '{$pet_arr[image]}', '{$descr}' );";
		}

		?>
		
		show_char( document.getElementById( 'moo' ) );
		char_set_events_noinv( );

		</script>

		<?
		
		echo "<br>";
		ScrollTableStart( );
		echo "<br>";
		echo "<img src=images/icons/attributes/hp.gif>".$player->GetAttr( 1 )."/".$player->GetAttr( 101 )."<br>";
		echo "<br>";
		$player->ShowSecondaryAttributes( 0 );
		echo "<br>";
		$player->ShowBattleAttributes( 0 );
		echo "<br>";

		$player->ShowPrimaryAttributes( 0 );
		echo "<br>";
		$player->ShowGlobalAttributes( 0 );
		echo "<br>";

		// показ действующих на персонажа аур
		
		$aurasQuery = f_MQuery( "SELECT auras.name, combat_auras.duration FROM combat_auras, auras WHERE combat_auras.player_id={$player->player_id} AND auras.aura_id=combat_auras.aura_id ORDER BY combat_auras.duration DESC" );
		if ( mysql_num_rows( $aurasQuery ) > 0 )
		{
			echo "<table><tr><td width=20>&nbsp;</td><td width=150><b><font color=green>Эффекты:</font></b></td><td>&nbsp;</td></tr>";
			for ( $i = 0; $i < mysql_num_rows( $aurasQuery ); $i++ )
			{
				$aurasArray = f_MFetch( $aurasQuery );
				echo "<tr><td width=20>&nbsp;</td><td width=150><b><font color=#003366>" . $aurasArray["name"] . "</font></b></td><td> <b>" . $aurasArray["duration"] . "</b></td></tr>";
			}
			echo "</table>";
		}
		
		// всё

		ScrollTableEnd( );

		echo "<br>";
		ScrollTableStart( );
		echo "<b>Статистика боев</b>";
		echo "<table>";
		$sarr = f_MFetch( f_MQuery( "SELECT * FROM player_statistics WHERE player_id={$player->player_id}" ) );
		if( !$sarr )
		{
			$sarr['pvp_w'] = 0; $sarr['pvp_l'] = 0;
			$sarr['npc_w'] = 0; $sarr['npc_l'] = 0;
		}
		echo "<tr><td>Побед над игроками:</td><td><b>$sarr[pvp_w]</b></td></tr>";
		echo "<tr><td>Поражений от игроков:</td><td><b>$sarr[pvp_l]</b></td></tr>";
		echo "<tr><td>Побед над NPC:</td><td><b>$sarr[npc_w]</b></td></tr>";
		echo "<tr><td>Поражений от NPC:</td><td><b>$sarr[npc_l]</b></td></tr>";
		echo "</table>";
		ScrollTableEnd( );

		echo "<br>";

		echo "</td><td vAlign=top>";

		ScrollTableStart( );
		
			echo "<div align=right><table><form action=player_info.php method=get><tr><td>Поиск игрока:</td><td><input name=nick class=te_btn></td><td><input type=submit class=ss_btn value='Искать'></td></tr></form></table></div>";

		ScrollTableEnd( );
		echo "<br>";

		ScrollLightTableStart( );

			if( $profile->name == '' ) $profile->name = '&nbsp;';
			if( $profile->city == '' ) $profile->city = '&nbsp;';
			if( $profile->quote == '' ) $profile->quote = '&nbsp;';

			echo "<table width=100%>";
			echo "<tr><td colspan=2>".GetScrollTableStart()."<b>Личные данные</b>".GetScrollTableEnd()."</td></tr>";
			echo "<tr><td height=100%>".GetScrollTableStart(/*"right"*/)."Имя:".GetScrollTableEnd()."</td><td height=100%>".GetScrollTableStart()."{$profile->name}".GetScrollTableEnd()."</td></tr>";
			if( $_COOKIE['c_id'] == 6825 ) { echo "<tr><td height=100%>".GetScrollTableStart(/*"right"*/)."ID:".GetScrollTableEnd()."</td><td height=100%>".GetScrollTableStart()."{$player->player_id}".GetScrollTableEnd()."</td></tr>"; }
			echo "<tr><td height=100%>".GetScrollTableStart(/*"right"*/)."Город:".GetScrollTableEnd()."</td><td height=100%>".GetScrollTableStart()."{$profile->city}".GetScrollTableEnd()."</td></tr>";
			echo "<tr><td height=100%>".GetScrollTableStart(/*"right"*/)."День Рождения:".GetScrollTableEnd()."</td><td height=100%>".GetScrollTableStart()."".date( "d.m.Y", $profile->birthday )."".GetScrollTableEnd()."</td></tr>";
			echo "<tr><td height=100%>".GetScrollTableStart(/*"right"*/)."Девиз:".GetScrollTableEnd()."</td><td height=100%>".GetScrollTableStart()."{$profile->quote}".GetScrollTableEnd()."</td></tr>";

		
			if( $profile->show_email ) echo "<tr><td>".GetScrollTableStart(/*"right"*/)."E-mail:".GetScrollTableEnd()."</td><td>".GetScrollTableStart()."{$profile->email}".GetScrollTableEnd()."</td></tr>";
			if( $profile->icq ) echo "<tr><td>".GetScrollTableStart(/*"right"*/)."ICQ:".GetScrollTableEnd()."</td><td>".GetScrollTableStart()."{$profile->icq}".GetScrollTableEnd()."</td></tr>";
			if( $profile->skype ) echo "<tr><td>".GetScrollTableStart(/*"right"*/)."Skype:".GetScrollTableEnd()."</td><td>".GetScrollTableStart()."{$profile->skype}".GetScrollTableEnd()."</td></tr>";

			$wsex = 1 - $player->sex;
			$warr = f_MFetch( f_MQuery( "SELECT p{$wsex} FROM player_weddings WHERE p{$player->sex} = {$player->player_id}" ) );
			if( $warr )
			{
    			echo "<tr><td height=100%>".GetScrollTableStart(/*"right"*/)."В браке с:".GetScrollTableEnd()."</td><td height=100%>".GetScrollTableStart();
    			$plr = new Player( $warr[0] );
    			echo "<script>document.write( ".$plr->Nick( )." );</script>";
    			echo GetScrollTableEnd()."</td></tr>";
			}
			
			// Наказания
			$has_punishment = false;
			if( ( $val = $player->GetPermission( "ban" ) ) > 0 ) $has_punishment = true;
			if( ( $val = $player->GetPermission( "silence" ) ) > 0 ) $has_punishment = true;
			if( ( $val = $player->GetPermission( "trade" ) ) > 0 ) $has_punishment = true;
			if( ( $val = $player->GetPermission( "fights" ) ) > 0 ) $has_punishment = true;
			
			if( $has_punishment )
			{
				echo "<tr><td colspan=2>".GetScrollTableStart()."<b>Наказания</b>".GetScrollTableEnd()."</td></tr>";
				if( ( $val = $player->GetPermission( "ban" ) ) > 0 ) print( "<tr><td colspan=2>".GetScrollTableStart()."<i>Персонаж заблокирован. Причина: ".$player->PermissionReason( 'ban' ).". До снятия блокировки осталось: ".my_time_str( $val )."</i>".GetScrollTableEnd()."</td></tr>" );
				if( ( $val = $player->GetPermission( "silence" ) ) > 0 ) print( "<tr><td colspan=2>".GetScrollTableStart()."<i>На персонажа наложен запрет на общение в чате и на форуме. Причина: ".$player->PermissionReason( 'silence' ).". До снятия запрета осталось: ".my_time_str( $val )."</i>".GetScrollTableEnd()."</td></tr>" );
				if( ( $val = $player->GetPermission( "trade" ) ) > 0 ) print( "<tr><td colspan=2>".GetScrollTableStart()."<i>На персонажа наложен запрет на торговлю и обмен. Причина: ".$player->PermissionReason( 'trade' ).". До снятия запрета осталось: ".my_time_str( $val )."</i>".GetScrollTableEnd()."</td></tr>" );
				if( ( $val = $player->GetPermission( "fights" ) ) > 0 ) print( "<tr><td colspan=2>".GetScrollTableStart()."<i>Персонаж получает половину опыта и не получает деньги за бои. Причина: ".$player->PermissionReason( 'fights' ).". До снятия запрета осталось: ".my_time_str( $val )."</i>".GetScrollTableEnd()."</td></tr>" );
			}
			// Наказания - конец

			echo "<tr><td colspan=2 height=100%>".GetScrollTableStart(/*"right"*/)."<b>Где Находится:</b>".GetScrollTableEnd()."</td></tr><tr><td height=100%>".GetScrollTableStart();
			if ($player->Rank() == 1)
				echo "Неизвестно";
			else
				echo $loc_names[$player->location];
			echo GetScrollTableEnd()."</td><td height=100%>".GetScrollTableStart();
			if ($player->Rank() == 1)
				echo "Неизвестно";
			else
				echo GetPlaceName( $player->location, $player->depth );
			echo GetScrollTableEnd()."</td></tr>";

			echo "<tr><td height=100%>".GetScrollTableStart();
			echo "Статус:";
			echo GetScrollTableEnd()."</td><td height=100%>".GetScrollTableStart();
			if ($player->Rank() == 1)
				echo "Неизвестно";
			else
		{
			$ores = f_MQuery( "SELECT * FROM online WHERE player_id={$player->player_id}" );
			if( f_MNum( $ores ) )
			{
				echo "<font color=green>ONLINE ".my_time_str( time( ) - f_MValue( "SELECT login_time FROM history_logon_logout WHERE player_id={$player->player_id} ORDER BY entry_id DESC LIMIT 1" ), false )."</font>";
				
				// Время простоя
				$lastPing = f_MValue( 'SELECT last_ping FROM online WHERE player_id = '.$player->player_id );
				$iddle = time( ) - $lastPing;
				if( $iddle > 60 )
				{
					echo "<br /><i>Мог выйти из игры ".my_time_str( $iddle )." назад</i>";
				}									
			}
			else echo "<font color=darkred>OFFLINE ".my_time_str( time( ) - f_MValue( "SELECT logout_time FROM history_logon_logout WHERE player_id={$player->player_id} ORDER BY entry_id DESC LIMIT 1" ), false )."</font>";
		}
			echo GetScrollTableEnd()."</td></tr>";

			if( $player->regime >= 101 && $player->regime <= 104 || $player->regime == 106 || $player->regime == 108 || $player->regime == 110 || $player->regime == 111 || $player->regime == 250 || ($player->regime >= 300 && $player->regime <= 320 ))
			{
				echo "<tr><td height=100%>".GetScrollTableStart();
    			echo "Что делает:";
    			echo GetScrollTableEnd()."</td><td height=100%>".GetScrollTableStart();
			if ($player->Rank() == 1)
				echo "Неизвестно";
			else
		{
    			$ores = f_MQuery( "SELECT * FROM online WHERE player_id={$player->player_id}" );
    			if( $player->regime == 101 || $player->regime == 102 ) echo "В сделке"; 
    			elseif( $player->regime == 103 ) echo "Делает вещи"; 
    			elseif( $player->regime == 104 ) echo "Добывает ресурсы"; 
    			elseif( $player->regime == 106 ) echo "Чинит вещи"; 
    			elseif( $player->regime == 108 ) echo "В дозоре"; 
    			elseif( $player->regime == 110 ) echo "Общается с NPC"; 
    			elseif( $player->regime == 111 ) echo "Играет в Магию"; 
    			elseif( $player->regime == 250 ) echo "Восстанавливается у Лекаря";
			elseif ($player->regime >= 300 && $player->regime <= 320 ) echo "Готовит еду";
		}
    			echo GetScrollTableEnd()."</td></tr>";
			}

			$cres = f_MQuery( "SELECT combat_id FROM combat_players WHERE player_id=$player->player_id" );
			$carr = f_MFetch( $cres );
			if( $carr ) echo "<tr><td height=100%>".GetScrollTableStart(/*"right"*/)."Текущий Бой:".GetScrollTableEnd()."</td><td height=100%>".GetScrollTableStart()."<a href=combat_log.php?id=$carr[0] target=_blank>Смотреть бой</a>".GetScrollTableEnd()."</td></tr>";

			$gres = f_MQuery( "SELECT * FROM player_guilds WHERE player_id = {$player->player_id}" );
			if( f_MNum( $gres ) )
			{
				echo "<tr><td colspan=2 height=100%>".GetScrollTableStart(/*"right"*/)."<b>Состоит в гильдиях:</b>".GetScrollTableEnd()."</td></tr>";
				while( $garr = f_MFetch( $gres ) )
				{
 					echo "<tr><td height=100%>".GetScrollTableStart();
					echo $guilds[$garr['guild_id']][0];
					echo GetScrollTableEnd()."</td><td height=100%>".GetScrollTableStart();
					echo "Ранг: <b>".$garr['rank']."</b>";
					// новый код для копирования
					if ( $guilds[$garr['guild_id']][4] == 1 ) // только для добытчиков
						echo " &nbsp; / &nbsp; Рейтинг: <b>" . $garr['rating'] . "</b>";
					// много, правда?
					echo GetScrollTableEnd()."</td></tr>";
				}
			}

			$clres = f_MQuery( "SELECT * FROM player_clans WHERE player_id = {$player->player_id}" );
			$clarr = f_MFetch( $clres );
			if( $clarr )
			{
				$clan_id = $clarr['clan_id'];
				$name = f_MFetch( f_MQuery( "SELECT name FROM clans WHERE clan_id=$clan_id" ) );
				$name = $name[0];
				echo "<tr><td colspan=2 height=100%>".GetScrollTableStart(/*"right"*/)."<b>$name</b>".GetScrollTableEnd()."</td></tr>";
				$rank = f_MFetch( f_MQuery( "SELECT name FROM clan_ranks WHERE clan_id=$clan_id AND rank=$clarr[rank]" ) );
				if( $rank )
				{
					$rank = $rank[0];
 					echo "<tr><td height=100%>".GetScrollTableStart();
					echo "Звание:";
					echo GetScrollTableEnd()."</td><td height=100%>".GetScrollTableStart();
					echo "<b>".$rank."</b>";
					echo GetScrollTableEnd()."</td></tr>";
				}
				$job = f_MFetch( f_MQuery( "SELECT name FROM clan_jobs WHERE clan_id=$clan_id AND job=$clarr[job]" ) );
				if( $rank )
				{
					$job = $job[0];
 					echo "<tr><td height=100%>".GetScrollTableStart();
					echo "Должность:";
					echo GetScrollTableEnd()."</td><td height=100%>".GetScrollTableStart();
					echo "<b>".$job."</b>";
					echo GetScrollTableEnd()."</td></tr>";
				}
			}

			//удаляем подарок
			if( $my_id == $player->player_id && isset( $_GET['drop'] ) )
			{
				$drop_id = (int)$_GET['drop'];
				f_MQuery( "DELETE FROM player_presents WHERE player_id=$my_id AND entry_id=$drop_id" );
			}
			// конец удаления подарка

			$prres = f_MQuery( "SELECT * FROM player_presents WHERE player_id={$player->player_id} ORDER BY entry_id DESC" );
			if( f_MNum( $prres ) )
			{
				$dst = "";
				if( $my_id == $player->player_id )
				{
					?>
					<script>
					function drop_present(id)
					{
						if( confirm( 'Выбросить подарок?' ) )
							location.href='player_info.php?id=<?=$my_id?>&drop=' + id;
					}
					</script>
					<?
				}
				echo "<tr><td colspan=2 height=100%>".GetScrollTableStart(/*"right"*/)."<b>Подарки</b>".GetScrollTableEnd()."</td></tr>";
				echo "<tr><td colspan=2 height=100%>".GetScrollTableStart(/*"right"*/);
				while( $prarr = f_MFetch( $prres ) ) echo " <img ".(($my_id == $player->player_id)?" style='cursor:pointer;' onclick='drop_present($prarr[entry_id])' ":"")." onmousemove='showTooltipW( event, \"<img align=left width=150 height=150 src=images/presents/$prarr[img]><b>Подарок от " . addslashes(htmlspecialchars($prarr['author'], ENT_QUOTES)) . "</b><br>".(str_replace( "\r", "", str_replace( "\n", "<br>", (addslashes(htmlspecialchars($prarr[txt], ENT_QUOTES))))))."<br><br>".(($prarr['deadline'] == 2147483647)?"":"<small><i>Закончится через:<br>".my_time_str2( $prarr['deadline'] - time( ), false )."</i></small>")."\", 350 )' onmouseout='hideTooltip( )' width=75 height=75 src='images/presents/$prarr[img]'>";
				echo GetScrollTableEnd()."</td></tr>";
			}
			
			$feres = f_MQuery( "SELECT * FROM player_feathers WHERE player_id={$player->player_id} ORDER BY time DESC" );
			if( f_MNum( $feres ) )
			{
				echo "<tr><td colspan=2 height=100%>".GetScrollTableStart(/*"right"*/)."<b>Перышки</b>".GetScrollTableEnd()."</td></tr>";
				echo "<tr><td colspan=2 height=100%>".GetScrollTableStart(/*"right"*/);
				while( $fearr = f_MFetch( $feres ) ) echo " <img onmousemove='showTooltipW( event, \"<b>{$fthrs[$fearr[feather_id]][0]}</b><br>{$fthrs[$fearr[feather_id]][2]}\", 350 )' onmouseout='hideTooltip( )' src=images/items/{$fthrs[$fearr[feather_id]][1]} width=50 height=50>"; //<br><br><small><i>Закончится через:<br>".my_time_str2( $prarr['deadline'] - time( ), false )."</i></small>\", 350 )' onmouseout='hideTooltip( )' width=75 height=75 src='images/presents/$prarr[img]'>";
				echo GetScrollTableEnd()."</td></tr>";
			}

			$effectTypeName = array( 'Эффекты', 'Медали' );
			$effectType = -1;
			$efres = f_MQuery( "SELECT * FROM player_effects WHERE player_id={$player->player_id} ORDER BY type ASC, id DESC" );
			while( $efarr = f_MFetch( $efres ) )
			{
				if( $efarr[type] != $effectType )
				{
					if( $effectType != -1 )
					{
						echo GetScrollTableEnd()."</td></tr>";
					}
					$effectType ++;
					echo "<tr><td colspan=2 height=100%>".GetScrollTableStart(/*"right"*/)."<b>".$effectTypeName[$efarr[type]]."</b>".GetScrollTableEnd()."</td></tr>";
					echo "<tr><td colspan=2 height=100%>".GetScrollTableStart(/*"right"*/);
				}
				echo " <img onmousemove='showTooltipW( event, \"<b>{$efarr[name]}</b><br>".str_replace( "&quot;", "\\&quot;", str_replace( "\\", "\\\\", str_replace( "\r", "", str_replace( "\n", "<br>", $efarr['description'].(($efarr['effect']=="")?"<br>":"<br><br>".ItemEffectStr( $efarr['effect'] ) ) ) ) )).(($efarr['expires'] == -1)?"":"<br><small><i>Закончится через:<br>".my_time_str2( $efarr['expires'] - time( ), false )."</i></small>")."\", 350 )' onmouseout='hideTooltip( )' src='images/effects/$efarr[image]'>";
			}
			if( $effectType != -1 )
			{
				echo GetScrollTableEnd()."</td></tr>";
			}
			
			$descr = str_replace( "\n", "<br>", $profile->descr );
			if( trim( $descr ) != "" )
			{
				echo "<tr><td colspan=2>".GetScrollTableStart()."<b>Описание</b>".GetScrollTableEnd()."</td></tr><tr><td colspan=2>".GetScrollTableStart()."<div align=justify>$descr&nbsp;";
				echo "</div>".GetScrollTableEnd()."</td></tr>";
			}
			if( $is_mob )
			{
				echo "<tr><td colspan=2>".GetScrollTableStart()."<b>Информация о монстре</b>".GetScrollTableEnd()."</td></tr><tr><td colspan=2>".GetScrollTableStart();
				include_once( 'beast.php' );
				$b = new Beast( $mob_id );
				if( $b->mob_id )
				{
					$b->ShowCards( );
					echo "<br>"; $b->ShowDrop( );
				}
				else $b->ShowCards( $player->player_id );
				echo GetScrollTableEnd()."</td></tr>";
			}

			echo "</table>";

		ScrollLightTableEnd( );

		echo "</td></tr>";
		echo "</table>";
	}
	else print( "Нет такого игрока" );
}

f_MClose();

?>
