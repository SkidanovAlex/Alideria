<?

include( "functions.php" );
include( "player.php" );
require_once( "referal_do_pay.php" );

f_MConnect( );

$pre = $_POST['paymentStatus'];

if( $pre == 3 )
{
	$purse = $_POST['eshopId'];
	if( $purse !== '2002630' )
	{
		LogError( "RBK Error: Purse: $purse" );
		header( "HTTP/1.0 404 Not Found" );
		die( "" );
	}
	if( $_POST['recipientCurrency'] != "RUR" )
	{
		LogError( "RBK Error: Currency: $_POST[recipientCurrency]" );
		header( "HTTP/1.0 404 Not Found" );
		die( "" );
	}
	$ammount = $_POST['recipientAmount'];
	if( fmod( $ammount, 10 ) != 0 )
	{
		LogError( "RBK Error: Amount: $ammount" );
		header( "HTTP/1.0 404 Not Found" );
		die( "" );
	}
	$player_id = (int)$_POST['orderId'];
	$res = f_MQuery( "SELECT count( player_id ) FROM characters WHERE player_id=$player_id" );
	$arr = f_MFetch( $res );
	if( !$arr[0] )
	{
		LogError( "RBK Error: Player_id: $player_id" );
		header( "HTTP/1.0 404 Not Found" );
		die( "" );
	}

	LogError( "RBK Check success: $player_id : $ammount" );
	die( "OK" );
}
else if( $pre == 5 )
{
	$purse = $_POST['eshopId'];
	if( $purse !== '2002630' )
	{
		LogError( "RBK Pay Error: Purse: $purse" );
		header( "HTTP/1.0 404 Not Found" );
		die( "" );
	}
	if( $_POST['recipientCurrency'] != "RUR" )
	{
		LogError( "RBK Pay Error: Currency: $_POST[recipientCurrency]" );
		header( "HTTP/1.0 404 Not Found" );
		die( "" );
	}
	$ammount = $_POST['recipientAmount'];
	if( fmod( $ammount, 10 ) != 0 )
	{
		LogError( "RBK Pay Error: Amount: $ammount" );
		header( "HTTP/1.0 404 Not Found" );
		die( "" );
	}
	$player_id = (int)$_POST['orderId'];
	$res = f_MQuery( "SELECT count( player_id ) FROM characters WHERE player_id=$player_id" );
	$arr = f_MFetch( $res );
	if( !$arr[0] )
	{
		LogError( "RBK Pay Error: Player_id: $player_id" );
		header( "HTTP/1.0 404 Not Found" );
		die( "" );
	}

	LogError( "RBK Pay success: $player_id : $ammount" );

	$ammount = floor( $ammount / 10 );
	$player = new Player( $player_id );
	$player->AddUMoney( $ammount );
	$player->AddToLogPost( -1, $ammount, 22, 3 );
	$player->syst2( "Вы приобрели <b>$ammount</b> ".my_word_str( $ammount, "талант", "таланта", "талантов" ) );
	referalDoPay( $player->player_id, $ammount );	
}

?>
