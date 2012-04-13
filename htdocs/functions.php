<?

file_put_contents('last.txt', $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING']);

                                                                                                        
define ('HTDOCS_PATH',         '/srv/www/alideria/htdocs/');
define ('PRIVATE_HTDOCS_PATH', '/srv/www/alideria/private_htdocs/');
define ('LOGS_PATH',           '/srv/www/alideria/logs/');
define ('FBASE',               true);

$bd_link = null;
$test_server = false;

$alredy_connected = 0;


require_once('mysql_connect.php');


function debugErrorHandler()
{
echo "<pre>";
var_dump(func_get_args());
echo "</pre>";
}


function debugDumpFunctionExists($func_name)
{
   if (function_exists($func_name))
   {
echo "<pre>";
var_dump(debug_backtrace());
echo "</pre>";
}

}



function SetCachedValue($key, $value, $timeout)
{
   xcache_set($key, $value, $timeout);
}


function GetCachedValue($key)
{

 $value = xcache_get($key);

   if($value === NULL)
      $value = -1;


   return $value;
}


function ClearCachedValue($key)
{

  xcache_unset($key);
}


















function create_select_global( $nm, $arr, $val )
{
	$st = "<select class=m_btn name='$nm'>";
	
	foreach( $arr as $key=>$value )
	{
		$st .= "<option value=$key";
		if( $key == $val ) $st .= " selected";
		$st .= ">$value" ;
	}
	
	$st .= '</select>';
	
	return $st;
}

function create_select_small( $nm, $arr, $val )
{
	$st = "<select class=btn40 name='$nm'>";
	
	foreach( $arr as $key=>$value )
	{
		$st .= "<option value=$key";
		if( $key == $val ) $st .= " selected";
		$st .= ">$value" ;
	}
	
	$st .= '</select>';
	
	return $st;
}


function rome_number( $a )
{
	if( $a == 0 ) return "0";
	if( $a < 0 ) return $a;

	$ret = "";
	while( $a >= 1000 )
	{
		$ret .= "M";
		$a -= 1000;
	}
	while( $a >= 900 )
	{
		$ret .= "CM";
		$a -= 900;
	}
	while( $a >= 500 )
	{
		$ret .= "D";
		$a -= 500;
	}
	while( $a >= 400 )
	{
		$ret .= "CD";
		$a -= 400;
	}
	while( $a >= 100 )
	{
		$ret .= "C";
		$a -= 100;
	}
	while( $a >= 90 )
	{
		$ret .= "XC";
		$a -= 90;
	}
	while( $a >= 50 )
	{
		$ret .= "L";
		$a -= 50;
	}
	while( $a >= 40 )
	{
		$ret .= "XL";
		$a -= 40;
	}
	while( $a >= 10 )
	{
		$ret .= "X";
		$a -= 10;
	}
	while( $a >= 9 )
	{
		$ret .= "IX";
		$a -= 9;
	}
	while( $a >= 5 )
	{
		$ret .= "V";
		$a -= 5;
	}
	while( $a >= 4 )
	{
		$ret .= "IV";
		$a -= 4;
	}
	while( $a >= 1 )
	{
		$ret .= "I";
		-- $a;
	}

	return $ret;
}

function my_word_str( $num, $a1, $a2, $a5 )
{
	if( $num % 10 == 0 ) return $a5;
	if( $num % 100 >= 10 && $num % 100 <= 19 ) return $a5;
	if( $num % 10 == 1 ) return $a1;
	if( $num % 10 >= 5 ) return $a5;
	return $a2;
}

function my_word_form( $num, $f1, $f13, $f2m ) // именительный (теперь у вас есть ... )
{
	if( $num == 1 ) return $f1;
	if( $num == 2 ) return "пара " . $f2m;
	if( $num == 3 ) return "тройка " . $f2m;
	if( $num == 12 ) return "дюжина " . $f2m;
	if( $num == 24 ) return "две дюжины " . $f2m;

	if( $num % 10 == 0 ) return "$num " . $f2m;
	if( $num % 100 >= 10 && $num % 100 <= 19 ) return "$num $f2m";
	if( $num % 10 == 1 ) return "$num $f1";
	if( $num % 10 >= 5 ) return "$num $f2m";
	return "$num $f13";
}

function my_word_form2( $num, $f4, $f13, $f2m ) // винительный (вы нашли ... )
{
	if( $num == 1 ) return $f4;
	if( $num == 2 ) return "пару " . $f2m;
	if( $num == 3 ) return "тройку " . $f2m;
	if( $num == 12 ) return "дюжину " . $f2m;
	if( $num == 24 ) return "две дюжины " . $f2m;

	if( $num % 10 == 0 ) return "$num " . $f2m;
	if( $num % 100 >= 10 && $num % 100 <= 19 ) return "$num $f2m";
	if( $num % 10 == 1 ) return "$num $f4";
	if( $num % 10 >= 5 ) return "$num $f2m";
	return "$num $f13";
}

function my_word_form3( $num, $f2, $f2m ) // родительный (вы стали обладателем ... )
{
	if( $num == 1 ) return $f2;
	if( $num == 2 ) return "пары " . $f2m;
	if( $num == 3 ) return "тройки " . $f2m;
	if( $num == 12 ) return "дюжины " . $f2m;
	if( $num == 24 ) return "двух дюжин " . $f2m;

	if( $num % 10 == 0 ) return "$num " . $f2m;
	if( $num % 100 >= 10 && $num % 100 <= 19 ) return "$num $f2m";
	if( $num % 10 == 1 ) return "$num $f2";
	if( $num % 10 >= 5 ) return "$num $f2m";
	return "$num $f2m";
}

function my_time_str( $a, $show_seconds = true )
{
	settype( $a, 'integer' );
	if( $show_seconds ) $res = ($a % 60)." ".my_word_str( ($a % 60), "секунда", "секунды", "секунд" );
	else $res = "";
	$a /= 60;
	settype( $a, 'integer' );
	if( $a ) $res = ($a % 60)." ".my_word_str( ($a % 60), "минута", "минуты", "минут" ) . ( $show_seconds ? ", " : "" ) . $res;
	$a /= 60;
	settype( $a, 'integer' );
	if( $a ) $res = ($a % 24)." ".my_word_str( ($a % 24), "час", "часа", "часов" ) . ", " . $res;
	$a /= 24;
	settype( $a, 'integer' );
	if( $a ) $res = $a." ".my_word_str( ($a), "день", "дня", "дней" ) . ", " . $res;

	if( $res == "" ) $res = "меньше минуты";
	
	return $res;
}

function my_time_str2( $a, $show_seconds = true )
{
	settype( $a, 'integer' );
	if( $show_seconds ) $res = ($a % 60)." ".my_word_str( ($a % 60), "секунду", "секунды", "секунд" );
	else $res = "";
	$a /= 60;
	settype( $a, 'integer' );
	if( $a ) $res = ($a % 60)." ".my_word_str( ($a % 60), "минуту", "минуты", "минут" ) . ( $show_seconds ? ", " : "" ) . $res;
	$a /= 60;
	settype( $a, 'integer' );
	if( $a ) $res = ($a % 24)." ".my_word_str( ($a % 24), "час", "часа", "часов" ) . ", " . $res;
	$a /= 24;
	settype( $a, 'integer' );
	if( $a ) $res = $a." ".my_word_str( ($a), "день", "дня", "дней" ) . ", " . $res;

	if( $res == "" ) $res = "совсем скоро";
	
	return $res;
}

function UpdateTitle( $sc_tags = true )
{
	global $player;
	$st = '';
	$res = f_MQuery( "SELECT title FROM player_profile WHERE player_id={$player->player_id}" );
	$arr = f_MFetch( $res ); if( $arr ) $st = AddSlashes( $arr[0] )." ";
	if( $sc_tags ) print( "<script>window.top.tstr = '{$st}$player->login, Уровень: $player->level, Боевой Опыт: $player->exp, Проф. Опыт: $player->prof_exp - Алидерия'; window.top.document.title=window.top.tstr;</script>" );
	else  print( "window.top.tstr = '{$st}$player->login, Уровень: $player->level, Боевой Опыт: $player->exp, Проф. Опыт: $player->prof_exp - Алидерия'; window.top.document.title=window.top.tstr;" );
}

function LogError( $a, $b = false )
{
	global $HTTP_COOKIE_VARS;
	global $_SERVER;
	
	if( $b !== false ) $a .= ". Доп. информация: $b";
	
	$tm = date( "d.m.Y H:i", time( ) );
	


	$s = $tm.": ".$_SERVER['PHP_SELF'].": ".getenv( "HTTP_X_REAL_IP" )."(".getenv( "HTTP_X_FORWARDED_FOR" ).")".": "."[".$HTTP_COOKIE_VARS['c_id']."] ".$a;

	$s = str_replace(array("\n", "\r"), array(" \\n", " "), $s)."\n";
			
	if ($HTTP_COOKIE_VARS['c_id'] == 1375970)
	{
	   LogErrorCustom( $a, $b);
	}
	$f = fopen(LOGS_PATH . "log.txt", "a" );
	fwrite( $f, $s );
	fclose( $f );
}

function LogScripting( $a, $b = false )
{
	global $HTTP_COOKIE_VARS;
	global $_SERVER;
	
	if( $b !== false ) $a .= ". Доп. информация: $b";
	
	$tm = date( "d.m.Y H:i", time( ) );
	


	$s = $tm.": ".$_SERVER['PHP_SELF'].": ".getenv( "HTTP_X_REAL_IP" )."(".getenv( "HTTP_X_FORWARDED_FOR" ).")".": "."[".$HTTP_COOKIE_VARS['c_id']."] ".$a;

	$s = str_replace(array("\n", "\r"), array(" \\n", " "), $s)."\n";
			
	$f = fopen(LOGS_PATH . "log_scripting.txt", "a" );
	fwrite( $f, $s );
	fclose( $f );
}

function LogErrorSql( $a, $b = false )
{
	global $HTTP_COOKIE_VARS;
	global $_SERVER;
	
	if( $b !== false ) $a .= ". Доп. информация: $b";
	
	

	$tm = date( "d.m.Y H:i", time( ) );
	$s = $tm.": ".$_SERVER['PHP_SELF'].": ".getenv( "HTTP_X_REAL_IP" )."(".getenv( "HTTP_X_FORWARDED_FOR" ).")".": "."[".$HTTP_COOKIE_VARS['c_id']."] ".$a;
	
	$s = str_replace(array("\n", "\r"), array(" \\n", " "), $s)."\n";
		
	if ($HTTP_COOKIE_VARS['c_id'] == 1375970)
	{
	   LogErrorCustom( $a, $b);
	}
	
	
	$f = fopen(LOGS_PATH . "log_sql.txt", "a" );
	fwrite( $f, $s );
	fclose( $f );
}




function LogErrorCustom( $a, $b = false )
{
	global $HTTP_COOKIE_VARS;
	global $_SERVER;
	
	if( $b !== false ) $a .= ". Доп. информация: $b";
	

	$tm = date( "d.m.Y H:i", time( ) );
	$s = $tm.": ".$_SERVER['PHP_SELF'].": ".getenv( "HTTP_X_REAL_IP" )."(".getenv( "HTTP_X_FORWARDED_FOR" ).")".": "."[".$HTTP_COOKIE_VARS['c_id']."] ".$a;

	$s = str_replace(array("\n", "\r"), array(" \\n", " "), $s)."\n";
	
	
	$f = fopen(LOGS_PATH . "log_custom.txt", "a" );
	fwrite( $f, $s );
	fclose( $f );
}


function LogErrorParams( $a, $b = false )
{
	global $HTTP_COOKIE_VARS;
	global $_SERVER;
	
	if( $b !== false ) $a .= ". Доп. информация: $b";
	


	$tm = date( "d.m.Y H:i", time( ) );
	$s = $tm.": ".$_SERVER['PHP_SELF'].": ".getenv( "REMOTE_ADDR" )."(".getenv( "HTTP_X_FORWARDED_FOR" ).")".": "."[".$HTTP_COOKIE_VARS['c_id']."] ".$a;

	$s = str_replace(array("\n", "\r"), array(" \\n", " "), $s)."\n";
	
		
	if ($HTTP_COOKIE_VARS['c_id'] == 1375970)
	{
	   LogErrorCustom( $a, $b);
	}
	$f = fopen(LOGS_PATH . "log_params.txt", "a" );
	fwrite( $f, $s );
	fclose( $f );
}

function RaiseError( $a, $b = false )
{
	LogError( $a, $b );
	die( "</script><script>window.top.location.href='error.php?text=$a';</script>" );
}

function text_sex_parse( $op, $dl, $cl, $txt, $sex )
{
	$ret = '';
	$l = strlen( $txt );
	$lop = -1;
	$ldl = -1;
	for( $i = 0; $i < $l; ++ $i )
	{
		if( $txt[$i] == $op ) $lop = $i;
		else if( $txt[$i] == $dl && $lop != -1 ) $ldl = $i;
		else if( $txt[$i] == $cl && $lop != -1 && $ldl != -1 )
		{
			if( !$sex ) $ret .= substr( $txt, $lop + 1, $ldl - $lop - 1 );
			else $ret .= substr( $txt, $ldl + 1, $i - $ldl - 1 );

			$lop = -1;
			$ldl = -1;
		}
		else if( $lop == -1 ) $ret .= $txt[$i];
	}

	return $ret;
}

function f_MQuery( $a )
{
	global $query_number, $bd_link;
	
	$query_number ++;
//	LogErrorSql( $a );
	
	$res = mysql_query( $a, $bd_link );
	$err = mysql_error( );
	if( $err )
	{
		LogErrorSql( "Ошибка MySQL запроса", "Строка: ".__FUNCTION__.", Запрос: $a, Ошибка: $err" );
    	die( "</script><script>window.top.location.href='/error.php?text=Внутренная ошибка сервера';</script>" );
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

function f_register_globals( )
{
	global $HTTP_GET_VARS;
	global $HTTP_POST_VARS;
	
	foreach( $HTTP_GET_VARS as $key=>$value )
	{
		$a = &$key;
		$a = "g_".$a;
		global $$a;
		$$a = $value;
	}
	foreach( $HTTP_POST_VARS as $key=>$value )
	{
		$a = &$key;
		$a = "p_".$a;
		global $$a;
		$$a = $value;
	}
}







function check_cookie( )
{
	global $HTTP_COOKIE_VARS;
	global $test_server;
	
	$id = $HTTP_COOKIE_VARS['c_id'];

	settype( $id, 'integer' );
	settype( $HTTP_COOKIE_VARS['c_id'], 'integer' ); // Скажем нет инъекциям через куки


if (false && $id == 1308118)// 1325884)
{
   ini_set('display_errors', 1);
error_reporting(E_ALL) ; //E_ERROR | E_WARNING | E_PARSE);
// set_error_handler  ("debugErrorHandler", (E_ALL ^ E_NOTICE));
//error_reporting(0);

define('DEBUG_MODE', 1);

}

            
        if (GetCachedValue('USER:' . $id . ':scrc_key') == $HTTP_COOKIE_VARS['c_loc'])
        {
           return $id;
        }



	// part 1. Try using chat server
	if( !$test_server )
	{
        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_set_option( $sock, SOL_SOCKET, SO_REUSEADDR, 1 );
        socket_connect($sock, "127.0.0.1", 1100);
        socket_set_option( $sock, SO_REUSEADDR, 1 );
        $msg = "check\n$id\n".(int)$HTTP_COOKIE_VARS['c_loc']."\n";
        socket_write( $sock, $msg, strlen($msg) ); 
    	$txt = (int) socket_read( $sock, 100000, PHP_BINARY_READ );
    	/*
    	settype( $val, 'integer' );

    	$txt = '';
    	for( $i = 0; $i < $val; $i += 512 )
    	{
    		$txt .= socket_read( $sock, min( $val - $i, 512 ), PHP_BINARY_READ );
    	}

    	*/
socket_close($sock);

    	if ($txt == "1")
    	{

           SetCachedValue('USER:' . $id . ':scrc_key', $HTTP_COOKIE_VARS['c_loc'],  7200);
               
    		return $id;
    		
    	}
    	
	}
	
	


	$res = f_MQuery( "SELECT session_crc FROM online WHERE player_id=$id" );
	if( !( $arr = mysql_fetch_array( $res ) ) )
		return 0;
		
	if( $arr[0] != $HTTP_COOKIE_VARS['c_loc'] )
		return 0;

	f_MQuery( "UPDATE characters SET scripts=scripts + 1 WHERE player_id=$id" );
		
//	LogError("$id - $HTTP_COOKIE_VARS[c_loc] - APPROVED");

        SetCachedValue('USER:' . $id . ':scrc_key', $HTTP_COOKIE_VARS['c_loc'],  7200);
               


	return $id;
}

function contains( $a, $b )
{
	foreach( $a as $v )
		if( $v == $b ) return true;
		
	return false;
}

$js_scripts = Array( );
function include_js( $a )
{
	if( $js_scripts[$a] ) return;
	$js_scripts[$a] = 1;
	print( "<script src=$a></script>\n" );
//	print( "<script>" );
//	include( "$a" );
//	print( "</script>" );
}

function getReqStr( $req )
{
	$str3 = '';
	foreach( $req as $stat=>$value )
	{
		$res = f_MQuery( "SELECT icon FROM attributes WHERE attribute_id=$stat" );
		$arr = f_MFetch( $res );
		$str3 .= "<img width=20 height=20 src=images/icons/attributes/$arr[0]> $value ";
	}

	return $str3;
}

function ipng( $tid, $iid, $src, $w, $h, $add = '', $tags = true )
{
	$st = ( $tags ) ? "<script>document.write( " : "";
	$st .= "ipng('$tid','$iid','$src',$w,$h,'$add')";
	if( $tags ) $st .= " );</script>";
	return $st;
}


// Проверка на инъекции

$HTTP_GET_VARS = Array( );
$HTTP_POST_VARS = Array( );
$HTTP_COOKIE_VARS = Array( );

foreach( $_GET as $key=>$value )
{
	$HTTP_GET_VARS[$key] = $value;
}
foreach( $_POST as $key=>$value )
{
	$HTTP_POST_VARS[$key] = $value;
}
foreach( $_COOKIE as $key=>$value )
{
	$HTTP_COOKIE_VARS[$key] = $value;
}

function checkInt( $a, $b, $c )
{
	global $_SERVER;
	if( $_SERVER['PHP_SELF'] == '/forum.php/forum.php' ) return;
	if( $c == 'on' || $b == 'ajax' || $b == 'q' || $b == 'mode' || $b == 'rank_name' || $b == 'job_name' ||  $b == 'red_black_action' || $b == 'email' || $b == 'text' || $b == 'descr' || $b == 'pwd' || $b == 'txt' || $b == 'nm' || $b == 'where' || $b == 'msg' || $b == 'order' || $b == 'title' || $b == 'pwd_again' ) return;
	if( $b == 'city' || $b == 'skype' || $b == 'quote' || $b == 'hide_email' ||  $b == 'nick' || $b == 'ref' || $b == 'target' || $b == 'login' ) return;
	if( $b == 'act' && $_SERVER['PHP_SELF'] == '/post_action.php/post_action.php' ) return;
	if( $b == 'do' && $_SERVER['PHP_SELF'] == '/lab_do.php/lab_do.php' ) return;
	if( ( $b == 'premium' || $b == 'premium2' ) && $_SERVER['PHP_SELF'] == '/help.php/help.php' ) return;
	if( $c == 'deleted' || $c == 'sms' || $c == 'undefined' || $c == 'wm' ) return;
	if ($_SERVER['PHP_SELF'] == '/tgm/ajaxQuery.php/tgm/ajaxQuery.php' || $_SERVER['PHP_SELF'] == '/admin86006609098moo/item_editor_apply.php/admin86006609098moo/item_editor_apply.php' || $_SERVER['PHP_SELF'] == '/admin86006609098moo/talk_editor.php/admin86006609098moo/talk_editor.php') return;
	if( $_SERVER['PHP_SELF'] == '/player_control.php/player_control.php' || $_SERVER['PHP_SELF'] == '/ch_ignore.php/ch_ignore.php' || $_SERVER['PHP_SELF'] == '/ajaxQuery.php/ajaxQuery.php' || $_SERVER['PHP_SELF'] == '/tgm/index.php/tgm/index.php' ) return;
	if( $c == 'newpwd' || $c == 'newpwd2' || $c == 'oldpwd' ) return;

//	if ($c == 'right' || $c == 'go' || $c == 'left') return;

	if( $c != '' && !is_numeric( $c ) ) LogErrorParams( 'Подозрение на параметр '."$a '$b' = '$c'" );
}

if( !isset( $dont_check_params ) || !$dont_check_params )
{
    foreach( $HTTP_GET_VARS as $key=>$value )
    {
    	if( strpos( strtoupper( $value ), "SELECT " ) !== false ) RaiseError( "Возможна попытка инъекции через GET-переменную $key: $value" );
    	checkInt( 'GET', $key, $value );
    }
    foreach( $HTTP_POST_VARS as $key=>$value )
    {
    	if( strpos( strtoupper( $value ), "SELECT " ) !== false ) RaiseError( "Возможна попытка инъекции через POST-переменную $key: $value" );
    	checkInt( 'POST', $key, $value );

    }
}

class Sql
{
	var $table;
	var $res;
	var $fields;
	var $arr;

	function Sql( $table )
	{
		$this->table = $table;
		$this->arr = array( );
	}
	function get( $where = 'true' )
	{
		$a = "";
		foreach( $this->fields as $key )
			$a .= ", $key";
		$a = substr( $a, 1 );

		$this->res = f_MQuery( "SELECT $a FROM {$this->table} WHERE {$where}" );
	}
	function val( $what, $where )
	{
		$res = f_MQuery( "SELECT $what FROM {$this->table} WHERE {$where}" );
		$arr = f_MFetch( $res );
		return $arr[0];
	}
	function fetch( )
	{
		$this->arr = f_MFetch( $this->res );
	}
	function store( $key = false, $lock = false )
	{
		if( $lock ) f_MQuery( "LOCK TABLE {$this->table} WRITE" );

		$insert = false;
		if( $key === false ) $insert = true;
		else
		{
			$res = f_MQuery( "SELECT count( $key ) FROM {$this->table} WHERE $key = ".($this->arr[$key]) );
			$arr = f_MFetch( $res );
			if( !$arr[0] ) $insert = true;
		}

		if( $insert ) $this->insert( );
		else $this->update( "$key = ".($this->arr[$key]) );


		if( $lock ) f_MQuery( "UNLOCK TABLES" );
	}
	function update( $where )
	{
		$st = "";
		foreach( $this->fields as $key )
		{
			$value = addslashes( $this->arr[$key] );
			$st .= ", $key='$value'";
		}
		$st = substr( $st, 1 );
		f_MQuery( "UPDATE {$this->table} SET $st WHERE $where" );
	}
	function insert( )
	{
		$a = "";
		$b = "";
		foreach( $this->fields as $key )
		{
			$value = addslashes( $this->arr[$key] );
			$a .= ", $key";
			$b .= ", '$value'";
		}
		$a = substr( $a, 1 );
		$b = substr( $b, 1 );
		f_MQuery( "INSERT INTO {$this->table} ( $a ) VALUES ( $b )" );
	}
};

function conv_utf( $msg )
{
    $msg2 = iconv("UTF-8", "CP1251", $msg );
    if( $msg2 === false || $msg != iconv("CP1251", "UTF-8", $msg2 ) )
    	$msg = HtmlSpecialChars( $msg );
    else $msg = HtmlSpecialChars( $msg2 );
    return $msg;
}

function sendMessage( $pid, $a )
{
	global $test_server;
	if( $test_server ) return;
	// ---------------------
    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
	socket_set_option( $sock, SOL_SOCKET, SO_REUSEADDR, 1 );
    socket_connect($sock, "127.0.0.1", 1100);
    $tm = date( "H:i", time( ) );
    $msg = "say\n{$a}\n0\n{$pid}\n0\n{$tm}\n";
    socket_write( $sock, $msg, strlen($msg) ); 
    socket_close( $sock );
    // ---------------------
}

function glashSay( $msg )
{
	if( $test_server )LogError( $msg );
	else
	{
		$st = $msg;
        // ---------------------
        $plr = new Player( 69055 );
        $plr->UploadInfoToJavaServer( );

        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_connect($sock, "127.0.0.1", 1100);
        $tm = date( "H:i", time( ) );
        $st = "say\n{$st}\n69055\n0\n0\n{$tm}\n";
        socket_write( $sock, $st, strlen($st) ); 
        socket_close( $sock );
        // ---------------------
	}
}

function echoItemsList( $seat_res )
{
	global $player;
   	echo "<table cellspacing=0 cellpadding=0><tr>";

	foreach( $seat_res as $item_id => $number )
	{
		$iarr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$item_id" ) );
		echo "<td>&nbsp;</td><td background=images/items/bg.gif><a title='$iarr[name]' href=help.php?id=1010&item_id=$item_id target=_blank><img width=50 height=50 border=0 src=images/items/$iarr[image]></a></td>";
	}
	echo "</tr><tr>";
	foreach( $seat_res as $item_id => $number )
	{
		echo "<td>&nbsp;</td><td align=center><b><small>";
		$phas = $player->NumberItems( $item_id );
		if( $phas < $number ) echo "<font color=darkred>$phas</font>";
		else echo $phas;
		echo "</small>/$number</b></td>";
	}

	echo "</tr></table>";
}

function AjaxStr( $str )
{
	return str_replace( "\n", "\\n", str_replace( "\r", "", addslashes( $str ) ) );
}

//mb_internal_encoding( "cp1251" );

?>
