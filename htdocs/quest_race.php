<?

include_once('functions.php');
include_once('player.php');
include_once('quest_race_update_status.php');

f_MConnect();

// ���� { ����, ����, ������� } ...
$questMobs = array 	( 	array ( "����", "�����", "�����" ),
						array ( "�������", "��������", "��������"),
						array ( "���������", "����������", "����������"),
						array ( "���������� �����", "���������� ����", "���������� ����"), 
						array ( "����������� �����", "���������� ������", "���������� ������"),
						array ( "�����", "������", "������"), 
						array ( "�����", "����", "����"),
						array ( "����", "�����", "�����"), 
						array ( "������� ����", "������� �����", "������� �����"), 
						array ( "������", "�����", "�����"),
						array ( "�������", "�������", "�������"), 
						array ( "������", "������", "������"),
						array ( "���������� ��������", "����������� ��������", "����������� ��������"),
						array ( "�������������� ������", "������������� ������", "������������� ������"),
						array ( "���������� �����", "����������� �����", "����������� �����"),
						array ( "�������", "��������", "��������"),
						array ( "���", "����", "����")
					);
					
// ������ { ���, ��� } ������ ...
$questFlowers = array ( "���������", "�������", "���������", "�����", "�������" );
					
// �������� ������ � ������� ����� � ���������� ������
function createQuest ( $questType, $questDetails, $questAmount, $questText )
{
	f_MQuery( "INSERT INTO quest_race (race_type, race_details, race_amount, race_text) values ($questType, $questDetails, $questAmount, '$questText')" );
}

// ��������� �������, ������� ���������� ������ � ������������ ���������� �� ������� �����
function generateQuest ( )
{
	// ���� ��������� �� ��� � ���������� ������ ���� ��������� �����
	$arr = array( );
	for( $i = 0; $i < 13; ++ $i )
		$arr[$i] = $i + 1;
	
	for( $i = 0; $i < 3; ++ $i )
	{
		$pos = mt_rand( $i, 12 );
		$t = $arr[$pos]; 
		$arr[$pos] = $arr[$i]; 
		$arr[$i] = $t;
	}
	
	// ������ $arr ������ ������ 3 �������� ���������� ������ �������
	
	// ��������� ����� ���������� ������� � ������: 2 ��� 3
	$questSize = mt_rand ( 2, 3 );
	
	// �������� ��������� ������ ���������� ���
	for( $i = 0; $i < $questSize; ++ $i )
	{
		// ������� �� ������ ������
		$questDetails = 0;
		$questAmount = 0;
		$questText = "";
		
		// ��� ������ �������
		$questType = $arr[$i] + 2500;
	
		if( $arr[$i] == 1 ) // ����� ��������
		{
			global $questMobs; // ������ �������� � ������ �����, �� ���� ������
			
			// "����� �������, ��� �� 1 �� 17, ���������� �� 2 �� 4"
			$questDetails = 100 + mt_rand( 1, 17 );
			$questAmount = mt_rand( 2, 4 );
			
			// ����������� �������������� ��� ��������� ������
			$amountText = "����";
			if ( $questAmount == 3 )
				$amountText = "����";
			if ( $questAmount == 4 )
				$amountText = "�������";
			
			// ���� ���� { 3, "����", "�����", "�����" }
			$questText = "���� <b>$amountText " . my_word_str( $questAmount, $questMobs[$questDetails-101][0], $questMobs[$questDetails-101][1], $questMobs[$questDetails-101][2] ) . "</b>.";
		}
		
		if( $arr[$i] == 6 ) // �������� �����
		{
			global $questFlowers;
			
			// "�������� ����� ������, ��� �� 1 �� 5, ���������� �� 2 �� 3
			$questDetails = 150 + mt_rand( 1, 5 );
			$questAmount = mt_rand( 2, 3 );
			
			$questText = "������ <b>$questAmount ����� " . my_word_str( $questAmount, "�����", "������", "�������" ) . /* " " . $questFlowers[$questDetails - 151] . */ "</b> ����� �������.";
		}
		
		// ������, �����, ����, ������� � ����� ������
		if( $arr[$i] == 2 || $arr[$i] == 3 || $arr[$i] == 5 || $arr[$i] == 7 || $arr[$i] == 8 )
		{
			$questAmount = mt_rand( 1, 2 );
			
			switch ( $arr[$i] )
			{
				case 2: // ������
					$questText = "������ ����� ������ � ����� ������� <b>$questAmount " . my_word_str( $questAmount, "���", "����", "���" ) . "</b>.";
					break;
				case 3: // �����
					$questText = "�������� ����� ������� <b>$questAmount " . my_word_str( $questAmount, "���", "����", "���" ) . "</b>.";
					break;
				case 5: // ����
					$questText = "����� ���-������ �� ������ ������� <b>$questAmount " . my_word_str( $questAmount, "���", "����", "���" ) . "</b>.";
					break;
				case 7: // �������
					$questText = "���� �� �������� <b>$questAmount " . my_word_str( $questAmount, "����� ���", "����� ����", "����� �����" ) . "</b>.";
					break;
				case 8: // ����� �������
					$questText = "����� �� ������������ ������ <b>$questAmount " . my_word_str( $questAmount, "�������", "�������", "�������" ) . "</b>.";
					break;	
			}
		}
		
		// ����������, ��������
		if( $arr[$i] == 4 || $arr[$i] == 13 ) 
		{
			$questAmount = mt_rand( 5, 9 );
			
			if( $arr[$i] == 4 ) // ����������
			{
				$questText = "������ ����������� <b>$questAmount " . my_word_str( $questAmount, "��������", "���������", "���������" ) . "</b>.";
			}
			else // ��� � ��������
			{
				$questText = "��������� � �������� ������ ������ ���� �� <b>$questAmount " . my_word_str( $questAmount, "�������", "�������", "������" ) . " ���.</b>";
			}
		}
		
		// ��������
		if( $arr[$i] == 10 )
		{
			$questAmount = mt_rand( 15, 30 ) * 100;
			$questText = "���������� <b>$questAmount ������ ��������</b> � ��������.";
		}
		
		// �����
		if( $arr[$i] == 11)
		{
			$questAmount = 1;
			$questText = "������ �������� ����� �� ������, <b>����� � �����</b>!";
		}
		
		// ������������ ������� � ��������
		if( $arr[$i] == 9 || $arr[$i] == 12 )
		{
			$questAmount = mt_rand( 2, 4 );
			if( $arr[$i] == 12 ) // �������� 
			{
				$questText = "������ ������ ������ ��������� �� <b>$questAmount " . my_word_str( $questAmount, "������", "������", "������" ) . "</b>.";
			}
			else // ������������ �������
			{
				$questText = "�������� � ����-������ <b>$questAmount " . my_word_str( $questAmount, "�������", "�������", "�������" ) . "</b>.";
			}
		}
		
		// �������� ������ � ������ � ���� ������
		createQuest( $questType, $questDetails, $questAmount, $questText );
	}
}

