<?
/* @author = undefined
 * @date = 17 ������� 2011
 * @about = �������� ����� ���������
*/

	$moderList = explode( ',', $_POST['moderList'] ); // ������ ��������������� �����������, ������� ������������ ������
	$taskText = '������ �� �������� '.$Demiurg->login.'<br /><br />'.mysql_real_escape_string( iconv( 'UTF-8', 'CP1251', $_POST['taskText'] ) ); // ����� ������

	// ��������� ������
	$count = count( $moderList );
	for( $i = 0; $i < $count; ++ $i )
	{
		$Moder = new Player( (int)$moderList[$i] );
		
		$Moder->syst2( $taskText );
		$Moder->syst3( $taskText );	
	}
?>
<span style="color: green; font-weight: bold;">��������� �������� ���� ������</span>