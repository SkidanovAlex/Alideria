<?

include_once( 'guild.php' );
include_once( 'prof_exp.php' );
include_once( 'items.php' );
include_once( 'phrase.php' );

$guild_id = BERRYPICKERS_GUILD;
$hodka_time = 150;

function get_finish_text( $item_id, $num, $str, $str1, $str2 )
{
	global $guild_id;
	// ������ ��������� ����, ��� �� ������)) ���������, ������)))
	if( $item_id == 102 )
	{
		//return "�� ������������ ����. ����� ������� ������, ����� ������ ��� ���� �������� .. �� �� ���� ������ � ������, ��� ������ ������ ��� !! ������ ��� ������ �� ������ ����� : � ����, ��� �� ����� ��������� !! � ���� ������, ����� �� ������ �� ����� ���� ���������. ��� ��� �����, ������ � ��� ���� $str1";
		return "������� �� ��������� � ������ �������� � �� ��� ��� � ������ ����������� �� ��������. ��� � ����� ���������?! ���!!! �� �����: $str1";	}
	
    // �������� ����
//	if( $item_id == 101 )
//	{
//		return "�� ������������ ����. ����� ������� ������, ����� ������ ��� ���� ��������... ������� ��� ����� ������������ ��� ������ ������ ? �� ������ ������ ���� ��� � ����� ������� ��� � �������� ���� �������� .. ��� ����� ������� ���� ���������� ������, �������� ����� ��� ����, ����� ������� ����������� ���-��. ����� ������������������ ����� ������ ������, ��� ��� �� ���������� �� �������� �� ���� � � ������ ������� �����. �� �� ������ ���������� ��� ���� �� ����� ��������. �� ����� ���� - ��� �� $str1. ����� ����� !! � ��� ��� � ��� ������ ... ";
//	}
	
    // �������
	if( $item_id == 100 )
	{
		return "������ ��������� ��� ����� �������� �����, �� ������ ���� ������� �������, ���� ������ ����� ���-�� ���������, ������� � ����������������, ��� ����� ��� ������� ��������� ��. �� ������ � ���� �� ����� ����� ����� ������� � ���, ����� ���� ����. ����������� �� �������� ��������. ��� ���� ����� ��������� ��������, �� ��� �� �������� ��� � ������� $str";
	}
	
	if( mt_rand( 1, 80 ) == 1 )
	{
		global $player;
		$player->AddToLog( 87, 1, 1, $guild_id, 2 );
		$player->AddItems( 87, 1 );
		return "�� ������������ ����. ������ �� ������, �� � ��� �������. �� �������� $str. �� ����� �������, ����� � ���� ������� ������ �� �������� <a href=help.php?id=1010&item_id=87 target=_blank><b>������ ���������� �����</b></a>. �� ������ ������ ����� ������������, �� �� ��� ���������� ���������� ���������� ������� ����������� ������  ����. ��������� ��� �� �� ���� �� ����...";
	}
	if( mt_rand( 1, 79 ) == 1 )
	{
		global $player;
		$lnum = mt_rand( 2, 5 );
		if( $player->money >= $lnum )
		{
			$player->AddToLog( 0, - $lnum, 1, $guild_id, 2 );
			$player->SpendMoney( $lnum );
			return "�� ������������ ����. ������ �� ������, �� � ��� �������. �� �������� $str. ����������� ���������, �� �������������, ��� ���-�� ������� ��� �� ������. �� ��� �� ���? ����� ������� ��� ����? ��� ������ ����������� ������ �������� ���, ����� ������� ���������� ���������. �������� ���, �� � ���������� ��������, ��� �������� ���-�� � ����� <b>���� �����</b> ($lnum). ����� ������...";
		}
	}
	if( mt_rand( 1, 2500 ) == 1 )
	{
		global $player;
		$player->AddToLog( 104, 1, 1, $guild_id, 2 );
		$player->AddItems( 104, 1 );
		return "�� ������������ ����. ������ �� ������, �� � ��� �������. �� �������� $str. ����� ��������� ������� � ������, �� � ���������� ���������, ��� ��������� � ����� �� ���-�� ������� � ���������. �� ��� �� <a href=help.php?id=1010&item_id=104 target=_blank><b>��������� ������</b></a> - ��� ��� ����� ���������� � �������. �� ������ ����� ������� �������?! ";
	}

	return "�� ������������ ����. ������ �� ������, �� � ��� �������. �� �������� $str"; 
}

