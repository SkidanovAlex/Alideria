<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );

f_MConnect( );

include( 'admin_header.php' );

echo "<a href='index.php'>�� �������</a><br><br>";

if (isset($_GET['sl']))
{
	$pan = (int)$_GET['pan'];
	if (!f_MValue("SELECT pandora_id FROM pandora WHERE item_id=-2 AND pandora_id=".$pan))
		echo "<script>alert('��� ������� � ����� ID');</script>";
	else
	{
		$ns=(int)$_GET['ns'];
		$os=(int)$_GET['os'];
		f_MQuery("UPDATE pandora SET schans=$ns WHERE pandora_id=$pan AND schans=".$os);
		echo "<script>alert('��� ������� $pan ����� ������� ��������');</script>";
	}
}

if (isset($_GET['delPan']))
{
	$delPan = (int)$_GET['delPan'];
	if (!f_MValue("SELECT pandora_id FROM pandora WHERE item_id=-2 AND pandora_id=".$delPan))
		echo "<script>alert('��� ������� � ����� ID');</script>";
	else
	{
		f_MQuery("DELETE FROM pandora WHERE pandora_id=".$delPan);
		echo "<script>alert('������� $delPan ������� �������');</script>";
	}
}

if (isset($_GET['addPan']))
{
	$addPan = (int)$_GET['addPan'];
	if (f_MValue("SELECT pandora_id FROM pandora WHERE item_id=-2 AND pandora_id=".$addPan))
		echo "<script>alert('������� � ����� ID=$addPan ��� ����');</script>";
	elseif (f_MValue("SELECT type FROM items WHERE item_id=$addPan") != 23)
		echo "<script>alert('�������� � ����� ID=$addPan �� ����������, ���� ��� ��� �� ����� 23(�������)');</script>";
	else
	{
		f_MQuery("INSERT INTO pandora (pandora_id, item_id) VALUES ($addPan, -2)");
		echo "<script>alert('������� $addPan ������� ���������');</script>";
	}
}

$res = f_MQuery("SELECT p.pandora_id, i.name, i.type FROM pandora AS p, items AS i WHERE p.pandora_id = i.item_id GROUP BY p.pandora_id");
while ($arr = f_MFetch($res))
{
	if ($arr[2]!=23) echo "�� �������� ���������>";
	echo "<a href='admin_pandora.php?pan=".$arr[0]."'>".$arr[1]."</a> > <a href='#' onclick='if (confirm(\"������� ������� {$arr[0]}?\")) location.href=\"admin_pandora.php?delPan={$arr[0]}\"'>������� �������</a><br>";
}

echo "<a href='javascript:addPan();'>�������� �������</a><br>";

echo "<hr>";

if (isset($_GET['add']) && $_GET['add']==1) // add Item
{
	if (isset($_GET['pan']) && isset($_GET['item_id']))
	{
		$pan=(int)$_GET['pan'];
		$item_id=(int)$_GET['item_id'];
		if (!f_MValue("SELECT pandora_id FROM pandora WHERE item_id=-2 AND pandora_id=".$pan))
			echo "<script>alert('������� � ����� ID �� �������');</script>";
		elseif (f_MValue("SELECT item_id FROM pandora WHERE pandora_id=$pan AND item_id=".$item_id))
			echo "<script>alert('����� ������� ��� ���� � ���� �������');</script>";
		elseif (!f_MValue("SELECT name FROM items WHERE item_id=".$item_id))
			echo "<script>alert('������� � ����� ID=$item_id �� ������');</script>";
		elseif ($item_id <= 0)
			echo "<script>alert('����� �������� ������ �������� � ������������� ID');</script>";
		else
		{
			f_MQuery("INSERT INTO pandora (pandora_id, item_id) VALUES ($pan, $item_id)");
			$iname = f_MValue("SELECT name FROM items WHERE item_id=".$item_id);
			echo "<script>alert('� ������� $pan �������� ������� {$iname} ({$item_id}) ����������� 1 � ������ 1');</script>";
		}
	}
}

