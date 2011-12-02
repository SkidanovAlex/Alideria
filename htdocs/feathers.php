<?

$feathers_regime0 = array( 0, 1 );
$feathers_combat = array( 3, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 29, 30, 31, 35 );
$feathers_hour = array( 2, 4 );
$feathers_2hour = array( 32, 33, 34 );

// 28 - dispell
// 36 - obereg

$featherErr = '';

function canUseFeather( $player, $silent = false )
{
	if( $player->regime != 0 )
	{
		if( !$silent ) echo "alert('�� �� �������� ��� ������������� �������.');";
		return false;
	}
	if( $player->location == 2 && $player->depth == 43 )
	{
		if( !$silent ) echo "alert('������ ������������ ����� � ���� ��������!');";
		return false;
	}
	return true;
}

$dont_check_feather = false;
function featherCheckLimit( $lim, $player, $id )
{
	global $featherErr;
	global $dont_check_feather;
	if( $dont_check_feather ) return true;
	
	f_MQuery( "LOCK TABLE player_feathers WRITE" );
	$num = f_MValue( "SELECT count( feather_id ) FROM player_feathers WHERE player_id={$player->player_id} AND feather_id={$id}" );
	if( $num >= $lim )
	{
		$featherErr = "�� �� ������ ��������� ��� �������. �� ������ ��� ���������� ������� ������� ����� ����, ��� ��� ����� ������� �� ������� ��������� �����.";
		f_MQuery( "UNLOCK TABLES" );
		return false;
	}
	$tm = time( );
	f_MQuery( "INSERT INTO player_feathers ( player_id, feather_id, time ) VALUES ( {$player->player_id}, {$id}, {$tm} )" );
	f_MQuery( "UNLOCK TABLES" );
	return true;
}

function doFeather( $player, $id )
{
	if( $id == 0 )
	{
		if( featherCheckLimit( 1, $player, $id ) ) 
			$player->SetTrigger( 400 );
		else return false;
	}
	if( $id == 1 )
	{
		if( featherCheckLimit( 1, $player, $id ) ) 
			$player->SetTrigger( 401 );
		else return false;
	}
	if( $id == 2 )
	{
		if( featherCheckLimit( 1, $player, $id ) )
			$player->SetTrigger( 402 );
		else return false;
	}
	if( $id == 3 )
	{
		if( featherCheckLimit( 1, $player, $id ) )
			$player->SetTrigger( 403 );
		else return false;
	}
	if( $id == 4 )
	{
		if( featherCheckLimit( 1, $player, $id ) )
			$player->SetTrigger( 404 );
		else return false;
	}
	if( $id == 5 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 150, 3 );
		else return false;
	}
	if( $id == 6 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 140, 3 );
		else return false;
	}
	if( $id == 7 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 130, 3 );
		else return false;
	}
	if( $id == 8 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 16, 1 );
		else return false;
	}
	if( $id == 9 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 15, 1 );
		else return false;
	}
	if( $id == 10 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 13, 1 );
		else return false;
	}
	if( $id == 11 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 16, -1 );
		else return false;
	}
	if( $id == 12 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 15, -1 );
		else return false;
	}
	if( $id == 13 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 13, -1 );
		else return false;
	}
	if( $id == 14 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 502, 1 );
		else return false;
	}
	if( $id == 15 )
	{
		if( featherCheckLimit( 1, $player, $id ) )
			$player->SetTrigger( 405 );
		else return false;
	}
	if( $id == 16 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 131, 1 );
		else return false;
	}
	if( $id == 17 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 151, 1 );
		else return false;
	}
	if( $id == 18 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 141, 1 );
		else return false;
	}
	if( $id == 22 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 132, 1 );
		else return false;
	}
	if( $id == 23 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 152, 1 );
		else return false;
	}
	if( $id == 24 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 142, 1 );
		else return false;
	}
	if( $id == 19 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 131, -1 );
		else return false;
	}
	if( $id == 20 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 151, -1 );
		else return false;
	}
	if( $id == 21 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 141, -1 );
		else return false;
	}
	if( $id == 25 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 132, -1 );
		else return false;
	}
	if( $id == 26 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 152, -1 );
		else return false;
	}
	if( $id == 27 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 142, -1 );
		else return false;
	}

	if( $id == 29 )
	{
		if( featherCheckLimit( 1, $player, $id ) )
			$player->SetTrigger( 406 );
		else return false;
	}
	if( $id == 30 )
	{
		if( featherCheckLimit( 1, $player, $id ) )
			$player->SetTrigger( 407 );
		else return false;
	}
	if( $id == 31 )
	{
		if( featherCheckLimit( 1, $player, $id ) )
			$player->SetTrigger( 408 );
		else return false;
	}
	if( $id == 32 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterRealAttrib( 30, 1 );
		else return false;
	}
	if( $id == 33 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterRealAttrib( 40, 1 );
		else return false;
	}
	if( $id == 34 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterRealAttrib( 50, 1 );
		else return false;
	}
	if( $id == 35 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->AlterAttrib( 1, 50 );
		else return false;
	}
	if( $id == 36 )
	{
		if( featherCheckLimit( $player->level, $player, $id ) )
			$player->SetTrigger( 409 );
		else return false;
	}
	return true;
}

