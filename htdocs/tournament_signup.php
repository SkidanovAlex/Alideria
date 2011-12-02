<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "tournament_functions.php" );
include_once( "tournament_order_functions.php" );
include_once( "skin.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "�������� ��������� Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

if( $player->location != 2 || $player->depth != 43 ) RaiseError( "������� ���������� �� ������ �� ������ � ���� ��������", "Loc: {$player->location}; Depth: {$player->depth};" );

$id = $HTTP_RAW_POST_DATA;
if( isset( $_GET['id'] ) ) $id = $_GET['id'];
settype( $id, 'integer' );

$res = f_MQuery( "SELECT * FROM tournament_announcements WHERE tournament_id=$id" );
$arr = f_MFetch( $res );

if( !$arr ) RaiseError( "������� ���������� �� �������������� ������", "$id" );

if( $arr['date'] - time( ) < 300 ) die( "alert( '������ �� ������ ��� ���������' );" );
if( $arr['min_level']  > $player->level || $arr['max_level']  < $player->level ) RaiseError( "������� ���������� �� ������ ��� �� ������ ������", "Min: $arr[min_level] Max: $arr[max_level] Cur: {$player->level}" );

// ������ �� ������ ����� �� ��� �������

$player_tournaments = f_MQuery( "SELECT `tournament_id` FROM `tournament_players` WHERE `player_id` = {$player->player_id}" );
while( $tournament_id = f_MFetch( $player_tournaments ) )
{
	$tournament_id = $tournament_id[0];
	
	if( f_MValue( "SELECT * FROM tournament_announcements WHERE tournament_id=$tournament_id AND `date` > $arr[date] - 43200 AND `date` < $arr[date] + 43200 AND `tournament_id` != $id" ) )
	{
		if( $player->Rank( ) == 1 ) die( "alert( 'SELECT * FROM tournament_announcements WHERE tournament_id=$tournament_id AND ( date > $arr[date] - 43200 OR date < $arr[date] + 43200 )' );" );
		die( "alert( '�� ��� ������� �� ������ � ���� ����. ������ ���������� �� ������� ��� � ���������� �� ����.' );" );
	}
}

if( $arr['type'] == 2 )
{
	// process order competitions separately
	if( $player->clan_id == 0 ) die( 'alert("�� �� �������� � ������");' );

	if( isset( $_GET['cancel'] ) )
	{
		// player was shown the list of his order bets and now wants to return to tournament info view
		$st = getTournamentDesc( $arr );
		echo "document.getElementById( 't$id' ).innerHTML = '".AddSlashes( $st )."';";
	}
	else if( isset( $_GET['apply'] ) )
	{
		// player wants to assign himself to the tournament
		$slot = (int)$_GET['apply'];
		$bid = (int)$_GET['bid'];
		
		if( $slot < 0 || $slot > 5 ) RaiseError( "�������� �������� ���� slot ��� ������ �� ������ �������", "$slot" );

		$ok = true;
		f_MQuery( "LOCK TABLES tournament_group_bets WRITE" );
		$st = ""; for ($i = 0; $i < 6; ++ $i) $st .= " OR slot_$i = {$player->player_id}";
		$v = f_MValue( "SELECT count( bet_id ) FROM tournament_group_bets WHERE tournament_id=$id AND (1=2 $st)" );
		if( $v ) $ok = false;
		
		if( !$ok ) echo "alert( '�� ��� �������� �� ���� ������ � ������ ������. ������� ������� ���������� �� ���.' );";
		else if( $slot < 2 && $player->level > 9 ) { $ok = false; echo "alert( '������ ������ 9 ������ � ���� ����� ���������� �� ������� � � �.' );"; }
		else if( $slot < 4 && $player->level > 14 ) { $ok = false; echo "alert( '������ ������ 14 ������ � ���� ����� ���������� �� ������� � � �.' );"; }
		else
		{
			if( $bid == 0 ) // ����� ������
			{
				f_MQuery( "INSERT INTO tournament_group_bets (slot_{$slot}, clan_id, tournament_id, count) VALUES ({$player->player_id}, {$player->clan_id}, $id, 1)" );
			}
			else // ����������� � ������ ������
			{
				$clan_id = f_MValue( "SELECT clan_id FROM tournament_group_bets WHERE bet_id=$bid" );
				if( $clan_id != $player->clan_id ) { RaiseError( "������� ���������� � ������ �� ����� �����", "BID: $bid; clan_id: $clan_id; player clan: {$player->clan_id}" ); }
				$slot_cur = f_MValue( "SELECT slot_{$slot} FROM tournament_group_bets WHERE bet_id=$bid" );
				if( $slot_cur > 0 ){ $ok = false; echo "alert( '���-�� ��������� �� ��� �� ��� �������.' );"; }
				else
					f_MQuery( "UPDATE tournament_group_bets SET slot_{$slot} = {$player->player_id}, count=count + 1 WHERE bet_id = $bid" );
			}
		}
		f_MQuery( "UNLOCK TABLES" );
		if( $ok )
		{
			$st = getTournamentDesc( $arr );
			echo "document.getElementById( 't$id' ).innerHTML = '".AddSlashes( $st )."';";
			echo "alert( '�� ������� ������� ���� ��� � ������ ���������� �������' );";
		}
	}
	else if( isset( $_GET['remove'] ) )
	{
		for ($i = 0; $i < 6; ++ $i)
		{
			f_MQuery( "UPDATE tournament_group_bets SET slot_{$i}=0, count=count-1 WHERE tournament_id=$id AND slot_{$i} = {$player->player_id}" );
		}
		f_MQuery( "DELETE FROM tournament_group_bets WHERE count = 0" );
		$st = getTournamentDesc( $arr );
		echo "document.getElementById( 't$id' ).innerHTML = '".AddSlashes( $st )."';";
		echo "alert( '�� ������� ���������� �� �������' );";
	}
	else
	{
		// player wants to show the list of teams
    	$st = GetScrollLightTableStart( );
    	$st .= "<table width=300><tr><td>";
    	$st .= GetScrollTableStart( "center" );
    	$st .= "<a target=_blank href=help.php?id=>�������� � ������ � ���������� ������ �� �������</a>";
    	$st .= GetScrollTableEnd( );
    	$st .= "</td></tr><tr><td>";
    	$st .= GetScrollTableStart( "left" );
    	$st .= "<table width=100%>";
    	$tres = f_MQuery( "SELECT * FROM tournament_group_bets WHERE tournament_id=$id AND clan_id={$player->clan_id}" );
    	$pos = 1;
    	while($tarr = f_MFetch($tres))
    	{
    		$st .= "<tr><td colspan=2 align=center><b>������ #$pos</b></td></tr>";
    		++ $pos;
    		$st .= showBet($tarr);
    	}
    	$st .= "<tr><td colspan=2 align=center><b>������� ����� ������</b></td></tr>";
    	$st .= showBet(array("bet_id" => 0));
    	$st .= "</table>";
    	$st .= GetScrollTableEnd( );
    	$st .= "</td></tr><tr><td>";
    	$st .= GetScrollTableStart( "center" );
    	$st .= "<a href='javascript:void(0)' onclick='query(\"tournament_signup.php?id=$id&cancel=1\",\"\")'>�������</a>";
    	$st .= GetScrollTableEnd( );
    	$st .= "</td></tr></table>";
    	$st .= GetScrollLightTableEnd( );
		echo "document.getElementById( 't$id' ).innerHTML = '".AddSlashes( $st )."';";
	}
	die( );	
}

f_MQuery( "LOCK TABLES tournament_players WRITE" );

// ������� � �������
$res = f_MQuery( "SELECT * FROM tournament_players WHERE player_id={$player->player_id} AND tournament_id=$id" );
if( f_MNum( $res ) )
{
	f_MQuery( "DELETE FROM `tournament_players` WHERE `tournament_id` = $id AND `player_id` = {$player->player_id}" );
	$player->syst2( "�� ������� ���������� �� ������� � ������� <b>$arr[name]</b>" );
	$st = getTournamentDesc( $arr );
	echo "document.getElementById( 't$id' ).innerHTML = '".AddSlashes( $st )."';";
}
else
{
	f_MQuery( "INSERT INTO tournament_players ( tournament_id, player_id ) VALUES ( $id, {$player->player_id} )" );
	f_MQuery( "UNLOCK TABLES" );
	$player->syst2( "�� ������� ���������� �� ������ <b>$arr[name]</b>" );
	$st = getTournamentDesc( $arr );
	echo "document.getElementById( 't$id' ).innerHTML = '".AddSlashes( $st )."';";
}

?>
