<?

include_once('../../functions.php');
include_once('../../player.php');
include_js( '../js/skin2.js' );
//include_js('func.js');

function oneCell($cell_num="", $name = "&nbsp;", $empty = 1)
{
	if ($cell_num != 10)
	{
		$r = "";
		if ($empty)
			$r .= "<td><script>FUlt();</script>";
		else
			$r .= "<td><script>FLUl();</script>";
		$r .= "<table id=cell$cell_num onclick=\"alert('1');\"><tr><td align=center valign=middle style='width:90px;height:90px;'>";
		if ($empty)
			$r .= "&nbsp;</td></tr></table><script>FL();</script></td>";
		else
			$r .= $name."</td></tr></table><script>FLL();</script></td>";
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

?>