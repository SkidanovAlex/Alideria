<?

include_once( 'prof_exp.php' );

if( !isset( $mid_php ) ) die( );

echo "<b>���� � ������</b> - <a href=game.php?order=main>�����</a><br><br>";

$level = getBLevel( 6 );

if( $level < 4 )
{
	echo "<i>��� ����, ����� ���������� � ����������, ���� ����� ������ ���� �� ���������� ������...</i>";
	return;
}

$moo = f_MValue( "SELECT count( player_id ) FROM player_portal_visits WHERE player_id={$player->player_id}" );
if( $moo )
{
	echo "<i>�� ��� ��������� ����� ������ �������. ����� �� ������� ���������� ������ ����� ��������.</i>";
	return;
}

if( $player->regime == 0 && isset( $_GET['enter'] ) )
{
	include_once( 'locations/portal/func.php' );
	if( $player->SetLocation( 5 ) )
	{
		$player->SetDepth( 0 );
		if( portal_swap_items( $player->player_id ) )
		{
			f_MQuery( "INSERT INTO player_portal_visits ( player_id ) VALUES ( {$player->player_id} )" );
			die( '<script>location.href="game.php";</script>' );
		}
		else
		{
			$player->SetLocation( 2 );
			$player->SetDepth( 50 );
			echo "<script>setTimeout( function(){alert( '������ ��� ����� � ������, ������� ��� ����' );}, 100 );</script>";
		}
	}
}

echo "<table width=90% cellspacing=0 cellpadding=0><tr><td align=justify>";
echo "���������� - ���������� ������ ��� � ������� ��������. � ���������� ������ ������� � ���-�� ��������� �������� �� ����� ����. ��������, ��� ����� ������� �������� ����� ������, �� �������� ��� ������, ������ � ��������. ��������� ������ ���������� �������� ������� �� �������� ������ ����� ������, ���� ������ ���� - ������� �� ������������ ����� ����������. ������� ������, ��� ������ ����� ������, ���������� ��� � ���� �����. ����� �� ��������� �� �� ������� �������, ��� ���� �� ����� ���� �������� �� ������ ���������, �� ��� �������� ������� � ��� �� ������ ��� �� �������� ����� ������ �������. ����������, ��� ����, ������� �� ������� �� ��������, �� ��������� �������� � ���� ���, �� ��� ����� ������ ��� ����������� � ��� � �����, ����� �� ������ ������������ � ����������.<br><br>";
echo "�� ������ ����� � ������ ������ ���� ��� � �����. ��� ���� �� ������ ��� �������� ������� �������, ������� ��� ������� - � ������� �� ���������� � ����� � ������, ��������� � ��� � ���������� �� ������ �� ����� ����������� ������������.<br><br>";
echo "<li><a href='javascript:enter_portal()'>����� � ������</a>";
echo "</td></tr></table>";

?>

<script>
function enter_portal()
{
	if( confirm( '����� � ������?' ) )
		location.href='game.php?order=portal&enter=1';
}
</script>
