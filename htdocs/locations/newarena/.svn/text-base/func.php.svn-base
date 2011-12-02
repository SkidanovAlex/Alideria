<?

$arena_regimes = array( 'Дуэли', "Групповые", "Хаотичные", "Зачарованные", "Обновить" );
$timeouts = array( 30, 60, 90, 120, 180, 300, 600 );
$timeout_strs = array( "30 сек", "1 мин", "1.5 мин", "2 мин", "3 мин", "5 мин", "10 мин" );

function render_duels( )
{
	global $player;
	global $timeout_strs;
	
	if( $player->regime == 300 )
	{
		$ret = '';
		$ret .= "<table width=400><tr><td>{{{<center>";
		$ret .= "<b>Ваша текущая заявка:</b><br><br>";
		$arr = f_MFetch( f_MQuery( "SELECT * FROM newarena_duel_bets WHERE p1_id = {$player->player_id} OR p2_id = {$player->player_id}" ) );
		if( !$arr )
		{
			$player->SetRegime( 0 );
			RaiseError( "У игрока режим заявки на дуэль, но заявки нет в БД" );
		}
		
		$ret .= "<table>";
		$plr1 = new Player( $arr['p1_id'] );
		if( $arr['p2_id'] != -1 ) $plr2 = new Player( $arr['p2_id'] );
		$ret .= "<tr><td>Автор:</td><td>` + ".str_replace( "'", "`", $plr1->Nick() )." + `</td></tr>";
		$ret .= "<tr><td>Таймаут:</td><td><b>".$timeout_strs[$arr['timeout']]."</b></td></tr>";
		$ret .= "<tr><td>Мин. Уровень:</td><td><b>".$arr['min_level']."</b></td></tr>";
		$ret .= "<tr><td>Макс. Уровень:</td><td><b>".$arr['max_level']."</b></td></tr>";
		$ret .= "<tr><td>Оппонент:</td><td>".((-1 == $arr['p2_id'])?'<i>Ожидаем</i>':"` + ".str_replace( "'", "`", $plr2->Nick() )." + `")."</td></tr>";
		$ret .= "<tr><td colspan='2'>&nbsp;</td></tr>";
		if( $arr['p2_id'] != -1 && $arr['p1_id'] == $player->player_id )
		{
			$ret .= "<tr><td colspan='2' align='center'><a href='javascript:arenaAction(\"startDuel\",0,0,0,0,0)'>Запустить бой</a></td></tr>";
			$ret .= "<tr><td colspan='2' align='center'><a href='javascript:arenaAction(\"refuseOpponent\",0,0,0,0,0)'>Отклонить оппонента</a></td></tr>";
		}
		$ret .= "<tr><td colspan='2' align='center'><a href='javascript:arenaAction(\"cancelDuelBet\",0,0,0,0,0)'>Отозвать заявку</a></td></tr>";
		$ret .= "</table>";
		
		$ret .= "</center>}}}</td></tr></table>";
		return $ret;
	}
	
	$ret = '';
	
	$ret .= "<table><tr>";
	
		$ret .= "<td vAlign='top' width='200'>{{{<center><b>Подать заявку</b><br><br>";
		
		$ret .= "<small><b>Таймаут</b></small><br><select class='te_btn' id='tmo'><option value=0>30 сек<option value=1>1 мин<option value=2>1.5 мин<option value=3>2 мин<option value=4>3 мин<option value=5>5 мин<option value=6>10 мин</select><br>";
		$ret .= "<br><small><b>Уровени:</b></small><br>";
		$ret .= "<input type='text' value='{$player->level}' id=min_lvl class='te_btn' style='text-align: center;width:40px;'> &ndash; <input type='text' value='{$player->level}' id=max_lvl class='te_btn' style='text-align: center;width:40px;'><br>";
		$ret .= "<br><button onclick='arenaAction(\"duelCreateBet\",_(\"tmo\").selectedIndex,_(\"min_lvl\").value,_(\"max_lvl\").value,0,0)' class='ss_btn'>Подать</button>";
		$ret .= "</center>";
		
		/*$ret .= "<br><table>";
		$ret .= "<tr><td>Таймаут:</td><td><select id='tmo'><option value=0>30 сек<option value=1>1 мин<option value=2>1.5 мин<option value=3>2 мин<option value=4>3 мин<option value=5>5 мин<option value=6>10 мин</select></td></tr>";
		$ret .= "<tr><td>Мин. уровеь:</td><td><input type='text' value='{$player->level}' id=min_lvl class='te_btn' style='width:80px;'></td></tr>";
		$ret .= "<tr><td>Макс. уровеь:</td><td><input type='text' value='{$player->level}' id=max_lvl class='te_btn' style='width:80px;'></td></tr>";
		$ret .= "<tr><td>&nbsp;</td><td><button class='ss_btn'>Подать</button></td></tr>";
		$ret .= "</table>";
		*/
		$ret .= "}}}</td><td vAlign='top' width='500'>{{{<b>Текущие заявки</b>";
		
		$ret .= "}}}</td>";
	
	$ret .= "</tr></table>";
	
	return $ret;
}

function render_arena( $arena_regime )
{
	global $arena_regimes;
	$ret = '<center>';

    $ret .= "<br>";
    $ret .= ( "<table cellspacing=0 cellpadding=0 border=0><tr>" );

    foreach( $arena_regimes as $a=>$b )
    {
    	if( $a == 4 )
    	{
    		$ret .= ( "<td><img border=0 width=17 height=21 src=images/top/c.png></td>" );
    		$ret .= ( "<td><img border=0 width=17 height=21 src=images/top/b.png></td>" );
    	}
    	else if( $a ) $ret .= ( "<td><img border=0 width=17 height=21 src=images/top/d.png></td>" );
    	else $ret .= ( "<td><img border=0 width=17 height=21 src=images/top/b.png></td>" );
    	$ret .= ( "<td width=92 height=21 background=images/top/f.png align=center valign=middle>" );
    	if( $a == $arena_regime )
    		$ret .= ( "<b>$b</b>" );
    	else $ret .= ( "<a href='javascript:arenaRegime($a)'>$b</a>" );
    	$ret .= ( "</td>" );
    }

    $ret .= ( "<td><img border=0 width=17 height=21 src=images/top/c.png></td></tr></table>" );
    $ret .= "<br>";
    
    if( $arena_regime == 0 ) $ret .= render_duels( );
    
    $ret .= "</center>";

	return $ret;
}

?>
