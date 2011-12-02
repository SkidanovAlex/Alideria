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
<link href="style.css" rel="stylesheet" type="text/css">
<html>
<head>
</head>
<script>
<?
	echo 'Animation = ';
	if( isset( $_POST['combat_mode'] ) )
	{
		$player->SetTrigger( 321, ( ( $_POST['combat_mode'] == 1 ) ? 1 : 0 ) );
		echo ( ( $_POST['combat_mode'] == 1 ) ? 'false' : 'true' );
	}
	else
	{
		if( $player->HasTrigger( 321 ) )
			echo 'false';
		else
			echo 'true';
	}
	echo ";\n";
?>
</script>
<script src="functions.js"></script>
<script src="js/ajax.js"></script>
<script src="js/timer.js"></script>
<script src="js/tooltips.php"></script>
<script src="js/combat_panel.js"></script>
<script src="js/cc2.js"></script>
<script src="js/skin.js"></script>
<script src="js/skin2.js"></script>
<script src="js/combat_log2.js"></script>
<script src="js/event_handlers.js"></script>
<script src="js/turn_desc.js"></script>
<script src="js/combat_inner.js"></script>
<?
// noob
	$noob = 0;
	if( $player->level == 1 )
	{
		$res = f_MQuery( "SELECT a, b FROM noob WHERE player_id={$player->player_id}" );
		$arr = f_MFetch( $res );
		if( $arr )
		{
			$noob = $arr[0];
			$noob_param = $arr[1];
		}
		if( $noob )
		{
			include( 'noob.php' );
			echo "<script>";add_noob_js( );echo "</script>";
		}
	}
// -noob
?>
<body>
<table width=100% style='position:absolute;left:0px;top:0px;'>
<colgroup>
	<col width=10><col width=110><col width=10><col width=169>
	<col width=*>
	<col width=169><col width=10><col width=110><col width=10>
<tr>
<td>&nbsp;</td>
<td valign=top align=center><div id=my_space><div id=my_login><a href=player_info.php?id=<?=$player->player_id?> target=_blank><b><?=$player->login?></b></a></div><script>FLUc();</script><div style='position:relative;top:0px;left:0px;'><img border=0 width=100 height=225 src=images/avatars/<?=$player->getAvatar( )?> id=my_avatar><div id=my_health style='position:absolute;top:0px;left:0px;width:100px;height:0px;background-color:red;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=40);opacity: 0.4;-khtml-opacity: 0.4;-moz-opacity: 0.4;'><img width=0 height=0 src=empty.gif></div><div id=my_hp style='position:absolute;top:207px;left:0px;width:100px;height:0px;text-align:center'></div><script>FLL();</script></td></td>
<td>&nbsp;</td>
<td valign=top align=center>&nbsp;<br><table id="spellArea1"style='width:169px;height:183px;' background='images/rect/ql.png' cellspacing=0 cellpadding=0 border=0><tr><td align=center valign=middle><div id=my_spell style='width:141px;height:141px;position:relative;left:10px;top:0px;'>&nbsp;</div></td></tr></table></td>

<td>
	<div id="logc" style="position:absolute;top:35px;left:50%;z-index:10;margin:0px;display:none;">
		<script>FLUl();</script>
		<div id=log name=log style='overflow-y:auto;'>&nbsp;</div>
		<script>FLL();</script>
	</div>
	<div id="settings" style="position:absolute;top:35px;left:50%;margin-left:-100px;height:75px;width:200px;z-index:10;display:none;">
		<script>FLUl();</script>
		<form method="POST">
			<label><input type="radio" id="rch1" name="combat_mode" value="0"> Включить анимацию</label>
			<br>
			<label><input type="radio" id="rch2" name="combat_mode" value="1"> Отключить анимацию</label>
			<br><br>
			<center><a href="#" onclick="document.forms[0].submit( );">Применить</a></center>
			<script>
				if( Animation )
					_( 'rch1' ).checked = true;
				else
					_( 'rch2' ).checked = true;
			</script>
		</form>
		<script>FLL();</script>
	</div>
