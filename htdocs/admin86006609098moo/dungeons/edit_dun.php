<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../../style2.css" rel="stylesheet" type="text/css">

<?
include_once( '../../functions.php' );
include_once( '../../arrays.php' );
include_once( '../../player.php' );
include_js('tooltips.php');
include_js('../../js/ajax.js');

f_MConnect( );

include( '../admin_header.php' );
?>
<script>
var dun_id=0;
var cells = new Array();
var cell_num = 0;
var cur_cell = -1;
var click_st;
var click_st_arr = new Array();
click_st_arr[0] = "Текущая клетка";
click_st_arr[1] = "Выберите переход вверх";
click_st_arr[2] = "Выберите переход влево";
click_st_arr[3] = "Выберите переход вправо";
click_st_arr[4] = "Выберите переход вниз";

function expand_pe( a, b )
{
	if( document.getElementById( 'd' + a ).style.display == "none" )
	{
		document.getElementById( 'd' + a ).style.display = "";
		document.getElementById( 'i' + a ).src = "../../images/e_minus.gif";
		if (b)
		{
			document.getElementById('d'+a).innerHTML = 'Загрузка...';
			var y = cur_cell % 100;
			var x = Math.round(cur_cell / 100);
			query('dun_ref.php?'+b+'=0', dun_id+'|'+x+'|'+y);
		}
	}
	else
	{
		document.getElementById( 'd' + a ).style.display = "none";
		document.getElementById( 'i' + a ).src = "../../images/e_plus.gif";
	}
}

function ch_img_folder()
{
	var y = cur_cell % 100;
	var x = Math.round(cur_cell / 100);
	var ni = document.getElementById('img').value;
	document.getElementById('dimgs').innerHTML = 'Загрузка...';
	query('dun_ref.php?get_images='+ni, dun_id+'|'+x+'|'+y);
}

function safeTooltip( e )
{
	var x,y;
	var _1 = this.id.indexOf('_');
	var _2 = this.id.indexOf('_', _1+1)
	x = this.id.substr(this.id.indexOf('_')+1, _2-_1-1);
	y = this.id.substr(_2+1);
	var c = x*100+y*1;
	if (cells[c])
		showTooltip(e, cells[c].nm+'<br>'+x+'|'+y);
	else
		showTooltip(e, 'Пусто...'+'<br>'+x+'|'+y);
}

function _(id)
{
	return document.getElementById(id);
}

function addCell(id, nm, img, up, right, down, left)
{
	if (!cells[id])
	{
		cells[id] = new Object();
		cell_num++;
	}
	cells[id].cell_id = id;
	cells[id].nm = nm;
	cells[id].img = img;
	cells[id].up = up;
	cells[id].right = right;
	cells[id].down = down;
	cells[id].left = left;
}

function updateNameCell(id, nm)
{
	if (cells[id])
		cells[id].nm = nm;
	else
	{
		addCell(id, nm, '', -1, -1, -1, -1);
		_('cell_'+Math.round(id/100)+'_'+(id%100)).style.cursor = 'pointer';
	}
}

function setCellColor(id, clr, a)
{
	if (id==-1) return 0;
	var y = id % 100;
	var x = Math.round(id / 100);
	if (a != '&nbsp;')
		a = '<b>'+a+'</b>';
	if (cells[id])
		_('cell_'+x+'_'+y).style.backgroundColor = clr;
	else
		_('cell_'+x+'_'+y).style.backgroundColor = '';
	_('cell_'+x+'_'+y).innerHTML = a;
	return 1;
}

