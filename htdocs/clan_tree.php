<?

// ����� Reincarnation

if( !$mid_php ) die( );

$reslt = f_MValue("SELECT tree_active FROM clans WHERE clan_id=".$clan_id);

if ($reslt >= 0 ) // ���� ����� ������������, �� ���� ������
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
		$player->syst("�� ������ ����� �����. ������ ������������� ���, � ��� �������, ��� ��� ����� ��� ���� � �����.");
		$player->tree_can = 1;

		$reslt = $reslt + $pv;
		f_MQuery("UPDATE clans SET tree_active = tree_active + $pv WHERE clan_id =  ".$clan_id);
	}
	f_MQuery("UNLOCK TABLES");
}

echo "<b>����� �����</b> - <a href=game.php?order=main>�����</a><br>";

$canbuild = ( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_BUILD ) );
$maybuild = false;

$needtext = "����� �����. ";

$content = "<table><tr><td width=140 align=center><script>FLUl();</script><img src='/images/camp/c/14.png'><script>FLL();</script></td><td width=500><script>FLUl();</script>����� ���� <b>����� �����</b>. ��� ������ ������ ������, ���������� ��� ������ �������� � ������ �� ������� ����� ������. ���� ����� �����, ����� � �����. ���� ����� �������, ���� � �����. ��� ����� ����� ��������� � ����� �����, ��� ������� ������� �� �������� �� ������������ ������������. ������� <b>����� �����</b>, � � ��� � ������� �����, ����� �������� �� ������. � ����� ���� ������ ���������� ������ ����� ��������.<script>FLL();</script></td></tr>";

$restreelvl = f_MValue("SELECT level FROM clan_buildings WHERE building_id=14 AND clan_id=".$clan_id);

$content .= "<tr><td align=center><script>FLUl();</script><b><center>����� �����<br>{$restreelvl} �������";
$content .= "</center></b><script>FLL();</script></td><td>";


$numG = 0;
$numB = 0;
$numR = 0;
$numN = 0;

$dl = f_MValue("SELECT deadline FROM clan_tree_uping WHERE clan_id=".$clan_id);
if (!$dl) $dl=0;

$min_tree_active = 10; // ���� ����� ����� ���� �����, �� ����� ����� � ������ �������

if ($restreelvl > 1 && $reslt > $min_tree_active) //������ �������
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
			$player->AddEffect( 10, 0, '����� ���', '����� �������� ��� ������� ��� ������� ����� �����', 'tree.png', $eff, time() + 7 * 24 * 60 * 60 );
		$num_effs = f_MValue("SELECT COUNT(id) FROM player_effects WHERE player_id=".$player->player_id." AND effect_id=10");
		for($i = 0; $i < $num_effs - $restreelvl; $i++)
			$player->RemoveEffect(10, false);
		$player->syst('������� ������� � ���� ����� �����, �� ������� ����������� ������ ��������. �� ��������� ����� ������.');
	}
}

if( isset( $_GET['fast'] ) )
{
	// ��������� �� �������
	$fst = (int)$_GET['fast'];
	if ($fst > 0)
	if( $dl <= time()+60 )
	{
		$player->syst( '��� ����� �� ���������� ������ ����� �����, ���� ��� ����� ������� ����� ����������.' );
	}
	else
	{
		// ������� ��������
		if( $player->SpendUMoney( $fst ) )
		{
			$dl = $dl - 3600 * $fst;
			f_MQuery("LOCK TABLE clan_tree_uping WRITE");
			f_MQuery( "UPDATE clan_tree_uping SET deadline = $dl WHERE clan_id = $clan_id" );
			f_MQuery("UNLOCK TABLES");
			
			$buildPhrase = array( '���� �������� ����� ���������!', '<a href="/forum.php?thread=6141" target="_blank">�������� � ��������, �������� ����������� ��� ���� �����!</a>' );
			$player->syst( '����� �������� ���� ��� ����� � ������ �����, ��������� ������ ��������� <b>'.$player->login.'</b>. '.$buildPhrase[mt_rand( 0, 1 )] );
			$player->AddToLogPost(-1, -$fst, 1004, $player->clan_id, 1);
			
			$Rein = new Player( 6825 );
			$Rein->syst2( '�������� <a href="/player_info.php?nick='.$player->login.'" target="_blank"><b>'.$player->login.'</b></a> ������� ��������� ����� ����� �� <b>'.$fst.' ���</b>' );
	
		}
		else
		{
			$player->syst2( '� ���� ��������� �������� ��� ���������.' );	
		}
	}
}

