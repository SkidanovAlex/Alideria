<html>
<head>
	<meta http-equiv=Content-Type content="text/html; charset=windows-1251">
</head>
<body>

<?
$mid_php = 1;
$external = 1;

include_once( "functions.php" );
include_once( "player.php" );
include_once( "shop.php" );
include_once( "arrays.php" );
include_once( "items.php" );
include_once( "clan.php" );
include_js( 'js/ii.js' );
include_js( 'js/clans.php' );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
$clan_id = $player->clan_id;

if( $_GET['log1'] == 1 ) { include( 'clan_shop_log_new.php' ); }
else { echo '<link rel="stylesheet" type="text/css" href="style2.css">'; include( 'clan_shop_control_log.php' ); }

?>
</html>
</body>