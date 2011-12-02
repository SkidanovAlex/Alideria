<?

/*

regimes
0 - nothing
1 - sending info

*/

function uploadfile($origin, $dest, $tmp_name)
{
  $origin = mt_rand( 100000000, 999999999 ).".gif";
  $fulldest = $dest.$origin;
  $filename = $origin;
  for ($i=1; file_exists($fulldest); $i++)
  {
   $fileext = (strpos($origin,'.')===false?'':'.'.substr(strrchr($origin, "."), 1));
   $filename = substr($origin, 0, strlen($origin)-strlen($fileext)).'['.$i.']'.$fileext;
   $fulldest = $dest.$filename;
  }
  
  if (move_uploaded_file($tmp_name, $fulldest))
   return $filename;
  return "Moo!:$fulldest:";
}





if( !$mid_php ) die( );

if( $player->level < 3 ) 
{
	echo "<center><i>�� ��� ������� ����. �������������, ����� ���������� 3-��� ������.</i></center>";
	return;
}

$mode = 0;

if( !isset( $_GET['mode'] ) && $player->clan_id /* && isset( $_GET['order'] ) */ )
{	
	$mode = 2;
	if( !isset( $_GET['order'] ) ) $_GET['order'] = 'main';
}

else if( isset( $_GET['unapply'] ) )
{
	f_MQuery( "DELETE FROM clan_bets WHERE player_id={$player->player_id}" );
}

else if( isset( $_GET['apply_for'] ) && $player->clan_id == 0 )
{
	$clan_id = $_GET['apply_for'];
	settype( $clan_id, 'integer' );

	$ccres = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE invites_whom = {$player->player_id} AND status = 1" );
	$ccarr = f_MFetch( $ccres );
	$ddres = f_MQuery( "SELECT count( player_id ) FROM clan_creation WHERE player_id = {$player->player_id}" );
	$ddarr = f_MFetch( $ccres );
	$oores = f_MQuery( "SELECT count( player_id ) FROM clan_bets WHERE player_id = {$player->player_id}" );
	$ooarr = f_MFetch( $oores );

	$res = f_MQuery( "SELECT count( clan_id ) FROM clans WHERE clan_id=$clan_id" );
	$arr = f_MFetch( $res );

	if( $arr[0] == 0 ) RaiseError( "������� ������ ������ � �������������� �����", "$clan_id" );

	if( $ccarr[0] > 0 )
	{
		$player->syst( "� ��� ��� ������ ������ � ������ �����, ��� ������� ������� ��������� ��" );
	}
	else if( $ccarr[0] > 0 )
	{
		$player->syst( "�� ����������� ���������� ������������ ������, ������� ������� �������� ��������" );
	}
	else if( $ooarr[0] > 0 )
	{
		$player->syst( "� ��� ������ ������ � ���� �� �������, ������� ������� �������� ��" );
	}
	else
	{
		f_MQuery( "INSERT INTO clan_bets( clan_id, player_id ) VALUES ( $clan_id, {$player->player_id} )" );
	}
}

else if( isset( $_GET['register_order'] ) && $player->level >= 5 )
{
	$ccres = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE invites_whom = {$player->player_id} AND status != 2" );
	$ccarr = f_MFetch( $ccres );

	$oores = f_MQuery( "SELECT count( player_id ) FROM clan_bets WHERE player_id = {$player->player_id}" );
	$ooarr = f_MFetch( $oores );


	if( $player->clan_id != 0 )
	{
		$player->syst( "�� ��� �������� � ������. �� �� ������ �������� ����� �����" );
	}
	else if( $ooarr[0] > 0 )
	{
		$player->syst( "� ��� ������ ������ � ���� �� �������, ������� ������� �������� ��" );
	}
	else if( $ccarr[0] )
	{
		$player->syst( "�� ���������� � ������ �����. ������� ������� ���������� �� �����������" );
	}
	else
	{
    	if( isset( $_GET['cancel_registration'] ) )
    	{
    		$res = f_MQuery( "SELECT icon FROM clan_creation WHERE player_id={$player->player_id}" );
    		$arr = f_MFetch( $res );
    		if( $arr ) unlink( "images/clans/$arr[0]" );
    		f_MQuery( "DELETE FROM clan_creation WHERE player_id={$player->player_id}" );
    		f_MQuery( "DELETE FROM clan_creation_players WHERE player_id={$player->player_id}" );
    		$mode = 0;
    	}
    	else $mode = 1;
	}
}
else if( ( isset( $_GET['find_order'] ) || isset( $_GET['enter'] ) ) && $player->clan_id == 0 || $_GET['mode'] == 3 )
{
	$mode = 3;
}

