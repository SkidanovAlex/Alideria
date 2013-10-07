<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "forest_functions.php" );
include_once( "phrase.php" );
include_once( "prof_exp.php" );
include_once( "feathers.php" );

include_once( "charmed_meadow.php" );
include_once( "marriage_functions.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "�������� ��������� Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
$fpd = new ForestPlayerData( $player->player_id );

$forest_ref_php = true;

/*
if (f_MValue("SELECT COUNT(*) FROM player_triggers WHERE trigger_id=12345 AND player_id=".$player->player_id))
	die();
*/

$depth = $place = $player->depth;
$loc = $player->location;

if( $loc != 1 && $loc != 6 && $loc != 7 ) die( );

$ut = new ForestUtils( $loc );

$hares = false;

// ��������

$till = $player->till;
$tm = time( );

$x = $depth / 100;
$y = $depth % 100;
settype( $x, 'integer' );

$cur_tile = $ut->getTile( $x, $y );

//==================================================================================================
// = ����� �������
//==================================================================================================
if( $player->regime == 150 )
{
	if( $HTTP_RAW_POST_DATA[0] == '>' )
	{
		$answer = substr( iconv("UTF-8", "CP1251", $HTTP_RAW_POST_DATA ), 1 );
		$answer = str_replace( '�', '�', $answer );
		$answer = str_replace( '�', '�', $answer );
		$answer = str_replace( '�', '�', $answer );
		$fpr = new ForestPlayerRiddle( $player->player_id );
		if( mb_strtolower( $answer ) == mb_strtolower( $fpr->riddle_a ) )
		{
			$player->SetRegime( 0 );
		}
		else
		{
			if ($player->Rank() == 1) $tm_sec = 1;
			else $tm_sec = 40;
			$player->SetTill( $tm + $tm_sec );
			$till = $tm + $tm_sec;
			$fpd->SetStatus( 3 );
			$player->SetRegime( 0 );
		}
	}
}

//==================================================================================================
// = ����� ������ �������
//==================================================================================================
if( $player->regime == 120 )
{
	if( $HTTP_RAW_POST_DATA[0] == '>' )
	{
		$id = (int)( substr( $HTTP_RAW_POST_DATA, 1 ) );
		if( $id < 0 || $id > 9 ) RaiseError( '�������� ID ������� ��� ������ � ����', "$id" );
		f_MQuery( "LOCK TABLE player_revealed_feathers WRITE" );
    		$item_id = f_MValue( "SELECT item_id FROM player_revealed_feathers WHERE player_id={$player->player_id} ORDER BY entry_id LIMIT $id, 1" );
    		f_MQuery( "DELETE FROM player_revealed_feathers WHERE player_id={$player->player_id}" );
		f_MQuery( "UNLOCK TABLES" );
		if( $item_id )
		{
			$player->AddItems( $item_id );
			$player->AddToLogPost( $item_id, 1, 38 );
			// widow quest
		   	include_once( "quest_race.php" );
		   	updateQuestStatus ( $player->player_id, 2508 );
		}
		$player->SetRegime( 0 );
		MakeStep( );
	}
}

//==================================================================================================
// = ����� ������
//==================================================================================================
else if( $HTTP_RAW_POST_DATA[0] == '!' && $player->regime == 0 && $fpd->status == 0 && !$till )
{
	$target_id = substr( $HTTP_RAW_POST_DATA, 1 );
	settype( $target_id, 'integer' );


	$res = f_MQuery( "SELECT characters.player_id FROM characters, online WHERE characters.player_id = online.player_id AND characters.loc = $loc AND characters.depth = $depth AND characters.player_id = $target_id UNION
	                  SELECT characters.player_id FROM characters, combat_players WHERE characters.player_id = combat_players.player_id AND characters.loc = $loc AND characters.depth = $depth AND combat_players.ai = 1 AND combat_players.ready < 2 AND characters.player_id = $target_id" );
	if( !f_MNum( $res ) ) echo( "alert( '����� ��� ����� ������, �������� �� ����� ����' );" );
	else if( $target_id == $player->player_id ) RaiseError( "�������� ������� ������� �� ������ ���� � ����" );
	else
	{
		$target_fpd = new ForestPlayerData( $target_id );
		$target_fpd->SetStatus( 0 );
		$target = new Player( $target_id );
		$target->SetTill( 0 );
		$target->syst2( "/combat" );
		include( "create_combat.php" );
		$combat_id = ccAttackPlayer( $player->player_id, $target_id, 0, true );
		if( $combat_id )
		{
    		if( $target->regime == 100 ) f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( $combat_id, '<b>{$player->login}</b> ����������� � ���<br>' )" );
    		else f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( $combat_id, '<b>{$player->login}</b> �������� � ���� �� ��������� <b>{$target->login}</b><br>' )" );

    		die( 'location.href="combat.php";' );
		}
		else $player->syst( $combat_last_error, false );
	}

}

else
{
	list( $dx, $dy ) = @explode( "|", $HTTP_RAW_POST_DATA );
}

settype( $dx, 'integer');
settype( $dy, 'integer');



//==================================================================================================
// = ���������� ������� � ��������
//==================================================================================================
if( $till && $tm >= $till - 2 )
{
	f_MQuery( "UPDATE characters SET go_till = 0 WHERE player_id = {$player->player_id}" );
	$act = $fpd->status;
	$fpd->SetStatus( 0 );
	$till = 0;
	if( $act < 0 )
	{
		$act_id = - $act;	
		$res = f_MQuery( "SELECT * FROM forest_additional_actions WHERE entry_id=$act_id" );
		$arr = f_MFetch( $res );
		if( !$arr ) RaiseError( "����������� ���. �������� � ����", "$act_id" );
		if( !allow_phrase( $arr['condition_id'], false ) ) RaiseError( "��� ���������� �������� � ���� ����� ������� ����������� �� ��������� ��� ���������� �������", "$act_id, $arr[condition_id]" );

		$phrase_id = $arr['action_id'];
		$rnd = mt_rand( 1, 1000000 );
		$cur = 0;
		$res = f_MQuery( "SELECT * FROM forest_add_act_var WHERE entry_id=$act_id" );
		while( $arr = f_MFetch( $res ) )
		{
			if( $cur + $arr['chance1000000'] >= $rnd )
			{
				$phrase_id = $arr[phrase_id];
				break;
			}
			$cur += $arr['chance1000000'];
		}
		
		do_phrase( $phrase_id, false );
		$res = f_MQuery( "SELECT text FROM phrases WHERE phrase_id=$phrase_id" );
		$arr = f_MFetch( $res );
		if( !$arr ) RaiseError( "����������� ����� ��� ���������� ���. ����� � ����", "���. �������� $act_id, $phrase_id" );

		$player->syst( $arr[0], false );
		MakeStep( );
    }
	if( $act == 3 )
	{ // ����� �������
		include( "riddle_generator.php" );
		$rdg = new RiddleGenerator( );
		$rdg->Generate( );
		$fpr = new ForestPlayerRiddle( $player->player_id );
		$fpr->SetRiddle( $rdg->text, $rdg->number );
		$player->SetRegime( 150 );
	}
	else if( $act == 2 )
	{
		$player->syst( '�� ��������� �����. '.getItems( ), false );
		MakeStep( );
	}
	else if( $act == 4 )
	{
		if( $player->SetDepth( $fpd->goto ) )
		{
			$place = $fpd->goto;
			$depth = $fpd->goto;
			$x = $depth / 100;
			$y = $depth % 100;
			settype( $x, 'integer' );
			$cur_tile = $ut->getTile( $x, $y );
			
			if (monstersCampAttack())
			{
				die ("location.href='combat.php';");
			}
			
			MakeStep( );
		}
	}
	else if( $act == 5 )
	{
		f_MQuery( "LOCK TABLE forest_fallen_trees WRITE" );
		$tres = f_MQuery( "SELECT * FROM forest_fallen_trees WHERE location = $loc AND depth = $depth" );
		$tarr = f_MFetch( $tres );
		if ($cur_tile == 1) $tree_val = 10;
		elseif ($cur_tile == 13) $tree_val = 5;
		elseif ($cur_tile == 12) $tree_val = 3;
		if( !$tarr ) f_MQuery( "INSERT INTO forest_fallen_trees ( location, depth, value ) VALUES ( $loc, $depth, ".$tree_val." )" );
		else
		{
			f_MQuery( "UPDATE forest_fallen_trees SET value = value + ".$tree_val." WHERE location = $loc AND depth = $depth" );
		}
		f_MQuery( "UNLOCK TABLES" );
		$player->syst( '�� ������� ������� ������. ������ ������ ���������� ���������.', false );
		MakeStep( );
		AlterProfExp( $player, 1 );
	}
	else if( $act == 6 )
	{
		$player->syst( '�� ��������� <u>������</u>.', false );
		$player->AddToLog( 36, 1, 9, $act );
		$player->AddItems( 36, 1 );
		checkZhorik( $player, 18, 30 ); // ����� ������ �������� 30 ������
		MakeStep( );
	}
	else if( $act == 7 )
	{
		include_once( 'berrypickers_finish.php' );
		berrypickers_finish( );

		if( mt_rand( 1, 20 ) == 3 )
		{
			$coors = f_MFetch( f_MQuery( "SELECT depth FROM forest_tiles WHERE tile=0 ORDER BY rand() LIMIT 1" ) );
			if( !$coors ) RaiseError( "��� �����������, �� � ���� ��� ������!!!", "" );
			f_MQuery( "LOCK TABLE player_berry_places WRITE" );
			f_MQuery( "DELETE FROM player_berry_places WHERE player_id={$player->player_id}" );
			$expires = time( ) + 3600;
			f_MQuery( "INSERT INTO player_berry_places VALUES ( {$player->player_id}, $coors[0], $expires )" );
			f_MQuery( "UNLOCK TABLES" );

			$xxx = $coors[0] / 100;
			$yyy = $coors[0] % 100;
			settype( $xxx, 'integer' );
			$xxx = ($xxx + 50) % 100;

			$player->syst( "������, ��� ����� ���� �� ��� �� �������. �� �� ����� ������� �� ����� �������, �������� ������� ����� ���� ����� ����� �� $xxx:$yyy.", false );
		}
		else
		{
			$expires = time( ) + 3600;
			f_MQuery( "UPDATE player_berry_places SET expires=$expires WHERE player_id={$player->player_id}" );
		}
		if( mt_rand( 0, 99 ) < 50 ) MakeStep( );
	}
	else if( $act == 8 )
	{
		if( mt_rand( 1, 7 ) == 1 )
		{
			$player->AddToLog( 87, 1, 9, $act );
			$player->AddItems( 87 );
			$player->SetTrigger( 20, 1 );
			$player->syst( "����� ������ ������ �� ������� ������� <a href=help.php?id=1010&item_id=87 target=_blank><b>�����</b></a>", false );
			f_MQuery( "DELETE FROM player_hare_coords WHERE player_id={$player->player_id}" );
			AlterProfExp( $player, 2 );
			echo "update_exp( $player->exp, $player->prof_exp );";
		}
		else
		{
			$dir = mt_rand( 0, 7 );
			if( $dir >= 4 ) ++ $dir;
			$player->syst( "����� ������ ������ ���� ������ �� {$sides[$dir]}", false );
			move_hare( $dir );
		}
	}
	else if( $act == 9 )
	{
		processMeadow( );
	}
	else if ( $act == 10 ) // ����� �������
	{
		if (mt_rand(1, 20) == 1) // 5%
		{
			if ($cur_tile == 5) {$part_id = 74595; $part_name = "����� ���������";}
			if ($cur_tile == 8) {$part_id = 74594; $part_name = "���� ��-������";}
			if ($cur_tile == 9) {$part_id = 74596; $part_name = "����� �������";}
			$player->AddToLog( $part_id, 1, 9, $act );
			$player->AddItems( $part_id );
			$player->syst( '�� ��������� <u>'.$part_name.'</u>.', false );
		}
		else
			$player->syst( '� ���������, �� ������ �� �����.', false);
		MakeStep();
	}
}

else if( $till && $_GET['cancel'] && $fpd->status == 9 )
{
	$player->SetRegime( 0 );
	$player->SetTill( 0 );
	$fpd->SetStatus( 0 );
	$till = 0;
	$regime = 0;
}

else if( !$till && ( $dx != 0 || $dy != 0 ) && $player->regime == 0 )
{
	if( $cur_tile == 10 && $dx == 2 )
	{
		$fpr = new ForestPlayerRiddle( $player->player_id );
		$fpd->CleanUp( );
		$fpr->CleanUp( );
		$player->SetLocation( 2 );
		$player->SetDepth( 3 );
		die( "location.href='game.php';" );
	}
	
	else if ( $cur_tile == 110 && $dx == 2 )
	{
		$fpr = new ForestPlayerRiddle( $player->player_id );
		$fpd->CleanUp( );
		$fpr->CleanUp( );
		$player->SetLocation( 3 );
		$player->SetDepth( 6 );
		die( "location.href='game.php';" );
	}
	
	else if ( $cur_tile == 210 && $dx == 2 )
	{
		$fpr = new ForestPlayerRiddle( $player->player_id );
		$fpd->CleanUp( );
		$fpr->CleanUp( );
		$player->SetLocation( 0 );
		$player->SetDepth( 11 );
		die( "location.href='game.php';" );
	}
	
	if( $cur_tile == 300 && $dx == 2 )
	{
		$fpr = new ForestPlayerRiddle( $player->player_id );
		$fpd->CleanUp( );
		$fpr->CleanUp( );
		$player->SetLocation( 5 );
		$player->SetDepth( 0 );
		die( "location.href='game.php';" );
	}
	
	else if( $dx == 2 )
	{ // ��������
		if( $cur_tile == 0 )
		{
			$berry_duration = 20;
			if( $player->HasTrigger( 402 ) ) $berry_duration = 10;
			$player->SetTill( $tm + $berry_duration );
			$till = $tm + $berry_duration;
			$fpd->SetStatus( 2 );
		}
		if( $cur_tile == 1 || $cur_tile == 13 )
		{
			$player->SetTill( $tm + 120 );
			$till = $tm + 120;
			$fpd->SetStatus( 5 );
		}
		if( $cur_tile == 12 )
		{
			$player->SetTill( $tm + 60 );
			$till = $tm + 60;
			$fpd->SetStatus( 5 );
		}
		if( $cur_tile == 2 )
		{
			clickMeadow( );
		}
	}
	
	else if( $dx == 3 )
	{
		if( $cur_tile == 0 ) // ��������������
		{
        	$tres = f_MQuery( "SELECT cell_id FROM player_berry_places WHERE player_id={$player->player_id} AND cell_id=$depth AND expires > ".time( ) );
			$tarr = f_MFetch( $tres );
			if( $tarr )
			{
    			$player->SetTill( $tm + 90 );
    			$till = $tm + 90;
    			$fpd->SetStatus( 7 );
			}
		}
		if( $cur_tile == 5 || $cur_tile == 8 || $cur_tile == 9 ) // ����� ������
		{
			$player->SetTill( $tm + 30 );
			$till = $tm + 30;
			$fpd->SetStatus( 10 );
		}
		if( $cur_tile == 1 || $cur_tile == 13 || $cur_tile == 12 ) // ����� ������
		{
			f_MQuery( "LOCK TABLE forest_fallen_trees WRITE" );
			$tres = f_MQuery( "SELECT * FROM forest_fallen_trees WHERE location = $loc AND depth = $depth" );
			$tarr = f_MFetch( $tres );
			if( !$tarr )
			{
				echo "<script>alert( '� ���������, ����� ��� ��� ����������� ������' );</script>";
				f_MQuery( "UNLOCK TABLES" );
			}
			else
			{
				if( $tarr['value'] > 1 )
					f_MQuery( "UPDATE forest_fallen_trees SET value = value - 1 WHERE location = $loc AND depth = $depth" );
				else f_MQuery( "DELETE FROM forest_fallen_trees WHERE location = $loc AND depth = $depth" );
				f_MQuery( "UNLOCK TABLES" );
				
				$player->SetTill( $tm + 30 );
				$till = $tm + 30;
				$fpd->SetStatus( 6 );
			}
		}
	}

	else if( $dx == 4 )
	{
		$res = f_MQuery( "SELECT * FROM forest_additional_actions WHERE entry_id = $dy" );
		$arr = f_MFetch( $res );
		if( !$arr ) RaiseError( "����������� ���. �������� � ����", "$dy" );
		$player->SetTill( $tm + $arr['time'] );
		$till = $tm + $arr['time'];
		$fpd->SetStatus(  - $dy );
	}

	else if( $dx == 5 && hares( ) )
	{
		$player->SetTill( $tm + 30 );
		$till = $tm + 30;
		$fpd->SetStatus( 8 );
	}

	else if( $dx == 6 && isRazbojnik( ) )
	{
		$talk_id = 178;
		if( $player->HasTrigger( 73 ) ) $talk_id = 248;
		else if( $player->HasTrigger( 41 ) ) $talk_id = 183;
		else if( $player->HasTrigger( 44 ) ) $talk_id = 186;

		f_MQuery( "INSERT INTO player_talks ( player_id, talk_id, npc_id ) VALUES ( {$player->player_id}, $talk_id, 32 )" );
		$player->SetRegime( 110 );
		die( "location.href='game.php';" );
	}

	else if( $dx == 6 && isStarikKosh( ) )
	{
		$talk_id = 241;

		f_MQuery( "INSERT INTO player_talks ( player_id, talk_id, npc_id ) VALUES ( {$player->player_id}, $talk_id, 43 )" );
		$player->SetRegime( 110 );
		die( "location.href='game.php';" );
	}

	else if( $dx == 6 && isLeavesKeeper( ) )
	{
		$talk_id = 570;

		f_MQuery( "INSERT INTO player_talks ( player_id, talk_id, npc_id ) VALUES ( {$player->player_id}, $talk_id, 109 )" );
		$player->SetRegime( 110 );
		die( "location.href='game.php';" );
	}
	
	else if( $dx == 6 && isMahjong( ) )
	{
		$talk_id = 574;

		f_MQuery( "INSERT INTO player_talks ( player_id, talk_id, npc_id ) VALUES ( {$player->player_id}, $talk_id, 109 )" );
		$player->SetRegime( 110 );
		die( "location.href='game.php';" );
	}

	else
	{
		f_MQuery("LOCK TABLE player_triggers WRITE");
		if (f_MValue("SELECT COUNT(*) FROM player_triggers WHERE trigger_id=12345 AND player_id=".$player->player_id))
			die();
		else
			$player->SetTrigger(12345);
		f_MQuery("UNLOCK TABLES");

		$x += $dx; $y += $dy;
		$x = ( $x + 100 ) % 100;
		$y = ( $y + 100 ) % 100;
		$new_depth = $x * 100 + $y;
		$ok = true;
		if( abs( $dx ) > 1 || abs( $dy ) > 1 ) $ok = false;
		if( $ok )
		{
			$new_tile = $ut->getTile( $x, $y );
			if( $new_tile == 6 ) $ok = false;
			if( $cur_tile == 5 ) if( $new_tile == 5 ) $ok = false;
			if( $new_tile == 100 ) $ok = false;
			if( !($new_tile == 0 || $new_tile == 10 ) && $player->level < 2 )
			{
				echo "alert( '�� �� ������ �������� ������ ���� ������, ��� ���������� ������� ������' );";
				$ok = false;
			}
			/*
			if( $new_tile == 7 )
			{
				echo "alert( '������� ������ ��������� � ����������, ������ �� ��� ������' );";
				$ok = false;
			}
			*/
			if ($new_tile == 14)// && !($player->HasTrigger(12900) || $player->HasTrigger(12901)))
			{
				echo "alert( '�� ����������� �� ��������� ����� � �� ������ ������ ������' );";
				$ok = false;
			}
			if( $new_tile == 2 && $player->level < 4 )
			{
				echo "alert( '�� �� ������ ������ �� ������������ ������ ������, ��� ���������� ���������� ������' );";
				$ok = false;
			}
			/*if( $new_tile == 8 )
			{
				echo "alert( '������ ���������� ��������� � ����������, ������ �� ��� ������' );";
				$ok = false;
			}*/
			/*if( $new_tile == 9 )
			{
				echo "alert( '��������� ����� ��������� � ����������, ������ �� ��� ������' );";
				$ok = false;
			}*/
		}
		if( $ok )
		{
			if( $cur_tile == 1 )
			{
				$player->SetTill( $tm + 15 );
				$till = $tm + 15;
				$fpd->SetStatus( 4 );
				$fpd->SetGoto( $new_depth );
				$x -= $dx;
				$y -= $dy;
				$x = ( $x + 100 ) % 100;
				$y = ( $y + 100 ) % 100;
			}
			else if( $player->SetDepth( $new_depth ) )
			{
				$place = $new_depth;
				$depth = $new_depth;
				$cur_tile = $ut->getTile( $x, $y );
				MakeStep( );
			}
		}
		else
		{
			$x -= $dx;
			$y -= $dy;
			$x = ( $x + 100 ) % 100;
			$y = ( $y + 100 ) % 100;
		}
		f_MQuery("LOCK TABLE player_triggers WRITE");
		$player->SetTrigger(12345, 0);
		f_MQuery("UNLOCK TABLES");
	}
}


$razbojnik = isRazbojnik( );
$koshey = isStarikKosh( );
$leavesKeeper = isLeavesKeeper( );
$mahjong = isMahjong( );

if( $till )
{
	$delta = $till - $tm;
	$str = '';
	if( $fpd->status == 1 ) $str = '�� ������ �� ������ �������� �� ���� � ������� ���������.<br>��������� ��� <b>';
	else if( $fpd->status == 2 ) $str = "�� ������ �� ������ � ������� ������.<br>�� ������ ������ ��� <b>";
	else if( $fpd->status == 3 )
	{
		$fpr = new ForestPlayerRiddle( $player->player_id );
		$str = "�� �� �������, ���������� �����: {$fpr->riddle_a}.<br>��������� ������� ����� <b>";
	}
	else if( $fpd->status == 4 ) $str = "�� ������������ ����� �������� ���.<br>��� ������ ��� <b>";
	else if( $fpd->status == 5 ) $str = "�� ������ ������.<br>��� ��������� ��� <b>";
	else if( $fpd->status == 6 ) $str = "�� ������������ ���������� ������.<br>��� ��������� ��� <b>";
	else if( $fpd->status == 7 ) $str = "�� ����� �����.<br>��� ��������� ��� <b>";
	else if( $fpd->status == 8 ) $str = "�� ������ �����.<br>��� ��������� ��� <b>";
	else if( $fpd->status == 9 ) $str = "�� ����� ������� (<a href=\\'javascript:query(\"forest_ref.php?cancel=1\",\"\")\\'>��������</a>).<br>��� ��������� ��� <b>";
	else if ( $fpd->status == 10 ) $str = "�� ����� ��������.<br>��� ��������� ��� <b>";
	else if( $fpd->status < 0 )
	{
		$act_id = - $fpd->status;
		$res = f_MQuery( "SELECT condition_id FROM forest_additional_actions WHERE entry_id=$act_id" );
		$arr = f_MFetch( $res );
		if( !$arr ) RaiseError( "����������� ���. �������� � ����", "$act_id" );
		$res = f_MQuery( "SELECT text FROM phrases WHERE phrase_id = $arr[0]" );
		$arr = f_MFetch( $res );
		if( !$arr ) RaiseError( "����������� ����� ��� ���. �������� � ����", "entry_id=$act_id, cond" );
		$str = $arr[0]."<br><b>";
	}
	echo( "forest_timer( '$str', $delta );" );
}
else echo( "forest_timer( '', -1 );" );

// �������� - �����

//==================================================================================================
// = �����������
//==================================================================================================
$rx = ( $x + 50 ) % 100;
echo "forest_coord( $rx, $y );";

if( $rx == 30 && $y == 10 ) checkZhorik( $player, 19, 1 ); // ����� ������ 30 10
if( $rx == 37 && $y == 22 ) checkZhorik( $player, 20, 1 ); // ����� ������ 37 22

$tiles = Array( );
$tile_dirs = Array( );

$moo = 0;
for( $jj = $y - 1; $jj <= $y + 1; ++ $jj )
{
	$ny = ( $jj + 100 ) % 100;
	$tiles[$ny] = Array( );
	for( $ii = $x - 1; $ii <= $x + 1; ++ $ii )
	{
		$nx = ( $ii + 100 ) % 100;

		$tiles[$ny][$nx] = $ut->getTile( $nx, $ny );
		if( $moo != 4 )	
			$tile_dirs[$tiles[$ny][$nx]][] = $moo;

		++ $moo;
	}
}

$cur_tile = $tiles[$y][$x];

$st = '<u><b>'.$forest_names[$tiles[$y][$x]].'</b></u><br>';
$st .= $forest_comments[$tiles[$y][$x]]."<br><br>";

if( count( $tile_dirs[$tiles[$y][$x]] ) == 8 ) $st .= ( $forest_names[$tiles[$y][$x]]." �������� ��� �� ���� ������.<br>" );
else
{
	if( count( $tile_dirs[$tiles[$y][$x]] ) > 0 )
	{
		$st .= "<b>".$forest_names[$tiles[$y][$x]]."</b> ������ �� ";
		for( $i = 0; $i < count( $tile_dirs[$tiles[$y][$x]] ); ++ $i )
		{
			if( $i ) if( $i == count( $tile_dirs[$tiles[$y][$x]] ) - 1 ) $st .= " � "; else $st .= ", ";
			$st .= $sides[$tile_dirs[$tiles[$y][$x]][$i]];
		}
		$st .= "<br>";
	}
	
	foreach( $tile_dirs as $a=>$b )
	{
		if( $a != $tiles[$y][$x] )
		{
			$st .= '�� ';
			$comma = false;
			for( $i = 0; $i < count( $b ); ++ $i ) if( $b[$i] != 4 )
			{
				if( $i ) if( $i == count( $b ) - 1 ) $st .= " � "; else $st .= ", ";
				$st .= $sides2[$b[$i]];
			}
			$st .= " ��������� <b>" . $forest_names[$a] . "</b><br>";
		}
	}
	
	if( $tiles[$y][$x] == 0 && $tile_dirs[10] == 0 ) $st .= "<br><font color=red>��������!</font> �� ��������� ������ �� ��� ����� ������� ������ ������.<br>";
	if( $cur_tile == 5 ) $st .= "<br>��-�� ��������� ������ � ����, �� �� ������ ���� ����� ������.<br>";
}

$st .= "<br>";

	// ���� ���
	if( $dx != 0 || $dy != 0 )
	{
		$hres = f_MQuery( "SELECT location_items.*, items.name FROM location_items, items WHERE location = $loc AND depth = $depth AND items.item_id = location_items.item_id" );
		print( "reset_loc_items( );" );
		if( f_MNum( $hres ) )
		{
			while( $harr = f_MFetch( $hres ) ) print( "add_loc_item( $harr[item_id], '$harr[name]', $harr[number] );" );
		}
		print( "show_loc_items( );" );
	}
	
	// NPC ���
	$npc_show = "";
	if ($player->regime==0 && $fpd->status==0)
	{
		$nres = f_MQuery( "SELECT * FROM npcs WHERE location = {$player->location} AND depth = {$player->depth}" );
		if( mysql_num_rows( $nres ) )
		{
			while( $narr = f_MFetch( $nres ) )
				if( $narr[condition_id] == -1 || allow_phrase( $narr[condition_id] ) )
    					$npc_show .= "<li><a href='game.php?talk=$narr[npc_id]'>$narr[name]</a><br>";
				if( $npc_show != '' )
					$npc_show = "<b>����� ����� ���������� �:</b><br><ul>".$npc_show."</ul>";
		}
	}
	echo "forest_show_npc(\"".$npc_show."\");";
		
	// ������ ���
	echo "forest_clear_players( );";
	$ares = f_MQuery( "SELECT characters.login, characters.regime, characters.player_id, combat_id, 0 as mobik FROM characters INNER JOIN online ON characters.player_id = online.player_id LEFT JOIN combat_players ON characters.player_id = combat_players.player_id WHERE characters.loc = $loc AND characters.depth = $depth UNION
	                   SELECT characters.login, characters.regime, characters.player_id, combat_id, 1 as mobik FROM characters, combat_players WHERE characters.player_id = combat_players.player_id AND characters.loc = $loc AND characters.depth = $depth AND combat_players.ai = 1 AND combat_players.ready < 2" );
	$can_attack = ( $cur_tile == 0 || $cur_tile == 10 ) ? 0 : 1;
	if( $till || $player->regime != 0 ) $can_attack = 0;
	while( $aarr = f_MFetch( $ares ) )
	{
		$plr = new Player( $aarr[2] );
		$in_combat = ( $aarr[1] == 100 ) ? 1 : 0;
		if( !$can_attack ) $in_combat = 0;
		$moo = $can_attack;
		if( $aarr[2] == $player->player_id ) $moo = 0;
		if( !$plr->nick_clr ) $plr->nick_clr = 'FFFFFF';
		$aarr['combat_id'] = (int)$aarr['combat_id'];
		echo "forest_add_player( ".$plr->Nick2().", $moo, $in_combat, $aarr[2], $aarr[combat_id] );";
	}
	echo( "forest_show_players( );" );

	// --
	$st_act = '';
	if( $till || $player->regime != 0 ) echo( "forest_dirs( 0, 0, 0, 0, 0, 0, 0, 0 );" );
	else
	{
//		if( $depth == 0 )
		if( $cur_tile == 10 )
			$st_act .= '<a href="javascript:void(0)" onclick="forest_go( 2, 0 );" style="cursor:pointer"><li>���� �� �������� � �������</li></a>';
		if( $cur_tile == 300 && $player->HasTrigger( 180572 ) )
			$st_act .= '<a href="javascript:void(0)" onclick="forest_go( 2, 0 );" style="cursor:pointer"><li>���� �� �������� � �������</li></a>';
		if( $cur_tile == 110 )
			$st_act .= '<a href="javascript:void(0)" onclick="forest_go( 2, 0 );" style="cursor:pointer"><li>����� � ����</li></a>';
		if( $cur_tile == 210 )
			$st_act .= '<a href="javascript:void(0)" onclick="forest_go( 2, 0 );" style="cursor:pointer"><li>����� � ������ �������</li></a>';
		if( $cur_tile == 0 )
			$st_act .= '<a href="javascript:void(0)" onclick="forest_go( 2, 0 );" style="cursor:pointer"><li>������ �����</li></a>';
		if( $cur_tile == 1 || $cur_tile == 13 || $cur_tile == 12 )
			$st_act .= '<a href="javascript:void(0)" onclick="forest_go( 2, 0 );" style="cursor:pointer"><li>������ ������</li></a>';
		if( $cur_tile == 5 || $cur_tile == 8 || $cur_tile == 9 )
			$st_act .= '<a href="javascript:void(0)" onclick="forest_go( 3, 0 );" style="cursor:pointer"><li>������ �������</li></a>';

        // ���. ��������
		$res = f_MQuery( "SELECT * FROM forest_additional_actions WHERE cell_type = $cur_tile OR loc=$player->location AND depth=$player->depth" );
		while( $arr = f_MFetch( $res ) )
		{
			if( allow_phrase( $arr['condition_id'] ) )
			{
				if( $arr['flavor_text'] ) $st .= "<br>$arr[flavor_text]<br>";
				$st_act .= "<a href=\"javascript:void(0)\" onclick=\"forest_go( 4, $arr[entry_id] );\" style=\"cursor:pointer\"><li>$arr[text]</li></a>";
			}
		}

        // ���������� �������
		$tres = f_MQuery( "SELECT * FROM forest_fallen_trees WHERE location = $loc AND depth = $depth" );
		if( f_MNum( $tres ) ) 
		{
			$st_act .= '<a onclick="forest_go( 3, 0 );" style="cursor:pointer"><li>������ ���������� ������</li></a>';
			$st .= '<br>���������� �� ��� ����� ���������� ������.<br>';
		}

		// ����� - ����������
		if( $loc == 1 )
		{
        	$tres = f_MQuery( "SELECT cell_id FROM player_berry_places WHERE player_id={$player->player_id} AND cell_id=$depth AND expires > ".time( ) );
			$tarr = f_MFetch( $tres );
			if( $tarr )
			{
				$st .= "<br>������, ������ ����� ������� �������� ������� ������ �����<br>";
				$st_act .= '<a onclick="forest_go( 3, 0 );" style="cursor:pointer"><li>������ �����</li></a>';
			}
        }

        if( hares( ) )
        {
        	$st .= "<br>�� ������ ������������� �� ������� �����<br>";
        	$st_act .= '<a onclick="forest_go( 5, 0 );" style="cursor:pointer"><li>������ �����</li></a>';
        }

        if( $razbojnik ) 
        {
			$st .= "<br>���������� � ������� �� ������ �������� ������<br>";
        	$st_act .= '<a onclick="forest_go( 6, 0 );" style="cursor:pointer"><li>���� � ����</li></a>';
        }

        if( $koshey ) 
        {
			$st .= "<br>������ ��� ���� ���<br>";
        	$st_act .= '<a onclick="forest_go( 6, 0 );" style="cursor:pointer"><li>� �������</li></a>';
        }
        
        if( $leavesKeeper ) 
        {
			$st .= "<br>� �������� �� ����� �� ������ ��������� ������<br>";
        	$st_act .= '<a onclick="forest_go( 6, 0 );" style="cursor:pointer"><li>���� � ������</li></a>';
        }
        
        if( $mahjong )
        {
			$st .= "<br>��������� ������� ��� ���� ��� �����.<br>";
        	$st_act .= '<a onclick="forest_go( 6, 0 );" style="cursor:pointer"><li>� ��������� �������</li></a>';
        }
        
        if( $cur_tile == 2 ) renderMeadow( );
        if( $player->depth == 600 ) $st_act .= marriageActions( );

		echo( "forest_dirs( " );
		$comma = false;
		for( $jj = $y - 1; $jj <= $y + 1; ++ $jj )
		{
			for( $ii = $x - 1; $ii <= $x + 1; ++ $ii )
			{
				if( $ii == $x && $jj == $y )
				{
					;
				}
				else
				{
					$nx = ( $ii + 100 ) % 100;
					$ny = ( $jj + 100 ) % 100;
					$nv = $nx * 100 + $ny;

					$dx = $ii - $x;
					$dy = $jj - $y;

					if( $comma ) echo( ', ' );
					if( $tiles[$ny][$nx] != 200 && $tiles[$ny][$nx] != 100 && $tiles[$ny][$nx] != 6 && ( $tiles[$ny][$nx] != 5 || $cur_tile != 5 ) ) echo "1";
					else echo "0";
					
					$comma = true;
				}
			}
		}
		echo " );";
	}


if( $player->regime == 150 )	
{
	$fpr = new ForestPlayerRiddle( $player->player_id );
	if ($player->location == 1)
		$hran_tekst = "<b>��������� ����</b><br>� ��� ��������� ���������� ��������� ����.";
	if ($player->location == 6)
		$hran_tekst = "<b>��������� ����</b><br>� ��� ��������� ���������� ��������� ����.";
	if ($player->location == 7)
		$hran_tekst = "<b>��������� �����</b><br>� ��� ��������� ���������� ��������� �����.";
	echo "forest_text( '".$hran_tekst." ��� �� ������� �������� ��, ���� �� ��������� �� �������� �� �� �������:<br><i>" . $fpr->riddle . "</i><br><br><table cellspacing=0 cellpadding=0 border=0><tr><td>��� �����: </td><td><input type=text class=m_btn id=answr></td><td><button onclick=\"forest_answer()\" class=ss_btn>��������</button></td></tr></table>' );";
}
else if( $player->regime == 120 )
{
	$res = f_MQuery( "SELECT feather_id FROM player_revealed_feathers WHERE player_id={$player->player_id} ORDER BY entry_id" );
	$st = '<b>������ �� ������ ������ �������, �� ����� ������ ������ ���� �� ���...</b><br><br>';
	$i = 0;
	while( $arr = f_MFetch( $res ) )
	{
		$st .= "<img width=50 height=50 src='images/items/{$fthrs[$arr[feather_id]][1]}' title='".strip_tags($fthrs[$arr[feather_id]][2])."' style='cursor:pointer' onclick='forest_feather({$i});'>";
		++ $i;
	}
	echo "forest_text( '".addslashes($st)."' );";
}
else if( $fpd->status == 3 )	
{
	$fpr = new ForestPlayerRiddle( $player->player_id );
	echo "forest_text( '<b>��������� ����</b><br>� ��� ��������� ���������� ��������� ����. ��� �� ������� �������� ��, ���� �� ��������� �� �������� �� �� �������:<br><i>" . $fpr->riddle . "</i>' );";
}
else
{
	//if( $player->depth == 600 ) $st = "<b><u>����� ���� - ������</u></b><br>�����, �� ������ ����, �������� �������� ������ ��� ��������������.<br><br>".marriageMiddle( );
	echo "forest_text( '$st' );\n";
}
echo "forest_actions( '$st_act' );\n";

echo 'document.getElementById( "loc_img" ).src = "'.GetTileImage( $cur_tile, $player->depth ).'";';
if(GetTileImageLarge( $cur_tile, $player->depth ) == "")
{
	echo 'document.getElementById( "loc_img" ).style.cursor = "default";';
	echo 'document.getElementById( "loc_img" ).onclick = function(e){};';
}
else
{
	echo 'document.getElementById( "loc_img" ).style.cursor = "pointer";';
	echo "document.getElementById( 'loc_img' ).onclick = function(e){window.open('".GetTileImageLarge( $cur_tile, $player->depth )."', '_blank', 'width=700,height=528,toolbar=no,status=no,scrollbars=no,menubar=no,resizable=no');};";
}

?>
