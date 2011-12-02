<?

/*

regimes
0 - nothing
1 - sending info

*/

function uploadfile($origin, $dest, $tmp_name)
{
  $origin = mt_rand( 100000000, 999999999 ).".gif";
  $fulldest = $dest.$origin;
  $filename = $origin;
  for ($i=1; file_exists($fulldest); $i++)
  {
   $fileext = (strpos($origin,'.')===false?'':'.'.substr(strrchr($origin, "."), 1));
   $filename = substr($origin, 0, strlen($origin)-strlen($fileext)).'['.$i.']'.$fileext;
   $fulldest = $dest.$filename;
  }
  
  if (move_uploaded_file($tmp_name, $fulldest))
   return $filename;
  return "Moo!:$fulldest:";
}





if( !$mid_php ) die( );

if( $player->level < 3 ) 
{
	echo "<center><i>Вы еще слишком малы. Возвращайтесь, когда достигнете 3-ого уровня.</i></center>";
	return;
}

$mode = 0;

if( !isset( $_GET['mode'] ) && $player->clan_id /* && isset( $_GET['order'] ) */ )
{	
	$mode = 2;
	if( !isset( $_GET['order'] ) ) $_GET['order'] = 'main';
}

else if( isset( $_GET['unapply'] ) )
{
	f_MQuery( "DELETE FROM clan_bets WHERE player_id={$player->player_id}" );
}

else if( isset( $_GET['apply_for'] ) && $player->clan_id == 0 )
{
	$clan_id = $_GET['apply_for'];
	settype( $clan_id, 'integer' );

	$ccres = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE invites_whom = {$player->player_id} AND status = 1" );
	$ccarr = f_MFetch( $ccres );
	$ddres = f_MQuery( "SELECT count( player_id ) FROM clan_creation WHERE player_id = {$player->player_id}" );
	$ddarr = f_MFetch( $ccres );
	$oores = f_MQuery( "SELECT count( player_id ) FROM clan_bets WHERE player_id = {$player->player_id}" );
	$ooarr = f_MFetch( $oores );

	$res = f_MQuery( "SELECT count( clan_id ) FROM clans WHERE clan_id=$clan_id" );
	$arr = f_MFetch( $res );

	if( $arr[0] == 0 ) RaiseError( "Попытка подать заявку в несуществующий Орден", "$clan_id" );

	if( $ccarr[0] > 0 )
	{
		$player->syst( "У вас уже подана заявка в другой Орден, вам следует сначала отклонить ее" );
	}
	else if( $ccarr[0] > 0 )
	{
		$player->syst( "Вы занимаетесь основанием собственного Ордена, сначала следует отменить создание" );
	}
	else if( $ooarr[0] > 0 )
	{
		$player->syst( "У вас подана заявка в один из Орденов, сначала следует отозвать ее" );
	}
	else
	{
		f_MQuery( "INSERT INTO clan_bets( clan_id, player_id ) VALUES ( $clan_id, {$player->player_id} )" );
	}
}

else if( isset( $_GET['register_order'] ) && $player->level >= 5 )
{
	$ccres = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE invites_whom = {$player->player_id} AND status != 2" );
	$ccarr = f_MFetch( $ccres );

	$oores = f_MQuery( "SELECT count( player_id ) FROM clan_bets WHERE player_id = {$player->player_id}" );
	$ooarr = f_MFetch( $oores );


	if( $player->clan_id != 0 )
	{
		$player->syst( "Вы уже состоите в Ордене. Вы не можете основать новый Орден" );
	}
	else if( $ooarr[0] > 0 )
	{
		$player->syst( "У вас подана заявка в один из Орденов, сначала следует отозвать ее" );
	}
	else if( $ccarr[0] )
	{
		$player->syst( "Вы приглашены в другой Орден. Сначала следует отказаться от приглашения" );
	}
	else
	{
    	if( isset( $_GET['cancel_registration'] ) )
    	{
    		$res = f_MQuery( "SELECT icon FROM clan_creation WHERE player_id={$player->player_id}" );
    		$arr = f_MFetch( $res );
    		if( $arr ) unlink( "images/clans/$arr[0]" );
    		f_MQuery( "DELETE FROM clan_creation WHERE player_id={$player->player_id}" );
    		f_MQuery( "DELETE FROM clan_creation_players WHERE player_id={$player->player_id}" );
    		$mode = 0;
    	}
    	else $mode = 1;
	}
}
else if( ( isset( $_GET['find_order'] ) || isset( $_GET['enter'] ) ) && $player->clan_id == 0 || $_GET['mode'] == 3 )
{
	$mode = 3;
}

