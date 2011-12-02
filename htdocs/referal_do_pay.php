<?
	// ����������� ��������� - ������� ���������� ������� �������� �������� ������
	require_once( 'functions.php' );
	require_once( 'player.php' );
	
	// $amount - ����� ��������� ��������� ��������
	// $player_id - ������������� ��������
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
			$inviter->syst2( '�������� <b>'.$referal->login.'</b> ������� ������ ������ ��������. �� ��������� <b>'.my_word_str( $amount, "������", "�������", "��������" ).'</b> �������� ������ <a href="/help.php?id=50000" target="_blank">���������� ���������</a>' );
		}
	}
?>