if (isset($_GET['add']) && $_GET['add']==3) // add Mob
{
	if (isset($_GET['pan']) && isset($_GET['mob_id']))
	{
		$pan=(int)$_GET['pan'];
		$mob_id=(int)$_GET['mob_id'];
		if (!f_MValue("SELECT pandora_id FROM pandora WHERE item_id=-2 AND pandora_id=".$pan))
			echo "<script>alert('������� � ����� ID �� �������');</script>";
		elseif (f_MValue("SELECT num FROM pandora WHERE pandora_id=$pan AND item_id=-3 AND num=".$mob_id))
			echo "<script>alert('������ � ����� ID=$mob_id ��� ���� � ������� $pan');</script>";
		else
		{
			$mob_name = f_MValue("SELECT name FROM mobs WHERE mob_id=".$mob_id);
			if (!$mob_name)
				echo "<script>alert('������ � ����� ID=$mob_id �� ������');</script>";
			else
			{
				f_MQuery("INSERT INTO pandora (pandora_id, item_id, num) VALUES ($pan, -3, $mob_id)");
				echo "<script>alert('� ������� $pan �������� ������ {$mob_name} ({$mob_id}) ������ 1');</script>";
			}
		}
	}
}

if (isset($_GET['add']) && $_GET['add']==4) // add Money
{
	if (isset($_GET['pan']) && isset($_GET['money']))
	{
		$pan = (int)$_GET['pan'];
		$money = (int)$_GET['money'];
		if (!f_MValue("SELECT pandora_id FROM pandora WHERE item_id=-2 AND pandora_id=".$pan))
			echo "<script>alert('������� � ����� ID �� �������');</script>";
		elseif (f_MValue("SELECT num FROM pandora WHERE pandora_id=$pan AND item_id=-4 AND num=".$money))
			echo "<script>alert('����� ���������� ����� ��� ���� � ������� $pan');</script>";
		else
		{
			f_MQuery("INSERT INTO pandora (pandora_id, item_id, num) VALUES ($pan, -4, $money)");
			echo "<script>alert('� ������� $pan ��������� {$money} �������� ������ 1');</script>";
		}
	}
}

if (isset($_GET['del']) && $_GET['del']==1)
{
	if (isset($_GET['pan']) && isset($_GET['item_id']))
	{
		$pan = (int)$_GET['pan'];
		$item_id=(int)$_GET['item_id'];
		if (!f_MValue("SELECT pandora_id FROM pandora WHERE item_id=-2 AND pandora_id=".$pan))
			echo "<script>alert('������� � ����� ID �� �������');</script>";
		elseif (!f_MValue("SELECT item_id FROM pandora WHERE pandora_id=$pan AND item_id!=-2 AND item_id=".$item_id))
			echo "<script>alert('������� � ID={$item_id} �� ������ � ���� �������');</script>";
		else
		{
			f_MQuery("DELETE FROM pandora WHERE pandora_id=$pan AND item_id=$item_id");
			echo "<script>alert('������� $item_id ������� ������ �� ������� $pan ');</script>";
		}
	}
}

if (isset($_GET['del']) && $_GET['del']==3)
{
	if (isset($_GET['pan']) && isset($_GET['mob_id']))
	{
		$pan = (int)$_GET['pan'];
		$mob_id = (int)$_GET['mob_id'];
		if (!f_MValue("SELECT pandora_id FROM pandora WHERE item_id=-2 AND pandora_id=".$pan))
			echo "<script>alert('������� � ����� ID �� �������');</script>";
		elseif (!f_MValue("SELECT num FROM pandora WHERE pandora_id=$pan AND item_id=-3 AND num=".$mob_id))
			echo "<script>alert('������ � ID={$mob_id} �� ������ � ���� �������');</script>";
		else
		{
			f_MQuery("DELETE FROM pandora WHERE num=$mob_id AND pandora_id=$pan AND item_id=-3");
			echo "<script>alert('������ $mob_id ������� ������ �� ������� $pan ');</script>";
		}
	}
}

