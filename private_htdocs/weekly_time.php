<?
/* @author = undefined
 * @date = 28 ������� 2011
 * @about = ������������� ������, ������������� � ������� � ����������� �� �����������
 */

	// �����������
	require_once( 'time_functions.php' );
	require_once( 'functions.php' );
	
	// ������� � ��
	f_MConnect( );
	
	// ���� ����� ��������� ������������ �������
	
	// ��������� ���������� ����� ��� �������
	f_MQuery( 'UPDATE labyrinth_of_love SET best_time = 999999999 WHERE 1' );
	
	// ����� �� ������� � �����
	$clanPlayers = f_MQuery( 'SELECT level,clan_id FROM characters WHERE clan_id > 0' );
	$clans = array( );
	$nalog = 100; // ����� == ��� ��������

	while( $player = f_MFetch( $clanPlayers ) )
	{
		if( !$clans[$player[clan_id]] )
		{
			$clans[$player[clan_id]] = 0;
		}
		
		$clans[$player[clan_id]] += $nalog * $player[level];
	}

	// ������� ������
	foreach( $clans as $clan_id=>$money )
	{
		if (f_MValue("SELECT level FROM clan_buildings WHERE building_id = 14 AND clan_id=".$clan_id) < 2) // ���� ������� ����� ����� ������ ����, �� ����� �����
			f_MQuery( 'UPDATE clans SET money = money - '.$money.' WHERE clan_id = '.$clan_id );
	}
	
	// ���������� ������ ��� ������ �����
	$lo_day = mt_rand( 0, 6 );
	$time = $lo_day * 86400 + mt_rand( 0, 10800 ) + 68400 + mktime( 23, 59, 59, date( 'n' ), date( 'd' ), date( 'Y' ) );;
	
	f_MConnect( );
	f_MQuery( "UPDATE `lo` SET `time` = $time, `status` = 0" );
	
	echo "UPDATE `lo` SET `time` = $time, `status` = 0<br />";
	echo date( 'H:i:s d.n.Y', $time ).'<br /><br />';
	
	// ��������� ������
	// 0 - ������� ����
	// 1 - ������ �����
	// 2 - ���������� �����
	$slot = array( 0, 1, 1, 1, 1, 1, 1, 1, 2 );
	
	shuffle( $slot );
	echo str_replace( "\n", '<br />', print_r( $slot, true ) );
	
	foreach( $slot as $slot_id=>$type )	
	{
		echo "UPDATE `lo_slots` SET `type` = $type WHERE `slot_id` = ".( $slot_id + 1 )."<br />";
		f_MQuery( "UPDATE `lo_slots` SET `type` = $type WHERE `slot_id` = ".( $slot_id + 1 ) );
	}
?>