// ���������� ���������� � ������� �����
function displayQuest ()
{
	$questInfo = "";
	
	if( checkQuestActivity() )
	{
		// �������� �� ������� ��� ��������� �������� �������, ��� ��� ������� ������������� ��� �����������
		$res = f_MQuery( "SELECT race_text FROM quest_race" );
		if ( mysql_num_rows( $res ) > 0 )
		{
			$questInfo .= "� ���� ��� ���� �������!<br>";
			
			// ������ �� �������� ����������� � ������������ ��������� � ����� �������, ����������� ����� �� �������
			while ( $arr = f_MFetch( $res ) )
			{
				$questInfo .= $arr['race_text'] . " <br> ";
			}
			
			$questInfo .= "����������, � ������� ������ ���������� �� ��� ������!";
		}
	}
	else
	{
		$questInfo = "� ������ ������ ������� ������������ ���.";
	}
	
	// ������������ �������� ������ ���� ���������� ������ � ��������� ��� ��� � �������
	// ��� ����� ������� ��������� ����� � �����, ������� ����� ����������� �� ����������
	return $questInfo;
}

// �������� ��������� "������������" �������
// �������� ��� ���������� � ������ �����, ��� ����������� ���������� ������� � ������ ��������� � ���� ����

// - ��������: �� ����� ����������� ������ ���� ������� ������ ����� updateQuestStatus �� ������� ������������ ���������� � �������� �����
// �� ���� ������ � ������ ����� �� ����� ����� ������� ���������, � ����� �������� �������
// ����� ����� ������ ������ �������� �������, ������� � ����� ������ ���������� � ������ ����������� ������

// - ����� ���� ������� � �������� �� �������� �����������
// ���������� true ��� �������� ����� � false ��� �������, ������ ��������� ���
function checkQuestActivity ( ) 
{
	$res = f_MQuery( "SELECT * FROM quest_race" );
	if ( mysql_num_rows( $res ) > 0 )
		return true;
	else
		return false;
}

