<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $_GET['del'] ) )
{
	$id = (int)$_GET['del'];
	f_MQuery( "DELETE FROM glash_phrases WHERE entry_id=$id" );
}

if( isset( $_GET['phrase'] ) )
{
	$phrase = $_GET['phrase'];
	$pri = (int)$_GET['pri'];
	$chat = (int)$_GET['chat'];
	f_MQuery( "INSERT INTO glash_phrases ( phrase, priority, chat ) VALUES ( '$phrase', '$pri', '$chat' )" );
	
	die( "<script>location.href='admin_glash.php';</script>" );
}

$res = f_MQuery( "SELECT * FROM glash_phrases" );

if( !f_MNum( $res ) ) echo "<i>���� ��� ��� �� ����� �������</i><br><br>";
else
{
    while( $arr = f_MFetch( $res ) )
    {
    	echo "�����: <i>{$arr[phrase]}</i><br>���������: <b>{$arr[priority]}</b><br>���: <b>{$arr[chat]}</b><br><a href='admin_glash.php?del={$arr[entry_id]}'>�������</a><br><br>";
    }
}

echo "�������� �����:<br><form action='admin_glash.php' method='get'>";
echo "�����: <input type=text name=phrase><br>";
echo "���������: <input type=text name=pri><br>";
echo "����� ����: <input type=text name=chat><br>";
echo "0 - �����, > 0 - �� ������<br>";
echo "<input type='submit' value='��������'>";
echo "</form>";

echo "��������� 5 ���������� ��� ���� �� ����� � 5 ��� ������, ��� � ���������� 1. �� ���� ��� ���� ���������, ��� ���� ���� ���� ����������<br>";
echo "��� ���� ���� ����� ����, �� ��������� �� ������ ����, ���� �� ���, �� ���������� 2 � 5 ����� ����� ����� �� ������, ��� � 4 � 10. �� ���� ������ ������ ������������� ���������� �� ��������� � ������ ������<br>";

echo "<br>� ����� ������, ���������� ������ �� ��������, ��� ������������� ��� ������� ����. � �������� ���� ������ ������ �� ���������.<br>";
echo "<br />�� ���: ���� �� ��������, html-���� ��������. �������, ��� ��������� ���� ����� �������� ��� ������������� ������� ������� ��� �������� ����������: �� ����, ����� ����� ��� ����� ��������, �� �����.<br />������: <b>������ ����� ��������� ��� �����������.</b>";

?>
