<?

include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "�������� ��������� Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$res = f_MQuery( "SELECT combat_id, side, ready FROM combat_players WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

if( !$arr )
{
	die( "����� �� � ���" );
}

$tm = time( );
f_MQuery( "UPDATE online SET last_ping = $tm WHERE player_id = {$player->player_id}" );

$combat_id = $arr[0];
$side = $arr[1];
$ready = $arr[2];

if( $player->level < 5 && f_MValue( "SELECT `combat_id` FROM `ta_combats` WHERE `combat_id` = $combat_id" ) )
{
	header( 'Content-Type: text/html;charset=windows-1251' );
	die( "alert( '�� ������� ����������� �� ������� ����� ������ ���� ����� �������' );" );
}

$cres = f_MQuery( "SELECT last_turn_made, timeout FROM combats WHERE combat_id=$combat_id" );
$carr = f_MFetch( $cres );
$ltm = $carr[0];
$rtmo = $carr[1];
$tm = time( );

// TO begin

if( $ready != 1 )
{
}
else if( $tm - $ltm > $rtmo )
{
	f_MQuery( "LOCK TABLE combat_players WRITE" );
	$res = f_MQuery( "SELECT player_id FROM combat_players WHERE combat_id = $combat_id AND ready = 0" );
	if( !f_MNum( $res ) )
		f_MQuery( "UNLOCK TABLES" );
	else
	{
    	f_MQuery( "UPDATE combat_players SET forces=forces+ 1, ready=1, card_id=-1 WHERE combat_id = $combat_id AND ready = 0" );
		f_MQuery( "UNLOCK TABLES" );
    	include_once( 'combat_functions.php' );
    	$st = '';
    	$nn = 0;
    	while( $arr = f_MFetch( $res ) )
    	{
    		$larr = f_MFetch( f_MQuery( "SELECT login FROM characters WHERE player_id=$arr[0]" ) );
    		$st .= ", ".$larr[0];
            ++ $nn;
    	}
    	$st = substr( $st ,1 );
    	if( $nn == 1 ) $st .= " �� ��� ��� �� �������� ���. ";
    	else $st .= " �� ��� ��� �� ��������� ���. ";
    	CheckTurnOver( $combat_id, $side, "<font color=darkblue>$st<b>$player->login</b> ��������� ���</font><br>" );
	}
}
else
{
	print( "$tm:$ltm:$rtmo" );
}

// TO end

?>

query('combat_ref.php','ref');

