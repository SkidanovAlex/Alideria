<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "card.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$mid_php = 1;	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

if ($player->location == 1 && !$player->regime)
{
	$player->SetDepth(0);
}

?>
