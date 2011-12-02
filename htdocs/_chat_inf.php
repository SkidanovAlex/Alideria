<?

include_once( "no_cache.php" );
include_once( "functions.php" );

header("Content-type: text/html; charset=windows-1251");

if( $test_server ) return;

f_MConnect( );

if( !check_cookie( ) )
	die( "<script>window.top.location.href='index.php';</script>" );

$player_id = $HTTP_COOKIE_VARS['c_id'];
$lid = $HTTP_RAW_POST_DATA;
if( isset( $_GET['lid'] ) )
	$lid = $_GET['lid'];

if(mt_rand(1,10)==3)
{
	$tm = time( );
	f_MQuery( "UPDATE online SET last_ping = $tm WHERE player_id = {$player_id}" );
}

settype( $lid, 'integer' );

?>

	function m(a,b,c,d,e,f,g)
	{
		parent.msg(a,b,c,d,e,f,g);
	}

<?


$login_cash = Array( );
function getLogin( $q )
{
	global $login_cash;

	if( $login_cash[$q] ) return $login_cash[$q];
	
	$res = f_MQuery( "SELECT login FROM characters WHERE player_id = $q" );
	$arr = f_MFetch( $res );
	if( !$arr ) $res = "Неизвестный Персонаж";
	else $res = $arr[0];
	
	$login_cash[$q] = $res;
	return $res;
}

// ---------------------
$sock = socket_create(AF_INET, SOCK_STREAM, 0);
//socket_set_option( $sock, SOL_SOCKET, SO_REUSEADDR, 1 );
//socket_set_option( $sock, SOL_SOCKET, SO_RCVTIMEO, array( 'sec'=>0, 'usec'=>100000 ) );
//socket_set_option( $sock, SOL_SOCKET, SO_SNDTIMEO, array( 'sec'=>0, 'usec'=>100000 ) );
$cs_connected = socket_connect($sock, "127.0.0.1", 1100);

if (!$cs_connected)
{
   LogError("Cannot conect to chat server" . socket_strerror  ( socket_last_error( $sock )). "______" . var_export($cs_conected, true));
   socket_clear_error();
}
//socket_set_option( $sock, SO_REUSEADDR, 1 );
$msg = "get\n{$player_id}\n$lid\n";
socket_write( $sock, $msg, strlen($msg) ); 

if( $player_id == 173 || true )
{
	$txt          = "";
	$continueRead = true;
	
	while($continueRead)
	{
	   $retu = socket_read( $sock, 512, PHP_BINARY_READ );
           if ($retu[511] == '')
           {
              $continueRead = false;
           }
           
           $txt .= $retu;
	}
	
	
	/*
	settype( $val, 'integer' );

	$txt = '';
	for( $i = 0; $i < $val; $i += 512 )
	{
		$txt .= socket_read( $sock, min( $val - $i, 512 ) );
	}

        */
	socket_close( $sock );

	if( substr( $txt, -5 ) != "/*'*/" )
	{
		if( $player_id == 172 ) $txt = "alert('".addslashes($txt)."');";
		else
		{
		  LogError("CSServer " . socket_strerror  ( socket_last_error( $sock )) . "______" . var_export($txt, true));
		  socket_clear_error();
		LogError("UPAL_ TXT" . $txt);
		
		 $txt = "upal();";
		 
		 }
	}
	else $txt = substr( $txt, 0, -5 )."ok();";

	die( $txt );
}

socket_close( $sock );

// ---------------------

if( !$lid )
{
	$res = f_MQuery( "SELECT max( msg_id ) FROM ch_messages" );
	$arr = f_MFetch( $res );
	$lid = $arr[0];
	if( !$lid ) $lid = 0;
	print( "parent.chat.lid = $lid;" );
}

$res = f_MQuery( "SELECT * FROM ch_messages WHERE msg_id > $lid AND ( channel=0 AND ( target = 0 OR target = $player_id OR fr = $player_id ) ) ORDER BY msg_id" );

while( $arr = f_MFetch( $res ) )
{
	$msg = AddSlashes( $arr[message] );
	$tm = date( 'H:i', $arr[time] );
	if( $arr[author] != '' )
	{
		if( $arr[target] == 0 && $arr[channel] == 0 ) $moo = 'Общий';
		else if( $arr[channel] )
		{
			//	сделать здесь каналы
		}
		else if( $arr[fr] == $player_id ) $moo = getLogin( $arr[target] );
		else if( $arr[target] == $player_id ) $moo = getLogin( $arr[fr] );
		
		print( "m($arr[msg_id],'$tm','$arr[author]','$msg','$moo','$arr[nick_clr]','$arr[text_clr]');" );
		$lid = $arr[msg_id];
	}
	else
	{
		if( $msg == '/combat' ) print( "window.top.game.location.href='combat.php';" );
		else if( $msg == '/items' ) print( "window.top.char_ref.location.href='char_ref.php';" );
		else if( $msg == '/punish' ) print( "window.top.location.href='wpunish.html';" );
		else print( "parent.syst('$tm','$msg');parent.chat.lid = $arr[msg_id];" );
	}
	$lid = $arr[msg_id];
}

print( "parent.chat.lid = $lid;" );

?>
