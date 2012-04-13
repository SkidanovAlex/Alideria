<?

$last_instant_error = "";
function useInstant( $item_id )
{
	global $player;
	global $last_instant_error;
	global $last_add_item;
	$last_add_item = "";
	
	// ������ ��������
	$warp_ids = array( 46229, 46230, 46231, 46232, 46233, 46234, 46235, 46228 );
	for( $i = 3; $i <= 10; ++ $i )
	{
		if( $item_id == $warp_ids[$i - 3] )
		{
			if( $player->regime != 0 || $player->till != 0 )
			{
				$last_instant_error = "�� �� �������� ��� ������������� ������ ��������.";
				return false;
			}
			if( $player->location != 0 || $player->depth > 20 )
			{
				$last_instant_error = "������ �������� ����� ������������ ������ � �������!";
				return false;
			}
			if( !$player->HasWearedItem( 8 ) )
			{
				$last_instant_error = "����������� �������� � ���� �����. ��� �����, ���� ��� �������� ���� ������...";
				return false;
			}
			$player->SetDepth( $i );
			$last_instant_error = "�� ������� ����������� �� $i-� ������� �����.";
			$player->AddToLogPost( $item_id, -1, 1005);
			return true;
		}
	}
	
	// �������: ��������
	if( $item_id == 69307 )
	{
		// ���� ����� �� �������� (��� ������������� � �����?)
		if( $player->regime != 0 || $player->till != 0 )
		{
			$last_instant_error = '����� ������������� ���������� ��������� ������� ����';
			return false;
		}
		else
		{
			if ($player->location >= 10 && $player->location <= 20) // ����� � �����
			{
				$last_instant_error = '�� �� ������ ����������������� �� ����� �����';
				return false;
			}
			if ($player->location == 4) // ����� �� ��������
			{
				if (f_MValue("SELECT stage FROM player_caveexp WHERE player_id=".$player->player_id) > 0)
				{
					$last_instant_error = '�� �� ������ �����������������, ���� �� ��������� �������';
					return false;
				}
			}
			// ���� ��������, ������������ �� ������� �����
			$player->SetLocation( 2, true );
			$player->SetDepth( 0, true );
			
			$last_instant_error = '�� ��� �� � ���������';
			$player->AddToLogPost( $item_id, -1, 1005);
			return true;
		}
	}
	
	// �������: ���������
	if( $item_id == 71032 )
	{
 // ���������
/*
$last_instant_error = '�������� ��� ����� �������� � ������ ��������� ���������.';
return false;
*/
		// ���� ����� �� �������� (��� ������������� � �����?)
		if( $player->regime != 0 || $player->till != 0 )
		{
			$last_instant_error = '����� ���������� ���������� ��������� ������� ����';
			return false;
		}
		else
		{
			// ���� ��������, ���������, �������� �� ��� �������� ������
			
			if( $_GET['attackPlayer'] )
			{
				$attackPlayer = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_GET['attackPlayer'] ) );
				if( $attackPlayerId = f_MValue( "SELECT player_id FROM `characters` WHERE `login` = '$attackPlayer'" ) )
				{
					require_once( 'player.php' );
					
					$AttackPlayer = new Player( $attackPlayerId );
					
					if( $AttackPlayer->player_id == $player->player_id )
					{
						$last_instant_error = "����������� ������� ���� ���������� ������� ��������� ��������. ���-��!";
						return false;									
					}
					elseif( !f_MValue( 'SELECT * FROM `online` WHERE `player_id` = '.$AttackPlayer->player_id ) )
					{
						$last_instant_error = "�������� ������ ��� ����. ��������� ���������, ���� �� �������� � ����������� ������� �����.";
						return false;		
					}
					elseif( $AttackPlayer->Rank( ) == 1 )
					{
						$AttackPlayer->syst2( '�������� <b>'.$player->login.'</b> ������� ��������� ���� <b>������� ���������</b>. ��-��! ��! ��! ���������! BWHAHAHAHHAAAAA! :D' );
					
						$last_instant_error = "������, ������ �������� ��������� ��������! ���� ���� ����� ����� ������� � ���� ���� �� ����� ����� �����������!";
						return false;
					}
					elseif( f_MNum( f_MQuery( "SELECT * FROM tournament_busy_players WHERE player_id=".$AttackPlayer->player_id ) ) )
					{
						$last_instant_error = "���� ����� ��������� � �������, ��� ������ ���������.";
						return false;
					}
					elseif( $AttackPlayer->regime == 250 )
					{
						$last_instant_error = "���� ����� ����������������� � ������, ��� ������ ���������.";
						return false;			
					}
					// ���� �� ������� ������
					elseif( $AttackPlayer->DropItems( 71042 ) )
					{
						$last_instant_error = "� ��������� �������� ������� ������. �� ���� ��� ��� ������� ��������.";
						$AttackPlayer->syst2( '�������� <b>'.$player->login.'</b> ��������� ��������� ��� ��� ������ <b>�������� ���������</b>, �� ��� <b>������� ������</b> ������� ������� �����!' );
						$player->AddToLogPost( $item_id, -1, 1005);
						return true;
					}

		    		require_once( "create_combat.php" );
		    		// ������� ���������
		    		$combat_last_error = '';
    				if( ccAttackPlayer( $player->player_id, $AttackPlayer->player_id, 0, true, false ) )
    				{
    					// ����������
						$AttackPlayer->syst2( '�� ���� ��������� ��� ������ <b>�������� ���������</b> ���������� <b>'.$player->login.'</b>.' );
						$AttackPlayer->syst2( '/combat' );
						$player->syst2( '�� ��������� ��������� <b>'.$AttackPlayer->login.'</b> ��� ������ <b>�������� ���������</b>.' );
						$player->syst2( '/combat' );
					
						$last_instant_error = "� ���!";
						$player->AddToLogPost( $item_id, -1, 1005);
						return true;
					}
					else
					{
						// �� ����������
						$last_instant_error = $combat_last_error;
						return false;		
					}
				}
				else
				{
					$last_instant_error = "��������� � ����� ������ �� ����������!";
					return false;
				}
			}
			else
			{
				?>
					document.location.replace( document.location.href + '&attackPlayer=' + encodeURIComponent( prompt( '������� ������� ���������, �������� ����������� ���������', '' ) ) );
				<?
				$last_instant_error = "������� ���������..";				
				return false;
			}
		}
	}
	
	// �������: ������������
	if( $item_id == 72801 )
	{
			include_once( "arrays.php" );
			include_once( "wear_items.php" );

			if ($player->location == 0)
			{
				$last_instant_error = "�� �� ������ ������������ ��� ���������� � ���� �������";
				return false;
			}

			foreach( $item_types_all as $a=>$b )
				if( $a > 0 && HasItemInSlot( $a ) )
					UnWearItem( $a );
			$attrs = Array( 30,40,50 );
			$sum = 0;
			foreach( $attrs as $a=>$b )
			{
				$val = $player->GetActualAttr( $b );
				$sum += $val;
				$player->AlterActualAttrib( $b, -$val );
			}
			$player->SetRealAttr( 1000, $player->level * 3 );

			$player->syst2( '/items' );
			$player->syst2( "������ ����� ������ ������������ ���� ���� � �����." );
			$last_instant_error = "������ ����� ������ ������������ ���� ���� � �����.";

			$player->SetTrigger( 41, 0 );
			if( $player->HasTrigger( 42 ) ) $player->SetTrigger( 44, 1 );
			$player->SetTrigger( 42, 0 );

			f_MQuery( "DELETE FROM player_cards WHERE card_id IN( 186, 185, 187 ) AND player_id={$player->player_id}" );
			f_MQuery( "DELETE FROM player_selected_cards WHERE card_id IN( 186, 185, 187 ) AND player_id={$player->player_id}" );
			
			checkZhorik( $player, 13, 1 ); // ����� ������ �������������
			$player->AddToLogPost( $item_id, -1, 1005);
			return true;
	}

	// �������: ����������������
	if( $item_id == 72802 )
	{
			include_once( "guild.php" );
			$res = f_MQuery( "SELECT * FROM player_guilds WHERE player_id={$player->player_id}" );
			$po = 0;
			while( $arr = f_MFetch( $res ) )
			{
				for( $i = 0; $i < $arr['rank']; $i++ ) $po += $rank_prices[$i];
				for( $i = 0; $i < $arr['rating']; $i++ ) $po += $rank_prices[$i];
			}
			$player->syst2( "�� ��������� $po ����������������� �����." );
			$last_instant_error = "�� ��������� $po ����������������� �����.";
			f_MQuery( "UPDATE characters SET prof_exp = prof_exp + $po WHERE player_id={$player->player_id}" );
			f_MQuery( "UPDATE player_guilds SET rank=0, rating=0 WHERE player_id={$player->player_id}" );
			
			$player->prof_exp += $po;
			
			UpdateTitle( false );
			$player->AddToLogPost( $item_id, -1, 1005);
			return true;
	}

	// �������: ����� �����
	if ($item_id == 74935)
	{
		if ($player->real_deaths <= 0)
		{
			$last_instant_error="������ � ������ ���������� �� ��� �� �������.";
			return false;
		}
		f_MQuery( "UPDATE characters SET real_deaths = real_deaths - 1 WHERE player_id = ".$player->player_id);
		$player->real_deaths = $player->real_deaths - 1;
		$td = 30*($player->real_deaths+1);
		$last_instant_error = "�� �������������� ������...";
		$player->syst2("��������� ������ ����, ������������ ��� ������� ������� - � �� �������� ����� ����� ��������� ������ ���. �������, <b>������ �� ������ ��������� � ������ ������� ������ �������.</b> ��� ��������� ��������� �� ��������� � ������ ".$td." ���.");
		$player->AddToLogPost( $item_id, -1, 1005);
		return true;
	}

	// ������� ������
	if ($item_id == 75474)
	{
		
		$num_inner_spell = f_MValue("SELECT count(i.item_id) FROM items as i INNER JOIN player_items as p ON p.item_id=i.item_id WHERE p.player_id={$player->player_id} AND i.inner_spell_id>0 AND i.charges<i.max_charges AND p.weared > 0");
		if ($num_inner_spell == 0)
		{
			$last_instant_error = "��� ��������� ��� �������";
			return false;
		}

		f_MQuery("UPDATE items as i INNER JOIN player_items as p ON p.item_id=i.item_id SET i.charges=i.max_charges WHERE p.player_id={$player->player_id} AND i.inner_spell_id>0 AND i.charges<i.max_charges AND p.weared > 0");
		$last_instant_error="�� ���������� ��� ������ � ���������� � ����� ��������� ����� ������ ����";
		$player->AddToLogPost( $item_id, -1, 1005);
		return true;
	}

	// �������
	if ($item_id > 0 && f_MValue("SELECT pandora_id FROM pandora WHERE item_id=-2 AND pandora_id=".$item_id)>0)
	{
		$last_instant_error = f_MValue('SELECT kind_text FROM items WHERE item_id='.$item_id);
		f_MQuery("LOCK TABLE pandora WRITE");
		$schall = f_MValue("SELECT SUM(schans) FROM pandora WHERE pandora_id=".$item_id);
		$sch1 = 0;
		$res = f_MQuery("SELECT * FROM pandora WHERE pandora_id=$item_id");
		f_MQuery("UNLOCK TABLES");
		$sch = mt_rand(1, $schall);
		$arr=0;
		while ($sch1 < $sch && $arr = f_MFetch($res))
		{
			$sch1 = $sch1 + $arr[3];
		}
		if ($sch1 < $sch) return false;
		if ($arr[1] == -4) // �������
		{
			$player->AddToLogPost( 0, $arr[2], 991, $item_id);
			$player->AddMoney($arr[2]);
			$last_instant_error .= " �� ��������� ".$arr[2].my_word_str($arr[2], ' ������', ' �������', ' ��������');
		}
		elseif ($arr[1] == -2)
		{
			$last_instant_error .= " ��� ������ ���.";
		}
		elseif ($arr[1] == -3) // ������
		{
			if (f_MValue("SELECT regime FROM characters WHERE player_id=".$player->player_id))
			{
				$last_instant_error .= "�� ��� ����� ������� �����-�� ������, �� �� ���� ������, � �� ���� ������...";
			}
			elseif ($arr[2] > 0)
			{
				$mob_name = f_MValue("SELECT name FROM mobs WHERE mob_id=".$arr[2]);
				if (!$mob_name)
					$last_instant_error .= "��������� ���-�� ����������...";
				else
				{
					include_once("mob.php");
					$mob = new Mob;
					$mob->CreateMob($arr[2], $player->location, $player->depth);
					$mob->AttackPlayer( $player->player_id, 0, 0, true /* �������� ������� */, true );
					setCombatTimeout($mob->combat_id, 60);
					$last_instant_error .= "�� ��� ������������� ".$mob_name;
					$player->syst2("<b>".$mob_name."</b> �������� �� ���");
					$player->syst2("/combat");
				}
			}
		}
		elseif ($arr[1] >0) // �������
		{
			$player->AddItems($arr[1], $arr[2]);
//			$last_add_item = "alter_item(".$arr[1].", 0, ".$arr[2]);";
			$player->AddToLogPost( $arr[1], $arr[2], 991, $item_id);
			$i_n = f_MValue('SELECT name FROM items WHERE item_id='.$arr[1]);
			$last_instant_error .= " �� ��������� ".$i_n." {$arr[2]} ".my_word_str( $arr[2], '�����', '�����', '����' );

if (false && $item_id==76555)
{
$goog_items = Array (70971, 70972, 70974, 74434, 74796, 74798, 74800, 74864, 74866, 74867, 74868, 74871, 74872, 74873, 74878, 74880, 74881, 75313, 75314, 75315, 75317, 75319, 75320, 75321, 75322, 75332, 75534, 75563);

if (in_array($arr[1], $good_items))
{
$plr = new Player( 69055 );
$plr->UploadInfoToJavaServer( );

$st = "<b>".$player->login."</b> ������ ��� ������� <b>".$i_n."</b> �� ������������� �������! <img src='images/smiles/congratulations.gif'>";

$sock = socket_create(AF_INET, SOCK_STREAM, 0);
socket_connect($sock, "127.0.0.1", 1100);
$tm = date( "H:i", time( ) );
$st = "say\n{$st}\n69055\n0\n0\n{$tm}\n";
socket_write( $sock, $st, strlen($st) ); 
socket_close( $sock );
}
}

		}

		$player->AddToLogPost( $item_id, -1, 1005);
		return true;
	}
	
	$last_instant_error = "�� ������ ����� �����, ���������� �� ���� ������, ������� ��������� � ��������, ��� ���-�� �� ���. ��������� ���� ������ ��� �� �������� � ���� ��������...";
	return false;
}

?>
