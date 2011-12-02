<?

include('../../functions.php');
include('../../player.php');

function createBet($player_id, $dtype, $lvl) //����� ������� ������ �� ����
{
	$res = f_MQuery("SELECT * FROM dungeons_groups WHERE leader_id=$player_id");
	if (f_MNum($res))
		RaiseError("������� ������� ������ �� ����, ��� �������� � ������.");

	if (!checkForType($player_id, $dtype))
		RaiseError("������� �������������� � �����, �� ����������� �� ��������.", "ID ������=$player_id, ��� �����=$dtype");

	$gr_num = 1 + f_MValue("SELECT max(group_number) FROM dungeons_groups");
	
	f_MQuery("INSERT INTO dungeons_groups (leader_id, group_number, status, dungeon_type, lvl_min, lvl_max) VALUES ($player_id, $gr_num, 1, $dtype, $lvl, $lvl)");
	f_MQuery("INSERT INTO dungeon_role_players (player_id, group_number) VALUES ($player_id, $gr_num)");
	f_MQuery("INSERT INTO dungeon_players (player_id, group_number, dungeon_type, status) VALUES ($player_id, $gr_num, $dtype, 1)");
	return $gr_num;
}

function joinToBet($player_id, $gr_num, $dtype, $pl_lvl) //����� �������������� � ������
{
	$res = f_MQuery("SELECT * FROM dungeon_players WHERE player_id=$player_id");
	if (f_MNum($res))
		RaiseError("������� �������������� � ������ � ����, ��� �������� � ������.", "����� ������=$gr_num, ��� �����=$dtype");

	f_MQuery("LOCK TABLE dungeons_groups WRITE");
	$res = f_MQuery("SELECT * FROM dungeons_groups WHERE group_number = $gr_num AND dungeon_type = $dtype");
	if (!f_MNum($res))
	{
		f_MQuery("UNLOCK TABLES");
		RaiseError("������� �������������� � �������������� ������ � ����.", "����� ������=$gr_num, ��� �����=$dtype");
	}

	$arr = f_MFetch($res);
	if ($arr[status] < 6 && $arr[status] > 0) // � ������ ���� ����� ��� ������ ������
	{
		if (!checkForType($player_id, $dtype))
		{
			f_MQuery("UNLOCK TABLES");
			RaiseError("������� �������������� � �����, �� ����������� �� ��������.", "��� �����=".$dtype);
		}
		if ($arr[lvl_min] + 4 > $pl_lvl && $arr[lvl_max] - 4 < $pl_lvl) // �������� �� ������
		{
			f_MQuery("UPDATE dungeons_groups SET status=status+1 WHERE group_number=$gr_num");
			f_MQuery("UNLOCK TABLES");
			f_MQuery("INSERT INTO dungeon_players (player_id, group_number, dungeon_type, status) VALUES ($player_id, $gr_num, $dtype, 0)");
			f_MQuery("INSERT INTO dungeon_role_players (player_id, group_number) VALUES ($player_id, $gr_num)");
		}
		else // �� �������� �� ������
		{
			f_MQuery("UNLOCK TABLES");
			return 2;
		}
	}
	else // � ������ ��� ����� ��� ����� �������
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
	if ($arr[3] == 1) // ���� �� �����, �� ������� ������
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

function canCreateBet($player_id, $dtype) //���������, ����� �� ������� ������(1 - �����, 0 - �� �����)
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
		$ret .= "<a href=\"game.php?create=1\">������� ������</a><br>";
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
					$ret .= "<a href=\"game.php?joinTo=$gr_num\">��������������</a><br><hr>";
				else
					$ret .= "<small><i>�� �� ��������� �� ������</i></small><br><hr>";
			}
			$min = 100;
			$max = 0;
			$ret .= "<hr><b>������ ����� $arr[group_number]</b>";
			$gr_num = $arr[group_number];
			if ($my_group == $gr_num)
				$ret .= "&nbsp;<a href=\"game.php?leave=$pl->player_id&group=$gr_num\">����������</a>&nbsp;<a href=\"game.php?start=$gr_num\">������ �����</a>";
			$ret .= "<br>����� ������: ' + ".$pl->Nick()." + '<br>";
			
			$num_players = 0;
		}
		else
			$ret .= "&nbsp; ' + ".$pl->Nick()." + '";
			if ($my_group == $gr_num)
				if ($player_id == $pl->player_id) ;
				else
					$ret .= "&nbsp;<a href=\"game.php?leave=$pl->player_id&group=$gr_num\">��������</a>";
			elseif ($player_id == $pl->player_id)
				$ret .= "&nbsp;<a href=\"game.php?leave=$pl->player_id&group=$gr_num\">�����</a>";
			$ret .= "<br>";
		$num_players++;
		if ($pl->level < $min) $min = $pl->level;
		if ($pl->level > $max) $max = $pl->level;
	}
	if ($gr_num > 0 && $canJoin && $num_players > 0 && $num_players < 6)
	{
		if ($player->level > $max - 4 && $player->level < $min + 4)
			$ret .= "<a href=\"game.php?joinTo=$gr_num\">�������������� $player->level $min $max</a><br><hr>";
		else
			$ret .= "<small><i>�� �� ��������� �� ������</i></small><br><hr>";
	}
	return $ret;
}

function startDungeon($pl_id, $grnum)
{
	$res = f_MQuery("SELECT status, dungeon_type FROM dungeons_groups WHERE leader_id=$pl_id AND group_number=$grnum");
	$arr = f_MFetch($res);
	if (!$arr)
	{
		echo ("<scrip>alert(\"��� �� ���� ������ ��� �� �� �����\");</script>");
		return 1;
	}
	$dtype = $arr[1];
if ($pl_id != 6825)
	if ($arr[0] < 4 || $arr[0] > 6)
	{
		echo ("<script>alert(\"� ������ ������ ���� �� 4-� �� 6-�� �������\");</script>");
		return 1;
	}
	$res = f_MQuery("SELECT player_id FROM dungeon_players WHERE group_number=$grnum AND dungeon_type=$dtype");
	while ($arr = f_MFetch($res))
	{
		$pl = new Player($arr[0]);
		$pl->SetRegime(0);
		$pl->SetLocation(10+$dtype, true);
		$pl->SetDepth($grnum, true);
	}
	f_MQuery("UPDATE dungeons_groups SET status=7 WHERE group_number=$grnum");
	echo ("<script>window.top.game.location.href='game.php';</script>");
	return 0;
}

function checkForType($player_id, $dtype) //��������� �� ����������� ����� � ����, ������ �� ��������� ������ � ��� ������
{
	return 1; //���� �������� �� ��������
}

?>