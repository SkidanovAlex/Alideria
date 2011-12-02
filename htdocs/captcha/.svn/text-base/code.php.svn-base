<?

include_once( '../no_cache.php' );
include_once( "../functions.php" );
include_once( "kcaptcha.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player_id = $HTTP_COOKIE_VARS['c_id'];

$code=rand(1000,9999);

f_MQuery( "LOCK TABLE player_num WRITE" );
f_MQuery( "DELETE FROM player_num WHERE player_id = $player_id" );
f_MQuery( "INSERT INTO player_num VALUES ( $player_id, $code )" );
f_MQuery( "UNLOCK TABLES" );


$captcha = new KCAPTCHA("$code");

?> 