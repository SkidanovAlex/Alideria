<?

include_once('player.php');

if( !isset( $mid_php ) ) die( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
$player_id = (int)$HTTP_COOKIE_VARS['c_id'];
$player = new Player( $player_id );

$res1 = f_MQuery("SELECT * FROM mobs ORDER BY level");
$res2=$res1;

$mob_id = 0;
if (isset($_GET['mob_id']))
	$mob_id = (int)$_GET['mob_id'];
if ($mob_id < 0) $mob_id = 0;

$hog = 0;
if (isset($_GET['hog']))
	$hog = (int)$_GET['hog'];
if ($hog < 0) $hog = 0;

echo "<br><table><tr><td align=left><script>FLUl();</script>";

// шапка BEGIN
$b1 = "border:1px solid black";
$b2 = "border-left:1px solid black;border-right:1px solid black;border-top:1px solid black";

echo "<table width=100% cellspacing=0 cellpadding=0 height=25><tr align=center>";

if ($hog!=0) $border=$b1; else $border=$b2;
if ($hog!=0) echo "<td style='$border;width:110px;'><a href='game.php?hog=0'><b>Книга Монстров</b></a></td>";
else echo "<td style='$border;width:110px;'>Книга Монстров</td>";

if ($hog!=1) $border=$b1; else $border=$b2;
if ($hog!=1) echo "<td style='$border;width:110px;'><a href='game.php?hog=1'><b>Книга Турниров</b></a></td>";
else echo "<td style='$border;width:110px;'>Книга Турниров</td>";

if ($hog!=2) $border=$b1; else $border=$b2;
if ($hog!=2) echo "<td style='$border;width:110px;'><a href='game.php?hog=2'><b>Книга Дуэлянтов</b></a></td>";
else echo "<td style='$border;width:110px;'>Книга Дуэлянтов</td>";
/*
if ($hog!=3) $border=$b1; else $border=$b2;
if ($hog!=3) echo "<td style='$border;width:110px;'><a href='game.php?hog=3'><b>Книга еще</b></a></td>";
else echo "<td style='$border;width:110px;'>Книга еще</td>";
*/
if ($hog!=-1) echo "<td style='border-bottom:1px solid black;'></td>";

echo "</tr></table><br>";
// шапка END

if ($hog==0) // Книга Монстров
{
	echo "<table width=100%><tr><td><script>FUct();</script><form action='game.php' method=GET>";
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
	
	echo $st;
	echo "<input type=text name=hog value=0 style='display:none;'>";
	echo " <input class=m_btn type=submit value='Смотреть'>";
	
	echo "</form><script>FL();</script></td></tr>";
	
	if ($check_mob)
	{
		echo "<tr><td><script>FUct();</script>";
		if ($mob_id != 0)
		{
			$arr = f_MFetch(f_MQuery("SELECT * FROM mobs WHERE mob_id=".$mob_id));
			echo "<b>Десятка лучших против монстра </b><script>document.write(window.top.ii(".$arr['level'].",'".$arr['name']."','black',0, 0));</script>";
		}
		else echo "<b>Славные воители Теллы</b>";
		echo "<script>FL();</script></td></tr><tr><td></td></tr>";
		echo "<tr><td><script>FUct();</script>";
//		if ($mob_id == 0) echo "<td width=200><b>Монстр</b></td>";
//		echo "</tr>";
		$is_mobs = true;
		if ($mob_id != 0)
		{
			$where = " WHERE mob_id=$mob_id ORDER BY wins DESC, player_id LIMIT 10";
			$res = f_MQuery("SELECT mob_id, player_id, wins FROM mob_wins $where");
			if (f_MNum($res) > 0)
			{
				$i=0;
				echo "<table border=1><tr><td width=20 align=center><b>№</b></td><td width=200><b>Игрок</b></td><td width=60 align=center><b>Побед</b></td></tr>";
				while ($arr = f_MFetch($res))
				{
					$i++;
					$plr = new Player($arr[1]);
					echo "<tr><td width=20 align=center>$i</td><td><script>document.write(".$plr->Nick1().");</script>";
					if ($player->Rank() == 1) echo "<a href='/player_control.php?nick=".$plr->login."' target='_blank' title='Контроль Персонажа ".$plr->login."'><img src='/images/c.gif' style='width: 11px; height: 11px; border: 0px;' /></a>";
					echo "</td><td align=center>$arr[2]</td>";
					if ($mob_id == 0) echo "<td>".f_MValue("SELECT name FROM mobs WHERE mob_id=$arr[0]")."</td>";
					echo "</tr>";
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
			echo "<table border=1><tr><td width=20 align=center><b>№</b></td><td width=200><b>Игрок</b></td><td width=60 align=center><b>Побед</b></td>";
			echo "<td width=200><b>Монстр</b></td></tr>";
			while ($arr1 = f_MFetch($res1))
			{
				$i++;
				$res = f_MQuery("SELECT mob_id, player_id, wins FROM mob_wins WHERE mob_id=".$arr1[0]." ORDER BY wins DESC LIMIT 1");
				if (f_MNum($res) > 0)
				{
					$arr = f_MFetch($res);
					$plr = new Player($arr[1]);
					echo "<tr><td width=20 align=center>$i</td><td><script>document.write(".$plr->Nick1().");</script>";
					if ($player->Rank() == 1) echo "<a href='/player_control.php?nick=".$plr->login."' target='_blank' title='Контроль Персонажа ".$plr->login."'><img src='/images/c.gif' style='width: 11px; height: 11px; border: 0px;' /></a>";
					echo "</td><td align=center>$arr[2]</td>";
					echo "<td><script>document.write(window.top.ii(".$arr1['level'].",'".$arr1['name']."','black',0, 0));</script></td>";
					echo "</tr>";
				}
			}
		}
		if (!$is_mobs)
			echo "Никто не убил этого монстра ни разу.<br>Станьте первым.<script>FL();</script></td></tr></table>";
		else
			echo "</table><script>FL();</script></td></tr></table>";
	}
}
else if ($hog == 1) // Книга Турниров
{
	echo "<table width=100%><tr>";

	echo "<td><script>FUct();</script><form action='game.php' method=GET>";
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
	
	echo $st;
	echo "<input type=text name=hog value=1 style='display:none;'>";
	echo " <input class=m_btn type=submit value='Смотреть'>";
	
	echo "</form><script>FL();</script></td></tr>";

	echo "<td><script>FUct();</script>";
	echo "<b>Славные турнирщики Теллы</b>";
	echo "<script>FL();</script></td></tr>";

	echo "<tr><td><script>FUct();</script>";
	echo "<table border=1 align=center>";

	echo "<tr><td width=20 align=center><b>№</b></td><td width=200 align=left><b>Игрок</b></td><td width=60 align=center><b>Побед</b></td></tr>";

	if ($mob_id == 0)
		$res = f_MQuery("SELECT champion, COUNT( * ) FROM tournament_results GROUP BY champion ORDER BY COUNT( * ) DESC , champion LIMIT 10");
	if ($mob_id == 1)
		$res = f_MQuery("SELECT second_place, COUNT( * ) FROM tournament_results GROUP BY second_place ORDER BY COUNT( * ) DESC , second_place LIMIT 10");
	if ($mob_id == 2)
		$res = f_MQuery("SELECT third_place, COUNT( * ) FROM tournament_results GROUP BY third_place ORDER BY COUNT( * ) DESC , third_place LIMIT 10");
	
	for ($i=1;$i<=10;$i++)
	{
		echo "<tr><td width=20 align=center>$i</td>";
		$arr = f_MFetch($res);
		$plr = new Player($arr[0]);
		echo "<td><script>document.write(".$plr->Nick1().");</script></td><td align=right>$arr[1]</b></td>";
		echo "</tr>";
	}


	echo "</table>";
	echo "<script>FL();</script></td></tr></table>";
}
else if ($hog==2) // Книга Дуэлянтов
{
	echo "<table width=100%><tr>";
	echo "<td><script>FUct();</script>";
	echo "<b>Славные дуэлянты Теллы</b>";
	echo "<script>FL();</script></td></tr>";

	echo "<tr><td><script>FUct();</script>";
	echo "<table align=center><tr><td align=center>";
	echo "<b>Лучшие</b><br>";
	echo "<table border=1 align=center>";

	echo "<tr><td width=20 align=center><b>№</b></td><td><b>Игрок</b></td><td width=60 align=center><b>Побед</b></td></tr>";

	$res = f_MQuery("SELECT player_id, pvp_w FROM player_statistics ORDER BY pvp_w DESC LIMIT 10");
	
	$i = 1;
	while ($arr = f_MFetch($res))
	{
		echo "<tr><td width=20 align=center>$i</td>";
		$plr = new Player($arr[0]);
		echo "<td width=200 align=left><script>document.write(".$plr->Nick1().");</script></td><td align=right>$arr[1]</b></td>";
		$i++;
	}

	echo "</table></td>";

	echo "<td>&nbsp;</td><td align=center><b>Худшие</b><br>";
	echo "<table border=1 align=center>";
	echo "<tr><td width=20 align=center><b>№</b></td><td><b>Игрок</b></td><td width=60 align=center><b>Поражений</b></td></tr>";

	$res = f_MQuery("SELECT player_id, pvp_l FROM player_statistics ORDER BY pvp_l DESC LIMIT 10");
	$i = 1;
	while ($arr = f_MFetch($res))
	{
		echo "<tr><td width=20 align=center>$i</td>";
		$plr = new Player($arr[0]);
		echo "<td width=200 align=left><script>document.write(".$plr->Nick1().");</script></td><td align=right>$arr[1]</b></td>";
		$i++;
	}

	echo "</table></td>";

	echo "</tr></table><script>FL();</script></td></tr></table>";
}

echo "<script>FLL();</script></td></tr></table>";
?>