// render page
if( $mode == 0 )
{
	if( isset( $_GET['accept'] ) )
	{
		$id = $_GET['accept'];
		settype( $id, 'integer' );
		$res = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE player_id=$id AND invites_whom={$player->player_id} AND status != 2" );
		$arr = f_MFetch( $res );
		$ccres = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE invites_whom = {$player->player_id} AND status = 1" );
		$ccarr = f_MFetch( $ccres );
		$ddres = f_MQuery( "SELECT count( player_id ) FROM clan_creation WHERE player_id = {$player->player_id}" );
		$ddarr = f_MFetch( $ccres );
		$oores = f_MQuery( "SELECT count( player_id ) FROM clan_bets WHERE player_id = {$player->player_id}" );
		$ooarr = f_MFetch( $oores );

		if( $ccarr[0] > 0 )
		{
			$player->syst( "� ��� ��� ������ ������ � ������ �����, ��� ������� ������� ��������� ��" );
		}
		else if( $ccarr[0] > 0 )
		{
			$player->syst( "�� ����������� ��������� ������������ ������, ������� ������� �������� ��������" );
		}
		else if( $ooarr[0] > 0 )
		{
			$player->syst( "� ��� ������ ������ � ���� �� �������, ������� ������� �������� ��" );
		}

		else if( $player->clan_id )
		{
			$player->syst( "�� ��� �������� � ������ ������. �� �� ������ ��������� ������." );
		}
		else if( $arr[0] > 0 )
		{
			f_MQuery( "UPDATE clan_creation_players SET status=1 WHERE player_id=$id AND invites_whom={$player->player_id}" );
			$plr = new Player( $id );
			$plr->syst3( "�������� <b>{$player->login}</b> ������ ������ � ��� �����" );
		}
	}
	if( isset( $_GET['refuse'] ) )
	{
		$id = $_GET['refuse'];
		settype( $id, 'integer' );
		$res = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE player_id=$id AND invites_whom={$player->player_id} AND status != 2" );
		$arr = f_MFetch( $res );
		if( $arr[0] > 0 )
		{
			f_MQuery( "UPDATE clan_creation_players SET status=2 WHERE player_id=$id AND invites_whom={$player->player_id}" );
			$plr = new Player( $id );
			$plr->syst3( "�������� <b>{$player->login}</b> �������� ������ � ��� �����" );
		}
	}

	echo "<ul>";
	if( $player->clan_id ) echo "<li> <a href=game.php?order=main>����� � ������� ������ ������</a>";
	else
	{
    	$chres = f_MQuery( "SELECT name FROM clan_creation WHERE player_id={$player->player_id}" );
    	$charr = f_MFetch( $chres );

    	if( $charr ) echo "<li><a href=game.php?register_order=1>$charr[0] - ���������� ���������</a>";
    	else if( $player->level >= 5 ) echo "<li><a href=game.php?register_order=1>�������� ����� �����</a>";
    	echo "<li><a href=game.php?find_order=1>����� �����, ���������� ���</a>";
   		echo "<li><a href=orders.php target=_blank>������� �������</a>";
    }
//	echo "<li><a href=orders_rating.php target=_blank>���������� ������� �������</a>";
	echo "</ul>";
	$res = f_MQuery( "SELECT clans.name, clans.clan_id FROM clans, clan_bets WHERE clans.clan_id=clan_bets.clan_id AND player_id={$player->player_id}" );
	while( $arr = f_MFetch( $res ) )
	{
		echo "� ��� ������ ������ � <b>$arr[name]</b>.<br><a href=game.php?unapply=$arr[clan_id]>�������� ������</a><br><br>";
	}

	$res = f_MQuery( "SELECT clan_creation_players.player_id, clan_creation_players.status,clan_creation.name FROM clan_creation_players, clan_creation WHERE invites_whom = {$player->player_id} AND clan_creation.player_id=clan_creation_players.player_id AND status != 2" );
	while( $arr = f_MFetch( $res ) )
	{
		if( $arr['status'] == 0 ) echo "�� ���������� � <b>$arr[name]</b>.<br>";
		else if( $arr['status'] == 1 ) echo "�� ������� ����������� � <b>$arr[name]</b>.<br>����� ����� �������, ����� ��� ������ ������ �����������.<br>";
		else if( $arr['status'] == 2 ) echo "�� ��������� ����������� � <b>$arr[name]</b>. ";
		if( $arr['status'] != 2 ) echo "<a href=# onclick='if( confirm( \"�� �������, ��� ������ ��������� ����������� � $arr[name]? ������������ �� �� ������� ������� ��� �����������, ���� ����� ������ �� ������ ��� ������.\" ) ) location.href=\"game.php?refuse=$arr[player_id]\";'>��������� �����������</a>";
		if( $arr['status'] == 0 ) echo "&nbsp;&nbsp;&nbsp;&nbsp;";
		if( $arr['status'] != 1 ) echo "<a href=# onclick='if( confirm( \"�� �������, ��� ������ ������� ����������� � $arr[name]?\" ) ) location.href=\"game.php?accept=$arr[player_id]\";'>������� �����������</a>";
		echo "<br>";
	}
}                                                                               