if (isset($_GET['del']) && $_GET['del']==4)
{
	if (isset($_GET['pan']) && isset($_GET['money']))
	{
		$pan = (int)$_GET['pan'];
		$money = (int)$_GET['money'];
		if (!f_MValue("SELECT pandora_id FROM pandora WHERE item_id=-2 AND pandora_id=".$pan))
			echo "<script>alert('������� � ����� ID �� �������');</script>";
		elseif (!f_MValue("SELECT num FROM pandora WHERE pandora_id=$pan AND item_id=-4 AND num=".$money))
			echo "<script>alert('������ ���������� ��������{$money} �� ������� � ���� �������');</script>";
		else
		{
			f_MQuery("DELETE FROM pandora WHERE num=$money AND pandora_id=$pan AND item_id=-4");
			echo "<script>alert('������� ����������� $money ������� ������� �� ������� $pan ');</script>";
		}
	}
}

if (isset($_GET['edit']) && isset($_GET['val']))
{
	$val=(int)$_GET['val'];
	if (isset($_GET['pan']) && isset($_GET['item_id']))
	{
		$pan = (int)$_GET['pan'];
		$item_id=(int)$_GET['item_id'];
		if (!f_MValue("SELECT pandora_id FROM pandora WHERE item_id=-2 AND pandora_id=".$pan))
			echo "<script>alert('������� � ����� ID �� �������');</script>";
		elseif (!f_MValue("SELECT item_id FROM pandora WHERE pandora_id=$pan AND item_id=".$item_id))
			echo "<script>alert('������� � ID={$item_id} �� ������ � ���� �������');</script>";
		else
		{
			$edit=(int)$_GET['edit'];
			$ok=false;
			if ($edit==1) // schans
			{
				if ($val >= 0)
				{
					$str="SET schans=$val";
					$ok=true;
				}
			}
			elseif($edit==2) // num
			{
				if ($val > 0)
				{
					$str="SET num=$val";
					$ok=true;
				}
			}
			if (!$ok)
				echo "<script>alert('������� �������� ��������!!!');</script>";
			else
			{
				f_MQuery("UPDATE pandora ".$str." WHERE pandora_id=$pan AND item_id=$item_id");
				echo "<script>alert('��������� ������� ���������');</script>";
			}
		}
	}
}

if (isset($_GET['editMob']) && isset($_GET['pan']) && isset($_GET['mob_id']) && isset($_GET['val']))
{
	$val = (int)$_GET['val'];
	$pan = (int)$_GET['pan'];
	$mob_id = (int)$_GET['mob_id'];
	if (!f_MValue("SELECT pandora_id FROM pandora WHERE item_id=-2 AND pandora_id=".$pan))
		echo "<script>alert('������� � ����� ID �� �������');</script>";
	elseif (!f_MValue("SELECT num FROM pandora WHERE pandora_id=$pan AND num=$mob_id AND item_id=-3"))
		echo "<script>alert('������ � ID={$mob_id} �� ������ � ���� �������');</script>";
	else
	{
		$ok = false;
		if ($_GET['editMob'] == 1)
		{
			if ($val >= 0)
			{
				$str="SET schans=$val";
				$ok=true;
			}
		}
		elseif ($_GET['editMob'] == 2)
		{
			$Mname = f_MValue("SELECT name FROM mobs WHERE mob_id=".$mob_id);
			if (!$Mname)
				echo "<script>alert('������ � ����� ID=$mob_id �� ������');</script>";
			elseif (f_MValue("SELECT num FROM pandora WHERE pandora_id=$pan AND num=$val AND item_id=-3"))
				echo "<script>alert('������ � ����� ID=$val ��� ���� � ���� �������');</script>";
			else
			{
				$str = "SET num=".$val;
				$ok = true;
			}
		}
		if ($ok)
		{
			f_MQuery("UPDATE pandora ".$str." WHERE item_id=-3 AND pandora_id=$pan AND num=".$mob_id);
			echo "<script>alert('��������� ������� ���������');</script>";
		}
	}
}

