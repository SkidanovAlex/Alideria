<script>begin_help( '������������ - ���������' );</script>

<script src=js/ii.js></script>
<script src=js/tooltips.php></script>
<script src=functions.js></script>

<script>
function ccb(id,nick,lv,hp,mhp,wp,wm,wma,wa,wd,np,nm,nma,na,nd,fp,fm,fma,fa,fd,clan)
{
	var st = '<table width=220 height=112 cellspacing=0 cellpadding=0 border=0>';
	
	tt = window.top.ii( lv, nick, 'black',clan );
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
				st += '<tr height=22><td align=left valign=top><div style="position:relative;top:2px;left:2px;z-index:3"><a title="����� ����� ����"><font color=#003E95><b>' + wa + '</b></font></a></div></td>';
				st += '<td align=right valign=top><div style="position:relative;top:2px;left:-2px;z-index:3"><a title="������ ����� ����"><font color=#003E95><b>' + wd + '</b></font></a></div></td></tr>';
				if( wp > 0 )
				{
					st += '<tr height=33><td colspan=2 align=center valign=top><div style="position:relative;top:-15px;left:1px;z-index:2"><font color=#003E95><big><b>' + wp + '</b></big></font></div></td></tr>';
					st += '<tr height=22><td colspan=2 align=center valign=bottom><div style="position:relative;top:-2px;left:0px"><a title="���� ����"><font color=#003E95><b>' + wm + '<sup><span style="position:relative;top:2px">+' + wma + '</span></sup></b></font></a></div></td></tr>';
				}
				st += '</table>';
				st += '</td>';

				if( np <= 0 ) st += '<td style="width:73;height:77" background=images/rect/nd.png>';
				else st += '<td style="width:73;height:77" background=images/rect/nl.png>';
				st += '<table width=73 height=77 cellspacing=0 cellpadding=0 border=0>';
				st += '<tr height=22><td align=left valign=top><div style="position:relative;top:2px;left:2px;z-index:3"><a title="����� ����� �����"><font color=#0F4000><b>' + na + '</b></font></a></div></td>';
				st += '<td align=right valign=top><div style="position:relative;top:2px;left:-2px;z-index:3"><a title="������ ����� �����"><font color=#0F4000><b>' + nd + '</b></font></a></div></td></tr>';
				if( np > 0 )
				{
					st += '<tr height=33><td colspan=2 align=center valign=top><div style="position:relative;top:-15px;left:0px;z-index:2"><font color=#0F4000><big><b>' + np + '</b></big></font></div></td></tr>';
					st += '<tr height=22><td colspan=2 align=center valign=bottom><div style="position:relative;top:-2px;left:0px"><a title="���� �����"><font color=#0F4000><b>' + nm + '<sup><span style="position:relative;top:2px">+' + nma + '</span></sup></b></font></a></div></td></tr>';
				}	
				st += '</table>';
				st += '</td>';
			
				if( fp <= 0 ) st += '<td style="width:74;height:77" background=images/rect/fd.png>';
				else  st += '<td style="width:74;height:77" background=images/rect/fl.png>';
				st += '<table width=74 height=77 cellspacing=0 cellpadding=0 border=0>';
				st += '<tr height=22><td align=left valign=top><div style="position:relative;top:2px;left:2px;z-index:3"><a title="����� ����� ����"><font color=#6E1F01><b>' + fa + '</b></font></a></div></td>';
				st += '<td align=right valign=top><div style="position:relative;top:2px;left:-2px;z-index:3"><a title="������ ����� ����"><font color=#6E1F01><b>' + fd + '</b></font></a></div></td></tr>';
				if( fp > 0 )
				{
					st += '<tr height=33><td colspan=2 align=center valign=top><div style="position:relative;top:-15px;left:0px;z-index:2"><font color=#6E1F01><big><b>' + fp + '</b></big></font></div></td></tr>';
					st += '<tr height=22><td colspan=2 align=center valign=bottom><div style="position:relative;top:-2px;left:0px"><a title="���� ����"><font color=#6E1F01><b>' + fm + '<sup><span style="position:relative;top:2px">+' + fma + '</span></sup></b></font></a></div></td></tr>';
				}
				st += '</table>';
				st += '</td>';
	
		st += '</tr></table>';
	st += '</td></tr>';
	
	st += '</table>';
	
	return st;
}

</script>
<?

if( !isset( $_GET['beast_id'] ) )
{
	$res = f_MQuery( "SELECT * FROM mobs WHERE defend_depth!=1000 AND name NOT LIKE '!%' ORDER BY name" );
	while( $arr = f_MFetch( $res ) ) echo "<li><a href=help.php?id=1016&beast_id=$arr[mob_id]>$arr[name]</a>";
}
else
{
	$stats = array( );
	$aimgs = array( );
	$aclrs = array( );

	$res = f_MQuery( "SELECT * FROM attributes" );
	while( $arr = f_MFetch( $res ) )
	{
		$stats[$arr['attribute_id']] = $arr['name'];
		$aimgs[$arr['attribute_id']] = $arr['icon'];
		$aclrs[$arr['attribute_id']] = $arr['color'];
	}

	include_once( 'beast.php' );
	include_once( 'card.php' );

	$mob_id = ( int )$_GET['beast_id']; 
	$b = new Beast( $mob_id );
	echo "<center><a href=help.php?id=1016>����� � ������</a><br><br><table><tr><td valign=top><script>document.write( "; $b->ARect( ); echo " );</script><br><center>"; $b->ShowGlobalAttributes( );

	echo "<br><br>";

	$res = f_MQuery( "SELECT i.*, m.number, m.chance FROM items as i INNER JOIN mob_items as m ON i.item_id = m.item_id WHERE m.mob_id=$mob_id" );
	echo "<table width=200><tr><td>";ScrollTableStart( );
	echo "<b>��� �������:</b><br>";
	echo "<table>";
	echo "<tr><td align=center>";
	if( $b->loc == 2 ) echo "� �������";
	else if( $b->loc == 3 )
	{
		echo "� ����, ";
		$larr = f_MFetch( f_MQuery( "SELECT title FROM  loc_texts WHERE loc=3 AND depth={$b->dfd}" ) );
		echo $larr[0];
	}
	else if( $b->loc == 1 )
	{
		include_once( 'forest_functions.php' );
		echo $forest_names[$b->dfd]." � �������� ����";
	}
	else if( $b->mnd >= 33 && $b->mnd <= 40 ) echo "� ��������� ��������";
	else if( $b->mnd == 1000 ) echo "<i>���������� ������</i>";
	else echo "� ������<br>�������: {$b->mnd} - {$b->mxd}";
	echo "</td></tr>";
	echo "</table>";
	ScrollTableEnd( );echo "</td></tr></table>";



	 echo "</center></td><td valign=top>";


	echo "<table width=300><tr><td>";ScrollTableStart( );
	$b->ShowCards( );
	ScrollTableEnd( );echo "</td></tr></table>";

	echo "<br><br>";

	echo "<table width=300><tr><td>";ScrollTableStart( );
	$b->ShowDrop();
   	ScrollTableEnd( );echo "</td></tr></table>";

   	echo "<br><br>";
	echo "<table width=300><tr><td>";ScrollTableStart( );
	echo "<b>��������:</b><br>";
	echo "<table>";
	echo "<tr><td><i>{$b->descr}</i></td></tr>";
	echo "</table>";
	ScrollTableEnd( );echo "</td></tr></table>";

    echo "</td></tr></table></center>";

}

?>

<script>end_help( );</script>
