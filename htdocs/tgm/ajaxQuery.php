<?
/* @author = undefined
 * @version = 1.0.0.1
 * @date = 12 ������� 2011
 * @about = �������� �������������� ��������, ������� � �������� ������������
 */

	// ���������� � ��������� ������ ������������������ �������
	Header( 'Content-type: text/html; charset=windows-1251' );

	// ����������� ������������ ������
	require_once( '../functions.php' );	// ������ ����������� ����������� ������� ����
	require_once( '../player.php' );		// ��������������� �����, ���������� ��������� �������� ��� �������������

	// �������� �� �������������� � ����
	if( !check_cookie( ) )
	{
		die( );	
	}
	else
	{
		$Demiurg = new Player( $_COOKIE['c_id'] );

		if( $Demiurg->Rank( ) != 1 )
		{
			die( );
		}
	}
	
	// ���������� AJAX-���������� �������
	$serviceIdentity = preg_replace( '/[^a-zA-Z0-9_-]+/', '', $_GET['service'] ); // ������ �� ��������� ��������
	if( !include_once( 'servicesAjax/'.$serviceIdentity.'.php' ) )
	{
		echo '<span style="color: darkred; font-weight: bold;">����������� AJAX-���������� ������ "'.$serviceIdentity.'"</span>';	
	}
?>