<?

if( !isset( $mid_php ) ) die( );

if( 0 == ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_CONTROL ) )
{
	echo( "� ��� ��� ���� �������� � ���� �������� ������<br><a href=game.php?order=main>�����</a>" );
	return;
}

$show_list = true;

echo "<b>������ � ��������� ������</b> - <a href=game.php?order=main>�����</a><br>";

if( isset( $_GET['delrank'] ) )
{
	$id = $_GET['delrank'];
	settype( $id, 'integer' );
	if( $id == 0 ) echo "<font color=darkred>������ ������� 0-�� ������!</font><br>";
	else if( $id == 1000 ) echo "<font color=darkred>������ ������� 1000-�� ������!</font><br>";
	else if( playerSpendControlPoint( $clan_id, $player->player_id ) )
	{
		f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0, arg1, arg2 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 3, -1, 1, $id )" );
		f_MQuery( "DELETE FROM clan_ranks WHERE clan_id=$clan_id AND rank=$id" );
		f_MQuery( "UPDATE player_clans SET rank=0 WHERE clan_id=$clan_id AND rank=$id" );
	}
}

else if( isset( $_GET['deljob'] ) )
{
	$id = $_GET['deljob'];
	settype( $id, 'integer' );
	if( $id == 1000 ) echo "<font color=darkred>������ ������� 1000-�� ���������!</font><br>";
	else if( playerSpendControlPoint( $clan_id, $player->player_id ) )
	{
		f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0, arg1, arg2 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 3, -1, 2, $id )" );
		f_MQuery( "DELETE FROM clan_jobs WHERE clan_id=$clan_id AND job=$id" );
		f_MQuery( "UPDATE player_clans SET job=0 WHERE clan_id=$clan_id AND job=$id" );
	}
}