// render page
if( $mode == 0 )
{
	if( isset( $_GET['accept'] ) )
	{
		$id = $_GET['accept'];
		settype( $id, 'integer' );
		$res = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE player_id=$id AND invites_whom={$player->player_id} AND status != 2" );
		$arr = f_MFetch( $res );
		$ccres = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE invites_whom = {$player->player_id} AND status = 1" );
		$ccarr = f_MFetch( $ccres );
		$ddres = f_MQuery( "SELECT count( player_id ) FROM clan_creation WHERE player_id = {$player->player_id}" );
		$ddarr = f_MFetch( $ccres );
		$oores = f_MQuery( "SELECT count( player_id ) FROM clan_bets WHERE player_id = {$player->player_id}" );
		$ooarr = f_MFetch( $oores );

		if( $ccarr[0] > 0 )
		{
			$player->syst( "У вас уже подана заявка в другой Орден, вам следует сначала отклонить ее" );
		}
		else if( $ccarr[0] > 0 )
		{
			$player->syst( "Вы занимаетесь основание собственного Ордена, сначала следует отменить создание" );
		}
		else if( $ooarr[0] > 0 )
		{
			$player->syst( "У вас подана заявка в один из Орденов, сначала следует отозвать ее" );
		}

		else if( $player->clan_id )
		{
			$player->syst( "Вы уже состоите в другом Ордене. Вы не можете принимать заявки." );
		}
		else if( $arr[0] > 0 )
		{
			f_MQuery( "UPDATE clan_creation_players SET status=1 WHERE player_id=$id AND invites_whom={$player->player_id}" );
			$plr = new Player( $id );
			$plr->syst3( "Персонаж <b>{$player->login}</b> принял заявку в ваш Орден" );
		}
	}
	if( isset( $_GET['refuse'] ) )
	{
		$id = $_GET['refuse'];
		settype( $id, 'integer' );
		$res = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE player_id=$id AND invites_whom={$player->player_id} AND status != 2" );
		$arr = f_MFetch( $res );
		if( $arr[0] > 0 )
		{
			f_MQuery( "UPDATE clan_creation_players SET status=2 WHERE player_id=$id AND invites_whom={$player->player_id}" );
			$plr = new Player( $id );
			$plr->syst3( "Персонаж <b>{$player->login}</b> отклонил заявку в ваш Орден" );
		}
	}

	echo "<ul>";
	if( $player->clan_id ) echo "<li> <a href=game.php?order=main>Войти в комнату своего Ордена</a>";
	else
	{
    	$chres = f_MQuery( "SELECT name FROM clan_creation WHERE player_id={$player->player_id}" );
    	$charr = f_MFetch( $chres );

    	if( $charr ) echo "<li><a href=game.php?register_order=1>$charr[0] - продолжить основание</a>";
    	else if( $player->level >= 5 ) echo "<li><a href=game.php?register_order=1>Основать новый Орден</a>";
    	echo "<li><a href=game.php?find_order=1>Найти Орден, подходящий Вам</a>";
   		echo "<li><a href=orders.php target=_blank>Рейтинг Орденов</a>";
    }
//	echo "<li><a href=orders_rating.php target=_blank>Посмотреть рейтинг Орденов</a>";
	echo "</ul>";
	$res = f_MQuery( "SELECT clans.name, clans.clan_id FROM clans, clan_bets WHERE clans.clan_id=clan_bets.clan_id AND player_id={$player->player_id}" );
	while( $arr = f_MFetch( $res ) )
	{
		echo "У вас подана заявка в <b>$arr[name]</b>.<br><a href=game.php?unapply=$arr[clan_id]>Отозвать заявку</a><br><br>";
	}

	$res = f_MQuery( "SELECT clan_creation_players.player_id, clan_creation_players.status,clan_creation.name FROM clan_creation_players, clan_creation WHERE invites_whom = {$player->player_id} AND clan_creation.player_id=clan_creation_players.player_id AND status != 2" );
	while( $arr = f_MFetch( $res ) )
	{
		if( $arr['status'] == 0 ) echo "Вы приглашены в <b>$arr[name]</b>.<br>";
		else if( $arr['status'] == 1 ) echo "Вы приняли приглашение в <b>$arr[name]</b>.<br>Орден будет основан, когда все игроки примут приглашения.<br>";
		else if( $arr['status'] == 2 ) echo "Вы отклонили приглашение в <b>$arr[name]</b>. ";
		if( $arr['status'] != 2 ) echo "<a href=# onclick='if( confirm( \"Вы уверены, что хотите отклонить приглашение в $arr[name]? Впоследствии вы не сможете принять это приглашение, если глава ордена не вышлет его заново.\" ) ) location.href=\"game.php?refuse=$arr[player_id]\";'>Отклонить приглашение</a>";
		if( $arr['status'] == 0 ) echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		if( $arr['status'] != 1 ) echo "<a href=# onclick='if( confirm( \"Вы уверены, что хотите принять пришлашение в $arr[name]?\" ) ) location.href=\"game.php?accept=$arr[player_id]\";'>Принять приглашение</a>";
		echo "<br>";
	}
}                                                                               

