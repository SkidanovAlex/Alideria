<?php

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "card.php" );
include_once( "noob.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$res = f_MQuery( "SELECT a FROM noob WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( $arr[0] != $_GET['a'] ) die( );
$a = $arr[0];
++ $a;

if( $a == 38 )
{
	f_MQuery( "DELETE FROM noob WHERE player_id={$player->player_id}" );
	echo "location.href='leave_combat.php';";
	f_MQuery( "UPDATE statistics SET noobs=noobs+1" );
	die( );
}

f_MQuery( "UPDATE noob SET a=$a, b=0 WHERE player_id={$player->player_id}" );
if( true ) echo "n_clear( );";
else
{
	echo "n_els = new Array( );\n";
	echo "n_pars = new Array( );\n";
}
show_noob( $a );

?>
