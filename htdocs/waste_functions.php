<?

function getActions( $game_id )
{
	global $player;
	$res = f_MQuery( "SELECT player1_id, player2_id FROM waste_bets WHERE player1_id = {$player->player_id} OR player2_id = {$player->player_id}" );
	$arr = f_MFetch( $res );

	if( !$arr ) 
	{
		if( $player->level == 1 ) return "<li><a href='javascript:act2(0, 0)'>Подать заявку</a><br><li><a href='javascript:ref()'>Обновить</a><br>";
		else return "<li><a href='javascript:act2(0, document.getElementById( \"stv\" ).value)'>Подать заявку</a>. <input id=stv type=hidden value=0><li><a href='javascript:ref()'>Обновить</a><br>";// Ставка: <input class=btn40 maxlength=5 id=stv value=0> <img src=images/money.gif width=11 height=11><br>";
	}
	else if( $arr['player1_id'] == $player->player_id )
	{
		$ret = "<li><a href='javascript:act(1)'>Отозвать заявку</a><br>";
		if( $arr['player2_id'] != -1 )
		{
			$cres = f_MQuery( "SELECT login FROM characters WHERE player_id=$arr[player2_id]" );
			$carr = f_MFetch( $cres );
			$ret .= "<li><a href='javascript:act(2)'>Отказаться от игры с игроком $carr[0]</a><br>"; 
			$ret .= "<li><a href='javascript:act(3)'>Начать игру с игроком $carr[0]</a><br>"; 
		}
		$ret .= "<li><a href='javascript:ref()'>Обновить</a><br>";
		return $ret;
	}
	else return "<li><a href='javascript:act(1)'>Отозвать заявку</a><br><li><a href='javascript:ref()'>Обновить</a><br>";
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
	if( !f_MNum( $res ) && $game_id != 3 ) return "<i>Нет ни одной заявки на игру</i>";
	$ret = "<table><tr><td>' + rFLUl() + '<table>";

	if( $game_id == 3 ) // glash
	{                                                          
		$plr1 = new Player( 69055 );
		$ret .= "<tr><td height=100% width=195>' + rFUcm() + ".$plr1->Nick()." + rFL() + '</td>";
		$ret .= "<td width=50>' + rFUcm() + '<img width=11 height=11 src=images/money.gif> $arr[money]' + rFL() + '</td>";
			if( $can_bet ) $ret .= "<td height=100% width=195>' + rFUcm() + '<a href=javascript:act2(100,0)>Принять заявку</a>' + rFL() + '</td>";
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
			if( $can_bet ) $ret .= "<td height=100% width=195>' + rFUcm() + '<a href=javascript:act2(4,$arr[player1_id])>Принять заявку</a>' + rFL() + '</td>";
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
	"Магия - захватывающая карточная игра. Задача мага - вырастить свое дерево жизни раньше, чем враг уничтожит его. Для достижения поставленной цели маги прибегают к источникам маны Огня, Природы и Воды. Наличие достаточного количества маны позволяет им колдовать заклинания, укрепляющие свое дерево или разрушающие дерево врага. Для защиты дерева маги роют ров, используя, разумеется, не лопату, а все те же заклинания.",
	"",
	"",
	"5 в Ряд - игра, проходящая на поле размером 20 на 20. Двое игроков играют друг против друга: один ходит крестиками, другой - ноликами. На каждом ходу игрок ставит крестик или нолик на одну из свободных клеток поля. Игрок, выстроивший пять своих знаков в ряд по горизонтали, вертикали или диагонали, выигрывает."
);

?>
