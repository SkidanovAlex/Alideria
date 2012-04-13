<?
header("Content-type: text/html; charset=windows-1251");
?>
<!--[if IE]><script language="javascript" type="text/javascript" src="../flot/excanvas.min.js"></script><![endif]-->
<script language="javascript" type="text/javascript" src="../flot/jquery.js"></script>
<script language="javascript" type="text/javascript" src="../flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="../flot/jquery.flot.crosshair.js"></script>
<script src="functions.js"></script>
<script src="ajax.js"></script>
<script>
var graphs = new Array();
var graph_num = 0;
var cur_graph = -1;

function addGraph(pr_id, p_id, last_time, yaxis_ticks, pr_name)
{
	if (graph_num>=8)
		return;
	graphs[graph_num] = new Object();
	graphs[graph_num].pr_id = pr_id;
	graphs[graph_num].pipe_id = p_id;
	graphs[graph_num].last_time = last_time;
	graphs[graph_num].geting = false;
	graphs[graph_num].all_data = new Array();
	graphs[graph_num].mins = new Array();
	graphs[graph_num].maxs = new Array();
	graphs[graph_num].all_min = 999999999;
	graphs[graph_num].all_max = -999999999;
	graphs[graph_num].yaxis_ticks = yaxis_ticks;
	graphs[graph_num].lgnd = new Object();
	graphs[graph_num].lgnd.show = true;
	graphs[graph_num].lgnd.position = "nw";
	graphs[graph_num].lgnd.margin = 1;
	graphs[graph_num].pr_name = pr_name;
	graphs[graph_num].plot = null;
	graphs[graph_num].latestPosition = null;
	graphs[graph_num].updateLegendTimeout = null;
	graphs[graph_num].etaps = new Array();
	graph_num++;
}

function updateLegend(event) {
        graphs[cur_graph].updateLegendTimeout = null;
        
        var pos = graphs[cur_graph].latestPosition;
        var legends = $("#graph_"+(cur_graph+1)+" .legendLabel");
        /*
        var axes = graphs[cur_graph].plot.getAxes();
        if (pos.x < axes.xaxis.min || pos.x > axes.xaxis.max ||
            pos.y < axes.yaxis.min || pos.y > axes.yaxis.max)
            return;
*/
        var i, j, dataset = graphs[cur_graph].plot.getData();
        for (i = 0; i < dataset.length; ++i) {
            var series = dataset[i];

            // find the nearest points, x-wise
            for (j = 0; j < series.data.length; ++j)
                if (series.data[j][0] > pos.x)
                    break;
            
            // now interpolate
            var y, p1 = series.data[j - 1], p2 = series.data[j];
            if (p1 == null)
                y = p2[1];
            else if (p2 == null)
                y = p1[1];
            else
                y = p1[1] + (p2[1] - p1[1]) * (pos.x - p1[0]) / (p2[0] - p1[0]);

	var koef = (graphs[cur_graph].all_max - graphs[cur_graph].all_min)/(graphs[cur_graph].maxs[i] - graphs[cur_graph].mins[i]);
	y = (graphs[cur_graph].maxs[i] != graphs[cur_graph].mins[i])?((y - graphs[cur_graph].all_min)/koef + graphs[cur_graph].mins[i]):(graphs[cur_graph].maxs[i]);

		
            legends.eq(i).text(series.label.replace(/=.*/, "= " + y.toFixed(2)));
        }
    }

function setConf(pr_id, p_id, num, labels, clrs)
{
	var gr_id = getGraphId(pr_id, p_id);
	for (var i=0;i<num;i++)
	{
		graphs[gr_id].all_data[i] = new Object();
		graphs[gr_id].all_data[i].label = labels[i]+" = 0.00";
		graphs[gr_id].all_data[i].color = clrs[i];
		graphs[gr_id].all_data[i].data = new Array();
		graphs[gr_id].mins[i] = 999999999;
		graphs[gr_id].maxs[i] = -999999999;
	}
	
}

function getGraphId(pr_id, p_id)
{
	for (var i=0;i<graphs.length;i++)
		if (graphs[i] && graphs[i].pr_id==pr_id && graphs[i].pipe_id==p_id)
			return i;
	return -1;
}

