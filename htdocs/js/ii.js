function oi( c )
{
	window.open( '/player_info.php?nick=' + c,'_blank' ); // Вас я оставляю только потому что надеюсь вас перевоспитать
}

function iin(a)
{
	if( a == 'Астаниэль' ) return '<font color=#0000FF>А</font><font color=#2525FF>с</font><font color=#4545FF>т</font><font color=#7070FF>а</font><font color=#9090FF>н</font><font color=#B0B0FF>и</font><font color=#D0D0FF>э</font><font color=#E0E0FF>л</font><font color=#FFFFFF>ь</font>';
	if( a == 'Пламени' ) return '<font color=#FF0000>П</font><font color=#FF4040>л</font><font color=#FF7070>а</font><font color=#FF9595>м</font><font color=#FFCCCC>е</font><font color=#FFEEEE>н</font><font color=#FFFFFF>и</font>';
	if( a == 'Ка-Напис' ) return '<font color=#00FF00>К</font><font color=#40FF40>а</font><font color=#70FF70>-Н</font><font color=#95FF95>а</font><font color=#CCFFCC>п</font><font color=#EEFFEE>и</font><font color=#FFFFFF>с</font>';

 if( a == 'Xen' ) return '<font color=#0000FF>X</font><font color=#9090FF>e</font><font color=#E0E0FF>n</font>';
 if( a == 'Reincarnation' ) return '<font color=#870087>R</font><font color=#C800C8>e</font><font color=#FF64FF>i</font><font color=#FFFFFF>n</font>';
// if( a == 'Rhiannon' ) return '<font color=#000000>R</font><font color=#000000>h</font><font color=#000000>i</font><font color=#660099>a</font><font color=#660099>n</font><font color=#660099>n</font><font color=#8B00FF>o</font><font color=#8B00FF>n</font>';
	 if( a == 'Жорик' ) return '<font color=#000000>Ж</font><font color=#404040>о</font><font color=#666666>р</font><font color=#8c8c8c>и</font><font color=#ffffff>к</font>';
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

	return '[' + a + ']&nbsp;' + cln + sco + '<span style="color:' + d + ';font-weight: bold;">' + nick + '</span>' + scc + '&nbsp;<a href="/player_info.php?nick=' + c + '" target="_blank" title="Информация о Персонаже ' + c + '"><img border=0 src="/images/i.gif" style="width: 11px; height: 11px;"></a>';
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
	st = '[' + a + ']&nbsp;' + cln + sco + '<b><font color=' + d + '>' + nick + '</font></b>' + scc + '&nbsp;<a href="/player_info.php?nick=' + c + '" target="_blank" title="Информация о Персонаже ' + c + '" style="cursor: pointer"><img src="/images/i.gif" style="width: 11px; height: 11px; border: 0px;" /></a>';
	return st;
}
 