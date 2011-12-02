<?
require_once("time_functions.php");


include_once( 'functions.php' );
include_once( 'player.php' );
include_once( 'feathers.php' );

f_MConnect( );

$tm = time( );
f_MQuery( "LOCK TABLE player_feathers WRITE" );
$res = f_MQuery( "SELECT * FROM player_feathers WHERE time + 60*60 < $tm AND feather_id IN ( ".implode( ",", $feathers_hour )." ) OR time + 120*60 < $tm AND feather_id IN ( ".implode( ",", $feathers_2hour )." )" );
f_MQuery( "DELETE FROM player_feathers WHERE time + 60*60 < $tm AND feather_id IN ( ".implode( ",", $feathers_hour )." ) OR time + 120*60 < $tm AND feather_id IN ( ".implode( ",", $feathers_2hour )." )" );
f_MQuery( "UNLOCK TABLES" );

while( $arr = f_MFetch( $res ) )
{
	$plr = new Player( $arr['player_id'] );
	undoFeather( $plr, $arr['feather_id'] );
}

?>
