<?

include_once('../../functions.php');
include_once('../../player.php');

function createBet($player_id, $dtype, $lvl) //лидер создает заявку на данж
{
	$res = f_MQuery("SELECT * FROM dungeons_groups WHERE leader_id=$player_id");
	if (f_MNum($res))
		RaiseError("Попытка создать заявку на данж, уже находясь в заявке.");

	if (!checkForType($player_id, $dtype))
		RaiseError("Попытка присоединиться к данжу, не подходящему по условиям.", "ID игрока=$player_id, тип данжа=$dtype");

	$gr_num = 1 + f_MValue("SELECT max(group_number) FROM dungeons_groups");
	
	f_MQuery("INSERT INTO dungeons_groups (leader_id, group_number, status, dungeon_type, lvl_min, lvl_max) VALUES ($player_id, $gr_num, 1, $dtype, $lvl, $lvl)");
	f_MQuery("INSERT INTO dungeon_role_players (player_id, group_number) VALUES ($player_id, $gr_num)");
	f_MQuery("INSERT INTO dungeon_players (player_id, group_number, dungeon_type, status) VALUES ($player_id, $gr_num, $dtype, 1)");
	return $gr_num;
}

function joinToBet($player_id, $gr_num, $dtype, $pl_lvl) //игрок присоединяется к заявке
{
	$res = f_MQuery("SELECT * FROM dungeon_players WHERE player_id=$player_id");
	if (f_MNum($res))
		RaiseError("Попытка присоединиться к заявке в данж, уже находясь в группе.", "Номер группы=$gr_num, тип данжа=$dtype");

	f_MQuery("LOCK TABLE dungeons_groups WRITE");
	$res = f_MQuery("SELECT * FROM dungeons_groups WHERE group_number = $gr_num AND dungeon_type = $dtype");
	if (!f_MNum($res))
	{
		f_MQuery("UNLOCK TABLES");
		RaiseError("Попытка присоединиться к несуществующей заявке в данж.", "Номер группы=$gr_num, тип данжа=$dtype");
	}

	$arr = f_MFetch($res);
	if ($arr[status] < 6 && $arr[status] > 0) // в группе есть место для нового игрока
	{
		if (!checkForType($player_id, $dtype))
		{
			f_MQuery("UNLOCK TABLES");
			RaiseError("Попытка присоединиться к данжу, не подходящему по условиям.", "Тип данжа=".$dtype);
		}
		if ($arr[lvl_min] + 4 > $pl_lvl && $arr[lvl_max] - 4 < $pl_lvl) // подходим по уровню
		{
			f_MQuery("UPDATE dungeons_groups SET status=status+1 WHERE group_number=$gr_num");
			f_MQuery("UNLOCK TABLES");
			f_MQuery("INSERT INTO dungeon_players (player_id, group_number, dungeon_type, status) VALUES ($player_id, $gr_num, $dtype, 0)");
			f_MQuery("INSERT INTO dungeon_role_players (player_id, group_number) VALUES ($player_id, $gr_num)");
		}
		else // не подходим по уровню
		{
			f_MQuery("UNLOCK TABLES");
			return 2;
		}
	}
	else // в группе нет места для новых игроков
	{
		f_MQuery("UNLOCK TABLES");
		return 1;
	}
	return 0;
	

}

function LeaveFromBet($player_id, $gr_num)
{
	f_MQuery("LOCK TABLE dungeon_players WRITE");
	$res = f_MQuery("SELECT * FROM dungeon_players WHERE player_id=".$player_id);
	f_MQuery("UNLOCK TABLES");
	$arr = f_MFetch($res);
	if ($arr[1] != $gr_num) return 1;
	if ($arr[3] == 1) // если мы лидер, то роспуск группы
	{
//		f_MQuery("LOCK TABLE dungeon_players WRITE, dungeon_role_players WRITE, dungeons_groups WRITE, characters WRITE");
		$res = f_MQuery("SELECT * FROM dungeon_players WHERE group_number=".$gr_num);
		while ($arr1 = f_MFetch($res))
		{
			f_MQuery("DELETE FROM dungeon_role_players WHERE player_id=$arr1[0]");
			f_MQuery("DELETE FROM dungeon_players WHERE player_id=$arr1[0]");
			f_MQuery("UPDATE characters SET regime=0 WHERE player_id=$arr1[0]");
		}
		f_MQuery("DELETE FROM dungeons_groups WHERE group_number=$gr_num");
//		f_MQuery("UNLOCK TABLES");
	}
	else
	{
		f_MQuery("DELETE FROM dungeon_role_players WHERE player_id=$player_id");
		f_MQuery("DELETE FROM dungeon_players WHERE player_id=$player_id");
//		f_MQuery("LOCK TABLE dungeons_groups WRITE, characters WRITE");
		f_MQuery("UPDATE dungeons_groups SET status=status-1 WHERE group_number=$gr_num");
		f_MQuery("UPDATE characters SET regime=0 WHERE player_id=$player_id");
//		f_MQuery("UNLOCK TABLES");
	}
	return 0;
}

function canCreateBet($player_id, $dtype) //проверяем, можно ли создать заявку(1 - можем, 0 - не можем)
{
	if (!checkForType($player_id, $dtype))
		return 0;
	if (!f_MNum(f_MQuery("SELECT * FROM dungeon_players WHERE player_id=$player_id")))
		return 1;
	else
		return 0;
}

