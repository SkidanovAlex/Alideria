<?

if( !isset( $mid_php ) ) die( "Moo!" );

if( isset( $_GET['continue'] ) )
{
	$player->SetRegime( 0 );
	$res = f_MQuery( "SELECT ref_id FROM player_invitations WHERE player_id={$player->player_id}" );
	$arr = f_MFetch( $res );
	if( $player->level % 5 == 0 && $arr )
	{
		$plr = new Player( $arr[0] );
		$plr->AddUMoney( 5 );
		$plr->AddToLogPost( -1, 5, 25, $player->player_id );
		$plr->syst2( '�������� <b>'.$player->login.'</b> ������� �� <b>'.$player->level.' �������</b>! �� ��������� <b>5 ��������</b> �������� ������ <a href="/help.php?id=50000" target="_blank">����������� ���������</a>' );
	}

	echo( "<script>location.href='game.php';</script>" );
	if( $player->level == 2 ) f_MQuery( "UPDATE statistics SET lvl2=lvl2+1" );
	
	die();
}

include_once( 'skin.php' );
echo "<center><table width=80%><tr><td>";
ScrollLightTableStart( );

echo "<br><center><b>�����������! �� ������� ��� ������� �� {$player->level}</b></center><br><br>";

if( $player->level <= 5 )
{
echo "�� ���� ������ ��� ����������� ��� ����� ������������!<br><br>";

if( $player->level == 2 )
{
	echo "<b>����� ����������</b><br>";
	echo "���� �� ������ ������ ��� ���� �������� ������ ��� ������� ����������, �� �� ������ ������ �� ������� �������� � ���� ����� ���������� ����� ���������� �����. ����������� ������� � <b>����� ������ ������</b> � ������� ���� ����� ������ �����������.<br><br>";

	echo "<b>����� �� �������</b><br>";
	echo "������ �� ������ ������������ ������ � �������� � ������� �������� �� ��������� �������. ����� ��������� ��������� ������ �� ������������� ��������� ���������: ������� �������� ������������� ���� ����� ���������� ����������� ������ ���, ��� �������� �������� �� ������, �� ����, ��� ��������� ������.<br><br>";

	echo "<b>������������</b><br>";
	echo "� ����� ������ ��� �������� ����������� ���������� &quot;������������&quot;, ������� ��������� ���������������� ��� ������ ���������. ������ ���������� ����� � <b>����� ������ ������,</b> ����� ���� ��� ���� ������� (�������� ��������� ������ � �����������). ����� ������� ��� ��������� ����������� ��������, ������� �� ������ � ������������ ��������� ����� � ������ ������ ���� ����� ����������.<br><br>";

	echo "<b>�������</b><br>";
	echo "����������� �������� ��� ������� � ��������� ������. ���������� � ��������� ��������� ��������� � ������� ��������� ����� ��� ���� ���� �������� � ���� ��������. �� ����, ��� �� ���������� ������ ������, �� ������� �������� ������ � ����� �������; � ������ ������ �� ������� �������� � ��� ������� �����!<br><br>";

	echo "<b>�������</b><br>";
	echo "���� �� �� ������ ���� �������� � �������, �� ������ �������� ������� ������ �� ������� ������� �����. ����� ����� ��� �������� ������, � �� ���� ����� ���������� ������� ��������. �� �������, ��� �� ������� ���������� ������� ������, ������� � �������.<br><br>";

}
else if( $player->level == 3 )
{
	echo "<b>��������� � ��������� ���</b><br>";
	echo "��������� � ��������� ��� ��������� ��� ������� �� ���� �� ����, ��� � �����, � ������� ������ ������. ��� �������� ���������� ��� ������ ��������, ���������������� � ���, ��� ��������, �� ����� ������� ������. ��� �������� ���������� ��� ������ ���������� ��� �������� �������, � ��� ������� ��� ����������� �� ��� ������ ��������� �������. � ������� ��������� ���� ����� ��������� � <a href=help.php?id=34317>������</a>.<br><br>";

	echo "<b>������</b><br>";
	echo "� ����� ������ �� ������ �������� � ������. ����� - ��� ����������� �������, ������������ �� ��������� �������� ����� ����. ����������� � ���� �������� ������� � ���� ����� �������: ��������� � ��� ����� ��������� � <a href=help.php?id=34252>������</a>.<br><br>";

	echo "<b>����������� �����</b><br>";
	echo "������ ��� ����, ������� �� ����������� �� ������ ���������, ���������� �� ����� � �������, � ������� ��������� ��� ��������. ��� ��������� ���������� �� ������ ������� � ����� �������. ������, ��� �� ������������ ���� �� ���� ����� � ����: �������� �� ����� &quot;������&quot; ����� ����, �� ����� ��� ������������� � ���� ����. � ���� �� �� ���������, ��� ������� �� ����� ���� ����� ����� ��� ������.<br><br>";
}

else if( $player->level == 4 )
{
	echo "<b>��������</b><br>";
	echo "� ����� ������ ��� �������� ����� ����������, ������� ��������� ��������� � ��� �������. ��������� � ��������� ������� � <a href=help.php?id=1050>������</a>.<br><br>";
}

else if( $player->level == 5 )
{
	echo "<b>������ �������</b><br>";
	echo "������� � ����� ������, �� ������ �������� � ���� �������� ������������.<br><br>";
}

else echo "�� ������� �������� ������! ����������� � ��� �� ����, � ����� ����� �� ������� ������ ����� ��������!<br><br>";
}

echo "<a href=game.php?continue>�������</a><br><br>";

ScrolLLightTableEnd( );
echo "</td></tr></table>";

?>
