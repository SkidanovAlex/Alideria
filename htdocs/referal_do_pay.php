<?
	// Реферальная программа - функция начисления нужного процента талантов игроку
	require_once( 'functions.php' );
	require_once( 'player.php' );
	
	// $amount - число купленных рефералом талантов
	// $player_id - идентификатор реферала
	function referalDoPay( $player_id, $amount )
	{
		$referal = new Player( $player_id );
		
		$inviterId = f_MValue( "SELECT ref_id FROM player_invitations WHERE player_id={$referal->player_id}" );		

		if( $inviterId )
		{
			$inviter = new Player( $inviterId );
		
			$amount = round( $amount * 0.2 );
		
			$inviter->AddUMoney( $amount );
			$inviter->AddToLogPost( -1, $amount, 1001, $referal->player_id );
			$inviter->syst2( 'Персонаж <b>'.$referal->login.'</b> приобрёл свежую порцию талантов. Вы получаете <b>'.my_word_str( $amount, "талант", "таланта", "талантов" ).'</b> согласно правил <a href="/help.php?id=50000" target="_blank">Партнёрской Программы</a>' );
		}
	}
?>