<?

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "combat_functions.php" );
include_once( "skin.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

setcookie( "last_note", 0 );

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style3.css" rel="stylesheet" type="text/css">
<script src=functions.js></script>
<script src=js/ajax.js></script>
<script src=js/timer.js></script>
<script src=js/tooltips.php></script>
<script src=js/combat_panel.js></script>
<script src=js/cc2.js></script>
<script src=js/skin2.js></script>

<table width=100%>                                       
<tr><td vAlign=top>
<div id=my_side name=my_side>

<?

	$res = f_MQuery( "SELECT combat_id, side, ready FROM combat_players WHERE player_id={$player->player_id}" );
	$arr = f_MFetch( $res );
	
	if( !$arr )
	{
		print( "<script>parent.location.href='game.php';</script>" );
		die( );
	}
	
	$player->ShowAttributes( $arr[2] );
	
	$combat_id = $arr[0];
	$side = $arr[1];
	$res = f_MQuery( "SELECT * FROM combat_players WHERE combat_id=$combat_id AND side=$side AND player_id <> {$player->player_id} AND ready < 2" );
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr['player_id'] );
		$plr->ShowAttributes( $arr['ready'] );
	}

	print( "</div>" );
	
	print( "<div style='position: absolute; top: 100px; left: 280px;'>" );
	print( "<table width=120px cellspacing=0 cellpadding=0><tr><td width=100% align=center><script>document.write( InsertTimer( 120, '<b>', '</b>', 0, 'query(\"combat_ref.php\",\"timeout\");' ) );</script></td></tr></table></div>" );
	print( "<div style='position: absolute; top: 78px; left: 275px;'>" );
	print( "<table cellspacing=0 cellpadding=0 border=0><tr><td><img src=images/top/b.png></td><td><button class=n_btn id=rdy_td style='cursor: pointer' onClick='pumpusja();'>Готов</button></td><td><img src=images/top/c.png></td></tr></table></div>" );
	print( "<span id=crdsnone style='cursor: pointer;position:absolute;' onClick=\"select_card( -1 )\"><script>document.write( csimg( 3, 'Не колдовать заклинание.', 'none_small.gif', '' ) );</script></span>" );
	print( "<div style='position:absolute;top:0px;left:220px;width:240px;'><center><b>Выберите заклинание</b></center></div>" );

		$clrs = Array( "blue", "green", "red" );
		$res = f_MQuery( "SELECT cards.* FROM player_selected_cards, cards WHERE player_selected_cards.card_id=cards.card_id AND player_selected_cards.player_id={$player->player_id} ORDER BY player_selected_cards.entry_id" );
		$num = f_MNum( $res );
		$lft = 340 - floor( ( $num * 25 ) / 2 );

		$i = 1;
		while( $arr = mysql_fetch_array( $res ) )
		{
			print( "<span id=crds$arr[card_id] style='cursor:pointer;position:absolute;' onClick=\"select_card( ".$arr[card_id]." )\"><script>document.write(".cardGetSmallIcon( $arr ).");</script></span>" );
			$st .= "spell_ids[$i] = $arr[card_id];\n";
			$st .= "spell_xs[$i] = ".($lft+($i-1)*25).";\n";
			$st .= "spell_ys[$i] = 20;\n";
            ++ $i;
		}

		echo "\n\n<script>\n";
		echo "var spells_left = $lft;\nvar spells_top = 20;\n";
		echo "var spell_ids = new Array( );\n";
		echo "var spell_xs = new Array( );\n";
		echo "var spell_ys = new Array( );\n";
		echo "var spell_sxs = new Array( );\n";
		echo "var spell_sys = new Array( );\n";
		echo "var spell_exs = new Array( );\n";
		echo "var spell_eys = new Array( );\n";
		echo "var spells_step;\n";
		echo "spell_ids[0] = -1;\n";
		echo "spell_xs[0] = 328;\n";
		echo "spell_ys[0] = 48;\n";
		echo "{$st}var spells_n = $i;";
		echo "</script>\n\n";


?>

</td><td vAlign=top width=100%>

<div id=crs name=crs align=center>
<table><tr><td style='width:220px;'>&nbsp;</td><td><center><b>Существа</b></center>
<div style='position:relative;top:0px;left:0px;width:164;'>
<table style='position:relative;top:0px;left:0px;width:164;height:99px;' style='height:99px;' cellspacing=0 cellpadding=0>
<tr><td align=right style='width:30' width=30>
 &nbsp;
