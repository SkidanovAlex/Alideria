<?

if( !$mid_php ) die( );

echo "<b>Состав Ордена</b> - <a href=game.php?order=main>Назад</a><br>";

$ranks = Array( );
$jobs = Array( 0 => "---" );
$res = f_MQuery( "SELECT rank, name FROM clan_ranks WHERE clan_id=$clan_id ORDER BY rank" );
while( $arr = f_MFetch( $res ) ) $ranks[$arr[0]] = $arr[1];
$res = f_MQuery( "SELECT job, name FROM clan_jobs WHERE clan_id=$clan_id ORDER BY job" );
while( $arr = f_MFetch( $res ) ) $jobs[$arr[0]] = $arr[1];

$cancontrol = ( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_CONTROL ) );
$candismiss = ( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_DISMISS ) );

if( $candismiss && isset( $_GET['dismiss'] ) )
{
	$id = $_GET['dismiss'];
	settype( $id, 'integer' );
	$vres = f_MQuery( "SELECT count( player_id ) FROM player_clans WHERE clan_id=$clan_id AND player_id=$id AND job <> 1000" );
	$varr = f_MFetch( $vres );

	if( $varr[0] && playerSpendControlPoint( $clan_id, $player->player_id ) )
	{
		f_MQuery( "DELETE FROM player_clans WHERE clan_id=$clan_id AND player_id=$id" );
		f_MQuery( "DELETE FROM player_clan_items WHERE player_id=$id" );
		f_MQuery( "UPDATE characters SET clan_id=0 WHERE player_id=$id" );
		f_MQuery( "UPDATE characters SET regime=0, go_till=0 WHERE player_id=$id AND loc=2 AND depth=19" );
		f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0, arg1 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 10, $id, 2 )" );

		$plr = new Player( $id );
		$res = f_MQuery( "SELECT wonder_id FROM clan_wonders WHERE clan_id=$clan_id AND stage=100" );
		while( $arr = f_MFetch( $res ) )
			applyWonder( $arr[0], $plr, 0 );
		$plr->UploadInfoToJavaServer( );
		orderBroadcast( $clan_id, "Игрок <b>{$plr->login}</b> был отчислен из вашего Ордена" );
		$plr->syst3("Вы были отчислены из Ордена.");
		f_MQuery("DELETE FROM paid_smiles WHERE set_id>10000 AND player_id=".$plr->player_id);
		$plr->RemoveEffect(10, true); //удаляем эффекты Древа Жизни
	}
}

$res = f_MQuery( "SELECT * FROM player_clans WHERE clan_id=$clan_id ORDER BY rank DESC, job DESC" );