function addDatas(pr_id, p_id, datas)
{
	var gr_id = getGraphId(pr_id, p_id);
	for (var i=0;i<datas[0].length;i++)
		for (var j=0;j<graphs[gr_id].all_data.length;j++)
		{
			datas[j][i][0] = Date.parse(datas[j][i][0]);
			graphs[gr_id].all_data[j].data.push(datas[j][i]);
			if (datas[j][i][1] < graphs[gr_id].mins[j])
				graphs[gr_id].mins[j] = datas[j][i][1];
			if (datas[j][i][1] > graphs[gr_id].maxs[j])
				graphs[gr_id].maxs[j] = datas[j][i][1];
			if (datas[j][i][1] < graphs[gr_id].all_min)
				graphs[gr_id].all_min = datas[j][i][1];
			if (datas[j][i][1] > graphs[gr_id].all_max)
				graphs[gr_id].all_max = datas[j][i][1];
		}
	graphs[gr_id].last_time += datas[0].length*5;
}

function getDatas(pr_id, p_id, tm)
{
	var gr_id = getGraphId(pr_id, p_id);
	if (gr_id >= 0)
	{
		graphs[gr_id].geting = true;
		query('ajax_functions.php?pr_id='+pr_id+'&p_id='+p_id+'&tm='+tm, '');
	}
}

function showProject(pr_id, p_id)
{
	if (graph_num>=8)
	{
		alert('Не более восьми проектов для одновременного просмотра.');
		return;
	}
	var gr_id = getGraphId(pr_id, p_id);
	if (gr_id == -1)
		query('ajax_functions.php?getProject='+pr_id+'&p_id='+p_id, '');
	else
	{
		delete graphs[gr_id];
		document.getElementById('graph_'+(gr_id+1)).innerHTML = "";
		document.getElementById('st_graph_'+(gr_id+1)).innerHTML = "";
		for (var i=gr_id;i<graph_num;i++)
		{
			graphs[i] = graphs[i+1];
			if (graphs[i])
			{
				document.getElementById('graph_'+(i+1)).innerHTML = document.getElementById('graph_'+(i+2)).innerHTML;
				document.getElementById('graph_'+(i+2)).innerHTML = "";
				document.getElementById('st_graph_'+(i+1)).innerHTML = document.getElementById('st_graph_'+(i+2)).innerHTML;
				document.getElementById('st_graph_'+(i+2)).innerHTML = "";
			}
		}
		graph_num--;
		delete graphs[graph_num];
	}
}

function refr_all()
{
	var r = 0;
	for (var i=0;i<graph_num;i++)
		if (!graphs[i].geting)
			getDatas(graphs[i].pr_id, graphs[i].pipe_id, graphs[i].last_time);
	setTimeout('refr_all()', 10000);
}

function get_y_tick(gr_id, i)
{
//	var yt = "<font style='align:\"justify\";background-color:#000000'>";
	var yt = "<table width=100% border=0 cellpadding=0 cellspacing=0 style='background-color:#000000;'><tr>";
	
	for (var j=0;j<graphs[gr_id].mins.length;j++)
	{
		var s = ((graphs[gr_id].maxs[j]-graphs[gr_id].mins[j])*i/(graphs[gr_id].yaxis_ticks-1)+graphs[gr_id].mins[j]);
		if (s < 10)
			s = "   "+s.toFixed(2);
		else if (s < 100)
			s = "  "+s.toFixed(2);
		else if (s < 1000)
			s = " "+s.toFixed(2);
		yt += "<td width=20% align=center><font size=2 color="+graphs[gr_id].all_data[j].color+">"+s+"</font></td>";
	}
//	yt += "</font>";
	yt += "</tr></table>";
	return yt;
}