</td>

<td style='width:52' width=23>
	<table style='height:99px;width:52px' cellspacing=0 cellpadding=0 border=0 background=images/rect/luni.png>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>
</td>

<td style='width:52' width=23>
	<table style='height:99px;width:52px' cellspacing=0 cellpadding=0 border=0 background=images/rect/runi.png>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>
</td>

<td style='width:30' width=30 align=right>
	&nbsp;
</td></tr>

</table>

<div style='position:absolute;left:20px;top:6px;' id=crtl><table width=23 height=21 cellspacing=0 cellpadding=0 background=images/rect/larr.png><tr><td>&nbsp;</td></tr></table></div>
<div style='position:absolute;left:125px;top:6px;' id=crtr><table width=22 height=21 cellspacing=0 cellpadding=0 background=images/rect/rarr.png><tr><td>&nbsp;</td></tr></table></div>

<div align=center style='cursor:pointer;position:absolute;left:47px;top:6px;width:25px;height:25px;'><table cellspacing=0 cellpadding=0 border=0 width=25 height=25><tr><td align=center valign=middle id=mycreat1 onclick='select_target( 0 );'>&nbsp;</td></tr></table>
</div>
<div align=center style='cursor:pointer;position:absolute;left:92px;top:6px;width:25px;height:25px;'><table cellspacing=0 cellpadding=0 border=0 width=25 height=25><tr><td align=center valign=middle id=hiscreat1 onclick='select_target( 0 );'>&nbsp;</td></tr></table>
</div>

<div align=center style='cursor:pointer;position:absolute;left:47px;top:35px;width:25px;height:25px;'><table cellspacing=0 cellpadding=0 border=0 width=25 height=25><tr><td align=center valign=middle id=mycreat2 onclick='select_target( 1 );'>&nbsp;</td></tr></table>
</div>
<div align=center style='cursor:pointer;position:absolute;left:92px;top:35px;width:25px;height:25px;'><table cellspacing=0 cellpadding=0 border=0 width=25 height=25><tr><td align=center valign=middle id=hiscreat2 onclick='select_target( 1 );'>&nbsp;</td></tr></table>
</div>

<div align=center style='cursor:pointer;position:absolute;left:47px;top:64px;width:25px;height:25px;'><table cellspacing=0 cellpadding=0 border=0 width=25 height=25><tr><td align=center valign=middle id=mycreat3 onclick='select_target( 2 );'>&nbsp;</td></tr></table>
</div>
<div align=center style='cursor:pointer;position:absolute;left:92px;top:64px;width:25px;height:25px;'><table cellspacing=0 cellpadding=0 border=0 width=25 height=25><tr><td align=center valign=middle id=hiscreat3 onclick='select_target( 2 );'>&nbsp;</td></tr></table>
</div>

</div>
</td></tr></table>

</div>

<div id=leave name=leave style='display: none;'>&nbsp;</div><div id=txttmo name=txttmo style='display: none;'>
<font color=darkblue>Кто-то из игроков не сходил в течение времени, отведённого на один ход. Если вы считаете, что у этого игрока временные проблемы с сетью, или по какой-либо иной причине полагаете, что он в скором времени сделает свой выбор, вы можете подождать некоторое вермя.<br>
В противном случае вы можете <a onclick='query("combat_force_timeout.php","to");' style='cursor:pointer'><font color=blue>форсировать ход</font></a>.</font><br><br>
</div><div id=log name=log>&nbsp;</div>

</td>

<td vAlign=top>
<div id=his_side name=his_side>

<?

	$side = 1 - $side;
	$res = f_MQuery( "SELECT * FROM combat_players WHERE combat_id=$combat_id AND side=$side AND ready < 2" );
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr['player_id'] );
		$plr->ShowAttributes( $arr['ready'] );
	}

?>

</div>

</td></tr>
</table>