if( $mode == 1 )
{
	echo "<b>Основание нового Ордена</b> - ";
	$res = f_MQuery( "SELECT * FROM clan_creation WHERE player_id={$player->player_id}" );
	$arr = f_MFetch( $res );
	if( !$arr )
	{
		$registration_begun = false;
		$st = '';

		if( isset( $_POST['nm'] ) )
		{
			$registration_begun = true;
			$name = $_POST['nm'].' ';
			$l = strlen( $name );
			if( $l > 56 )
			{
				$st .= "Длина названия Ордена не может превышать 55 символов<br>";
				$registration_begun = false;
			}
			if( strpos( $name, "Орден " ) === false )
			{
				$st .= "Наличие слова Орден в названии обязательно<br>";
				$registration_begun = false;
			}
			if( $name[0] < 'А' || $name[0] > 'Я' )
			{
				$st .= "Первый символ названия обязательно должен быть заглавной русской буквой<br>";
				$registration_begun = false;
	   		}
			else 
			{
				for( $i = 1; $i < $l; ++ $i )
				{
					if( !( $name[$i] >= 'а' && $name[$i] <= 'я' ) && !( $name[$i] >= 'А' && $name[$i] <= 'Я' ) && $name[$i] != ' ' )
					{
						$st .= "Название может содержать только русские буквы и пробелы<br>";
						$registration_begun = false;
						break;
					}
					if( ( $name[$i] >= 'А' && $name[$i] <= 'Я' ) && ( $name[$i - 1] != ' ' ) )
					{
						$st .= "Название не может содержать заглавные буквы, если они не являются началом слова<br>";
						$registration_begun = false;
						break;
					}
					if( ( $name[$i] >= 'а' && $name[$i] <= 'я' ) && $name[$i - 1] == ' ' )
					{
						$st .= "Каждое слово названия должно начинаться с заглавной буквы<br>";
						$registration_begun = false;
						break;
					}
					if( $name[$i] == ' ' && $name[$i - 1] == ' ' )
					{
						if( $i != $l - 1 ) $st .= "Название не может содержать двух пробелов подряд<br>";
						else  $st .= "Название не может оканчиваться пробелом<br>";
						$registration_begun = false;
						break;
					}
				}
			}

        	list( $width, $height, $type, $attr ) = getimagesize( $_FILES['icon']['tmp_name'] );

        	if( !isset( $_FILES['icon'] ) )	
        	{
        		$st .= "Иконка Ордена обязательно должна быть. Вы не послали никакой иконки.<br>";
        		$registration_begun = false;
        	}
        	else if( !$width )
        	{
        		$st .= "Иконка Ордена должна быть в формате GIF. Посленный вами файл не распознан сервером как картинка.<br>";
        		$registration_begun = false;
            }
        	else if( $width != 18 || $height != 13 )
        	{
        		$st .= "Размер иконки должен быть 18х13 (размер посланной иконки {$width}x{$height})<br>";
        		$registration_begun = false;
            }
        	else if( $_FILES['icon']['size'] > 2048 )
        	{
        		$val = $_FILES['icon']['size'];
        		$st .= "Размер файла с иконкой не может превышать 2048 байт (размер посланного файла $val<br>";
        		$registration_begun = false;
        	}
        	else if( $type != 1 )
        	{
				$st .= "Иконка Ордена должна быть в формате GIF<br>";
        		$registration_begun = false;
        	}

    		$ccres = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE invites_whom = {$player->player_id} AND status = 1" );
    		$ccarr = f_MFetch( $ccres );
    		$ddres = f_MQuery( "SELECT count( player_id ) FROM clan_creation WHERE player_id = {$player->player_id}" );
    		$ddarr = f_MFetch( $ccres );

    		if( $ccarr[0] > 0 )
    		{
    			$st .= "У вас уже подана заявка в другой Орден, вам следует сначала отклонить ее";
        		$registration_begun = false;
    		}
    		else if( $ccarr[0] > 0 )
    		{
    			$st .= "Вы занимаетесь основание собственного Ордена, сначала следует отменить создание";
        		$registration_begun = false;
    		}
    		else if( $player->clan_id )
    		{
    			$st .= "Вы уже состоите в другом Ордене. Вы не можете принимать заявки.";
        		$registration_begun = false;
    		}



        	if( $registration_begun && !$player->SpendMoney( 5000 ) )
        	{
        		$st .= "У вас недостаточно дублонов для уплаты пошлины";
        		$registration_begun = false;
        	}

			if( $registration_begun )
			{
				$name = trim( $name );
				$img = uploadfile($_FILES['icon']['name'],'images/clans/',$_FILES['icon']['tmp_name']);
				f_MQuery( "INSERT INTO clan_creation ( player_id, step, name, icon ) VALUES ( {$player->player_id}, 1, '$name', '$img' )" );
				$arr = Array( );
				$arr['player_id'] = $player->player_id;
				$arr['step'] = 1;
				$arr['icon'] = $img;
				$arr['name'] = $name;
			}
			else
			{
				$st = "<font color=darkred>$st</font>";
			}
		}

		if( !$registration_begun )
		{
    		echo "Шаг 1 из 3 - название, иконка и пошлина<br><ul><li><a href=game.php>Вернуться в Зал Собраний</a><li><a href=# onclick='if( confirm( \"Вы действительно хотите отменить процесс основания нового Ордена?\" ) ) location.href=\"game.php?register_order=1&cancel_registration=1\";'>Отказаться от основания Ордена</a></ul>";
    		echo "$st";
    		echo '<form enctype="multipart/form-data" action=game.php?register_order=1 method=post>';
    		echo "<table cellspacing=0 cellpadding=0 border=0>";
    		echo "<tr><td>Название Ордена:&nbsp;</td><td><input type=text name=nm class=m_btn></td></tr>";
    		echo "<tr><td>&nbsp;</td><td><small>Наличие слова Орден в названии обязательно<br>Каждое слово названия должно начинаться с большой буквы<br>В названии могут использоваться только русские буквы и пробелы<br>Максимальная длина названия Ордена - 55 символов</small></td></tr>";
    		echo "<tr><td>Иконка Ордена:&nbsp;</td><td><input type=file class=m_btn name=icon value=''></td></tr>";
    		echo "<tr><td>&nbsp;</td><td><small>Формат: GIF<br>Размеры: 18x13 пикселей<br>Размер файла не больше 2048 байт</small></td></tr>";
    		echo "<tr><td>Пошлина: </td><td><img width=11 height=11 src=images/money.gif> 5000</td></tr>";
    		echo "<tr><td>&nbsp;</td><td><input type=submit class=ss_btn value='Дальше'></td></tr>";
    		echo "</table>";
    		echo "</form>";
		}
	}
	if( $arr && $arr['step'] == 1 )
	{
		include_once( "textedit.php" );
		$manual_provided = false;
		$st = '';
		if( isset( $_POST['manual'] ) )
		{
			$manual_provided = true;
			$orientation = $_POST['orientation'];
			$element = $_POST['element'];
			settype( $orientation, 'integer' );
			settype( $element, 'integer' );
			if( $orientation < 0 || $orientation > 2 || $element < 0 || $element > 3 )
			{
				RaiseError( "Намеренная попытка указать неверную склонность или стихию", "Склонность $orientation, Стихия: $element" );
			}
			$manual = trim( HtmlSpecialChars( $_POST['manual'] ) );
			$manual = str_replace( "\n", "<br>", $manual );
			$manual = process_str( $manual );

			if( $manual_provided )
			{
				f_MQuery( "UPDATE clan_creation SET step=2, element=$element, orientation=$orientation, manual='$manual' WHERE player_id={$player->player_id}" );
				$arr['element'] = $element;
				$arr['orientation'] = $orientation;
				$arr['manual'] = $manual;
				$arr['step'] = 2;
			}
		}
		if( !$manual_provided )
		{
			include_js( "js/textedit.js" );
    		echo "Шаг 2 из 3 - описание, стихия, склонность<br><ul><li><a href=game.php>Вернуться в Зал Собраний</a><li><a href=# onclick='if( confirm( \"Вы действительно хотите отменить процесс основания нового Ордена?\" ) ) location.href=\"game.php?register_order=1&cancel_registration=1\";'>Отказаться от основания Ордена</a></ul>";
    		echo "$st";
    		echo '<form name=frm action=game.php?register_order=1 method=post>';
    		echo "<table cellspacing=0 cellpadding=0 border=0>";
    		echo "<tr><td vAlign=top>";
    		echo "<b>Склонность:</b>&nbsp;".create_select_global( "orientation", $orientations, 0 );
    		echo "</td><td align=right><b>Стихия:</b>&nbsp;".create_select_global( "element", $elements, 3 );
    		echo "</td></tr><tr><td colspan=2 vAlign=top><br><b>Описание</b> (этот текст будет показан на главной странице вашего Ордена):<br>"; insert_text_edit( 'frm', 'manual' ); echo "</td></tr>";
    		echo "<tr><td colspan=2><input type=submit class=ss_btn value='Дальше'></td></tr>";
    		echo "</table>";
    		echo "</form>";
		}
	}
	if( $arr && $arr['step'] == 2 )
	{
		$clan_name = $arr['name'];
		$finish = false;
		$st = '';

		if( isset( $_GET['finish'] ) )
		{
			f_MQuery( "LOCK TABLE clan_creation WRITE, clan_creation_players WRITE, clans WRITE" );

			$res = f_MQuery( "SELECT count( invites_whom ) FROM clan_creation_players WHERE player_id={$player->player_id}" );
			$arr = f_MFetch( $res );
			$total = $arr[0];
			$res = f_MQuery( "SELECT count( invites_whom ) FROM clan_creation_players WHERE player_id={$player->player_id} AND status=1" );
			$arr = f_MFetch( $res );
			$accepted = $arr[0];

			$res = f_MQuery( "SELECT * FROM clan_creation WHERE player_id={$player->player_id}" );
			$arr = f_Mfetch( $res );

			$alres = f_MQuery( "SELECT * FROM clans WHERE name='$arr[name]'" );
			$alres2 = f_MQuery( "SELECT * FROM clan_creation WHERE name='$arr[name]' AND player_id != {$player->player_id}" );

			if( f_MNum( $alres ) ) { $player->syst( "Похоже, Орден с таким названием уже успели основать до вас. Придется начинать процесс основния с начала" ); f_MQuery( "UNLOCK TABLES" ); }
			else if( f_MNum( $alres2 ) ) { $player->syst( "Похоже, Орден с таким названием уже основывается. Придется начинать процесс основния с начала" ); f_MQuery( "UNLOCK TABLES" ); }
			else if( $arr && $arr['step'] == 2 && $total >= 4 && $accepted == $total )
			{
				f_MQuery( "UPDATE clan_creation SET step=4 WHERE player_id={$player->player_id}" );
				f_MQuery( "UNLOCK TABLES" );

				f_MQuery( "INSERT INTO clans ( name, icon, orientation, element ) VALUES ( '$arr[name]', '$arr[icon]', $arr[orientation], $arr[element] )" );
				$clan_id = mysql_insert_id( );
				f_MQuery( "insert into forum_rooms( id ) values ( -{$clan_id} );" );
				f_MQuery( "INSERT INTO clan_pages( clan_id, title, text, is_title ) VALUES ( $clan_id, 'Главная', '$arr[manual]', 1 )" );
				f_MQuery( "INSERT INTO clan_ranks( clan_id, rank, name, permitions ) VALUES ( $clan_id, 0, 'Послушник', 0 )" );
				f_MQuery( "INSERT INTO clan_ranks( clan_id, rank, name, permitions ) VALUES ( $clan_id, 1000, 'Магистр', 2147483647 )" );
				f_MQuery( "INSERT INTO clan_jobs( clan_id, job, name, permitions ) VALUES ( $clan_id, 1000, 'Глава', 2147483647 )" );

				$pres = f_MQuery( "SELECT * FROM clan_creation_players WHERE player_id={$player->player_id}" );
				while( $parr = f_MFetch( $pres ) )
				{
					f_MQuery( "INSERT INTO player_clans ( player_id, clan_id ) VALUES ( $parr[invites_whom], $clan_id )" );
					f_MQuery( "UPDATE characters SET clan_id=$clan_id WHERE player_id=$parr[invites_whom]" );
					$plr = new Player( $parr['invites_whom'] );
					$plr->syst3( "В Алидерии основан <b>$arr[name]</b>. Поздравляем! Для того, чтобы появился закрытый чат Ордена, может понадобиться перезайти в игру" );
					$plr->UploadInfoToJavaServer( );
				}
				$player->syst3( "В Алидерии основан <b>$arr[name]</b>. Поздравляем! Для того, чтобы появился закрытый чат Ордена, может понадобиться перезайти в игру" );
				f_MQuery( "INSERT INTO player_clans ( player_id, clan_id, rank, job, control_points ) VALUES ( {$player->player_id}, $clan_id, 1000, 1000, -1 )" );
				f_MQuery( "UPDATE characters SET clan_id=$clan_id WHERE player_id={$player->player_id}" );
				f_MQuery( "DELETE FROM clan_creation WHERE player_id={$player->player_id}" );
				f_MQuery( "DELETE FROM clan_creation_players WHERE player_id={$player->player_id}" );
				$player->clan_id = $clan_id;
				$player->UploadInfoToJavaServer( );
				$finish = true;
				die( "<script>location.href='clans_js.php';</script>" );
			}
			else f_MQuery( "UNLOCK TABLES" );

		}

		if( !$finish )
		{
			if( isset( $_GET['del'] ) )
			{
				$id = $_GET['del'];
				settype( $id, 'integer' );
				f_MQuery( "DELETE FROM clan_creation_players WHERE player_id={$player->player_id} AND invites_whom=$id" );
			}
			if( isset( $_POST['nm'] ) )
			{
				$name = htmlspecialchars( $_POST['nm'], ENT_QUOTES );
				$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$name' AND clan_id=0" );
				$arr = f_MFetch( $res );
				if( !$arr ) $st = "Игрока с именем $name не существует либо он состоит в другом Ордене";
				else
				{
					$plr = new Player( $arr[0] );
					f_MQuery( "LOCK TABLE clan_creation_players WRITE, clan_creation WRITE" );
					$qres = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE player_id={$player->player_id}" );
					$qarr = f_MFetch( $qres );

					$res = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE player_id={$player->player_id} AND invites_whom={$plr->player_id}" );
					$arr = f_MFetch( $res );

					$ccres = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE invites_whom = {$plr->player_id} AND status != 2" );
                	$ccarr = f_MFetch( $ccres );
					$bbres = f_MQuery( "SELECT count( player_id ) FROM clan_creation WHERE player_id = {$plr->player_id}" );
                	$bbarr = f_MFetch( $bbres );

                	if( $plr->clan_id != 0 )
                	{
						$st = "Нельзя пригласить игрока, который состоит в другом Ордене";
						f_MQuery( "UNLOCK TABLES" );
                	}
					else if( $plr->player_id == $player->player_id )
					{
						$st = "Нельзя пригласить в Орден себя";
						f_MQuery( "UNLOCK TABLES" );
					}
					if( $arr[0] > 0 )
					{
						$st = "Указанный персонаж уже приглашен вами в Орден";
						f_MQuery( "UNLOCK TABLES" );
					}
                	else if( $ccarr[0] )
                	{
						$st = "Указанный вами игрок имеет не отклоненные заявки в один из других Орденов";
						f_MQuery( "UNLOCK TABLES" );
                	}
                	else if( $bbarr[0] )
                	{
						$st = "Указанный вами игрок в настоящий момент пытается основать собственный Орден";
						f_MQuery( "UNLOCK TABLES" );
                	}
					else if( $qarr[0] >= 4 )
					{
						$st = "В заявке на регистрацию Ордена не может быть больше пяти человек";
						f_MQuery( "UNLOCK TABLES" );
					}
					else
					{
						f_MQuery( "INSERT INTO clan_creation_players ( player_id, invites_whom ) VALUES ( {$player->player_id}, {$plr->player_id} )" );
						f_MQuery( "UNLOCK TABLES" );
						$plr->syst3( "Персонаж <b>{$player->login}</b> приглашает вас в <b>{$clan_name}</b>. Согласиться или отказаться можно в Зале Собраний в Городской Управе столицы." );
    				}
				}
			}

    		echo "Шаг 3 из 3 - приглашение игроков<br><ul><li><a href=game.php>Вернуться в Зал Собраний</a><li><a href=# onclick='if( confirm( \"Вы действительно хотите отменить процесс основания нового Ордена?\" ) ) location.href=\"game.php?register_order=1&cancel_registration=1\";'>Отказаться от основания Ордена</a></ul>";
    		echo "<i>Для основания Ордена необходимо ровно пять игроков не ниже третьего уровня.<br>После основания Ордена вы не сможете принять новых игроков, пока не построите казармы. Отнеситесь к выбору первых пяти игроков максимально ответственно.</i>";
			$plrs = Array( );
			$plrs[0] = Array( $player, 1 );
			$res = f_MQuery( "SELECT * FROM clan_creation_players WHERE player_id={$player->player_id}" );
			while( $arr = f_MFetch( $res ) )
			{
				$plr = new Player( $arr['invites_whom'] );
				$plrs[] = Array( $plr, $arr['status'] );
			}

			echo "<table>";
			$waiting = false;
			foreach( $plrs as $id=>$arr )
			{
				echo "<tr><td><b>".($id + 1).".</b></td><td><script>document.write( ".$arr[0]->Nick( )." );</script><td>";
				if( $arr[1] == 0 ) echo "<font color=navy>Ждем подтверждения</font>";
				if( $arr[1] == 1 ) echo "<font color=green>Подтвердил</font>";
				if( $arr[1] == 2 ) echo "<font color=red>Отказал</font>";
				echo "</td><td>";
				if( $id == 0 ) echo "&nbsp;";
				else echo "<a href=game.php?register_order=1&del=".$arr[0]->player_id.">Удалить</a>";
				echo "</td></tr>";
				if( $arr[1] != 1 ) $waiting = true;
			}
			echo "</table><br><br>";

			if( count( $plrs ) < 5 )
			{
    			echo "<b>Пригласить игрока:</b><br>";
    			if( $st != '' ) echo "<font color=darkred>$st</font><br>";
    			echo "<form action=game.php?register_order=1 method=POST><table><tr><td><input type=text name=nm class=m_btn></td><td><input type=submit class=s_btn value='Пригласить'></td></tr></table></form>";
			}
			else
			{
				if( $waiting ) echo "<i>Ожидаем подтверждения от всех игроков</i>";
				else echo "<button class=s_btn onclick='location.href=\"game.php?register_order=1&finish=1\"'>Основать Орден</button>";
			}
		}
	}
}

