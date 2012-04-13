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
	places[pl_id] = new Array();
	places[pl_id][0] = pl_name;
	places[pl_id][1] = new Array();
}

function addProject(pr_id, pr_name, pl_id, kust, skv, own_id)
{
	if (!places[pl_id])
	{
		places[pl_id] = new Array();
		places[pl_id][0] = 'undefined';
		places[pl_id][1] = new Array();
	}
	if (!places[pl_id][1][kust])
		places[pl_id][1][kust] = new Array();
	if (!places[pl_id][1][kust][skv])
		places[pl_id][1][kust][skv] = new Array();
	places[pl_id][1][kust][skv][pr_id] = new Array();
	places[pl_id][1][kust][skv][pr_id][0] = pr_id;
	places[pl_id][1][kust][skv][pr_id][1] = pr_name;
}

function addZak(id, nm, up_id)
{
	if (up_id==0)
	{
		if (!zak[id])
		{
			zak[id] = new Array();
			zak[id][1] = new Array();
		}
		zak[id][0] = nm;
	}
	else
	{
		if (!zak[up_id])
		{
			zak[up_id] = new Array();
			zak[up_id][0] = 'undefined';
			zak[up_id][1] = new Array();
		}
		zak[up_id][1][id] = nm;
	}
}

function showZaks()
{
	var ret = '';
	for (var i=1;i<=zak.length;i++)
	{
		if (zak[i])
		{
			ret += '<div id=mdiv_'+i+' onclick="expand_pe(\'div_'+i+'\');" style="cursor:pointer;">&nbsp;';
			ret += '<img id=idiv_'+i+'  src="images/e_plus.gif" width="11" height="11" title="Развернуть список">';
			ret += zak[i][0]+'</div>';
			ret += '<div style="display:none;" id=ddiv_'+i+'>';
			for (var j=1;j<=zak[i][1].length;j++)
			{
				if (zak[i][1][j])
				{
					ret += '<div style="cursor:pointer" id=mdiv_'+i+'_'+j+'>&nbsp;&nbsp;&nbsp;'+zak[i][1][j]+'</div>';
				}
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
function show(a)
{
	parent.main_graph.redraw('graph_'+a);
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