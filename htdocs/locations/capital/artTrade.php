<?
	/*
		���������� ����������	
	*/
	require_once( $_SERVER['DOCUMENT_ROOT'].'/items.php' );	
	
	// ID ������ => ���� �� ���� �����
	// �� �����������, ���� ������� ����� ������ 9 - ���� �� ��5 ������ 7, ���� - 10, ����� ������� 10,15,20
	$arts = array( 77127 => 5, 71032 => 2, 71042 => 1, 72802 => 10, 72801 => 10, 70969 => 125, 70964 => 100, 70965 => 100,
		 70966 => 125, 70968 => 125, 70973 => 50, 70970 => 25, 70971 => 25, 70972 => 25, 70974 => 25,
		 74639 => 7, 77224 => 7, 74476 => 7, 74477 => 7, 74475 => 7, 74520 => 7, 74521 => 7,74557 => 7, 74662 => 10, 74526 => 10, 
   		 75134 => 10, 75114 => 15, 75135 => 20, 75146=> 10, 75143=> 15, 75144=> 20, 75171 => 10, 75172=> 15, 75173=> 20);
	
	// ���������� ������
	echo "<table>";
	foreach( $arts as $itemId => $itemCost )
	{
		$item = f_MFetch( f_MQuery( 'SELECT * FROM `items` WHERE `item_id` = '.$itemId ) );
		echo "<form method='POST'><input type='hidden' name='artId' value='$itemId' />";
		echo "<tr><td style='text-align: center;'><img src='/images/items/".( ( $item[image_large] == '' ) ? $item[image] : $item[image_large] )."' /><br /><a href='/help.php?id=1010&item_id=$itemId' target='_blank'>{$item[name]}</a></td><td><img src='/images/umoney.gif' /> $itemCost</td><td><input type='submit' value='������' class='s_btn' style='width: 100px;' /></td></tr>";
		echo "</form>";
	}
	echo "</table>";

	// ���� ���� ������	
	if( $_POST['artId'] && $arts[$_POST['artId']] )
	{
		$artCost = $arts[$_POST['artId']];
		$artId = (int)$_POST['artId'];
		
		// ������� �� �����?
		if( $player->SpendUMoney( $artCost ) )
		{
			$player->AddItems( $artId, 1 );
			$player->syst2( '����������� � ��������!' );
			$player->AddToLogPost( $artId, 1, 1003, $artCost );
			$player->AddToLogPost( -1, -$artCost, 1003, $artId );

			
			$Rein = new Player( 6825 );
			$Rein->syst2( '�������� <b>'.$player->login.'</b> ������� <b><a href="/help.php?id=1010&item_id='.$artId.'" target="_blank">��� ����� ��� �����</a></b> � ����������� ����������.' );

			$bp = new Player(67573);
			$bp->syst2( '�������� <b>'.$player->login.'</b> ������� <b><a href="/help.php?id=1010&item_id='.$artId.'" target="_blank">��� ����� ��� �����</a></b> � ����������� ����������.' );

			$undefined = new Player( 286464 );
			$undefined->syst2( '�������� <b>'.$player->login.'</b> ������� <b><a href="/help.php?id=1010&item_id='.$artId.'" target="_blank">��� ����� ��� �����</a></b> � ����������� ����������.' );
		}
		else
		{
			$player->syst2( '����� ������ ��������!' );
		}
	}
?>