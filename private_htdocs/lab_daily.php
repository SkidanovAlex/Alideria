<?
require_once("time_functions.php");


include( 'functions.php' );
include( 'lab.php' );

f_MConnect( );

LogError( "lab_daily" );

f_MQuery( "DELETE FROM player_labs" );
f_MQuery( "DELETE FROM lab_items" );
f_MQuery( "DELETE FROM lab_mobs" );

$lab = new Lab( 0, 1, 30, 30 );
$lab->Init( );
$lab->Generate( );
$lab->Store( );



?>
Moo!