function redraw(pr_id, p_id, again)
{
	var gr_id = getGraphId(pr_id, p_id);
//	var d = [[0, 1], [1, 10], [5,19]];
	if (gr_id>=0)
	{
//		var in_st = "<table witdh=100% height=23 align=left border=0 cellpadding=0 cellspacing=0><tr>";
//		in_st += "<td width=130>";
		var in_st = "<font size=2>";
		if (graphs[gr_id].lgnd.show)
			in_st += "<button style='width:130px;' onclick='graphs["+gr_id+"].lgnd.show=false;redraw("+pr_id+", "+p_id+");'>Скрыть легенду</button>";
		else
			in_st += "<button style='width:130px;' onclick='graphs["+gr_id+"].lgnd.show=true;redraw("+pr_id+", "+p_id+");'>Показать легенду</button>";
//		in_st += "</td>";
//		in_st += "<td><font size=2>"+graphs[gr_id].pr_name+"</font></td>";
//		in_st += "</tr></table>";
		in_st += "&nbsp;&nbsp;"+graphs[gr_id].pr_name+"</font>";
		document.getElementById('st_graph_'+(gr_id+1)).innerHTML = in_st;
		var all_data = new Array();
		for (var i=0;i<graphs[gr_id].all_data.length;i++)
		{
			all_data[i] = new Object();
			all_data[i].label = graphs[gr_id].all_data[i].label;
			all_data[i].color = graphs[gr_id].all_data[i].color;
			all_data[i].data = new Array();
			var koef = (graphs[gr_id].all_max-graphs[gr_id].all_min)/(graphs[gr_id].maxs[i] - graphs[gr_id].mins[i]);
			for (var j=0;j<graphs[gr_id].all_data[i].data.length;j++)
			{
				all_data[i].data[j]=new Array();
				all_data[i].data[j][0] = graphs[gr_id].all_data[i].data[j][0];
				all_data[i].data[j][1] = graphs[gr_id].all_data[i].data[j][1];
				all_data[i].data[j][1] = (all_data[i].data[j][1]-graphs[gr_id].mins[i]) * koef + graphs[gr_id].all_min;
			}
		}
		var y_ticks = new Array();
		y_ticks = new Array();
		for (var i=0;i<graphs[gr_id].yaxis_ticks;i++)
		{
			y_ticks[i] = new Array();
			y_ticks[i][0] = (graphs[gr_id].all_max-graphs[gr_id].all_min)*i/(graphs[gr_id].yaxis_ticks-1);
			y_ticks[i][1] = get_y_tick(gr_id, i);
		}
		graphs[gr_id].plot = $.plot($("#graph_"+(gr_id+1)), all_data, {series:{shadowSize: 0}, grid: { aboveData:true, hoverable: true, autoHighlight: false }, crosshair:{mode:"x"},legend:graphs[gr_id].lgnd, xaxis:{ticks:6, mode:"time", timeformat:"%H:%M"}, yaxis:{min:(graphs[gr_id].all_min - (graphs[gr_id].all_max-graphs[gr_id].all_min)/graphs[gr_id].yaxis_ticks), max:(graphs[gr_id].all_max + (graphs[gr_id].all_max-graphs[gr_id].all_min)/graphs[gr_id].yaxis_ticks), ticks:y_ticks}});
		$("#graph_"+(gr_id+1)).bind("plothover",  function (event, pos, item) {
        graphs[gr_id].latestPosition = pos;
        cur_graph = gr_id;
//        if (!graphs[gr_id].updateLegendTimeout)
            graphs[gr_id].updateLegendTimeout = setTimeout(updateLegend(), 50);
    });
		graphs[gr_id].geting = false;
		
		if (again)
			getDatas(pr_id, p_id, graphs[gr_id].last_time);
	}
	
}

refr_all();

</script>

<table border=0 cellpadding=0 cellspacing=0 height=100% width=100%>
<tr height=25%>
<td align=left width=50%><div id=st_graph_1 style="width:1px;height:25px;">&nbsp;</div><div style="width:1px;height:175px;" id=graph_1>&nbsp;</div></td>
<td align=left width=50%><div id=st_graph_2 style="width:1px;height:25px;">&nbsp;</div><div style="width:1px;height:175px;" id=graph_2>&nbsp;</div></td>
</tr>
<tr height=25%>
<td align=left width=50%><div id=st_graph_3 style="width:1px;height:25px;">&nbsp;</div><div style="width:1px;height:175px;" id=graph_3>&nbsp;</div></td>
<td align=left width=50%><div id=st_graph_4 style="width:1px;height:25px;">&nbsp;</div><div style="width:1px;height:175px;" id=graph_4>&nbsp;</div></td>
</tr>
<tr height=25%>
<td align=left width=50%><div id=st_graph_5 style="width:1px;height:25px;">&nbsp;</div><div style="width:1px;height:175px;" id=graph_5>&nbsp;</div></td>
<td align=left width=50%><div id=st_graph_6 style="width:1px;height:25px;">&nbsp;</div><div style="width:1px;height:175px;" id=graph_6>&nbsp;</div></td>
</tr>
<tr height=25%>
<td align=left width=50%><div id=st_graph_7 style="width:1px;height:25px;">&nbsp;</div><div style="width:1px;height:175px;" id=graph_7>&nbsp;</div></td>
<td align=left width=50%><div id=st_graph_8 style="width:1px;height:25px;">&nbsp;</div><div style="width:1px;height:175px;" id=graph_8>&nbsp;</div></td>
</tr>
</table>
<script>
var width_div = document.width/2;
for (var i=1;i<=8;i++)
{
	document.getElementById('graph_'+i).style.width = width_div;
	document.getElementById('st_graph_'+i).style.width = width_div;
}
</script>