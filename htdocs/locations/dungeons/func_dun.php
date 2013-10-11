<?
header("Content-type: text/html; charset=windows-1251");
include_once( "../../no_cache.php" );
include_once('../../functions.php');
include_once('../../player.php');
include_once('func.php');
//include_js( '../js/skin2.js' );
//include_js('func.js');

if( !check_cookie( ) )
	die("window.top.location.href='index.php';");
else
{
	$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
}

$grres = f_MQuery("SELECT p.* FROM dungeon_players as p, dungeons_groups as g WHERE g.status>=7 AND g.group_number=p.group_number AND p.player_id=".$player->player_id);
$grarr = f_MFetch($grres);
if ($grarr)
{
	$grnum = $grarr[1];
	$dun_id = $grarr[2];
	$pl_status = $grarr[3];
	$cur_cell = $grarr[4];
}
else
	$grnum=0;
if ($grnum<=0)
	die("<script>window.top.location.href='main.php';</script>");
	
$dun_id = f_MValue("SELECT dungeon_type FROM dungeons_groups WHERE group_number=".$grnum);


if(isset($_GET['checkForMobs']))
{
  if($player->regime!=0) die();
  f_MQuery("LOCK TABLE dungeon_mobs WRITE, mobs WRITE");
  $res = f_MQuery("SELECT dungeon_mobs.*, mobs.name FROM mobs, dungeon_mobs WHERE mobs.mob_id=dungeon_mobs.mob_id AND dungeon_mobs.group_number=$grnum AND dungeon_mobs.cell_num=".$cur_cell);
  f_MQuery("DELETE FROM dungeon_mobs WHERE group_number=$grnum AND cell_num=$cur_cell");
  f_MQuery("UNLOCK TABLES");
  if(f_MNum($res) == 0)
  {
    die();
  }
  include_once("../../mob.php");
  while($arr = f_MFetch($res))
  {
    $mob = new Mob;
    $mob->CreateMob($arr[3], $player->location, $player->depth);
    $mob->AttackPlayer( $player->player_id, 0, 0, true /* нападаем кроваво */, true );

    setCombatTimeout($mob->combat_id, 60);
    $player->syst2("<b>".$mob->name."</b> нападает на Вас");
  }
  $player->syst2("/combat");
  $combat_id = f_MValue("SELECT combat_id FROM combat_players WHERE player_id = $player->player_id");
  if($combat_id)
    f_MQuery("INSERT INTO dungeon_combats (combat_id, group_number, cell_num) VALUES ($combat_id, $grnum, $cur_cell)");
  echo "showCombats();";
}


if (isset($_GET['showItems']))
{
	echo "reset_loc_items();\n";
	if ($player->regime!=0) die();
	$res = f_MQuery("SELECT d.*, i.name FROM items as i, dungeon_items as d WHERE i.item_id=d.item_id AND d.group_number=$grnum AND d.cell_num=".$cur_cell);
	while ($arr = f_MFetch($res))
	{
		echo "add_loc_item($arr[item_id], '$arr[name]', $arr[number]);\n";
	}
	echo "show_loc_items( );";
}

if (isset($_GET['showPlayers']))
{
	
	$ret = "";
	$res = f_MQuery("SELECT d.* FROM dungeon_players as d, online as o WHERE o.player_id=d.player_id AND group_number=$grnum AND cell_num=".$cur_cell);
//	$ret .= "<script>";
	while ($arr=f_MFetch($res))
	{
		$plr = new Player($arr[0]);
		$ret .= "+".$plr->Nick()."+'<br>'";
	}
//	$ret .= "</script>";
	echo "document.getElementById('player_in_dun').innerHTML = \"\"".$ret."";
	die();
}