function showBets($player_id, $dtype)
{
	global $player;
	$ret = "";
	$res = f_MQuery("SELECT * FROM dungeon_players WHERE player_id=$player_id");
	if (!f_MNum($res)) $canJoin = true;
	else $canJoin = false;
	$my_group = 0;
	if ($canJoin)
	{
		$ret .= "<a href=\"game.php?create=1\">Создать заявку</a><br>";
	}
	else
	{
		$arr = f_MFetch($res);
		if ($arr[3] == 1)
			$my_group = $arr[1];
	}
	$res = f_MQuery("SELECT p.* FROM dungeon_players as p, dungeons_groups as g WHERE p.group_number=g.group_number AND p.dungeon_type=g.dungeon_type AND g.status>0 AND g.status<6 ORDER BY group_number,  status DESC");
	$min = 100;
	$max = 0;
	$gr_num = 0;
	$num_players = 0;
	$pl = 0;
	while ($arr = f_MFetch($res))
	{
		$pl = new Player($arr[player_id]);
		if ($arr[status] == 1)
		{
			if ($gr_num > 0 && $canJoin && $num_players > 0 && $num_players < 6)
			{
				if ($pl->level <= $max && $pl->level >= $min)
					$ret .= "<a href=\"game.php?joinTo=$gr_num\">Присоединиться</a><br><hr>";
				else
					$ret .= "<small><i>Вы не подходите по уровню</i></small><br><hr>";
			}
			$min = 100;
			$max = 0;
			$ret .= "<hr><b>Группа номер $arr[group_number]</b>";
			$gr_num = $arr[group_number];
			if ($my_group == $gr_num)
				$ret .= "&nbsp;<a href=\"game.php?leave=$pl->player_id&group=$gr_num\">Распустить</a>&nbsp;<a href=\"game.php?start=$gr_num\">Начать забег</a>";
			$ret .= "<br>Лидер группы: ' + ".$pl->Nick()." + '<br>";
			
			$num_players = 0;
		}
		else
			$ret .= "&nbsp; ' + ".$pl->Nick()." + '";
			if ($my_group == $gr_num)
				if ($player_id == $pl->player_id) ;
				else
					$ret .= "&nbsp;<a href=\"game.php?leave=$pl->player_id&group=$gr_num\">Прогнать</a>";
			elseif ($player_id == $pl->player_id)
				$ret .= "&nbsp;<a href=\"game.php?leave=$pl->player_id&group=$gr_num\">Выйти</a>";
			$ret .= "<br>";
		$num_players++;
		if ($pl->level < $min) $min = $pl->level;
		if ($pl->level > $max) $max = $pl->level;
	}
	if ($gr_num > 0 && $canJoin && $num_players > 0 && $num_players < 6)
	{
		if ($player->level > $max - 4 && $player->level < $min + 4)
			$ret .= "<a href=\"game.php?joinTo=$gr_num\">Присоединиться $player->level $min $max</a><br><hr>";
		else
			$ret .= "<small><i>Вы не подходите по уровню</i></small><br><hr>";
	}
	return $ret;
}

function startDungeon($pl_id, $grnum)
{
	$res = f_MQuery("SELECT status, dungeon_type FROM dungeons_groups WHERE leader_id=$pl_id AND group_number=$grnum");
	$arr = f_MFetch($res);
	if (!$arr)
	{
		echo ("<scrip>alert(\"Это не ваша группа или вы не лидер\");</script>");
		return 1;
	}
	$dtype = $arr[1];
if ($pl_id != 6825 && $pl_id != 1835898 && $pl_id != 173)
	if ($arr[0] < 4 || $arr[0] > 6)
	{
		echo ("<script>alert(\"В группе должно быть от 4-х до 6-ти игроков\");</script>");
		return 1;
	}
	$res = f_MQuery("SELECT player_id FROM dungeon_players WHERE group_number=$grnum AND dungeon_type=$dtype");
	while ($arr = f_MFetch($res))
	{
		$pl = new Player($arr[0]);
		$pl->SetRegime(0);
		$pl->SetLocation(100+$dtype, true);
		$pl->SetDepth($grnum, true);
		$pl->syst2("/items");
	}
	$res = f_MQuery("SELECT * FROM dungeon_template_items WHERE dun_id=$dtype");
	while ($arr = f_MFetch($res))
		f_MQuery("INSERT INTO dungeon_items (group_number, cell_num, item_id, number) VALUES ($grnum, $arr[1], $arr[2], $arr[3])");
	f_MQuery("UPDATE dungeons_groups SET status=7 WHERE group_number=$grnum");
	echo ("<script>window.top.game.location.href='game.php';</script>");
	return 0;
}

function getPlayers($grnum, $cell_num)
{
	$res = f_MQuery("SELECT d.player_id FROM dungeon_players as d, online as o WHERE o.player_id=d.player_id AND d.group_number=$grnum AND d.cell_num=".$cell_num);
	$ret = "";
	while ($arr = f_MFetch($res))
	{
		$plr = new Player($arr[0]);
		$ret .= "+'<br>'+".$plr->Nick1();
	}
	return $ret;
}

function checkForType($player_id, $dtype) //проверяем на возможность зайти в данж, исходя из положения игрока и его уровня
{
	return 1; //пока проверка не написана
}

function moveIt($pl_id, $cell_num)
{
	f_MQuery("UPDATE dungeon_players SET cell_num=$cell_num WHERE player_id=".$pl_id);
}

function checkCanMove($dtype, $cell_from, $move_to, $pl_id)
{
	$str = "";
	if ($move_to==2)
		$str = "cell_up";
	if ($move_to==4)
		$str = "cell_left";
	if ($move_to==6)
		$str = "cell_right";
	if ($move_to==8)
		$str = "cell_down";
	if ($str == "") return -1;
	$res = f_MQuery("SELECT ".$str." FROM dungeons_cells WHERE dungeon_id=".$dtype." AND cell_num=".$cell_from);
	if ($arr = f_MFetch($res))
		return (int)$arr[0];
	else
		return -1;
}

?>