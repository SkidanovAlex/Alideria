<?

// IMPORTANT
// tries_left - ��� ������� ���������, � �� ������� ��������
// so do $left

// ����� ����������� � LeaveCombat � Player. � �� ���

function getOPrice( $oberegs )
{
	global $player;
	$level = $player->level;
	return ( 400 + ($level-4)*50 ) + $oberegs * $oberegs * ( 80 + ($level-4)*10 );
}

function getGPrice( $level )
{
	return $level * 150;
}

function getMPlayers( )
{
	global $rdy1, $rdy2, $number;
	global $player;
	$st = "";
	if( $rdy1 == $number ) $st .= "<li><a href='javascript:do_action(14)'>���������� ������ � ������ ��������</a>";
	if( $rdy2 >= 1 ) $st .= "<li><a href='javascript:do_action(12)'>���������� ������ � ��������� ������</a>";
	$res = f_MQuery( "SELECT leader_id, ready FROM caveexp_groups WHERE player_id={$player->player_id}" );
	$arr = f_MFetch( $res );
	$leader_id = $arr[0]; $ready = $arr[1];
	if( $ready != 0 )$st .= "<li><a href='javascript:do_action(13,0)'>������� ������ �� �����</a>";
	if( $ready != 1 )$st .= "<li><a href='javascript:do_action(13,1)'>������� ������ �� �� �����</a>";
	if( $ready != 2 )$st .= "<li><a href='javascript:do_action(13,2)'>���������� ����</a>";

	$st .= "<table><tr><td>\" + rFLUl() + \"<table>";

	$res = f_MQuery( "SELECT * FROM caveexp_groups WHERE leader_id={$leader_id}" );
	while( $arr = f_MFetch( $res ) )
	{
		$st .= "<tr><td width=250>\" + rFUlt() + \"";
		$plr = new Player( $arr['player_id'] );
		$st .= "\" + ".( $plr->Nick( ) )." + \"";
		$st .= "\" + rFL() + \"</td><td width=120>\" + rFUct() + \"";
		if( $arr['ready'] == 0 ) $st .= "<font color=darkgreen>�����</font>";
		if( $arr['ready'] == 1 ) $st .= "<font color=darkred>�� �����</font>";
		if( $arr['ready'] == 2 ) $st .= "<font color=darkblue>����� ����</font>";
		$st .= "\" + rFL() + \"</td></tr>";
	}
	$st .= "</table>\" + rFLL() + \"</td></tr></table>";
	return $st;
}

function outMBets( )
{
	global $left, $total, $player;

	if( $left == $total ) echo "<center><i>� ���������, ��� ������, ����� ���������. �� �� ���� ��� �� ������ ������� � ����� ���� �����������; ������, �� ���� ������ ��� ��� ������ ������ ���-�� ������� � ��������. �, ���� �� ������ �����, �� ������ ����.</i></center>";

	else
	{
		$gprice = getGPrice( $player->level );

		echo "<table width=90%>";
		echo "<tr><td>������� �� ���������� � ����������: </td><td><b>$left ".my_word_str( $left, '���', "����", "���" )."</b></td></tr>";
		echo "<tr><td>�������� ������� �� �������: </td><td><b>".($total-$left)." ".my_word_str( $total-$left, '���', "����", "���" )."</b></td></tr>";
		echo "<tr><td>� ���� ����� ���������� ������ � �������: </td><td><b>".($player->level - 1)."-".($player->level + 1)."</b></td></tr>";
		echo "<tr><td colspan=2>";
		$res = f_MQuery( "SELECT player_id, leader_id, number FROM caveexp_groups WHERE player_id={$player->player_id}" );
		$arr = f_MFetch( $res );
		$can_bet = false;
		if( $arr )
		{
			echo "<li><a href='javascript:do_action(1)'>�������� ������</a>";
			if( $arr['player_id'] == $arr['leader_id'] )
			{
				$narr = f_MValue( "SELECT count( player_id ) FROM caveexp_groups WHERE leader_id=$arr[0]" );
				if( $narr == $arr['number'] ) echo "<li><a href='javascript:do_action(10)'>���������� � ����������</a>";
			}
		}
		else
		{
			echo "<table width=100% cellspacing=0 cellpadding=0 border=0>";
			echo "<tr><td><li><a href='javascript:do_action(0,1)'>���������� � ���������� � ��������&nbsp;</a></td><td>(<img width=11 height=11 src=images/money.gif border=0> <b>$gprice</b>)</td></tr>";
			echo "<tr><td><li><a href='javascript:do_action(0,3)'>���������� � ���������� ������&nbsp;</a></td><td>(<img width=11 height=11 src=images/money.gif border=0> <b>".(3*$gprice)."</b>, <img width=11 height=11 src='images/icons/attributes/bo.gif' title='������ ����'> <b>�1.5</b>)</td></tr>";
			echo "<tr><td><li><a href='javascript:do_action(0,5)'>���������� � ���������� ��������&nbsp;</a></td><td>(<img width=11 height=11 src=images/money.gif border=0> <b>".(5*$gprice)."</b>, <img width=11 height=11 src='images/icons/attributes/bo.gif' title='������ ����'> <b>�2</b>)</td></tr>";
			echo "<tr><td colspan=2><li><a href='javascript:do_action(-1)'>��������</td></tr>";
			echo "</table>";
			$can_bet = true;
		}

		echo "</td></tr></table>";

		$res = f_MQuery( "SELECT * FROM caveexp_groups WHERE started=0 AND player_id=leader_id" );
		if( !f_MNum( $res ) ) echo "<i>�� ������ �� ����� ������</i>";
		else
		{
			echo "<table width=100%><tr><td>\" + rFLUl() + \"<table width=100%><colgroup><col width=30><col width=*>";
			while( $arr = f_MFetch( $res ) )
			{
				echo "<tr><td height=100% valign=top>\" + rFUcm() + \"<b>$arr[number]</b>\" + rFL() + \"</td><td height=100% valign=top>\" + rFUlm() + \"";
				$res2 = f_MQuery( "SELECT player_id FROM caveexp_groups WHERE leader_id=$arr[leader_id] ORDER BY player_id <> $arr[leader_id]" );
				$ok = false;
				$rnum = 0;
				while( $arr2 = f_MFetch( $res2 ) )
				{
					++ $rnum;
					$plr = new Player( $arr2[0] );
					if( $ok ) echo ", "; $ok = true;
					echo "\" + ".$plr->Nick( )." + \"";
				}
				if( $can_bet && $rnum < $arr['number'] ) echo "&nbsp;&raquo;&nbsp;<a href='javascript:do_action(3,$arr[leader_id])'>��������������</a>";
				echo "\" + rFL() + \"</td></tr>";
			}
			echo "</table>\"+ rFLL() +\"</td></tr></table>";
		}
	}
}

