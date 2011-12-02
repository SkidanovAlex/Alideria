<?

include_once( 'locations/newarena/func.php' );

if( !isset( $mid_php ) ) die( );

if( isset( $_GET['act'] ) )
{
	$action = $_GET['act'];
	$arg1 = (int)$_GET['arg1'];
	$arg2 = (int)$_GET['arg2'];
	$arg3 = (int)$_GET['arg3'];
	$arg4 = (int)$_GET['arg4'];
	$arg5 = (int)$_GET['arg5'];
	
	if( $action == "duelCreateBet" )
	{
		if( $arg1 < 0 || $arg1 >= count( $timeouts ) ) die( );
		$timeout = $arg1;
		
		$min_level = $arg2; if( $min_level < 1 ) die( "alert('Минимальный уровень не может быть меньше 1');" );
		$max_level = $arg3; if( $max_level > 25 ) die( "alert('Максимальный уровень не может быть больше 25');" );
		if( $min_level > $max_level ) die( "alert('Минимальный уровень не может быть больше максимального');" );
		
		if( $player->regime == 0 )
		{
			$player->SetRegime( 300 );
			f_MQuery( "INSERT INTO newarena_duel_bets( p1_id, timeout, min_level, max_level, date ) VALUES ( {$player->player_id}, $timeout, $min_level, $max_level, ".time( )." )" );
		}
	}
	if( $action == "cancelDuelBet" )
	{
		if( $player->regime == 300 )
		{
			$opponent = f_MValue( "SELECT p2_id FROM newarena_duel_bets WHERE p1_id = {$player->player_id}" );
			if( $opponent ) // мы - автор заявки
			{
				$plr = new Player( $opponent );
				f_MQuery( "DELETE FROM newarena_duel_bets WHERE p1_id={$player->player_id}" );
				$plr->SetRegime( 0 );
				$player->SetRegime( 0 );
			}
			else // мы приняли заявку
			{
				f_MQuery( "UPDATE newarena_duel_bets SET p2_id=-1 WHERE p2_id = {$player->player_id}" );
				$player->SetRegime( 0 );
			}
		}
	}
	if( $action == "refuseOpponent" )
	{
		$opponent = f_MValue( "SELECT p2_id FROM newarena_duel_bets WHERE p1_id = {$player->player_id}" );
		if( $opponent ) // мы - автор заявки
		{
			$plr = new Player( $opponent );
			f_MQuery( "UPDATE newarena_duel_bets SET p2_id=-1 WHERE p2_id = {$opponent}" );
			$plr->SetRegime( 0 );
		}
	}
}

$arena_regime = (int)$_GET['a'];

if( $arena_regime < 0 ) $arena_regime = 0;
if( $arena_regime > 3 ) $arena_regime = 3;

if( $player->regime >= 300 && $player->regime <= 310 ) $arena_regime = $player->regime - 300;

$s = str_replace( "{{{", "' + rFLUl( ) + '", str_replace( "}}}", "' + rFLL( ) + '", addslashes( render_arena( $arena_regime ) ) ) );
$s = str_replace( "`", "'", $s );

echo "_( 'arena_holder' ).innerHTML = '".$s."';";
echo "curRegime = {$arena_regime};";
if( $player->regime != 0 ) echo "arenaScheduleRef( );";

?>