if( $cancontrol && isset( $_GET['control'] ) )
{
	$player_id = $_GET['control'];
	settype( $player_id, 'integer' );

	$res = f_MQuery( "SELECT * FROM player_clans WHERE clan_id=$clan_id AND player_id=$player_id" );
	$arr = f_MFetch( $res );

	if( !$arr ) echo "<i>У вас нет в Ордене выбранного игрока</i>";
	else
	{
		if( isset( $_POST['rank'] ) )
		{
			$rank = $_POST['rank'];
			$job = $_POST['job'];
			$points = $_POST['points'];
			$balance = $_POST['balance'];

			settype( $rank, 'integer' );
			settype( $job, 'integer' );
			settype( $points, 'integer' );
			settype( $balance, 'integer' );

			if( !isset( $ranks[$rank] ) ) echo "<font color=darkred>Вы пытаетесь установить несуществующее звание</font><br>";
			else if( !isset( $jobs[$job] ) ) echo "<font color=darkred>Вы пытаетесь установить несуществующую должность</font><br>";
			else if( $arr['job'] == 1000 && $job != 1000 ) echo "<font color=darkred>Нельзя менять должность главе</font><br>";
			else if( $arr['job'] != 1000 && $job == 1000 ) echo "<font color=darkred>Нельзя установить должность Глава</font><br>";
			else if( $points != $arr['control_points'] && getControlPoints( $player->player_id ) >= 0 ) echo "<font color=darkred>Нельзя менять очки управления, если вы не обладаете бесконечным количеством очков управления</font><br>";
			else if( $job == 1000 && $points != -1 ) echo "<font color=darkred>Глава обязан иметь бесконечное количество очков управления</font><br>";
			else if( playerSpendControlPoint( $clan_id, $player->player_id ) )
			{
				f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0, arg1, arg2, arg3, arg4 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 5, $player_id, $rank, $job, $balance, $points )" );

				// Надо отдельно рассмотреть два случая, иначе контрол поинт не тратится
				if( $points != $arr['control_points'] ) f_MQuery( "UPDATE player_clans SET rank=$rank, job=$job, balance=$balance, control_points = $points WHERE clan_id=$clan_id AND player_id=$player_id" );
				else f_MQuery( "UPDATE player_clans SET rank=$rank, job=$job, balance=$balance WHERE clan_id=$clan_id AND player_id=$player_id" );
				$res = f_MQuery( "SELECT * FROM player_clans WHERE clan_id=$clan_id AND player_id=$player_id" );
				$arr = f_MFetch( $res );
			}

		}

		$plr = new Player( $player_id );
		echo "<table><tr><td valign=top width=50%>Управление игроком <script>document.write( ".( $plr->Nick( ) )." );</script> - <a href=game.php?order=barracks>Назад</a><br>";
		echo "<br>";
		echo "<form action=game.php?order=barracks&control=$player_id method=post><table>";
		echo "<tr><td align=right>Звание: </td><td>".create_select_global( 'rank', $ranks, $arr['rank'] )."</td></tr>";
		echo "<tr><td align=right>Должность: </td><td>".create_select_global( 'job', $jobs, $arr['job'] )."</td></tr>";
		echo "<tr><td align=right valign=top>Очки Управления: </td><td><input class=m_btn type=text name=points value=$arr[control_points]><br><small>-1 - бесконечное количество</small></td></tr>";
		echo "<tr><td align=right valign=top>Баланс: </td><td><input class=m_btn type=text name=balance value=$arr[balance]></td></tr>";
		echo "<tr><td>&nbsp;</td><td><input class=s_btn type=submit value='Изменить'></td></tr>";
		echo "</table></form></td>";

		echo "<td valign=top width=50%>Права игрока<br><small>Права являются совокупностью прав должности и звания игрока. Менять права напрямую игроку нельзя<br>";

		outControlsList( getPlayerPermitions( $clan_id, $player_id ) );
		echo "<script>peReadOnly = true;</script>";

		echo "</td></tr></table>";
	}
}
else
{
    $control_cols = '';
    if( $cancontrol )
    	$control_cols = '<td align=center><b>ОУ</b></td><td>&nbsp;</td></tr>';
    if( $candismiss )
    	$control_cols .= '<td>&nbsp;</td></tr>';

    $num = 1;
    $content = "<table><tr><td width=200 align=center><b>Игрок</b></td><td width=100 align=center><b>Звание</b></td><td width=100 align=center><b>Должность</b></td><td width=50 align=center><b>Баланс</b></td>$control_cols</tr><script src=js/clans.php></script><script src=js/ii.js></script><script>";
    while( $arr = f_MFetch( $res ) )
    {
    	$rank = $ranks[$arr['rank']];
    	$job = $jobs[$arr['job']];
    	$plr = new Player( $arr['player_id'] );
    	if( $arr['control_points'] == -1 ) $arr['control_points'] = "<font color=green>безлим</font>";
    	else if( $arr['control_points'] == 0 ) $arr['control_points'] = "<font color=red>0</font>";
    	if( $arr['balance'] > 0 ) $arr['balance'] = "<font color=green>$arr[balance]</font>";
    	if( $arr['balance'] < 0 ) $arr['balance'] = "<font color=red>$arr[balance]</font>";

    	$moo = '';
    	if( $cancontrol ) $moo = "<td align=center>$arr[control_points]</td><td><a href=game.php?order=barracks&control=$arr[player_id]>Управление</a></td>";
    	if( $candismiss ) $moo .= "<td><a href=\"#\" onclick=\\'if( confirm( \"Отчислить игрока {$plr->login}?\" ) ) location.href=\"game.php?order=barracks&dismiss=$arr[player_id]\"\\'>Отчислить</a></td>";
    	$content .= "document.write( '<tr><td><b>$num. </b>' + ".$plr->Nick( )." + '</td><td align=center>$rank</td><td align=center>$job</td><td align=center>$arr[balance]</td>$moo</tr>' );\n";
    	++ $num;
    }
    $content .= "</script></table>";
                  
    echo "<table><tr><td><script>FLUl();</script>".$content."<script>FLL();</script></td></tr></table>";
}

?>
