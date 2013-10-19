<?

header("Content-type: text/html; charset=windows-1251");

include( '../../functions.php' );
include( '../../arrays.php' );

f_MConnect( );



include( 'admin_header.php' );

$str = $HTTP_RAW_POST_DATA;
list($dun_id, $x, $y) = @explode( "|", $str );
$cell = $x*100+$y;

function create_select( $nm, $arr, $val )
{
	$st = "<select onchange='ch_img_folder()' id='$nm' name='$nm'>";
	
	foreach( $arr as $key=>$value )
	{
		$st .= "<option value=$key";
		if( $key == $val ) $st .= " selected";
		$st .= ">$value" ;
	}
	
	$st .= '</select>';
	
	return $st;
}

if (isset($_GET['show']))
{
	$res = f_MQuery("SELECT * FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$cell);
	if (f_MNum($res))
	{
		$arr = f_MFetch($res);
		echo "showDunActions({$x}, {$y}, 0, {$arr[4]}, {$arr[5]}, {$arr[6]}, {$arr[7]});";
	}
	else
	{
		echo "showDunActions({$x}, {$y}, 0, -1, -1, -1, -1);";
	}
	die();
}

if (isset($_GET['get_mobs']))
{
	//echo "alert('Мобов хочу!');";
	$ret = "";
	$ret .= "<table border=1><tr><td>Имя монстра</td><td>&nbsp;</td></tr>";
	$res = f_MQuery("SELECT d.*, m.name FROM dungeon_template_mobs as d, mobs as m WHERE m.mob_id=d.mob_id AND d.dungeon_id=$dun_id AND d.cell_num=".$cell);
	while ($arr = f_MFetch($res))
	{
		$ret .= "<tr><td>$arr[5]</td><td><button onclick=\"addMob(".$arr[3].", -1)\">Удалить</button></td></tr>";
	}
	$ret .= "<tr><td colspan=2><hr></td></tr>";
	$ret .= "<tr><td><input type=text id=mb_id value=0></td><td><button onclick=\"addMob(0, 1)\">Добавить</button></td></tr>";
	
	$ret .= "</table>";
	echo "document.getElementById('dmobs').innerHTML = '".$ret."';";
	die();
}

if (isset($_GET['get_items']))
{
	$ret = "";
	$ret .= "<table border=1><tr><td>Имя предмета</td><td>Количество</td><td>&nbsp;</td></tr>";
	$res = f_MQuery("SELECT d.*, i.name FROM dungeon_template_items as d, items as i WHERE i.item_id=d.item_id AND d.dun_id=$dun_id AND d.cell_num=".$cell);
	while ($arr = f_MFetch($res))
	{
		$ret .= "<tr><td>$arr[4]</td><td>$arr[3]</td><td><button onclick=\"addItem(".$arr[2].", -1)\">Удалить</button></td></tr>";
	}
	$ret .= "<tr><td colspan=3><hr></td></tr>";
	$ret .= "<tr><td><input type=text id=itm_id value=0></td><td><input type=text id=itm_num value=0></td><td><button onclick=\"addItem(0, 0)\">Добавить</button></td></tr>";
	
	$ret .= "</table>";
	echo "document.getElementById('ditms').innerHTML = '".$ret."';";
	die();
}

if(isset($_GET['get_traps']))
{
  $ret = "";
  $ret .= "<table border=1><tr><td>Имя ловушки</td><td width=300>Описание</td><td>ID эффекта</td><td>Прочность макс.</td><td>Хрупкость</td><td>&nbsp;</td></tr>";
  $ret .= "</table>";
	echo "document.getElementById('dtraps').innerHTML = '".$ret."';";
	die();
}

if (isset($_GET['item_id']) && isset($_GET['number']))
{
	$item_id = (int)$_GET['item_id'];
	$number = (int)$_GET['number'];
	if (f_MValue("SELECT COUNT(*) FROM dungeons_cells WHERE cell_num=$cell AND dungeon_id=".$dun_id) <= 0)
		die("alert('Клетка еще не создана!');");
	if ($number > 0)
	{
		if (f_MValue("SELECT COUNT(*) FROM items WHERE item_id=".$item_id) <= 0)
			die("alert('Нет такого предмета!');");
		f_MQuery("INSERT INTO dungeon_template_items (dun_id, cell_num, item_id, number) VALUES ($dun_id, $cell, $item_id, $number)");
	}
	if ($number == -1)
		f_MQuery("DELETE FROM dungeon_template_items WHERE cell_num=$cell AND dun_id=$dun_id AND item_id=".$item_id);
	echo ("query('dun_ref.php?get_items=0', dun_id+'|'+Math.round(cur_cell/100)+'|'+(cur_cell%100));");
}

if (isset($_GET['mob_id']) && isset($_GET['number']))
{
	$mob_id = (int)$_GET['mob_id'];
	$number = (int)$_GET['number'];
	if (f_MValue("SELECT COUNT(*) FROM dungeons_cells WHERE cell_num=$cell AND dungeon_id=".$dun_id) <= 0)
		die("alert('Клетка еще не создана!');");
	if ($number > 0)
	{
		if (f_MValue("SELECT COUNT(*) FROM mobs WHERE mob_id=".$mob_id) <= 0)
			die("alert('Нет такого монстра!');");
		f_MQuery("INSERT INTO dungeon_template_mobs (dungeon_id, cell_num, mob_id) VALUES ($dun_id, $cell, $mob_id)");
	}
	if ($number == -1)
		f_MQuery("DELETE FROM dungeon_template_mobs WHERE cell_num=$cell AND dungeon_id=$dun_id AND mob_id=$mob_id LIMIT 1");
	echo ("query('dun_ref.php?get_mobs=0', dun_id+'|'+Math.round(cur_cell/100)+'|'+(cur_cell%100));");
}


if (isset($_GET['get_images']))
{
	$img_num = $_GET['get_images'];
	$img_folders = glob("../../images/dungeons/*", GLOB_ONLYDIR);
	
	$ret = create_select('img', $img_folders, $img_num)."<br>";

	$imageList = glob($img_folders[$img_num]."/*.jpg");
	
	$j=0;
	if ($imageList)
	{
		$ret .= "<table border=1>";
		foreach ($imageList as $i)
		{
			if ($j==0)
				$ret .= "<tr>";
			$ii = substr($i, 22);
			$ret .= "<td><img onclick=setImg('$ii') src='$i'></td>";
			$j++;
			if ($j==3)
			{
				$j = 0;
				$ret .= "</tr>";
			}
		}
		if ($j!=0)
			$ret .= "</tr>";
		$ret .= "</table>";
	}
	else
		$ret .= "Нет тайлов в этой папке";
	
	echo "document.getElementById('dimgs').innerHTML = \"".$ret."\";";
}

if (isset($_GET['img']))
{
	$img = $_GET['img'];
	if (!glob("../../images/dungeons/".$img))
		die("alert('Такой картинки не найдено! $img');");
	
	if (f_MValue("SELECT COUNT(*) FROM dungeons_cells WHERE cell_num=$cell AND dungeon_id=".$dun_id) <= 0)
		die("alert('Клетка еще не создана!');");
	f_MQuery("UPDATE dungeons_cells SET cell_img='".$img."' WHERE dungeon_id=$dun_id AND cell_num=".$cell);
	echo "cells[$cell].img='$img';";
	echo "showCell($x, $y);";
}

if (isset($_GET['name']))
{
	if (f_MValue("SELECT COUNT(*) FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$cell))
		f_MQuery("UPDATE dungeons_cells SET cell_name='{$_GET['name']}' WHERE dungeon_id=$dun_id AND cell_num=".$cell);
	else
		f_MQuery("INSERT INTO dungeons_cells (dungeon_id, cell_num, cell_name) VALUES ({$dun_id}, {$cell}, '{$_GET['name']}')");
	echo "updateNameCell({$cell}, '".$_GET['name']."');\n";
	echo "_('dun_load_st').innerHTML = 'Ok';\n";
	echo "showCell($x, $y);";
	die();
}

if (isset($_GET['dr']))
{
	$dr = $_GET['dr'];
	$cur=$_GET['cur_cell'];
	$cc=f_MValue("SELECT COUNT(*) FROM dungeons_cells WHERE (cell_num=$cell OR cell_num=$cur) AND dungeon_id=".$dun_id);
	if ($cell != $cur && $cc < 2 || $cell==$cur && $cc < 1)
		die("click_st_func(0);\nalert('Одна или обе клетки еще не созданы!'+$cur+' '+$cell);\n");
	if ($cell==$cur)
	{
		if ($dr==1)
			$cell=f_MValue("SELECT cell_up FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$cur);
		if ($dr==2)
			$cell=f_MValue("SELECT cell_left FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$cur);
		if ($dr==3)
			$cell=f_MValue("SELECT cell_right FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$cur);
		if ($dr==4)
			$cell=f_MValue("SELECT cell_down FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$cur);
		if ($cell!=-1)
			echo "setCellColor($cell, 'blue', '&nbsp;');\n";
		$cell=-1;
	}
	if ($dr==1)
	{
		$cc = f_MValue("SELECT cell_up FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$cur);
		echo "setCellColor($cc, 'blue', '&nbsp;');\n";
		f_MQuery("UPDATE dungeons_cells SET cell_up=$cell WHERE dungeon_id=$dun_id AND cell_num=".$cur);
		echo "cells[$cur].up=$cell;\n";
	}
	if ($dr==2)
	{
		$cc = f_MValue("SELECT cell_left FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$cur);
		echo "setCellColor($cc, 'blue', '&nbsp;');\n";
		f_MQuery("UPDATE dungeons_cells SET cell_left=$cell WHERE dungeon_id=$dun_id AND cell_num=".$cur);
		echo "cells[$cur].left=$cell;\n";
	}
	if ($dr==3)
	{
		$cc = f_MValue("SELECT cell_right FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$cur);
		echo "setCellColor($cc, 'blue', '&nbsp;');\n";
		f_MQuery("UPDATE dungeons_cells SET cell_right=$cell WHERE dungeon_id=$dun_id AND cell_num=".$cur);
		echo "cells[$cur].right=$cell;\n";
	}
	if ($dr==4)
	{
		$cc = f_MValue("SELECT cell_down FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$cur);
		echo "setCellColor($cc, 'blue', '&nbsp;');\n";
		f_MQuery("UPDATE dungeons_cells SET cell_down=$cell WHERE dungeon_id=$dun_id AND cell_num=".$cur);
		echo "cells[$cur].down=$cell;\n";
	}
	$x = (int)($cur / 100);
	$y = $cur % 100;
	echo "click_st_func(0);\nshowCell($x, $y);\n";
	die();
}

if (isset($_GET['del']))
{
	$res = f_MQuery("SELECT * FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$cell);
	if (!f_MNum($res))
		die();
	f_MQuery("DELETE FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_num=".$cell);
	echo "setCellColor(cells[$cell].up, 'blue', '&nbsp;');\n";
	echo "setCellColor(cells[$cell].left, 'blue', '&nbsp;');\n";
	echo "setCellColor(cells[$cell].right, 'blue', '&nbsp;');\n";
	echo "setCellColor(cells[$cell].down, 'blue', '&nbsp;');\n";
	echo "delete cells[$cell]\n";
	echo "_('cell_".$x."_".$y."').style.cursor=''\n";
	$res = f_MQuery("SELECT cell_num FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_up=".$cell);
	if (f_MNum($res))
		f_MQuery("UPDATE dungeons_cells SET cell_up=-1 WHERE dungeon_id=$dun_id AND cell_up=".$cell);
	while ($arr = f_MFetch($res))
		echo "cells[{$arr[0]}].up=-1;\n";
	$res = f_MQuery("SELECT cell_num FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_left=".$cell);
	if (f_MNum($res))
		f_MQuery("UPDATE dungeons_cells SET cell_left=-1 WHERE dungeon_id=$dun_id AND cell_left=".$cell);
	while ($arr = f_MFetch($res))
		echo "cells[{$arr[0]}].left=-1;\n";
	$res = f_MQuery("SELECT cell_num FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_right=".$cell);
	if (f_MNum($res))
		f_MQuery("UPDATE dungeons_cells SET cell_right=-1 WHERE dungeon_id=$dun_id AND cell_right=".$cell);
	while ($arr = f_MFetch($res))
		echo "cells[{$arr[0]}].right=-1;\n";
	$res = f_MQuery("SELECT cell_num FROM dungeons_cells WHERE dungeon_id=$dun_id AND cell_down=".$cell);
	if (f_MNum($res))
		f_MQuery("UPDATE dungeons_cells SET cell_down=-1 WHERE dungeon_id=$dun_id AND cell_down=".$cell);
	while ($arr = f_MFetch($res))
		echo "cells[{$arr[0]}].down=-1;\n";
	echo "showCell($x, $y);\n";
	die();
}

?>
