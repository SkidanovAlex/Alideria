<?

if( !isset( $mid_php ) ) die( );

$havkas = Array (
	Array( "������ ��������", 6, 50, '������ ��������' ),
	Array( "�������", 10, 100, "�������" ),
	Array( "�������� ��-����������", 18, 200, "�������� ��-����������" )
);

if( isset( $_GET['eat'] ) && $player->regime == 0 )
{
	$id = $_GET['eat'];
	settype( $id, 'integer' );
	if( $id < 0 || $id >= count( $havkas ) ) RaiseError( "������� ������ �������� �� �������� ���������!", "$id" );

	if( !$player->SpendMoney( $havkas[$id][1] ) )
		$player->syst( '� ��� ������������ ��������' );
	else
	{
		$player->AddToLogPost( 0, -$havkas[$id][1], 11 );
		$player->syst( "�� ������� <b>{$havkas[$id][3]}</b> � ������������ <b>{$havkas[$id][2]}</b> ������ ��������" );
		$player->AlterRealAttrib( 1, $havkas[$id][2] );
		
		// Widow quest
	   	include_once( "quest_race.php" );
	   	updateQuestStatus ( $player->player_id, 2510, $havkas[$id][2] );
	}
}


echo "<table><tr><td>";
ScrollLightTableStart( );
echo "<table><tr><td>&nbsp;</td><td height=100%>".GetScrollTableStart( )."<b>���������</b>".GetScrollTableEnd( )."</td><td height=100%>".GetScrollTableStart( )."<b>��������������� ��������</b>".GetScrollTableEnd( )."</td><td>&nbsp;</td></tr>";
foreach( $havkas as $id=>$arr )
{
	echo "<tr><td height=100%>".GetScrollTableStart( 'left' )."$arr[0]".GetScrollTableEnd( )."</td><td height=100%>".GetScrollTableStart( 'right' )."$arr[1] <img width=11 height=11 border=0 src='images/money.gif'>".GetScrollTableEnd( )."</td><td height=100%>".GetScrollTableStart( 'right' )."$arr[2]".GetScrollTableEnd( )."</td><td>".GetScrollTableStart( )."<a href=game.php?eat=$id>������</a>".GetScrollTableEnd( )."</td></tr>";
}
echo "</table>";
ScrollLightTableEnd( );
echo "</td></tr></table>";
echo "<br>";


if( $player->HasTrigger( 231 ) )
{
	echo "<script>q7_login = '$player->login';</script>";
	include( 'quest_scripts/q7_tavern.php' );
}


// ����� �� ���������� � �����������

$fdm_places = Array(
	1 => "������",
	"������",
	"������ ����",
	"������� �����������",
	"���������",
);



if( $player->till && time( ) >= $player->till - 2 && $player->regime >= 300 && $player->regime <= 310 )
{
	// ��������� ����� �������

	$val = $player->GetQuestValue( 16 );
	if( $val == 0 )
	{
		$val = mt_rand( 1, count( $fdm_places ) );
		$player->SetQuestValue( 16, $val );
	}
	$cur = $player->regime - 300;

	if( $val == $player->regime - 300 )
	{
		$player->syst( "����������, ����� ������ ������� �� ����� ��� $fdm_places[$cur] ���������� ���������� ������! ���� ���������� ������� ������� �����������!" );
		$player->AddToLog( 219, 1, 1000000 );
		$player->AddItems( 219 );
		$player->SetTrigger( 18, 0 );
		$player->SetTrigger( 45, 1 );

		$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$player->player_id} AND quest_part_id = 31" );
		if( !mysql_num_rows( $qres ) )
		{
			$player->syst( "���������� � ������ <b>����� ���������� ������</b> ���������." );
			f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$player->player_id}, 31 )" );
		}
	}
	else $player->syst( "������, ������ �������� �� ��� $fdm_places[$cur]" );

	$player->SetRegime( 0 );
	$player->SetTill( 0 );
	$regime = 0;
}



if( $player->HasTrigger( 18 ) && $player->regime == 0 )
{
	if( isset( $_GET['fdm'] ) )
	{
		$fdm = $HTTP_GET_VARS['fdm'];
		settype( $fdm, 'integer' );
		if( !isset( $fdm_places[$fdm] ) ) RaiseError( "��� ������� ������ ������", "$fdm" );
		$player->SetTill( time( ) + 90 );
		$player->SetRegime( 300 + $fdm );
		$regime = 300 + $fdm;
	}
	else
	{

		echo "<b>������ ���������� ������:</b>";
		echo "<ul>";
		foreach( $fdm_places as $a=>$b )
		{
			echo "<li><a href=game.php?fdm=$a>��� $b</a></li>";
		}
		echo "</ul>";
	}	
}




if( $player->regime >= 300 && $player->regime <= 310 )
{
	$text = "<b>�� ����� ���������� ������ ��� ".$fdm_places[$player->regime - 300];
	$text .= ".</b><br>��������: ";
	include( 'action_timer.php' );
}

?>
