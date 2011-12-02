var ccs = new Array( "blue", "green", "red", "gray" );

function _(a)
{
	return document.getElementById( a );
}

function cn( a, b, c )
{
	return '<a onmousemove="showTooltipW(event,\'' + c + '\', 250)" onmouseout="hideTooltip()"><font color=' + ccs[a] + '><b>' + b + '</b></font></a>';
}

function cimg( a, n, g, c, q )
{
	c = '<b><font color=' + ccs[g] + '>' + n + '</font></b><br>' + c;
	return '<a onmousemove="spells_style(' + q + ',\'none\');document.getElementById(\'hint' + q + '\').innerHTML = \'' + c + '\';document.getElementById(\'hint' + q + '\').style.display=\'\';" onmouseout="spells_style(' + q + ',\'\');document.getElementById(\'hint' + q + '\').style.display=\'none\';"><img width=141 height=141 border=0 src=images/spells/' + a + '></a>';
}

function csimg( g, n, i, dr )
{
	c = '<b><font color=' + ccs[g] + '>' + n + '</font></b>';
	return '<a onmousemove="showTooltipW(event,\'' + c + '<br>' + dr + '\', 250)" onmouseout="hideTooltip()"><img src=images/spells/' + i + ' width=25 height=25 border=0></a>';
}

function csimgl( g, n, i, dr, id )
{
	c = '<b><font color=' + ccs[g] + '>' + n + '</font></b>';
	if( document.all )
		return '<div onmousemove="showTooltipW(event,\'' + c + '<br>' + dr + '\', 250)" onmouseout="hideTooltip()" width=141 height=141 style="cursor:pointer;width:141px;height:141px;position:relative;left:0px;top:0px; filter:progid:DXImageTransform.Microsoft.AlphaImageLoader( src=\'images/spells/' + i + '\', sizingMethod=\'scale\');" border=0 id=' + id + '></div>';

	return '<img onmousemove="showTooltipW(event,\'' + c + '<br>' + dr + '\', 250)" onmouseout="hideTooltip()" src=images/spells/' + i + ' width=141 height=141 style="cursor:pointer;width:141px;height:141px;position:relative;left:0px;top:0px;" border=0 class=pngie id=' + id + '>';
}

function cr( a, b, c, d )
{
	return '<a title="' + a + '"><small><small><font color=' + ccs[b] + '><b>' + c + '<br>' + d + '</b></font></small></small></a>';
}

function crn( a, b )
{
	return '<font color=' + ccs[b] + '><b>' + a + '</b></font>';
}

function getAp(el)
{
   var r = { x: el.offsetLeft, y: el.offsetTop };
   if (el.offsetParent)
   {
       var tmp = getAp(el.offsetParent);
       r.x += tmp.x;
       r.y += tmp.y;
   }
   return r;
}


