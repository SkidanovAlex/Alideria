<?php
sleep(3);
require_once('time_functions.php');
include_once( 'functions.php' );
include_once( 'arrays.php' );
include_once( 'player.php' );

f_MConnect( );
$res = f_MQuery( "SELECT player_id FROM online" );
while( $arr = f_MFetch( $res ) )
{
	$player = new Player( $arr[0] );
	$player->UploadInfoToJavaServer( );
}