if (isset($_GET['editMoney']) && isset($_GET['pan']) && isset($_GET['money']) && isset($_GET['val']))
{
	$val = (int)$_GET['val'];
	$pan = (int)$_GET['pan'];
	$money = (int)$_GET['money'];
	if (!f_MValue("SELECT pandora_id FROM pandora WHERE item_id=-2 AND pandora_id=".$pan))
		echo "<script>alert('������� � ����� ID �� �������');</script>";
	elseif (!f_MValue("SELECT num FROM pandora WHERE pandora_id=$pan AND num=$money AND item_id=-4"))
		echo "<script>alert('������� ����������� {$money} �� ������� � ���� �������');</script>";
	else
	{
		$ok = false;
		if ($_GET['editMoney'] == 1)
		{
			if ($val >= 0)
			{
				$str="SET schans=$val";
				$ok=true;
			}
		}
		elseif ($_GET['editMoney'] == 2)
		{
			if (f_MValue("SELECT num FROM pandora WHERE pandora_id=$pan AND num=$val AND item_id=-4"))
				echo "<script>alert('������� � ���������� {$val} ��� ���� � ���� �������');</script>";
			else
			{
				$str = "SET num=".$val;
				$ok = true;
			}
		}
		if ($ok)
		{
			f_MQuery("UPDATE pandora ".$str." WHERE item_id=-4 AND pandora_id=$pan AND num=".$money);
			echo "<script>alert('��������� ������� ���������');</script>";
		}
	}
}