</td>

<td valign=top align=center>&nbsp;<br><table id="spellArea2"style='width:169px;height:183px;' background='images/rect/qr.png' cellspacing=0 cellpadding=0 border=0><tr><td align=center valign=middle><div id=his_spell  style='width:141px;height:141px;position:relative;left:-10px;top:0px;'>&nbsp;</div></td></tr></table></td>
<td>&nbsp;</td>
<td valign=top align=center><div id=his_space><div id=his_login>&nbsp;</div><script>FLUc();</script><div style='position:relative;top:0px;left:0px;'><img border=0 width=100 height=225 src=empty.gif id=his_avatar><div id=his_health style='position:absolute;top:0px;left:0px;width:100px;height:0px;background-color:red;filter:progid:DXImageTransform.Microsoft.Alpha(opacity=40);opacity: 0.4;-khtml-opacity: 0.4;-moz-opacity: 0.4;'><img width=0 height=0 src=empty.gif></div><div id=his_hp style='position:absolute;top:207px;left:0px;width:100px;height:0px;text-align:center'></div><script>FLL();</script></div></td>
<td>&nbsp;</td>
</tr></table>

<?
//header
	print( "<center>" );
	print( "<table cellspacing=0 cellpadding=0 border=0 style='position:relative;left:0px;top:0px;'><tr>" );

	$header_act = array( "showLog();", "qref();", "showSettings();" );
	$header_ttl = array( "Лог боя", "Обновить", "Настройки" );

	for( $i = 0; $i < count( $header_ttl ); ++ $i )
	{
		print( "<td><img border=0 width=17 height=9 src=images/top/a.png></td>" );
		print( "<td><img border=0 width=92 height=9 src=images/top/e.png></td>" );
	}
	print( "<td><img border=0 width=17 height=9 src=images/top/a.png></td>" );

	print( "</tr><tr>" );

	foreach( $header_ttl as $a => $b )
	{
		if( $a ) print( "<td><img border=0 width=17 height=21 src=images/top/d.png></td>" );
		else print( "<td><img border=0 width=17 height=21 src=images/top/b.png></td>" );
		print( "<td width=92 height=21 background=images/top/f.png align=center valign=middle>" );
		print( "<a href='#' onclick='{$header_act[$a]}'>$b</a>" );
		print( "</td>" );
	}
	print( "<td><img border=0 width=17 height=21 src=images/top/c.png></td></tr></table></center>" );
?>
<table style="width:100%;position:absolute;top:255px;z-index:0;">
	<colgroup>
		<col width=10><col width=145><col width=*><col width=145><col width=10>
	</colgroup>
	<tr>
		<td>&nbsp;</td>
		<td valign="top">
				<div style='position:relative;top:0px;left:0px;' id=my_side_o><script>FLUl();</script><div id=my_side name=my_side style='position:relative;top:0px;left:0px;'>
					&nbsp;
				</div>
				<script>FLL();</script>
			</div>
		</td>
		<td>&nbsp;</td>
		<td valign=top>
			<div style="position:relative;top:0px;left:0px;" id="his_side_o">
				<script>FLUl();</script>
				<div id="his_side" name="his_side" style="position:relative;top:0px;left:0px;">
					&nbsp;
				</div>
				<script>FLL();</script>
			</div>
		</td>
		<td>&nbsp;</td>
	</tr>