// ������ ���������� � ������ � ������� ������ ����
// ���������� ����� ���������� ������ ����� ����������
function dropQuest ( ) 
{
	f_MQuery( "TRUNCATE TABLE quest_race" );
	
	// ��� ����� �������� ��� Quest Value � ������ �������, ����� ������ �� ��������
	f_MQuery( "DELETE FROM player_quest_values WHERE value_id > 2500 AND value_id < 2514" );
	f_MQuery( "DELETE FROM player_triggers WHERE trigger_id=260" );
}


// ������� ��� ������ ��������������� �� ������ � ��������� �������
// ���� ��� ���� ��� ���������� �� � ���-���� ������, ������� ���������� ����� ��������
// ���������� ���������, ������������ �� ���������� ��������� � ������ ������� �����
function getRemainingActions ( $player_id )
{
	// ����� �����������, ���� ��� ��������� ������
	// ��� ������ ������� ��������� ��� ����������� ������� �� ������ � ���������� ��������� �������
	if ( !checkQuestActivity( ) )
		return false;
	
	$tasks = array( );
	$i = 0;

	// ������� ���������� ��������� ������ �������� ������
	$res = f_MQuery( "SELECT race_type, race_details, race_amount FROM quest_race" );
	while ( $arr = f_MFetch( $res ) )
	{
		$tasks[$i] = array (
			"race_type" => $arr['race_type'],
			"race_details" => $arr['race_details'],
			"race_amount" => $arr['race_amount']
		);
		$i ++;
	}

	$plr = new Player( $player_id );
	$value = "";

	// ������ ���������, ������� ���� ����� ��� ����� ���������
	for ( $i = 0; $i < count( $tasks ); ++ $i )
	{
		// ��� �������, ����� ��������� ������ ������������, ������� � ���������� ��������������� �������
		$remainingAmount = $tasks[$i]['race_amount'] - $plr->GetQuestValue( $tasks[$i]['race_type']);
		
		if ( $remainingAmount > 0 )
		{
			switch ( $tasks[$i]['race_type'] )
			{
				case 2501:
					global $questMobs;
					$value .= "����� $remainingAmount " . my_word_str( $remainingAmount, $questMobs[$tasks[$i]['race_details']-101][0],  $questMobs[$tasks[$i]['race_details']-101][1],  $questMobs[$tasks[$i]['race_details']-101][2] ) . ". ";
					break;
				case 2502:
					$value .= "������ � ����� ������� $remainingAmount " . my_word_str( $remainingAmount, "�������", "��������", "���������" ) . ". ";
					break;
				case 2503:
					$value .= "���������� � ����� ������� $remainingAmount " . my_word_str( $remainingAmount, "�������", "��������", "���������" ) . ". ";
					break;
				case 2504:
					$value .= "������� ����������� $remainingAmount " . my_word_str( $remainingAmount, "�������", "��������", "���������" ) . ". ";
					break;
				case 2505:
					$value .= "������ $remainingAmount " . my_word_str( $remainingAmount, "�������", "��������", "���������" ) . " �� ������ �������. ";
					break;
				case 2506:
					global $questFlowers;
					$value .= "�������� $remainingAmount " . my_word_str( $remainingAmount, "�����", "������", "�������" ) /*. " " . $questFlowers[$tasks[$i]['race_details']-151]*/ . ". ";
					break;
				case 2507:
					$value .= "������ $remainingAmount " . my_word_str( $remainingAmount, "���", "����", "�����" ) . " �� ��������. ";
					break;
				case 2508:
					$value .= "����� $remainingAmount " . my_word_str( $remainingAmount, "�������", "�������", "�������" ) . " �� ������������ ������. ";
					break;
				case 2509:
					$value .= "���������� $remainingAmount " . my_word_str( $remainingAmount, "�������", "�������", "�������" ) . " � ������ ������. ";
					break;
				case 2510:
					$value .= "������������ $remainingAmount " . my_word_str( $remainingAmount, "�������", "�������", "������" ) . " �������� � ��������. ";
					break;
				case 2511:
					$value .= "������� � �����. ";
					break;
				case 2512:
					$value .= "�������� ������ ��������� �� $remainingAmount " . my_word_str( $remainingAmount, "������", "������", "������" ) . ". ";;
					break;
				case 2513:
					$value .= "����������� $remainingAmount " . my_word_str( $remainingAmount, "�������", "�������", "������" ) . " ��� � �������� ������. ";
					break;
			}
		}
	}
	
	if( $value == "" ) $value = "��������� � �����!";

	$value = "��� ���������� ����� ��������: " . $value;
	$value .= "�������!";
	return $value;
}

