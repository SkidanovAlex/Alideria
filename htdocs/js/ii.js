function oi( c )
{
	window.open( '/player_info.php?nick=' + c,'_blank' ); // ��� � �������� ������ ������ ��� ������� ��� �������������
}

function iin(a)
{
	if( a == '���������' ) return '<font color=#0000FF>�</font><font color=#2525FF>�</font><font color=#4545FF>�</font><font color=#7070FF>�</font><font color=#9090FF>�</font><font color=#B0B0FF>�</font><font color=#D0D0FF>�</font><font color=#E0E0FF>�</font><font color=#FFFFFF>�</font>';
	if( a == '�������' ) return '<font color=#FF0000>�</font><font color=#FF4040>�</font><font color=#FF7070>�</font><font color=#FF9595>�</font><font color=#FFCCCC>�</font><font color=#FFEEEE>�</font><font color=#FFFFFF>�</font>';
	if( a == '��-�����' ) return '<font color=#00FF00>�</font><font color=#40FF40>�</font><font color=#70FF70>-�</font><font color=#95FF95>�</font><font color=#CCFFCC>�</font><font color=#EEFFEE>�</font><font color=#FFFFFF>�</font>';

 if( a == 'Xen' ) return '<font color=#0000FF>X</font><font color=#9090FF>e</font><font color=#E0E0FF>n</font>';
 if( a == 'Reincarnation' ) return '<font color=#870087>R</font><font color=#C800C8>e</font><font color=#FF64FF>i</font><font color=#FFFFFF>n</font>';
// if( a == 'Rhiannon' ) return '<font color=#000000>R</font><font color=#000000>h</font><font color=#000000>i</font><font color=#660099>a</font><font color=#660099>n</font><font color=#660099>n</font><font color=#8B00FF>o</font><font color=#8B00FF>n</font>';
	 if( a == '�����' ) return '<font color=#000000>�</font><font color=#404040>�</font><font color=#666666>�</font><font color=#8c8c8c>�</font><font color=#ffffff>�</font>';
	  return a;
}

function ii( a, c, d, e )
{
	var nick = iin(c);
	var sco = '<a href="javascript://" onclick="window.top.chat_who.nick(\'' + c + '\')">';
	var scc = '</a>';

	if( typeof isForum != 'undefined' )
		sco = '<a href="javascript://" onclick="f2(document.q.txt, \'' + c + '\')">';
	else if( !window.top.chat_who )
		sco = scc = '';

	var cln = '';
	if( e )
		cln = '<a href="/orderpage.php?id=' + e + '" title="' + clans[e][0] + '" target=_blank><img border=0 width=18 height=13 src="/images/clans/' + clans[e][1] + '" style="position:relative;top:2px;"></a>&nbsp;';

	return '[' + a + ']&nbsp;' + cln + sco + '<span style="color:' + d + ';font-weight: bold;">' + nick + '</span>' + scc + '&nbsp;<a href="/player_info.php?nick=' + c + '" target="_blank" title="���������� � ��������� ' + c + '"><img border=0 src="/images/i.gif" style="width: 11px; height: 11px;"></a>';
}

function ii2(a,c,d,e,id)
{
	var st = '';
	var nick = iin(c);
	var onc = "window.top.oi('" + c + "&id=" + id + "')";
	var sco = '<a style="cursor: pointer" onClick="window.top.chat_who.nick(\'' + c + '\')">';
	var scc = '</a>';
	if( typeof isForum != 'undefined' ) sco = '<a style="cursor: pointer" onClick="f2(document.q.txt, \'' + c + '\')">';
	else if( !window.top.chat_who ) sco = scc = '';
	var cln = '';
	if( e ) cln = '<a href=orderpage.php?id=' + e + ' title="' + clans[e][0] + '" target=_blank><img border=0 width=18 height=13 src=images/clans/' + clans[e][1] + ' style="position:relative;top:2px;"></a>&nbsp;';
	st = '[' + a + ']&nbsp;' + cln + sco + '<b><font color=' + d + '>' + nick + '</font></b>' + scc + '&nbsp;<a href="/player_info.php?nick=' + c + '" target="_blank" title="���������� � ��������� ' + c + '" style="cursor: pointer"><img src="/images/i.gif" style="width: 11px; height: 11px; border: 0px;" /></a>';
	return st;
}
 