function loadPlayer( $player_id )
{
	global $total, $left, $oberegs, $stage, $premium;
	f_MQuery( "LOCK TABLE player_caveexp WRITE" );
	$res = f_MQuery( "SELECT * FROM player_caveexp WHERE player_id=$player_id" );
	$arr = f_MFetch( $res );
	if( $arr )
	{
		$total = 1; if( $arr['premium'] > time( ) ) ++ $total;
		$premium = $arr['premium'];
		$left = $arr['tries_left'];
		$oberegs = $arr['oberegs'];
		$stage = $arr['stage'];
	}
	else
	{
		f_MQuery( "INSERT INTO player_caveexp ( player_id ) VALUES ( $player_id )" );
		$premium = 0;
		$left = 0;
		$total = 1;
		$oberegs = 0;
		$stage = 0;
	}
	f_MQuery( "UNLOCK TABLES" );
}

if( !$mid_php )
{
	header("Content-type: text/html; charset=windows-1251");
	include( "functions.php" );
	include( "player.php" );
	include( "mob.php" );
	f_MConnect( );

	if( !check_cookie( ) )
		die( "�������� ��������� Cookie" );

	$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
	$player_id = $player->player_id;
	loadPlayer( $player->player_id );

	if( $player->location != 4 && ( $player->location != 2 || $player->depth != 5 ) ) die( );

	if( isset( $_GET['buyo'] ) )
	{
		$oprice = getOPrice( $oberegs );
		if( $stage > 0 )
		{
			echo "alert( '������ �������� ������� ������ � ����������' );";
		}
		else if( $player->SpendMoney( $oprice ) )
		{
			$player->AddToLogPost( 0, - $oprice, 32 );
			++ $oberegs;
			f_MQuery( "UPDATE player_caveexp SET oberegs=$oberegs WHERE player_id={$player->player_id}" );
			$oprice = getOPrice( $oberegs );
			echo "_( 'onum' ).innerHTML = '<b>$oberegs</b>';";
			echo "_( 'oprice' ).innerHTML = \"<b>$oprice</b> - <a href='javascript:buyo($oprice);'>������</a>\";";
			echo "update_money( {$player->money}, {$player->umoney} );";
		}
	}
	else if( isset( $_GET['buyp'] ) )
	{
		if( $player->SpendUMoney( 10 ) )
		{
			$premium = max( time( ), $premium ) + 28 * 3600 * 24;
			f_MQuery( "UPDATE player_caveexp SET premium=$premium WHERE player_id={$player->player_id}" );
			echo "_( 'pprice' ).innerHTML = \"<font color=green>��������� ��: <b>".date( "d.m.Y H:i", $premium )."</b></font><br>����: <img width=11 height=11 src=images/umoney.gif> <b>10</b> / 28 ���� - <a href='javascript:buyp()'>��������</a>\";";
			echo "update_money( {$player->money}, {$player->umoney} );";
		}
	}
	else if( isset( $_GET['act'] ) && ( $player->regime == 0 || $player->regime == 118 ) )
	{
		f_MQuery( "LOCK TABLE player_caveexp WRITE, caveexp_groups WRITE" );
		$res = f_MQuery( "SELECT stage FROM player_caveexp WHERE player_id=$player_id" );
		$arr = f_MFetch( $res );
		$stage = $arr[0];
		$act = (int)$_GET['act'];
		$param = (int)$_GET['a'];

		$res = f_MQuery( "SELECT leader_id, ready FROM caveexp_groups WHERE player_id=$player_id" );
		$arr = f_MFetch( $res );
		$leader = $arr[0];
		$ready = $arr[1];

		if( $leader && ( $act == 10 || $act == 14 ) && $ready == 0 )
		{
			$res = f_MQuery( "SELECT number, count( player_id ), level FROM caveexp_groups WHERE ready=0 AND leader_id=$leader GROUP BY leader_id" );
			$arr = f_MFetch( $res );
			if( $arr[0] == $arr[1] ) // start group tournament
			{
				if( $act == 10 && $leader == $player_id && !$stage )
				{
					$gprice = getGPrice( $player->level );
					f_MQuery( "UPDATE player_caveexp SET tries_left=tries_left+1, stage=1 WHERE player_id=$player_id" );
					f_MQuery( "UNLOCK TABLES" );
					if( $player->SpendMoney( $gprice ) )
					{
						$player->AddToLogPost( 0, - $gprice, 32 );
						$player->SetLocation( 4, true );
						$player->SetDepth( 1, true );
						$player->SetRegime( 0 );
						$mob = new Mob;
						for( $i = 0; $i < $arr[0]; ++ $i )
						{
							$mob->CreateDungeonMob( $arr[2], 1, 0, 0, 0, $player->location, $player->depth, '��������', 'pp1.png' );
							$mob->AttackPlayer( $player_id, 8, 0, false );
						}
						$res = f_MQuery( "SELECT player_id FROM caveexp_groups WHERE leader_id=$player_id AND player_id <> $player_id" );
						include_once( "create_combat.php" );
						while( $arr = f_MFetch( $res ) )
						{
							$plr = new Player( $arr[0] );
							$plr->SetLocation( 4, true );
							$plr->SetDepth( 1, true );
							$plr->SetRegime( 0 );
							$combat_id = ccAttackPlayer( $arr[0], $mob->player_id, 0, false, false );
							$plr->syst2( "/combat" );
							f_MQuery( "UPDATE player_caveexp SET tries_left=tries_left+1, stage=1 WHERE player_id=$arr[0]" );
						}
						f_MQuery( "UPDATE combat_players SET win_action=8 WHERE combat_id=$combat_id" );
						f_MQuery( "UPDATE combats SET type=1 WHERE combat_id=$combat_id" );
						f_MQuery( "UPDATE caveexp_groups SET started=1 WHERE leader_id=$player_id" );
						echo "location.href='combat.php';";
					}
					else f_MQuery( "UPDATE player_caveexp SET tries_left=tries_left-1, stage=0 WHERE player_id=$player_id" );
				}
				else if( $act == 14 && $stage > 0 )
				{
					f_MQuery( "UPDATE caveexp_groups SET ready=5 WHERE leader_id=$leader" );
					f_MQuery( "UNLOCK TABLES" );
					$mob = new Mob;
					for( $jj = 0; $jj < $arr[0]; ++ $jj )
					{
    					$pts = 3 + (int)($stage / 5);
    					$wnf = array( 0, 0, 0 );
    					for( $i = 0; $i < $pts; ++ $i ) $wnf[mt_rand( 0, 2 )] ++;
    					if( mt_rand( 1, 2 ) == 1 ) // �����
    					{
    						if( $stage <= 3 ) { $nm = "�����"; $ava = 'pp1.png'; }
    						else if( $stage <= 6 ) { $nm = "������� �����"; $ava = 'pp3.png'; }
    						else if( $stage <= 9 ) { $nm = "�����-������"; $ava = 'pp4.png'; }
    						else { $nm = "�����-������"; $ava = 'pp5.png'; }
    					}
    					else
    					{
    						if( $stage <= 5 ) { $nm = "�������� ������"; $ava = 'pp2.png'; }
    						else { $nm = "�������� ����"; $ava = 'pp6.png'; }
    					}
    					$mob->CreateDungeonMob( $player->level, $stage, $wnf[0], $wnf[1], $wnf[2], $player->location, $player->depth, $nm, $ava );
    					$mob->AttackPlayer( $player_id, 8, 0, false );
//    					LogError( "$mob->combat_id" );
					}
					$res = f_MQuery( "SELECT player_id FROM caveexp_groups WHERE leader_id=$leader AND player_id <> $player_id" );
					include_once( "create_combat.php" );
					while( $arr = f_MFetch( $res ) )
					{
						$plr = new Player( $arr[0] );
						$combat_id = ccAttackPlayer( $arr[0], $mob->player_id, 0, false, false );
						$plr->syst2( "/combat" );
					}
					f_MQuery( "UPDATE combat_players SET win_action=8 WHERE combat_id=$combat_id" );
					f_MQuery( "UPDATE combats SET type=1 WHERE combat_id=$combat_id" );
					echo "location.href='combat.php';";
				}
			}
		}
		if( $leader && $act == 12 && $stage > 0 )
		{
			$res = f_MQuery( "SELECT number, count( player_id ), level FROM caveexp_groups WHERE ready=2 AND leader_id=$leader GROUP BY leader_id" );
			$arr = f_MFetch( $res );
			if( $arr[1] >= 1 )
			{
				f_MQuery( "UPDATE caveexp_groups SET ready=5 WHERE leader_id=$leader" );
				f_MQuery( "UNLOCK TABLES" );
    			$res = f_MQuery( "SELECT player_id FROM caveexp_groups WHERE leader_id=$leader" );
				while( $arr = f_MFetch( $res ) )
				{
    				$plr = new Player( $arr[0] );
					$plr->syst2( $player->login.' �������� ������������ � ��� ���� ������� ���� ��������� ��������� �� ����������.' );
					$plr->SetLocation( 2, true );
					$plr->SetDepth( 5, true );
					f_MQuery( "UPDATE player_caveexp SET stage=0 WHERE player_id={$plr->player_id}" );
					$plr->syst2( "/items" );
				}
				f_MQuery( "DELETE FROM caveexp_groups WHERE leader_id=$leader" );
				die( "location.href='game.php';" );
			} else echo "/*not enough*/";
		}

		if( $stage > 0 )
		{
			if( $leader && $ready < 3 )
			{
				if( $act == 13 )
				{
					if( $param >= 0 && $param < 3 )
						f_MQuery( "UPDATE caveexp_groups SET ready=$param WHERE player_id=$player_id" );
				}
			}
			else // alone
			{
    			if( $act == 10 )
    			{
    				f_MQuery( "UNLOCK TABLES" );
    				$num = f_MValue( "SELECT number FROM player_num WHERE player_id=$player_id" );
    				if( $num != $param )
    				{
    					echo "alert( '������� ���������� ��� � ����' );";
    					$code=rand(1000,9999);

    		            f_MQuery( "LOCK TABLE player_num WRITE" );
    		            f_MQuery( "DELETE FROM player_num WHERE player_id = {$player->player_id}" );
            		    f_MQuery( "INSERT INTO player_num VALUES ( {$player->player_id}, $code )" );
    		            f_MQuery( "UNLOCK TABLES" );
    				}
    				else
    				{
    					$mob = new Mob;
    					$pts = 3 + (int)($stage / 5);
    					$wnf = array( 0, 0, 0 );
    					for( $i = 0; $i < $pts; ++ $i ) $wnf[mt_rand( 0, 2 )] ++;
    					if( mt_rand( 1, 2 ) == 1 ) // �����
    					{
    						if( $stage <= 3 ) { $nm = "�����"; $ava = 'pp1.png'; }
    						else if( $stage <= 6 ) { $nm = "������� �����"; $ava = 'pp3.png'; }
    						else if( $stage <= 9 ) { $nm = "�����-������"; $ava = 'pp4.png'; }
    						else { $nm = "�����-������"; $ava = 'pp5.png'; }
    					}
    					else
    					{
    						if( $stage <= 5 ) { $nm = "�������� ������"; $ava = 'pp2.png'; }
    						else { $nm = "�������� ����"; $ava = 'pp6.png'; }
    					}
    					$mob->CreateDungeonMob( $player->level, $stage, $wnf[0], $wnf[1], $wnf[2], $player->location, $player->depth, $nm, $ava );
    					$mob->AttackPlayer( $player_id, 8, 0, false );
    					echo "location.href='combat.php';";
    				}
    				die( );
    			}
    			else if( $act == 11 )
    			{
    				f_MQuery( "UPDATE player_caveexp SET stage=0 WHERE player_id=$player_id" );
    				f_MQuery( "UNLOCK TABLES" );
    				$player->SetLocation( 2, true );
    				$player->SetDepth( 5, true );
    				echo "location.href='game.php';";
    				die( );
    			}
			}
		}
		else
		{
			$setbet = 0;
			$ttarr = 0;
			if( $act == 0 ) // ������ ������
			{
				$cnt = f_MValue( "SELECT count( player_id ) FROM caveexp_groups WHERE player_id=$player_id" );
				if( $cnt > 0 ) echo "alert( '� ��� ��� ������ ������, ������� ������� �������� ��' );";
				else if( $left >= $total ) echo "alert( '������ �� ������ ������� ��� � ����������. ��������� ������.' );";
				else
				{
					$gprice = getGPrice( $player->level );
					if( $param == 1 )
					{
						f_MQuery( "UPDATE player_caveexp SET tries_left=tries_left+1, stage=1 WHERE player_id=$player_id" );
						f_MQuery( "UNLOCK TABLES" );
						if( $player->SpendMoney( $gprice ) )
						{
							$player->AddToLogPost( 0, - $gprice, 32 );
    						$mob = new Mob;
    						$player->SetLocation( 4, true );
    						$player->SetDepth( 1, true );
    						$mob->CreateDungeonMob( $player->level, 1, 0, 0, 0, $player->location, $player->depth, '��������', 'pp1.png' );
    						$mob->AttackPlayer( $player_id, 8, 0, false );
    						echo "location.href='combat.php';";
						}
						else f_MQuery( "UPDATE player_caveexp SET tries_left=tries_left-1, stage=0 WHERE player_id=$player_id" );
						die( );
					}
					else if( $param == 1 ) echo "alert( '� ��� �� ������� ��������' );";
					else if( $param == 3 || $param == 5 )
					{
						$tm = time( );
						$lvl = $player->level;
						f_MQuery( "INSERT INTO caveexp_groups( leader_id, player_id, last_action, number, level ) VALUES ( $player_id, $player_id, $tm, $param, $lvl )" );
						$setbet = 1;
					}
					else RaiseError( "�������� �������� 'a' ��� ������ ������ � ����������. ����������, ��� ����� ������� ����� �������� ����� � ����, �� ������������� � ��������������, �������� � ������������ ���������� ���������.", "act=0, a=$a" );
				}
			}
			else if( $act == 1 )
			{
				$val = f_MValue( "SELECT leader_id FROM caveexp_groups WHERE player_id=$player_id" );
				if( $val )
				{
					$ttarr = array( );
					if( $val == $player_id ) $res = f_MQuery( "SELECT player_id FROM caveexp_groups WHERE leader_id=$val" );
					while( $arr = f_MFetch( $res ) ) $ttarr[] = $arr[0];
    				f_MQuery( "DELETE FROM caveexp_groups WHERE leader_id=$player_id" );
    				f_MQuery( "DELETE FROM caveexp_groups WHERE player_id=$player_id" );
				}
				$setbet = 2;
			}
			else if( $act == 3 )
			{
				$res = f_MQuery( "SELECT number, count( player_id ), level FROM caveexp_groups WHERE leader_id=$param GROUP BY leader_id" );
				$arr = f_MFetch( $res );
				if( $arr )
				{
    				$lvl = $player->level;
    				if( $lvl < $arr['level'] - 1 || $lvl > $arr['level'] + 1 ) echo "alert( '�� �� ��������� �� ������ ��� ���� ������' );";
    				else if( $arr[0] > $arr[1] )
    				{
    					f_MQuery( "INSERT INTO caveexp_groups( leader_id, player_id, number, level ) VALUES ( $param, $player_id, $arr[0], $arr[2] )" );
    					f_MQuery( "UPDATE caveexp_groups SET last_action=".time( )." WHERE leader_id=$param" );
    					$setbet = 1;
    				}
    				else echo "alert( '� ���� ������ ��� ������������ ���������� ����������' );";
				} else echo "alert( '������ ���� ��������� ��� ��� ���������� � ����������' );";
			}
		}
		f_MQuery( "UNLOCK TABLES" );
		if( $stage == 0 )
		{
    	   	echo "_( 'betshere' ).innerHTML = \"";
       		outMBets( );
    	   	echo "\";";
	   	}
	   	else if( $stage > 0 && $leader && $ready < 4 )
	   	{
    	   	echo "_( 'betshere' ).innerHTML = \"";
    	   	$rdy1 = f_MValue( "SELECT count( player_id ) FROM caveexp_groups WHERE leader_id=$leader AND ready=0" );
    	   	$rdy2 = f_MValue( "SELECT count( player_id ) FROM caveexp_groups WHERE leader_id=$leader AND ready=2" );
    	   	$number = f_MValue( "SELECT count( player_id ) FROM caveexp_groups WHERE leader_id=$leader" );

       		echo getMPlayers( );
    	   	echo "\";";
	   	}
	   	if( $setbet == 1 ) $player->SetRegime( 118 );
	   	if( $setbet == 2 ) $player->SetRegime( 0 );
	   	if( $ttarr ) foreach( $ttarr as $id )
	   		f_MQuery( "UPDATE characters SET regime=0 WHERE player_id=$id" );
	}
	die( );
}

