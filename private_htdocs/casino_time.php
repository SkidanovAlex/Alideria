<?
require_once("time_functions.php");

include( 'functions.php' );

f_MConnect( );

f_MQuery( "UPDATE player_casino SET stavkas = 0" );

include( 'lottery_time.php' );

?>
