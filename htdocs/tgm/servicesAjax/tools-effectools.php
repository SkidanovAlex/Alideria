<?
/*
 * @author = undefined
 * @about = ��������� ������� ��� �������� �������
 */
 
	$effectName = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['effectName'] ) );
	$effectImage = mysql_real_escape_string( $_POST['effectImage'] );
	$effectDeadline = (int)$_POST['effectDeadline'];
	$effectText = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['effectText'] ) );
	$happyPlayers = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['happyPlayers'] ) );
	$effect = mysql_real_escape_string( $_POST['effect'] );
	$effectType = (int)$_POST['effectType'];
	$effectUin = (int)$_POST['effectUin'];
	
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
		
		$Player = new Player( $playerId );
		
		$Player->AddEffect( $effectUin, $effectType, $effectName, $effectText, $effectImage, $effect, $effectDeadline );
	}

	// �������� �� ����������
	echo '<span style="color: green; font-weight: bold;">�� � �������! ������, ������ '.$playersCounter.', �������� ������! ��� ��������! <s>��� �� ��������.. ���..</s>:D</span>';
?>