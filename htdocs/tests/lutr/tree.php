<?
header("Content-type: text/html; charset=windows-1251");
?>
<script src='functions.js'></script>
<script src="ajax.js"></script>
<script>
var zak = new Array();
var places = new Array();

function addPlace(pl_id, pl_name)
{
	places[pl_id] = pl_name;
}

function addProject(pr_id, pr_name, pipes, pl_id, kust, skv, own_id)
{
	var oid = 0;
	for (var i=0;i<zak.length;i++)
	{
		if (!zak[i])
			continue;
		if (i==own_id)
		{
			oid = zak[i].up_owner;
			break;
		}
	}
	if (oid)
		var z = zak[oid].zak[own_id];
	else
		var z = zak[own_id];
	if (!z) return;
	if (!z.place)
		z.place = new Array();
	if (!z.place[pl_id])
		z.place[pl_id] = new Array();
	if (!z.place[pl_id][kust])
		z.place[pl_id][kust] = new Array();
	if (!z.place[pl_id][kust][skv])
		z.place[pl_id][kust][skv] = new Array();
	z.place[pl_id][kust][skv][pr_id] = new Object();
	z.place[pl_id][kust][skv][pr_id].nm = pr_name;
	z.place[pl_id][kust][skv][pr_id].pipes = pipes;
}

function searchKust(pl, k_n)
{
	for (var i=0;i<pl.length;i++)
		if (pl[i].nm == k_n)
			return i;
	return -1;
}

function addZak(id, nm, up_id)
{
	if (up_id==0)
	{
		if (!zak[id])
		{
			zak[id] = new Object();
			zak[id].zak = new Array();
			zak[id].up_owner = 0;
		}
		zak[id].nm = nm;
	}
	else
	{
		if (!zak[up_id])
		{
			zak[up_id] = new Object();
			zak[up_id].nm = 'undefined';
			zak[up_id].up_owner = 0;
			zak[up_id].zak = new Array();
		}
		zak[up_id].zak[id] = new Object;
		zak[up_id].zak[id].nm = nm;
		zak[up_id].zak[id].up_owner = up_id;
		if (!zak[id])
		{
			zak[id] = new Object();
			zak[id].zak = new Array();
			zak[id].up_owner = up_id;
		}
		zak[id].nm = nm;
	}
}

