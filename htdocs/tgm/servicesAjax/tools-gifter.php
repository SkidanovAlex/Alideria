<?
/* @author = undefined
 * @date = 16 ������� 2011
 * @about = ��������� ������� �������
 */
 
	$fromWho = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['fromWho'] ) );                                        // �� ���� �������?
	$presentImage = mysql_real_escape_string( $_POST['presentImage'] );                              // �������� �������
	$presentDeadline = (int)$_POST['presentDeadline'];                                               // �� ������� ������� �������?
	$presentText = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['presentText'] ) );    // �������������� ������� �����
	$happyPlayers = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['happyPlayers'] ) );  // ��, ���� ���������
	
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
		$playersList = f_MQuery( 'SELECT player_id FROM characters WHERE length( pswrddmd5 ) = 32 AND sex = 1' );
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
		
		f_MQuery( 'INSERT INTO player_presents( player_id, img, txt, author, deadline ) VALUES ( '.$playerId.', "'.$presentImage.'", "'.$presentText.'" ,"'.$fromWho.'", '.$presentDeadline.' )' );
	}

	// �������� �� ����������
	echo '<span style="color: green; font-weight: bold;">�� � �������! ������, ������ '.$playersCounter.', �������� �������!</span>';
?>