function undoFeather( $player, $id )
{
	if( $id == 0 )
	{
		$player->SetTrigger( 400, 0 );
	}
	if( $id == 1 )
	{
		$player->SetTrigger( 401, 0 );
	}
	if( $id == 2 )
	{
		$player->SetTrigger( 402, 0 );
	}
	if( $id == 3 )
	{
		$player->SetTrigger( 403, 0 );
	}
	if( $id == 4 )
	{
		$player->SetTrigger( 404, 0 );
	}
	if( $id == 5 )
	{
		$player->AlterAttrib( 150, -3 );
	}
	if( $id == 6 )
	{
		$player->AlterAttrib( 140, -3 );
	}
	if( $id == 7 )
	{
		$player->AlterAttrib( 130, -3 );
	}
	if( $id == 8 )
	{
		$player->AlterAttrib( 16, -1 );
	}
	if( $id == 9 )
	{
		$player->AlterAttrib( 15, -1 );
	}
	if( $id == 10 )
	{
		$player->AlterAttrib( 13, -1 );
	}
	if( $id == 11 )
	{
		$player->AlterAttrib( 16, 1 );
	}
	if( $id == 12 )
	{
		$player->AlterAttrib( 15, 1 );
	}
	if( $id == 13 )
	{
		$player->AlterAttrib( 13, 1 );
	}
	if( $id == 14 )
	{
		$player->AlterAttrib( 502, -1 );
	}
	if( $id == 15 )
	{
		$player->SetTrigger( 405, 0 );
	}
	if( $id == 16 )
	{
		$player->AlterAttrib( 131, -1 );
	}
	if( $id == 17 )
	{
		$player->AlterAttrib( 151, -1 );
	}
	if( $id == 18 )
	{
		$player->AlterAttrib( 141, -1 );
	}
	if( $id == 22 )
	{
		$player->AlterAttrib( 132, -1 );
	}
	if( $id == 23 )
	{
		$player->AlterAttrib( 152, -1 );
	}
	if( $id == 24 )
	{
		$player->AlterAttrib( 142, -1 );
	}
	if( $id == 19 )
	{
		$player->AlterAttrib( 131, 1 );
	}
	if( $id == 20 )
	{
		$player->AlterAttrib( 151, 1 );
	}
	if( $id == 21 )
	{
		$player->AlterAttrib( 141, 1 );
	}
	if( $id == 25 )
	{
		$player->AlterAttrib( 132, 1 );
	}
	if( $id == 26 )
	{
		$player->AlterAttrib( 152, 1 );
	}
	if( $id == 27 )
	{
		$player->AlterAttrib( 142, 1 );
	}

	if( $id == 29 )
	{
		$player->SetTrigger( 406, 0 );
	}
	if( $id == 30 )
	{
		$player->SetTrigger( 407, 0 );
	}
	if( $id == 31 )
	{
		$player->SetTrigger( 408, 0 );
	}
	if( $id == 32 )
	{
		$player->AlterRealAttrib( 30, -1 );
	}
	if( $id == 33 )
	{
		$player->AlterRealAttrib( 40, -1 );
	}
	if( $id == 34 )
	{
		$player->AlterRealAttrib( 50, -1 );
	}
	if( $id == 35 )
	{
		$player->AlterAttrib( 1, -50 );
	}
	if( $id == 36 )
	{
		$player->SetTrigger( 409, 0 );
	}
}


