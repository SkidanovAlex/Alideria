<?

// Автор Reincarnation

if( !$mid_php ) die( );

$reslt = f_MValue("SELECT tree_active FROM clans WHERE clan_id=".$clan_id);

if ($reslt >= 0 ) // если древо активировано, то надо полить
{
	f_MQuery("LOCK TABLE player_clans WRITE");
	$arr_poliv = f_MFetch(f_MQuery("SELECT tree_effects, trup FROM player_clans WHERE player_id=".$player->player_id));
	if ($arr_poliv[0] == 0)
	{
		if ($arr_poliv[1] <= 20) $pv = 1;
		elseif ($arr_poliv[1] <= 100) $pv = 2;
		elseif ($arr_poliv[1] <= 200) $pv = 3;
		elseif ($arr_poliv[1] <= 300) $pv = 4;
		else $pv = 5;

		f_MQuery("UPDATE player_clans SET tree_effects = 1, trup = trup+1, trup_val = trup_val+".$pv." WHERE player_id=".$player->player_id);
		f_MQuery("UNLOCK TABLES");
		$player->syst("Вы полили Древо Жизни. Дерево благославляет Вас, и Вам кажется, что оно стало ещё выше и краше.");
		$player->tree_can = 1;

		$reslt = $reslt + $pv;
		f_MQuery("UPDATE clans SET tree_active = tree_active + $pv WHERE clan_id =  ".$clan_id);
	}
	f_MQuery("UNLOCK TABLES");
}

echo "<b>Древо Жизни</b> - <a href=game.php?order=main>Назад</a><br>";

$canbuild = ( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_BUILD ) );
$maybuild = false;

$needtext = "Нужен текст. ";

$content = "<table><tr><td width=140 align=center><script>FLUl();</script><img src='/images/camp/c/14.png'><script>FLL();</script></td><td width=500><script>FLUl();</script>Перед Вами <b>Древо Жизни</b>. Это сердце Вашего Ордена, подаренное ему Богами Алидерии в память об ушедших Духах Стихий. Пока живет Орден, живет и Древо. Если Древо погибло, умер и Орден. Чем лучше Орден заботится о своем Древе, тем большую награду он получает от божественных покровителей. Уровень <b>Древа Жизни</b>, а с ним и награду Богов, можно повысить за жертву. В обмен Боги одарят участников Ордена своей милостью.<script>FLL();</script></td></tr>";

$restreelvl = f_MValue("SELECT level FROM clan_buildings WHERE building_id=14 AND clan_id=".$clan_id);

$content .= "<tr><td align=center><script>FLUl();</script><b><center>Древо Жизни<br>{$restreelvl} уровень";
$content .= "</center></b><script>FLL();</script></td><td>";


$numG = 0;
$numB = 0;
$numR = 0;
$numN = 0;

$dl = f_MValue("SELECT deadline FROM clan_tree_uping WHERE clan_id=".$clan_id);
if (!$dl) $dl=0;

$min_tree_active = 10; // если жизнь Древа выше этого, то можно апать и давать эффекты

if ($restreelvl > 1 && $reslt > $min_tree_active) //выдаем эффекты
{
	$player->RemoveEffect(10, true);
	$tref = 0;
	if ($player->level < 4) $tref = 0;
	elseif ($player->level < 10) $tref = 1;
	elseif ($player->level < 16) $tref = 2;
	elseif ($player->level < 22) $tref = 3;
	else $tref = 4;
	if ($tref != 0)
	{
		$trefhp = $tref * 100;
		$trefv = $tref * 2;
		$eff = '30:'.$tref.':40:'.$tref.':50:'.$tref.':13:'.$tref.':15:'.$tref.':16:'.$tref.':101:'.$trefhp.':223:'.$trefv.'.';
		for ($i = 1;$i < $restreelvl; $i++)
			$player->AddEffect( 10, 0, 'Божий дар', 'Заряд бодрости был получен под ветвями Древа Жизни', 'tree.png', $eff, time() + 7 * 24 * 60 * 60 );
		$num_effs = f_MValue("SELECT COUNT(id) FROM player_effects WHERE player_id=".$player->player_id." AND effect_id=10");
		for($i = 0; $i < $num_effs - $restreelvl; $i++)
			$player->RemoveEffect(10, false);
		$player->syst('Постояв немного в тени Древа Жизни, вы ощутили невероятный прилив бодрости. Он продлится около недели.');
	}
}

