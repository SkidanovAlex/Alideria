<?php
echo "<pre>"; 
var_dump(get_extension_funcs('xcache')); 
die();
include_once('functions.php');
include_once('player.php');


       	$plr = new Player( 69055 );
        $plr->UploadInfoToJavaServer( );

$msg = "Поздравляю с Хелуином. Всем подарочные премиумы на 4 дня!";
            $sock = socket_create(AF_INET, SOCK_STREAM, 0);
            socket_connect($sock, "127.0.0.1", 1100);
            $tm = date( "H:i", time( ) );
            $st = "say\n{$msg}\n69055\n0\n0\n{$tm}\nsay\n{$msg}\n0\n0\n0\n{$tm}\n";
            socket_write( $sock, $st, strlen($st) ); 
            socket_close( $sock );