</table>
<div id="elGroup">
	<?
	// cards
		print( "<div id=tmodv style='position: absolute; top: 0px; left: 0px;'>" );
		print( "<table width=120px cellspacing=0 cellpadding=0><tr><td width=100% align=center><script>document.write( InsertTimer( 120, '<b>', '</b>', 0, 'query(\"combat_ref.php\",\"timeout\");' ) );</script></td></tr></table></div>" );

		$clrs = Array( "blue", "green", "red" );
		$res = f_MQuery( "SELECT cards.* FROM player_selected_cards, cards WHERE player_selected_cards.card_id=cards.card_id AND player_selected_cards.player_id={$player->player_id} ORDER BY player_selected_cards.staff, player_selected_cards.entry_id" );
		$num = f_MNum( $res );

		$i = 0;
		$st .= "spell_img[-1] = 'nospell.png';\n";
		while( $arr = mysql_fetch_array( $res ) )
		{
			print( "<span id=crds$arr[card_id] style='display:none;cursor:pointer;position:absolute;' onClick=\"select_card( ".$arr[card_id]." )\"><script>document.write(".cardGetLargeIcon( $arr, "crdi{$arr[card_id]}", $player ).");</script></span>" );
			$st .= "spell_ids[$i] = $arr[card_id];\n";
			$st .= "spell_img[$arr[card_id]] = '$arr[image_large]';\n";
			++ $i;
		}
		print( "<span id=crds12345 style='display:none;cursor:pointer;position:absolute;' onClick=\"select_card( -1 )\"><script>document.write(csimgl( 12345, '<font color=#000000>Не колдовать</font>', 'nospell.png', 'Пропустить ход, не колдуя заклинания.', 'crdi12345' ));</script></span>" );
		$st .= "spell_ids[".$i++."] = 12345;\n";
		$st .= "spell_img[12345] = 'nospell.png';\n";

		print( "<span id=crdsl style='display:none;cursor:pointer;position:absolute;' onClick=\"roll_cards( -1 )\"><img width=72 height=80 border=0 src=images/rect/l.png></span>" );
		print( "<span id=crdsr style='display:none;cursor:pointer;position:absolute;' onClick=\"roll_cards( 1 )\"><img width=73 height=80 border=0 src=images/rect/r.png></span>" );

		echo "\n\n<script>\n";
		echo "var spell_ids = new Array( );\n";
		echo "var spell_img = new Array( );\n";
		echo "{$st}var spells_n = $i;";
		echo "</script>\n\n";


	?>

	<div id=items style='position:absolute;left:0px;top:0px;text-align:right'>moo!!!</div>

	<div id=last_turn style='position:absolute;left:0px;top:0px;width:350px;height:100px;display:none;'>
	<script>FLUl();</script>
	<div id=last_turn_inner style='left:0px;top:0px;width:340px;height:90px;overflow-y:hidden;'>
	</div>
	<script>FLL();</script>
	</div>

	<div id=crs name=crs align=center style='position:absolute;left:0px;top:0px;'>
		<div style='position:relative;top:0px;left:0px;width:164;'>
		<table style='position:relative;top:0px;left:0px;width:164;height:99px;' style='height:99px;' cellspacing=0 cellpadding=0>
		<tr><td align=right style='width:30' width=30>
		 &nbsp;
		</td>

		<td style='width:52'>
			<script>opng( 'images/rect/luni.png', 52, 99 );</script>
		</td>

		<td style='width:52'>
			<script>opng( 'images/rect/runi.png', 52, 99 );</script>
		</td>

		<td style='width:30' width=30 align=right>
			&nbsp;
		</td></tr>

		</table>

		<div style='position:absolute;left:20px;top:6px;' id=crtl><script>opng('images/rect/larr.png',23,21);</script></div>
		<div style='position:absolute;left:125px;top:6px;' id=crtr><script>opng('images/rect/rarr.png',22,21);</script></div>

    <div onclick='select_target( 0 );' align=center style='cursor:pointer;position:absolute;left:47px;top:6px;width:25px;height:25px;'><table cellspacing=0 cellpadding=0 border=0 width=25 height=25><tr><td align=center valign=middle id=mycreat1 onclick='select_target( 0 );'>&nbsp;</td></tr></table>
    </div>
    <div onclick='select_target( 0 );' align=center style='cursor:pointer;position:absolute;left:92px;top:6px;width:25px;height:25px;'><table cellspacing=0 cellpadding=0 border=0 width=25 height=25><tr><td align=center valign=middle id=hiscreat1 onclick='select_target( 0 );'>&nbsp;</td></tr></table>
    </div>

    <div onclick='select_target( 1 );' align=center style='cursor:pointer;position:absolute;left:47px;top:35px;width:25px;height:25px;'><table cellspacing=0 cellpadding=0 border=0 width=25 height=25><tr><td align=center valign=middle id=mycreat2 onclick='select_target( 1 );'>&nbsp;</td></tr></table>
    </div>
    <div onclick='select_target( 1 );' align=center style='cursor:pointer;position:absolute;left:92px;top:35px;width:25px;height:25px;'><table cellspacing=0 cellpadding=0 border=0 width=25 height=25><tr><td align=center valign=middle id=hiscreat2 onclick='select_target( 1 );'>&nbsp;</td></tr></table>
    </div>

    <div onclick='select_target( 2 );' align=center style='cursor:pointer;position:absolute;left:47px;top:64px;width:25px;height:25px;'><table cellspacing=0 cellpadding=0 border=0 width=25 height=25><tr><td align=center valign=middle id=mycreat3 onclick='select_target( 2 );'>&nbsp;</td></tr></table>
    </div>
    <div onclick='select_target( 2 );' align=center style='cursor:pointer;position:absolute;left:92px;top:64px;width:25px;height:25px;'><table cellspacing=0 cellpadding=0 border=0 width=25 height=25><tr><td align=center valign=middle id=hiscreat3 onclick='select_target( 2 );'>&nbsp;</td></tr></table>
    </div>

		</div> 
		</td></tr></table>

	</div> <? /* сущетсва */ ?>
