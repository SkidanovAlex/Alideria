<?
/* @author = undefined
 * @date = 16 ������� 2011
 * @about = ���������� ��������� ������� 
 */
 
	$presentTime = (int)$_POST['prolongDays'] * 86400 + (int)$_POST['prolongHours'] * 3600;          // ���������� ����� ��������
	$premiumType = (int)$_POST['premiumType'];                                                       // ��� ����������� ��������
	$presentDeadline = time( ) + $presentTime;                                                       // ����� ���������� ���������� ��������� � ������, ����� � ������ �� ���� �������� � �� �� ��� ���������. ����� ������� � ���� ����������, ������ ��� ����� ���������� ����� �� ���� ������ 9000
	$happyPlayers = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['happyPlayers'] ) );  // ��, ���� ��������� ����������
	
	$playersCounter = 0; // ������ ��� ����������

	// ����������, ���� ����� ��������
	if( strpos( $happyPlayers, ',' ) ) // ���� ��� ������ �������
	{
		// ���������� ������������ � ����� ������ �������
		$happyPlayers = implode( '" OR login = "', explode( ',', $happyPlayers ) );
		
		// �������� ������ �������� �� ����
		$playersList = f_MQuery( 'SELECT player_id FROM characters WHERE login = "'.$happyPlayers.'"' );
	}
	elseif( $happyPlayers == '%' ) // ���� �������� ������� ���� �������
	{
		// �������� ������ ���� ������� (� ����� ������ ���� ������ ����� �����, ������ �� ����)
		$playersList = f_MQuery( 'SELECT player_id FROM characters WHERE length( pswrddmd5 ) = 32' );
	}
	else // �������� ������� ������ ������������
	{
		// �������� ����� ������ ���������� ��������
		$playersList = f_MQuery( 'SELECT player_id FROM characters WHERE login = "'.$happyPlayers.'"' );
	}

	// ��������� ���������� �� ������
	while( $playerId = f_MFetch( $playersList ) )
	{
		$playersCounter ++; // ���� ���� ����� ������������ �������
		
		$playerId = $playerId['player_id']; // ������������, �� � ����������
		
		// ���������, ���� �� ��� ������� ������ ����
		if( f_MValue( 'SELECT * FROM premiums WHERE player_id = '.$playerId.' AND premium_id = '.$premiumType ) )
		{
			// ��, ����� ������� � ��������� ����, ����������� ��� ����������������� �� �������� �����������������
			f_MQuery( 'UPDATE premiums SET deadline = deadline + '.$presentTime.' WHERE player_id = '.$playerId.' AND premium_id = '.$premiumType ); 
		
		}
		elseif( f_MValue( 'SELECT * FROM frozen_premiums WHERE player_id = '.$playerId.' AND premium_id = '.$premiumType ) )
		{
			// ��, ����� ������� � ��������� ����, �� �� ���������. �� ����� ����������� ��� �� �������� �����
			f_MQuery( 'UPDATE frozen_premiums SET duration = duration + '.$presentTime.' WHERE player_id = '.$playerId.' AND premium_id = '.$premiumType );
		}
		else
		{
			// ���, � ������ ��� �������� ������ ����
			
			// �� ���� �� � ���� ���� �����-�� ������������ ��������?
			if( $available = f_MValue( 'SELECT available FROM frozen_premiums WHERE player_id = '.$playerId ) )
			{
				// ��, ����, ��������� ������� � ������������
				f_MQuery( 'INSERT INTO frozen_premiums( player_id, premium_id, duration, available ) VALUES( '.$playerId.', '.$premiumType.', '.$presentTime.', '.$available[0].' )' );				
			}
			else
			{
				// ���, ����, ��������� ������� � ��������
				f_MQuery( 'INSERT INTO premiums( player_id, premium_id, deadline ) VALUES( '.$playerId.', '.$premiumType.', '.$presentDeadline.' )' );
			}	
		}
	}

	// �������� �� ����������
	echo '<span style="color: green; font-weight: bold;">�� � �������! ������, ������ '.$playersCounter.', �������� ���������!</span>';
?>