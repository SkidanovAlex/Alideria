<?
header("Content-type: text/html; charset=windows-1251");

include_once("functions.php");

f_MConnect();

if (isset($_GET['show_tree']))
{
//	echo "alert('show tree');";
	$res = f_MQuery("SELECT * FROM owners");
	while ($arr = f_MFetch($res))
	{
//		$arr[1] = iconv("UTF-8", "windows-1251", $arr[1]);
		echo "addZak($arr[0], '$arr[2]', $arr[1]);\n";
	}
	$res = f_MQuery("SELECT * FROM places");
	while ($arr = f_MFetch($res))
		echo "addPlace($arr[0], '$arr[1]');\n";
	$res = f_MQuery("SELECT project_id, project_name, place_id, kust_number, skv_number, owner_id FROM projects");
	while ($arr = f_MFetch($res))
	{
		$res_p = f_MQuery("SELECT i.sn, i.pipe_id FROM pipes as i, project_pipe as p WHERE i.pipe_id=p.pipe_id AND p.project_id=".$arr[0]);
		$p = "[";
		while ($arr_p = f_MFetch($res_p))
		{
			if ($p!="[")
				$p .= ", ";
			$p .= "[".$arr_p[1].",'".$arr_p[0]."']";
		}
		$p .= "]";
		echo "addProject($arr[0], '$arr[1]', $p, $arr[2], '".(int)$arr[3]."', '".(int)$arr[4]."', $arr[5]);\n";
	}
	echo "showZaks();\n";
	die();
}

if (isset($_GET['getProject']) && isset($_GET['p_id']))
{
	$arr_clrs = Array(0=>"#00FF00", "#0000FF", "#FFFF00", "#FF0000", "#FF00FF");
//	echo "alert('get ".$_GET['getProject']." project');";
	$pr_id = $_GET['getProject'];
//	$p_id = f_MValue("SELECT pipe_id FROM pipes WHERE sn='".$_GET['p_id']."'");
	$p_id = $_GET['p_id'];
	$pr_name = f_MValue("SELECT l.place_name FROM places as l, projects as p WHERE p.place_id=l.place_id AND p.project_id=".$pr_id);
	$pr_arr = f_MFetch(f_MQuery("SELECT kust_number, skv_number, project_name FROM projects WHERE project_id=".$pr_id));
	$pr_name .= "  уст ".$pr_arr[0]." скв. ".$pr_arr[1]." ".$pr_arr[2];
	$pr_name = substr($pr_name, 0, 50);
	echo "addGraph($pr_id, $p_id, 0, 10, '$pr_name');\n";
	$res = f_MQuery("SELECT * FROM pipe_axes WHERE pipe_id=".$p_id);
	$num = 0;
	$clrs = "[";
	$lbls = "[";
	while ($arr = f_MFetch($res))
	{
		if ($clrs != "[")
			$clrs .= ",";
		$clrs .= "\"".$arr_clrs[$num]."\"";
		$num++;
		if ($lbls != "[")
			$lbls .= ",";
		$lbls .= "'".$arr[2].", ".$arr[3]."'";
	}
	$clrs .= "]";
	$lbls .= "]";
	echo "setConf($pr_id, $p_id, $num, $lbls, $clrs);\n";
	echo "redraw($pr_id, $p_id);\n";
	echo "refr_all();\n";
	die();
}

if (isset($_GET['tm']) && isset($_GET['pr_id']) && isset($_GET['p_id']))
{
	$last_time = (int)$_GET['tm'];
	$pr_id = (int)$_GET['pr_id'];
	$p_id = (int)$_GET['p_id'];
	$begin_time = f_MValue("SELECT begin_time FROM projects WHERE project_id=$pr_id");
	$res = f_MQuery("SELECT axe_id FROM pipe_axes WHERE pipe_id=".$p_id);
	if (!f_MNum($res))
		die();
	$axe = Array();
	$ax_num = 0;
	$all_datas = Array();
	while ($arr = f_MFetch($res))
	{
		$axe[$ax_num] = (int)$arr[0];
		$all_datas[$axe[$ax_num]] = Array();
		$ax_num++;
	}
	
	$a = true;
	$d_num = 0;
	while ($a && $d_num<100)
	{
		$res = f_MQuery("SELECT * FROM `project_data` WHERE project_id=$pr_id AND pipe_id=$p_id AND time>$last_time group by axe_id");
		if (!f_MNum($res))
			$a = false;
		else
		{
			if ($d_num == 5*((int)$d_num/5))
				while ($arr = f_MFetch($res))
				{
					$t_data = Array();
					$t_data[0] = (int)$arr[2]+4*3600;
					$t_data[1] = (float)$arr[4];
					$ax = (int)$arr[3];
					$all_datas[$ax][$d_num/5] = $t_data;
					$last_time = (int)$arr[2];
				}
			else
			{
				$arr = f_MFetch($res);
				$last_time = (int)$arr[2];
			}
			$d_num++;
		}
		
	}

	$a_datas = "[";
	for ($i=0;$i<$ax_num;$i++)
	{
		$a_datas .= "[";
		for ($j=0;$j<$d_num/5;$j++)
		{
			$a_datas .= "[";
			$a_datas .= "'".date( "Y/m/d H:i:s", $begin_time+$all_datas[$i][$j][0] )."'".",".sprintf("%.3f", $all_datas[$i][$j][1]);
			
			$a_datas .= "]";
			if ($j != $d_num/5-1)
				$a_datas .= ",";
		}
		$a_datas .= "]";
		if ($i != $ax_num-1)
			$a_datas .= ",";
	}
	$a_datas .= "]";
	echo "addDatas($pr_id, $p_id, ".$a_datas.");\n";
	if (f_MValue("SELECT COUNT(*) FROM `project_data` WHERE project_id=$pr_id AND pipe_id=$p_id AND time>$last_time"))
		echo "redraw($pr_id, $p_id, true);\n";
	else
		echo "redraw($pr_id, $p_id, false);\n";
	die();
}

if (isset($_GET['ownerName'])&&isset($_GET['owner_id_up']))
{
	f_MQuery("INSERT INTO owners (up_owner_id, name) VALUES (".$_GET['owner_id_up'].", '".$_GET['ownerName']."')");
	die();
}
?>