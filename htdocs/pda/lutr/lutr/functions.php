<?
define ('FBASE',               true);

$bd_link = null;
$test_server = false;

$alredy_connected = 0;


require_once('mysql_connect.php');

function RaiseError($a, $b)
{
	die("alert('$a');");
}

function f_MQuery( $a )
{
	global $query_number, $bd_link;
	
	$query_number ++;
	
	$res = mysql_query( $a, $bd_link );
	$err = mysql_error( );
	if( $err )
	{
    	die( "alert('Внутренная ошибка сервера');" );
		return 0;
	}
	
	return $res;
}


function f_MEscape( $str )
{
   global $bd_link;

   return mysql_real_escape_string( $str, $bd_link);
}


function f_MFetch( $a )
{

	return mysql_fetch_array( $a );
}

function f_MNum( $a )
{
	return mysql_num_rows( $a );
}

function f_MValue( $a )
{
	$res = f_MQuery( $a );
	$arr = f_MFetch( $res );
	return $arr[0];
}

function f_MClose( )
{
global $bd_link;
	mysql_close( $bd_link);
}


?>