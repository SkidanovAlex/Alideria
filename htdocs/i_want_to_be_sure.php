<?

include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "�������� ��������� Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">

<?

include_js( "js/skin.js" );

echo "<center><table width=80%><tr><td><script>FLUl();</script>";

echo "������ IWtBS ��������� ��� ����� ���������� �� ����� ����, ����� ����� ����� ������ ���������� � ������������ �����, ������, ������������ ����� � ��������� ������������ ��������.<br>����� �������� ������ � �������� ���������: �������� ��������; ��������� ��������� ���������� ����; �������� ������ � ����������, ��� ����� ����������� ������������ ��� �������.";

echo "<script>FLL();</script></td></tr>";

if( 1 == $_GET['do'] )
{
	f_MQuery( "LOCK TABLE player_counters WRITE" );
	f_MQuery( "DELETE FROM player_counters WHERE player_id={$player->player_id}" );
	f_MQuery( "INSERT INTO player_counters( player_id ) VALUES ({$player->player_id})" );
	f_MQuery( "UNLOCK TABLES" );
}
if( 2 == $_GET['do'] )
	f_MQuery( "DELETE FROM player_counters WHERE player_id={$player->player_id}" );

$arr = f_MFetch( f_MQuery( "SELECT * FROM player_counters WHERE player_id={$player->player_id}" ) );

if( !$arr )
{
	echo "<tr><td><script>FLUl();</script>";
	echo "� ��������� ������ � ��� �� ������������ ���������� ����.<br>";
	echo "�� ������ ������ �� ����������: <a href=i_want_to_be_sure.php?do=1>������� ����</a>";
	echo "<script>FLL();</script></td></tr>";
}

else
{
	echo "<tr><td><script>FLUl();</script>";
	echo "<b>������� ���������� �� ���� � ������� ������ ������ �����������:</b><br>";
	echo "<table cellspacing=0 cellpadding=0>";
	echo "<tr><td>����� �����:</td><td>&nbsp;</td><td>{$arr[turns]}</td></tr>";
	echo "<tr><td>&nbsp;&nbsp;&nbsp;�� ������:</td><td>&nbsp;</td><td>{$arr[hits]}</td></tr>";
	echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�� ��� �� ����� ������� ������ ��� ������:</td><td>&nbsp;</td><td>{$arr[doubles_allowed]}</td></tr>";
	echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�� ��� �� ������� ������ ��� ������:</td><td>&nbsp;</td><td>{$arr[doubles]}</td></tr>";
	echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�� ��� �� ������� ������:</td><td>&nbsp;</td><td>{$arr[triples]}</td></tr>";
	echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�� ������� ���� ������:</td><td>&nbsp;</td><td>{$arr[damages]}</td></tr>";
	echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�� ��� ����������� ������:</td><td>&nbsp;</td><td>{$arr[krits]}</td></tr>";
	echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�� ��� ������� ����������� ������:</td><td>&nbsp;</td><td>{$arr[triplekrits]}</td></tr>";
	echo "<tr><td>&nbsp;&nbsp;&nbsp;�� ��� ������:</td><td>&nbsp;</td><td>{$arr[loses]}</td></tr>";
	echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�� ��� ������� ������:</td><td>&nbsp;</td><td>{$arr[resists]}</td></tr>";
	echo "<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�� ��� ���� ������� ������:</td><td>&nbsp;</td><td>{$arr[doubleresists]}</td></tr>";
	echo "</table>";
	echo "<br>";
	echo "<li><a href=i_want_to_be_sure.php?do=1>�������� ���������� � ������ ������ ������</a>";
	echo "<li><a href=i_want_to_be_sure.php?do=2>�������� ���������� � ���������� ������</a>";
	echo "<script>FLL();</script></td></tr>";
}

echo "</table></center>";


?>
