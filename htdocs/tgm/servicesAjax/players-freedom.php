<?
/* @author = undefined
 * @version = 0.0.0.1
 * @date = 12 ������� 2011
 * @about = �������� ��� ������������ ������� (������� ����� � ��������� � ������ � �����)
*/

	// �������� ����� ���������
	$playerLogin = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['freedomUsername'] ) );

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
		
		// ���� ����� �����, ������� ����� � ���������
		if( $Player->regime !=0 )
		{
			$Player->SetRegime( 0 );
		}
		// ���� ����� ������������� � �����, ������� ����� ��� ��������
		if( $Player->till != 0 )
		{
			$Player->SetTill( 0 );
		}

		echo '<span style="color: green; font-weight: bold;">�������� ���������!</span>';
	}
?>