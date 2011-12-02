<?php
require_once("functions.php");
require_once("player.php");
require_once("referal_do_pay.php");

f_MConnect();

if ( isset($_GET['text']) && preg_match('/([A-z]+)\s*(\d+)/', $_GET['text'], $res) )
{
	$crys 		= $res[1];
	$phone 		= htmlspecialchars($_GET['phone']);
	$operator 	= htmlspecialchars($_GET['op']);
	$req_phone 	= htmlspecialchars($_GET['isnn']);
	$sms_id		= htmlspecialchars($_GET['sms_id']);

	settype( $uid, 'integer' );

	$player_id = (int)$res[2];
	$res = f_MQuery( "SELECT count( player_id ) FROM characters WHERE player_id=$player_id" );
	$arr = f_MFetch( $res );
	if( !$arr[0] )
	{
		LogError( "SMS Error: Player_id: $player_id" );
		$out_text = "==LAT==Vy ukazali nevernyj ID igroka!==RUS==�� ������� �������� ID ������!";
	}

	elseif($req_phone == 7250)
	{
		$ammount = 5;
    	$player = new Player( $player_id );
    	$player->AddUMoney( $ammount );
    	$player->AddToLogPost( -1, $ammount, 22, 0 );
    	$player->syst2( "�� ��������� <b>$ammount</b> ".my_word_str( $ammount, "������", "�������", "��������" ) );

		$out_text = "==LAT==Personaju nachisleno $ammount talantov!==RUS==��������� ��������� $ammount ��������!";

		$add = "PID: $player_id | NUM: 5 | OPERATOR: " . $operator . " | PHONE: " . $phone . " | SMS_ID: " . $sms_id;
		LogError( "SMS Success $add" );
		
		referalDoPay( $player->player_id, $ammount );
	}
	elseif($req_phone == 5373)
	{
		$ammount = 10;
    	$player = new Player( $player_id );
    	$player->AddUMoney( $ammount );
    	$player->AddToLogPost( -1, $ammount, 22, 0 );
    	$player->syst2( "�� ��������� <b>$ammount</b> ".my_word_str( $ammount, "������", "�������", "��������" ) );

		$out_text = "==LAT==Personaju nachisleno $ammount talantov!==RUS==��������� ��������� $ammount ��������!";

		$add = "PID: $player_id | NUM: 10 | OPERATOR: " . $operator . " | PHONE: " . $phone . " | SMS_ID: " . $sms_id;
		LogError( "SMS Success $add" );
		
		referalDoPay( $player->player_id, $ammount );
	}
	else
	{
		LogError( "SMS Error: number [$req_phone]" );
		$out_text = "==LAT==error: Current number not supported!==RUS==������: ������ ����� �� �������������!";
	}
}
else
{
	LogError( "SMS Error: [query {$_GET[text]}]" );
	$out_text = "==LAT==error: Oshibochnij zapros!==RUS==������: ��������� ������!";
}

header("Ort: OK");
header("Content-Type: text/plain");

echo $out_text;

?>
