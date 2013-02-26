<?

header("Content-type: text/html; charset=windows-1251");

if( !$mid_php ) die( );

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "arrays.php" );
include_once( "skin.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "<script>window.top.location.href='index.php';</script>" );

		$premiums = Array
		(
			Array( "Премиум-Бои", "1. На 50% больше получаемый боевой опыт;<br>2. 240 минут дозора в день;<br>3. 10 боевого опыта в момент активации или продления на 28 дней.<br>", 30, 10, 2 ),
			Array( "Премиум-Добыча", "1. На 50% больше средняя прибыль при добыче в гильдиях Рыбаков, Охотников, Старателей и Собирателей;<br>2. 100 дублонов в момент активации или продления на 28 дней.<br>", 15, 5, 1 ),
			Array( "Премиум-Крафт", "Возможность указать последовательность изготовления на два часа вперед при условии, что персонаж находится в игре<br>", 15, 5, 1 ),
			Array( "Премиум-Работа", "1. На 50% больше получаемый профессиональный опыт<br>2. 10 профессионального опыта в момент активации или продления на 28 дней.<br>", 15, 5, 1 ),
			Array( "Премиум-Свобода", "Автоматический перезапуск работы в гильдиях Рыбаков, Охотников, Старателей и Собирателей в течение полутора часов при условии, что персонаж находится в игре.<br>", 15, 5, 1 ),
			Array( "Премиум-Монстры", "1. На 50% выше дроп с монстров <sup>1</sup>;<br>2. В полтора раза меньше время, проводимое у лекаря.<sup>2</sup><br><small><sup>1</sup>При условии, что вы - текущий соперник монстра, с шансом 50% количество каждой выпавшей вещи будет удвоено</small><br><small><sup>2</sup>Шепот мечты и Лечение у Фавна при этом уменьшают время на 20 секунд а не на 30</small><br>", 30, 10, 2 )
		);

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">

<?

$mode = $_GET['p'];
if( isset( $_GET['fail'] ) ) $mode = 'fail';
if( isset( $_GET['smiles'] ) ) $mode = 'smiles';

f_MQuery( "LOCK TABLE frozen_premiums WRITE, premiums WRITE" );
$frozen_premiums = f_MValue( "SELECT count( premium_id ) FROM frozen_premiums WHERE player_id={$player->player_id}" );

if( !$frozen_premiums )
{
    if( isset( $_GET['freeze'] ) )
    {
    	$tm = time( );
    	f_MQuery( "INSERT INTO frozen_premiums SELECT player_id, premium_id, deadline-$tm, $tm + 2*24*3600 from premiums where player_id={$player->player_id};" );
		f_MQuery( "UNLOCK TABLES" );
    	f_MQuery( "DELETE FROM premiums WHERE player_id={$player->player_id}" );
		$frozen_premiums = f_MValue( "SELECT count( premium_id ) FROM frozen_premiums WHERE player_id={$player->player_id}" );
    }

    else if( isset( $_GET['activate'] ) )
    {
		f_MQuery( "UNLOCK TABLES" );
    	$act = (int)$_GET['activate'];
    	if( $act < 0 || $act >= 6 )
    		RaiseError( "Неизвестный тип премиум-аккаунта", "$act" );

    	$price = $premiums[$act][2]; $durat = 28;
    	if( $_GET['l'] == 7 )
    	{
    		$price = $premiums[$act][3];
    		$durat = 7;
    	}
    	if( $_GET['l'] == 1 )
    	{
    		$price = $premiums[$act][4];
    		$durat = 1;
    	}

    	if( $player->SpendUMoney( $price ) )
    	{
    		f_MQuery( "LOCK TABLE premiums WRITE" );
    		$res = f_MQuery( "SELECT deadline FROM premiums WHERE player_id={$player->player_id} AND premium_id=$act" );
    		$arr = f_MFetch( $res );
    		$deadline = time( ) + $durat * 24 * 60 * 60;
    		if( !$arr ) f_MQuery( "INSERT INTO premiums( player_id, premium_id, deadline ) VALUES ( {$player->player_id}, $act, $deadline )" );
    		else if( $arr[0] < time( ) ) f_MQuery( "UPDATE premiums SET deadline=$deadline WHERE player_id={$player->player_id} AND premium_id=$act" ); 
    		else f_MQuery( "UPDATE premiums SET deadline=deadline+$durat*24*60*60 WHERE player_id={$player->player_id} AND premium_id=$act" ); 
    		f_MQuery( "UNLOCK TABLES" );
    		if( $durat == 28 && $act == 0 ) f_MQuery( "UPDATE characters SET exp=exp+10 WHERE player_id={$player->player_id}" );
    		else if( $durat == 28 && $act == 1 ) $player->AddMoney( 100 );
    		else if( $durat == 28 && $act == 3 ) f_MQuery( "UPDATE characters SET prof_exp=prof_exp+10 WHERE player_id={$player->player_id}" );
    		$player->AddToLogPost( -1, -$price, 21, $act );
    		die( "<script>location.href='game.php';</script>" );
    	}
    	else echo "<center><font color=darkred>У вас не хватает талантов</font></center>";
    }
    
    else f_MQuery( "UNLOCK TABLES" );
}
else
{
	if( isset( $_GET['ufreeze'] ) )
	{
		$tm = time( );
    	f_MQuery( "INSERT INTO premiums SELECT player_id, premium_id, duration+$tm from frozen_premiums where player_id={$player->player_id} AND available < $tm;" );
    	f_MQuery( "DELETE FROM frozen_premiums WHERE player_id={$player->player_id} AND available < $tm" );
		$frozen_premiums = f_MValue( "SELECT count( premium_id ) FROM frozen_premiums WHERE player_id={$player->player_id}" );
	}
	f_MQuery( "UNLOCK TABLES" );
}

if( isset( $_GET['nick_clr'] ) )
{
	if( $_GET['nick_clr'] ==2 )
		include( "favn_nick_clr_2.php" );
	else include( "favn_nick_clr.php" );

	return;
}

echo "<center><table width=90% height=90%><tr>";
echo "<td width=50% height=100%>";
	echo GetScrollLightTableStart2( );
	echo "<table height=100% width=100%><tr><td>";
	if( !isset( $mode ) )
	{
    	ScrollTableStart( );
    	echo "<b>Фавн Жорик</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( );

    	echo "<div style='text-align:left'><img width=163 height=165 src=images/npcs/favn.jpg hspace=5 vspace=5 align=left>";
    	echo "<i>Как только Вы подошли к дереву, маленький Фавн окликнул Вас. А Вы ведь сразу и не заметили  его. Только теперь смогли отчетливо разглядеть  и маленькие рожки, пробивающиеся сквозь  курчавые волосы, и зоркие глазки, и волосатые  звериные ноги-лапы. Это явно Фавн. Говорят, это очень  редкие существа и очень хитрые. Но этот - молодой и, судя по всему, не опасный. А Ваше чутье  никогда  ещё не обманывало Вас. Гм, недавно в  таверне один старатель хвастался, что после  встречи с фавном дела на прииске пошли в разы  лучше. Может и Вам повезет?</i><br>";
    	echo "<br><b>Фавн</b>: Тс-с-с. Эй, {$player->login}, привет !! Не верти так головой по  сторонам, городские стражи не дремлют. Я слыхал тебе нужны таланты,  у меня есть именно то, что тебе надо. Немного свежих талантов как раз  для тебя, дешевле ты не найдешь. Ты только посмотри какие таланты !!  Это же сказка. Тебе интересно, зачем они? Ну это просто: ты можешь  купить за них магические премиумы. Каждый из них даст тебе то  или иное преимущество. Ничего сверхчеловеческого, но жизнь будет  немножко проще. Вот, гляди на список того, что у меня есть, и выбирай. Удачи, {$player->login}. Только ни в коем случае не говори стражам, что ты  меня видел".($player->sex?"а":"")."...";
    	
    	echo "<br><br><ul>";

		if ( $player->GetQuestValue( 101 ) < time( ) ) //check for quests
		{
   			$player->SetTrigger( 101, 0 ); //disable quest trigger
   			$player->SetTrigger( 102, 0 );
   			$player->SetQuestValue( 102, 0 ); //disable task selected
   			$player->SetQuestValue( 42, 0 ); //clear task counter
   			echo "<br><br><li><a href='game.php?phrase=606'>Таланты мне нужны, ещё как нужны. Но я так же ".($player->sex?'слыхала':'слыхал').", что у тебя можно разжиться легкими деньгами. Это так? Если да, я ".($player->sex?'была':'был')." бы очень ".($player->sex?'признательна':'признателен').".</a>";
		}
		else if ( $player->HasTrigger( 102 ) && !$player->HasTrigger( 101 ) ) //check for quests
		{
   			echo "<br><br><li><a href='game.php?phrase=606'>Я  ".($player->sex?'выполнила':'выполнил')." твое задание, хотя, ".($player->sex?'должна':'должен')." тебе сказать, это было далеко не так просто, как ты думаешь.</a>";
		}
		else if( !$player->HasTrigger( 102 ) && !$player->HasTrigger( 101 ) )
		{
   			echo "<br><br><li><a href='game.php?phrase=606'>Таланты мне нужны, ещё как нужны. Но я так же ".($player->sex?'слыхала':'слыхал').", что у тебя можно разжиться легкими деньгами. Это так? Если да, я ".($player->sex?'была':'был')." бы очень ".($player->sex?'признательна':'признателен').".</a>";
		}
		echo "<li><a href=game.php?smiles=1>Да у меня вот как раз есть несколько блестящих, свеженьких талантов. Не знаешь где можно обменять их на что-то толковое? К примеру на пяток-другой смайликов. Уж больно скуден мой запас, надо бы пополнить.</a>";
		
		if( $player->level >= 4 )
		{
			$has261 = $player->HasTrigger( 261 );
			$has262 = $player->HasTrigger( 262 );
			$phraseTitle = "Жора, я откровенно скучаю. Строить, драться, добывать уж больно буднично. Скажи мне, нет ли у тебя чего-то такого да эдакого, что бы мне нервишки пощекотало и где бы я ".($player->sex?'смогла':'смог')." проявить себя во всей красе?";
			echo "<br><br><table><tr><td><img src=images/misc/race/chest.png></td><td>";
    		if( !$has262 && !$has261 )
    		{
    			echo "<a href=game.php?phrase=1363>$phraseTitle.</a>";
    		}
    		else if( $has262 )
    		{
    			echo "<a href=game.php?phrase=1369>$phraseTitle.</a>";
    		}
    		else
    		{
    			echo "<a href=game.php?phrase=1372>$phraseTitle.</a>";
    		}
    		echo "</td></tr></table>";
		}

		echo "</ul>";
		
    	echo "</div>";
    }
    else if( $mode == 'smiles' )
    {
    	ScrollTableStart( );
    	echo "<b>Платные Смайлики</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( );

    	echo "<div style='text-align:left'><img width=163 height=165 src=images/npcs/favn.jpg hspace=5 vspace=5 align=left>";
    	echo "<i>Жорик поморщился, заерзал. Судя по всему, Ваша осведомленность в его возможностях не очень-то ему по душе. Но, видать, Вы застали его врасплох: выкрутиться он уже не сможет.</i><br>";
    	echo "<b>Фавн</b>: Да, точно, есть у меня эти колобки. Ток они мне как детки, все под счет, всем я имена дал. Поэтому ну никак не могу продать. И не смотри на меня такими умоляющими глазами, даже не проси. Ну не надо, я не терплю слез... Ладно, ладно, перестань! Могу дать попользоваться. И, самом собой, не бесплатно. Вот смотри, мои сокровища. Стоят они совсем недорого - 2 таланта за штучку. Кстати, если хочешь, можешь также подарить кому-то. Мои пушистики будут только рады. Эх, как же мне тяжко с ними расставаться...<br>";

		$moo = array(
			array( 'Эти вот, к примеру, умеют здороваться, прощаться и радоваться', 3 ),
			array( 'А вот эти меня просто радуют, и с ними весело. Да ты сам погляди, такие лапочки', 4 ),
			array( 'Этих я давно растил и расставаться с ними мне так тяжко, так тяжко...', 5 ),
			array( "И, наконец, мои ценные. Они же самые нескромные. Как и цены на них", 7 )
		);

		echo "<table><colgroup><col width=140><col width=80><col width=140>";
		include_once("smiles_list.php");
		if( isset( $_GET['smile_do'] ) )
		{
			$set_id = (int)$_GET['smile_do'];
			$pid = $player->player_id;
			$ok = true;
			if( isset( $_GET['smile_whom'] ) )
			{
				$_GET['smile_whom'] = conv_utf($_GET['smile_whom']);
				$_GET['smile_whom'] = htmlspecialchars($_GET['smile_whom'],ENT_QUOTES);
				$pid = f_MValue( "SELECT player_id FROM characters WHERE login='".$_GET['smile_whom']."';" );
				if( !$pid )
				{
					echo "<script>alert('Персонажа с именем {$_GET[smile_whom]} не существует');</script>";
					$ok = false;
				}
				else if ($set_id >= 10)
				{
					$res = f_MValue("SELECT expires FROM paid_smiles WHERE player_id=$pid AND set_id=$set_id");
					if ($res == -1)
					{
						echo "<script>alert('У персонажа с именем {$_GET[smile_whom]} уже есть такой смайлик');</script>";
						$ok = false;
					}
				}
			}
			if( $ok )
			{
/*
			if ($player->Rank() != 1)
			{
	    			if( $set_id < 0 || $set_id > 3 ) RaiseError( "Непонятный номер набора смайликов $set_id" );
    				if( $player->SpendUMoney( $moo[$set_id][1] ) )
    				{
	    				$player->AddToLogPost( -1, - $moo[$set_id][1], 21, 1000, 2, $pid );
		        			f_MQuery( "LOCK TABLE paid_smiles WRITE" );
        					$exp = f_MValue( "SELECT expires FROM paid_smiles WHERE player_id={$pid} AND set_id=$set_id" );
        					if( $exp > time( ) ) $nexp = $exp + 28 * 24 * 60 * 60;
        					else $nexp = time( ) + 28 * 24 * 60 * 60;
	        				if( !$exp )
	        				{
        						f_MQuery( "INSERT INTO paid_smiles ( player_id, set_id, expires ) VALUES ( {$pid}, $set_id, $nexp )" );
        					}
	        				else f_MQuery( "UPDATE paid_smiles SET expires=$nexp WHERE player_id={$pid} AND set_id=$set_id" );
        					f_MQuery( "UNLOCK TABLES" );
	        				if( $pid != $player->player_id )
        					{
    							f_MQuery( "INSERT INTO post( sender_id, receiver_id, title, content, money, np, deadline ) VALUES ( {$player->player_id}, $pid, 'Набор смайликов', 'Персонаж {$player->login} подарил вам набор смайликов', '0', '0', '0' )" );
    							sendMessage( $pid, "У вас новое сообщение в дневнике" );
	        				}
        				}
			}
			else
*/
			{
				if( $set_id < 10 || $set_id > 166 ) RaiseError( "Непонятный номер смайлика $set_id" );
				if( $player->SpendUMoney( 2 ) )
				{
					f_MQuery("LOCK TABLE paid_smiles WRITE");
					$res = f_MValue("SELECT expires FROM paid_smiles WHERE player_id=$pid AND set_id=$set_id");
					if ($res == -1)
					{
						echo "<script>alert('У Вас уже есть такой смайлик');</script>";
						f_MQuery("UNLOCK TABLES");
						$player->AddUMoney(2);
					}
					else
					{
						if ($res == 0)
							f_MQuery("INSERT INTO paid_smiles ( player_id, set_id, expires ) VALUES ({$pid}, $set_id, -1)");
						else
							f_MQuery("UPDATE paid_smiles set expires=-1 WHERE player_id={$pid} AND set_id=$set_id");
						$numSm = f_MValue("SELECT COUNT(*) FROM paid_smiles WHERE set_id<10000 AND player_id={$pid} AND set_id>=10 AND expires=-1");
						f_MQuery("UNLOCK TABLES");
						$player->AddToLogPost( -1, -2, 21, 1000, 2, $pid );
						$lck = 0;
						if ($numSm >= 150) $lck=15;
						elseif ($numSm >= 100) $lck=10;
						elseif ($numSm >= 60) $lck=7;
						elseif ($numSm >=40) $lck=5;
						elseif ($numSm >=25) $lck=3;
						elseif ($numSm >=10) $lck=2;
						elseif ($numSm >=5) $lck=1;
						$plr = new Player($pid);
						$plr->RemoveEffect(30, true);
						if ($lck)
						{
							$plr->AddEffect(30, 0, "Любитель улыбок", "Всего vip-смайлов: ".$numSm, "../../images/smiles/ura.gif", "13:".$lck.".", -1);
						}
						if( $pid != $player->player_id )
        						{
    							f_MQuery( "INSERT INTO post( sender_id, receiver_id, title, content, money, np, deadline ) VALUES ( {$player->player_id}, $pid, 'Смайлик', 'Персонаж {$player->login} подарил вам смайлик *".$vsmiles[$set_id][0]."*', '0', '0', '0' )" );
    							sendMessage( $pid, "У вас новое сообщение в дневнике" );
		        				}
					}
				}
				else
					echo "<script>alert('У Вас не хватает талантов');</script>";
			}
		}
	}
?>
			<script>
			function smiles_buy(i,v)
			{
				if( confirm( "Купить смайлик за " + v + " тал?" ) )
					location.href="game.php?smiles=1&smile_do=" + i;
			}
			function smiles_prolong(i,v)
			{
				if( confirm( "Продлить набор смайликов на месяц за " + v + " тал?" ) )
					location.href="game.php?smiles=1&smile_do=" + i;
			}
			function smiles_pres(i,v)
			{
				var login = _( "nick" + i ).value;
				if( confirm( "Подарить персонажу " + login + " смайлик за " + v + " тал?" ) )
					location.href="game.php?smiles=1&smile_do=" + i + "&smile_whom=" + encodeURIComponent( login );
			}
			</script>
<?
		/*
		if ($player->Rank() != 1)
		for( $i = 0; $i < 4; ++ $i )
		{
			$exp = f_MValue( "SELECT expires FROM paid_smiles WHERE player_id={$player->player_id} AND set_id=$i" );
			
			echo "<tr>";
			echo "<td colspan=2><br><br>{$moo[$i][0]}<br>";
			if( $exp < time( ) ) echo "Статус: <font color=darkred>Не активен</font><br>";
			else echo "Статус: <font color=darkgreen>Активен до <b>".date( "d.m.Y H:i", $exp )."</b></font><br>";
			echo "</td>";
			echo "<td rowspan=3 valign=top><br>"; foreach($vsmiles[$i] as $b) echo "<img src=images/smiles/{$b}.gif> "; echo "</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td><img src=images/umoney.gif width=11 height=11> <b>{$moo[$i][1]}</b> за <b>28</b> дней</td>";
			if( $exp > time( ) ) echo "<td><button onclick='smiles_prolong($i,{$moo[$i][1]});' class=n_btn>Продлить</button></td>";
			else echo "<td><button onclick='smiles_buy($i,{$moo[$i][1]});' class=n_btn>Активировать</button></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td><input id=nick$i class=m_btn value='Ник персонажа' style='color:#808080;width:160px;' onfocus='if(this.value==\"Ник персонажа\"){this.value=\"\"; this.style.color=\"black\";}' onblur='if(this.value==\"\"){this.style.color=\"#808080\";this.value=\"Ник персонажа\";}'></td>";
			echo "<td><button onclick='smiles_pres($i,{$moo[$i][1]});' class=n_btn>Подарить</button></td>";
			echo "</tr>";
		}
		*/
		//if ($player->Rank() == 1)
		for ($i = 10; $i <= 166; $i++)
		{
			$exp = f_MValue( "SELECT expires FROM paid_smiles WHERE player_id={$player->player_id} AND set_id=$i" );

			echo "<tr><td colspan=3><hr></td></tr>";
			echo "<tr>";
			echo "<td>";
			if( $exp != -1 ) echo "Статус: <font color=darkred>Не активен</font><br>";
			else echo "Статус: <font color=darkgreen>Активен</font><br>";
			echo "</td>";
			if ($exp != -1)
				echo "<td><button onclick='smiles_buy($i,2);' class=n_btn>Активировать</button></td>";
			else
				echo "<td>&nbsp;</td>";
			echo "<td rowspan=2 valign=top align=right><br>";
			if ($player->Rank() == 1) echo $i." => ".f_MValue("SELECT COUNT(*) FROM paid_smiles WHERE set_id=$i AND expires=-1")."&nbsp;";
			foreach($vsmiles[$i] as $b) echo "<img src=images/smiles/{$b}.gif> ";
			echo "</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td><input id=nick$i class=m_btn value='Ник персонажа' style='color:#808080;width:160px;' onfocus='if(this.value==\"Ник персонажа\"){this.value=\"\"; this.style.color=\"black\";}' onblur='if(this.value==\"\"){this.style.color=\"#808080\";this.value=\"Ник персонажа\";}'></td>";
			echo "<td><button onclick='smiles_pres($i,2);' class=n_btn>Подарить</button></td>";
			echo "</tr>";
			
		}
			?>
			
			<?

		echo "</table>";
    }
    else if( $mode == 'fail' )
    {
    	ScrollTableStart( );
    	echo "<b>Платеж не совершен</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( );

    	echo "<big><b>Платеж <font color=darkred>не был</font> совершен</b></big><br>Свяжитесь с администрацией для определения причин";
	}
	else if( $mode == 'sms' )
	{
	   	ScrollTableStart( );
	   	echo "<b>Покупка с помощью SMS-сообщения</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( );
             
    	if( !isset( $_GET['country'] ) )
    	{
	    	echo "Выберите страну:<br>";
	    	echo "<a href=game.php?p=sms&country=0>Россия</a><br>";
	    	echo "<a href=game.php?p=sms&country=1>Украина</a><br>";
	    	echo "<a href=game.php?p=sms&country=2>Белоруссия</a><br>";
	    	echo "<a href=game.php?p=sms&country=3>Латвия</a><br>";
	    	echo "<a href=game.php?p=sms&country=4>Казахстан</a><br>";
	    	echo "<a href=game.php?p=sms&country=5>Эстония</a><br>";
	    	echo "<a href=game.php?p=sms&country=6>Армения</a><br>";
	    	echo "<a href=game.php?p=sms&country=7>Азербайджан</a><br>";
	    }
	    else if( $_GET['country'] == 0 )
	    {
		 	echo "Чтобы купить <b>четыре</b> таланта, отправьте сообщение с текстом<br>";
        	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
        	echo "На номер <b>6365</b><br>Стоимость сообщения: 75 рублей с учетом НДС<br><br>";

        	echo "Чтобы купить <b>10</b> талантов, отправьте сообщение с текстом<br>";
        	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
        	echo "На номер <b>8385</b><br>Стоимость сообщения: 180 рублей с учетом НДС<br><br>";

        	echo "<small>Поддерживаются операторы: Билайн, МТС, МегаФон, ON GSM, Tele2, Utel, АКОС, Алтайсвязь, БайкалВестКом, Дальсвязь GSM, ЕТК, Мотив, НСС, НТК, Связьинформ, Скай Линк, СМАРТС, СТЕК GSM, Улан-Удэнская сотовая сеть, Ульяновск-GSM, Цифровая Экспансия, Элайн GSM </small>";
	    
		   echo "<br/><br/><br/><small><small>Стоимость доступа к услугам контент-провайдера устанавливается Вашим оператором. Подробную информацию можно узнать в разделе \"Услуги по коротким номерам\" на сайте www.mts.ru или обратившись в контактный центр по 
телефону 8 800 333 0890 (0890 для абонентов МТС)</small></small>";
		 }
	    else if( $_GET['country'] == 1 )
	    {
		 	echo "Чтобы купить <b>10</b> талантов, отправьте сообщение с текстом<br>";
        	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
        	echo "На номер <b>8385</b><br>Стоимость сообщения: 30 гривен<br><br>";

        	echo "<small>Для всех национальных GSM операторов</small>";
        	echo "<br>";
        	echo "<br><br><br>Номер обслуживается провайдером <a href='http://www.smsonline.ru/' target=_blank>СМС ОНЛАЙН</a>, информационная служба: (044) 383-20-90, (с 9:00 до 18:00, в рабочие дни).<br><br>Информация относительно тарифа, учета НДС и сбора в ПФ указывается таким образом: &laquo;тариф в гривнах с учетом НДС. Дополнительно взимается сбор в Пенсионный фонд в размере 7.5% от стоимости услуги без учета НДС&raquo;.";
	    }
	    else if( $_GET['country'] == 3 )
	    {
		 	echo "Чтобы купить <b>четыре</b> таланта, отправьте сообщение с текстом<br>";
        	echo "<b><font color=darkred>3FF tal {$player->player_id}</font></b><br>";
        	echo "На номер <b>1897</b><br>Стоимость сообщения: 1.5 лата с учетом НДС<br><br>";

        	echo "<small>Поддерживаются операторы: Bite, LMT, Tele2</small>";
	    }
	    else if($_GET['country'] == 2 )
	    {
	    /*
		 	echo "Чтобы купить <b>четыре</b> таланта, отправьте сообщение с текстом<br>";
        	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
        	echo "На номер <b>1315</b><br>Стоимость сообщения: 9900 белорусских рублей с учетом НДС<br><br>";

        	echo "<small>Поддерживаются операторы: МТС, Диалог</small>";
        	*/
        	echo "На данный момент, сервис не доступен.";
	    }
	    else if( $_GET['country'] == 4 )
	    {
		 	echo "Чтобы купить <b>четыре</b> таланта, отправьте сообщение с текстом<br>";
        	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
        	echo "На номер <b>6365</b><br>Стоимость сообщения: 300 тенге (KZT) с учетом НДС<br><br>";

        	echo "<small>Поддерживаются операторы: Beeline Kazakhstan, Kcell, Activ, NEO, PAThWORD, Dalacom, City</small>";
	    }
	    else if( $_GET['country'] == 6 )
	    {
		 	echo "Чтобы купить <b>три</b> таланта, отправьте сообщение с текстом<br>";
        	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
        	echo "На номер <b>4009</b><br>Стоимость сообщения: 1000 драм с учетом НДС<br><br>";

        	echo "<small>Поддерживаются операторы: Beeline Armenia, MTS (VivaCell), K-Telekom </small>";
	    }
	    else if( $_GET['country'] == 5 )
	    {
		 	echo "Чтобы купить <b>два</b> таланта, отправьте сообщение с текстом<br>";
        	echo "<b><font color=darkred>FF tal {$player->player_id}</font></b><br>";
        	echo "На номер <b>15330</b><br>Стоимость сообщения: 15 эстонских крон с учетом НДС<br><br>";

		 	echo "Чтобы купить <b>пять</b> талантов, отправьте сообщение с текстом<br>";
        	echo "<b><font color=darkred>FF tal {$player->player_id}</font></b><br>";
        	echo "На номер <b>13015</b><br>Стоимость сообщения: 39 эстонских крон с учетом НДС<br><br>";

        	echo "<small>Поддерживаются операторы: EMT, Radiolinija Eesti, Tele2</small>";
	    }
	    else if( $_GET['country'] == 7 )
	    {
		 	echo "Чтобы купить <b>четыре</b> таланта, отправьте сообщение с текстом<br>";
        	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
        	echo "На номер <b>9645</b><br>Стоимость сообщения: 4.72 маната с учетом НДС<br>";
        	echo "<small>Оператор: <b>Azercell</b></small><br /><br />";

		 	echo "Чтобы купить <b>шесть</b> талантов, отправьте сообщение с текстом<br>";
        	echo "<b><font color=darkred>RR tal {$player->player_id}</font></b><br>";
        	echo "На номер <b>3304</b><br>Стоимость сообщения: 5.9 маната с учетом НДС<br>";
        	echo "<small>Оператор: <b>BakCell</b></small><br /><br />";
	    }	    
	                     /*
    	echo "Чтобы купить <b>пять</b> талантов, отправьте сообщение с текстом<br>";
    	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
    	echo "На номер <b>7250</b><br><br>";

    	echo "Чтобы купить <b>10</b> талантов, отправьте сообщение с текстом<br>";
    	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
    	echo "На номер <b>5373</b><br><br>";

    	echo "<small>Убедитесь в согласии того, кто оплачивает услуги сотовой связи<br>";
    	echo "Перед отправкой внимательно проверьте правильность введеного сообщения, убедитесь, что между <b>tal</b> и <b>{$player->player_id}</b> поставлен ровно один пробел<br>";
    	echo "Внимание! Слово <b>tal</b> должно быть написано строчными буквами.<br></small><br>";

    	echo "<div id=sms_ext><a href='javascript:show_ext();'>Стоимость сообщений и поддерживаемые операторы связи</a></div>";
    	echo "<div id=sms_ext_info style='display:none'>";
    	?>
    	<script>
    	function show_ext( ) { _("sms_ext_info").style.display="";_("sms_ext").style.display="none"; };
    	</script>
<b>Для Российской Федерации:</b><br>
смс на номер <b>7250</b> - 2.5$ с НДС<br>
смс на номер <b>5373</b> - 5$ с НДС<br>
<br>
Поддерживаются мобильные операторы:<br>
ОАО "МегаФон"                          <br>
ЗАО "Мобиком-Хабаровск"                    <br>
ЗАО "Мобиком-Новосибирск"<br>
ЗАО "Мобиком-Центр"<br>
ЗАО "Мобиком-Кавказ"<br>
ЗАО "Уральский GSM"<br>
ОАО "МСС-Поволжье"<br>
Северо-Западный филиал ОАО "МегаФон"<br>
ЗАО "Соник-Дуо"<br>
ОАО "МТС"<br>
ОАО "ВымпелКом" (Билайн)<br>
ЗАО "НСС"<br>
ЗАО &lt;Ростовская Сотовая Связь&gt; (Теле-2)<br>
ООО "Екатеринбург - 2000" (Мотив)<br>
ООО "Контент Урал" (ОАО "Уралсвязьинформ", Utel)<br>
ЗАО &lt;Астрахань GSM&gt;<br>
ОАО "НТК"<br>
ЗАО "БайкалВестКом"<br>
ЗАО "СМАРТС"<br>
ЗАО Енисейтелеком (ООО Мобилфон)<br>
ОАО Алтайсвязь (ООО Мобилфон)<br>
ЗАО Ульяновск GSM<br>
ЗАО <Дельта Телеком> SKYLINK<br>
<br>
<b>Для Украины:</b><br>
смс на номер <b>7250</b> - 2.5$ с НДС<br>
смс на номер <b>5373</b> - 5$ с НДС<br>
<br>
Поддерживаются мобильные операторы:<br>
Киевстар, МТС, Билайн Украина, Life<br>
<br>
<b>Для Казахстана:</b><br>
смс на номер <b>7250</b> - 2.5$ с НДС<br>
смс на номер <b>5373</b> - 5$ с НДС<br>
<br>
Т713,00 (K-cell) и Т600,00 (Билайн)<br>
<br>
Поддерживаются мобильные операторы:<br>
K-cell (GSM Kazakhstan) и Билайн Казахстан (Кар-Тел)<br>
    	<?
    	echo "</div>"; */

//    	ScrollTableEnd( );
	}/**/
    else if( $mode == 'wm' )
    {
    	ScrollTableStart( );
    	echo "<b>Платеж через систему Web-Money</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( );
//	echo "Сервис временно неактивен<br>За подробной информацией обращайтесь к Администрации игры<br>";
//point1

    ?>
    <script>
    function wm_update()
    {
    	var coeff = [];// Число валюты за один талант
    		 coeff['R346197619375'] = 10;   // Рубли
    		 coeff['U426214563258'] = 2.7;  // Гривны
    		 coeff['Z301545765621'] = 0.35; // Доллары
    		  
    	var val = parseInt( document.getElementById( 'wm_num' ).value );
    	var valute = document.getElementById( 'wm_valute' ).value; 
    	
    	if( isNaN( val ) )
    	{
    		val = 0;
    	}
    	
    	document.getElementById( 'wm_price' ).value = val * coeff[valute];
    }
   </script>
	<a href="/game.php">Назад</a><br />
	<br />
	<form target="_blank" action="https://merchant.webmoney.ru/lmi/payment.asp" method="POST">
	   <input type="hidden" name="LMI_PAYMENT_DESC" value="Покупка Талантов для игрока <?=$player->login?>" />
	   <input type="hidden" name="LMI_PAYMENT_NO" value="<?=$player->player_id?>" />
		<table>
   	 	<tr>
	   	 	<td style="font-weight: bold; vertical-align: top;">Количество талантов:</td>
	   	 	<td>
	   	 		<input type="text" class="m_btn" id="wm_num" value="15" onkeyup="wm_update()">
	   	 	</td>
	   	</tr>
			<tr>
				<td style="font-weight: bold; vertical-align: top;">Стоимость:</td>
				<td>
					<input type="text" class="m_btn" name="LMI_PAYMENT_AMOUNT" id="wm_price" value="150" />
				</td>
			</tr>
			<tr>
				<td style="font-weight: bold; vertical-align: top;">Валюта:</td>
				<td>
					<select id="wm_valute" name="LMI_PAYEE_PURSE" class="s_btn" onclick="wm_update()" onchange="wm_update()" onselect="wm_update()">
						<option value="R346197619375">Рубли</option>
						<option value="U426214563258">Гривны</option>
						<option value="Z301545765621">Доллары</option>
					</select>
				</td>
			</tr>
    		<tr>
    			<td>&nbsp;</td>
    			<td>
    				<input type="submit" class="s_btn" value="Дальше">
    			</td>
    		</tr>
    	</table>
	</form>
	<span style="font-size: 8px; color: #d36008;"><b>УВЕДОМЛЕНИЕ О РИСКАХ</b><br />Предлагаемые товары и услуги предоставляются не по заказу лица либо предприятия, эксплуатирующего систему WebMoney Transfer. Мы являемся независимым предприятием, оказывающим услуги, и самостоятельно принимаем решения о ценах и предложениях. Предприятия, эксплуатирующие систему WebMoney Transfer, не получают комиссионных вознаграждений или иных вознаграждений за участие в предоставлении услуг и не несут никакой ответственности за нашу деятельность.<br />Аттестация, произведенная со стороны WebMoney Transfer, лишь подтверждает наши реквизиты для связи и удостоверяет личность. Она осуществляется по нашему желанию и не означает, что мы каким-либо образом связаны с продажами операторов системы WebMoney.';
    <?

//point1
    }
    else if( $mode == 'rbk' )
    {
    	ScrollTableStart( );
    	echo "<b>Платеж через систему RBK Money</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( );


    ?>
    <script>
    function rbk_update()
    {
    	val = parseInt( document.getElementById( 'rbk_num' ).value );
    	if( isNaN( val ) ) val = 0;
    	document.getElementById( 'rbk_price' ).value = val * 10;
    }
    </script>
    <?
    	echo "<a href=game.php>Назад</a><br><br>";
	   	echo "<form target=_blank action=https://rbkmoney.ru/acceptpurchase.aspx method=POST><table>";
	   	echo "<input type=hidden name=eshopId value=2002630>";
	   	echo "<input type=hidden name=serviceName value='Покупка Талантов для игрока {$player->login}'>";
	   	echo "<input type=hidden name=orderId value='{$player->player_id}'>";
	   	echo "<input type=hidden name=recipientCurrency value='RUR'>";
    	echo "<tr><td>Количество талантов:</td><td><input class=m_btn type=text id=rbk_num value=15 onkeyup='rbk_update()'></td></tr>";
    	echo "<tr><td>Стоимость:</td><td><input class=m_btn type=text name=recipientAmount id=rbk_price value=150 onkeyup='rbk_update()'> руб.</td></tr>";
    	echo "<tr><td>&nbsp;</td><td><input type=submit class=s_btn value='Дальше'></td></tr>";

    	echo "</table></form>";
    }
    else if( $mode == 'yd' )
    {
    	ScrollTableStart( );
    	echo "<b>Платеж через систему Яндекс.Деньги</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( );


    	echo "<a href=game.php>Назад</a><br><br>";
    	echo "В настоящий момент автоматизированная система приема платежей не доступна.<br>";
    	echo "Для приобретения талантов через систему Яндекс.Деньги свяжитесь с администратором <b>Пламени</b>";
	}
	else if( $mode == '2pay' )
	{
    	ScrollTableStart( );
    	echo "<b>Еще тысяча и один способ купить Таланты</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( 'left' );

		?>
        <div id="dvapay_terminals" style="float: left; width: 300px;">
        </div>
        <div id="dvapay_emoney" style="float: left; width: 300px;">
        </div>
        <br style="clear: both; "/>
        <div id="dvapay_ecard" style="float: left; width: 300px;">
        </div>
        <div id="dvapay_ebank" style="float: left; width: 300px;">
        </div>
        <br style="clear: both; "/>
        <div id="dvapay_esendmoney" style="float: left; width: 300px;">
        </div>

        <script>
        var id='2149';
        var v1='<?=$player->player_id?>';
        var v2='';
        var v3='';
        var page='3021';
        var country='0';
        var conf='123';
        document.write('<script type="text/javascript" src="http://2pay.ru/view/script.php?id='+id+'&v1='+v1+'&v2='+v2+'&v3='+v3+'&country='+country+'&page='+page+'&conf='+conf+'"></' + 'script>');
        </script>
        <?
	}
	elseif( $mode == 'selfban' )
	{
		// Самозабанивание
		switch( $_GET['selfban'] )
		{
			// Показываем самозабанивание
			case 'begin':
			{
    			ScrollTableStart( );
    			echo "<b>Платеж через систему Яндекс.Деньги</b>";
    			ScrollTableEnd( );
    			echo "</td></tr><tr><td height=100%>";
    			ScrollTableStart( );
    			?>
					123    			
    			<?
				break;
			}
			
			// Дефолт
			case 'default':
			{
				break;			
			}
		}
	}

	ScrollTableEnd( );
	echo "</td></tr></table>";
	ScrollLightTableEnd( );
echo "</td><td width=50% height=100%>";
	echo GetScrollLightTableStart2('center', 'top' );
	echo "<table width=100%><tr><td>";

	ScrollTableStart( );
	echo "<b>Купить Таланты</b>";
	ScrollTableEnd( );

	echo "</td></tr><tr><td>";

	ScrollTableStart( 'left' );
	//echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'><a href=game.php?p=sms>С помощью SMS-сообщения</a>";
	echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'><a href=http://2pay.ru/oplata/number.html?id=2149&v1={$player->player_id} target=_blank>Через терминалы QiWi</a><br>";
//	echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'>С помощью SMS-сообщения";
	echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'><a href=game.php?p=wm>Через систему WebMoney</a>";
	echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'><a href=http://2pay.ru/oplata/yandex/?id=2149&v1={$player->player_id} target=_blank>Через систему Yandex.Деньги</a><br>";
	echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'><a href=game.php?p=2pay>Еще тысяча и один способ...</a><br>";
	echo "<br><li STYLE='list-style-image: URL(\"images/dots/dot-exit.gif\")'><a href=game.php?phrase=515>Покинуть фавна</a>";
	ScrollTableEnd( );

	echo "</td></tr><tr><td>";

	ScrollTableStart( );
	echo "<b>Активировать Премиум</b>";
	ScrollTableEnd( );
	echo "</td></tr><tr><td>";
	ScrollTableStart( 'left' );

	if( !$frozen_premiums )
	{

    	?>

    	<script>

    	function activate( id, s, c, l ) { if( confirm( 'Вы уверены, что хотите потратить ' + c + ' талантов и активировать ' + s + ' на ' + l + ' дней?' ) ) location.href='game.php?activate=' + id + '&l=' + l; }
    	function prolong( id, s, c, l ) { if( confirm( 'Вы уверены, что хотите потратить ' + c + ' талантов и продлить ' + s + ' на ' + l + ' дней?' ) ) location.href='game.php?activate=' + id + '&l=' + l; }

    	</script>
    	<?

    	foreach( $premiums as $a=>$b )
    	{
    		echo "<b><font color=green>$b[0]</font></b><br>";
    		echo "$b[1]";

    		$res = f_MQuery( "SELECT deadline FROM premiums WHERE player_id={$player->player_id} AND premium_id=$a" );
    		$arr = f_MFetch( $res );
    		if( !$arr || $arr[0] < time( ) )
    		{
        		echo "Статус: <font color=darkred>Не активен</font><br>";
        		echo "<table cellspacing=0 cellpadding=0><tr><td><img width=11 height=11 src=images/umoney.gif> <b>$b[2]</b> за <b>28</b> дней&nbsp;</td><td><button onclick='activate($a, \"$b[0]\", $b[2], 28)' class=n_btn>Активировать</button></td></tr>";
        		echo "<tr><td><img width=11 height=11 src=images/umoney.gif> <b>$b[3]</b> за <b>7</b> дней &nbsp;</td><td><button onclick='activate($a, \"$b[0]\", $b[3], 7)' class=n_btn>Активировать</button></td></tr>";
        		echo "<tr><td><img width=11 height=11 src=images/umoney.gif> <b>$b[4]</b> за <b>1</b> день &nbsp;</td><td><button onclick='activate($a, \"$b[0]\", $b[4], 1)' class=n_btn>Активировать</button></td></tr></table><br>";
    		}
    		else
    		{
        		echo "Статус: <font color=green>Активен до: <b>".date( "d.m.Y H:i", $arr[0] )."</b></font><br>";
        		echo "<table cellspacing=0 cellpadding=0><tr><td><img width=11 height=11 src=images/umoney.gif> <b>$b[2]</b> за <b>28</b> дней&nbsp;</td><td><button onclick='prolong($a, \"$b[0]\", $b[2], 28)' class=n_btn>Продлить</button></td></tr>";
        		echo "<tr><td><img width=11 height=11 src=images/umoney.gif> <b>$b[3]</b> за <b>7</b> дней &nbsp;</td><td><button onclick='prolong($a, \"$b[0]\", $b[3], 7)' class=n_btn>Продлить</button></td></tr>";
        		echo "<tr><td><img width=11 height=11 src=images/umoney.gif> <b>$b[4]</b> за <b>1</b> день &nbsp;</td><td><button onclick='prolong($a, \"$b[0]\", $b[4], 1)' class=n_btn>Продлить</button></td></tr></table><br>";
    		}
    	}
	}
	
	else
	{
		echo "<b>Ваши премиумы заморожены</b><br>";
		$res = f_MQuery( "SELECT * FROM frozen_premiums WHERE player_id={$player->player_id} ORDER BY premium_id" );
		$whena = 0;
		while( $arr = f_MFetch( $res ) )
		{
			echo "<li>".$premiums[$arr['premium_id']][0]." (".my_time_str( $arr['duration'], false ).")<br>";
			$whena = $arr['available'];
		}
?>

<script>
function unfreeze()
{
	if( confirm( 'Вы уверены, что хотите разморозить премиумы?' ) )
		location.href='game.php?ufreeze=1';
}
</script>

<?
		if( $whena < time( ) ) echo "<br><li><a href='javascript:unfreeze()'>Разморозить</a>";
		else echo "<br><i>Вы не сможете разморозить премиумы еще ".my_time_str( $whena - time( ), false )."</i>";
	}

	ScrollTableEnd( );
	echo "</td></tr><tr><td>";

	// freezing
	if (!$frozen_premiums){
	echo "<table width=100% cellspacing=0 cellpadding=0 border=0><tr><td>";
	ScrollTableStart( 'center' );
	echo "<b>Заморозка премиумов</b><br>";
	ScrollTableEnd( );
	echo "</td></tr></table>";
	echo "</td></tr><tr><td>";
	echo "<table width=100% cellspacing=0 cellpadding=0 border=0><tr><td>";
	ScrollTableStart( 'center' );
	echo "Вы можете заморозить премиумы, если по какой-то причине вы не сможете выходить в игру в течение нескольких дней или более долгий период времени.<br>";
	echo "Заморозить можно только все премиумы сразу. Разморозить их можно в любой момент, но не раньше чем через двое суток с момента заморозки.<br>";
	echo "В течение всего периода заморозки вы не сможете активировать или продлевать новые премиумы.<br><br>";
?>

<script>
function pfreeze()
{
	if( confirm( 'Вы уверены, что хотите заморозить премиумы? В течение двух дней вы не сможете разморозить их обратно.' ) )
		location.href='game.php?freeze=1';
}
</script>

<?
	
	echo "<li><a href='javascript:pfreeze()'>Заморозить премиумы</a>";
	
	ScrollTableEnd( );
	echo "</td></tr></table>";
	echo "</td></tr><tr><td>";
	
	}

	ScrollTableStart( );
	echo "<b>Дополнительные Услуги</b>";
	ScrollTableEnd( );

	echo "</td></tr><tr><td>";

	echo "<table width=100% cellspacing=0 cellpadding=0 border=0><tr>";
		echo "<td width=49% height=150>";
        	ScrollTableStart( 'center' );
        		echo "<b>Скупка Талантов</b><br>";
        		echo "<small>Фавн готов выкупить ваши таланты по <nobr><img width=11 height=11 src=images/money.gif> 5000</nobr> за каждый</small><br>";
        		echo "<br><small><b>Укажите количество талантов:</b></small><br>";
        		if( isset( $_GET['talsell'] ) )
        		{
        			$val =(int)$_GET['talsell'];
        			if( $val <= 0 ) echo "<small><font color=darkred>Введите положительное число</font></small><br>";
        			else if( !$player->SpendUMoney( $val ) ) echo "<small><font color=darkred>У вас недостаточно талантов</font></small><br>";
           			else
           			{
           				$player->AddMoney( $val * 5000 );
           				$player->AddToLogPost( 0, $val * 5000, 21, 5000, 0, $val );
           				$player->AddToLogPost( -1, - $val, 21, 5000, 0, $val );
           				echo "<small><font color=blue>Вы продали $val ".my_word_str($val,'талант',"таланта","талантов")." и получили 5000 дублонов</font></small><br>";
           			}

        		}
        		echo "<form action=game.php method=GET><input style='text-align:center' class=m_btn name=talsell value=0 type=text><br>";
        		echo "<table cellspacing=0 cellpadding=0 border=0><tr><td><img src=images/top/b.png></td><td><input type=submit class=n_btn value=Продать></td><td><img src=images/top/c.png></td></tr></table>";
        		echo "</form>";

        	ScrollTableEnd( );
        echo "</td><td>&nbsp;</td><td width=49% height=150>";
        	ScrollTableStart( 'center' );
        		echo "<b>Лечение</b><br>";
				echo "<small>Вы можете уменьшить время, проводимое у лекаря, на 30 секунд.<br><br><b>Стоимость услуги<br></b></small><img src=images/umoney.gif width=11 height=11> <b>1</b><br><br>";
				if( isset( $_GET['lek'] ) )
				{
					$res = f_MQuery( "SELECT real_deaths FROM characters WHERE player_id={$player->player_id}" );
					$arr = f_MFetch( $res );
					if( !$arr[0] ) echo "<small><font color=darkred>Ваше время у лекаря минимально</font></small><br>";
					else if( !$player->SpendUMoney( 1 ) ) echo "<small><font color=darkred>У вас недостаточно талантов</font></small><br>";
					else
					{
						echo "<small><font color=blue>Время успешно уменьшено</font></small><br>";
						f_MQuery( "UPDATE characters SET real_deaths=real_deaths-1 WHERE player_id={$player->player_id}" );
	       				$player->AddToLogPost( -1, - 1, 21, 1000, 1 );
					}
				}
				echo "<table cellspacing=0 cellpadding=0 border=0><tr><td><img src=images/top/b.png></td><td><button class=n_btn onclick='if( confirm( \"Уменьшить время, проводимое у лекаря, за 1 талант?\" ) ) location.href=\"game.php?lek=1\";'>Уменьшить</button></td><td><img src=images/top/c.png></td></tr></table>";
        	ScrollTableEnd( );
		echo "</td>";
	echo "</tr></table>";
	
	echo "</td></tr><tr><td>";

	echo "<table width=100% cellspacing=0 cellpadding=0 border=0><tr>";
		echo "<td width=49% height=150>";
        	ScrollTableStart( 'center' );
        		echo "<b>Смена Пола</b><br>";
        		$sex_price = 0;
        		if( $player->level > 4 || $player->HasTrigger( 555 ) ) $sex_price = 40;
        		
        		echo "<small>Фавн готов нанять колдуна для смены вашего пола<br>";
        		if( isset( $_GET['changesex'] ) )
        		{
        			if( !$player->SpendUMoney( $sex_price ) ) echo "<small><font color=darkred>У вас недостаточно талантов</font></small><br>";
           			else
           			{
	       				$player->AddToLogPost( -1, - $sex_price, 21, 1000, 4 );
           				$player->SetTrigger( 555, 1 );
           				$new_sex = 1 - $player->sex;
           				$old_sex = $player->sex;
           				f_MQuery( "UPDATE characters SET sex={$new_sex} WHERE player_id={$player->player_id}" );
           				f_MQuery( "UPDATE player_avatars SET avatar='f{$new_sex}w.jpg' WHERE player_id={$player->player_id} AND avatar='f{$old_sex}w.jpg'" );
           				f_MQuery( "UPDATE player_avatars SET avatar='f{$new_sex}n.jpg' WHERE player_id={$player->player_id} AND avatar='f{$old_sex}n.jpg'" );
           				f_MQuery( "UPDATE player_avatars SET avatar='f{$new_sex}f.jpg' WHERE player_id={$player->player_id} AND avatar='f{$old_sex}f.jpg'" );
           				echo "<script>parent.char_ref.location.href='char_ref.php?rnd=".mt_rand()."';</script>";
           			}
        		}
        		if( $player->level <= 4 && !$player->HasTrigger( 555 ) )
        		{
            		echo "<br>Одна смена пола игроку не выше четвертого уровня предоставляется бесплатно.</small><br><br>";
        		}
        		else
        		{
            		echo "<br>Стоимость услуги:</small><br> <nobr><img width=11 height=11 src=images/umoney.gif> <b>40</b></nobr></small><br><br>";
            	}
				echo "<table cellspacing=0 cellpadding=0 border=0><tr><td><img src=images/top/b.png></td><td><button class=n_btn onclick='if( confirm( \"Вы уверены, что хотите сменить пол?\" ) ) location.href=\"game.php?changesex=1\";'>Сменить пол</button></td><td><img src=images/top/c.png></td></tr></table>";

        	ScrollTableEnd( );
        echo "</td><td>&nbsp;</td><td width=49% height=150>";
        	ScrollTableStart( 'center' );
        		echo "<b>Смена Имени</b><br>";
        		echo "<small>У Фавна есть друзья в городской управе, которые могут посодействовать в смене имени за <nobr><img width=11 height=11 src=images/umoney.gif> <b>50</b></nobr></small><br>";
        		echo "<br><small><b>Укажите желаемое имя:</b></small><br>";
        		if( isset( $_GET['changename'] ) )
        		{
        			$newname =$_GET['changename'];
        			if( !$player->SpendUMoney( 50 ) ) echo "<small><font color=darkred>У вас недостаточно талантов</font></small><br>";
           			else
           			{
           				function correct_login( $a )
                        {	
                        	if( ( $a[0] >= 'a' && $a[0] <= 'z' ) || ( $a[0] >= 'A' && $a[0] <= 'Z' ) )
                        		$eng = 1;
                        	else if( ( $a[0] >= 'а' && $a[0] <= 'я' ) || ( $a[0] >= 'А' && $a[0] <= 'Я' ) )
                        		$eng = 0;
                        	else
                        	{
                        		return 0;
                        	}
                        	
                        	$l = strlen( $a );
                        	for( $i = 1; $i < $l; ++ $i )
                        	{
                        		if( ( $a[$i] >= 'a' && $a[$i] <= 'z' ) || ( $a[$i] >= 'A' && $a[$i] <= 'Z' ) )
                        		{
                        			if( !$eng )
                        			{
                        				return 0;
                        			}
                        		}
                        		else if( ( $a[$i] >= 'а' && $a[$i] <= 'я' ) || ( $a[$i] >= 'А' && $a[$i] <= 'Я' ) )
                        		{
                        			if( $eng )
                        			{
                        				return 0;
                        			}
                        		}
                        		else if( $a[$i] != '-' && $a[$i] != '_' && ( $a[$i] < '0' || $a[$i] > '9' ) )
                        		{
                        			return 0;
                        		}
                        	}
                        	
                        	return 1;
                        }
                        if( !correct_login( $newname ) ) 
                        {
                        	echo "<small><font color=darkred>Имя не соответствует требованиям</font></small><br>";
               				$player->AddUMoney( 50 );
                        }
                        else
						{
           			
               				f_MQuery( "LOCK TABLE characters WRITE" );
               				if( f_MValue( "SELECT count( player_id ) FROM characters WHERE login='$newname'" ) > 0 )
               				{
               					echo "<small><font color=darkred>Такое имя уже занято</font></small><br>";
	               				f_MQuery( "UNLOCK TABLES" );
	               				$player->AddUMoney( 50 );
               				}
               				else
               				{
               					f_MQuery( "UPDATE characters SET login='$newname' WHERE player_id={$player->player_id}" );
               					echo "<script>parent.char_ref.location.href='char_ref.php?rnd=".mt_rand()."';</script>";
	               				f_MQuery( "UNLOCK TABLES" );
			       				$player->AddToLogPost( -1, - 50, 21, 1000, 3 );
               				}
           				}
           			}

        		}
        		echo "<form action=game.php method=GET><input style='text-align:center' class=m_btn name=changename value='Новое Имя' type=text><br>";
        		echo "<table cellspacing=0 cellpadding=0 border=0><tr><td><img src=images/top/b.png></td><td><input type=submit class=n_btn value=Поменять></td><td><img src=images/top/c.png></td></tr></table>";
        		echo "</form>";
        	ScrollTableEnd( );
		echo "</td>";
	echo "</tr></table>";

	echo "</td></tr><tr><td>";

	echo "<table width=100% cellspacing=0 cellpadding=0 border=0><tr><td>";
	
        	ScrollTableStart( "left" );
        	
   	    	echo "<b>Другие Сервисы</b><br><br>";
   	    	echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'><a href='game.php?nick_clr=2'>Расширенный ассортимент цветов ника</a>";
				if( $player->Rank( ) == 1 )
				{   	    	
   	    	echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'><a href='game.php?p=selfban&selfban=begin'>Забанить своего персонажа</a>";
   	    	}   	    	
        	
        	ScrollTableEnd( );
	

	echo "</td></tr></table>";
	
	echo "</td></tr></table>";
	ScrollLightTableEnd( );

echo "</td>";
echo "</tr></table></center>";

?>
