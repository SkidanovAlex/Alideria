<?

include_once( 'guild.php' );

$script_name = "berrypickers_loc.php";
$guild_id = BERRYPICKERS_GUILD;
$cancel_text = '�� �������� ������������� � ������';
$begin_text = '�� ������������� �� ������ �� ������� � �������';
$descr_text = "�� ����� ��� � ������ ����������� � ���<br>";
$btn_text = "�����������";
$during_text = "�� ����� ����� � �����. ��� ������� �� ������ <a href=berrypickers_loc.php?cancel target=game_ref>���������</a> ����� ��������<br><br>";
$spent_text = "�� ������� �� ������";

$kopka_loc = 2;
$kopka_depth = 48;

$kapkan_req_rank = -1; // ���� �� ������
$kapkan_title = "<b>��������� ��������</b>";
$kapkan_what_to_do = "���������� �������";
$kapkan_what_to_do2 = "������� �������";
$kapkan_check = "��������� �������";
$kapkan_msg = "�� ���������� �������. �� ���� ��� ��� �������� ";
$kapkan_nothing_text = "� ��� ���������� ��������, �� ��� ������� �����!";

function get_finish_text( $item_id, $num, $str, $str1, $str2 )
{
	global $guild_id;
	// ������ ���������
	if( $item_id == 102 )
	{
		return "�� ������������ ����. ����� ������� ������, ����� ������ ��� ���� �������� .. �� �� ���� ������ � ������, ��� ������ ������ ��� !! ������ ��� ������ �� ������ ����� : � ����, ��� �� ����� ��������� !! � ���� ������, ����� �� ������ �� ����� ���� ���������. ��� ��� �����, ������ � ��� ���� $str1";
	}
	
    // �������� ����
	if( $item_id == 101 )
	{
		return "�� ������������ ����. ����� ������� ������, ����� ������ ��� ���� �������� .. ������� ��� ����� ������������ ��� ������ ������ ? �� ������ ������ ���� ��� � ����� ������� ��� � �������� ���� �������� .. ��� ����� ������� ���� ���������� ������, �������� ����� ��� ����, ����� ������� ����������� ���-��. ����� ������������������ ����� ������ ������, ��� ��� �� ���������� �� �������� �� ���� � � ������ ������� �����. �� �� ������ ���������� ��� ���� �� ����� ��������. �� ����� ���� - ��� �� $str1. ����� ����� !! � ��� ��� � ��� ������ ... ";
	}
	
    // �������
	if( $item_id == 100 )
	{
		return "������ ��������� ��� ����� �������� �����, �� ������ ���� ������� �������, ���� ������ ����� ���-�� ���������, ������� � ����������������, ��� ����� ��� ������� ��������� ��. �� ������ � ���� �� ����� ����� ����� ������� � ���, ����� ���� ����. ����������� �� �������� ��������. ��� ���� ����� ��������� ��������, �� ��� �� �������� ��� � ������� $str";
	}
	
	if( mt_rand( 1, 80 ) == 1 )
	{
		global $player;
		$player->AddToLog( 87, 1, 1, $guild_id, 2 );
		$player->AddItems( 87, 1 );
		return "�� ������������ ����. ������ �� ������, �� � ��� �������. �� �������� $str. �� ����� �������, ����� � ���� ������� ������ �� �������� <a href=help.php?id=1010&item_id=87 target=_blank><b>������ ���������� �����</b></a>. �� ������ ������ ����� ������������, �� �� ��� ���������� ���������� ���������� ������� ����������� ������  ����. ��������� ��� �� �� ���� �� ���� ..";
	}
	if( mt_rand( 1, 79 ) == 1 )
	{
		global $player;
		$lnum = mt_rand( 2, 5 );
		if( $player->money >= $lnum )
		{
			$player->AddToLog( 0, - $lnum, 1, $guild_id, 2 );
			$player->SpendMoney( $lnum );
			return "�� ������������ ����. ������ �� ������, �� � ��� �������. �� �������� $str. ����������� ���������, �� �������������, ��� ���-�� ������� ��� �� ������. �� ��� �� ���? ����� ������� ��� ����? ��� ������ ����������� ������ �������� ���, ����� ������� ���������� ���������. �������� ���, �� � ���������� ��������, ��� �������� ���-�� � ����� <b>���� �����</b> ($lnum). ����� ������ ..";
		}
	}
	if( mt_rand( 1, 2500 ) == 1 )
	{
		global $player;
		$player->AddToLog( 104, 1, 1, $guild_id, 2 );
		$player->AddItems( 104, 1 );
		return "�� ������������ ����. ������ �� ������, �� � ��� �������. �� �������� $str. ����� ��������� ������� � ������, �� � ���������� ���������, ��� ��������� � ����� �� ���-�� ������� � ���������. �� ��� �� <a href=help.php?id=1010&item_id=104 target=_blank><b>��������� ������</b></a> - ��� ��� ����� ���������� � �������. �� ������ ����� ������� ������� ?.. ";
	}

	return "�� ������������ ����. ������ �� ������, �� � ��� �������. �� �������� $str"; 
}

function get_nothing_text( )
{
	global $player;
	global $guild_id;
	
	if( mt_rand( 1, 120 ) == 1 )
	{
		global $player;
		$lnum = mt_rand( 1, 3 );
		$player->AddToLog( 99, $lnum, 1, $guild_id, 2 );
		$player->AddItems( 99, $lnum );
		return "�� ������������ ����. ����� ������� ������, ����� ������ ��� ���� �������� .. � ������� �� ��� ��������� ���� �������� ������ � ����� ����� ������� ��������. ��� � �������� ��������� �� ������ ����, ������� ������� ����� ���� � �� ��� �������� ����� �������� ����� �� <a href=help.php?id=1010&item_id=99 target=_blank><b>������� ������</b></a> ($lnum).";
	}

	return "�� ������������ ����. ����� ������� ������, ����� ������ ��� ���� �������� .. ";
}

include( 'kopka_loc.php' );

?>