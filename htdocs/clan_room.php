<?

if( !isset( $mid_php ) )
{
	header("Content-type: text/html; charset=windows-1251");
	include( "functions.php" );
	include( "player.php" );
	f_MConnect( );
	if( !check_cookie( ) )
		die( "�������� ��������� Cookie" );

	$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
	$clan_id = $player->clan_id;
}

if( $player->regime == 115 || $player->regime == 116 )
{

	include_once( "grain_game.php" );
	$gg = new GrainGame( );
	$gg->size = 10;
	$gg->LoadGame( $player->player_id );

	if( $player->regime == 115 && !$mid_php && isset( $_GET['act'] ) )
	{
		$player->SetRegime( 116 );
		if( $_GET['act'] == 2 )
		{
			echo "_( 'sost' ).innerHTML = '<b><big><big><font color=darkblue>";
			for( $i = 0; $i < 10; ++ $i ) if( $gg->a[$i] ) echo "&nbsp;".$gg->a[$i]."";
			echo "</font></big></big><br>����� ���� ������:<big><big><font color=darkgreen><br>";
			$gg->makeTurn( true );
			$gg->StoreGame( $player->player_id );
			for( $i = 0; $i < 10; ++ $i ) if( $gg->a[$i] ) echo "&nbsp;".$gg->a[$i]."";
			echo "</b>';";
		}
	}
	else if( $player->regime == 116 && !$mid_php && isset( $_GET['act'] ) )
	{
		$num = (int)$_GET['act'];
		if( $num > 0 )
			for( $i = 0; !$gg->a[$i]; ++ $i ) ;
		else for( $i = 9; !$gg->a[$i]; -- $i );
		$id = $i;
		if( $num < 0 ) $num = - $num;
		if( $gg->a[$i] < $num ) die( "alert( '� �������� ��� ������� ��������' );" );
		else if( $num < 1 ) RaiseError( "������� ��� ���� � ������� ����� ������ ������ ������� � �����" );
		echo "_( 'sost' ).innerHTML = '<b><big><big><font color=darkblue>";
		for( $i = 0; $i < 10; ++ $i ) if( $gg->a[$i] ) echo "&nbsp;".$gg->a[$i]."";
		echo "</font></big></big><br>����� ������ ����:<big><big><font color=darkblue><br>";
		$gg->a[$id] -= $num;
		for( $i = 0; $i < 10; ++ $i ) if( $gg->a[$i] ) echo "&nbsp;".$gg->a[$i]."";
		echo "</font></big></big><br>����� ���� ������:<big><big><font color=darkgreen><br>";
		$won = true;
		for( $i = 0; $i < 10; ++ $i ) if( $gg->a[$i] ) $won = false;
		if( $won )
		{
			echo "'; alert( '�� ��������! ������ ������� ����� ���������� 200 ������ ������' );";
			$player->SetRegime( 0 );
			f_MQuery( "UPDATE clans SET ta_lost=ta_lost + 200 WHERE clan_id=$clan_id" );
			$player->AlterQuestValue( 37, 200 );
			$player->SetQuestValue( 38, 1 );
			echo "location.href='game.php';";
			LogError( "����! ����� ������ ���� ������!!!" );
			die( );
		}
		$gg->makeTurn( true );
		$gg->StoreGame( $player->player_id );
		for( $i = 0; $i < 10; ++ $i ) if( $gg->a[$i] ) echo "&nbsp;".$gg->a[$i]."";
		echo "</b>';";
	}
	$won = true;
	for( $i = 0; $i < 10; ++ $i ) if( $gg->a[$i] ) $won = false;

	if( $mid_php )
	{
    	echo "<b>���� � �������</b><br>������� ���� ��������� ������. ����� ���� 10 ������ ��������. �� � ������ �� ������� ������ ����� ����� ���������� �������� �� ������ ������ ��� �� ������ ������� ��������. ��������� ���, ��� ����� ��������� ������� �� �����. ����� ������������� ��������� ����, �� ������ �������, ��� ����� ������ ������.<br><br>";
    	echo "<table><tr><td width=300 valign=top><b>��������� ����:<br><div id=sost><b><big><big><font color=darkgreen>";
    	for( $i = 0; $i < 10; ++ $i ) if( $gg->a[$i] ) echo "&nbsp;".$gg->a[$i]."";
    	echo "</font></big></big></b></div></b><br><br></td><td valign=top>";

    	echo "<script>function action(id) { query( 'clan_room.php?act=' + id,'' ); }</script>";
	}

	if( $mid_php )
	{
		echo "<b>���� ��������:</b><br>";
		echo "<div id=actions>";
	}
	else echo "_( 'actions' ).innerHTML = '";
	if( $player->regime == 115 )
	{
		echo "<li><a href=\"javascript:action(1)\">������ ���� ������</a>";
		echo "<li><a href=\"javascript:action(2)\">�������� ����� ������� ���� ������</a>";
	}
	else if( $player->regime == 116 && !$won )
	{
		for( $i = 0; !$gg->a[$i]; ++ $i ) ;
		echo "����� �� ������ ��������: ";
		for( $j = 1; $j <= $gg->a[$i]; ++ $j )
			echo "&nbsp;<a href=\"javascript:action($j)\">$j</a>";
		for( $i = 9; !$gg->a[$i]; -- $i ) ;
		echo "<br>����� �� ������� ��������: ";
		for( $j = 1; $j <= $gg->a[$i]; ++ $j )
			echo "&nbsp;<a href=\"javascript:action(-$j)\">$j</a>";
	}
	if( $mid_php ) echo "</div></td></tr></table>";
	else echo "';";

	if( !$mid_php && $won )
	{
		echo "alert( '�� ���������' );";
		$player->SetRegime( 0 );
		echo "location.href='game.php';";
	}
	else if( $won ) // ����� ����... 
	{
		echo "<script>";
		echo "alert( '�� ���������' );";
		$player->SetRegime( 0 );
		echo "location.href='game.php';";
		echo "</script>";
	}

	return;
}