</div>

<div id=txttmo name=txttmo style='z-index:3;display: none;position:absolute;width:400px;left:0px;top:0px;'>
<font color=darkblue><script>FLUc();</script>Кто-то из игроков не сходил в течение установленного для боя таймаута. Если вы считаете, что у игрока временные проблемы с сетью, или по какой-либо иной причине полагаете, что он в скором времени завершит свой ход, вы можете подождать некоторое врeмя.<br>
В противном случае вы можете <a onclick='query("combat_force_timeout.php","to");' style='cursor:pointer'><font color=blue>форсировать ход</font></a>.</font><br><br>
<script>FLL();</script></div>

<div id=leave name=leave style='background-image:url(images/bg.gif);z-index:4;display:none;border:1px solid black;position:relative;'>&nbsp;</div>

<div id=lttrash style='display:none;position:absolute;top:0px;left:0px;'><script>opng('images/rect/d1.png', 205, 27 );</script></div>
<div id=rttrash style='display:none;position:absolute;top:0px;left:0px;'><script>opng('images/rect/d1.png', 205, 27 );</script></div>

<div id=sv_container>&nbsp;
</div>

<div id=lbtrash style='display:none;position:absolute;top:0px;left:0px;'><script>opng('images/rect/d2.png', 205, 18 );</script></div>
<div id=rbtrash style='display:none;position:absolute;top:0px;left:0px;'><script>opng('images/rect/d2.png', 205, 18 );</script></div>

<script>

//	draw_spells( );
	query('combat_ref.php?compact','rdy');
	setTimeout( 'ref_timer_cast( )', 10000 );

	do_design( );
	blur_opa( _( 'my_spell' ), 0 );

	addHandler( window, 'resize', do_design );

	for( var i = 0; i < spells_n; ++ i )
	{
        if (_('crds' + spell_ids[i]).addEventListener)
        {
            _('crds' + spell_ids[i]).addEventListener('DOMMouseScroll', wheel, false);
        }
        _('crds' + spell_ids[i]).onmousewheel = wheel;
	}

	setInterval( ref_proccess, 250 );

	blur_opa( _( 'lttrash' ), 0 );
	blur_opa( _( 'lbtrash' ), 0 );
	blur_opa( _( 'rttrash' ), 0 );
	blur_opa( _( 'rbtrash' ), 0 );

	<?
	  if( $noob ) show_noob( $noob, $noob_param );
	?>
</script>

</body>
</html>