if( $mode == 2 )
{
	include( "clan_room.php" );
}

else if( $mode == 3 )
{
	$clan_id = $_GET['enter'];                                  
	settype( $clan_id, 'integer' );
	$res = f_MQuery( "SELECT * FROM clans WHERE clan_id=$clan_id" );
	$arr = f_MFetch( $res );
	if( $arr )
	{
		echo "<b>$arr[name]</b> - <a href=game.php?find_order=1>Назад</a><br><br>";
		echo "Направленность: <b>".$orientations[$arr['orientation']]."</b><br>";
		echo "Стихия: <b>".$elements[$arr['element']]."</b><br>";
		echo "Страница: <a href=orderpage.php?id=$clan_id target=_blank>Открыть</a><br>";
		echo "<ul><li><a href=game.php?apply_for=$clan_id>Подать заявку на вступление</a></ul>";
	}
	else
	{
		echo "<b>Ордена Алидерии</b> - <a href=game.php>Назад</a><br>";
    	$res = f_MQuery( "SELECT * FROM clans ORDER BY glory DESC, clan_id" );
    	while( $arr = f_MFetch( $res ) )
    	{
        	echo "<ul>";
        	echo "<li><a href=game.php?enter=$arr[clan_id]>$arr[name]</a>";
        	echo "</ul>";
    	}
	}
}

?>