if( isset( $_GET['fast'] ) )
{
	// Ускорение за таланты
	$fst = (int)$_GET['fast'];
	if ($fst > 0)
	if( $dl <= time()+60 )
	{
		$player->syst( 'Ваш Орден не занимается ростом Древа Жизни, либо уже скоро уровень Древа увеличится.' );
	}
	else
	{
		// Пробуем оплатить
		if( $player->SpendUMoney( $fst ) )
		{
			$dl = $dl - 3600 * $fst;
			f_MQuery("LOCK TABLE clan_tree_uping WRITE");
			f_MQuery( "UPDATE clan_tree_uping SET deadline = $dl WHERE clan_id = $clan_id" );
			f_MQuery("UNLOCK TABLES");
			
			$buildPhrase = array( 'Боги довольны твоим поступком!', '<a href="/forum.php?thread=6141" target="_blank">Участвуй в конкурсе, придумай продолжение для этой фразы!</a>' );
			$player->syst( 'Время ускорило свой ход рядом с Древом Жизни, благодаря умелым действиям <b>'.$player->login.'</b>. '.$buildPhrase[mt_rand( 0, 1 )] );
			$player->AddToLogPost(-1, -$fst, 1004, $player->clan_id, 1);
			
			$Rein = new Player( 6825 );
			$Rein->syst2( 'Персонаж <a href="/player_info.php?nick='.$player->login.'" target="_blank"><b>'.$player->login.'</b></a> оплатил ускорение Древа Жизни на <b>'.$fst.' час</b>' );
	
		}
		else
		{
			$player->syst2( 'У тебя нехватает талантов для ускорения.' );	
		}
	}
}

if ($dl == 0 && isset($HTTP_GET_VARS['upfrom'])) //запрос на активацию или улучшение Древа
{
	if ($reslt == -2 && $canbuild) //первая активация, за частицы
	{
		if (!getTreeUping($clan_id, -2)) //in clan.php
		{
			$tm = time()+1*3600;
			$dl = $tm;
			f_MQuery("INSERT INTO clan_tree_uping (clan_id, deadline) VALUES (".$clan_id.", ".$tm.")");
			orderBroadcast($clan_id, "Поздравляем! Ваш Орден собрал все необходимое для роста Древа Жизни!");
			$maybuild = true;
			die( "<script>location.href='game.php?order=tree';</script>" );
		}
		else $player->syst("Недостаточно частиц для роста.");
	}
	elseif ($reslt == -1 && $canbuild) // вторая активация, за кубы
	{
		if (!getTreeUping($clan_id, -1)) //in clan.php
		{
			$tm = time()+1*3600;
			$dl = $tm;
			f_MQuery("INSERT INTO clan_tree_uping (clan_id, deadline) VALUES (".$clan_id.", ".$tm.")");
			orderBroadcast($clan_id, "Поздравляем! Ваш Орден собрал все необходимое для роста Древа Жизни!");
			$maybuild = true;
			die( "<script>location.href='game.php?order=tree';</script>" );
		}
		else $player->syst("Недостаточно кубов для роста.");
	
	}
	elseif ($reslt <= $min_tree_active) // Древо Жизни умирает от истощения. Нельзя апать
	{
		$player->syst("Древо Жизни умирает. Его нельзя улучшать в таком состоянии.");
	}
	elseif ($restreelvl < $tree_max_lvl && $canbuild) // Древо живо и его уровень меньше $tree_max_lvl-х
	{
		if (!getTreeUping($clan_id, $restreelvl)) //in clan.php
		{
			$tm = time()+500*3600;
			$dl = $tm;
			f_MQuery("INSERT INTO clan_tree_uping (clan_id, deadline) VALUES (".$clan_id.", ".$tm.")");
			orderBroadcast($clan_id, "Поздравляем! Ваш Орден собрал все необходимое для укрепления Древа Жизни!");
			$maybuild = true;
			die( "<script>location.href='game.php?order=tree';</script>" );
		}
		else $player->syst("Недостаточно кубов или славы Ордена для жертвы.");
	}
	else // уровень Древа Жизни достиг максимума
	{
		$player->syst("Выше вырастить Древо Жизни вы пока не сможете.");
	}
}

