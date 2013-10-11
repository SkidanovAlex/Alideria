<?
header("Content-type: text/html; charset=windows-1251");

include_once('functions.php');
include_once('player.php');

$hog = (int)$HTTP_RAW_POST_DATA[0];

$ret = "";
if ($hog==0) // Книга Монстров
{
	$ret .= "<table width=100%><tr><td><script>FUct();</script><form action='game.php' method=GET>";
	$check_mob = false;
	$st = "Монстр: ";
	$st .= "<select class=m_btn name='mob_id'>";
	
	$st .= "<option value=0";
	if( $mob_id == 0 ) {$st .= " selected"; $check_mob = true;}
	$st .= ">Все" ;
	while ($arr = f_MFetch($res1))
	{
		$st .= "<option value=$arr[0]";
		if( $mob_id == $arr[0] ) {$st .= " selected"; $check_mob = true;}
		$st .= ">$arr[1]" ;
	}
	$st .= "</select>";
	
	$ret .= $st;
	$ret .= "<input type=text name=hog value=0 style='display:none;'>";
	$ret .= " <input class=m_btn type=submit value='Смотреть'>";
	
	$ret .= "</form><script>FL();</script></td></tr>";
	
	if ($check_mob)
	{
		$ret .= "<tr><td><script>FUct();</script>";
		if ($mob_id != 0)
		{
			$arr = f_MFetch(f_MQuery("SELECT * FROM mobs WHERE mob_id=".$mob_id));
			$ret .= "<b>Десятка лучших против монстра </b><script>document.write(window.top.ii(".$arr['level'].",'".$arr['name']."','black',0, 0));</script>";
		}
		else $ret .= "<b>Славные воители Теллы</b>";
		$ret .= "<script>FL();</script></td></tr><tr><td></td></tr>";
		$ret .= "<tr><td><script>FUct();</script>";
//		if ($mob_id == 0) $ret .= "<td width=200><b>Монстр</b></td>";
//		$ret .= "</tr>";
		$is_mobs = true;
		if ($mob_id != 0)
		{
			$where = " WHERE mob_id=$mob_id ORDER BY wins DESC, player_id LIMIT 10";
			$res = f_MQuery("SELECT mob_id, player_id, wins FROM mob_wins $where");
			if (f_MNum($res) > 0)
			{
				$i=0;
				$ret .= "<table border=1><tr><td width=20 align=center><b>№</b></td><td width=200><b>Игрок</b></td><td width=60 align=center><b>Побед</b></td></tr>";
				while ($arr = f_MFetch($res))
				{
					$i++;
					$plr = new Player($arr[1]);
					$ret .= "<tr><td width=20 align=center>$i</td><td><script>document.write(".$plr->Nick1().");</script>";
					if ($player->Rank() == 1) $ret .= "<a href='/player_control.php?nick=".$plr->login."' target='_blank' title='Контроль Персонажа ".$plr->login."'><img src='/images/c.gif' style='width: 11px; height: 11px; border: 0px;' /></a>";
					$ret .= "</td><td align=center>$arr[2]</td>";
					if ($mob_id == 0) $ret .= "<td>".f_MValue("SELECT name FROM mobs WHERE mob_id=$arr[0]")."</td>";
					$ret .= "</tr>";
				}
			}
			else
				$is_mobs = false;
		}
		else
		{
			f_MQuery("LOCK TABLE mobs WRITE");
			$res1 = f_MQuery("SELECT * FROM mobs ORDER BY level DESC");
			f_MQuery("UNLOCK TABLES");
			$i=0;
			$ret .= "<table border=1><tr><td width=20 align=center><b>№</b></td><td width=200><b>Игрок</b></td><td width=60 align=center><b>Побед</b></td>";
			$ret .= "<td width=200><b>Монстр</b></td></tr>";
			while ($arr1 = f_MFetch($res1))
			{
				$res = f_MQuery("SELECT mob_id, player_id, wins FROM mob_wins WHERE mob_id=".$arr1[0]." ORDER BY wins DESC LIMIT 1");
				if (f_MNum($res) > 0)
				{
					$i++;
					$arr = f_MFetch($res);
					$plr = new Player($arr[1]);
					$ret .= "<tr><td width=20 align=center>$i</td><td><script>document.write(".$plr->Nick1().");</script>";
					if ($player->Rank() == 1) $ret .= "<a href='/player_control.php?nick=".$plr->login."' target='_blank' title='Контроль Персонажа ".$plr->login."'><img src='/images/c.gif' style='width: 11px; height: 11px; border: 0px;' /></a>";
					$ret .= "</td><td align=center>$arr[2]</td>";
					$ret .= "<td><script>document.write(window.top.ii(".$arr1['level'].",'".$arr1['name']."','black',0, 0));</script></td>";
					$ret .= "</tr>";
				}
			}
		}
		if (!$is_mobs)
			$ret .= "Никто не убил этого монстра ни разу.<br>Станьте первым.<script>FL();</script></td></tr></table>";
		else
			$ret .= "</table><script>FL();</script></td></tr></table>";
	}
}
else if ($hog == 1) // Книга Турниров
{
	$ret .= "<table width=100%><tr>";

	$ret .= "<td><script>FUct();</script><form action='game.php' method=GET>";
	$check_mob = false;
	$st = "";
//	$st .= "Выборка: ";
	$st .= "<select class=m_btn name='mob_id'>";
	
	$st .= "<option value=0";
	if( $mob_id == 0 ) $st .= " selected";
	$st .= ">По первым местам";
	$st .= "<option value=1";
	if( $mob_id == 1 ) $st .= " selected";
	$st .= ">По вторым местам";
	$st .= "<option value=2";
	if( $mob_id == 2 ) $st .= " selected";
	$st .= ">По третьим местам";
	$st .= "</select>";
	
	$ret .= $st;
	$ret .= "<input type=text name=hog value=1 style='display:none;'>";
	$ret .= " <input class=m_btn type=submit value='Смотреть'>";
	
	$ret .= "</form><script>FL();</script></td></tr>";

	$ret .= "<td><script>FUct();</script>";
	$ret .= "<b>Славные турнирщики Теллы</b>";
	$ret .= "<script>FL();</script></td></tr>";

	$ret .= "<tr><td><script>FUct();</script>";
	$ret .= "<table border=1 align=center>";

	$ret .= "<tr><td width=20 align=center><b>№</b></td><td width=200 align=left><b>Игрок</b></td><td width=60 align=center><b>Побед</b></td></tr>";

	if ($mob_id == 0)
		$res = f_MQuery("SELECT champion, COUNT( * ) FROM tournament_results GROUP BY champion ORDER BY COUNT( * ) DESC , champion LIMIT 10");
	if ($mob_id == 1)
		$res = f_MQuery("SELECT second_place, COUNT( * ) FROM tournament_results GROUP BY second_place ORDER BY COUNT( * ) DESC , second_place LIMIT 10");
	if ($mob_id == 2)
		$res = f_MQuery("SELECT third_place, COUNT( * ) FROM tournament_results GROUP BY third_place ORDER BY COUNT( * ) DESC , third_place LIMIT 10");
	
	for ($i=1;$i<=10;$i++)
	{
		$ret .= "<tr><td width=20 align=center>$i</td>";
		$arr = f_MFetch($res);
		$plr = new Player($arr[0]);
		$ret .= "<td><script>document.write(".$plr->Nick1().");</script></td><td align=right>$arr[1]</b></td>";
		$ret .= "</tr>";
	}


	$ret .= "</table>";
	$ret .= "<script>FL();</script></td></tr></table>";
}
else if ($hog==2) // Книга Дуэлянтов
{
	$ret .= "<table width=100%><tr>";
	$ret .= "<td><script>FUct();</script>";
	$ret .= "<b>Славные дуэлянты Теллы</b>";
	$ret .= "<script>FL();</script></td></tr>";

	$ret .= "<tr><td><script>FUct();</script>";
	$ret .= "<table align=center><tr><td align=center>";
	$ret .= "<b>Лучшие</b><br>";
	$ret .= "<table border=1 align=center>";

	$ret .= "<tr><td width=20 align=center><b>№</b></td><td><b>Игрок</b></td><td width=60 align=center><b>Побед</b></td></tr>";

	$res = f_MQuery("SELECT player_id, pvp_w FROM player_statistics ORDER BY pvp_w DESC LIMIT 10");
	
	$i = 1;
	while ($arr = f_MFetch($res))
	{
		$ret .= "<tr><td width=20 align=center>$i</td>";
		$plr = new Player($arr[0]);
		$ret .= "<td width=200 align=left><script>document.write(".$plr->Nick1().");</script></td><td align=right>$arr[1]</b></td>";
		$i++;
	}

	$ret .= "</table></td>";

	$ret .= "<td>&nbsp;</td><td align=center><b>Худшие</b><br>";
	$ret .= "<table border=1 align=center>";
	$ret .= "<tr><td width=20 align=center><b>№</b></td><td><b>Игрок</b></td><td width=60 align=center><b>Поражений</b></td></tr>";

	$res = f_MQuery("SELECT player_id, pvp_l FROM player_statistics ORDER BY pvp_l DESC LIMIT 10");
	$i = 1;
	while ($arr = f_MFetch($res))
	{
		$ret .= "<tr><td width=20 align=center>$i</td>";
		$plr = new Player($arr[0]);
		$ret .= "<td width=200 align=left><script>document.write(".$plr->Nick1().");</script></td><td align=right>$arr[1]</b></td>";
		$i++;
	}

	$ret .= "</table></td>";

	$ret .= "</tr></table><script>FL();</script></td></tr></table>";
}

echo "document.getElementById('show_hall_of_glory').innerHTML ='".$ret."'";
?>