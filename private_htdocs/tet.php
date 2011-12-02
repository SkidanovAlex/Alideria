<?php
sleep(3);
require_once('time_functions.php');
//include_once( 'functions.php' );
include_once( 'arrays.php' );
include_once( 'player.php' );

f_MConnect( );
$player = new Player( 6825);
$player->UploadInfoToJavaServer( );
$player->syst2("tet.php");