if ($dl > 0) //если уже строится
{
	$content .= "<script>FLUl();</script>";
	include_js( 'js/timer.js' );
	$left = $dl - time( ) + 5;
	if ($left <= 0) $content .= "<script>document.write( InsertTimer( 60, 'Еще немного: <b>', '</b>', 1, 'location.href=\"game.php?order=tree\";' ) );</script>";
	else
	{
		if ($reslt < 0)
		{
			$str = "рост";
			$content .= "Древо Жизни посажено. Теперь в него нужно вдохнуть силу.<br><br>";
		}
		else
		{
			$str = "укрепление";
			$content .= "Древо Жизни растет в Лагере Вашего Ордена. Вы можете укрепить его силу с помощью жертвы Богам Алидерии.<br><br>";
		}
		
		$content .= "<b>Идет ".$str." Древа Жизни</b><br><script>document.write( InsertTimer( $left, 'До окончания осталось: <b>', '</b>', 1, 'location.href=\"game.php?order=tree\";' ) );</script>";
		$content .= "<br /><br />";
		$content .= "За <b><img src='/images/umoney.gif' /> 1</b> можно <a href='#' onclick='if( confirm( \"Оплатить 1 час мгновенной стройки?\" ) ) location.href=\"game.php?order=tree&fast=1\";'>купить</a> <b>1 час мгновенной стройки</b>";
		$content .= "<br>За <b><img src='/images/umoney.gif' /> 10</b> можно <a href='#' onclick='if( confirm( \"Оплатить 10 часов мгновенной стройки?\" ) ) location.href=\"game.php?order=tree&fast=10\";'>купить</a> <b>10 часов мгновенной стройки</b>";
		$content .= "<br>За <b><img src='/images/umoney.gif' /> 100</b> можно <a href='#' onclick='if( confirm( \"Оплатить 100 часов мгновенной стройки?\" ) ) location.href=\"game.php?order=tree&fast=100\";'>купить</a> <b>100 часов мгновенной стройки</b>";
	}
}

