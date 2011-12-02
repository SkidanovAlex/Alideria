<?

include_once( "functions.php" );
include_once( "chat_functions.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player_id = $HTTP_COOKIE_VARS['c_id'];
$channel_id = $HTTP_GET_VARS['channel'];

settype( $channel_id, "integer" );

if( AllowChannel( $channel_id ) )
{
	PlayerSetChannel( $player_id, $channel_id );

?>

<script>
	parent.chat_who.location.href='chat_who.php';

<?
	$channel_id = ChannelConvert( $channel_id, $player_id );
	$moo = $chat_channel_names[$channel_id];
	if( !$moo ) $moo = "#".$channel_id;
	$tm = date( "H:i", time( ) );
	print( "parent.chat.syst( '$tm', 'Переходим в канал $moo' );" );
?>

</script>

<?

}

?>
