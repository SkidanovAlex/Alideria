<?
header("Content-type: text/html; charset=windows-1251");


include_once( "functions.php" );
include_once( "combat_interface.php" );
include_once( 'player_noobs.php' );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$a = $HTTP_GET_VARS['id'];
settype( $a, "integer" );

$turn = $_GET['turn'];
settype( $turn, 'integer' );

$force_ai_card = -1;

$pid = $HTTP_COOKIE_VARS['c_id'];
$level = f_MValue( "SELECT level FROM characters WHERE player_id=$pid" );
if( $level == 1 )
{
	include_once( "player.php" );
	$player = new Player( $pid );
	PingNoob( 7 );

	// new noob
    $res = f_MQuery( "SELECT a, b FROM noob WHERE player_id={$pid}" );
    $arr = f_MFetch( $res );
    if( $arr ) { $noob = $arr[0]; $noob_param = $arr[1]; }
    if( $noob == 21 )
    {
    	if( $a != 57 ) die( "alert( 'Выбранное тобой заклинание тоже очень сильное, но сейчас лучше сколдовать то, что советует Астаниэль.' );" );
    	else echo "follow(21);";
    	$force_ai_card = 127;
    }
    else if( $noob == 27 )
    {
    	if( $a != 56 ) die( "alert( 'Выбранное тобой заклинание тоже очень сильное, но сейчас лучше сколдовать то, что советует Астаниэль.' );" );
    	else echo "follow(27);";
    	$force_ai_card = 126;
    }
    else if( $noob == 30 || $noob == 32 )
    {
    	if( $a != 58 ) die( "alert( 'Выбранное тобой заклинание тоже очень сильное, но сейчас лучше сколдовать то, что советует Астаниэль.' );" );
    	else echo "follow($noob);";
    	if( $noob == 30 ) $force_ai_card = 125;
    	else $force_ai_card = 126;
    }
    else if( $noob ) die( "alert( 'Не спеши колдовать! Следуй инструкциям Астаниэль.' );" );
    
    if( $noob )
    {
    	$combat_id = f_MValue( "SELECT combat_id FROM combat_players WHERE player_id=$pid" );
    	f_MQuery( "UPDATE combat_players SET ready=1 WHERE combat_id=$combat_id AND ai=1" );
    }
}

if( CombatSetCard( $HTTP_COOKIE_VARS['c_id'], $a, $turn ) )
{
	CombatSetReady( $HTTP_COOKIE_VARS['c_id'], $turn );
	echo "query('combat_ref.php','rdy');";
}

?>