elseif ($reslt == -2) //нужны частицы на аквивацию
{
	$numG = f_MValue("SELECT number FROM clan_items WHERE color=0 AND item_id=74594 AND clan_id=".$clan_id);
	if (!$numG) $numG=0;
	$numB = f_MValue("SELECT number FROM clan_items WHERE color=0 AND item_id=74595 AND clan_id=".$clan_id);
	if (!$numB) $numB=0;
	$numR = f_MValue("SELECT number FROM clan_items WHERE color=0 AND item_id=74596 AND clan_id=".$clan_id);
	if (!$numR) $numR=0;

	if ($numG >= 500 && $numB >= 500 && $numR >= 500)
		$maybuild = true;

	$content .= "<script>FLUl();</script>";
	$content .= "Древо Жизни посажено. Теперь в него нужно вдохнуть силу.<br><br>";
	$content .= "Соберите воедино по 500 частиц каждой стихии. Найти их несложно: <b><font color=darkblue>Слезы Астаниэль</font></b> хранят Пираньи, <b><font color=darkred>Искры Пламени</font></b> оберегают Гигантские Пауки, а <b><font color=darkgreen>Листья Ка-Написа</font></b> носят с собой Вервольфы. Добудьте нужное количество частиц, и оживите Ваше Древо.<br><br>";
	$content .= "<small>Частицы стихий должны находиться на красной полке склада.</small><br><br>";
	$content .= "<a href='help.php?id=1010&item_id=74594' target=_blank><img src='/images/items/Cube/list.png'><b><font color=darkgreen>Лист Ка-Написа</font></b>:</a>&nbsp;<b>".$numG."/500</b><br>";
	$content .= "<a href='help.php?id=1010&item_id=74595' target=_blank><img src='/images/items/Cube/drop.png'><b><font color=darkblue>Слеза Астаниэль</font></b>:</a>&nbsp;<b>".$numB."/500</b><br>";
	$content .= "<a href='help.php?id=1010&item_id=74596' target=_blank><img src='/images/items/Cube/spark.png'><b><font color=darkred>Искра Пламени</font></b>:</a>&nbsp;<b>".$numR."/500</b><br>";
}
elseif ($reslt == -1) //нужны кубы на активацию
{
	$numG = f_MValue("SELECT number FROM clan_items WHERE color=0 AND item_id=73180 AND clan_id=".$clan_id);
	if (!$numG) $numG=0;
	$numB = f_MValue("SELECT number FROM clan_items WHERE color=0 AND item_id=73179 AND clan_id=".$clan_id);
	if (!$numB) $numB=0;
	$numR = f_MValue("SELECT number FROM clan_items WHERE color=0 AND item_id=73178 AND clan_id=".$clan_id);
	if (!$numR) $numR=0;
	$numN = f_MValue("SELECT number FROM clan_items WHERE color=0 AND item_id=73181 AND clan_id=".$clan_id);
	if (!$numN) $numN=0;

	if ($numG >= 1 && $numB >= 1 && $numR >= 1 && $numN >= 1)
		$maybuild = true;

	$content .= "<script>FLUl();</script>";
	$content .= "Древо Жизни посажено. Теперь в него нужно вдохнуть силу.<br><br>";
	$content .= "Добудьте нужное количество кубов, и оживите Ваше Древо.<br><br>";
	$content .= "<small>Магические Кубы должны находиться на красной полке</small><br><br>";
	$content .= "<a href='help.php?id=1010&item_id=73180' target=_blank><img src='/images/items/Cube/cube_ground.png'>Магический Куб Природы:</a>&nbsp;<b>".$numG."/1</b><br>";
	$content .= "<a href='help.php?id=1010&item_id=73179' target=_blank><img src='/images/items/Cube/cube_water.png'>Магический Куб Воды:</a>&nbsp;<b>".$numB."/1</b><br>";
	$content .= "<a href='help.php?id=1010&item_id=73178' target=_blank><img src='/images/items/Cube/cube_fire.png'>Магический Куб Огня:</a>&nbsp;<b>".$numR."/1</b><br>";
	$content .= "<a href='help.php?id=1010&item_id=73181' target=_blank><img src='/images/items/Cube/cube_neutral.png'>Куб Нейтральной Магии:</a>&nbsp;<b>".$numN."/1</b><br>";

}
elseif ($reslt <= $min_tree_active) //дерево умирает от истощения
{
	$content .= "<script>FLUl();</script>";
	$content .= "Древо Жизни умирает от истощения. Игрокам вашего ордена нужно каждый день заходить в игру для поддержания жизни Древа.";
}
else //дерево активировано и живо более-менее
{
	if ($restreelvl < $tree_max_lvl)
	{
		$numG = f_MValue("SELECT number FROM clan_items WHERE color=0 AND item_id=73180 AND clan_id=".$clan_id);
		if (!$numG) $numG=0;
		$numB = f_MValue("SELECT number FROM clan_items WHERE color=0 AND item_id=73179 AND clan_id=".$clan_id);
		if (!$numB) $numB=0;
		$numR = f_MValue("SELECT number FROM clan_items WHERE color=0 AND item_id=73178 AND clan_id=".$clan_id);
		if (!$numR) $numR=0;
		$numN = f_MValue("SELECT number FROM clan_items WHERE color=0 AND item_id=73181 AND clan_id=".$clan_id);
		if (!$numN) $numN=0;
		$numGlory = f_MValue("SELECT glory FROM clans WHERE clan_id=".$clan_id);
		if (!$numGlory) $numGlory = 0;
	
		if ($restreelvl < 4) {$needtreetoup = $praceuping[$restreelvl]; $needglorytoup = $prace_uping_glory[$restreelvl]; }
		else
			{$needtreetoup = $praceuping[3] + 10*($restreelvl - 3); $needglorytoup = $prace_uping_glory[3] + 200*($restreelvl - 3); }

		if ($numG >= $needtreetoup && $numB >= $needtreetoup && $numR >= $needtreetoup && $numN >= $needtreetoup && $numGlory >= $needglorytoup)
			$maybuild = true;

		$content .= "<script>FLUl();</script>";
		$content .= "Древо Жизни растет в Лагере Вашего Ордена. Вы можете укрепить его силу с помощью жертвы Богам Алидерии.<br>";
		$content .= "Соберите воедино магические кубы каждой стихии, потратьте несколько очков славы. Боги обязательно оценят такую жертву и наградят своих почитателей за заботу о Древе Жизни.<br>";
		$content .= "<small>Магические Кубы должны находиться на красной полке</small><br><br>";
		$content .= "<a href='help.php?id=1010&item_id=73180' target=_blank><img src='/images/items/Cube/cube_ground.png'>Магический Куб Природы:</a>&nbsp;<b>".$numG."/$needtreetoup</b><br>";
		$content .= "<a href='help.php?id=1010&item_id=73179' target=_blank><img src='/images/items/Cube/cube_water.png'>Магический Куб Воды:</a>&nbsp;<b>".$numB."/$needtreetoup</b><br>";
		$content .= "<a href='help.php?id=1010&item_id=73178' target=_blank><img src='/images/items/Cube/cube_fire.png'>Магический Куб Огня:</a>&nbsp;<b>".$numR."/$needtreetoup</b><br>";
		$content .= "<a href='help.php?id=1010&item_id=73181' target=_blank><img src='/images/items/Cube/cube_neutral.png'>Куб Нейтральной Магии:</a>&nbsp;<b>".$numN."/$needtreetoup</b><br>";
		$content .= "Слава Ордена: <b>".$numGlory."/$needglorytoup</b><br>";
	}
	else
	{
		$content .= "<script>FLUl();</script>";
		$content .= "Древо Жизни достигло максимальной высоты. Вряд ли оно вырастет выше.<br>";
	}
}

