<?

if( !isset( $mid_php ) ) die( );

include_once( 'tella_assault.php' );

if( $player->regime == 0 && ta_now( ) )
{
	echo "<font color=darkred><b>��������!</b></font> ������� ��������� ���� ����� �� ��� �������� � �������� ����������� �����!!! �� ������ �������� � ��� �� ������� �����!";
	ta_output( 6, "��������", "������� ��������� ���� ���������", "��� � ��������� ��������� ���� ��������" );
	return;
}


// ����� �� ���������� � �����������

$fdm_places = Array(
	1 => "������",
	"������",
	"�������",
	"���������",
	"�����",
	"������",
	"�������",
	"�������",
	"�������",
	"�������"
);



if( $player->till && time( ) >= $player->till - 2 && $player->regime >= 300 && $player->regime <= 310 )
{
	// ��������� ����� ������

	$val = $player->GetQuestValue( 18 );
	if( $val == 0 )
	{
		$val = mt_rand( 1, count( $fdm_places ) );
		$player->SetQuestValue( 18, $val );
	}
	$cur = $player->regime - 300;

	if( $val == $player->regime - 300 )
	{
		$player->syst( "������� ������ ��������� � ���� �����, �� ����� ���������� ��� �� ��� ����� ��� ���������, ���� ��������� ������� � ���� ����. ����� �����, ������� ����� � ���� ��� ��������� � ������, � ���� ��� � ���. ����� �������� ���������� ��� ������ �������� ���� ? �� ��� ����� �������� ���� � ����, ��� ��������� �������� ����� - � �� ���������� �� ������ ����. �� ��� ������� �� ��� ��������� ����, ��� ��� ������ - �� ��������� �� ���� !! �� � ������, ������ ����� ������� � � ������." );
		$player->SetTrigger( 33, 0 );
		$player->SetTrigger( 34, 1 );

		$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$player->player_id} AND quest_part_id = 47" );
		if( !mysql_num_rows( $qres ) )
		{
			$player->syst( "���������� � ������ <b>������ � ����� ����</b> ���������." );
			f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$player->player_id}, 47 )" );
		}
	}
	else $player->syst( "������� ������ ��������� � ���� �����, ��� ����� ��������, ��� ����� ��� ������ ����� ����. ���� ����� ��� ����� ������������ � ������ �������� ���� � ��������, ��� ��� ����� �������� ������. �� �������� ������ - ��������� �� �� ��������..." );

	$player->SetRegime( 0 );
	$player->SetTill( 0 );
	$regime = 0;
}



if( $player->HasTrigger( 33 ) && $player->regime == 0 )
{
	if( isset( $_GET['fdm'] ) )
	{
		$fdm = $HTTP_GET_VARS['fdm'];
		settype( $fdm, 'integer' );
		if( !isset( $fdm_places[$fdm] ) ) RaiseError( "��� ������ ������ ������", "$fdm" );
		$player->SetTill( time( ) + 90 );
		$player->SetRegime( 300 + $fdm );
		$regime = 300 + $fdm;
	}
	else
	{

		echo "<b>������ ������:</b>";
		echo "<ul>";
		foreach( $fdm_places as $a=>$b )
		{
			echo "<li><a href=game.php?fdm=$a>� $b �����</a></li>";
		}
		echo "</ul>";
	}	
}




if( $player->regime >= 300 && $player->regime <= 310 )
{
	$text = "<b>�� ����� ������ � ".$fdm_places[$player->regime - 300]." �����";
	$text .= ".</b><br>��������: ";
	include( 'action_timer.php' );
}

?>