include( 'tella_assault.php' );

if( ta_now( ) )
{
	echo "<font color=darkred><b>��������!</b></font> ������� ����� ����� �� ��� �������� � �������� ����������� �����!!! �� ������ �������� � ��� �� ������� �����!";
	ta_output( 0, "������� ���� ���� ��������", "������� ����� � �������� ���������", "��� � �������� ������� � �������� ��������", "������� ����� ��� ������� ������..." );
	ta_output( 1, "���� ���� � �����", "���� ���� � ����� ���������", "��� � ����� ���� � ����� ��������", "���� ���� ��� ������� ������" );
	ta_output( 2, "����������� ����� ������", "����������� ����� ������ ���������", "��� � ����������� ������ ������ ��������", "����������� ����� ��� ������� ������" );
}

if( $player->level >= 4 )
{
	echo "<center><table width=100%><tr><td><script>FLUl();</script>";

	echo "<table width=100%><colgroup><col width=60%><col width=40%>";
	echo "<tr><td colspan=2><script>FUlt();</script>";
	if( $stage == 0 ) echo "<small><b>�������� �� ����� � ������ ���� ���������, ������� � ����������; �� ������, � ��� ������ ��������, ��� ��������� �� �����. ��������� ������ ��������� ���������� � ���������� � ���� ��������� ����� ����� ������; �������, �� ��� ���� ����� �� ������, ��� ���������� ������ ����� ���� ������� � �������...</b></small>";
	else echo "<small><b>� ���������� � ����� ���� ���������� �� �� ����. ������ ��� �������� ����������� ������ ������ ��� � ���� �������, ����� ���������� ���������� ������������ ������, ��������� �������� ����� ������ �����...</b></small>";

	echo "<script>FL();</script></td></tr>";

	echo "<tr>";
	echo "<td height=100% valign=top><script>FUlt();</script>";
	loadPlayer( $player->player_id );
	if( !$stage)
	{
		echo "<div id=betshere><script>document.write( \""; outMBets( ); echo "\" );</script></div>";
	}
	else
	{
		$res = f_MQuery( "SELECT * FROM caveexp_groups WHERE player_id={$player->player_id}" );
		$arr = f_MFetch( $res );
		if( $arr )
		{
			$ready = $arr['ready'];
			$leader = $arr['leader_id'];
			$number = $arr['number'];
			f_MQuery( "LOCK TABLE caveexp_groups WRITE" );
			$res = f_MQuery( "SELECT * FROM caveexp_groups WHERE leader_id=$leader" );
			$lost = 1; $won = 0; $others = 0;
			$rdy1 = 0; $rdy2 = 0;
			while( $arr = f_MFetch( $res ) )
			{
				if( $arr['ready'] != 4 ) $lost = 0;
				if( $arr['ready'] == 3 ) $won = 1;
				if( $arr['ready'] < 3 || $arr['ready'] > 4 ) $others ++;
				if( $arr['ready'] == 0 ) ++ $rdy1;
				if( $arr['ready'] == 2 ) ++ $rdy2;
			}
			if( $lost || ( $won && !$others ) )
			{
				f_MQuery( "UPDATE caveexp_groups SET ready=1 WHERE leader_id=$leader" );
				$ready = 1;
			}
			f_MQuery( "UNLOCK TABLES" );

			if( $won && !$others )
			{
    			$res = f_MQuery( "SELECT player_id FROM caveexp_groups WHERE leader_id=$leader" );
    			while( $arr = f_MFetch( $res ) )
    			{
    				$plr = new Player( $arr[0] );
    				$barr = f_MFetch( f_MQuery( "SELECT level FROM clan_buildings WHERE clan_id=( SELECT clan_id FROM characters WHERE player_id=$plr->player_id ) AND building_id=4" ) );
                    if( $barr )
                        $blvl = $barr[0];
                    else $blvl = 0;
                    $add_exp = '';
    				$cstage = f_MValue( "SELECT stage FROM player_caveexp WHERE player_id={$plr->player_id}" );
    				$coef = 1.5; if( $number == 5 ) $coef = 2.0;
                    $coef *= 0.98;
    				$exp = (int)($cstage * $plr->level * $coef * 6 * mt_rand( 80, 120 ) / 100);
				$exp_to_log = $exp;
    				if( $blvl )
				{
					$bonus = ceil( $exp * ( 0.02 ) * $blvl );
					$exp_to_log += $bonus;
					$add_exp = ". ���� �������� �������� ��� ������������� <b>$bonus</b> ".my_word_str( $bonus, "������� �����", "������� �����", "������ �����" );
					f_MQuery( "UPDATE characters SET exp = exp + $bonus WHERE player_id = $plr->player_id" );
				}
				if( f_MValue( 'SELECT * FROM `premiums` WHERE `player_id` = '.$plr->player_id.' AND `premium_id` = 0' ) )
				{
					$premiumExp = round( $exp / 2 );
					$exp_to_log += $premiumExp;
					$add_exp .= ". �������-��� �������� ��� ������������� <b>$premiumExp</b> ".my_word_str( $premiumExp, "������� �����", "������� �����", "������ �����" );
					f_MQuery( "UPDATE characters SET exp = exp + $premiumExp WHERE player_id = $plr->player_id" );
				}
				else
				{
					$premiumExp = 0;
				}
    				$koef_money = f_MValue("SELECT koef_value FROM koefs WHERE koef_id = 2");
    				$money = (int)($cstage * $plr->level * $koef_money * mt_rand( 80, 120 ) / 100);
    				f_MQuery( "UPDATE characters SET depth=depth+1, exp=exp+$exp WHERE player_id={$plr->player_id}" );
    				f_MQuery( "UPDATE player_caveexp SET stage=stage+1 WHERE player_id={$plr->player_id}" );
    				if ($money > 0)
    				{
					$plr->AddMoney( $money );
					$plr->AddToLogPost( 0, $money, 33, $cstage );
					$earn = ". ������� ��� ������, �� ����� <b>{$money}</b> ".my_word_str( $money, "������", "�������", "��������" );
				}
				else
					$earn = "";
				f_MQuery("INSERT INTO player_log (player_id, item_id, type, have, arg1, time) VALUES ($plr->player_id, -2, 998, $exp_to_log, $cstage, ".time().")");
    				$plr->syst2( "�� �������� ��������, ���������� <b>$cstage</b> ������� ����������, � �������� <b>$exp</b>".my_word_str( $exp, ' ������� �����', " ������� �����", " ������ �����" )."{$add_exp}"."{$earn}" );
        		}
			}
			if( $lost )
			{
				$plr = new Player( $leader );
				$oberegs = f_MValue( "SELECT oberegs FROM player_caveexp WHERE player_id={$leader}" );
				if( $oberegs > 0 )
				{
					f_MQuery( "UPDATE player_caveexp SET oberegs=oberegs-1 WHERE player_id={$leader}" );
	    			$res = f_MQuery( "SELECT player_id FROM caveexp_groups WHERE leader_id=$leader" );
    				while( $arr = f_MFetch( $res ) )
    				{
	    				$plr = new Player( $arr[0] );
						$plr->syst2( '�� ��������� � ���, ����� ����� ������� ������� ���� ������, �� ������ ���������� ������������.' );
					}
				}
				else
				{
	    			$res = f_MQuery( "SELECT player_id FROM caveexp_groups WHERE leader_id=$leader" );
    				while( $arr = f_MFetch( $res ) )
    				{
	    				$plr = new Player( $arr[0] );
    					$plr->syst2( '�� ��������� � ���. � ������ ������ ��� �������, ��� �������� �������� ����������.' );
    					$plr->SetLocation( 2, true );
    					$plr->SetDepth( 5, true );
    					f_MQuery( "UPDATE player_caveexp SET stage=0 WHERE player_id={$plr->player_id}" );
    					$plr->syst2( "/items" );
    				}
    				f_MQuery( "DELETE FROM caveexp_groups WHERE leader_id=$leader" );
    				die( "<script>location.href='game.php';</script>" );
				}
			}

			if( $ready == 4 )
				echo "<center><i>�� ��������� � ���, ��������� ���� ���� ��������� �������� ���</i><br><a href=game.php>��������</a></center>";
			else if( $ready == 3 )
				echo "<center><i>�� �������� � ���, �� �� ��� ������ �������� ���. ���������, ���� ���� ��������� ������� ���.</i><br><a href=game.php>��������</a></center>";
			else
			{
				echo "<div id=betshere><script>document.write( \"".getMPlayers( )."\" );</script></div>";
			}
		}
		else
		{
			echo "<b>��� ������ �� ������ ������, �� ������ ���������� �� <font color=darkgreen>{$stage}-�</font> �������</b><br>";
			echo "<br>";

			$code=rand(1000,9999);

            f_MQuery( "LOCK TABLE player_num WRITE" );
            f_MQuery( "DELETE FROM player_num WHERE player_id = {$player->player_id}" );
            f_MQuery( "INSERT INTO player_num VALUES ( {$player->player_id}, $code )" );
            f_MQuery( "UNLOCK TABLES" );

            echo "<table cellspacing=0 cellpadding=0 border=0><tr><td><div id=num_img><img src=captcha/code.php width=90 height=40 border=1 bordercolor=black></div></td><td>&nbsp;";
			$oncl = 'do_action( 10, document.getElementById( "num" ).value );document.getElementById( "num" ).value="";';
			echo "<input onkeydown='e = event || window.event;if( e.keyCode == 13 ) { $oncl }' type=text class=te_btn size=4 maxlength=4 name=num id=num></td><td>&nbsp;<button class=n_btn onClick='$oncl' class=ss_btn>����������</button></td></tr></table>";
			echo "(���� �� �� ������ ��������� ����, ������� <a href=# onclick='reload();'>����</a>, ����� �������� ��������).<br>";
			echo "<script src='js/numkeyboard.js'></script><script>showkeyboard('num');</script>";

			echo "<br><br><b>�� ������ ��������� ������������ � ��������� � �����</b><br>";
			echo "<li><a href='javascript:do_action(11,0)'>��������� ������������</a>";

		}
	}
	echo "<script>FL();</script></td>";

	echo "<td height=100% valign=top><script>FUlt();</script>";

	echo "<b>�������: <span id=onum>$oberegs</span> ��.</b><br>";
	echo "<div align='justify'><small>���� � ��� ��� �������, ����� ������� �� ��������� � ������� ���� ������������ ����� ���������.<br>���� ��� ��������� ���� ������, �� ���������, �� ��������� ���������� ������������ ����������. ��� ��������� ������������ ���������� ������� ����� ������ ������ �������.</small></div>";
	$oprice = getOPrice( $oberegs );
	echo "����: <img width=11 height=11 src=images/money.gif> <span id=oprice><b>$oprice</b> - <a href='javascript:buyo($oprice);'>������</a></span><br><br>";

	echo "<b>������������������� ������</b><br>";
	echo "<div align='justify'><small>��� ���� ����� ������� ������ ������ ����� �� ������� �������. ���, �� ������������� �����, ��� ������ ���������� ��� � ���������� ��� ���� � ����.</small></div>";
	if( $premium > time( ) ) echo "<div id=pprice><font color=green>��������� ��: <b>".date( "d.m.Y H:i", $premium )."</b></font><br>����: <img width=11 height=11 src=images/umoney.gif> <b>10</b> / 28 ���� - <a href='javascript:buyp()'>��������</a></div>";
	else echo "<div id=pprice><font color=darkred>������ �� ��������������� � ���</font><br>����: <img width=11 height=11 src=images/umoney.gif> <b>10</b> / 28 ���� - <a href='javascript:buyp()'>���� ������</a></div>";

	echo "<script>FL();</script></td>";

	echo "</tr>";

	echo "</table>";
	echo "<script>FLL();</script></td></tr></table>";
}

