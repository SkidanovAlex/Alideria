<?

header("Content-type: text/html; charset=windows-1251");

if( !$mid_php ) die( );

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "arrays.php" );
include_once( "skin.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "<script>window.top.location.href='index.php';</script>" );

		$premiums = Array
		(
			Array( "�������-���", "1. �� 50% ������ ���������� ������ ����;<br>2. 240 ����� ������ � ����;<br>3. 10 ������� ����� � ������ ��������� ��� ��������� �� 28 ����.<br>", 30, 10, 2 ),
			Array( "�������-������", "1. �� 50% ������ ������� ������� ��� ������ � �������� �������, ���������, ���������� � �����������;<br>2. 100 �������� � ������ ��������� ��� ��������� �� 28 ����.<br>", 15, 5, 1 ),
			Array( "�������-�����", "����������� ������� ������������������ ������������ �� ��� ���� ������ ��� �������, ��� �������� ��������� � ����<br>", 15, 5, 1 ),
			Array( "�������-������", "1. �� 50% ������ ���������� ���������������� ����<br>2. 10 ����������������� ����� � ������ ��������� ��� ��������� �� 28 ����.<br>", 15, 5, 1 ),
			Array( "�������-�������", "�������������� ���������� ������ � �������� �������, ���������, ���������� � ����������� � ������� �������� ����� ��� �������, ��� �������� ��������� � ����.<br>", 15, 5, 1 ),
			Array( "�������-�������", "1. �� 50% ���� ���� � �������� <sup>1</sup>;<br>2. � ������� ���� ������ �����, ���������� � ������.<sup>2</sup><br><small><sup>1</sup>��� �������, ��� �� - ������� �������� �������, � ������ 50% ���������� ������ �������� ���� ����� �������</small><br><small><sup>2</sup>����� ����� � ������� � ����� ��� ���� ��������� ����� �� 20 ������ � �� �� 30</small><br>", 30, 10, 2 )
		);

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">

<?

$mode = $_GET['p'];
if( isset( $_GET['fail'] ) ) $mode = 'fail';
if( isset( $_GET['smiles'] ) ) $mode = 'smiles';

f_MQuery( "LOCK TABLE frozen_premiums WRITE, premiums WRITE" );
$frozen_premiums = f_MValue( "SELECT count( premium_id ) FROM frozen_premiums WHERE player_id={$player->player_id}" );

if( !$frozen_premiums )
{
    if( isset( $_GET['freeze'] ) )
    {
    	$tm = time( );
    	f_MQuery( "INSERT INTO frozen_premiums SELECT player_id, premium_id, deadline-$tm, $tm + 2*24*3600 from premiums where player_id={$player->player_id};" );
		f_MQuery( "UNLOCK TABLES" );
    	f_MQuery( "DELETE FROM premiums WHERE player_id={$player->player_id}" );
		$frozen_premiums = f_MValue( "SELECT count( premium_id ) FROM frozen_premiums WHERE player_id={$player->player_id}" );
    }

    else if( isset( $_GET['activate'] ) )
    {
		f_MQuery( "UNLOCK TABLES" );
    	$act = (int)$_GET['activate'];
    	if( $act < 0 || $act >= 6 )
    		RaiseError( "����������� ��� �������-��������", "$act" );

    	$price = $premiums[$act][2]; $durat = 28;
    	if( $_GET['l'] == 7 )
    	{
    		$price = $premiums[$act][3];
    		$durat = 7;
    	}
    	if( $_GET['l'] == 1 )
    	{
    		$price = $premiums[$act][4];
    		$durat = 1;
    	}

    	if( $player->SpendUMoney( $price ) )
    	{
    		f_MQuery( "LOCK TABLE premiums WRITE" );
    		$res = f_MQuery( "SELECT deadline FROM premiums WHERE player_id={$player->player_id} AND premium_id=$act" );
    		$arr = f_MFetch( $res );
    		$deadline = time( ) + $durat * 24 * 60 * 60;
    		if( !$arr ) f_MQuery( "INSERT INTO premiums( player_id, premium_id, deadline ) VALUES ( {$player->player_id}, $act, $deadline )" );
    		else if( $arr[0] < time( ) ) f_MQuery( "UPDATE premiums SET deadline=$deadline WHERE player_id={$player->player_id} AND premium_id=$act" ); 
    		else f_MQuery( "UPDATE premiums SET deadline=deadline+$durat*24*60*60 WHERE player_id={$player->player_id} AND premium_id=$act" ); 
    		f_MQuery( "UNLOCK TABLES" );
    		if( $durat == 28 && $act == 0 ) f_MQuery( "UPDATE characters SET exp=exp+10 WHERE player_id={$player->player_id}" );
    		else if( $durat == 28 && $act == 1 ) $player->AddMoney( 100 );
    		else if( $durat == 28 && $act == 3 ) f_MQuery( "UPDATE characters SET prof_exp=prof_exp+10 WHERE player_id={$player->player_id}" );
    		$player->AddToLogPost( -1, -$price, 21, $act );
    		die( "<script>location.href='game.php';</script>" );
    	}
    	else echo "<center><font color=darkred>� ��� �� ������� ��������</font></center>";
    }
    
    else f_MQuery( "UNLOCK TABLES" );
}
else
{
	if( isset( $_GET['ufreeze'] ) )
	{
		$tm = time( );
    	f_MQuery( "INSERT INTO premiums SELECT player_id, premium_id, duration+$tm from frozen_premiums where player_id={$player->player_id} AND available < $tm;" );
    	f_MQuery( "DELETE FROM frozen_premiums WHERE player_id={$player->player_id} AND available < $tm" );
		$frozen_premiums = f_MValue( "SELECT count( premium_id ) FROM frozen_premiums WHERE player_id={$player->player_id}" );
	}
	f_MQuery( "UNLOCK TABLES" );
}

if( isset( $_GET['nick_clr'] ) )
{
	if( $_GET['nick_clr'] ==2 )
		include( "favn_nick_clr_2.php" );
	else include( "favn_nick_clr.php" );

	return;
}

echo "<center><table width=90% height=90%><tr>";
echo "<td width=50% height=100%>";
	echo GetScrollLightTableStart2( );
	echo "<table height=100% width=100%><tr><td>";
	if( !isset( $mode ) )
	{
    	ScrollTableStart( );
    	echo "<b>���� �����</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( );

    	echo "<div style='text-align:left'><img width=163 height=165 src=images/npcs/favn.jpg hspace=5 vspace=5 align=left>";
    	echo "<i>��� ������ �� ������� � ������, ��������� ���� �������� ���. � �� ���� ����� � �� ��������  ���. ������ ������ ������ ��������� ����������  � ��������� �����, ������������� ������  �������� ������, � ������ ������, � ���������  �������� ����-����. ��� ���� ����. �������, ��� �����  ������ �������� � ����� ������. �� ���� - ������� �, ���� �� �����, �� �������. � ���� �����  �������  ��� �� ���������� ���. ��, ������� �  ������� ���� ��������� ���������, ��� �����  ������� � ������ ���� �� ������� ����� � ����  �����. ����� � ��� �������?</i><br>";
    	echo "<br><b>����</b>: ��-�-�. ��, {$player->login}, ������ !! �� ����� ��� ������� ��  ��������, ��������� ������ �� �������. � ������ ���� ����� �������,  � ���� ���� ������ ��, ��� ���� ����. ������� ������ �������� ��� ���  ��� ����, ������� �� �� �������. �� ������ �������� ����� ������� !!  ��� �� ������. ���� ���������, ����� ���? �� ��� ������: �� ������  ������ �� ��� ���������� ��������. ������ �� ��� ���� ���� ��  ��� ���� ������������. ������ ������������������, �� ����� �����  �������� �����. ���, ����� �� ������ ����, ��� � ���� ����, � �������. �����, {$player->login}. ������ �� � ���� ������ �� ������ �������, ��� ��  ���� �����".($player->sex?"�":"")."...";
    	
    	echo "<br><br><ul>";

		if ( $player->GetQuestValue( 101 ) < time( ) ) //check for quests
		{
   			$player->SetTrigger( 101, 0 ); //disable quest trigger
   			$player->SetTrigger( 102, 0 );
   			$player->SetQuestValue( 102, 0 ); //disable task selected
   			$player->SetQuestValue( 42, 0 ); //clear task counter
   			echo "<br><br><li><a href='game.php?phrase=606'>������� ��� �����, ��� ��� �����. �� � ��� �� ".($player->sex?'�������':'������').", ��� � ���� ����� ��������� ������� ��������. ��� ���? ���� ��, � ".($player->sex?'����':'���')." �� ����� ".($player->sex?'������������':'�����������').".</a>";
		}
		else if ( $player->HasTrigger( 102 ) && !$player->HasTrigger( 101 ) ) //check for quests
		{
   			echo "<br><br><li><a href='game.php?phrase=606'>�  ".($player->sex?'���������':'��������')." ���� �������, ����, ".($player->sex?'������':'������')." ���� �������, ��� ���� ������ �� ��� ������, ��� �� �������.</a>";
		}
		else if( !$player->HasTrigger( 102 ) && !$player->HasTrigger( 101 ) )
		{
   			echo "<br><br><li><a href='game.php?phrase=606'>������� ��� �����, ��� ��� �����. �� � ��� �� ".($player->sex?'�������':'������').", ��� � ���� ����� ��������� ������� ��������. ��� ���? ���� ��, � ".($player->sex?'����':'���')." �� ����� ".($player->sex?'������������':'�����������').".</a>";
		}
		echo "<li><a href=game.php?smiles=1>�� � ���� ��� ��� ��� ���� ��������� ���������, ���������� ��������. �� ������ ��� ����� �������� �� �� ���-�� ��������? � ������� �� �����-������ ���������. �� ������ ������ ��� �����, ���� �� ���������.</a>";
		
		if( $player->level >= 4 )
		{
			$has261 = $player->HasTrigger( 261 );
			$has262 = $player->HasTrigger( 262 );
			$phraseTitle = "����, � ���������� ������. �������, �������, �������� �� ������ ��������. ����� ���, ��� �� � ���� ����-�� ������ �� �������, ��� �� ��� �������� ���������� � ��� �� � ".($player->sex?'������':'����')." �������� ���� �� ���� �����?";
			echo "<br><br><table><tr><td><img src=images/misc/race/chest.png></td><td>";
    		if( !$has262 && !$has261 )
    		{
    			echo "<a href=game.php?phrase=1363>$phraseTitle.</a>";
    		}
    		else if( $has262 )
    		{
    			echo "<a href=game.php?phrase=1369>$phraseTitle.</a>";
    		}
    		else
    		{
    			echo "<a href=game.php?phrase=1372>$phraseTitle.</a>";
    		}
    		echo "</td></tr></table>";
		}

		echo "</ul>";
		
    	echo "</div>";
    }
    else if( $mode == 'smiles' )
    {
    	ScrollTableStart( );
    	echo "<b>������� ��������</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( );

    	echo "<div style='text-align:left'><img width=163 height=165 src=images/npcs/favn.jpg hspace=5 vspace=5 align=left>";
    	echo "<i>����� ����������, �������. ���� �� �����, ���� ��������������� � ��� ������������ �� �����-�� ��� �� ����. ��, ������, �� ������� ��� ��������: ����������� �� ��� �� ������.</i><br>";
    	echo "<b>����</b>: ��, �����, ���� � ���� ��� �������. ��� ��� ��� ��� �����, ��� ��� ����, ���� � ����� ���. ������� �� ����� �� ���� �������. � �� ������ �� ���� ������ ���������� �������, ���� �� �����. �� �� ����, � �� ������ ����... �����, �����, ���������! ���� ���� ��������������. �, ����� �����, �� ���������. ��� ������, ��� ���������. ����� ��� ������ �������� - 2 ������� �� ������. ������, ���� ������, ������ ����� �������� ����-��. ��� ��������� ����� ������ ����. ��, ��� �� ��� ����� � ���� ������������...<br>";

		$moo = array(
			array( '��� ���, � �������, ����� �����������, ��������� � ����������', 3 ),
			array( '� ��� ��� ���� ������ ������, � � ���� ������. �� �� ��� �������, ����� �������', 4 ),
			array( '���� � ����� ������ � ������������ � ���� ��� ��� �����, ��� �����...', 5 ),
			array( "�, �������, ��� ������. ��� �� ����� ����������. ��� � ���� �� ���", 7 )
		);

		echo "<table><colgroup><col width=140><col width=80><col width=140>";
		include_once("smiles_list.php");
		if( isset( $_GET['smile_do'] ) )
		{
			$set_id = (int)$_GET['smile_do'];
			$pid = $player->player_id;
			$ok = true;
			if( isset( $_GET['smile_whom'] ) )
			{
				$_GET['smile_whom'] = conv_utf($_GET['smile_whom']);
				$_GET['smile_whom'] = htmlspecialchars($_GET['smile_whom'],ENT_QUOTES);
				$pid = f_MValue( "SELECT player_id FROM characters WHERE login='".$_GET['smile_whom']."';" );
				if( !$pid )
				{
					echo "<script>alert('��������� � ������ {$_GET[smile_whom]} �� ����������');</script>";
					$ok = false;
				}
				else if ($set_id >= 10)
				{
					$res = f_MValue("SELECT expires FROM paid_smiles WHERE player_id=$pid AND set_id=$set_id");
					if ($res == -1)
					{
						echo "<script>alert('� ��������� � ������ {$_GET[smile_whom]} ��� ���� ����� �������');</script>";
						$ok = false;
					}
				}
			}
			if( $ok )
			{
/*
			if ($player->Rank() != 1)
			{
	    			if( $set_id < 0 || $set_id > 3 ) RaiseError( "���������� ����� ������ ��������� $set_id" );
    				if( $player->SpendUMoney( $moo[$set_id][1] ) )
    				{
	    				$player->AddToLogPost( -1, - $moo[$set_id][1], 21, 1000, 2, $pid );
		        			f_MQuery( "LOCK TABLE paid_smiles WRITE" );
        					$exp = f_MValue( "SELECT expires FROM paid_smiles WHERE player_id={$pid} AND set_id=$set_id" );
        					if( $exp > time( ) ) $nexp = $exp + 28 * 24 * 60 * 60;
        					else $nexp = time( ) + 28 * 24 * 60 * 60;
	        				if( !$exp )
	        				{
        						f_MQuery( "INSERT INTO paid_smiles ( player_id, set_id, expires ) VALUES ( {$pid}, $set_id, $nexp )" );
        					}
	        				else f_MQuery( "UPDATE paid_smiles SET expires=$nexp WHERE player_id={$pid} AND set_id=$set_id" );
        					f_MQuery( "UNLOCK TABLES" );
	        				if( $pid != $player->player_id )
        					{
    							f_MQuery( "INSERT INTO post( sender_id, receiver_id, title, content, money, np, deadline ) VALUES ( {$player->player_id}, $pid, '����� ���������', '�������� {$player->login} ������� ��� ����� ���������', '0', '0', '0' )" );
    							sendMessage( $pid, "� ��� ����� ��������� � ��������" );
	        				}
        				}
			}
			else
*/
			{
				if( $set_id < 10 || $set_id > 166 ) RaiseError( "���������� ����� �������� $set_id" );
				if( $player->SpendUMoney( 2 ) )
				{
					f_MQuery("LOCK TABLE paid_smiles WRITE");
					$res = f_MValue("SELECT expires FROM paid_smiles WHERE player_id=$pid AND set_id=$set_id");
					if ($res == -1)
					{
						echo "<script>alert('� ��� ��� ���� ����� �������');</script>";
						f_MQuery("UNLOCK TABLES");
						$player->AddUMoney(2);
					}
					else
					{
						if ($res == 0)
							f_MQuery("INSERT INTO paid_smiles ( player_id, set_id, expires ) VALUES ({$pid}, $set_id, -1)");
						else
							f_MQuery("UPDATE paid_smiles set expires=-1 WHERE player_id={$pid} AND set_id=$set_id");
						$numSm = f_MValue("SELECT COUNT(*) FROM paid_smiles WHERE set_id<10000 AND player_id={$pid} AND set_id>=10 AND expires=-1");
						f_MQuery("UNLOCK TABLES");
						$player->AddToLogPost( -1, -2, 21, 1000, 2, $pid );
						$lck = 0;
						if ($numSm >= 150) $lck=15;
						elseif ($numSm >= 100) $lck=10;
						elseif ($numSm >= 60) $lck=7;
						elseif ($numSm >=40) $lck=5;
						elseif ($numSm >=25) $lck=3;
						elseif ($numSm >=10) $lck=2;
						elseif ($numSm >=5) $lck=1;
						$plr = new Player($pid);
						$plr->RemoveEffect(30, true);
						if ($lck)
						{
							$plr->AddEffect(30, 0, "�������� ������", "����� vip-�������: ".$numSm, "../../images/smiles/ura.gif", "13:".$lck.".", -1);
						}
						if( $pid != $player->player_id )
        						{
    							f_MQuery( "INSERT INTO post( sender_id, receiver_id, title, content, money, np, deadline ) VALUES ( {$player->player_id}, $pid, '�������', '�������� {$player->login} ������� ��� ������� *".$vsmiles[$set_id][0]."*', '0', '0', '0' )" );
    							sendMessage( $pid, "� ��� ����� ��������� � ��������" );
		        				}
					}
				}
				else
					echo "<script>alert('� ��� �� ������� ��������');</script>";
			}
		}
	}
?>
			<script>
			function smiles_buy(i,v)
			{
				if( confirm( "������ ������� �� " + v + " ���?" ) )
					location.href="game.php?smiles=1&smile_do=" + i;
			}
			function smiles_prolong(i,v)
			{
				if( confirm( "�������� ����� ��������� �� ����� �� " + v + " ���?" ) )
					location.href="game.php?smiles=1&smile_do=" + i;
			}
			function smiles_pres(i,v)
			{
				var login = _( "nick" + i ).value;
				if( confirm( "�������� ��������� " + login + " ������� �� " + v + " ���?" ) )
					location.href="game.php?smiles=1&smile_do=" + i + "&smile_whom=" + encodeURIComponent( login );
			}
			</script>
<?
		/*
		if ($player->Rank() != 1)
		for( $i = 0; $i < 4; ++ $i )
		{
			$exp = f_MValue( "SELECT expires FROM paid_smiles WHERE player_id={$player->player_id} AND set_id=$i" );
			
			echo "<tr>";
			echo "<td colspan=2><br><br>{$moo[$i][0]}<br>";
			if( $exp < time( ) ) echo "������: <font color=darkred>�� �������</font><br>";
			else echo "������: <font color=darkgreen>������� �� <b>".date( "d.m.Y H:i", $exp )."</b></font><br>";
			echo "</td>";
			echo "<td rowspan=3 valign=top><br>"; foreach($vsmiles[$i] as $b) echo "<img src=images/smiles/{$b}.gif> "; echo "</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td><img src=images/umoney.gif width=11 height=11> <b>{$moo[$i][1]}</b> �� <b>28</b> ����</td>";
			if( $exp > time( ) ) echo "<td><button onclick='smiles_prolong($i,{$moo[$i][1]});' class=n_btn>��������</button></td>";
			else echo "<td><button onclick='smiles_buy($i,{$moo[$i][1]});' class=n_btn>������������</button></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td><input id=nick$i class=m_btn value='��� ���������' style='color:#808080;width:160px;' onfocus='if(this.value==\"��� ���������\"){this.value=\"\"; this.style.color=\"black\";}' onblur='if(this.value==\"\"){this.style.color=\"#808080\";this.value=\"��� ���������\";}'></td>";
			echo "<td><button onclick='smiles_pres($i,{$moo[$i][1]});' class=n_btn>��������</button></td>";
			echo "</tr>";
		}
		*/
		//if ($player->Rank() == 1)
		for ($i = 10; $i <= 166; $i++)
		{
			$exp = f_MValue( "SELECT expires FROM paid_smiles WHERE player_id={$player->player_id} AND set_id=$i" );

			echo "<tr><td colspan=3><hr></td></tr>";
			echo "<tr>";
			echo "<td>";
			if( $exp != -1 ) echo "������: <font color=darkred>�� �������</font><br>";
			else echo "������: <font color=darkgreen>�������</font><br>";
			echo "</td>";
			if ($exp != -1)
				echo "<td><button onclick='smiles_buy($i,2);' class=n_btn>������������</button></td>";
			else
				echo "<td>&nbsp;</td>";
			echo "<td rowspan=2 valign=top align=right><br>";
			if ($player->Rank() == 1) echo $i." => ".f_MValue("SELECT COUNT(*) FROM paid_smiles WHERE set_id=$i AND expires=-1")."&nbsp;";
			foreach($vsmiles[$i] as $b) echo "<img src=images/smiles/{$b}.gif> ";
			echo "</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td><input id=nick$i class=m_btn value='��� ���������' style='color:#808080;width:160px;' onfocus='if(this.value==\"��� ���������\"){this.value=\"\"; this.style.color=\"black\";}' onblur='if(this.value==\"\"){this.style.color=\"#808080\";this.value=\"��� ���������\";}'></td>";
			echo "<td><button onclick='smiles_pres($i,2);' class=n_btn>��������</button></td>";
			echo "</tr>";
			
		}
			?>
			
			<?

		echo "</table>";
    }
    else if( $mode == 'fail' )
    {
    	ScrollTableStart( );
    	echo "<b>������ �� ��������</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( );

    	echo "<big><b>������ <font color=darkred>�� ���</font> ��������</b></big><br>��������� � �������������� ��� ����������� ������";
	}
	else if( $mode == 'sms' )
	{
	   	ScrollTableStart( );
	   	echo "<b>������� � ������� SMS-���������</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( );
             
    	if( !isset( $_GET['country'] ) )
    	{
	    	echo "�������� ������:<br>";
	    	echo "<a href=game.php?p=sms&country=0>������</a><br>";
	    	echo "<a href=game.php?p=sms&country=1>�������</a><br>";
	    	echo "<a href=game.php?p=sms&country=2>����������</a><br>";
	    	echo "<a href=game.php?p=sms&country=3>������</a><br>";
	    	echo "<a href=game.php?p=sms&country=4>���������</a><br>";
	    	echo "<a href=game.php?p=sms&country=5>�������</a><br>";
	    	echo "<a href=game.php?p=sms&country=6>�������</a><br>";
	    	echo "<a href=game.php?p=sms&country=7>�����������</a><br>";
	    }
	    else if( $_GET['country'] == 0 )
	    {
		 	echo "����� ������ <b>������</b> �������, ��������� ��������� � �������<br>";
        	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
        	echo "�� ����� <b>6365</b><br>��������� ���������: 75 ������ � ������ ���<br><br>";

        	echo "����� ������ <b>10</b> ��������, ��������� ��������� � �������<br>";
        	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
        	echo "�� ����� <b>8385</b><br>��������� ���������: 180 ������ � ������ ���<br><br>";

        	echo "<small>�������������� ���������: ������, ���, �������, ON GSM, Tele2, Utel, ����, ����������, �������������, ��������� GSM, ���, �����, ���, ���, �����������, ���� ����, ������, ���� GSM, ����-�������� ������� ����, ���������-GSM, �������� ���������, ����� GSM </small>";
	    
		   echo "<br/><br/><br/><small><small>��������� ������� � ������� �������-���������� ��������������� ����� ����������. ��������� ���������� ����� ������ � ������� \"������ �� �������� �������\" �� ����� www.mts.ru ��� ����������� � ���������� ����� �� 
�������� 8 800 333 0890 (0890 ��� ��������� ���)</small></small>";
		 }
	    else if( $_GET['country'] == 1 )
	    {
		 	echo "����� ������ <b>10</b> ��������, ��������� ��������� � �������<br>";
        	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
        	echo "�� ����� <b>8385</b><br>��������� ���������: 30 ������<br><br>";

        	echo "<small>��� ���� ������������ GSM ����������</small>";
        	echo "<br>";
        	echo "<br><br><br>����� ������������� ����������� <a href='http://www.smsonline.ru/' target=_blank>��� ������</a>, �������������� ������: (044) 383-20-90, (� 9:00 �� 18:00, � ������� ���).<br><br>���������� ������������ ������, ����� ��� � ����� � �� ����������� ����� �������: &laquo;����� � ������� � ������ ���. ������������� ��������� ���� � ���������� ���� � ������� 7.5% �� ��������� ������ ��� ����� ���&raquo;.";
	    }
	    else if( $_GET['country'] == 3 )
	    {
		 	echo "����� ������ <b>������</b> �������, ��������� ��������� � �������<br>";
        	echo "<b><font color=darkred>3FF tal {$player->player_id}</font></b><br>";
        	echo "�� ����� <b>1897</b><br>��������� ���������: 1.5 ���� � ������ ���<br><br>";

        	echo "<small>�������������� ���������: Bite, LMT, Tele2</small>";
	    }
	    else if($_GET['country'] == 2 )
	    {
	    /*
		 	echo "����� ������ <b>������</b> �������, ��������� ��������� � �������<br>";
        	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
        	echo "�� ����� <b>1315</b><br>��������� ���������: 9900 ����������� ������ � ������ ���<br><br>";

        	echo "<small>�������������� ���������: ���, ������</small>";
        	*/
        	echo "�� ������ ������, ������ �� ��������.";
	    }
	    else if( $_GET['country'] == 4 )
	    {
		 	echo "����� ������ <b>������</b> �������, ��������� ��������� � �������<br>";
        	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
        	echo "�� ����� <b>6365</b><br>��������� ���������: 300 ����� (KZT) � ������ ���<br><br>";

        	echo "<small>�������������� ���������: Beeline Kazakhstan, Kcell, Activ, NEO, PAThWORD, Dalacom, City</small>";
	    }
	    else if( $_GET['country'] == 6 )
	    {
		 	echo "����� ������ <b>���</b> �������, ��������� ��������� � �������<br>";
        	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
        	echo "�� ����� <b>4009</b><br>��������� ���������: 1000 ���� � ������ ���<br><br>";

        	echo "<small>�������������� ���������: Beeline Armenia, MTS (VivaCell), K-Telekom </small>";
	    }
	    else if( $_GET['country'] == 5 )
	    {
		 	echo "����� ������ <b>���</b> �������, ��������� ��������� � �������<br>";
        	echo "<b><font color=darkred>FF tal {$player->player_id}</font></b><br>";
        	echo "�� ����� <b>15330</b><br>��������� ���������: 15 ��������� ���� � ������ ���<br><br>";

		 	echo "����� ������ <b>����</b> ��������, ��������� ��������� � �������<br>";
        	echo "<b><font color=darkred>FF tal {$player->player_id}</font></b><br>";
        	echo "�� ����� <b>13015</b><br>��������� ���������: 39 ��������� ���� � ������ ���<br><br>";

        	echo "<small>�������������� ���������: EMT, Radiolinija Eesti, Tele2</small>";
	    }
	    else if( $_GET['country'] == 7 )
	    {
		 	echo "����� ������ <b>������</b> �������, ��������� ��������� � �������<br>";
        	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
        	echo "�� ����� <b>9645</b><br>��������� ���������: 4.72 ������ � ������ ���<br>";
        	echo "<small>��������: <b>Azercell</b></small><br /><br />";

		 	echo "����� ������ <b>�����</b> ��������, ��������� ��������� � �������<br>";
        	echo "<b><font color=darkred>RR tal {$player->player_id}</font></b><br>";
        	echo "�� ����� <b>3304</b><br>��������� ���������: 5.9 ������ � ������ ���<br>";
        	echo "<small>��������: <b>BakCell</b></small><br /><br />";
	    }	    
	                     /*
    	echo "����� ������ <b>����</b> ��������, ��������� ��������� � �������<br>";
    	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
    	echo "�� ����� <b>7250</b><br><br>";

    	echo "����� ������ <b>10</b> ��������, ��������� ��������� � �������<br>";
    	echo "<b><font color=darkred>tal {$player->player_id}</font></b><br>";
    	echo "�� ����� <b>5373</b><br><br>";

    	echo "<small>��������� � �������� ����, ��� ���������� ������ ������� �����<br>";
    	echo "����� ��������� ����������� ��������� ������������ ��������� ���������, ���������, ��� ����� <b>tal</b> � <b>{$player->player_id}</b> ��������� ����� ���� ������<br>";
    	echo "��������! ����� <b>tal</b> ������ ���� �������� ��������� �������.<br></small><br>";

    	echo "<div id=sms_ext><a href='javascript:show_ext();'>��������� ��������� � �������������� ��������� �����</a></div>";
    	echo "<div id=sms_ext_info style='display:none'>";
    	?>
    	<script>
    	function show_ext( ) { _("sms_ext_info").style.display="";_("sms_ext").style.display="none"; };
    	</script>
<b>��� ���������� ���������:</b><br>
��� �� ����� <b>7250</b> - 2.5$ � ���<br>
��� �� ����� <b>5373</b> - 5$ � ���<br>
<br>
�������������� ��������� ���������:<br>
��� "�������"                          <br>
��� "�������-���������"                    <br>
��� "�������-�����������"<br>
��� "�������-�����"<br>
��� "�������-������"<br>
��� "��������� GSM"<br>
��� "���-��������"<br>
������-�������� ������ ��� "�������"<br>
��� "�����-���"<br>
��� "���"<br>
��� "���������" (������)<br>
��� "���"<br>
��� &lt;���������� ������� �����&gt; (����-2)<br>
��� "������������ - 2000" (�����)<br>
��� "������� ����" (��� "���������������", Utel)<br>
��� &lt;��������� GSM&gt;<br>
��� "���"<br>
��� "�������������"<br>
��� "������"<br>
��� ������������� (��� ��������)<br>
��� ���������� (��� ��������)<br>
��� ��������� GSM<br>
��� <������ �������> SKYLINK<br>
<br>
<b>��� �������:</b><br>
��� �� ����� <b>7250</b> - 2.5$ � ���<br>
��� �� ����� <b>5373</b> - 5$ � ���<br>
<br>
�������������� ��������� ���������:<br>
��������, ���, ������ �������, Life<br>
<br>
<b>��� ����������:</b><br>
��� �� ����� <b>7250</b> - 2.5$ � ���<br>
��� �� ����� <b>5373</b> - 5$ � ���<br>
<br>
�713,00 (K-cell) � �600,00 (������)<br>
<br>
�������������� ��������� ���������:<br>
K-cell (GSM Kazakhstan) � ������ ��������� (���-���)<br>
    	<?
    	echo "</div>"; */

//    	ScrollTableEnd( );
	}/**/
    else if( $mode == 'wm' )
    {
    	ScrollTableStart( );
    	echo "<b>������ ����� ������� Web-Money</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( );
//	echo "������ �������� ���������<br>�� ��������� ����������� ����������� � ������������� ����<br>";
//point1

    ?>
    <script>
    function wm_update()
    {
    	var coeff = [];// ����� ������ �� ���� ������
    		 coeff['R346197619375'] = 10;   // �����
    		 coeff['U426214563258'] = 2.7;  // ������
    		 coeff['Z301545765621'] = 0.35; // �������
    		  
    	var val = parseInt( document.getElementById( 'wm_num' ).value );
    	var valute = document.getElementById( 'wm_valute' ).value; 
    	
    	if( isNaN( val ) )
    	{
    		val = 0;
    	}
    	
    	document.getElementById( 'wm_price' ).value = val * coeff[valute];
    }
   </script>
	<a href="/game.php">�����</a><br />
	<br />
	<form target="_blank" action="https://merchant.webmoney.ru/lmi/payment.asp" method="POST">
	   <input type="hidden" name="LMI_PAYMENT_DESC" value="������� �������� ��� ������ <?=$player->login?>" />
	   <input type="hidden" name="LMI_PAYMENT_NO" value="<?=$player->player_id?>" />
		<table>
   	 	<tr>
	   	 	<td style="font-weight: bold; vertical-align: top;">���������� ��������:</td>
	   	 	<td>
	   	 		<input type="text" class="m_btn" id="wm_num" value="15" onkeyup="wm_update()">
	   	 	</td>
	   	</tr>
			<tr>
				<td style="font-weight: bold; vertical-align: top;">���������:</td>
				<td>
					<input type="text" class="m_btn" name="LMI_PAYMENT_AMOUNT" id="wm_price" value="150" />
				</td>
			</tr>
			<tr>
				<td style="font-weight: bold; vertical-align: top;">������:</td>
				<td>
					<select id="wm_valute" name="LMI_PAYEE_PURSE" class="s_btn" onclick="wm_update()" onchange="wm_update()" onselect="wm_update()">
						<option value="R346197619375">�����</option>
						<option value="U426214563258">������</option>
						<option value="Z301545765621">�������</option>
					</select>
				</td>
			</tr>
    		<tr>
    			<td>&nbsp;</td>
    			<td>
    				<input type="submit" class="s_btn" value="������">
    			</td>
    		</tr>
    	</table>
	</form>
	<span style="font-size: 8px; color: #d36008;"><b>����������� � ������</b><br />������������ ������ � ������ ��������������� �� �� ������ ���� ���� �����������, ���������������� ������� WebMoney Transfer. �� �������� ����������� ������������, ����������� ������, � �������������� ��������� ������� � ����� � ������������. �����������, ��������������� ������� WebMoney Transfer, �� �������� ������������ �������������� ��� ���� �������������� �� ������� � �������������� ����� � �� ����� ������� ��������������� �� ���� ������������.<br />����������, ������������� �� ������� WebMoney Transfer, ���� ������������ ���� ��������� ��� ����� � ������������ ��������. ��� �������������� �� ������ ������� � �� ��������, ��� �� �����-���� ������� ������� � ��������� ���������� ������� WebMoney.';
    <?

//point1
    }
    else if( $mode == 'rbk' )
    {
    	ScrollTableStart( );
    	echo "<b>������ ����� ������� RBK Money</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( );


    ?>
    <script>
    function rbk_update()
    {
    	val = parseInt( document.getElementById( 'rbk_num' ).value );
    	if( isNaN( val ) ) val = 0;
    	document.getElementById( 'rbk_price' ).value = val * 10;
    }
    </script>
    <?
    	echo "<a href=game.php>�����</a><br><br>";
	   	echo "<form target=_blank action=https://rbkmoney.ru/acceptpurchase.aspx method=POST><table>";
	   	echo "<input type=hidden name=eshopId value=2002630>";
	   	echo "<input type=hidden name=serviceName value='������� �������� ��� ������ {$player->login}'>";
	   	echo "<input type=hidden name=orderId value='{$player->player_id}'>";
	   	echo "<input type=hidden name=recipientCurrency value='RUR'>";
    	echo "<tr><td>���������� ��������:</td><td><input class=m_btn type=text id=rbk_num value=15 onkeyup='rbk_update()'></td></tr>";
    	echo "<tr><td>���������:</td><td><input class=m_btn type=text name=recipientAmount id=rbk_price value=150 onkeyup='rbk_update()'> ���.</td></tr>";
    	echo "<tr><td>&nbsp;</td><td><input type=submit class=s_btn value='������'></td></tr>";

    	echo "</table></form>";
    }
    else if( $mode == 'yd' )
    {
    	ScrollTableStart( );
    	echo "<b>������ ����� ������� ������.������</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( );


    	echo "<a href=game.php>�����</a><br><br>";
    	echo "� ��������� ������ ������������������ ������� ������ �������� �� ��������.<br>";
    	echo "��� ������������ �������� ����� ������� ������.������ ��������� � ��������������� <b>�������</b>";
	}
	else if( $mode == '2pay' )
	{
    	ScrollTableStart( );
    	echo "<b>��� ������ � ���� ������ ������ �������</b>";
    	ScrollTableEnd( );
    	echo "</td></tr><tr><td height=100%>";
    	ScrollTableStart( 'left' );

		?>
        <div id="dvapay_terminals" style="float: left; width: 300px;">
        </div>
        <div id="dvapay_emoney" style="float: left; width: 300px;">
        </div>
        <br style="clear: both; "/>
        <div id="dvapay_ecard" style="float: left; width: 300px;">
        </div>
        <div id="dvapay_ebank" style="float: left; width: 300px;">
        </div>
        <br style="clear: both; "/>
        <div id="dvapay_esendmoney" style="float: left; width: 300px;">
        </div>

        <script>
        var id='2149';
        var v1='<?=$player->player_id?>';
        var v2='';
        var v3='';
        var page='3021';
        var country='0';
        var conf='123';
        document.write('<script type="text/javascript" src="http://2pay.ru/view/script.php?id='+id+'&v1='+v1+'&v2='+v2+'&v3='+v3+'&country='+country+'&page='+page+'&conf='+conf+'"></' + 'script>');
        </script>
        <?
	}
	elseif( $mode == 'selfban' )
	{
		// ���������������
		switch( $_GET['selfban'] )
		{
			// ���������� ���������������
			case 'begin':
			{
    			ScrollTableStart( );
    			echo "<b>������ ����� ������� ������.������</b>";
    			ScrollTableEnd( );
    			echo "</td></tr><tr><td height=100%>";
    			ScrollTableStart( );
    			?>
					123    			
    			<?
				break;
			}
			
			// ������
			case 'default':
			{
				break;			
			}
		}
	}

	ScrollTableEnd( );
	echo "</td></tr></table>";
	ScrollLightTableEnd( );
echo "</td><td width=50% height=100%>";
	echo GetScrollLightTableStart2('center', 'top' );
	echo "<table width=100%><tr><td>";

	ScrollTableStart( );
	echo "<b>������ �������</b>";
	ScrollTableEnd( );

	echo "</td></tr><tr><td>";

	ScrollTableStart( 'left' );
	//echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'><a href=game.php?p=sms>� ������� SMS-���������</a>";
	echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'><a href=http://2pay.ru/oplata/number.html?id=2149&v1={$player->player_id} target=_blank>����� ��������� QiWi</a><br>";
//	echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'>� ������� SMS-���������";
	echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'><a href=game.php?p=wm>����� ������� WebMoney</a>";
	echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'><a href=http://2pay.ru/oplata/yandex/?id=2149&v1={$player->player_id} target=_blank>����� ������� Yandex.������</a><br>";
	echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'><a href=game.php?p=2pay>��� ������ � ���� ������...</a><br>";
	echo "<br><li STYLE='list-style-image: URL(\"images/dots/dot-exit.gif\")'><a href=game.php?phrase=515>�������� �����</a>";
	ScrollTableEnd( );

	echo "</td></tr><tr><td>";

	ScrollTableStart( );
	echo "<b>������������ �������</b>";
	ScrollTableEnd( );
	echo "</td></tr><tr><td>";
	ScrollTableStart( 'left' );

	if( !$frozen_premiums )
	{

    	?>

    	<script>

    	function activate( id, s, c, l ) { if( confirm( '�� �������, ��� ������ ��������� ' + c + ' �������� � ������������ ' + s + ' �� ' + l + ' ����?' ) ) location.href='game.php?activate=' + id + '&l=' + l; }
    	function prolong( id, s, c, l ) { if( confirm( '�� �������, ��� ������ ��������� ' + c + ' �������� � �������� ' + s + ' �� ' + l + ' ����?' ) ) location.href='game.php?activate=' + id + '&l=' + l; }

    	</script>
    	<?

    	foreach( $premiums as $a=>$b )
    	{
    		echo "<b><font color=green>$b[0]</font></b><br>";
    		echo "$b[1]";

    		$res = f_MQuery( "SELECT deadline FROM premiums WHERE player_id={$player->player_id} AND premium_id=$a" );
    		$arr = f_MFetch( $res );
    		if( !$arr || $arr[0] < time( ) )
    		{
        		echo "������: <font color=darkred>�� �������</font><br>";
        		echo "<table cellspacing=0 cellpadding=0><tr><td><img width=11 height=11 src=images/umoney.gif> <b>$b[2]</b> �� <b>28</b> ����&nbsp;</td><td><button onclick='activate($a, \"$b[0]\", $b[2], 28)' class=n_btn>������������</button></td></tr>";
        		echo "<tr><td><img width=11 height=11 src=images/umoney.gif> <b>$b[3]</b> �� <b>7</b> ���� &nbsp;</td><td><button onclick='activate($a, \"$b[0]\", $b[3], 7)' class=n_btn>������������</button></td></tr>";
        		echo "<tr><td><img width=11 height=11 src=images/umoney.gif> <b>$b[4]</b> �� <b>1</b> ���� &nbsp;</td><td><button onclick='activate($a, \"$b[0]\", $b[4], 1)' class=n_btn>������������</button></td></tr></table><br>";
    		}
    		else
    		{
        		echo "������: <font color=green>������� ��: <b>".date( "d.m.Y H:i", $arr[0] )."</b></font><br>";
        		echo "<table cellspacing=0 cellpadding=0><tr><td><img width=11 height=11 src=images/umoney.gif> <b>$b[2]</b> �� <b>28</b> ����&nbsp;</td><td><button onclick='prolong($a, \"$b[0]\", $b[2], 28)' class=n_btn>��������</button></td></tr>";
        		echo "<tr><td><img width=11 height=11 src=images/umoney.gif> <b>$b[3]</b> �� <b>7</b> ���� &nbsp;</td><td><button onclick='prolong($a, \"$b[0]\", $b[3], 7)' class=n_btn>��������</button></td></tr>";
        		echo "<tr><td><img width=11 height=11 src=images/umoney.gif> <b>$b[4]</b> �� <b>1</b> ���� &nbsp;</td><td><button onclick='prolong($a, \"$b[0]\", $b[4], 1)' class=n_btn>��������</button></td></tr></table><br>";
    		}
    	}
	}
	
	else
	{
		echo "<b>���� �������� ����������</b><br>";
		$res = f_MQuery( "SELECT * FROM frozen_premiums WHERE player_id={$player->player_id} ORDER BY premium_id" );
		$whena = 0;
		while( $arr = f_MFetch( $res ) )
		{
			echo "<li>".$premiums[$arr['premium_id']][0]." (".my_time_str( $arr['duration'], false ).")<br>";
			$whena = $arr['available'];
		}
?>

<script>
function unfreeze()
{
	if( confirm( '�� �������, ��� ������ ����������� ��������?' ) )
		location.href='game.php?ufreeze=1';
}
</script>

<?
		if( $whena < time( ) ) echo "<br><li><a href='javascript:unfreeze()'>�����������</a>";
		else echo "<br><i>�� �� ������� ����������� �������� ��� ".my_time_str( $whena - time( ), false )."</i>";
	}

	ScrollTableEnd( );
	echo "</td></tr><tr><td>";

	// freezing
	if (!$frozen_premiums){
	echo "<table width=100% cellspacing=0 cellpadding=0 border=0><tr><td>";
	ScrollTableStart( 'center' );
	echo "<b>��������� ���������</b><br>";
	ScrollTableEnd( );
	echo "</td></tr></table>";
	echo "</td></tr><tr><td>";
	echo "<table width=100% cellspacing=0 cellpadding=0 border=0><tr><td>";
	ScrollTableStart( 'center' );
	echo "�� ������ ���������� ��������, ���� �� �����-�� ������� �� �� ������� �������� � ���� � ������� ���������� ���� ��� ����� ������ ������ �������.<br>";
	echo "���������� ����� ������ ��� �������� �����. ����������� �� ����� � ����� ������, �� �� ������ ��� ����� ���� ����� � ������� ���������.<br>";
	echo "� ������� ����� ������� ��������� �� �� ������� ������������ ��� ���������� ����� ��������.<br><br>";
?>

<script>
function pfreeze()
{
	if( confirm( '�� �������, ��� ������ ���������� ��������? � ������� ���� ���� �� �� ������� ����������� �� �������.' ) )
		location.href='game.php?freeze=1';
}
</script>

<?
	
	echo "<li><a href='javascript:pfreeze()'>���������� ��������</a>";
	
	ScrollTableEnd( );
	echo "</td></tr></table>";
	echo "</td></tr><tr><td>";
	
	}

	ScrollTableStart( );
	echo "<b>�������������� ������</b>";
	ScrollTableEnd( );

	echo "</td></tr><tr><td>";

	echo "<table width=100% cellspacing=0 cellpadding=0 border=0><tr>";
		echo "<td width=49% height=150>";
        	ScrollTableStart( 'center' );
        		echo "<b>������ ��������</b><br>";
        		echo "<small>���� ����� �������� ���� ������� �� <nobr><img width=11 height=11 src=images/money.gif> 5000</nobr> �� ������</small><br>";
        		echo "<br><small><b>������� ���������� ��������:</b></small><br>";
        		if( isset( $_GET['talsell'] ) )
        		{
        			$val =(int)$_GET['talsell'];
        			if( $val <= 0 ) echo "<small><font color=darkred>������� ������������� �����</font></small><br>";
        			else if( !$player->SpendUMoney( $val ) ) echo "<small><font color=darkred>� ��� ������������ ��������</font></small><br>";
           			else
           			{
           				$player->AddMoney( $val * 5000 );
           				$player->AddToLogPost( 0, $val * 5000, 21, 5000, 0, $val );
           				$player->AddToLogPost( -1, - $val, 21, 5000, 0, $val );
           				echo "<small><font color=blue>�� ������� $val ".my_word_str($val,'������',"�������","��������")." � �������� 5000 ��������</font></small><br>";
           			}

        		}
        		echo "<form action=game.php method=GET><input style='text-align:center' class=m_btn name=talsell value=0 type=text><br>";
        		echo "<table cellspacing=0 cellpadding=0 border=0><tr><td><img src=images/top/b.png></td><td><input type=submit class=n_btn value=�������></td><td><img src=images/top/c.png></td></tr></table>";
        		echo "</form>";

        	ScrollTableEnd( );
        echo "</td><td>&nbsp;</td><td width=49% height=150>";
        	ScrollTableStart( 'center' );
        		echo "<b>�������</b><br>";
				echo "<small>�� ������ ��������� �����, ���������� � ������, �� 30 ������.<br><br><b>��������� ������<br></b></small><img src=images/umoney.gif width=11 height=11> <b>1</b><br><br>";
				if( isset( $_GET['lek'] ) )
				{
					$res = f_MQuery( "SELECT real_deaths FROM characters WHERE player_id={$player->player_id}" );
					$arr = f_MFetch( $res );
					if( !$arr[0] ) echo "<small><font color=darkred>���� ����� � ������ ����������</font></small><br>";
					else if( !$player->SpendUMoney( 1 ) ) echo "<small><font color=darkred>� ��� ������������ ��������</font></small><br>";
					else
					{
						echo "<small><font color=blue>����� ������� ���������</font></small><br>";
						f_MQuery( "UPDATE characters SET real_deaths=real_deaths-1 WHERE player_id={$player->player_id}" );
	       				$player->AddToLogPost( -1, - 1, 21, 1000, 1 );
					}
				}
				echo "<table cellspacing=0 cellpadding=0 border=0><tr><td><img src=images/top/b.png></td><td><button class=n_btn onclick='if( confirm( \"��������� �����, ���������� � ������, �� 1 ������?\" ) ) location.href=\"game.php?lek=1\";'>���������</button></td><td><img src=images/top/c.png></td></tr></table>";
        	ScrollTableEnd( );
		echo "</td>";
	echo "</tr></table>";
	
	echo "</td></tr><tr><td>";

	echo "<table width=100% cellspacing=0 cellpadding=0 border=0><tr>";
		echo "<td width=49% height=150>";
        	ScrollTableStart( 'center' );
        		echo "<b>����� ����</b><br>";
        		$sex_price = 0;
        		if( $player->level > 4 || $player->HasTrigger( 555 ) ) $sex_price = 40;
        		
        		echo "<small>���� ����� ������ ������� ��� ����� ������ ����<br>";
        		if( isset( $_GET['changesex'] ) )
        		{
        			if( !$player->SpendUMoney( $sex_price ) ) echo "<small><font color=darkred>� ��� ������������ ��������</font></small><br>";
           			else
           			{
	       				$player->AddToLogPost( -1, - $sex_price, 21, 1000, 4 );
           				$player->SetTrigger( 555, 1 );
           				$new_sex = 1 - $player->sex;
           				$old_sex = $player->sex;
           				f_MQuery( "UPDATE characters SET sex={$new_sex} WHERE player_id={$player->player_id}" );
           				f_MQuery( "UPDATE player_avatars SET avatar='f{$new_sex}w.jpg' WHERE player_id={$player->player_id} AND avatar='f{$old_sex}w.jpg'" );
           				f_MQuery( "UPDATE player_avatars SET avatar='f{$new_sex}n.jpg' WHERE player_id={$player->player_id} AND avatar='f{$old_sex}n.jpg'" );
           				f_MQuery( "UPDATE player_avatars SET avatar='f{$new_sex}f.jpg' WHERE player_id={$player->player_id} AND avatar='f{$old_sex}f.jpg'" );
           				echo "<script>parent.char_ref.location.href='char_ref.php?rnd=".mt_rand()."';</script>";
           			}
        		}
        		if( $player->level <= 4 && !$player->HasTrigger( 555 ) )
        		{
            		echo "<br>���� ����� ���� ������ �� ���� ���������� ������ ��������������� ���������.</small><br><br>";
        		}
        		else
        		{
            		echo "<br>��������� ������:</small><br> <nobr><img width=11 height=11 src=images/umoney.gif> <b>40</b></nobr></small><br><br>";
            	}
				echo "<table cellspacing=0 cellpadding=0 border=0><tr><td><img src=images/top/b.png></td><td><button class=n_btn onclick='if( confirm( \"�� �������, ��� ������ ������� ���?\" ) ) location.href=\"game.php?changesex=1\";'>������� ���</button></td><td><img src=images/top/c.png></td></tr></table>";

        	ScrollTableEnd( );
        echo "</td><td>&nbsp;</td><td width=49% height=150>";
        	ScrollTableStart( 'center' );
        		echo "<b>����� �����</b><br>";
        		echo "<small>� ����� ���� ������ � ��������� ������, ������� ����� ��������������� � ����� ����� �� <nobr><img width=11 height=11 src=images/umoney.gif> <b>50</b></nobr></small><br>";
        		echo "<br><small><b>������� �������� ���:</b></small><br>";
        		if( isset( $_GET['changename'] ) )
        		{
        			$newname =$_GET['changename'];
        			if( !$player->SpendUMoney( 50 ) ) echo "<small><font color=darkred>� ��� ������������ ��������</font></small><br>";
           			else
           			{
           				function correct_login( $a )
                        {	
                        	if( ( $a[0] >= 'a' && $a[0] <= 'z' ) || ( $a[0] >= 'A' && $a[0] <= 'Z' ) )
                        		$eng = 1;
                        	else if( ( $a[0] >= '�' && $a[0] <= '�' ) || ( $a[0] >= '�' && $a[0] <= '�' ) )
                        		$eng = 0;
                        	else
                        	{
                        		return 0;
                        	}
                        	
                        	$l = strlen( $a );
                        	for( $i = 1; $i < $l; ++ $i )
                        	{
                        		if( ( $a[$i] >= 'a' && $a[$i] <= 'z' ) || ( $a[$i] >= 'A' && $a[$i] <= 'Z' ) )
                        		{
                        			if( !$eng )
                        			{
                        				return 0;
                        			}
                        		}
                        		else if( ( $a[$i] >= '�' && $a[$i] <= '�' ) || ( $a[$i] >= '�' && $a[$i] <= '�' ) )
                        		{
                        			if( $eng )
                        			{
                        				return 0;
                        			}
                        		}
                        		else if( $a[$i] != '-' && $a[$i] != '_' && ( $a[$i] < '0' || $a[$i] > '9' ) )
                        		{
                        			return 0;
                        		}
                        	}
                        	
                        	return 1;
                        }
                        if( !correct_login( $newname ) ) 
                        {
                        	echo "<small><font color=darkred>��� �� ������������� �����������</font></small><br>";
               				$player->AddUMoney( 50 );
                        }
                        else
						{
           			
               				f_MQuery( "LOCK TABLE characters WRITE" );
               				if( f_MValue( "SELECT count( player_id ) FROM characters WHERE login='$newname'" ) > 0 )
               				{
               					echo "<small><font color=darkred>����� ��� ��� ������</font></small><br>";
	               				f_MQuery( "UNLOCK TABLES" );
	               				$player->AddUMoney( 50 );
               				}
               				else
               				{
               					f_MQuery( "UPDATE characters SET login='$newname' WHERE player_id={$player->player_id}" );
               					echo "<script>parent.char_ref.location.href='char_ref.php?rnd=".mt_rand()."';</script>";
	               				f_MQuery( "UNLOCK TABLES" );
			       				$player->AddToLogPost( -1, - 50, 21, 1000, 3 );
               				}
           				}
           			}

        		}
        		echo "<form action=game.php method=GET><input style='text-align:center' class=m_btn name=changename value='����� ���' type=text><br>";
        		echo "<table cellspacing=0 cellpadding=0 border=0><tr><td><img src=images/top/b.png></td><td><input type=submit class=n_btn value=��������></td><td><img src=images/top/c.png></td></tr></table>";
        		echo "</form>";
        	ScrollTableEnd( );
		echo "</td>";
	echo "</tr></table>";

	echo "</td></tr><tr><td>";

	echo "<table width=100% cellspacing=0 cellpadding=0 border=0><tr><td>";
	
        	ScrollTableStart( "left" );
        	
   	    	echo "<b>������ �������</b><br><br>";
   	    	echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'><a href='game.php?nick_clr=2'>����������� ����������� ������ ����</a>";
				if( $player->Rank( ) == 1 )
				{   	    	
   	    	echo "<li STYLE='list-style-image: URL(\"images/dots/dot-generic.gif\")'><a href='game.php?p=selfban&selfban=begin'>�������� ������ ���������</a>";
   	    	}   	    	
        	
        	ScrollTableEnd( );
	

	echo "</td></tr></table>";
	
	echo "</td></tr></table>";
	ScrollLightTableEnd( );

echo "</td>";
echo "</tr></table></center>";

?>
