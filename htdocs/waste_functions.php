<?

function getActions( $game_id )
{
	global $player;
	$res = f_MQuery( "SELECT player1_id, player2_id FROM waste_bets WHERE player1_id = {$player->player_id} OR player2_id = {$player->player_id}" );
	$arr = f_MFetch( $res );

	if( !$arr ) 
	{
		if( $player->level == 1 ) return "<li><a href='javascript:act2(0, 0)'>������ ������</a><br><li><a href='javascript:ref()'>��������</a><br>";
		else return "<li><a href='javascript:act2(0, document.getElementById( \"stv\" ).value)'>������ ������</a>. <input id=stv type=hidden value=0><li><a href='javascript:ref()'>��������</a><br>";// ������: <input class=btn40 maxlength=5 id=stv value=0> <img src=images/money.gif width=11 height=11><br>";
	}
	else if( $arr['player1_id'] == $player->player_id )
	{
		$ret = "<li><a href='javascript:act(1)'>�������� ������</a><br>";
		if( $arr['player2_id'] != -1 )
		{
			$cres = f_MQuery( "SELECT login FROM characters WHERE player_id=$arr[player2_id]" );
			$carr = f_MFetch( $cres );
			$ret .= "<li><a href='javascript:act(2)'>���������� �� ���� � ������� $carr[0]</a><br>"; 
			$ret .= "<li><a href='javascript:act(3)'>������ ���� � ������� $carr[0]</a><br>"; 
		}
		$ret .= "<li><a href='javascript:ref()'>��������</a><br>";
		return $ret;
	}
	else return "<li><a href='javascript:act(1)'>�������� ������</a><br><li><a href='javascript:ref()'>��������</a><br>";
}

function getBets( $game_id )
{
	global $player;
	$uid = $player->player_id;
	$res = f_MQuery( "SELECT count( game_id ) FROM waste_bets WHERE player1_id = $uid OR player2_id = $uid" );
	$arr = f_MFetch( $res );
	$can_bet = true;
	if( $arr[0] ) $can_bet = false;

	$res = f_MQuery( "SELECT * FROM waste_bets WHERE game_id=$game_id ORDER BY timestamp DESC" );
	if( !f_MNum( $res ) && $game_id != 3 ) return "<i>��� �� ����� ������ �� ����</i>";
	$ret = "<table><tr><td>' + rFLUl() + '<table>";

	if( $game_id == 3 ) // glash
	{                                                          
		$plr1 = new Player( 69055 );
		$ret .= "<tr><td height=100% width=195>' + rFUcm() + ".$plr1->Nick()." + rFL() + '</td>";
		$ret .= "<td width=50>' + rFUcm() + '<img width=11 height=11 src=images/money.gif> $arr[money]' + rFL() + '</td>";
			if( $can_bet ) $ret .= "<td height=100% width=195>' + rFUcm() + '<a href=javascript:act2(100,0)>������� ������</a>' + rFL() + '</td>";
			else $ret .= "<td height=100% width=195>' + rFUcm() + '&nbsp;' + rFL() + '</td>";
		$ret .= "</tr>";

	}

	while( $arr = f_MFetch( $res ) )
	{
		$plr1 = new Player( $arr['player1_id'] );
		$ret .= "<tr><td height=100% width=195>' + rFUcm() + ".$plr1->Nick()." + rFL() + '</td>";
		$ret .= "<td width=50>' + rFUcm() + '<img width=11 height=11 src=images/money.gif> $arr[money]' + rFL() + '</td>";
		if( $arr['player2_id'] == -1 )
		{
			if( $can_bet ) $ret .= "<td height=100% width=195>' + rFUcm() + '<a href=javascript:act2(4,$arr[player1_id])>������� ������</a>' + rFL() + '</td>";
			else $ret .= "<td height=100% width=195>' + rFUcm() + '&nbsp;' + rFL() + '</td>";
		}
		else
		{
    		$plr2 = new Player( $arr['player2_id'] );
    		$ret .= "<td height=100% width=195>' + rFUcm() + ".$plr2->Nick()." + rFL() + '</td>";
		}
		$ret .= "</tr>";
	}
	$ret .= "</table>' + rFLL() + '</td></tr></table>";
	return $ret;
}

$game_descrs = Array(
	"����� - ������������� ��������� ����. ������ ���� - ��������� ���� ������ ����� ������, ��� ���� ��������� ���. ��� ���������� ������������ ���� ���� ��������� � ���������� ���� ����, ������� � ����. ������� ������������ ���������� ���� ��������� �� ��������� ����������, ����������� ���� ������ ��� ����������� ������ �����. ��� ������ ������ ���� ���� ���, ���������, ����������, �� ������, � ��� �� �� ����������.",
	"",
	"",
	"5 � ��� - ����, ���������� �� ���� �������� 20 �� 20. ���� ������� ������ ���� ������ �����: ���� ����� ����������, ������ - ��������. �� ������ ���� ����� ������ ������� ��� ����� �� ���� �� ��������� ������ ����. �����, ����������� ���� ����� ������ � ��� �� �����������, ��������� ��� ���������, ����������."
);

?>