if ($dl == 0 && isset($HTTP_GET_VARS['upfrom'])) //������ �� ��������� ��� ��������� �����
{
	if ($reslt == -2 && $canbuild) //������ ���������, �� �������
	{
		if (!getTreeUping($clan_id, -2)) //in clan.php
		{
			$tm = time()+1*3600;
			$dl = $tm;
			f_MQuery("INSERT INTO clan_tree_uping (clan_id, deadline) VALUES (".$clan_id.", ".$tm.")");
			orderBroadcast($clan_id, "�����������! ��� ����� ������ ��� ����������� ��� ����� ����� �����!");
			$maybuild = true;
			die( "<script>location.href='game.php?order=tree';</script>" );
		}
		else $player->syst("������������ ������ ��� �����.");
	}
	elseif ($reslt == -1 && $canbuild) // ������ ���������, �� ����
	{
		if (!getTreeUping($clan_id, -1)) //in clan.php
		{
			$tm = time()+1*3600;
			$dl = $tm;
			f_MQuery("INSERT INTO clan_tree_uping (clan_id, deadline) VALUES (".$clan_id.", ".$tm.")");
			orderBroadcast($clan_id, "�����������! ��� ����� ������ ��� ����������� ��� ����� ����� �����!");
			$maybuild = true;
			die( "<script>location.href='game.php?order=tree';</script>" );
		}
		else $player->syst("������������ ����� ��� �����.");
	
	}
	elseif ($reslt <= $min_tree_active) // ����� ����� ������� �� ���������. ������ �����
	{
		$player->syst("����� ����� �������. ��� ������ �������� � ����� ���������.");
	}
	elseif ($restreelvl < $tree_max_lvl && $canbuild) // ����� ���� � ��� ������� ������ $tree_max_lvl-�
	{
		if (!getTreeUping($clan_id, $restreelvl)) //in clan.php
		{
			$tm = time()+500*3600;
			$dl = $tm;
			f_MQuery("INSERT INTO clan_tree_uping (clan_id, deadline) VALUES (".$clan_id.", ".$tm.")");
			orderBroadcast($clan_id, "�����������! ��� ����� ������ ��� ����������� ��� ���������� ����� �����!");
			$maybuild = true;
			die( "<script>location.href='game.php?order=tree';</script>" );
		}
		else $player->syst("������������ ����� ��� ����� ������ ��� ������.");
	}
	else // ������� ����� ����� ������ ���������
	{
		$player->syst("���� ��������� ����� ����� �� ���� �� �������.");
	}
}

if ($dl > 0) //���� ��� ��������
{
	$content .= "<script>FLUl();</script>";
	include_js( 'js/timer.js' );
	$left = $dl - time( ) + 5;
	if ($left <= 0) $content .= "<script>document.write( InsertTimer( 60, '��� �������: <b>', '</b>', 1, 'location.href=\"game.php?order=tree\";' ) );</script>";
	else
	{
		if ($reslt < 0)
		{
			$str = "����";
			$content .= "����� ����� ��������. ������ � ���� ����� �������� ����.<br><br>";
		}
		else
		{
			$str = "����������";
			$content .= "����� ����� ������ � ������ ������ ������. �� ������ �������� ��� ���� � ������� ������ ����� ��������.<br><br>";
		}
		
		$content .= "<b>���� ".$str." ����� �����</b><br><script>document.write( InsertTimer( $left, '�� ��������� ��������: <b>', '</b>', 1, 'location.href=\"game.php?order=tree\";' ) );</script>";
		$content .= "<br /><br />";
		$content .= "�� <b><img src='/images/umoney.gif' /> 1</b> ����� <a href='#' onclick='if( confirm( \"�������� 1 ��� ���������� �������?\" ) ) location.href=\"game.php?order=tree&fast=1\";'>������</a> <b>1 ��� ���������� �������</b>";
		$content .= "<br>�� <b><img src='/images/umoney.gif' /> 10</b> ����� <a href='#' onclick='if( confirm( \"�������� 10 ����� ���������� �������?\" ) ) location.href=\"game.php?order=tree&fast=10\";'>������</a> <b>10 ����� ���������� �������</b>";
		$content .= "<br>�� <b><img src='/images/umoney.gif' /> 100</b> ����� <a href='#' onclick='if( confirm( \"�������� 100 ����� ���������� �������?\" ) ) location.href=\"game.php?order=tree&fast=100\";'>������</a> <b>100 ����� ���������� �������</b>";
	}
}