if (isset($_GET['showMap']))
{
	$res = f_MQuery("SELECT * FROM dungeons_cells as c, dungeon_players as p WHERE p.player_id=".$player->player_id." AND p.cell_num=c.cell_num");
	$arr = f_MFetch($res);
	$ret = "";
	for ($i=0;$i<3;$i++)
	{
		for ($j=1;$j<=3;$j++)
		{
			if ($i!=1 && $j!=2)
			{
				$ret .= "oneCell(".($j+$i*3).", '&nbsp;', '', 1);\n";
			}
			else
			{
				if ($i==0 && $j==2)
				{
					if ($arr['cell_up']==-1)
						$ret .= "oneCell(2, '&nbsp;', '', 1);\n";
					else
					{
						$arrr = f_MFetch(f_MQuery("SELECT cell_name, cell_img FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$arr['cell_up']));
						$cnm = "'".$arrr[0]."'";
						$cnm .= getPlayers($grnum, $arr['cell_up']);
						$ret .= "oneCell(2, ".$cnm.", '{$arrr[1]}', 0);\n";
					}
				}
				if ($i==1 && $j==1)
				{
					if ($arr['cell_left']==-1)
						$ret .= "oneCell(4, '&nbsp;', '', 1);\n";
					else
					{
						$arrr = f_MFetch(f_MQuery("SELECT cell_name, cell_img FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$arr['cell_left']));
						$cnm = "'".$arrr[0]."'";
						$cnm .= getPlayers($grnum, $arr['cell_left']);
						$ret .= "oneCell(4, ".$cnm.", '{$arrr[1]}', 0);\n";
					}
				}
				if ($i==1 && $j==3)
				{
					if ($arr['cell_right']==-1)
						$ret .= "oneCell(6, '&nbsp;', '', 1);\n";
					else
					{
						$arrr = f_MFetch(f_MQuery("SELECT cell_name, cell_img FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$arr['cell_right']));
						$cnm = "'".$arrr[0]."'";
						$cnm .= getPlayers($grnum, $arr['cell_right']);
						$ret .= "oneCell(6, ".$cnm.", '{$arrr[1]}', 0);\n";
					}
				}
				if ($i==2 && $j==2)
				{
					if ($arr['cell_down']==-1)
						$ret .= "oneCell(8, '&nbsp;', '', 1);\n";
					else
					{
						$arrr = f_MFetch(f_MQuery("SELECT cell_name, cell_img FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$arr['cell_down']));
						$cnm = "'".$arrr[0]."'";
						$cnm .= getPlayers($grnum, $arr['cell_down']);
						$ret .= "oneCell(8, ".$cnm.", '{$arrr[1]}', 0);\n";
					}
				}
				if ($i==1 && $j==2)
				{
					$cnm = "'".$arr['cell_name']."'".getPlayers($grnum, $arr['cell_num']);
					$ret .= "oneCell(5, ".$cnm.", '{$arr['cell_img']}', 0);\n";
				}
			}
		}
	}
	echo $ret;
	die();
}

if (isset($_GET['move']))
{
	$mv = $_GET['move'];
	if ($player->regime != 0)
		die();
	$new_cell = checkCanMove($dun_id, $cur_cell, $mv, $player->player_id);
	if ($new_cell<0)
		die();
	$tm = time() + 3;
	$player->SetRegime($mv);
	$player->SetTill($tm);
	$new_cell_name = f_MValue("SELECT cell_name FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$new_cell);
	echo "mvTo(5, '{$new_cell_name}');";
	die();
}

if (isset($_GET['checkStatus']))
{
	echo "refLock();\n";
	if ($player->regime == 0 && $player->till == 0)
		echo "document.getElementById('you_doing').innerHTML = 'Вы стоите на месте';";
	else
	{
		echo "document.getElementById('you_doing').innerHTML = 'Вы стоите на месте';";
		$tm = $player->till-time();
		if ($tm > 0)
		{
			$new_cell = checkCanMove($dun_id, $cur_cell, $player->regime, $player->player_id);
			$new_cell_name = f_MValue("SELECT cell_name FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$new_cell);
			echo "mvTo(".$tm.", '".$new_cell_name."');";
		}
		else
		{
			$new_cell = checkCanMove($dun_id, $cur_cell, $player->regime, $player->player_id);
			if ($new_cell >= 0)
				moveIt($player->player_id, $new_cell);
			$player->SetRegime(0);
			$player->SetTill(0);
			echo "refLock();\n";
			echo "document.getElementById('you_doing').innerHTML = 'Вы стоите на месте';";
		}
	}
	die();
}


/*
function oneCell($cell_num="", $name = "&nbsp;", $empty = 1)
{
	if ($cell_num != 5)
	{
		$r = "";
		if ($empty)
			$r .= "<script>FUlt();</script>";
		else
			$r .= "<script>FLUl();</script>";
		$r .= "<table onclick=\"alert('".$cell_num."');\"><tr><td align=center valign=middle style='width:90px;height:90px;'>";
		if ($empty)
			$r .= "&nbsp;</td></tr></table><script>FL();</script>";
		else
			$r .= $name."</td></tr></table><script>FLL();</script>";
	}
	else // центральная клетка
	{
		
	}
	return $r;
}

function showMap($grnum, $dtype, $cell)
{
	$res = f_MQuery("SELECT * FROM dungeons_cells WHERE dungeon_id=$dtype AND cell_num=$cell");
	$arr_my = f_MFetch($res);
	if (!$arr_my)
		return -1;
	$ret = "<table><tr>".oneCell(1);
	if ($arr_my[3] != -1)
	{
		$res = f_MQuery("SELECT * FROM dungeons_cells WHERE dungeon_id=$dtype AND cell_num=$arr_my[3]");
		$arr = f_MFetch($res);
		$ret .= oneCell(2, $arr[2], 0);
	}
	else
		$ret .= oneCell(2);

	$ret .= oneCell(3)."</tr><tr>";
	if ($arr_my[6] != -1)
	{
		$res = f_MQuery("SELECT * FROM dungeons_cells WHERE dungeon_id=$dtype AND cell_num=$arr_my[6]");
		$arr = f_MFetch($res);
		$ret .= oneCell(4, $arr[2], 0);
	}
	else
		$ret .= oneCell(4);
	$ret .= oneCell(5, $arr_my[2], 0);
	if ($arr_my[4] != -1)
	{
		$res = f_MQuery("SELECT * FROM dungeons_cells WHERE dungeon_id=$dtype AND cell_num=$arr_my[4]");
		$arr = f_MFetch($res);
		$ret .= oneCell(6, $arr[2], 0)."</tr><tr>";
	}
	else
		$ret .= oneCell(6)."</tr><tr>";
	$ret .= oneCell(7);
	if ($arr_my[5] != -1)
	{
		$res = f_MQuery("SELECT * FROM dungeons_cells WHERE dungeon_id=$dtype AND cell_num=$arr_my[5]");
		$arr = f_MFetch($res);
		$ret .= oneCell(8, $arr[2], 0);
	}
	else
		$ret .= oneCell(8);
	$ret .= oneCell(9);

	$ret .= "</tr></table>";
	return $ret;
}

function showPlayers($grnum)
{
	$r = "";
	$res = f_MQuery("SELECT * FROM dungeon_players WHERE group_number=$grnum");
	$r .= "<script>";
	while ($arr=f_MFetch($res))
	{
		$plr = new Player($arr[0]);
		$r .= "document.write(".$plr->Nick2().");";
	}
	$r .= "</script>";
	return $r;
}
*/
?>