if (isset($_GET['pan']))
{
	$pan=(int)$_GET['pan'];
	if (!$p_n = f_MValue("SELECT i.name FROM items as i, pandora as p WHERE p.pandora_id=i.item_id AND p.pandora_id=".$pan))
		die("�������� ID �������");
	else
	{
		echo "������ ID �������<br>";
		echo "������� ��������� � ������� � ������ <b>".$p_n."</b><br><br>";
		$allschans = f_MValue("SELECT SUM(schans) FROM pandora WHERE pandora_id=".$pan);
		$allPrice = f_MValue("SELECT SUM(i.price*p.schans/{$allschans}) FROM items as i, pandora as p WHERE p.item_id>0 AND p.item_id=i.item_id AND p.pandora_id=".$pan);
		echo "������� ���� �������� ������(������ ��������)=".$allPrice."<br>";
		echo "<table><tr>";
		echo "<td><a href='javascript:addItemInPandora({$pan});'>�������� �������</a>&nbsp;</td>";
		echo "<td><a href='javascript:addMobInPandora({$pan});'>�������� �������</a>&nbsp;</td>";
		echo "<td><a href='javascript:addMoneyInPandora({$pan});'>�������� �������</a>&nbsp;</td>";
		echo "<td><a href='javascript:setSchansAll({$pan});'>������� ����� �������</a>&nbsp;</td>";
		echo "</tr></table><br>";

		echo "<table border=1><tr><td>��� �������� (ID)</td><td>���.���� (����*����)</td><td>����������</td><td>���� ��������� (%)</td><td>�������?</td><td>������� ����?</td><td>������� ����������?</td></tr>";
		
		$res = f_MQuery("SELECT * FROM pandora WHERE pandora_id={$pan} AND item_id=-2");
		$arr = f_MFetch($res);
		echo "<tr><td>������ (-2)</td><td></td><td>{$arr[2]}</td><td align=right>{$arr[3]} �� {$allschans} (".(((int)(100000*100*$arr[3]/$allschans))/100000)."%)</td><td>������</td><td><a href='javascript:editSchans({$pan}, -2)'>������� ����</a></td><td><a href='javascript:editNum({$pan}, -2)'>������� ����������</a></td></tr>";

		// �������
		echo "<tr><td colspan=7 align=center><b>�������</b></td></tr>";
		echo "<tr><td>��� ������� (ID)</td><td colspan=2>ID ����</td><td>���� ��������� (%)</td><td colspan=3 align=center><b>��������</b></td></tr>";
		$res = f_MQuery("SELECT m.name, p.* FROM pandora as p, mobs as m WHERE p.num=m.mob_id AND p.item_id=-3 AND p.pandora_id=".$pan);
		while ($arr = f_MFetch($res))
		{
			echo "<tr><td>{$arr[0]} ({$arr[3]})</td><td colspan=2>{$arr[3]}</td><td>{$arr[4]} �� {$allschans} (".(((int)(100000*100*$arr[4]/$allschans))/100000)."%)</td><td><a href='javascript:delMob({$pan}, {$arr[3]});'>�������</a></td><td><a href='javascript:editSchansMob({$pan}, {$arr[3]})'>������� ����</a></td><td><a href='javascript:editMob({$pan}, {$arr[3]})'>�������� �������</a></td></tr>";
		}
		
		// �������
		echo "<tr><td colspan=7 align=center><b>�������</b></td></tr>";
		echo "<tr><td colspan=2>����������*����</td><td>����������</td><td>���� ��������� (%)</td><td colspan=3 align=center><b>��������</b></td></tr>";
		$res = f_MQuery("SELECT * FROM pandora as p WHERE p.item_id=-4 AND p.pandora_id=".$pan);
		while ($arr = f_MFetch($res))
		{
			echo "<tr><td colspan=2>".(((int)(100000*$arr[2]*$arr[3]/$allschans))/100000)."</td><td>{$arr[2]}</td><td>{$arr[3]} �� {$allschans} (".(((int)(100000*100*$arr[3]/$allschans))/100000)."%)</td><td><a href='javascript:delMoney({$pan}, {$arr[2]});'>�������</a></td><td><a href='javascript:editSchansMoney({$pan}, {$arr[2]})'>������� ����</a></td><td><a href='javascript:editMoney({$pan}, {$arr[2]})'>�������� ����������</a></td></tr>";
		}

		// ��������
		echo "<tr><td colspan=7 align=center><b>��������</b></td></tr>";
		echo "<tr><td>��� �������� (ID)</td><td>���.���� (����*����)</td><td>����������</td><td>���� ��������� (%)</td><td colspan=3 align=center><b>��������</b></td></tr>";
		$res = f_MQuery("SELECT i.name, p.*, i.price FROM items as i, pandora as p WHERE p.item_id>0 AND p.pandora_id=".$pan." AND p.item_id=i.item_id ORDER BY p.item_id");
		while ($arr = f_MFetch($res))
		{
			echo "<tr><td>{$arr[0]} ({$arr[2]})</td><td>{$arr[5]} (".(((int)(100000*$arr[5]*$arr[4]/$allschans))/100000).")</td><td>{$arr[3]}</td><td align=right>{$arr[4]} �� {$allschans} (".(((int)(100000*100*$arr[4]/$allschans))/100000)."%)</td><td><a href='javascript:delItem({$pan}, {$arr[2]});'>�������</a></td><td><a href='javascript:editSchans({$pan}, {$arr[2]})'>������� ����</a></td><td><a href='javascript:editNum({$pan}, {$arr[2]})'>������� ����������</a></td></tr>";
		}
	}
}

f_MClose( );

?>

<script>

function addItemInPandora(pan)
{
	iid = prompt('�� ������ �������� ������� � ������� '+pan + '?\n������� ID ��������: ', '' );
	item_id=parseInt(iid);
	if (iid && (isNaN(item_id) || iid!=item_id))
		return alert('�� �������� ����� ������');
	document.location='admin_pandora.php?add=1&pan='+pan+'&item_id='+item_id;
}

function delItem(pan, item_id)
{
	if (confirm('�� ������ ������� ������� '+item_id+' �� ������� '+pan+'?'))
		document.location='admin_pandora.php?del=1&pan='+pan+'&item_id='+item_id;
}

function delMob(pan, mob_id)
{
	if (confirm('�� ������ ������� ������� '+mob_id+' �� ������� '+pan+'?'))
		document.location='admin_pandora.php?del=3&pan='+pan+'&mob_id='+mob_id;
}

function delMoney(pan, money)
{
	if (confirm('�� ������ ������� ������� ����������� '+money+' �� ������� '+pan+'?'))
		document.location='admin_pandora.php?del=4&pan='+pan+'&money='+money;
}

