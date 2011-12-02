<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

$mid_php = 1;

$plr = new Player( 69055 );
$plr->UploadInfoToJavaServer( );

$sock = socket_create(AF_INET, SOCK_STREAM, 0);
socket_set_option( $sock, SOL_SOCKET, SO_REUSEADDR, 1 );
socket_set_option( $sock, SOL_SOCKET, SO_RCVTIMEO, array( 'sec'=>0, 'usec'=>100000 ) );
socket_set_option( $sock, SOL_SOCKET, SO_SNDTIMEO, array( 'sec'=>0, 'usec'=>100000 ) );
socket_connect($sock, "127.0.0.1", 1100);
socket_set_option( $sock, SO_REUSEADDR, 1 );
$msg = "get\n69055\n1\n";
socket_write( $sock, $msg, strlen($msg) ); 

$val = socket_read( $sock, 100000, PHP_NORMAL_READ );
settype( $val, 'integer' );

$txt = '';
for( $i = 0; $i < $val; $i += 512 )
{
	$txt .= socket_read( $sock, min( $val - $i, 512 ), PHP_BINARY_READ );
}

socket_close( $sock );

if( substr( $txt, -5 ) != "/*'*/" ) $ok = 0;
else $ok = 1;

if (false && $ok) echo "Chat is OK";
else
{
	echo "Chat is NOT ok";
	function my_exec($cmd, $input='')
         {$proc=proc_open($cmd, array(0=>array('pipe', 'r'), 1=>array('pipe', 'w'), 2=>array('pipe', 'w')), $pipes);
          fwrite($pipes[0], $input);fclose($pipes[0]);
          fclose($pipes[1]);
          fclose($pipes[2]);
          $rtn=proc_close($proc);
          return 0;
         }
	my_exec('/home/test/data/chat2/restart_chat');
	sleep(1);
	include ('../fix/admin_init_chat_server.php');
}

?>