if( $mode == 1 )
{
	echo "<b>��������� ������ ������</b> - ";
	$res = f_MQuery( "SELECT * FROM clan_creation WHERE player_id={$player->player_id}" );
	$arr = f_MFetch( $res );
	if( !$arr )
	{
		$registration_begun = false;
		$st = '';

		if( isset( $_POST['nm'] ) )
		{
			$registration_begun = true;
			$name = $_POST['nm'].' ';
			$l = strlen( $name );
			if( $l > 56 )
			{
				$st .= "����� �������� ������ �� ����� ��������� 55 ��������<br>";
				$registration_begun = false;
			}
			if( strpos( $name, "����� " ) === false )
			{
				$st .= "������� ����� ����� � �������� �����������<br>";
				$registration_begun = false;
			}
			if( $name[0] < '�' || $name[0] > '�' )
			{
				$st .= "������ ������ �������� ����������� ������ ���� ��������� ������� ������<br>";
				$registration_begun = false;
	   		}
			else 
			{
				for( $i = 1; $i < $l; ++ $i )
				{
					if( !( $name[$i] >= '�' && $name[$i] <= '�' ) && !( $name[$i] >= '�' && $name[$i] <= '�' ) && $name[$i] != ' ' )
					{
						$st .= "�������� ����� ��������� ������ ������� ����� � �������<br>";
						$registration_begun = false;
						break;
					}
					if( ( $name[$i] >= '�' && $name[$i] <= '�' ) && ( $name[$i - 1] != ' ' ) )
					{
						$st .= "�������� �� ����� ��������� ��������� �����, ���� ��� �� �������� ������� �����<br>";
						$registration_begun = false;
						break;
					}
					if( ( $name[$i] >= '�' && $name[$i] <= '�' ) && $name[$i - 1] == ' ' )
					{
						$st .= "������ ����� �������� ������ ���������� � ��������� �����<br>";
						$registration_begun = false;
						break;
					}
					if( $name[$i] == ' ' && $name[$i - 1] == ' ' )
					{
						if( $i != $l - 1 ) $st .= "�������� �� ����� ��������� ���� �������� ������<br>";
						else  $st .= "�������� �� ����� ������������ ��������<br>";
						$registration_begun = false;
						break;
					}
				}
			}

        	list( $width, $height, $type, $attr ) = getimagesize( $_FILES['icon']['tmp_name'] );

        	if( !isset( $_FILES['icon'] ) )	
        	{
        		$st .= "������ ������ ����������� ������ ����. �� �� ������� ������� ������.<br>";
        		$registration_begun = false;
        	}
        	else if( !$width )
        	{
        		$st .= "������ ������ ������ ���� � ������� GIF. ��������� ���� ���� �� ��������� �������� ��� ��������.<br>";
        		$registration_begun = false;
            }
        	else if( $width != 18 || $height != 13 )
        	{
        		$st .= "������ ������ ������ ���� 18�13 (������ ��������� ������ {$width}x{$height})<br>";
        		$registration_begun = false;
            }
        	else if( $_FILES['icon']['size'] > 2048 )
        	{
        		$val = $_FILES['icon']['size'];
        		$st .= "������ ����� � ������� �� ����� ��������� 2048 ���� (������ ���������� ����� $val<br>";
        		$registration_begun = false;
        	}
        	else if( $type != 1 )
        	{
				$st .= "������ ������ ������ ���� � ������� GIF<br>";
        		$registration_begun = false;
        	}

    		$ccres = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE invites_whom = {$player->player_id} AND status = 1" );
    		$ccarr = f_MFetch( $ccres );
    		$ddres = f_MQuery( "SELECT count( player_id ) FROM clan_creation WHERE player_id = {$player->player_id}" );
    		$ddarr = f_MFetch( $ccres );

    		if( $ccarr[0] > 0 )
    		{
    			$st .= "� ��� ��� ������ ������ � ������ �����, ��� ������� ������� ��������� ��";
        		$registration_begun = false;
    		}
    		else if( $ccarr[0] > 0 )
    		{
    			$st .= "�� ����������� ��������� ������������ ������, ������� ������� �������� ��������";
        		$registration_begun = false;
    		}
    		else if( $player->clan_id )
    		{
    			$st .= "�� ��� �������� � ������ ������. �� �� ������ ��������� ������.";
        		$registration_begun = false;
    		}



        	if( $registration_begun && !$player->SpendMoney( 5000 ) )
        	{
        		$st .= "� ��� ������������ �������� ��� ������ �������";
        		$registration_begun = false;
        	}

			if( $registration_begun )
			{
				$name = trim( $name );
				$img = uploadfile($_FILES['icon']['name'],'images/clans/',$_FILES['icon']['tmp_name']);
				f_MQuery( "INSERT INTO clan_creation ( player_id, step, name, icon ) VALUES ( {$player->player_id}, 1, '$name', '$img' )" );
				$arr = Array( );
				$arr['player_id'] = $player->player_id;
				$arr['step'] = 1;
				$arr['icon'] = $img;
				$arr['name'] = $name;
			}
			else
			{
				$st = "<font color=darkred>$st</font>";
			}
		}

		if( !$registration_begun )
		{
    		echo "��� 1 �� 3 - ��������, ������ � �������<br><ul><li><a href=game.php>��������� � ��� ��������</a><li><a href=# onclick='if( confirm( \"�� ������������� ������ �������� ������� ��������� ������ ������?\" ) ) location.href=\"game.php?register_order=1&cancel_registration=1\";'>���������� �� ��������� ������</a></ul>";
    		echo "$st";
    		echo '<form enctype="multipart/form-data" action=game.php?register_order=1 method=post>';
    		echo "<table cellspacing=0 cellpadding=0 border=0>";
    		echo "<tr><td>�������� ������:&nbsp;</td><td><input type=text name=nm class=m_btn></td></tr>";
    		echo "<tr><td>&nbsp;</td><td><small>������� ����� ����� � �������� �����������<br>������ ����� �������� ������ ���������� � ������� �����<br>� �������� ����� �������������� ������ ������� ����� � �������<br>������������ ����� �������� ������ - 55 ��������</small></td></tr>";
    		echo "<tr><td>������ ������:&nbsp;</td><td><input type=file class=m_btn name=icon value=''></td></tr>";
    		echo "<tr><td>&nbsp;</td><td><small>������: GIF<br>�������: 18x13 ��������<br>������ ����� �� ������ 2048 ����</small></td></tr>";
    		echo "<tr><td>�������: </td><td><img width=11 height=11 src=images/money.gif> 5000</td></tr>";
    		echo "<tr><td>&nbsp;</td><td><input type=submit class=ss_btn value='������'></td></tr>";
    		echo "</table>";
    		echo "</form>";
		}
	}
	if( $arr && $arr['step'] == 1 )
	{
		include_once( "textedit.php" );
		$manual_provided = false;
		$st = '';
		if( isset( $_POST['manual'] ) )
		{
			$manual_provided = true;
			$orientation = $_POST['orientation'];
			$element = $_POST['element'];
			settype( $orientation, 'integer' );
			settype( $element, 'integer' );
			if( $orientation < 0 || $orientation > 2 || $element < 0 || $element > 3 )
			{
				RaiseError( "���������� ������� ������� �������� ���������� ��� ������", "���������� $orientation, ������: $element" );
			}
			$manual = trim( HtmlSpecialChars( $_POST['manual'] ) );
			$manual = str_replace( "\n", "<br>", $manual );
			$manual = process_str( $manual );

			if( $manual_provided )
			{
				f_MQuery( "UPDATE clan_creation SET step=2, element=$element, orientation=$orientation, manual='$manual' WHERE player_id={$player->player_id}" );
				$arr['element'] = $element;
				$arr['orientation'] = $orientation;
				$arr['manual'] = $manual;
				$arr['step'] = 2;
			}
		}
		if( !$manual_provided )
		{
			include_js( "js/textedit.js" );
    		echo "��� 2 �� 3 - ��������, ������, ����������<br><ul><li><a href=game.php>��������� � ��� ��������</a><li><a href=# onclick='if( confirm( \"�� ������������� ������ �������� ������� ��������� ������ ������?\" ) ) location.href=\"game.php?register_order=1&cancel_registration=1\";'>���������� �� ��������� ������</a></ul>";
    		echo "$st";
    		echo '<form name=frm action=game.php?register_order=1 method=post>';
    		echo "<table cellspacing=0 cellpadding=0 border=0>";
    		echo "<tr><td vAlign=top>";
    		echo "<b>����������:</b>&nbsp;".create_select_global( "orientation", $orientations, 0 );
    		echo "</td><td align=right><b>������:</b>&nbsp;".create_select_global( "element", $elements, 3 );
    		echo "</td></tr><tr><td colspan=2 vAlign=top><br><b>��������</b> (���� ����� ����� ������� �� ������� �������� ������ ������):<br>"; insert_text_edit( 'frm', 'manual' ); echo "</td></tr>";
    		echo "<tr><td colspan=2><input type=submit class=ss_btn value='������'></td></tr>";
    		echo "</table>";
    		echo "</form>";
		}
	}
	if( $arr && $arr['step'] == 2 )
	{
		$clan_name = $arr['name'];
		$finish = false;
		$st = '';

		if( isset( $_GET['finish'] ) )
		{
			f_MQuery( "LOCK TABLE clan_creation WRITE, clan_creation_players WRITE, clans WRITE" );

			$res = f_MQuery( "SELECT count( invites_whom ) FROM clan_creation_players WHERE player_id={$player->player_id}" );
			$arr = f_MFetch( $res );
			$total = $arr[0];
			$res = f_MQuery( "SELECT count( invites_whom ) FROM clan_creation_players WHERE player_id={$player->player_id} AND status=1" );
			$arr = f_MFetch( $res );
			$accepted = $arr[0];

			$res = f_MQuery( "SELECT * FROM clan_creation WHERE player_id={$player->player_id}" );
			$arr = f_Mfetch( $res );

			$alres = f_MQuery( "SELECT * FROM clans WHERE name='$arr[name]'" );
			$alres2 = f_MQuery( "SELECT * FROM clan_creation WHERE name='$arr[name]' AND player_id != {$player->player_id}" );

			if( f_MNum( $alres ) ) { $player->syst( "������, ����� � ����� ��������� ��� ������ �������� �� ���. �������� �������� ������� �������� � ������" ); f_MQuery( "UNLOCK TABLES" ); }
			else if( f_MNum( $alres2 ) ) { $player->syst( "������, ����� � ����� ��������� ��� ������������. �������� �������� ������� �������� � ������" ); f_MQuery( "UNLOCK TABLES" ); }
			else if( $arr && $arr['step'] == 2 && $total >= 4 && $accepted == $total )
			{
				f_MQuery( "UPDATE clan_creation SET step=4 WHERE player_id={$player->player_id}" );
				f_MQuery( "UNLOCK TABLES" );

				f_MQuery( "INSERT INTO clans ( name, icon, orientation, element ) VALUES ( '$arr[name]', '$arr[icon]', $arr[orientation], $arr[element] )" );
				$clan_id = mysql_insert_id( );
				f_MQuery( "insert into forum_rooms( id ) values ( -{$clan_id} );" );
				f_MQuery( "INSERT INTO clan_pages( clan_id, title, text, is_title ) VALUES ( $clan_id, '�������', '$arr[manual]', 1 )" );
				f_MQuery( "INSERT INTO clan_ranks( clan_id, rank, name, permitions ) VALUES ( $clan_id, 0, '���������', 0 )" );
				f_MQuery( "INSERT INTO clan_ranks( clan_id, rank, name, permitions ) VALUES ( $clan_id, 1000, '�������', 2147483647 )" );
				f_MQuery( "INSERT INTO clan_jobs( clan_id, job, name, permitions ) VALUES ( $clan_id, 1000, '�����', 2147483647 )" );

				$pres = f_MQuery( "SELECT * FROM clan_creation_players WHERE player_id={$player->player_id}" );
				while( $parr = f_MFetch( $pres ) )
				{
					f_MQuery( "INSERT INTO player_clans ( player_id, clan_id ) VALUES ( $parr[invites_whom], $clan_id )" );
					f_MQuery( "UPDATE characters SET clan_id=$clan_id WHERE player_id=$parr[invites_whom]" );
					$plr = new Player( $parr['invites_whom'] );
					$plr->syst3( "� �������� ������� <b>$arr[name]</b>. �����������! ��� ����, ����� �������� �������� ��� ������, ����� ������������ ��������� � ����" );
					$plr->UploadInfoToJavaServer( );
				}
				$player->syst3( "� �������� ������� <b>$arr[name]</b>. �����������! ��� ����, ����� �������� �������� ��� ������, ����� ������������ ��������� � ����" );
				f_MQuery( "INSERT INTO player_clans ( player_id, clan_id, rank, job, control_points ) VALUES ( {$player->player_id}, $clan_id, 1000, 1000, -1 )" );
				f_MQuery( "UPDATE characters SET clan_id=$clan_id WHERE player_id={$player->player_id}" );
				f_MQuery( "DELETE FROM clan_creation WHERE player_id={$player->player_id}" );
				f_MQuery( "DELETE FROM clan_creation_players WHERE player_id={$player->player_id}" );
				$player->clan_id = $clan_id;
				$player->UploadInfoToJavaServer( );
				$finish = true;
				die( "<script>location.href='clans_js.php';</script>" );
			}
			else f_MQuery( "UNLOCK TABLES" );

		}

		if( !$finish )
		{
			if( isset( $_GET['del'] ) )
			{
				$id = $_GET['del'];
				settype( $id, 'integer' );
				f_MQuery( "DELETE FROM clan_creation_players WHERE player_id={$player->player_id} AND invites_whom=$id" );
			}
			if( isset( $_POST['nm'] ) )
			{
				$name = htmlspecialchars( $_POST['nm'], ENT_QUOTES );
				$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$name' AND clan_id=0" );
				$arr = f_MFetch( $res );
				if( !$arr ) $st = "������ � ������ $name �� ���������� ���� �� ������� � ������ ������";
				else
				{
					$plr = new Player( $arr[0] );
					f_MQuery( "LOCK TABLE clan_creation_players WRITE, clan_creation WRITE" );
					$qres = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE player_id={$player->player_id}" );
					$qarr = f_MFetch( $qres );

					$res = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE player_id={$player->player_id} AND invites_whom={$plr->player_id}" );
					$arr = f_MFetch( $res );

					$ccres = f_MQuery( "SELECT count( player_id ) FROM clan_creation_players WHERE invites_whom = {$plr->player_id} AND status != 2" );
                	$ccarr = f_MFetch( $ccres );
					$bbres = f_MQuery( "SELECT count( player_id ) FROM clan_creation WHERE player_id = {$plr->player_id}" );
                	$bbarr = f_MFetch( $bbres );

                	if( $plr->clan_id != 0 )
                	{
						$st = "������ ���������� ������, ������� ������� � ������ ������";
						f_MQuery( "UNLOCK TABLES" );
                	}
					else if( $plr->player_id == $player->player_id )
					{
						$st = "������ ���������� � ����� ����";
						f_MQuery( "UNLOCK TABLES" );
					}
					if( $arr[0] > 0 )
					{
						$st = "��������� �������� ��� ��������� ���� � �����";
						f_MQuery( "UNLOCK TABLES" );
					}
                	else if( $ccarr[0] )
                	{
						$st = "��������� ���� ����� ����� �� ����������� ������ � ���� �� ������ �������";
						f_MQuery( "UNLOCK TABLES" );
                	}
                	else if( $bbarr[0] )
                	{
						$st = "��������� ���� ����� � ��������� ������ �������� �������� ����������� �����";
						f_MQuery( "UNLOCK TABLES" );
                	}
					else if( $qarr[0] >= 4 )
					{
						$st = "� ������ �� ����������� ������ �� ����� ���� ������ ���� �������";
						f_MQuery( "UNLOCK TABLES" );
					}
					else
					{
						f_MQuery( "INSERT INTO clan_creation_players ( player_id, invites_whom ) VALUES ( {$player->player_id}, {$plr->player_id} )" );
						f_MQuery( "UNLOCK TABLES" );
						$plr->syst3( "�������� <b>{$player->login}</b> ���������� ��� � <b>{$clan_name}</b>. ����������� ��� ���������� ����� � ���� �������� � ��������� ������ �������." );
    				}
				}
			}

    		echo "��� 3 �� 3 - ����������� �������<br><ul><li><a href=game.php>��������� � ��� ��������</a><li><a href=# onclick='if( confirm( \"�� ������������� ������ �������� ������� ��������� ������ ������?\" ) ) location.href=\"game.php?register_order=1&cancel_registration=1\";'>���������� �� ��������� ������</a></ul>";
    		echo "<i>��� ��������� ������ ���������� ����� ���� ������� �� ���� �������� ������.<br>����� ��������� ������ �� �� ������� ������� ����� �������, ���� �� ��������� �������. ���������� � ������ ������ ���� ������� ����������� ������������.</i>";
			$plrs = Array( );
			$plrs[0] = Array( $player, 1 );
			$res = f_MQuery( "SELECT * FROM clan_creation_players WHERE player_id={$player->player_id}" );
			while( $arr = f_MFetch( $res ) )
			{
				$plr = new Player( $arr['invites_whom'] );
				$plrs[] = Array( $plr, $arr['status'] );
			}

			echo "<table>";
			$waiting = false;
			foreach( $plrs as $id=>$arr )
			{
				echo "<tr><td><b>".($id + 1).".</b></td><td><script>document.write( ".$arr[0]->Nick( )." );</script><td>";
				if( $arr[1] == 0 ) echo "<font color=navy>���� �������������</font>";
				if( $arr[1] == 1 ) echo "<font color=green>����������</font>";
				if( $arr[1] == 2 ) echo "<font color=red>�������</font>";
				echo "</td><td>";
				if( $id == 0 ) echo "&nbsp;";
				else echo "<a href=game.php?register_order=1&del=".$arr[0]->player_id.">�������</a>";
				echo "</td></tr>";
				if( $arr[1] != 1 ) $waiting = true;
			}
			echo "</table><br><br>";

			if( count( $plrs ) < 5 )
			{
    			echo "<b>���������� ������:</b><br>";
    			if( $st != '' ) echo "<font color=darkred>$st</font><br>";
    			echo "<form action=game.php?register_order=1 method=POST><table><tr><td><input type=text name=nm class=m_btn></td><td><input type=submit class=s_btn value='����������'></td></tr></table></form>";
			}
			else
			{
				if( $waiting ) echo "<i>������� ������������� �� ���� �������</i>";
				else echo "<button class=s_btn onclick='location.href=\"game.php?register_order=1&finish=1\"'>�������� �����</button>";
			}
		}
	}
}

