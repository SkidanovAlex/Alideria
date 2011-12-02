<?

include_once( 'guild.php' );

$script_name = "lake.php";
$guild_id = FISHMEN_GUILD;
$cancel_text = '�� �������� ���������� �������.';
$begin_text = '�� ������������ ������ � ����...';
if( $player->player_id == 67573 ) $begin_text = '�� ������������ ������ ������, �������� � ���� �, ���� ������, ��������� ������ ���� ������.';
$descr_text = "������� ���� ����� � ���������. � ��� ���� ��� ����� ��������� ���� ��������� ������� ������.<br>";
$btn_text = "��������";
$during_text = "�� �������� ��������. ��� ������� �� ������ <a href=lake.php?cancel target=game_ref>����������</a> ��������.<br><br>";
$spent_text = "�� ������� �� ��������";

$kopka_loc = 2;
$kopka_depth = 4;

$kapkan_req_rank = 2;
$kapkan_title = "<b>��������� �����</b>";
$kapkan_what_to_do = "��������� ����";
$kapkan_what_to_do2 = "������� ����";
$kapkan_check = "��������� ����";
$kapkan_msg = "�� ���������� ����. �� ���� ��� �� ������� ";
$kapkan_nothing_text = "� ��� ���������� ��������, �� ���� �����!";

function get_finish_text( $item_id, $num, $str, $str1, $str2 )
{
	// ���������
	global $guild_id;
	if( $item_id == 111 || $item_id == 112 || $item_id == 113 )
	{
		$st = "";
		if( $item_id == 111 ) $st = "<font color=blue><b>����� ���������</b></font>";
		if( $item_id == 112 ) $st = "<font color=red><b>������� ���������</b></font>";
		if( $item_id == 113 ) $st = "<font color=green><b>������� ���������</b></font>";
		
		return "� ���������, �� ������ �� �������. ������ ������, �� ��������� ������������ ��. ����, �������, �� ������, �� ���� �� ������� ������� ����������!! �� ����������� ����������� ���������� � ���� ��������� <a href=help.php?id=1010&item_id=$item_id target=_blank>$st</a> ($num)";
	}
	
	if( $item_id == 115 )
	{
		return "� ���������, �� ������ �� �������. ��, ������ ����� �� ���� ��� ���� �� � ������� ������, �� ������ ������� ������� <a href=help.php?id=1010&item_id=$item_id target=_blank><b>������� �����</b></a> ($num)";
	}
	
	if( $item_id == 114 )
	{
		return "� ���������, �� ������ �� �������. ��, ������ ����� �� ���� ��� ���� �� � ������� ������, �� ������ ������� <a href=help.php?id=1010&item_id=$item_id target=_blank><b>�������� �����</b></a> ($num)";
	}
	
	if( $item_id == 479 )
	{
		return "� ���������, �� ������ �� �������. ��, ������ ����� �� ���� ��� ���� �� � ������� ������, �� ������ ��������� <a href=help.php?id=1010&item_id=$item_id target=_blank><b>������</b> � ������</a> ($num)";
	}
	

	if( mt_rand( 1, 20 ) == 1 )
	{
		global $player;
		$player->AddToLog( 36, 1, 1, $guild_id, 2 );
		$player->AddItems( 36, 1 );
		return "�������-�� �����! �� ��� ��� �� �������� $str. ����� ����, ������ �����, ������ ����� ��� ���������� ����� �������-�������. ����� ����������� ����� ����� � ��. � ��� ��� ��� � ��������� �������, �������, ����� � ����� ���� ��� �����. �� �������� <a href=help.php?id=1010&item_id=36 target=_blank><b>������</b></a>";
	}
	
	return "�������-�� �����! �� ��� ��� �� �������� $str"; 
}

function get_nothing_text( )
{
	global $player;
	global $guild_id;

	// ����� ����������� � ������ ������
	if( $player->HasTrigger( 1500 ) && mt_rand( 1, 5 ) == 1 )
	{
		$player->SetTrigger( 1500, 0 );
		$player->AddToLog( 124, 1, 1, $guild_id, 2 );
		$player->AddItems( 124, 1 );
		return "� ���������, �� ������ �� �������. �� �������� �������� �� <a href=help.php?id=1010&item_id=124 target=_blank><b>���� ������ � ������� ������</b></a>, ������������ �� ������ ������. ��� ���������� �����, �� ��� ������� ��������� ������� �� ����� ��������, ����� ����������.";
	}
	
	if( mt_rand( 1, 120 ) == 1 )
	{
		$player->AddToLog( 110, 1, 1, $guild_id, 2 );
		$player->AddItems( 110, 1 );
		return "� ���������, �� ������ �� �������. ������ �� ���� ��������� <a href=help.php?id=1010&item_id=110 target=_blank><b>������</b></a> ���������� ���� ������, ���������� ��� ������� � �������� ��� � ����� � ������� �����. �� ��� ��, ������������� ������, �� ����������� �� ��� �� �������� ����.";
	}
	return "� ���������, �� ������ �� �������";
}

include( 'kopka_loc.php' );

?>
