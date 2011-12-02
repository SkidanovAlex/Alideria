<?

header("Content-type: text/html; charset=windows-1251");
include_once( "functions.php" );
include_once( "combat_interface.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
	
$a = $HTTP_GET_VARS['id'];
settype( $a, "integer" );

CombatSetCard( $HTTP_COOKIE_VARS['c_id'], $a );

?>

query('combat_ref.php','ref');
