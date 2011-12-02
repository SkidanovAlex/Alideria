<?

include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$shop_id = $HTTP_GET_VARS['shop_id'];
settype( $shop_id, 'integer' );

if( !( $player->IsShopOwner( $shop_id ) ) )
	RaiseError( "Вы не являетесь владельцем этого магазина" );

?>

<frameset rows=100,*,100,0 bordercolor=white border=0>
	<frame id=tp name=tp src=shop_controls_top.php?shop_id=<? print $shop_id ?> frameborder=1>
	<frame id=md name=md frameborder=1>
	<frame id=bt name=bt src=shop_controls_bot.php?shop_id=<? print $shop_id ?> frameborder=1>
	<frame id=ac name=ac>
</frameset>
