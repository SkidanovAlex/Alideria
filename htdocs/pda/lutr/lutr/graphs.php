<!--[if IE]><script language="javascript" type="text/javascript" src="../flot/excanvas.min.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="../flot/jquery.js"></script>
<script language="javascript" type="text/javascript" src="../flot/jquery.flot.js"></script>
<script src="functions.js"></script>
<script src="ajax.js"></script>
<script>
var graphs = new Array();
var graph_num = 0;

function addGraph(pr_id)
{
	if (graph_num>=8)
		return;
	graphs[graph_num] = new Object();
	graphs[graph_num].pr_id = pr_id;
	graphs[graph_num].all_data = new Array(); // all_data
	graph_num++;
}

function setConf(gr_id, num, labels, clrs)
{
	for (var i=0;i<num;i++)
	{
		graphs[gr_id].all_data[i] = new Object();
		graphs[gr_id].all_data[i].label = labels[i];
		graphs[gr_id].all_data[i].color = clrs[i];
		graphs[gr_id].all_data[i].data = new Array();
	}
}

function setAxis(gr_id, yax)
{
	graphs[gr_id][2] = yax;
}

function addDatas(gr_id, datas)
{
	for (var i=0;i<datas[0].length;i++)
		for (var j=0;j<graphs[gr_id].all_data.length;j++)
		{
			datas[j][i][0] = Date.parse(datas[j][i][0]);
			graphs[gr_id].all_data[j].data.push(datas[j][i]);
			
		}
}

function getDatas(pr_id, tm)
{
	for (var i=0;i<8;i++)
		if (graphs[i] && graphs[i][0] == pr_id)
		{
			query('ajax_functions.php?pr_id='+pr_id+'&tm='+tm, '');
			return;
		}
}


function redraw(a)
{
//	var d = [[0, 1], [1, 10], [5,19]];
	$.plot($("#graph_"+(a+1)), graphs[a].all_data, {xaxis:{ticks:10, min:0, max:5}, yaxis:{ticks:[0, [1,"qwe <font color='#00FF00'>ewq</font> <font color='#00FFFF'>asd</font>"], [20, "21"]]}});
}
</script>

<table border=1 height=100% width=100%>
<tr height=25%>
<td width=50%><div style="width:500px;height:200px;" id=graph_1>&nbsp;</div></td>
<td width=50%><div style="width:500px;height:200px;" id=graph_2>&nbsp;</div></td>
</tr>
<tr height=25%>
<td width=50%><div style="width:500px;height:200px;" id=graph_3>&nbsp;</div></td>
<td width=50%><div style="width:500px;height:200px;" id=graph_4>&nbsp;</div></td>
</tr>
<tr height=25%>
<td width=50%><div style="width:500px;height:200px;" id=graph_5>&nbsp;</div></td>
<td width=50%><div style="width:500px;height:200px;" id=graph_6>&nbsp;</div></td>
</tr>
<tr height=25%>
<td width=50%><div style="width:500px;height:200px;" id=graph_7>&nbsp;</div></td>
<td width=50%><div style="width:500px;height:200px;" id=graph_8>&nbsp;</div></td>
</tr>
</table>
