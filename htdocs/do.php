<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "arrays.php" );
include_once( "skin.php" );
include_once( "game_functions.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "window.top.location.href='index.php';" );

$mid_php = 1;	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

if( $player->regime == 100 )
{
	print( "location.href='combat.php';" );
	die( );
}

if( $player->regime == 101 || $player->regime == 102 )
{
	print( "location.href='trade_sb.php';" );
	die( );
}

$loc = $player->location;
$depth = $player->depth;

if( $loc == 5 && $depth == 1 ) include( 'locations/portal/ref.php' );

if( $loc == 2 && $depth == 1 ) include( 'locations/newarena/ref.php' );

if( $loc == 2 && $depth == 50 && ( $regime == 114 || $regime == 117 ) )
{
	if( !f_MValue( "SELECT count( player_id ) FROM clan_wonder_ips WHERE ip='$ipstr' AND player_id={$player->player_id}" ) )
		f_MQuery( "INSERT INTO clan_wonder_ips VALUES  ( {$player->player_id}, '{$ipstr}', ".time( ).", $clan_id )" );
}

?>
