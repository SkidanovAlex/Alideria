<?
/* @author = undefined
 * @date = 17 ������� 2011
 * @about = �������� ��� ������� ������ ���� �������
 */

	// �������� ����� ���������
	$playerLogin = mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['playerLogin'] ) );

   // �������� ���� ������ ������ �� ��
	$playerId = f_MValue( 'SELECT player_id FROM characters WHERE login = "'.$playerLogin.'"' );
	
	// ���������, ���� �� ����� ��������
	if( !$playerId )
	{
		// ����
		echo '<span style="color: darkred; font-weight: bold;">��� ������ ���������</span>';
	}
	else
	{
		// ����	
		f_MQuery( 'UPDATE player_profile SET descr = "" WHERE player_id = '.$playerId );
		
		echo '<span style="color: green; font-weight: bold;">������! <a href="/player_info.php?nick='.$playerLogin.'" target="_blank">(���������)</a></span>';
	}
?>