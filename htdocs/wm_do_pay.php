<?
/* @author = Ishamael, undefined
 * @date = 28 февраля 2011
 * @about = Совершение платежа вебманей
 */
 
	$dont_check_params = true;
	
	require_once( 'functions.php' );
	require_once( 'player.php' );
	require_once( 'referal_do_pay.php' );

	f_MConnect( );

	LogError( 'WM CONNECT' . var_export( $_POST, true ) );


	$pre = $_POST['LMI_PREREQUEST'];

	if( $pre )
	{
		$purse = $_POST['LMI_PAYEE_PURSE'];

		// Валидация кошелька		
		if( $purse != 'R255933152423' && $purse != 'U313261991121' && $purse != 'Z352663604725' )
		{
			LogError( "WM Error: Purse: $purse" );
			die( 'NO' );
		}
		
		// Валидация суммы в талантах
		$ammount = $_POST['LMI_PAYMENT_AMOUNT'];
		if( ( $purse == 'R255933152423' && fmod( $ammount, 10 ) != 0 ) or
			 ( $purse == 'U313261991121' && fmod( $ammount, 2.7 ) != 0 ) or
			 ( $purse == 'Z352663604725' && fmod( $ammount, 0.35 ) != 0 )
		  )
		{
			LogError( "WM Error: Amount: $ammount" );
			die( 'NO' );
		}
		
		$player_id = (int)$_POST['LMI_PAYMENT_NO'];
		$res = f_MQuery( "SELECT count( player_id ) FROM characters WHERE player_id=$player_id" );
		$arr = f_MFetch( $res );
		if( !$arr[0] )
		{
			LogError( "WM Error: Player_id: $player_id" );
			die( 'NO' );
		}

		LogError( 'WM Check success' );
		die( 'YES' );
	}
	else
	{
		/*// Валидация параметров
		$wmhash = mb_strtoupper( md5( $_POST['LMI_PAYEE_PURSE'].$_POST['LMI_PAYMENT_AMOUNT'].$_POST['LMI_PAYMENT_NO'].$_POST['LMI_MODE'].$_POST['LMI_SYS_INVS_NO'].$_POST['LMI_SYS_TRANS_NO'].$_POST['LMI_SYS_TRANS_DATE'].'secret_key'.$_POST['LMI_SECRET_KEY'].$_POST['LMI_PAYER_PURSE'].$_POST['LMI_PAYER_WM'] ) );
		if( $wmhash != $_POST['LMI_HASH'] )
		{
			LogError( 'WM Error: lie in hash ( '.$wmhash.' != '.$_POST['LMI_HASH'].' ) && md5( '.$_POST['LMI_PAYEE_PURSE'].$_POST['LMI_PAYMENT_AMOUNT'].$_POST['LMI_PAYMENT_NO'].$_POST['LMI_MODE'].$_POST['LMI_SYS_INVS_NO'].$_POST['LMI_SYS_TRANS_NO'].$_POST['LMI_SYS_TRANS_DATE'].$_POST['LMI_SECRET_KEY'].$_POST['LMI_PAYER_PURSE'].$_POST['LMI_PAYER_WM'].' )' );
			die( 'NO' );
		}*/
		
		$purse = $_POST['LMI_PAYEE_PURSE'];
		$ammount = $_POST['LMI_PAYMENT_AMOUNT'];
		$player_id = (int)$_POST['LMI_PAYMENT_NO'];

		// Вычисляем сумму в талантах
		if( $purse == 'R255933152423' ) // Покупаем в рублях
		{	
			$ammount = floor( ($ammount+0.0000001) / 10 );
		}
		elseif( $purse == 'U313261991121' ) // Покупаем в гривнах
		{
			$ammount = floor( ($ammount+0.0000001) / 2.7 );		
		}
		elseif( $purse == 'Z352663604725' ) // Покупаем в долларах
		{
			$ammount = floor( ($ammount+0.0000001) / 0.35 );
		}
		
		$player = new Player( $player_id );
		$player->AddUMoney( $ammount );
		$player->AddToLogPost( -1, $ammount, 22, 1 );
		$player->syst2( "Вы приобрели <b>$ammount</b> ".my_word_str( $ammount, "талант", "таланта", "талантов" ) );
		
		referalDoPay( $player->player_id, $ammount );
	}
?>