if( !isset( $mid_php ) ) die( );

include_once( 'clan.php' );

$mode = $_GET['order'];
$clan_id = $player->clan_id;

$ta_lost = 0; $has_camp = 0;
$tres = f_MQuery( "SELECT ta_lost, hascamp FROM clans WHERE clan_id=$clan_id" );
$tarr = f_MFetch( $tres );
if( $tarr[0] ) $ta_lost = $tarr[0];
if( $tarr[1] ) $hascamp = $tarr[1];

if( $player->regime >= 300 && $player->regime < 310 )
{
	$player->SetRegime( 0 );
	$player->SetTill( 0 );
}
if( ( $player->regime == 114 || $player->regime == 117 ) && $hascamp )
{
	$player->SetRegime( 0 );
	$player->SetTill( 0 );
}
if( $player->regime == 112 && $ta_lost == 0 )
	$player->SetRegime( 0 );

if( $mode === 'main' )
{
	echo "<table><tr><td width=250 valign=top>";

	echo "<ul>";
	if( $hascamp )
		echo "<li><a href=game.php?order=camp>������ � ������ ������</a><br><br>";
	else
	{
		if( allow_phrase( 634 ) ) echo "<li><a href=game.php?talk=48>���������� � �������</a><br><br>";
		if( allow_phrase( 638 ) ) echo "<li><a href=game.php?talk=49>���������� � �������</a><br><br>";
	}

	echo "<li><a href=game.php?order=treasury>������������</a>";
	if( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_CHANGE_PAGE ) )
		echo "<li><a href=game.php?order=page>�������� ������</a>";
//	echo "<li><a href=game.php?order=buildings>��������� ������</a>";
	echo "<li><a href=game.php?order=barracks>������ ������</a>";
	if( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_CONTROL ) )
		echo "<li><a href=game.php?order=ranks>������ � ���������</a>";
	echo "<li><a href=game.php?order=silo>����� ������</a>";