function showZaks()
{
	var ret = '';
	for (var i=1;i<=zak.length;i++)
	{
		if (zak[i] && zak[i].up_owner==0)
		{
			ret += '<div id=mdiv_'+i+' onclick="expand_pe(\'div_'+i+'\');" style="cursor:pointer;">&nbsp;';
			ret += '<img id=idiv_'+i+'  src="images/e_plus.gif" width="11" height="11" title="Развернуть список">';
			ret += zak[i].nm+'</div>';
			ret += '<div style="display:none;" id=ddiv_'+i+'>';
			for (var j=1;j<=zak[i].zak.length;j++)
			{
				if (zak[i].zak[j])
				{
					ret += '<div onclick="expand_pe(\'div_'+i+'_'+j+'\');" style="cursor:pointer" id=mdiv_'+i+'_'+j+'>&nbsp;&nbsp;&nbsp;';
					ret += '<img id=idiv_'+i+'_'+j+'  src="images/e_plus.gif" width="11" height="11" title="Развернуть список">';
					ret += zak[i].zak[j].nm+'</div>';
					ret += '<div style="display:none;" id=ddiv_'+i+'_'+j+'>';
					if (zak[i].zak[j].place)
					for (var pl_id=0;pl_id<=zak[i].zak[j].place.length;pl_id++)
					{
						if (zak[i].zak[j].place[pl_id])
						{
							ret += '<div onclick="expand_pe(\'div_'+i+'_'+j+'_'+pl_id+'\');" style="cursor:pointer" id=mdiv_'+i+'_'+j+'_'+pl_id+'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
							ret += '<img id=idiv_'+i+'_'+j+'_'+pl_id+'  src="images/e_plus.gif" width="11" height="11" title="Развернуть список">';
							ret += places[pl_id]+'</div>';
							ret += '<div style="display:none;" id=ddiv_'+i+'_'+j+'_'+pl_id+'>';
							for (var k_id=0;k_id<=zak[i].zak[j].place[pl_id].length;k_id++)
								if (zak[i].zak[j].place[pl_id][k_id])
								{
									ret += '<div onclick="expand_pe(\'div_'+i+'_'+j+'_'+pl_id+'_'+k_id+'\');" style="cursor:pointer" id=mdiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
									ret += '<img id=idiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'  src="images/e_plus.gif" width="11" height="11" title="Развернуть список">';
									ret += 'Куст '+k_id+'</div>';
									ret += '<div style="display:none;" id=ddiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'>';
									for (var skv_id=0;skv_id<=zak[i].zak[j].place[pl_id][k_id].length;skv_id++)
										if (zak[i].zak[j].place[pl_id][k_id][skv_id])
										{
											ret += '<div onclick="expand_pe(\'div_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'\');" style="cursor:pointer" id=mdiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'>';
											ret += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
											ret += '<img id=idiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'  src="images/e_plus.gif" width="11" height="11" title="Развернуть список">';
											ret += 'Скважина '+skv_id+'</div>';
											ret += '<div style="display:none;" id=ddiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'>';
											for (var pr_id=0;pr_id<=zak[i].zak[j].place[pl_id][k_id][skv_id].length;pr_id++)
												if (zak[i].zak[j].place[pl_id][k_id][skv_id][pr_id])
												{
													ret += '<div onclick="expand_pe(\'div_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'_'+pr_id+'\');" style="cursor:pointer" id=mdiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'_'+pr_id+'>';
													ret += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
													ret += '<img id=idiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'_'+pr_id+'  src="images/e_plus.gif" width="11" height="11" title="Развернуть список">';
													ret += zak[i].zak[j].place[pl_id][k_id][skv_id][pr_id].nm;
													ret += '</div>';
													ret += '<div style="display:none;" id=ddiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'_'+pr_id+'>';
													for (var p_id=0; p_id<zak[i].zak[j].place[pl_id][k_id][skv_id][pr_id].pipes.length; p_id++)
													{
														ret += '<div onclick="show('+pr_id+', '+zak[i].zak[j].place[pl_id][k_id][skv_id][pr_id].pipes[p_id][0]+');" style="cursor:pointer" id=mdiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'_'+pr_id+'_'+p_id+'>';
														ret += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
														ret += zak[i].zak[j].place[pl_id][k_id][skv_id][pr_id].pipes[p_id][1]+'</div>';
													}
													ret += '</div>';
												}
											ret += '</div>';
										}
									ret += '</div>';
								}
							ret += '</div>';
						}
					}
					ret += '</div>';
				}
			}
			if (zak[i].place)
			{
				var j = i;
				ret += '<div id=ddiv_'+i+'_'+j+'>';
				if (zak[i].place)
				for (var pl_id=0;pl_id<=zak[i].place.length;pl_id++)
				{
					if (zak[i].place[pl_id])
					{
						ret += '<div onclick="expand_pe(\'div_'+i+'_'+j+'_'+pl_id+'\');" style="cursor:pointer" id=mdiv_'+i+'_'+j+'_'+pl_id+'>&nbsp;&nbsp;&nbsp;';
						ret += '<img id=idiv_'+i+'_'+j+'_'+pl_id+'  src="images/e_plus.gif" width="11" height="11" title="Развернуть список">';
						ret += places[pl_id]+'</div>';
						ret += '<div style="display:none;" id=ddiv_'+i+'_'+j+'_'+pl_id+'>';
						for (var k_id=0;k_id<=zak[i].place[pl_id].length;k_id++)
							if (zak[i].place[pl_id][k_id])
							{
								ret += '<div onclick="expand_pe(\'div_'+i+'_'+j+'_'+pl_id+'_'+k_id+'\');" style="cursor:pointer" id=mdiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
								ret += '<img id=idiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'  src="images/e_plus.gif" width="11" height="11" title="Развернуть список">';
								ret += 'Куст '+k_id+'</div>';
								ret += '<div style="display:none;" id=ddiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'>';
								for (var skv_id=0;skv_id<=zak[i].place[pl_id][k_id].length;skv_id++)
									if (zak[i].place[pl_id][k_id][skv_id])
									{
										ret += '<div onclick="expand_pe(\'div_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'\');" style="cursor:pointer" id=mdiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'>';
										ret += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
										ret += '<img id=idiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'  src="images/e_plus.gif" width="11" height="11" title="Развернуть список">';
										ret += 'Скважина '+skv_id+'</div>';
										ret += '<div style="display:none;" id=ddiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'>';
										for (var pr_id=0;pr_id<=zak[i].place[pl_id][k_id][skv_id].length;pr_id++)
												if (zak[i].place[pl_id][k_id][skv_id][pr_id])
												{
													ret += '<div onclick="expand_pe(\'div_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'_'+pr_id+'\');" style="cursor:pointer" id=mdiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'_'+pr_id+'>';
													ret += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
													ret += '<img id=idiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'_'+pr_id+'  src="images/e_plus.gif" width="11" height="11" title="Развернуть список">';
													ret += zak[i].place[pl_id][k_id][skv_id][pr_id].nm;
													ret += '</div>';
													ret += '<div style="display:none;" id=ddiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'_'+pr_id+'>';
													for (var p_id=0; p_id<zak[i].place[pl_id][k_id][skv_id][pr_id].pipes.length; p_id++)
													{
														ret += '<div onclick="show('+pr_id+', '+zak[i].place[pl_id][k_id][skv_id][pr_id].pipes[p_id][0]+');" style="cursor:pointer" id=mdiv_'+i+'_'+j+'_'+pl_id+'_'+k_id+'_'+skv_id+'_'+pr_id+'_'+p_id+'>';
														ret += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
														ret += zak[i].place[pl_id][k_id][skv_id][pr_id].pipes[p_id][1]+'</div>';
													}
													ret += '</div>';
												}
										ret += '</div>';
									}
								ret += '</div>';
							}
						ret += '</div>';
					}
				}
				ret += '</div>';
			}
			ret += '</div>';
		}
	}
	_('zaks').innerHTML = ret;
}

function refr()
{
	zak = new Array();
	query('ajax_functions.php?show_tree');
}

function _mg(a)
{
	return parent.main_graph.document.getElementById(a);
}
function show(pr_id, p_id)
{
	parent.main_graph.showProject(pr_id, p_id);
}

</script>
<button onclick="refr();">Обновить</button>
<div id=zaks>&nbsp;</div>
<script>
showZaks();
</script>

<?
//echo "123";
?>
<!--
<button onclick="show(1);">1</button>
<button onclick="show(2);">2</button>
<button onclick="show(3);">3</button>
<button onclick="show(4);">4</button>
<button onclick="show(5);">5</button>
<button onclick="show(6);">6</button>
<button onclick="show(7);">7</button>
<button onclick="show(8);">8</button>
-->