function editSchans(pan, item_id)
{
	schans = prompt('�� ������ �������� ����?\n������� ����� ����: ', '' );
	sch=parseInt(schans);
	if (schans && (isNaN(sch) || schans!=sch))
		return alert('�� �������� ����� ������');
	document.location='admin_pandora.php?edit=1&pan='+pan+'&item_id='+item_id+'&val='+sch;
}

function editSchansMob(pan, mob_id)
{
	schans = prompt('�� ������ �������� ����?\n������� ����� ����: ', '' );
	sch=parseInt(schans);
	if (schans && (isNaN(sch) || schans!=sch))
		return alert('�� �������� ����� ������');
	document.location='admin_pandora.php?editMob=1&pan='+pan+'&mob_id='+mob_id+'&val='+sch;
}

function editSchansMoney(pan, money)
{
	schans = prompt('�� ������ �������� ����?\n������� ����� ����: ', '' );
	sch=parseInt(schans);
	if (schans && (isNaN(sch) || schans!=sch))
		return alert('�� �������� ����� ������');
	document.location='admin_pandora.php?editMoney=1&pan='+pan+'&money='+money+'&val='+sch;
}

function editNum(pan, item_id)
{
	num = prompt('�� ������ �������� ����������?\n������� ����� ����������: ', '' );
	nm=parseInt(num);
	if (num && (isNaN(nm) || num!=nm))
		return alert('�� �������� ����� ������');
	document.location='admin_pandora.php?edit=2&pan='+pan+'&item_id='+item_id+'&val='+nm;
}

function editMob(pan, mob_id)
{
	num = prompt('�� ������ �������� �������?\n������� ID ������ �������: ', '' );
	nm=parseInt(num);
	if (num && (isNaN(nm) || num!=nm))
		return alert('�� �������� ����� ������');
	document.location='admin_pandora.php?editMob=2&pan='+pan+'&mob_id='+mob_id+'&val='+nm;
}

function editMoney(pan, money)
{
	num = prompt('�� ������ �������� ���������� ��������?\n������� ����� ���������� ��������: ', '' );
	nm=parseInt(num);
	if (num && (isNaN(nm) || num!=nm) || nm<=0)
		return alert('�� �������� ����� ������ ���� ������-����� ����');
	document.location='admin_pandora.php?editMoney=2&pan='+pan+'&money='+money+'&val='+nm;
}

function addPan()
{
	pan=prompt('�������� ����� �������?\n������� ID ��������:', '');
	pn=parseInt(pan);
	if (pan && (isNaN(pn) || pan!=pn))
		return alert('�� �������� ����� ������');
	document.location='admin_pandora.php?addPan='+pn;
}

function setSchansAll(pan)
{
	oldSch = prompt('������� ����, ������� ����� ������:', '');
	os = parseInt(oldSch);
	if (oldSch && (isNaN(os) || os!=oldSch))
		return alert('�� �������� ����� ������');
	newSch = prompt('������� ����� ����:', '');
	ns = parseInt(newSch);
	if (newSch && (isNaN(ns) || ns!=newSch))
		return alert('�� �������� ����� ������');
	document.location = 'admin_pandora.php?sl=1&pan='+pan+'&ns='+ns+'&os='+os;
}

function addMobInPandora(pan)
{
	mid = prompt('�� ������ �������� ������� � ������� '+pan + '?\n������� ID �������: ', '' );
	mob_id=parseInt(mid);
	if (mid && (isNaN(mob_id) || mid!=mob_id))
		return alert('�� �������� ����� ������');
	document.location='admin_pandora.php?add=3&pan='+pan+'&mob_id='+mob_id;
}

function addMoneyInPandora(pan)
{
	mid = prompt('�� ������ �������� ������� � ������� '+pan + '?\n������� ����������: ', '' );
	mob_id=parseInt(mid);
	if (mid && (isNaN(mob_id) || mid!=mob_id) || mid<=0)
		return alert('�� �������� ����� ������ ���� ������-����� ����');
	document.location='admin_pandora.php?add=4&pan='+pan+'&money='+mob_id;
}

</script>