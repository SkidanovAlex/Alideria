<?php
phpinfo();
die();
include_once( 'functions.php' );
include_once('chat_functions.php');
ini_set('display_errors', 1);      
echo ini_get('xcache.var_size');
 ini_set('xcache.var_size', '64M');
//error_reporting(0);
SetCachedValue("tews", str_repeat('1', 1700000), 10);
//echo GetCachedValue(  $key = 'chat.plist.who_global');
echo   chat_who_global_list();
die();
include_once( 'arrays.php' );
include_once( 'player.php' );

                             include_once('combat_turn.php');

$combat = new Combat(1147862);
$combat->CheckWinners(1, false);

           var_dump($combat);



/*
f_MConnect( );


	$res = f_MQuery( "SELECT player_id FROM characters WHERE regime='100'" );

         while( $arr = f_MFetch( $res ) )
	{
		$player2 = new Player( $arr[0] );
		if( $player2->regime == 100 )
			$player2->LeaveCombat( );
        }
        
        */
