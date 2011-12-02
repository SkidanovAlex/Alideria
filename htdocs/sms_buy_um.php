<?php
$dont_check_params = true;
require_once("functions.php");
require_once("player.php");
require_once( 'referal_do_pay.php' );

f_MConnect();

if ( isset($_GET['txt']) && preg_match('/(\d+)/', $_GET['txt'], $res) )
{
//	$crys 		= $res[0];
	$pref       = htmlspecialchars($_GET['pref']);
	$phone 		= htmlspecialchars($_GET['phone']);
	$operator 	= htmlspecialchars($_GET['op']);
	$req_phone 	= htmlspecialchars($_GET['sn']);
	$sms_id		= htmlspecialchars($_GET['tid']);

	if( $pref != 'tal' && $pref != '3FF tal' && $pref != 'RR tal' && $pref != 'FF tal' )
	{
		LogError( "СМС - неверное ключевое слово" );
		die( 'sms=Неверное ключевое слово - свяжитесь с администрацией' );
	}

	settype( $uid, 'integer' );

	$player_id = (int)$res[1];
	settype( $player_id, 'integer' );
	$res = f_MQuery( "SELECT count( player_id ) FROM characters WHERE player_id=$player_id" );
	$arr = f_MFetch( $res );
	if( !$arr[0] )
	{
		LogError( "SMS Error: Player_id: $player_id" );
		$out_text = "sms=Вы указали неверный ID игрока!";
	}

	elseif($req_phone == 6365 )
	{
		$ammount = 4;
    	$player = new Player( $player_id );
    	$player->AddUMoney( $ammount );
    	$player->AddToLogPost( -1, $ammount, 22, 0 );
    	$player->syst2( "Вы приобрели <b>$ammount</b> ".my_word_str( $ammount, "талант", "таланта", "талантов" ) );

		$out_text = "sms=Персонажу начислено $ammount таланта!";

		$add = "PID: $player_id | NUM: 4 | OPERATOR: " . $operator . " | PHONE: " . $phone . " | SMS_ID: " . $sms_id;
		LogError( "SMS Success $add" );
		
		referalDoPay( $player->player_id, $ammount );		
	}
	elseif($req_phone == 8385 )
	{
		$ammount = 10;
    	$player = new Player( $player_id );
    	$player->AddUMoney( $ammount );
    	$player->AddToLogPost( -1, $ammount, 22, 0 );
    	$player->syst2( "Вы приобрели <b>$ammount</b> ".my_word_str( $ammount, "талант", "таланта", "талантов" ) );

		$out_text = "sms=Персонажу начислено $ammount талантов!";

		$add = "PID: $player_id | NUM: 10 | OPERATOR: " . $operator . " | PHONE: " . $phone . " | SMS_ID: " . $sms_id;
		LogError( "SMS Success $add" );
		
		referalDoPay( $player->player_id, $ammount );		
	}
	elseif($req_phone == 3141 || $req_phone == 1315 || $req_phone == 1897 )
	{
		$ammount = 4;
    	$player = new Player( $player_id );
    	$player->AddUMoney( $ammount );
    	$player->AddToLogPost( -1, $ammount, 22, 0 );
    	$player->syst2( "Вы приобрели <b>$ammount</b> ".my_word_str( $ammount, "талант", "таланта", "талантов" ) );

		$out_text = "sms=Персонажу начислено $ammount таланта!";

		$add = "PID: $player_id | NUM: 4 | OPERATOR: " . $operator . " | PHONE: " . $phone . " | SMS_ID: " . $sms_id;
		LogError( "SMS Success $add" );
		
		referalDoPay( $player->player_id, $ammount );		
	}
	elseif($req_phone == 13015 )
	{
		$ammount = 5;
    	$player = new Player( $player_id );
    	$player->AddUMoney( $ammount );
    	$player->AddToLogPost( -1, $ammount, 22, 0 );
    	$player->syst2( "Вы приобрели <b>$ammount</b> ".my_word_str( $ammount, "талант", "таланта", "талантов" ) );

		$out_text = "sms=Персонажу начислено $ammount таланта!";

		$add = "PID: $player_id | NUM: $ammount | OPERATOR: " . $operator . " | PHONE: " . $phone . " | SMS_ID: " . $sms_id;
		LogError( "SMS Success $add" );
		
		referalDoPay( $player->player_id, $ammount );		
	}
	elseif($req_phone == 15330 )
	{
		$ammount = 2;
    	$player = new Player( $player_id );
    	$player->AddUMoney( $ammount );
    	$player->AddToLogPost( -1, $ammount, 22, 0 );
    	$player->syst2( "Вы приобрели <b>$ammount</b> ".my_word_str( $ammount, "талант", "таланта", "талантов" ) );

		$out_text = "sms=Персонажу начислено $ammount таланта!";

		$add = "PID: $player_id | NUM: $ammount | OPERATOR: " . $operator . " | PHONE: " . $phone . " | SMS_ID: " . $sms_id;
		LogError( "SMS Success $add" );
		
		referalDoPay( $player->player_id, $ammount );		
	}
	elseif($req_phone == 9645 )
	{
		$ammount = 4;
    	$player = new Player( $player_id );
    	$player->AddUMoney( $ammount );
    	$player->AddToLogPost( -1, $ammount, 22, 0 );
    	$player->syst2( "Вы приобрели <b>$ammount</b> ".my_word_str( $ammount, "талант", "таланта", "талантов" ) );

		$out_text = "sms=Персонажу начислено $ammount таланта!";

		$add = "PID: $player_id | NUM: $ammount | OPERATOR: " . $operator . " | PHONE: " . $phone . " | SMS_ID: " . $sms_id;
		LogError( "SMS Success $add" );
		
		referalDoPay( $player->player_id, $ammount );		
	}
	elseif($req_phone == 3304 )
	{
		$ammount = 6;
    	$player = new Player( $player_id );
    	$player->AddUMoney( $ammount );
    	$player->AddToLogPost( -1, $ammount, 22, 0 );
    	$player->syst2( "Вы приобрели <b>$ammount</b> ".my_word_str( $ammount, "талант", "таланта", "талантов" ) );

		$out_text = "sms=Персонажу начислено $ammount таланта!";

		$add = "PID: $player_id | NUM: $ammount | OPERATOR: " . $operator . " | PHONE: " . $phone . " | SMS_ID: " . $sms_id;
		LogError( "SMS Success $add" );
		
		referalDoPay( $player->player_id, $ammount );
	}	
	else
	{
		LogError( "SMS Error: number [$req_phone]" );
		$out_text = "sms=ошибка: Данный номер не обслуживается!";
	}
}
else
{
	LogError( "SMS Error: [query {$_GET[text]}]" );
	$out_text = "sms=ошибка: Ошибочный запрос!";
}

header("Content-Type: text/plain");

echo $out_text;

?>