if ($dl == 0)
	if ($canbuild && $restreelvl < $tree_max_lvl)
	{
		$content .= "<br>";
		if ($reslt < 0)
		{
			if (!$maybuild) $content .= "Недостаточно ресурсов для роста Древа Жизни.";
			else $content .= "<br>Вы можете <b><a href=game.php?order=tree&upfrom=$reslt>начать рост</a></b> Древа Жизни.";
		}
		else
		{
			if (!$maybuild) $content .= "Недостаточно даров или славы Ордена для укрепления Древа Жизни.";
			else $content .= "<br>Вы можете принести жертву Богам и <b><a href=game.php?order=tree&upfrom=$reslt>начать укрепление</a></b> Древа Жизни.";
		}
		$content .= "<script>FLL();</script>";
	}
	else $content .= "<script>FLL();</script>";
else $content .= "<script>FLL();</script>";

$content .= "</td></tr>";

if ($reslt >= 0)
{
	$content .= "<tr><td align=center><script>FLUl();</script>";
	$content .= "<center><b>".$reslt.my_word_str( $reslt, " жизнь", " жизни", " жизней" )."</center></b>";
	$content .= "<script>FLL();</script></td>";
	$content .= "<td align=center><script>FLUl();</script>";
	$res = f_MQuery("SELECT characters.login, player_clans.trup, player_clans.trup_val, characters.player_id FROM player_clans, characters WHERE player_clans.clan_id=".$clan_id." AND player_clans.player_id=characters.player_id ORDER BY player_clans.trup DESC");
	$content .= "<center><table><tr><td width=200 align=center><b>Персонаж</b></td><td width=100 align=center><b>Преданность</b></td><td width=100 align=center><b>Вклад в рост Древа Жизни</b></td></tr>";
	$content .= "<tr><td>&nbsp;</td></tr>";
	
	$content .= "<script src=js/clans.php></script><script src=js/ii.js></script><script>";
	while ($arr = f_MFetch($res))
	{
		if ($arr[2] == 0) $arr2 = "<font color=#000000>".$arr[2]."</font>";
		if ($arr[2] > 0) $arr2 = "<font color=darkgreen>".$arr[2]."</font>";
		if ($arr[2] < 0) $arr2 = "<font color=darkred>".$arr[2]."</font>";
		$plr = new Player($arr[3]);
		$content .= "document.write('<tr><td align=left>' +".$plr->Nick()." + '</td><td align=center>".$arr[1]."</td><td align=center>".$arr2."</td></tr>');\n";
//		$content .= "<tr><td align=left><script>".$plr->Nick().";</script></td><td align=center>".$arr[1]."</td><td align=center>".$arr[2]."</td></tr>";
	}

	$content .= "</script></table></center>";
	$content .= "<script>FLL();</script></td></tr>";
}

$content .= "</table>";
    
                  
echo "<table><tr><td><script>FLUl();</script><table><tr><td><script>FUcm();</script>".$content."<script>FL();</script></td></tr></table><script>FLL();</script></td></tr></table>";
?>