function get_nothing_text( )
{
	global $player;
	global $guild_id;
	
	if( mt_rand( 1, 20 ) == 1 )
	{
		global $player;
		$lnum = mt_rand( 1, 3 );
		$player->AddToLog( 99, $lnum, 1, $guild_id, 2 );
		$player->AddItems( 99, $lnum );
		return "�� ������������ ����. ����� ������� ������, ����� ������ ��� ���� ��������... � ������� �� ��� ��������� ���� �������� ������ � ����� ����� ������� ��������. ��� � �������� ��������� �� ������ ����, ������� ������� ����� ���� � �� ��� �������� ����� �������� ����� �� <a href=help.php?id=1010&item_id=99 target=_blank><b>������� ������</b></a> ($lnum).";
	}

	return "�� ������������ ����. ����� ������� ������, ����� ������ ��� ���� �������� .. ";
}


function berrypickers_finish( )
{
	global $player;
	global $guild_id;
	global $hodka_time;

	$guild = new Guild( $guild_id );
	if( !$guild->LoadPlayer( $player->player_id ) ) return;

	$res = f_MQuery( "SELECT items.item_id, items.price FROM lake_items, items WHERE lake_items.item_id=items.item_id AND lake_items.guild_id = $guild_id AND lake_items.rank <= {$guild->rank}" );
	$st = "";
	include( "kopka.php" );
	$kopka = new Kopka( );
		
	while( $arr = f_MFetch( $res ) )
		$kopka->AddItem( $arr[0], $arr[1] );

	$prem = false;	
	$per_hour = 200 + $guild->rating * 50;

	// premium
	$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=1" ) );
	if( $barr[0] ) $prem = true;

	$kopka->GetItemId( $hodka_time, $per_hour, true );
	
	if( !$kopka->num ) $st .= get_nothing_text( );
	else
	{
		$player->AddToLog( $kopka->item_id, $kopka->num, 1, $guild_id, 0 );
		$player->AddItems( $kopka->item_id, $kopka->num );

		$f1 = getItemNameForm( $kopka->item_id, "" );
		$f2 = getItemNameForm( $kopka->item_id, "2" );
		$f4 = getItemNameForm( $kopka->item_id, "4" );
		$f13 = getItemNameForm( $kopka->item_id, "13" );
		$f2m = getItemNameForm( $kopka->item_id, "2_m" );

		$tstr = "<a target=_blank href=help.php?id=1010&item_id={$kopka->item_id}><b>".my_word_form2( $kopka->num, $f4, $f13, $f2m )."</b></a>";
		$tstr1 = "<a target=_blank href=help.php?id=1010&item_id={$kopka->item_id}><b>".my_word_form( $kopka->num, $f1, $f13, $f2m )."</b></a>";
		$tstr2 = "<a target=_blank href=help.php?id=1010&item_id={$kopka->item_id}><b>".my_word_form3( $kopka->num, $f2, $f2m )."</b></a>";
		$st .= get_finish_text( $kopka->item_id, $kopka->num, $tstr, $tstr1, $tstr2 );

		// widow quest
	   	include_once( "quest_race.php" );
	   	updateQuestStatus ( $player->player_id, 2502 );

        // generic quests that require mining
        $quest_res = f_MQuery("SELECT * FROM player_quest_mine WHERE player_id={$player->player_id} AND item_id={$kopka->item_id} AND togo > 0");
        while ($quest_arr = f_MFetch($quest_res))
        {
            $new_togo = $quest_arr['togo'] - $kopka->num;
            if ($new_togo < 0) $new_togo = 0;
            f_MQuery("UPDATE player_quest_mine SET togo = {$new_togo} WHERE player_id={$player->player_id} AND item_id={$kopka->item_id} AND togo > 0 AND quest_part_id = {$quest_arr[quest_part_id]}");
            $quest_name = f_MValue("SELECT quests.name FROM quests INNER JOIN quest_parts ON quests.quest_id=quest_parts.quest_id WHERE quest_parts.quest_part_id={$quest_arr[quest_part_id]}");
            $player->syst("�� ������������ � ���� � ���������� ������ &laquo;{$quest_name}&raquo;");
            if ($new_togo == 0)
            {
                if ($quest_arr['action_trigger_id'] != 0)
                {
                    $player->SetTrigger($quest_arr['action_trigger_id']);
                }
                if ($quest_arr['action_phrase_id'] != 0)
                {
                    do_phrase($quest_arr['action_phrase_id']);
                }
            }
        }
	}
	
	// ������� ����� ���!
	if( !$kopka->num )
		$st .= AlterProfExp( $player, 1 );
	else $st .= AlterProfExp( $player, ceil( $kopka->item_prices[$kopka->item_id] * $kopka->avgnum * 50 / $per_hour ) );
//	$st .= AlterProfExp( $player, 2 );
	UpdateTitle( false );

	$player->syst( $st, false );

	echo "update_exp( $player->exp, $player->prof_exp );";
}

?>