function showDunActions(x, y, st, _1, _2, _3, _4)
{
	var ret = '';
	var cell = x*100 + y;
	if (cur_cell!=-1 && cur_cell!=cell)
	{
		setCellColor(cur_cell, 'blue', '&nbsp;');
		if (cells[cur_cell])
		{
			setCellColor(cells[cur_cell].up, 'blue', '&nbsp;');
			setCellColor(cells[cur_cell].right, 'blue', '&nbsp;');
			setCellColor(cells[cur_cell].down, 'blue', '&nbsp;');
			setCellColor(cells[cur_cell].left, 'blue', '&nbsp;');
		}
	}
	_('cell_'+x+'_'+y).style.backgroundColor = 'red';
	_('cell_'+x+'_'+y).innerHTML = '&nbsp;';
	if (cells[cell])
	{
		setCellColor(cells[cell].up, 'green', '^');
		setCellColor(cells[cell].right, 'green', '>');
		setCellColor(cells[cell].down, 'green', 'v');
		setCellColor(cells[cell].left, 'green', '<');
	}
	cur_cell=cell;
	var nm = '';
	if (cells[cell])
		nm=cells[cell].nm;
	ret += '<table width=450>';
	ret += '<tr><td>Координаты поля:</td><td>' + x + '|' + y + '</td>';
	if (cells[cur_cell])
		ret += '<td><button onclick="delCell('+ x +', '+ y +');">Удалить</button></td></tr>';
	ret += '<tr><td>Название поля:</td><td><input type="text" id="nm_cell" name="nm_cell" value="'+ nm +'"></td><td><button onclick="nameSet('+ x +', '+ y +');">Ок</button></td></tr>';
	
	ret += '<tr><td colspan=3>'+oneCell(cur_cell)+'</td></tr>';
	
	ret += '<tr><td colspan=3>'+ShowAdds()+'</td></tr>';
	
	ret += '</table>';
	_('dun_actions').innerHTML = ret;
	_('dun_load_st').innerHTML = 'Ok';
}

function ShowAdds()
{
	if (!cells[cur_cell])
		return '';
	var ret = "";
	ret += selectImage();
	ret += selectItems();
	ret += selectMobs();
	ret += selectActions();
	return ret;
}

function selectMobs()
{
	var ret ="";
	ret += "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('mobs', 'get_mobs')\" id=imobs src='../../images/e_plus.gif'>&nbsp;<b>Монстры в клетке</b><br>";
	ret += "<div id=dmobs style='display:none'>&nbsp;</div><br>";
	return ret;
}

function selectItems()
{
	var ret = "";
	ret += "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('itms', 'get_items')\" id=iitms src='../../images/e_plus.gif'>&nbsp;<b>Предметы в клетке</b><br>";
	ret += "<div id=ditms style='display:none'>&nbsp;</div><br>";
	return ret;
}

function selectActions()
{
	var ret = "";
	ret += "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('acts', 'get_action')\" id=iacts src='../../images/e_plus.gif'>&nbsp;<b>Действия</b><br>";
	ret += "<div id=dacts style='display:none'>&nbsp;</div><br>";
	return ret;
}

function addItem(item_id, num)
{
	var y = cur_cell % 100;
	var x = Math.round(cur_cell / 100);
	if (item_id==0 && num==0)
	{
		item_id = document.getElementById('itm_id').value;
		num = document.getElementById('itm_num').value;
	}
	query('dun_ref.php?item_id='+item_id+'&number='+num, dun_id + '|' + x + '|' + y);
}

function setAction()
{
	var y = cur_cell % 100;
	var x = Math.round(cur_cell / 100);
	action = document.getElementById('act').value;
	descr = document.getElementById('descr').value;
	query('dun_ref.php?set_action='+action+'&descr='+descr, dun_id + '|' + x + '|' + y);
}

function addMob(mob_id, num)
{
	var y = cur_cell % 100;
	var x = Math.round(cur_cell / 100);
	if (mob_id==0)
	{
		mob_id = document.getElementById('mb_id').value;
		num = 1;
	}
	query('dun_ref.php?mob_id='+mob_id+'&number='+num, dun_id + '|' + x + '|' + y);
}

function selectImage()
{
	var ret = "";
	ret += "<img width=11 height=11 style='cursor:pointer' onclick=\"expand_pe('imgs', 'get_images')\" id=iimgs src='../../images/e_plus.gif'>&nbsp;<b>Картинка тайла</b><br>";
	ret += "<div id=dimgs style='display:none'>&nbsp;</div><br>";
	return ret;
}

function setImg(img)
{
	var y = cur_cell % 100;
	var x = Math.round(cur_cell / 100);
	query('dun_ref.php?img='+img, dun_id + '|' + x + '|' + y);
}

function delCell(x, y)
{
	query('dun_ref.php?del=1', dun_id + '|' + x + '|' + y);
}