// ��������� ������ ������
// ������ �������, ���������� ���������� ����� � ����, ������ ������ �� N �����
function processPlayerWin ( $player_id, $player_level )
{
	// ������������ � ������ ������ ������� �� ������ � �����
	giveQuestPrise( $player_id, $player_level );
	
	// ���������� ������ � ����
	updatePlayerWins( $player_id );
	
	// ���������� �����
	dropQuest( );
	
	// ������ �� ������������ ������
	/*
	
		�������� � ���:
		1. ����� ������� ���������, ��� ������� ������ ������ 25? 25, 50, 75, 100... � ������� ������ ��������� ������� � ��� ������ ��� ��������
		2. ��� ���������� ������� � ����� ������� ����������?
	
	*/
	
	// ���������� ������ ��� ����������� ����������� ������ ��������
	// 10 - ������� ������, �� ��� ������� ��������� ����� ���������� ����� � ���������� �����
	// 20 - ��������� ������, �� ��� ������� ������ ��������� � ����������� ��������, ����� �������� � ��������� � ���
	$victory = getPlayerWins( $player_id );
	if ( $victory % 25 )
		return 10;
	else
		return 20;
}

// ������� ���������� ����� ���������� ������
// ������ 0 ��� �������������� ����� ������ ��� ���������� ����� ��� ������� ����������� ���� �� ���
function getPlayerWins ( $player_id )
{
	$res = f_MQuery( "SELECT win_count FROM quest_winners WHERE player_id=$player_id" );
	if ( mysql_num_rows( $res ) > 0 )
	{
		$arr = f_MFetch( $res );
		return $arr['win_count'];
	}
	else
		return 0;
}

// ����������� ����� �����
// � ������ ����� ����� ��������, ����� ���� ���� �������
function updatePlayerWins ( $player_id ) 
{
	$res = f_MQuery( "SELECT * FROM quest_winners WHERE player_id=$player_id" );
	if ( mysql_num_rows( $res ) > 0 )
	{
		f_MQuery( "UPDATE quest_winners SET win_count=win_count+1 WHERE player_id=$player_id" );
	}
	else
	{
		f_MQuery( "INSERT INTO quest_winners (player_id, win_count) VALUES ($player_id, 1)" );
	}
}

// ������� � ������ ������� �� �����
function giveQuestPrise ( $player_id, $player_level )
{
	// ��� ������ ������, ������� ������� ���� � ����� ������ (�� 2 �� 3)
	$res = f_MQuery( "SELECT COUNT(*) FROM quest_race" );
	if ( $arr = f_MFetch( $res ) )
	{
		// ���������� ����� � ����������
		if ($arr[0] > 0)
		{
			/*
				������ ����				100%		������� * 15 + ��������� �� ������� * 20 + ���������
				�������					100%		������� * 15 + ��������� �� ������� * 20 + ���������
				���������������� ����	50%			������� + 15 �� ������� + 30
				
				����� ������ �������� ����� ��������� ������ ������
			*/
			
			// ��� ������ �� ���� ������� ��������� ������� ����� 7, ��� ���� - 10
			$questSize = ( $arr[0] == 2 ) ? 7 : 10;
			
			// ������� ������� � ������ ��������� ������
			$battleExp = mt_rand( ( 15 * $player_level + $questSize * 10 ), ( 20 * $player_level + $questSize * 10 ) ); 
			$money = mt_rand( ( 15 * $player_level + $questSize * 10 ), ( 20 * $player_level + $questSize * 10 ) );
			$profExp = mt_rand( ( 15 + $player_level ), ( 30 + $player_level ) ) * mt_rand( 0, 1 ); // 50% ��������� �� ����; ��� ����� ��������� ������
			
			if ( updatePlayerValues ( $player_id, $battleExp, $money, $profExp ) )
			{
				global $questRacePrizeStr;
				$questRacePrizeStr = "<b>$battleExp</b> " . my_word_str( $battleExp, "�������", "�������", "������" ) . " ������� �����".($profExp?", <b>$profExp</b> " . my_word_str( $profExp, "�������", "�������", "������" ) . " ����������������� �����":"")." � <b>$money</b> ". my_word_str( $money, "������", "�������", "��������" );

				return true;
			}
			else
				return false;
		}
		else
			return false;
	}
	else
		return false;
}

// ����������� ���������� ��������� �������� � ������� ���� ������ � ����������� �������
function updatePlayerValues ( $player_id, $battleExp, $money, $profExp )
{
	// lock table, ��� ������, � �� ���� :-[
	
	if ( $res = f_MQuery( "UPDATE characters SET exp=exp+$battleExp, prof_exp=prof_exp+$profExp, money=money+$money WHERE player_id=$player_id" ) )
	{
		/*
			----- ��� ���������� ���������� � ������� � ��������� ��� -----
		*/
		
		return true;
	}
	else
		return false;
	
	// unlock table
}

?>
