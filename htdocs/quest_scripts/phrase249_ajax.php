<?

header("Content-type: text/html; charset=windows-1251");

include_once( "../no_cache.php" );
include_once( "../functions.php" );
include_once( "../player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$res = f_MQuery( "SELECT talk_id FROM player_talks WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( !$arr || $arr[0] != 249 ) die( );

if( $player->HasTrigger( 74 ) ) die( );

$num = ( int ) $HTTP_RAW_POST_DATA;
$val = $player->GetQuestValue( 32 );

if( !$val )
{
	$val = mt_rand( 1000, 9999 );
	$player->SetQuestValue( 32, $val );
}

if( $num < 1000 || $num > 9999 )
	echo "alert( 'Не забывай! Шифр лежит в пределах от 1000 до 9999' );";
else if( !$player->SpendMoney( 30 ) )
{
	echo "alert( 'Для того, чтобы назвать число, необходимо 30 дублонов' );";
}
else if( $num == $val )
{
	echo "alert( 'Сундук с треском открывается...' );";
	$player->SetTrigger( 74, 1 );
	echo "location.href='game.php?phrase=585';";
}
else if( $num < $val )
{
	echo "alert( 'Верный шифр больше, чем $num' );";
}
else if( $num > $val )
{
	echo "alert( 'Верный шифр меньше, чем $num' );";
}

echo "update_money( {$player->money}, {$player->umoney} );";

?>
_( 'num' ).value = '';
