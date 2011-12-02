<?

if( !$i_permit ) die( );

// �����
if( $won && $mob_id == 28 ) checkZhorik( $this, 12, 10 );
if( $won && $mob_id == 7 ) checkZhorik( $this, 11, 10 );
if( $won && $mob_id == 11 ) checkZhorik( $this, 10, 10 );
if( $won ) checkZhorik( $this, 4, 10 );

if( $this->location == 2 && $this->depth == 1 ) checkZhorik( $this, 5, 5 ); // ����� ������ ��� �� �����


// ����� �����
if( $won )
{
    $convertedId = -1;
    $questRaceIds = array( 37, 24, 18, 10, 25, 38, 7, 35, 8, 29, 31, 32, 11, 13, 26, 30, 28 );
    for( $i = 0; $i < count( $questRaceIds ); ++ $i )
    	if( $questRaceIds[$i] == $mob_id )
    		$convertedId = $i;
    if( $i != -1 )
    {
    	include_once( "quest_race_update_status.php" );
    	updateQuestStatus ( $this->player_id, 2501, 1, 101 + $convertedId );
    }
}


if( $this->HasTrigger( 227 ) )
{
	if( $won )
	{
		$this->SetTrigger( 221, 1 );
		f_MQuery( "DELETE FROM player_talks WHERE player_id={$this->player_id}" );
		$this->SetRegime( 0 );
	}
	else $this->SetTrigger( 226, 1 );
	$this->SetTrigger( 227, 0 );
}

if( $this->HasTrigger( 39 ) && !$this->HasTrigger( 40 ) && $won && $mob_id == 7 )
{
	if( mt_rand( 1, 50 ) <= 15 )
	{
		$this->AddItems( 6125, 1 );
		$this->SetTrigger( 40, 1 );
		$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$this->player_id} AND quest_part_id = 52" );
		if( !mysql_num_rows( $qres ) )
		{
			f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$this->player_id}, 52 )" );
		}
		$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$this->player_id} AND quest_part_id = 53" );
		if( !mysql_num_rows( $qres ) )
		{
			$this->syst( "���������� � ������ <b>��������� ��� ��������</b> ���������." );
			f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$this->player_id}, 53 )" );
		}
		$this->syst( "��� �����, ������� ��� � ��� ���������, ���� ������ �������. ��� �� ������ ������� ����� �������� �������. �� ��, ��� ���� ����� ��� �������� - ������ �����. ������, ��� ����� ���� ������ ��� �����������, �������� ��� �������� �� ���������. ������ ����� ����� �������� � ���, ��� �� ������� �������. �� ��������� <a href=help.php?id=1010&item_id=6125 target=_blank>����� �����</a> ����. " );
	}
}

// ����-����� 2, ��� ������ � �����, ��� � ������� ����� ����� ����
if( $this->HasTrigger( 66 ) && $this->location == 3 && $this->depth == 2 )
{
	if( !$won ) $this->syst2( '��� ���������� ��� ����������� ������. ��� ��� ��� ��������. ��� ���� ������ �� ����� ���� ����� � �������� ���� ���-�� �� ������� � ��������� ������. ���, ���� ������ �� ����. �������, ������ ������� � ������� �����. �� ������������ � ������, �� ���� �� ���� �� ������� ������������ �����������. �� �� ��� �����: ������ ���, �����, �������, � ���, �� ���� ��������� ����.' );
	else
	{
		$this->syst2( "����! ��������� ���� � ������ ����� ������� ���������� ���� ���. �������, ������� ����, ������� �����. � �� �������� � ������ � �������� ����. �� ��, ������ ��� ������� �����: ����� ����, ������ �������� � ������ � ��������� ���� ������ ������� ������� �� � �����. �����-�� ������������ ��������� �����������" );
    $this->SetTrigger( 66, 0 );
    $this->SetTrigger( 67, 1 );
		$this->AddItems( 9196, 1 );
	}
}

// ����� ��������� ���
if( $this->HasTrigger( 81 ) && $this->GetQuestValue( 40 ) < 10 && $won && $mob_id == 37 )
{
    $this->AlterQuestValue( 40, 1 );
	$val = 10 - $this->GetQuestValue( 40 );
	if( $val )	
		$this->syst2( "��� ���� ��� ��������, ��� <b>$val</b>, � ����� ������������ � ��� � ���������� ���������." );
	else
	{
		$this->syst2( "��� 10 ����� ���������, �������� ��������� � ���." );
		$this->syst( "���������� � ������ <b>�������� � ����</b> ���������." );
		f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$this->player_id}, 138 )" );
	    $this->SetTrigger( 81, 0 );
	    $this->SetTrigger( 82, 1 );
	}
}

if( $this->HasTrigger( 231 ) && $this->GetQuestValue( 46 ) < 3 && $won && $mob_id == 24 )
{
	$this->AlterQuestValue( 46, 1 );
	if( $this->GetQuestValue( 46 ) == 3 )
	{
		$this->syst2( "������ ��� ������ ����. ��� �� ������ ���� �������� ���������. ����� ������ ������ �������." );
		$this->SetTrigger( 232 );
		$this->SetTrigger( 231, 0 );
	}
	else $this->syst2( "��� ���� ������ ��������." );
}

if( $this->HasTrigger( 240 ) && $this->GetQuestValue( 47 ) < 25 && $won && $mob_id == 26 ||
    $this->HasTrigger( 241 ) && $this->GetQuestValue( 47 ) < 30 && $won && $mob_id == 32 ||
    $this->HasTrigger( 242 ) && $this->GetQuestValue( 47 ) < 35 && $won && $mob_id == 36 )
{
	$this->AlterQuestValue( 47, 1 );
}

?>
