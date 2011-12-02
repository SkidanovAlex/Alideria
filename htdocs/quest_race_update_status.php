<?

// ��� ������ ��������, ������� ������������ ����� ���� ������� � ��������, ����� ���������� ��� �������
// ���������� ������������ �������� ������ ������ �������� quest_value
// ���������, �� ������� �� �������� ���� �����
// �������� ������ �����, ����� ����� �������� ��������� ��������, � �� �����-�� ������
// $event_value �� ��������� 1, ����� �������� ������ �������� ��� ������� � �������� � ������� � �������� 
// ��������, � ����� ������ �� ��� ����� ������� updateQuestStatus ( 12345, 2501 );
// � � �������� ������ - updateQuestStatus ( 12345, 2513, { 2, 4, 6, 10 - ���� �� ���� ����� � ����������� �� ���-�� ��� } );
function updateQuestStatus ( $player_id, $event_id, $event_value = 1, $event_detail = -1 )
{
	if( $event_detail == -1 ) $res = f_MQuery( "SELECT * FROM quest_race WHERE race_type=$event_id" );
	else $res = f_MQuery( "SELECT * FROM quest_race WHERE race_type=$event_id AND race_details=$event_detail" );
	if ( mysql_num_rows( $res ) == 0 )
		return false;
	
	// �� ����� ����� ������ ������ �����, ����� ������� ������� �������� ������ ������
	$plr = new Player( $player_id );
	
	// �������� ��������� �� �����
	if( !$plr->HasTrigger( 262 ) ) return false;
	// �������� ���� �� ��� ��� ��������� ��� �������
	if( $plr->GetQuestValue( $event_id ) >= f_MValue( "SELECT race_amount FROM quest_race WHERE race_type = $event_id" ) ) return false;
	
	// ���� ����� ������ ����� ���������� ������, ������� ��� ��������� quest_value
	// ��������, ��� � ������ ������ ������� ������ GetQuestValue, ����� ��������� ���������� ���� ������� ����
	if ( $plr->GetQuestValue( $event_id ) == 0 )
	{
		$plr->SetQuestValue( $event_id, $event_value );
	}
	// ��� �������� �������-�� � ��� ���������
	else
	{
		$plr->AlterQuestValue( $event_id, $event_value );
	}

	// ������ ��������, ����� �������� ������ ����� ��������� ����������
	// ���� ����� �������� ��� �������, ������� ��� ��������� ������
	if ( getPlayerProgress( $player_id ) )
	{
		//$winValue = processPlayerWin( $player_id, $plr->level );
		
		$plr->SetTrigger( 260 );
		$plr->syst2( '��� ������� ����� ���������. ������ ����������� � ����!' );
		
		return 10;
	}
	else $plr->syst2( '���������� � ������� ����� ���������!' );

	// 5 -> ������� �������� ���������� ������ �������, ����� ��-�������� ��������� � �������� ���������� �������
	return 5;
}

// �������� ������ ���������� ������
// true - ���� ��� ������� ���������
// false - ���� �� ���
function getPlayerProgress ( $player_id )
{
	$value = true;
	$plr = new Player( $player_id );
	
	// �������� ��� ���� ������� �� �������� ������
	$res = f_MQuery( "SELECT race_type, race_amount FROM quest_race" );
	$i = 0;
	$raceTypes = array();
	$raceAmounts = array();
	while ( $arr = f_MFetch( $res ) )
	{
		$raceTypes[$i] = $arr['race_type'];
		$raceAmounts[$i] = $arr['race_amount'];
		$i ++;
	}
	
	// ��� ������� ���� �������� ������� ����������
	for ( $i = 0; $i < count( $raceTypes ); $i ++ )
	{
		// �������������� ���� ���� �� ���� �� ���������� ������ �������, $value ������ false
		if ( $plr->GetQuestValue( $raceTypes[$i] ) < $raceAmounts[$i] )
			$value = false;
	}
	
	return $value;
}



?>
