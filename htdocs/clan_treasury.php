<?

if( !$mid_php ) die( );

echo "<b>������������</b> - <a href=game.php?order=main>�����</a><br>";
if( isset( $_POST['put'] ) )
{
	if( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_PUT_TO_TREASURE ) )
	{
		$val = $_POST['put'];
		settype( $val, 'integer' );

    	$bres = f_MQuery( "SELECT balance FROM player_clans WHERE player_id={$player->player_id} AND clan_id={$clan_id}" );
    	$barr = f_MFetch( $bres );
    	$balance = $barr[0];

    	$balance += $val;

		if( $val < 0 ) RaiseError( "������� �������� � ����� ������������� ���������� �����" );
		if( $val > 0 && $player->SpendMoney( $val ) )
		{
			$player->AddToLogPost( 0, -$val, 14 );
			f_MQuery( "UPDATE clans SET money=money+$val WHERE clan_id=$clan_id" );
			f_MQuery( "UPDATE player_clans SET balance = $balance WHERE player_id=$player->player_id AND clan_id=$clan_id" );
			f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 7, $val )" );
		}
	}
	else echo "<font color=darkred>� ��� ��� ����� ������ ������ � �����.</font><br>";
}
else if( isset( $_POST['take'] ) )
{
	$val = $_POST['take'];
	settype( $val, 'integer' );
	if( $val < 0 ) RaiseError( "������� ����� �� ����� ������������� ���������� �����" );

	$bres = f_MQuery( "SELECT balance FROM player_clans WHERE player_id={$player->player_id} AND clan_id={$clan_id}" );
	$barr = f_MFetch( $bres );
	$balance = $barr[0];

	$balance -= $val;

	if( $balance >= 0 && 0 == ( getPlayerPermitions( $player->clan_id, $player->player_id ) & 16 ) )
		echo "<font color=darkred>� ��� ��� ���� ����� ������ �� �����.</font>";
	else if( $balance < 0 && 0 == ( getPlayerPermitions( $player->clan_id, $player->player_id ) & 32 ) )
		echo "<font color=darkred>� ��� ��� ���� ����� ������ �� �����, ���� ����� ����� ��� ������ ���������� �������������.</font>";
	else
	{
		$arr = f_MFetch( f_MQuery( "SELECT money FROM clans WHERE clan_id=$clan_id" ) );
		if( $arr[0] < $val ) 
			echo "<font color=darkred>� ����� ��� ����� �����!</font>";
		else
		{
			$player->AddMoney( $val );
			$player->AddToLogPost( 0, $val, 14 );
			f_MQuery( "UPDATE clans SET money=money-$val WHERE clan_id=$clan_id" );
			f_MQuery( "UPDATE player_clans SET balance = $balance WHERE player_id=$player->player_id AND clan_id=$clan_id" );
			f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 7, -$val )" );
		}
	}
}

$arr = f_MFetch( f_MQuery( "SELECT money FROM clans WHERE clan_id=$clan_id" ) );

echo "<br>� ����� ������: <b>$arr[0]</b> ".my_word_str( $arr[0], '������', "�������", "��������" );
echo "<br><br>";
echo "<table>";
echo "<tr><td>�������� �������: </td><td><form action='game.php?order=treasury' method=post><input type=text name=put value=0 class=m_btn><input type=submit class=ss_btn value='��������'></form></td></tr>";
echo "<tr><td>����� �������: </td><td><form action='game.php?order=treasury' method=post><input type=text name=take value=0 class=m_btn><input type=submit class=ss_btn value='�����'></form></td></tr>";
echo "</table>";

?>
