function cc(id,nick,lv,hp,mhp,wp,wm,wma,wa,wd,np,nm,nma,na,nd,fp,fm,fma,fa,fd,clan,rdy)
{
	var st = '<div style="position:relative;left:0px;top:0px;"><table style="position:relative;left:0px;top:0px;" width=220 height=112 cellspacing=0 cellpadding=0 border=0>';
	
	tt = window.top.ii2( lv, nick, 'black',clan, id );
	st += '<tr height=20><td align=center style="width:220px; height:20px;" background=images/rect/a.png>' + tt + '</td></tr>';

	st += '<tr height=15><td align=center style="width:220px; height:15px;">';
		wdt = parseInt( 144 * hp / mhp );
		if( wdt < 0 ) wdt = 0;
		st += '<table background=images/rect/b.png width=220 height=15 cellspacing=0 cellpadding=0 border=0><tr><td style="width:148;height:15"><img id=tbl' + id + ' src=images/rect/c.png border=0 width=' + wdt + ' height=13 style="position:relative;top:0px;left:2px;"></td><td align=center><div id=dhp' + id + '>' + hp + '/' + mhp + '</div></td></tr></table>';
	st += '</td></tr>';

	st += '<tr height=77><td align=center style="width:220px; height:77px;">';
		st += '<table width=220 height=77 cellspacing=0 cellpadding=0 border=0><tr>';
		
				if( wp <= 0 ) st += '<td style="width:73;height:77" background=images/rect/wd.png>';
				else st += '<td style="width:73;height:77" background=images/rect/wl.png>';
				st += '<table width=73 height=77 cellspacing=0 cellpadding=0 border=0>';
				st += '<tr height=22><td align=left valign=top><div style="position:relative;top:2px;left:2px;z-index:3"><a title="Атака Магии Воды"><font color=#003E95><b>' + wa + '</b></font></a></div></td>';
				st += '<td align=right valign=top><div style="position:relative;top:2px;left:-2px;z-index:3"><a title="Защита Магии Воды"><font color=#003E95><b>' + wd + '</b></font></a></div></td></tr>';
				if( wp > 0 )
				{
					st += '<tr height=33><td colspan=2 align=center valign=top><div style="position:relative;top:-15px;left:1px;z-index:2"><font color=#003E95><big><b>' + wp + '</b></big></font></div></td></tr>';
					st += '<tr height=22><td colspan=2 align=center valign=bottom><div style="position:relative;top:-2px;left:0px"><a title="Мана Воды"><font color=#003E95><b>' + wm + '<sup><span style="position:relative;top:2px">+' + wma + '</span></sup></b></font></a></div></td></tr>';
				}
				st += '</table>';
				st += '</td>';

				if( np <= 0 ) st += '<td style="width:73;height:77" background=images/rect/nd.png>';
				else st += '<td style="width:73;height:77" background=images/rect/nl.png>';
				st += '<table width=73 height=77 cellspacing=0 cellpadding=0 border=0>';
				st += '<tr height=22><td align=left valign=top><div style="position:relative;top:2px;left:2px;z-index:3"><a title="Атака Магии Земли"><font color=#0F4000><b>' + na + '</b></font></a></div></td>';
				st += '<td align=right valign=top><div style="position:relative;top:2px;left:-2px;z-index:3"><a title="Защита Магии Земли"><font color=#0F4000><b>' + nd + '</b></font></a></div></td></tr>';
				if( np > 0 )
				{
					st += '<tr height=33><td colspan=2 align=center valign=top><div style="position:relative;top:-15px;left:0px;z-index:2"><font color=#0F4000><big><b>' + np + '</b></big></font></div></td></tr>';
					st += '<tr height=22><td colspan=2 align=center valign=bottom><div style="position:relative;top:-2px;left:0px"><a title="Мана Земли"><font color=#0F4000><b>' + nm + '<sup><span style="position:relative;top:2px">+' + nma + '</span></sup></b></font></a></div></td></tr>';
				}	
				st += '</table>';
				st += '</td>';
			
				if( fp <= 0 ) st += '<td style="width:74;height:77" background=images/rect/fd.png>';
				else  st += '<td style="width:74;height:77" background=images/rect/fl.png>';
				st += '<table width=74 height=77 cellspacing=0 cellpadding=0 border=0>';
				st += '<tr height=22><td align=left valign=top><div style="position:relative;top:2px;left:2px;z-index:3"><a title="Атака Магии Огня"><font color=#6E1F01><b>' + fa + '</b></font></a></div></td>';
				st += '<td align=right valign=top><div style="position:relative;top:2px;left:-2px;z-index:3"><a title="Защита Магии Огня"><font color=#6E1F01><b>' + fd + '</b></font></a></div></td></tr>';
				if( fp > 0 )
				{
					st += '<tr height=33><td colspan=2 align=center valign=top><div style="position:relative;top:-15px;left:0px;z-index:2"><font color=#6E1F01><big><b>' + fp + '</b></big></font></div></td></tr>';
					st += '<tr height=22><td colspan=2 align=center valign=bottom><div style="position:relative;top:-2px;left:0px"><a title="Мана Огня"><font color=#6E1F01><b>' + fm + '<sup><span style="position:relative;top:2px">+' + fma + '</span></sup></b></font></a></div></td></tr>';
				}
				st += '</table>';
				st += '</td>';
	
		st += '</tr></table>';
	st += '</td></tr>';
	
	st += '</table>';

	if( rdy ) st += '<div style="position:absolute;top:3px;left:3px;"><table style="width:26px;height:25px;" cellspacing=0 cellpadding=0 border=0 background=images/rect/ch.png><tr><td>&nbsp;</td></tr></table></div>';
	st += '</div>';
	
	return st;
}
