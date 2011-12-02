<?php

function DrawBetSelect( $text, $add_update, $before_text = '' )
{
	?>
	<script>
	var min_bet = 1;
	var max_bet = 100;
	var current_bet = 1;

	function AddBet( a )
	{
		current_bet += a;
		if ( current_bet < min_bet )
			current_bet = min_bet;
		else
		if ( current_bet > max_bet )
			current_bet = max_bet;
		UpdateBet( );
	}

	function UpdateBet( )
	{
		<? echo "$add_update"; ?>
	}

	function SetMinMax( minbet, maxbet )
	{
		min_bet = minbet;
		max_bet = maxbet;
		_( 'ToMin' ).innerHTML = "<a href='javascript:AddBet( -1000000 )'>Минимум (" + min_bet + ")</a>";
		AddBet( 0 );
	}

	function ShowBetSelect( )
	{
		_( 'BetSelect' ).style.display = '';
	}

	function HideBetSelect( )
	{
		_( 'BetSelect' ).style.display = 'none';
	}

	</script>
    <div id='BetSelect' style='display:none;'>
    <?php
    	$vals = array( 1, 5, 10, 50, 100, 500, 1000 );
    	for ($i = 0; $i < count( $vals ); ++ $i )
    	{
    		echo "<a href='javascript:AddBet( {$vals[$i]} )'><img src=images/poker/bets/{$i}.png></a> ";
    	}
    ?>
    <br/>
	<?php echo "$before_text"; ?>
	<table border=0 cellpadding=0>
	<tr height=35px valign=center align=center>
	<td id='BetText' width=136px style="background-image:url('images/poker/button3.png');">
	<?php
     echo "$text";
    ?>
	</td></tr></table>

	<table border=0 cellpadding=0>
	<tr height=35px valign=center align=center>
	<td id='ToMin' width=136px style="background-image:url('images/poker/button3.png');">
	</td></tr></table>
    </div>
	<?php
}

function getPokerBets( )
{
	global $player;
	$uid = $player->player_id;
	$res = f_MQuery( "SELECT table_id, player_id, money FROM poker_table_players ORDER by table_id" );
	$tables = array( );
	$tables_ids = array( );
	$table_money = array( );
	while ( $arr = f_MFetch( $res ) )
	{
		$t_id = $arr[0];
		if ( !isset( $tables[$t_id] ) )
			$tables[$t_id] = array( );
		$tables[$t_id][] = $arr[1];
		$table_money[$t_id][] = $arr[2];
		$tables_ids[] = $t_id;
	}

	if ( count( $tables ) == 0 )
	{
		return "<i>Нет ни одной заявки на игру</i>";
    }
	$list_ids = '(' . implode( ',', $tables_ids ) . ')';
	$res = f_MQuery( "SELECT table_id, small_blind, start_money FROM poker_table_stats WHERE table_id in $list_ids" );
	$table_blinds = array( );
	$table_smoney = array( );
	while ( $arr = f_MFetch( $res ) )
	{
		$table_blinds[$arr[0]] = $arr[1];
		$table_smoney[$arr[0]] = $arr[2];
	}

	$ret = "<table>";
	$babos = '<img src=images/money.gif>';
	foreach ( $tables as $t_id => $pl_list )
	{
		//$ret .= "<tr><td></tr></td>";
		if ( count( $pl_list ) < 6 )
			$ret .= "<tr><td><a href=\"javascript:JoinTable($t_id)\" >Присоединиться, к столу номер $t_id </a>  </td></tr>";
		$small_blind = $table_blinds[$t_id];
		$big_blind = $small_blind * 2;
		$start_money = $table_smoney[$t_id];

		$ret .= "<tr><td>Ставки: {$small_blind}$babos/{$big_blind}$babos</td></tr><tr><td>Начальный закуп: {$start_money}$babos</td></tr>";
		$ret .= "<tr><td>' + rFLUl() + '<table>";
		foreach( $pl_list as $tmp => $player_id )
		{
			$plr1 = new Player( $player_id );
			$ret .= "<tr><td height=100% width=195>' + rFUcm() + ".$plr1->Nick()." + rFL() + '</td>";
			$ret .= "<td width=50>' + rFUcm() + '{$table_money[$t_id][$tmp]}$babos' + rFL() + '</td></tr>";
		}
		$ret .= "</table>' + rFLL() + '<br></td></tr>";
	}
	$ret .= "</table>";
	return $ret;
}

function check_ingame( )
{
	global $player;
	$uid = $player->player_id;
	$res = f_MQuery( "SELECT 1 FROM poker_table_players WHERE player_id = '{$uid}' LIMIT 1" );
	if ( f_MNum( $res ) )
		return true;
	else
		return false;
}

function get_player_table( )
{
	global $player;
	$uid = $player->player_id;
	$res = f_MQuery( "SELECT table_id FROM poker_table_players WHERE player_id = '{$uid}' LIMIT 1" );
	if ( $row = f_MFetch( $res ) )
		return (int)$row[0];
	else
		return -1;
}

function create_new_table( $start_money )
{
	global $player;
	if ( $start_money < 50 || $start_money > $player->money )
		return -1;
	$uid = $player->player_id;
	$small_blind = round( $start_money / 50 );
	if ( $small_blind <= 0 )
		$small_blind = 1;
	f_MQuery( "LOCK TABLE poker_table_stats WRITE;" );
	$res = f_MQuery( "SELECT IFNULL( max( table_id ) + 1, 1 ) from poker_table_stats;" );
	if ( $arr = f_MFetch( $res ) )
		$table_id = (int)$arr[0];
	else
		$table_id = 1;
	$tm = time( );
	f_MQuery( "INSERT INTO poker_table_stats ( table_id, creater, create_timestamp, start_money, small_blind ) VALUES ( '$table_id', '$uid', '$tm', '$start_money', '$small_blind' );" );
	f_MQuery( "UNLOCK TABLES" );
	return $table_id;
}

function print_functions( )
{
// а теперь функции на JS
?>
<script>
	function JoinTable( t_id )
	{
		query( 'poker_ajax.php', "join " + t_id );
	}
	function LeaveTable( )
	{
		query( 'poker_ajax.php', "leave " );
	}
	var timer_id = 0;
	function ref( )
	{
		if ( timer_id > 0 )
			clearTimeout( timer_id );
		timer_id = setTimeout( 'ref( );', 5000 );
		query( 'poker_ajax.php', 'refresh ' );
	}

</script>
<?php
}

?>