// autogenerated code
$fthrs[0] = array( '����� �������', 'feathers/1a.gif', '����� ���������� ��� ��� �������� ������ �����, ����������� �� ����, �� �������� �� \"���������\" ������� �� ���� ���� � ������ � 50%.' );
$fthrs[1] = array( '����� �������', 'feathers/1b.gif', '��� ������ ������ ����� ������ ������� ������ ������ ������ ����. ����� ������ ������ ������ ���������.' );
$fthrs[10] = array( '��������� �������', 'feathers/3c.gif', '����������� ������ ����� �� 1 ����� � ��������� ��� � ������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[11] = array( '���������� ������� � ������ �������', 'feathers/4a.gif', '��������� ������ ����������� ���� �� 1 ����� � ��������� ��� � ������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[12] = array( '����-������� ������� � ������ �������', 'feathers/4b.gif', '��������� ������ ������ �� 1 ����� � ��������� ��� � ������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[13] = array( '��������� ������� � ������ �������', 'feathers/4c.gif', '��������� ������ ����� �� 1 ����� � ��������� ��� � ������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[14] = array( '������ �������', 'feathers/5a.gif', '� ��������� ��� � ������� ��� ��� ������ ������ ����� ����� �������� �� 1 ������� ����� ������. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[15] = array( '������� �������', 'feathers/5b.gif', '�� ����� ���������� ��� �������� ��� � ������ ����� ������������� ������������� ����.' );
$fthrs[16] = array( '����� ������� � ����� �������', 'feathers/6a.gif', '����������� ������ ����� ����� ���� �� 1 ����� � ������� ��� � ��������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[17] = array( '������� ������� � ����� �������', 'feathers/6b.gif', '����������� ������ ����� ����� ���� �� 1 ����� � ������� ��� � ��������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[18] = array( '������� ������� � ����� �������', 'feathers/6c.gif', '����������� ������ ����� ����� ������� �� 1 ����� � ������� ��� � ��������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[19] = array( '����� ������� � ������ �������', 'feathers/7a.gif', '��������� ������ ����� ����� ���� �� 1 ����� � ������� ��� � ��������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[2] = array( '������ �������', 'feathers/1c.gif', '����� ������ � ���� ������ � ��� ���� ������. <br /><b>������� �������� ���� ���.</b>' );
$fthrs[20] = array( '������� ������� � ������ �������', 'feathers/7b.gif', '��������� ������ ����� ����� ���� �� 1 ����� � ������� ��� � ��������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[21] = array( '������� ������� � ������ �������', 'feathers/7c.gif', '��������� ������ ����� ����� ������� �� 1 ����� � ������� ��� � ��������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[22] = array( '����� ������� � ����� ��������', 'feathers/8a.gif', '����������� ������ ������ ����� ���� �� 1 ����� � ������� ��� � ��������� ���. ����� ��������� �� ������ ������ � �� ����� � ��� �� ���� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[23] = array( '������� ������� � ����� ��������', 'feathers/8b.gif', '����������� ������ ������ ����� ���� �� 1 ����� � ������� ��� � ��������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[24] = array( '������� ������� � ����� ��������', 'feathers/8c.gif', '����������� ������ ������ ����� ������� �� 1 ����� � ������� ��� � ��������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[25] = array( '����� ������� � ������ ��������', 'feathers/9a.gif', '��������� ������ ������ ����� ���� �� 1 ����� � ������� ��� � ��������� ���.  ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[26] = array( '������� ������� � ������ ��������', 'feathers/9b.gif', '��������� ������ ������ ����� ���� �� 1 ����� � ������� ��� � ��������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[27] = array( '������� ������� � ������ ��������', 'feathers/9c.gif', '��������� ������ ������ ����� ������� �� 1 ����� � ��������� ��� � ������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[28] = array( '����������� �������', 'feathers/10.gif', '������� � ������ ������ ������ ������� ������.' );
$fthrs[29] = array( '�������������� ���������� �������', 'feathers/11.gif', '� ����� ���������� ��� ��� �������� ��� � ������ �������������� ��������� <b>������ � ����� ����.</b> ����� ��������� ������ � ������, � �������� �� ���������� �� ������ ��������������� �����������, ���������� ��� �������-������ �������.' );
$fthrs[3] = array( '������ ������� � ����� �������', 'feathers/1d.gif', '� ����� ���������� ��� �������� ��� � ������ �������������� �� ��������� ������� ����.' );
$fthrs[30] = array( '�������������� ��������� �������', 'feathers/12.gif', '� ����� ���������� ��� ��� �������� ��� � ������ �������������� ��������� <b>������ � ������ ����.</b> ����� ��������� ������ � ������, � �������� �� ���������� �� ������ ��������������� �����������, ���������� ��� �������-������ �������.' );
$fthrs[31] = array( '�������������� �����-����� �������', 'feathers/13.gif', '� ����� ���������� ��� ��� �������� ��� � ������ �������������� ��������� <b>������.</b> ����� ��������� ������ � ������, � �������� �� ���������� �� ������ ��������������� �����������, ���������� ��� �������-������ �������.' );
$fthrs[32] = array( '�������������� ����� �������', 'feathers/14.gif', '����������� ������ ����� ���� �� 1 �����. ������� �������� <b>2 ����.</b><br />��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ���� <b>�� ����.</b>' );
$fthrs[33] = array( '�������������� ������� �������', 'feathers/15.gif', '����������� ������ ����� ������� �� 1 �����. ������� �������� <b>2 ����.</b><br />��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ���� <b>�� ����.</b>' );
$fthrs[34] = array( '�������������� ������� �������', 'feathers/16.gif', '����������� ������ ����� ���� �� 1 �����. ������� �������� <b>2 ����.</b> <br />��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ���� <b>�� ����.</b>' );
$fthrs[35] = array( '�������������� ������� �������', 'feathers/17.gif', '����� ��������������� 50 ������ ��������. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[36] = array( '�������������� ������� �������', 'feathers/17a.gif', '��� ��������� �� ����� ������������ ���������� ���� ����� ����� � ������ 25% ������ ���������� ������������, ���� �� ����� �� ���������� <b>� ��������</b> � � ���� �� �������� ��������. ������� �������� �� ������ �� ��� ���, ���� �� �� ������� ����������.' );
$fthrs[4] = array( '����� ������� � ������ �������', 'feathers/1e.gif', '����� ������� ����� � ������ � 50% ����� ������� � 5 ��� ������. <br /><b>������� �������� ���� ���.</b>' );
$fthrs[5] = array( '������� �������', 'feathers/2a.gif', '����������� ������ ���� ���� �� 3 ������ � ��������� ��� � ������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[6] = array( '������� �������', 'feathers/2b.gif', '����������� ������ ���� ������� �� 3 ������ � ��������� ��� � ������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[7] = array( '����� �������', 'feathers/2c.gif', '����������� ������ ���� ���� �� 3 ������ � ��������� ��� � ������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[8] = array( '���������� �������', 'feathers/3a.gif', '����������� ������ ����������� ���� �� 1 ����� � ��������� ��� � ������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );
$fthrs[9] = array( '����-������� �������', 'feathers/3b.gif', '����������� ������ ������ �� 1 ����� � ��������� ��� � ������� ���. ��� ������ ������� ������, ��� ������ ����� ������ ����� ��������� �� ����.' );


?>
