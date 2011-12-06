<?php

defined('FBASE') or die('');
$bd_link = null;
function f_MConnect( )
{
	global $query_number, $bd_link, $alredy_connected;
	
	if( $alredy_connected ) return;
	$alredy_connected = 1;
	
	$query_number = 0;
	
	$bd_link = mysql_connect( "localhost", "main_master", "tRy6TVMXrAL8amLCVjcMRrX36NpNtDxJ" );
	$err = mysql_error( );
	if( $err )
	{
		RaiseError( "Ошибка при подключении к БД ".$err );
		return 0;
	}
	mysql_select_db( "alideria", $bd_link);  /**/
	$err = mysql_error( );
	if( $err )
	{
		RaiseError( "Ошибка при выборе БД. ".$err );
		return 0;
	}
	mysql_set_charset( 'cp1251' );
}
