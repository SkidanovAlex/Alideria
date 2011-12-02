function ipng( tid, iid, src, w, h, add )
{
	var ret = '<table background='+src+' cellspacing=0 cellpadding=0 id='+tid+' width=' + w + ' height=' + h + ' border=0><tr><td width=' + w + ' height=' + h + '>';
	ret += '<img width=' + w + ' height=' + h + ' id='+iid+' src=empty.gif '+add+'>';
	ret += '</td></tr></table>';
	return ret;
}