$gprice = getGPrice($player->level);

?>

<script>
function buyo( pr )
{
	if( confirm( '������ ������ �� ' + pr + ' ��������?' ) )
		query( 'cave_entrance.php?buyo=1','');
}
function buyp( )
{
	if( confirm( '�������� ������������������� ������ �� ������ ������ �� 10 ��������?' ) )
		query( 'cave_entrance.php?buyp=1','');
}
function buyp2( )
{
	if( confirm( '�������� ������������������� ������ �� ��� ��� �� 2 �������?' ) )
		query( 'cave_entrance.php?buyp=2','');
}
function do_action( id, a )
{
	if( id == 0 )
	{
		if( a == 1 ) if( confirm( '���������� � ���������� � ��������, ��� ������ � ������� <?=$gprice?> ��������?' ) ) query( "cave_entrance.php?act=0&a=1", '' );
		if( a == 3 ) if( confirm( '������ ������ �� ����� � ���������� ������? ������� ����� ����� ������ ����� ����� ������ � ��������������� ������ ������������.' ) ) query( "cave_entrance.php?act=0&a=3", '' );
		if( a == 5 ) if( confirm( '������ ������ �� ����� � ���������� ��������? ������� ����� ����� ������ ����� ����� ������ � ��������������� ������ ������������.' ) ) query( "cave_entrance.php?act=0&a=5", '' );
	}
	else if( id == 11 )
	{
		if( confirm( '��������� � �����? ���������� ������������ ����� ����� ������.' ) ) query( "cave_entrance.php?act=11", '' );
	}
	else query( "cave_entrance.php?act=" + id + "&a=" + a, '' );
}
function reload () {

	var rndval = new Date().getTime();

	document.getElementById('num_img').innerHTML = '<img width=90 height=40 src=captcha/code.php?rnd=' + rndval + ' border=1 bordercolor=black>';
}

setInterval( 'do_action( -1 )', 10000 );

</script>