<script>

	cur_card = -1;
	var q = new Array( );
	var shown = new Array( );
	var qn = 0;
	var pg = 0;

	function move_spells( )
	{
		var steps_num = 10.0;
		var p = spells_step / steps_num;
		var q = ( steps_num - spells_step ) / steps_num;

		for( var i = 0; i < spells_n; ++ i )
		{
			spell_xs[i] = Math.round( spell_sxs[i] * q + spell_exs[i] * p );
			spell_ys[i] = Math.round( spell_sys[i] * q + spell_eys[i] * p );
		}
		draw_spells( );

		if( spells_step < steps_num )
		{
			++ spells_step;
			setTimeout( 'move_spells( )', 50 );
	    }
	}

	function draw_spells( )
	{
		for( var i = 0; i < spells_n; ++ i )
		{
			var dv = 'crds' + spell_ids[i];
			if( spell_ids[i] == -1 )  dv = 'crdsnone';
			ge( dv ).style.left = spell_xs[i];
			ge( dv ).style.top = spell_ys[i];
		}
	}
	
	function ge( a )
	{
		return document.getElementById( a );
	}
	
	function select_card( a )
	{
		query("combat_set_card.php?id=" + a,"crd");
	}

	function select_card_ref( a )
	{
		cur_card = a;

		var id = 0;
		for( var i = 0; i < spells_n; ++ i )
		{
			spell_sxs[i] = spell_xs[i];
			spell_sys[i] = spell_ys[i];
			if( spell_ids[i] == a )
			{
				spell_exs[i] = 328;
				spell_eys[i] = 48;
			}
			else
			{
				spell_exs[i] = spells_left + id * 25;
				spell_eys[i] = 20;
				++ id;
			}
		}
		spells_step = 0;
		move_spells( );
	}
	
	function select_target( a )
	{
		query( 'combat_select_target.php?id=' + a, 'trg' );
	}
	
	function pumpusja( )
	{
		if( ge( 'rdy_td' ).innerHTML == 'Готов' ) query("combat_ready.php?id=" + cur_card,"rdy");
		else query("combat_ref.php","ref");
		ge( 'rdy_td' ).innerHTML = 'Обновить';
	}
		
	function set_ready_button( a )
	{
		if( !a )
		{
			ge( 'rdy_td' ).innerHTML = 'Готов';
		} else
		{
			ge( 'rdy_td' ).innerHTML = 'Обновить';
		}
	}
	
	function select_target_ref( a )
	{
		ge( 'crtl' ).style.top = a * 29 + 12 + 'px';	
		ge( 'crtr' ).style.top = a * 29 + 12 + 'px';	
	}
	
	var lid = -1;
	function addtolog( id, a )
	{
		if( id > lid )
		{
			q[qn ++] = a;
			lid = id;
		}
	}
	
	function reflog( )
	{
		st = '';
		
		for( a in shown ) q[shown[a]] = document.getElementById( "logdiv" + shown[a] ).innerHTML;
		
		pgn = ( qn + 4 ) / 5;
		pgn = parseInt( pgn );
		if( pgn > 1 )
		{
			st += 'Страница: ';
			for( i = 0; i < pgn; ++ i )
				if( i == pg ) st += "<b>" + ( i + 1 ) + '</b> ';
				else st += "<a href='#' onClick='gotopage( " + i + " )'>" + ( i + 1 ) + '</a> ';
				
			st += '<br>';
		}
		
		shown = new Array( );
		
		for( i = qn - pg * 5 - 1; i >= qn - pg * 5 - 5 && i >= 0; -- i )
		{
			st += "<div id=logdiv" + i + ">" + q[i] + "</div>";
			shown[i] = i;
		}

		st += '<li><a href="javascript:window.top.createPrivateRoom( \'Бой - Все\' )">Открыть боевой чат со всеми</a><br>';
		st += '<li><a href="javascript:window.top.createPrivateRoom( \'Бой - Свои\' )">Открыть боевой чат с союзниками</a><br>';

		ge( 'log' ).innerHTML = st;
	}
	
	function reset_creatures( )
	{
		for( i = 1; i <= 3; ++ i )
		{
			ge( 'mycreat' + i ).innerHTML = '&nbsp';
			ge( 'hiscreat' + i ).innerHTML = '&nbsp';
		}
	}
	
	function gotopage( a )
	{
		pg = a;
		reflog( );
	}
	
	function ref_timer_cast( )
	{
		query('combat_ref.php','tmr');
		setTimeout( 'ref_timer_cast( )', 10000 );
	}

</script>

<script>

	draw_spells( );
	query('combat_ref.php?compact','ref');
	setTimeout( 'ref_timer_cast( )', 10000 );

</script>