//	echo "<li><a href=game.php?order=cafe>�������� ������</a>";
//	if( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_CONTROL_SHOP ) )
//	{
//		echo "<li><a href=game.php?order=shop_log>���������� ���� ��������</a>";
//		echo "<li><a href=game.php?order=shop_control_log>���������� ���� ���������� ���������</a>";
//	}
	if( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_SEND_POST ) )
		echo "<li><a href=game.php?order=post>����� ������</a>";
	if( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_WATCH_LOG ) )
		echo "<li><a href=game.php?order=log>���������� ���� ������</a>";
//	echo "<li><a href=game.php>��������� � ��� ��������</a>";

	echo "<br><br><li><a href=orders.php target=_blank>������� �������</a>";

	echo "</ul>";

	echo "</td><td valign=top>";

	if( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_ADMIT ) )
	{
		if( isset( $_GET['c_refuse'] ) )
		{
			$id = $_GET['c_refuse'];
			settype( $id, 'integer' );
			$res = f_MQuery( "SELECT player_id FROM clan_bets WHERE clan_id=$clan_id AND player_id=$id" );
			if( f_MNum( $res ) )
			{
				$plr = new Player( $id );
				$plr->syst3( '� ���������, ��� �������� �� ���������� � �����' );
				f_MQuery( "DELETE FROM clan_bets WHERE clan_id=$clan_id AND player_id=$id" );
			}
		}
		else if( isset( $_GET['c_admit'] ) )
		{
			$id = $_GET['c_admit'];
			settype( $id, 'integer' );
			$res = f_MQuery( "SELECT player_id FROM clan_bets WHERE clan_id=$clan_id AND player_id=$id" );
			if( f_MNum( $res ) )
			{
				$max_cap = getBLevel( 0 ) * 5 + 5;
				$cres = f_MQuery( "SELECT count( player_id ) FROM player_clans WHERE clan_id=$clan_id" );
				$carr = f_MFetch( $cres );
				$cur_cap = $carr[0];
				if( $cur_cap >= $max_cap ) $player->syst( "� ��� ��� ��������� ���� � ������. ��� ������� �������� �������, ����� ��������� ����� �������." );
				else
				{
					$res = f_MQuery( "SELECT name FROM clans WHERE clan_id=$clan_id" );
					$arr = f_MFetch( $res );
    				$plr = new Player( $id );
    				$plr->syst3( "�����������, ��� ������� � $arr[0]!" );
					orderBroadcast( $clan_id, "����� <b>{$plr->login}</b> ��� ������ � ��� �����" );
	   				f_MQuery( "DELETE FROM clan_bets WHERE clan_id=$clan_id AND player_id=$id" );
    				f_MQuery( "INSERT INTO player_clans ( clan_id, player_id ) VALUES ( $clan_id, $id )" );
    				f_MQuery( "UPDATE characters SET clan_id=$clan_id WHERE player_id=$id" );
  					f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0, arg1 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 10, $id, 1 )" );
					if ($clan_id == 7)
						f_MQuery("INSERT INTO paid_smiles (player_id, set_id, expires) VALUES ({$id}, ".(10000+$clan_id).", -1)");

  					$res = f_MQuery( "SELECT wonder_id FROM clan_wonders WHERE clan_id=$clan_id AND stage=100" );
  					while( $arr = f_MFetch( $res ) )
  						applyWonder( $arr[0], $plr );

    				$plr->UploadInfoToJavaServer( );
			 	}	
			}
		}

		include_js( 'js/ii_a.js' );
		$res = f_MQuery( "SELECT player_id FROM clan_bets WHERE clan_id=$clan_id" );
		if( f_MNum( $res ) )
		{
			echo "<b>������ � �����</b><br>";
			echo "<table>";
			while( $arr = f_MFetch( $res ) )
			{
				$plr = new Player( $arr['player_id'] );
				echo "<tr><td><script>document.write( ".( $plr->Nick( ) )." );</script></td>";
				echo "<td><a href=game.php?order=main&c_admit=$arr[player_id]>�������</a></td>";
				echo "<td><a href=game.php?order=main&c_refuse=$arr[player_id]>���������</a></td>";
				echo "</tr>";
			}
			echo "</table><br><br>";
		}
	}

	if( true )
	{
		if( $ta_lost >= 12 )
		{
			$points_to_achieve = 2000;
			if( $clan_id <= 20 ) $points_to_achieve = 500;

			if( ( $player->regime == 114 || $player->regime == 117 ) && $player->till <= time( ) + 2 )
			{
				include_once( "prof_exp.php" );
				$val = 1;
				$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=1" ) );
				if( $barr[0] )  $val = 2;
				if( $player->regime == 114 )
				{
					f_MQuery( "UPDATE clans SET ta_lost=ta_lost + $val WHERE clan_id=$clan_id" );
					$player->AlterQuestValue( 37, $val );
					$ta_lost += $val;
					AlterProfExp( $player, 12 );
				}
				else
				{
					$val *= 6;
					f_MQuery( "UPDATE clans SET ta_lost=ta_lost + $val WHERE clan_id=$clan_id" );
					$player->AlterQuestValue( 37, $val );
					$ta_lost += $val;
					AlterProfExp( $player, 12*6 );
				}
				$player->SetRegime( 0 );
				$player->SetTill( 0 );
			}

			$pisun = f_MValue( "select count( player_clans.player_id ) from player_clans inner join player_quest_values on player_clans.player_id=player_quest_values.player_id where value>0 and value_id=38 and clan_id=$clan_id;" );

			if( isset( $_GET['clean'] ) )
			{
				$act = $_GET['clean'];
				if( $act == 3 && $player->SpendMoney( 5000 ) )
				{
					$player->AddToLogPost( 0, -5000, 34 );
					f_MQuery( "UPDATE clans SET ta_lost=ta_lost + 10 WHERE clan_id=$clan_id" );
					$player->AlterQuestValue( 37, 10 );
					die( "<script>location.href='game.php';</script>" );
				}
				else if( $act == 3 ) echo "<font color=darkred>� ��� �� ������� ��������</font><br>";
				
				else if( !$pisun && $player->regime == 0 && $act == 2 && $player->SpendMoney( 200 ) )
				{
					$player->AddToLogPost( 0, -200, 34 );
					$player->SetRegime( 115 );
					$arr = array( );
					include_once( 'grain_game.php' );
					$gg = new GrainGame( ); $gg->size = 10; $gg->CreateField( );
					$gg->StoreGame( $player->player_id );
					die( "<script>location.href='game.php';</script>" );
				}

				else if( $act == 1 )
				{
					if( $player->regime == 0 )
					{
	    				$num = f_MValue( "SELECT number FROM player_num WHERE player_id={$player->player_id}" );
	    				if( $num != $_GET['num'] ) echo "<font color=darkred>������� ���������� ��� � ����</font><br>";
						else {
							$player->SetRegime( 114 );
							$player->SetTill( time( ) + 10 * 60 );
						}
					}
					else if( $player->regime == 114 )
					{
						$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=4" ) );
						if( $barr[0] ) 
						{
							$player->SetRegime( 117 );
							$player->SetTill( $player->till + 50 * 60 );
						}
					}
				}
				else if( $act == 4 )
				{
					if( $player->regime == 114 || $player->regime == 117 )
					{
						$player->SetRegime( 0 );
						$player->SetTill( 0 );
					}
				}
			}

			$done = $ta_lost - 12;
			$remaining = $points_to_achieve - $done;

			if( $remaining <= 0 )
			{
				f_MQuery( "UPDATE clans SET hascamp=1, ta_lost=0 WHERE clan_id=$clan_id" );
				f_MQuery("INSERT INTO clan_buildings (clan_id, building_id, level) VALUES ($clan_id, 14, 1)");
				die( "<script>location.href='game.php';</script>" );
			}

			echo "<b>������ ��� �������� ��������� ����������� ��������,<br>������ ������ ����� ������������ � ���������� ��� ������.</b><br><br>";
			echo "<table cellspacing=0 cellpadding=0><colgroup><col width=250><col width=*>";
			echo "<tr><td>����� ������ ���� ���������: </td><td>&nbsp;</td><td><b>$points_to_achieve</b></td></tr>";
			echo "<tr><td>������ ���������: </td><td>&nbsp;</td><td><b>$done</b></td></tr>";
			echo "<tr><td>������ ��������� ����: </td><td>&nbsp;</td><td><b>".($player->GetQuestValue( 37 ))."</b></td></tr>";
			echo "<tr><td>������ �������� ���������: </td><td>&nbsp;</td><td><b>$remaining</b></td></tr>";

			echo "<tr><td valign=top><br><b>�������� ��������������:</b><br><small>�������� ������ ��������������, �� ���������� ����� ����� ������ �� ������� �� ������ ������. ������ ������ 10 ����� � �������� 12��. <a href='javascript:premiums()'>������� ���������</a>";
				echo "</td><td>&nbsp;</td><td valign=top><br>";
					if( $player->regime == 0 )
					{
			            echo "<table cellspacing=0 cellpadding=0 border=0><tr><td><div id=num_img><img src=captcha/code.php width=90 height=40 border=1 bordercolor=black></div></td><td>&nbsp;";
    	    			$oncl = 'location.href= "game.php?clean=1&num=" + document.getElementById( "num" ).value;document.getElementById( "num" ).value="";';
        				echo "<input onkeydown='e = event || window.event;if( e.keyCode == 13 ) { $oncl }' type=text class=te_btn size=4 maxlength=4 name=num id=num></td><td>&nbsp;<button class=n_btn onClick='$oncl' class=ss_btn>��������</button></td></tr></table>";
        				echo "<small>(���� �� �� ������ ��������� ����, ������� <a href=# onclick='reload();'>����</a>, ����� �������� ��������).</small><br>";
        				echo "<script src='js/numkeyboard.js'></script><script>showkeyboard('num');</script>";
        			}
        			else
        			{
        				$rem = $player->till - time( );
        				include_js( 'js/timer.js' );
        				echo "<script>show_timer_title = true;document.write( InsertTimer( $rem, '<font color=darkgreen><b>�� ���������.</b></font><br>��������: <b>', '</b>', 1, 'location.href=\"game.php\"' ) );</script>";
        				if( $player->regime == 114 )
        				{
							$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=4" ) );
							if( $barr[0] ) 
							{
								echo "<a href=game.php?clean=1>�������� 60 �����</a><br>";
        					}
        				}
						echo "<a href=game.php?clean=4>�������� ������</a><br>";
        			}
				echo "</td></tr>";

				?>
				<script>function reload () {
                	var rndval = new Date().getTime(); 
                	document.getElementById('num_img').innerHTML = '<img width=90 height=40 src=captcha/code.php?rnd=' + rndval + ' border=1 bordercolor=black>';
                }</script>
				<?

			echo "<script>function premiums() { alert( '�������-������ ����������� ���������� �� �� 18 ������ �� ���;\\n�������-������� ��������� �������� � ������� ���� ��� ������������� ���� � ����;\\n�������-������ ��������� ���������������� �������.' ); };</script>";

			echo "<tr><td valign=top><br><b>������ ����� ��� ���������� ������:</b><br><small>�� ������������� ����� ���� ����������� ��������� 10 ������ ������.</td><td>&nbsp;</td><td valign=top><br><img src=images/money.gif> <b>5000</b><br><a href=game.php?clean=3>�������� ������</a></td></tr>";
			if( !$pisun ) echo "<tr><td valign=top><br><b>���������� � �������:</b><br><small>������ ����� �������������� ���������� ������ ����� �� 200 ������ ������, ���� �� ������� ������� ��� �� ������ � �������� � ������ ������� ����.</td><td>&nbsp;</td><td valign=top><br><img src=images/money.gif> <b>200</b><br>".(($player->regime==0)?"<a href=game.php?clean=2>������ � �������</a>":"<i>�� ������</i>")."</td></tr>";

			echo "</table>";

		}
		if( $ta_lost && $ta_lost < 11 )
		{
			if( $ta_lost < 10 )
			{
    			echo "<b>���� ���������������� ��������� ���������</b><br>";
    			echo "�� ��������� ��������� � ���� ���������� ������ �����.<br>";
    			echo "���� ���������������� ��������� ��������� � ������������ ������� ���������.<br>";
    			echo "����� ������ ��������, �� ������ ������� ����� ���� ����������.<br>";
    			echo "<br>";
			}
			else
			{
    			echo "<b>��� ���������� �������� ����������� ���������� �� ��������</b><br>";
    			echo "��� ����� ������ �������� ���� ������.<br>";
    			echo "��� ����� ���������� �������� ��������� �� ��������.<br>";
    			echo "����� ������ ��������, �� ������ ������� ����� ���� ����������.<br>";
    			echo "<br>";
			}

			$busy = true; $author = false; $ch_reg = false; $qry = false; $gsize = 0;
			f_MQuery( "LOCK TABLE ta_bets WRITE" );
			$res = f_MQuery( "SELECT entry_id, author FROM ta_bets WHERE player_id={$player->player_id}" );
			$arr = f_MFetch( $res );
			if( !$arr )
			{
				if( $_GET['ta_do'] == 1 )
				{
					f_MQuery( "INSERT INTO ta_bets( player_id, author, clan_id ) VALUES ( {$player->player_id}, 1, $clan_id )" );
					$moo = mysql_insert_id( );
					f_MQUery( "UPDATE ta_bets SET parent_id=$moo WHERE entry_id=$moo" );
					$ch_reg = 112;
					$author = true;
					$gsize = 1;
				}
				else if( $_GET['ta_do'] == 3 )
				{
					$eid = (int)$_GET['ta_wh'];
					$num = f_MFetch( f_MQUery( "SELECT count( player_id ) FROM ta_bets WHERE parent_id=$eid AND clan_id=$clan_id" ) );
					$num = $num[0];
					if( $num > 0 && $num < 5 )
					{
						f_MQuery( "INSERT INTO ta_bets( player_id, clan_id, parent_id ) VALUES ( {$player->player_id}, $clan_id, $eid )" );
						$res = f_MQUery( "SELECT player_id FROM ta_bets WHERE entry_id=$eid AND clan_id=$clan_id" );
						while( $arr = f_MFetch( $res ) )
							sendMessage( $arr[0], '/items' );
					}
					$ch_reg = 112;
				}
				else $busy = false;
			}
			else if( $_GET['ta_do'] == 2 && $arr['author'] )
			{
				$moo = $arr['entry_id'];
				$res = f_MQuery( "SELECT player_id FROM ta_bets WHERE author = 0 AND parent_id=$moo" );
				$qry = "";
				while( $arr = f_MFetch( $res ) ) { sendMessage( $arr[0], '/items' ); $qry .= ", $arr[0]"; }
				f_MQuery( "DELETE FROM ta_bets WHERE parent_id=$moo" );
				$ch_reg = 0;
				$busy = false;
			}
			else if( $arr['author'] )
			{
				$qres = f_MQuery( "SELECT count( player_id ) FROM ta_bets WHERE parent_id=$arr[entry_id]" );
				$qarr = f_MFetch( $qres );
				$gsize = $qarr[0];
				$author = true;
			}
			else if( $_GET['ta_do'] == 4 )
			{
				$res = f_MQuery( "SELECT parent_id FROM ta_bets WHERE player_id={$player->player_id}" );
				$arr = f_MFetch( $res );
				if( $arr )
				{
					$res = f_MQuery( "SELECT player_id FROM ta_bets WHERE parent_id=$arr[0] AND player_id <> {$player->player_id}" );
					while( $arr = f_MFetch( $res ) ) sendMessage( $arr[0], '/items' );
				}
				f_MQuery( "DELETE FROM ta_bets WHERE player_id = {$player->player_id}" );
				$ch_reg = 0;
				$busy = false;
			}

			if( !$busy )
				echo "<li><a href=game.php?ta_do=1>������� ������</a>";
			else if( $author )	
			{
				echo "<li><a href=game.php?ta_do=2>���������� ������</a>";
				if( $player->player_id <= 173 || $gsize == 5 )
				{
					if( $_GET['ta_do'] == 5 )
					{
						include_once( "mob.php" );
						$res = f_MQuery( "SELECT player_id FROM ta_bets WHERE parent_id=$arr[entry_id]" );
						f_MQuery( "DELETE FROM ta_bets WHERE parent_id=$arr[entry_id]" );
						f_MQuery( "UNLOCK TABLES" );
						$arr1 = array( ); $arr2 = array( );
						$id = 0;
						while( $arr = f_MFetch( $res ) )
						{
							$arr1[] = $arr[0];
							$mob = new Mob;
							if( $ta_lost < 10 )
							{
    							$cres = f_MQuery( "SELECT login FROM characters WHERE player_id = $arr[0]" ); $carr = f_MFetch( $cres );
    							$mob->CreateMirrorMob( $arr[0], $player->location, $player->depth, strrev( $carr[0] ) );
							}
							else
							{
								if( $id < 2 ) $mob->CreateMob( 13, $player->location, $player->depth );
								else $mob->CreateMob( 11, $player->location, $player->depth );
								++ $id;
							}
   							$arr2[] = $mob->player_id;
						}
						for( $i = $ta_lost; $i <= 2; ++ $i )
						{
							$mob = new Mob;
							$mob->CreateMob( 34, $player->location, $player->depth );
							$arr2[] = $mob->player_id;
						}
						include_once( 'create_combat.php' );
						$combat_id = CreateCombat( $arr1, $arr2, $player->location, $player->depth, 0 );
						f_MQuery( "UPDATE combat_players SET ai=1 WHERE player_id IN( ".implode( ',', $arr2 )." )" );
						f_MQuery( "UPDATE combats SET type=1 WHERE combat_id=$combat_id" );
						if( $ta_lost < 10 ) f_MQuery( "UPDATE combat_players SET win_action=7, win_action_param=$clan_id WHERE player_id IN( ".implode( ',', $arr1 )." )" );
						else f_MQuery( "UPDATE combat_players SET win_action=9, win_action_param=$clan_id WHERE player_id IN( ".implode( ',', $arr1 )." )" );
//						f_MQuery( "UPDATE combat_players SET bloody = 1 WHERE combat_id = $combat_id" );
						foreach( $arr1 as $id ) if( $id != $player->player_id ) sendMessage( $id, '/combat' );
						die( "<script>location.href='combat.php';</script>" );
					}
					echo "<li><a href=game.php?ta_do=5>������ ��������</a>";
				}
			}
			else echo "<li><a href=game.php?ta_do=4>�������� ������</a>";
			echo "<br><br>";

			f_MQuery( "UNLOCK TABLES" );

			if( $ch_reg !== false )
				f_MQuery( "UPDATE characters SET regime=$ch_reg WHERE player_id={$player->player_id}" );
			if( $qry !== false && strlen( $qry ) > 1 )	
			{
				$qry = substr( $qry, 1 );
				f_MQuery( "UPDATE characters SET regime=$ch_reg WHERE player_id IN ( $qry )" );
			}

			$are_there_combats = f_MValue( "SELECT count( player_id ) FROM combat_players WHERE ready < 2 AND win_action=7 AND win_action_param={$player->clan_id}" );
			if( $are_there_combats )
			{
				if( $_GET['ta_do'] == 183 )
				{
					if( $player->SpendMoney( 350 ) )
					{
						$player->AddToLogPost( 0, -350, 34 );
						if( mt_rand( 0, 99 ) < 65 )
						{
    						$pres = f_MQuery( "SELECT DISTINCT combat_id FROM combat_players WHERE ready < 2 AND win_action=7 AND win_action_param={$player->clan_id}" );
    						while( $parr = f_MFetch( $pres ) )
    						{
    							f_MQuery( "INSERT INTO combat_log( combat_id, string ) VALUES ( $parr[0], '������� ��� ������ ����������� � ��� � �������� ���� ����������� �����������. ������� ������: <b>{$player->login}</b><br>' )" );
    							$vres = f_MQuery( "SELECT player_id FROM combat_players WHERE side=1 AND ready < 2 AND combat_id={$parr[0]}" );
    							while( $varr = f_MFetch( $vres ) )
    							{
    								$plr = new Player( $varr[0] );
    								$plr->AlterAttrib( 222, -1 );
    							}
    						}
						}
						else
						{
    						$pres = f_MQuery( "SELECT DISTINCT combat_id FROM combat_players WHERE ready < 2 AND win_action=7 AND win_action_param={$player->clan_id}" );
    						while( $parr = f_MFetch( $pres ) )
    						{
    							f_MQuery( "INSERT INTO combat_log( combat_id, string ) VALUES ( $parr[0], '������� ��� ������ ����������� � ���, �� �� ���������� �� ����� �������� ������� � �������� ���� ����������� �����������. ������� ������: <b>{$player->login}</b><br>' )" );
    							$vres = f_MQuery( "SELECT player_id FROM combat_players WHERE side=1 AND ready < 2 AND combat_id={$parr[0]}" );
    							while( $varr = f_MFetch( $vres ) )
    							{
    								$plr = new Player( $varr[0] );
    								$plr->AlterAttrib( 222, 1 );
    							}
    						}
						}
					}
					else echo "<font color=darkred>�� ������� ��������</font><br>";
				}
				echo "� ��������� ������ ���� ������ ������ ��������� �� ����� �������.<br>������� ��� ��������� ������ ����� ������ � ���, ����������� ��������� ����������� ���� ����������� �� ���� �����.<br>���� ������ �� ���������� - 60%. � ������ ������� ������ ����� ������ ��������������� - ����������� ��������� �� ���� �����.<br>������ ������� � ����� <img width=10 height=10 src=images/money.gif> <b>350</b><br>";
				echo "<a href=game.php?ta_do=183>��������������� �������</a><br><br>";
			}

			$ok = false;
			$lid = -1;
			$res = f_MQuery( "SELECT * FROM ta_bets WHERE clan_id=$clan_id ORDER BY parent_id, author DESC, entry_id" );
			echo "<script>";
			while( $arr = f_MFetch( $res ) )
			{
				if( $arr['author'] )
				{
					if( $ok )
					{
						if( !$busy ) echo "document.write( '<li><a href=game.php?ta_do=3&ta_wh=$lid>��������������</a>' );";
						echo "FL();document.write( '<br>' );";
					}
					echo "FUlt();";
				}
				$ok = true; $lid = $arr['parent_id'];
				$plr = new Player(  $arr['player_id'] );
				echo "document.write( ".($plr->Nick())." + '<br>');";
			}
			if( $ok )
			{
				if( !$busy ) echo "document.write( '<li><a href=game.php?ta_do=3&ta_wh=$lid>��������������</a>' );";
				echo "FL();document.write( '<br>' );";
			}
			echo "</script>";
		}
	}

	echo "</td></tr></table>";
}
/*else if( $mode === 'buildings' )
{
	include( "clan_buildings.php" );
}*/
/*else if( $mode === 'tree' )
{
	include("clan_tree.php");
}*/
else if( $mode === 'ranks' )
{
	include( "clan_ranks_editor.php" );
}
else if( $mode === 'page' )
{
	include( 'clan_page_editor.php' );
}
else if( $mode === 'barracks' )
{
	include( 'clan_staff.php' );
}
else if( $mode === 'silo' )
{
	include( 'clan_silo.php' );
}
else if( $mode === 'treasury' )
{
	include( 'clan_treasury.php' );
}
/*else if( $mode === 'cafe' )
{
	include( 'clan_cafe.php' );
}*/
/*else if( $mode === 'shop_log' )
{
	include( 'clan_shop_log.php' );
}
else if( $mode === 'shop_control_log' )
{
	include( 'clan_shop_control_log.php' );
}*/
else if( $mode === 'post' )
{
	include( 'clan_post.php' );
}
else if( $mode === 'log' )
{
	include( 'clan_log.php' );
}
else if( $hascamp && $mode === 'camp' && $player->regime == 0 )
{
	$player->SetDepth( 50, true );
	die( "<script>location.href='game.php';</script>" );
}
else if( $mode === 'camp' )
{
	die( '<script>location.href="game.php";</script>' );
}

else RaiseError( "����������� ����� ������ $mode" );

?>