function oneCell(id)
{
	var ret = "";
	var c = new Object();
	c.id=id;
	if (cells[id])
	{
		c.up=cells[id].up;
		c.right=cells[id].right;
		c.down=cells[id].down;
		c.left=cells[id].left;
	}
	else
	{
		c.up=-1;
		c.right=-1;
		c.down=-1;
		c.left=-1;
	}
	ret += '<table border=0 cellspacing=0 cellpadding=0 width=270 height=270><colgroup><col width=90><col width=90><col width=90>';
	ret += '<tr><td></td>';
	ret += retTD_NM(cells[c.up], 1);
	ret += '<td></td></tr>';
	ret += '<tr>'+retTD_NM(cells[c.left], 2)+retTD_NM(cells[c.id], 0)+retTD_NM(cells[c.right], 3)+'</tr>';
	ret += '<tr><td></td>'+retTD_NM(cells[c.down], 4)+'<td></td></tr>';
	ret += '</table>';
	return ret;
}

function retTD_NM(c, a)
{
	if (c)
		return '<td background="../../images/dungeons/'+c.img+'" align=center valign=center onclick="click_st_func('+a+');"><b>'+/*c.nm+*/'</b></td>';
	else
		return '<td align=center valign=center onclick="click_st_func('+a+');"></td>';
}

function click_st_func(a)
{
	click_st=a;
	_('click_st_str').innerHTML = click_st_arr[a];
}

function nameSet(x, y)
{
	_('dun_load_st').innerHTML = 'Загрузка...';
	query('dun_ref.php?name='+ _('nm_cell').value, dun_id + '|' + x + '|' + y);
}

function drawDungeon()
{
	for (var y=0;y<50;y++)
	for (var x=0;x<50;x++)
		if (cells[x*100+y])
		{
			_('cell_'+x+'_'+y).style.backgroundColor='blue';
			_('cell_'+x+'_'+y).style.cursor = 'pointer';
		}
}

function showCell(x, y)
{
	if (click_st==0)
	{
		_('dun_load_st').innerHTML = 'Загрузка...';
		_('dun_actions').innerHTML = 'Загрузка...';
		query('dun_ref.php?show=1', dun_id + '|' + x + '|' + y);
	}
	if (click_st>=1 && click_st<=4)
	{
		_('dun_load_st').innerHTML = 'Загрузка...';
		query('dun_ref.php?cur_cell='+cur_cell+'&dr='+click_st, dun_id + '|' + x + '|' + y);
	}
}
</script>
<?
echo "<h1>Редактор данжей</h1>";

$res = f_MQuery("SELECT dungeon_id FROM dungeons_cells GROUP BY dungeon_id");
if (f_MNum($res)) echo "Выберите данж: ";
while ($arr = f_MFetch($res))
{
	echo "<a href='edit_dun.php?dun_id={$arr[0]}'>{$arr[0]}</a>&nbsp;";
}

echo "<hr>";

echo "<br><script>\n";
if (isset($_GET['dun_id']))
{
	$dun_id=$_GET['dun_id'];
	echo "dun_id=".$dun_id.";\n";
	$res = f_MQuery("SELECT * FROM dungeons_cells WHERE dungeon_id=".$dun_id." ORDER BY cell_num");
	while ($arr = f_MFetch($res))
	{
		echo "addCell({$arr[1]}, '{$arr[2]}', '{$arr[3]}', {$arr[4]}, {$arr[5]}, {$arr[6]}, {$arr[7]});\n";
	}
	
}
else die();
echo "</script>";
echo "<table width=100%><tr><td width=800 valign=top><div id=dun>";
echo "<table border=1 cellspacing=0 cellpadding=0>";
for ($i=49;$i>=0;$i--)
{
	echo "<tr>\n";
	for ($j=49;$j>=0;$j--)
	{
		echo "<td style='font-size:10;' valign=center align=center height=13 width=13><div onclick='showCell({$j}, {$i});' id='cell_{$j}_{$i}'>&nbsp;</div></td>\n";
	}
	echo "</tr>\n";
}
echo "</table></div></td>";

echo "<td valign=top><div id='dun_load_st'>&nbsp;</div><hr><div id='click_st_str'>&nbsp;</div><hr><div id='dun_actions'>&nbsp;</div></td></tr></table>";

echo "<script>";
for ($i=0;$i<50;$i++)
for ($j=0;$j<50;$j++)
{
	echo "_('cell_{$i}_{$j}').onmousemove = safeTooltip;\n";
	echo "_('cell_{$i}_{$j}').onmouseout = hideTooltip;\n";
}
echo "drawDungeon();\nclick_st_func(0);\n</script>";

?>

<?
f_MClose();
?>