<?
/* @author = undefined
 * @about = �������� ��� ����������� �������
*/

	// �������� ����� ���������
	$playerLogin = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['penaltyUsername'] ) );

	// �������� ������������� ������
	$playerIdQuery = f_MQuery( 'SELECT player_id FROM characters WHERE login = "'.$playerLogin.'"' );
	$playerId = f_MFetch( $playerIdQuery );
	$playerId = $playerId['player_id'];
	
	// ��������� ������������� ������
	if( !$playerId )
	{
		echo '<span style="color: darkred; font-weight: bold;">�������� �� ���������</span>';
	}
	else
	{
		// ������ ��������� ������
		$Player = new Player( $playerId );
		$penaltySum = (int)$_POST['penaltySum'];
		
		// �������� ��������� ����� �����
		f_MQuery( 'UPDATE characters SET money = money - '.$penaltySum.' WHERE player_id = '.$Player->player_id );
		// �������� ���������� � ����
		$Player->AddToLog( 0, $penaltySum * -1, 1001, $Demiurg->player_id );
		$Player->syst2( '��� ����������� �� <b>'.$penaltySum.'</b> '.my_word_str( $penaltySum, '������', '�������', '��������' ).'.' );
		
		echo '<span style="color: green; font-weigth: bold;">�������� '.$Player->login.' ���������� �� '.$penaltySum.' '.my_word_str( $penaltySum, '������', '�������', '��������' );
		echo '<br />';
		echo '������ � ���� '.( $Player->money - $penaltySum ).' '.my_word_str( ( $Player->money - $penaltySum ), '������', '�������', '��������' ).'</span>';
	}
?>