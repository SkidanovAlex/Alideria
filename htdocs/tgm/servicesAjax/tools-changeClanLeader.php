<?
/* @author = undefined
 * @version = 0.0.0.1
 * @date = 12 ������� 2011
 * @about = �������� ��� ����������� �������
*/

	// �������� ����� ���������
	$playerLogin = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['clanLeaderUsername'] ) );

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
		
		// ���� ����� �� �������� (��� ������������� � �����?)
		if( !$Player->clan_id )
		{
			echo '<span style="color: darkred; font-weight: bold;">�������� �� ������� � ������</span>';
		}
		else
		{
			// ���� ��������, ������������ � ��������� �����
			f_MQuery( 'UPDATE player_clans SET rank = 0, job = 0 WHERE rank = 1000 AND clan_id = '.$Player->clan_id );
			f_MQuery( 'UPDATE `player_clans` SET `rank` = 1000, `job` = 1000, `control_points` = -1 WHERE player_id = '.$Player->player_id );

			echo '<span style="color: green; font-weight: bold;">'.$Player->login.' ����� ����� ������!</span>';
		}
	}
?>