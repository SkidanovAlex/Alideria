<?

/*function getTournamentDesc( $arr )
{
	global $player;

	$st = GetScrollLightTableStart( );
	$st .= "<table width=300><tr><td>";
	$st .= GetScrollTableStart( "center" );
	$st .= "<b>Турнир \"$arr[name]\"</b>";
	$st .= GetScrollTableEnd( );
	$st .= "</td></tr><tr><td>";
	$st .= GetScrollTableStart( "left" );
	$st .= "<table width=100%>";
	$types = array( '<font color=green>боевой</font>', '<font color=navy>магия</font>', '<font color=darkred>орденский</font>');
	$st .= "<tr><td>Тип:</td><td align=right><b>{$types[$arr[type]]}</b></td></tr>";
	$st .= "<tr><td>Дата проведения:</td><td align=right><b>".date( "d.m.Y", $arr['date'] )."</b></td></tr>";
	$st .= "<tr><td>Время начала:</td><td align=right><b>".date( "H:i", $arr['date'] )."</b></td></tr>";
	$st .= "<tr><td>Минимальный уровень:</td><td align=right><b>$arr[min_level]</b></td></tr>";
	$st .= "<tr><td>Максимальный уровень:</td><td align=right><b>$arr[max_level]</b></td></tr>";
	$st .= "<tr><td>Призовой фонд:</td><td align=right><img width=11 height=11 border=0 src=images/money.gif> <b>$arr[prize]</b></td></tr>";
	if( $arr['type'] != 2 ) $rres = f_MQuery( "SELECT count( player_id ) FROM tournament_players WHERE tournament_id=$arr[tournament_id]" );
	else $rres = f_MQuery( "SELECT count( bet_id ) FROM tournament_group_bets WHERE tournament_id=$arr[tournament_id]" );
	$rarr = f_MFetch( $rres );
	if( $arr['type'] != 2 ) $st .= "<tr><td><a href='javascript:void(0)' onclick='tp($arr[tournament_id])'>Участники</a>:</td><td align=right><b>$rarr[0]</b></td></tr>";
	else $st .= "<tr><td><a href='javascript:void(0)' onclick='tp($arr[tournament_id])'>Команды</a>:</td><td align=right><b>$rarr[0]</b></td></tr>";

	if( $arr['status'] == 4 ) $st .= "<tr><td colspan=2 align=center><a href='/tournament_net.php?id=$arr[tournament_id]' target=_blank>Текущие результаты</a></td></tr>";
	else if( $arr['date'] - $tm < 300 ) $st .= "<tr><td colspan=2 align=center><i>Запись на турнир завершена</i></td></tr>";
	else if( $arr['min_level']  > $player->level || $arr['max_level']  < $player->level ) $st .= "<tr><td colspan=2 align=center><i>Вы не подходите по уровню для участия в этом турнире</i></td></tr>";
	elseif( !f_MValue( "SELECT * FROM `tournament_players` WHERE `player_id` = {$player->player_id} AND `tournament_id` = $arr[tournament_id]" ) )
	{
		// Если не записывался ещё
		$st .= "<tr><td colspan=2 align=center><a href='javascript:void(0)' onclick='query(\"tournament_signup.php\", \"$arr[tournament_id]\")'>Записаться</a></td></tr>";
	}
	else
	{
		// Если уже записался
		$st .= "<tr><td colspan=2 align=center><a href='javascript:void(0)' onclick='query(\"tournament_signup.php\", \"$arr[tournament_id]\")'>Отказаться от участия</a></td></tr>";
	}
	$st .= "</table>";
	$st .= GetScrollTableEnd( );
	$st .= "</td></tr></table>";
	$st .= GetScrollLightTableEnd( );
	return $st;
}*/

function getTournamentDesc( $arr )
{
	global $player;

	$types = array( '<font color=green>боевой</font>', '<font color=navy>магия</font>', '<font color=darkred>ордена</font>');
	$month = array( 'неведомаря', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря' );

	$st = GetScrollLightTableStart( "left"  );
	$st .= "<table width=\"100%\"><tr>";
	$st .= "<td style=\"width: 300px;\"><b>$arr[name]</b></td>";
	$st .= "<td style=\"width: 50px;\"><b>{$types[$arr[type]]}</b></td>";
	$st .= "<td style=\"width: 130px;\"><b>".date( 'd ', $arr['date'] ).$month[(int)date( 'm', $arr['date'] )].date( ' в H:i', $arr['date'] )."</b></td>";
	$st .= "<td style=\"width: 40px;\"><b>$arr[min_level]".( ( $arr[min_level] != $arr[max_level] ) ? " - $arr[max_level]" : '' ).'</b></td>';
	$st .= "<td style=\"width: 70px;\"><img width=11 height=11 border=0 src=images/money.gif> <b>$arr[prize]</b></td>";
	if( $arr['type'] != 2 ) $rres = f_MQuery( "SELECT count( player_id ) FROM tournament_players WHERE tournament_id=$arr[tournament_id]" );
	else $rres = f_MQuery( "SELECT count( bet_id ) FROM tournament_group_bets WHERE tournament_id=$arr[tournament_id]" );
	$rarr = f_MFetch( $rres );
	if( $arr['type'] != 2 ) $st .= "<td style=\"width: 25px;\"><a href='javascript:void(0)' onclick='tp($arr[tournament_id])' title='Участники'><b>$rarr[0]</b></a></td>";
	else $st .= "<td style=\"width: 25px;\"><a href='javascript:void(0)' onclick='tp($arr[tournament_id])' title='Команды'><b>$rarr[0]</b></a></td>";

	if( $arr['status'] == 4 ) $st .= "<td><a href='/tournament_net.php?id=$arr[tournament_id]' target=_blank><b>Результаты</a></b></td>";
	else if( $arr['date'] - $tm < 300 ) $st .= "<td>&nbsp;</td>"; // Записаться уже нельзя, осталось 5 минут до турнира
	else if( $arr['min_level']  > $player->level || $arr['max_level']  < $player->level ) $st .= "<td>&nbsp;</td>"; // Не прошёл по уровню
	elseif( !f_MValue( "SELECT * FROM `tournament_players` WHERE `player_id` = {$player->player_id} AND `tournament_id` = $arr[tournament_id]" ) )
	{
		// Если не записывался ещё
		$st .= "<td><a href='javascript:void(0)' onclick='query(\"tournament_signup.php\", \"$arr[tournament_id]\")'>Записаться</a></td>";
	}
	else
	{
		// Если уже записался
		$st .= "<td><a href='javascript:void(0)' onclick='query(\"tournament_signup.php\", \"$arr[tournament_id]\")'>Отказаться</a></td>";
	}
	$st .= "</tr></table>";
	$st .= GetScrollLightTableEnd( );
	return $st;
}

function PlayerLeaveTournament( $pid, $tid )
{
	$res = f_MQuery( "SELECT max_level FROM tournament_announcements WHERE tournament_id=$tid" );
	$arr = f_MFetch( $res );

	$plr = new Player( $pid );

	if( $plr->level < $arr[0] )
	{
		$plr->AlterRealAttrib( 101, - ( $arr[0] - $plr->level ) * 60 );
	}

	f_MQuery( "DELETE FROM tournament_busy_players WHERE player_id=$pid" );
}

?>