else if( isset( $_POST['rank_id'] ) )
{
	$rank_id = $_POST['rank_id'];
	settype( $rank_id, 'integer' );
	if( $rank_id < 0 || $rank_id > 1000 )
		echo "<font color=darkred>���� ������ ������ ���� ������ �� 0 �� 1000.</font><br>";
	else
	{
		$rank_name = trim( $_POST['rank_name'] );
		$ok = true;
		$l = strlen( $rank_name );
		if( $l > 22 ) echo "<font color=darkred>������ �� ����� ���� ������� 22 ����!</font><br>";
		else if( $l < 3 ) echo "<font color=darkred>������ �� ����� ���� ������ 3-� ����!</font><br>";
		else 
		{
			for( $i = 0; $i < $l; ++ $i ) if( ( $rank_name[$i] < '�' || $rank_name[$i] > '�' ) && ( $rank_name[$i] < '�' || $rank_name[$i] > '�' ) && $rank_name[$i] != '�' && $rank_name[$i] != '�' && $rank_name[$i] != ' ' )
			{
				echo "<font color=darkred>������ ����� �������� ������ �� ������� ���� � ��������!</font><br>";
				$ok = false;
				break;
			}
			if( $ok && playerSpendControlPoint( $clan_id, $player->player_id ) )
			{
				f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0, arg1, arg2 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 3, 1, 1, $rank_id )" );
				f_MQuery( "LOCK TABLE clan_ranks WRITE" );
				$res = f_MQuery( "SELECT * FROM clan_ranks WHERE clan_id=$clan_id AND rank=$rank_id" );
				if( f_MNum( $res ) ) f_MQuery( "UPDATE clan_ranks SET name='$rank_name' WHERE clan_id=$clan_id AND rank=$rank_id" );
				else f_MQuery( "INSERT INTO clan_ranks ( clan_id, rank, name ) VALUES( $clan_id, $rank_id, '$rank_name' )" );
				f_MQuery( "UNLOCK TABLES" );
			}
		}
	}
}
else if( isset( $_POST['job_id'] ) )
{
	$rank_id = $_POST['job_id'];
	settype( $rank_id, 'integer' );
	if( $rank_id < 0 || $rank_id > 999 )
		echo "<font color=darkred>���� ��������� ������ ���� ������ �� 0 �� 999.</font><br>";
	else
	{
		$rank_name = trim( $_POST['job_name'] );
		$ok = true;
		$l = strlen( $rank_name );
		if( $l > 22 ) echo "<font color=darkred>��������� �� ����� ���� ������� 22 ����!</font><br>";
		else if( $l < 3 ) echo "<font color=darkred>��������� �� ����� ���� ������ 3-� ����!</font><br>";
		else 
		{
			for( $i = 0; $i < $l; ++ $i ) if( ( $rank_name[$i] < '�' || $rank_name[$i] > '�' ) && ( $rank_name[$i] < '�' || $rank_name[$i] > '�' ) && $rank_name[$i] != '�' && $rank_name[$i] != '�' && $rank_name[$i] != ' ' )
			{
				echo "<font color=darkred>��������� ����� �������� ������ �� ������� ���� � ��������!</font><br>";
				$ok = false;
				break;
			}
			if( $ok && playerSpendControlPoint( $clan_id, $player->player_id ) )
			{
				f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0, arg1, arg2 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 3, 1, 2, $rank_id )" );
				f_MQuery( "LOCK TABLE clan_jobs WRITE" );
				$res = f_MQuery( "SELECT * FROM clan_jobs WHERE clan_id=$clan_id AND job=$rank_id" );
				if( f_MNum( $res ) ) f_MQuery( "UPDATE clan_jobs SET name='$rank_name' WHERE clan_id=$clan_id AND job=$rank_id" );
				else f_MQuery( "INSERT INTO clan_jobs ( clan_id, job, name ) VALUES( $clan_id, $rank_id, '$rank_name' )" );
				f_MQuery( "UNLOCK TABLES" );
			}
		}
	}
}
else if( isset( $_GET['rank'] ) )
{
	$rank = $_GET['rank'] ;
	settype( $rank, 'integer' );

	$res = f_MQuery( "SELECT * FROM clan_ranks WHERE clan_id=$clan_id AND rank=$rank" );
	if( !f_MNum( $res ) ) echo "<font color=darkred>� ��� ��� ������ ������ � ������.</font><br>";
	else
	{
		$arr = f_MFetch( $res );

		if( isset( $_GET['setp'] ) )
		{
       		$permitions = $_GET['setp'];
       		settype( $permitions, 'integer' );

       		$my_perm = getPlayerPermitions( $clan_id, $player->player_id );
       		if( ( ( $permitions ^ ( $arr['permitions'] ) ) | $my_perm ) != $my_perm )
       		{
       			$player->syst( "�� �� ������ ���������� ��� ������ � ������� ������ �����, �������� �� ��������� ����." );
       		}

        	else if( $permitions != $arr['permitions'] && playerSpendControlPoint( $clan_id, $player->player_id ) )
        	{
				f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0, arg1, arg2, arg3 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 4, 1, $rank, $arr[permitions], $permitions )" );
        		f_MQuery( "UPDATE clan_ranks SET permitions=$permitions WHERE clan_id=$clan_id AND rank=$rank" );
        		$arr['permitions'] = $permitions;
        	}
    	}

		echo "���������� ������� ������ <b>$arr[name]</b> - <a href=game.php?order=ranks>�����</a><br>";

		outControlsList( $arr['permitions'] );
		echo "<img width=11 height=1 src=empty.gif3>&nbsp;<button class=s_btn onclick='location.href=\"game.php?order=ranks&rank=$rank&setp=\" + permitions'>���������</button>";

		$show_list = false;
	}
}
else if( isset( $_GET['job'] ) )
{
	$rank = $_GET['job'] ;
	settype( $rank, 'integer' );

	$res = f_MQuery( "SELECT * FROM clan_jobs WHERE clan_id=$clan_id AND job=$rank" );
	if( !f_MNum( $res ) ) echo "<font color=darkred>� ��� ��� ����� ��������� � ������!</font><br>";
	else if( $rank === 1000 ) echo "<font color=darkred>������ ������ ����� �����!</font><br>";
	else
	{
		$arr = f_MFetch( $res );

		if( isset( $_GET['setp'] ) )
		{
       		$permitions = $_GET['setp'];
       		settype( $permitions, 'integer' );

       		$my_perm = getPlayerPermitions( $clan_id, $player->player_id );
       		if( ( ( $permitions ^ ( $arr['permitions'] ) ) | $my_perm ) != $my_perm )
       		{
       			$player->syst( "�� �� ������ ���������� ��� ������ � ������� ������ �����, �������� �� ��������� ����." );
       		}

        	else if( $permitions != $arr['permitions'] && playerSpendControlPoint( $clan_id, $player->player_id ) )
        	{
				f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0, arg1, arg2, arg3 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 4, 2, $rank, $arr[permitions], $permitions )" );
        		f_MQuery( "UPDATE clan_jobs SET permitions=$permitions WHERE clan_id=$clan_id AND job=$rank" );
        		$arr['permitions'] = $permitions;
        	}
    	}

		echo "���������� ������� ��������� <b>$arr[name]</b> - <a href=game.php?order=ranks>�����</a><br>";

		outControlsList( $arr['permitions'] );
		echo "<img width=11 height=1 src=empty.gif3>&nbsp;<button class=s_btn onclick='location.href=\"game.php?order=ranks&job=$rank&setp=\" + permitions'>���������</button>";

		$show_list = false;
	}
}