elseif ($reslt == -2) //����� ������� �� ���������
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
	$content .= "����� ����� ��������. ������ � ���� ����� �������� ����.<br><br>";
	$content .= "�������� ������� �� 500 ������ ������ ������. ����� �� ��������: <b><font color=darkblue>����� ���������</font></b> ������ �������, <b><font color=darkred>����� �������</font></b> ��������� ���������� �����, � <b><font color=darkgreen>������ ��-������</font></b> ����� � ����� ���������. �������� ������ ���������� ������, � ������� ���� �����.<br><br>";
	$content .= "<small>������� ������ ������ ���������� �� ������� ����� ������.</small><br><br>";
	$content .= "<a href='help.php?id=1010&item_id=74594' target=_blank><img src='/images/items/Cube/list.png'><b><font color=darkgreen>���� ��-������</font></b>:</a>&nbsp;<b>".$numG."/500</b><br>";
	$content .= "<a href='help.php?id=1010&item_id=74595' target=_blank><img src='/images/items/Cube/drop.png'><b><font color=darkblue>����� ���������</font></b>:</a>&nbsp;<b>".$numB."/500</b><br>";
	$content .= "<a href='help.php?id=1010&item_id=74596' target=_blank><img src='/images/items/Cube/spark.png'><b><font color=darkred>����� �������</font></b>:</a>&nbsp;<b>".$numR."/500</b><br>";
}
elseif ($reslt == -1) //����� ���� �� ���������
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
	$content .= "����� ����� ��������. ������ � ���� ����� �������� ����.<br><br>";
	$content .= "�������� ������ ���������� �����, � ������� ���� �����.<br><br>";
	$content .= "<small>���������� ���� ������ ���������� �� ������� �����</small><br><br>";
	$content .= "<a href='help.php?id=1010&item_id=73180' target=_blank><img src='/images/items/Cube/cube_ground.png'>���������� ��� �������:</a>&nbsp;<b>".$numG."/1</b><br>";
	$content .= "<a href='help.php?id=1010&item_id=73179' target=_blank><img src='/images/items/Cube/cube_water.png'>���������� ��� ����:</a>&nbsp;<b>".$numB."/1</b><br>";
	$content .= "<a href='help.php?id=1010&item_id=73178' target=_blank><img src='/images/items/Cube/cube_fire.png'>���������� ��� ����:</a>&nbsp;<b>".$numR."/1</b><br>";
	$content .= "<a href='help.php?id=1010&item_id=73181' target=_blank><img src='/images/items/Cube/cube_neutral.png'>��� ����������� �����:</a>&nbsp;<b>".$numN."/1</b><br>";

}
elseif ($reslt <= $min_tree_active) //������ ������� �� ���������
{
	$content .= "<script>FLUl();</script>";
	$content .= "����� ����� ������� �� ���������. ������� ������ ������ ����� ������ ���� �������� � ���� ��� ����������� ����� �����.";
}
else //������ ������������ � ���� �����-�����
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
		$content .= "����� ����� ������ � ������ ������ ������. �� ������ �������� ��� ���� � ������� ������ ����� ��������.<br>";
		$content .= "�������� ������� ���������� ���� ������ ������, ��������� ��������� ����� �����. ���� ����������� ������ ����� ������ � �������� ����� ����������� �� ������ � ����� �����.<br>";
		$content .= "<small>���������� ���� ������ ���������� �� ������� �����</small><br><br>";
		$content .= "<a href='help.php?id=1010&item_id=73180' target=_blank><img src='/images/items/Cube/cube_ground.png'>���������� ��� �������:</a>&nbsp;<b>".$numG."/$needtreetoup</b><br>";
		$content .= "<a href='help.php?id=1010&item_id=73179' target=_blank><img src='/images/items/Cube/cube_water.png'>���������� ��� ����:</a>&nbsp;<b>".$numB."/$needtreetoup</b><br>";
		$content .= "<a href='help.php?id=1010&item_id=73178' target=_blank><img src='/images/items/Cube/cube_fire.png'>���������� ��� ����:</a>&nbsp;<b>".$numR."/$needtreetoup</b><br>";
		$content .= "<a href='help.php?id=1010&item_id=73181' target=_blank><img src='/images/items/Cube/cube_neutral.png'>��� ����������� �����:</a>&nbsp;<b>".$numN."/$needtreetoup</b><br>";
		$content .= "����� ������: <b>".$numGlory."/$needglorytoup</b><br>";
	}
	else
	{
		$content .= "<script>FLUl();</script>";
		$content .= "����� ����� �������� ������������ ������. ���� �� ��� �������� ����.<br>";
	}
}

if ($dl == 0)
	if ($canbuild && $restreelvl < $tree_max_lvl)
	{
		$content .= "<br>";
		if ($reslt < 0)
		{
			if (!$maybuild) $content .= "������������ �������� ��� ����� ����� �����.";
			else $content .= "<br>�� ������ <b><a href=game.php?order=tree&upfrom=$reslt>������ ����</a></b> ����� �����.";
		}
		else
		{
			if (!$maybuild) $content .= "������������ ����� ��� ����� ������ ��� ���������� ����� �����.";
			else $content .= "<br>�� ������ �������� ������ ����� � <b><a href=game.php?order=tree&upfrom=$reslt>������ ����������</a></b> ����� �����.";
		}
		$content .= "<script>FLL();</script>";
	}
	else $content .= "<script>FLL();</script>";
else $content .= "<script>FLL();</script>";

$content .= "</td></tr>";

if ($reslt >= 0)
{
	$content .= "<tr><td align=center><script>FLUl();</script>";
	$content .= "<center><b>".$reslt.my_word_str( $reslt, " �����", " �����", " ������" )."</center></b>";
	$content .= "<script>FLL();</script></td>";
	$content .= "<td align=center><script>FLUl();</script>";
	$res = f_MQuery("SELECT characters.login, player_clans.trup, player_clans.trup_val, characters.player_id FROM player_clans, characters WHERE player_clans.clan_id=".$clan_id." AND player_clans.player_id=characters.player_id ORDER BY player_clans.trup DESC");
	$content .= "<center><table><tr><td width=200 align=center><b>��������</b></td><td width=100 align=center><b>�����������</b></td><td width=100 align=center><b>����� � ���� ����� �����</b></td></tr>";
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