if( $mode == 2 )
{
	include( "clan_room.php" );
}

else if( $mode == 3 )
{
	$clan_id = $_GET['enter'];                                  
	settype( $clan_id, 'integer' );
	$res = f_MQuery( "SELECT * FROM clans WHERE clan_id=$clan_id" );
	$arr = f_MFetch( $res );
	if( $arr )
	{
		echo "<b>$arr[name]</b> - <a href=game.php?find_order=1>�����</a><br><br>";
		echo "��������������: <b>".$orientations[$arr['orientation']]."</b><br>";
		echo "������: <b>".$elements[$arr['element']]."</b><br>";
		echo "��������: <a href=orderpage.php?id=$clan_id target=_blank>�������</a><br>";
		echo "<ul><li><a href=game.php?apply_for=$clan_id>������ ������ �� ����������</a></ul>";
	}
	else
	{
		echo "<b>������ ��������</b> - <a href=game.php>�����</a><br>";
    	$res = f_MQuery( "SELECT * FROM clans ORDER BY glory DESC, clan_id" );
    	while( $arr = f_MFetch( $res ) )
    	{
        	echo "<ul>";
        	echo "<li><a href=game.php?enter=$arr[clan_id]>$arr[name]</a>";
        	echo "</ul>";
    	}
	}
}

?>