if( !$show_list ) return;

echo "<i>� ���� ������� ������ ������ �� ������ �������� ��� ������� ������ ��� ���������.<br>";
echo "����� ������ � ������ ������������ ��� ����������� ���� ��� ��������� � ���� ��� ������. ����� ��� ������� ������ � ��������� ����� ����� ������� � ���� �������.</i>";

echo "<table><tr><td><script>FLUl();</script>";

echo "<table><tr><td width=250><script>FUcm();</script><b>������</b><script>FL();</script></td><td width=250><script>FUcm();</script><b>���������</b><script>FL();</script></td>";

echo "<tr>";// lists

echo "<td height=100% valign=top><script>FUlt();</script>";

echo "<table width=100% cellspacing=0 cellpadding=0 border=0>";
$res = f_MQuery( "SELECT * FROM clan_ranks WHERE clan_id=$clan_id ORDER BY rank DESC" );
while( $arr = f_Mfetch( $res ) )
	echo "<tr><td align=right>$arr[rank]:&nbsp;</td><td width=100%><a href=game.php?order=ranks&rank=$arr[rank]><b>$arr[name]</b></a></td><td align=right><a href='#' onclick='if( confirm( \"������� ������ $arr[name]?\" ) ) location.href=\"game.php?order=ranks&delrank=$arr[rank]\";'>�������</a></td></tr>";
echo "</table>";

echo "<script>FL();</script></td>";

echo "<td height=100% valign=top><script>FUlt();</script>";

echo "<table width=100% cellspacing=0 cellpadding=0 border=0>";
$res = f_MQuery( "SELECT * FROM clan_jobs WHERE clan_id=$clan_id ORDER BY job DESC" );
while( $arr = f_Mfetch( $res ) )
	echo "<tr><td align=right>$arr[job]:&nbsp;</td><td width=100%><a href=game.php?order=ranks&job=$arr[job]><b>$arr[name]</b></a></td><td align=right><a href='#' onclick='if( confirm( \"������� ��������� $arr[name]?\" ) ) location.href=\"game.php?order=ranks&deljob=$arr[job]\";'>�������</a></td></tr>";
echo "</table>";

echo "<script>FL();</script></td>";

echo "</tr>"; // lists end

echo "<tr>"; // adding

echo "<td height=100% valign=top><script>FUlt();</script>";
echo "<form action=game.php?order=ranks method=post>";
echo "<table><tr><td>����:</td><td><input type=text class=m_btn name=rank_id></td></tr>";
echo "<tr><td>������:</td><td><input type=text class=m_btn name=rank_name></td></tr>";
echo "<tr><td>&nbsp;</td><td><input type=submit class=s_btn value='��������/��������'></td></tr></table>";
echo "</form>";

echo "<script>FL();</script></td>";


echo "<td height=100% valign=top><script>FUlt();</script>";
echo "<form action=game.php?order=ranks method=post>";
echo "<table><tr><td>����:</td><td><input type=text class=m_btn name=job_id></td></tr>";
echo "<tr><td>���������:</td><td><input type=text class=m_btn name=job_name></td></tr>";
echo "<tr><td>&nbsp;</td><td><input type=submit class=s_btn value='��������/��������'></td></tr></table>";
echo "</form>";

echo "<script>FL();</script></td>";

echo "</tr>"; // adding end

echo "</table>";
echo "<script>FLL();</script></td></tr></table>";

?>
