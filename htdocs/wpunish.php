<?

include( 'functions.php' );
include( 'player.php' );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">

<?

if( $player->Rank( ) != 1 ) die( 'У вас недостаточно прав для просмотра этой страницы' );

$player_login = $_GET['nick'];
$player_login = htmlspecialchars( $player_login, ENT_QUOTES );

$res = f_MQuery( "SELECT player_id FROM characters WHERE login = '$player_login'" );
if( !mysql_num_rows( $res ) ) die( "Нет такого игрока." );

$arr = f_MFetch( $res );

$target = new Player( $arr[0] );
$target->syst2( '/punish' );

?>
