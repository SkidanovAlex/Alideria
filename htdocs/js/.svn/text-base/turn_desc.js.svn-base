var csdir1 = 'images/spells/';
var csdir2 = 'images/icons/attributes/';

var ttps = [];
function turn_desc(a){
	var cur_t = 0;
	function z(v){
		var ret = '';
    	for( i in v ) {
    	    ttps[cur_t] = v[i][2];
    		if( v[i][2] != '' ) ret += '<td><img onmouseout="hideTooltip()" onmousemove="showTooltipW(event,ttps[' + cur_t + '],350)" width=25 height=25 src='+v[i][0]+'></td>';
    		else ret += '<td><img width=25 height=25 src='+v[i][0]+'></td>';
    		++ cur_t;
    	}
    	ret += '</tr><tr>';
    	for( i in v ) {
    		var c = 'green';
    		if( v[i][1] < 0 ) c = 'red';
    		else if( v[i][1] == 0 ) v[i][1] = "&nbsp;";
    		else v[i][1] = '+' + v[i][1];
    		ret += '<td align=center><small><font color='+c+'>'+v[i][1]+'</font></small></td>';
    	}
		return ret;
	}
	var c1 = '#606060';
	var c2 = '#606060';
	var ic = ['w_ic1.gif','e_ic1.gif','f_ic1.gif','empty.gif'];
	if( a[2] == ( a[4] + 1 ) % 3 || a[4] == 3 ) { c1 = 'darkgreen'; c2 = 'darkred'; }
	else if( a[2] != a[4] || a[2] == 3 ) { c1 = 'darkred'; c2 = 'darkgreen'; }
	var ret = '<table width=330 cellspacing=0 cellpadding=0><tr><td align=left><table cellspacing=0 cellpadding=0><tr><td><font color=' + c1 + '><b>' + a[0] + '&nbsp;</b></font></td><td><img width=20 height=20 src=' + csdir2 + ic[a[2]] + '></td></tr></table></td>';
	ret += '<td align=right><table cellspacing=0 cellpadding=0><tr><td><img width=20 height=20 src=' + csdir2 + ic[a[4]] + '></td><td><b><font color=' + c2 + '>&nbsp;' + a[1] + '</font></b></td></tr></table></td></tr></table>';
	ret += '<table width=330 cellspacing=0 cellpadding=0><tr><td align=left><table cellspacing=0 cellpadding=0><tr>';
	ret += z(a[3]);
	ret += '</tr></table></td><td align=right><table cellspacing=0 cellpadding=0><tr>';
	for( var i = 0; i+i < a[5].length-1; ++ i )
	{
		var t = a[5][i];
		a[5][i] = a[5][a[5].length-1-i];
		a[5][a[5].length-1-i] = t;
	}
	ret += z(a[5]);
	ret += '</tr></table></td></tr></table>';

	return ret;
}
