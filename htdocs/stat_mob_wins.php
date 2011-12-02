<?

$entries_per_page = 40;

include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$player_id = $HTTP_GET_VARS['player_id'];

if( !$player_id ) $player_id = $player->player_id;

settype( $player_id, 'integer' );

$res = f_MQuery( "SELECT login FROM characters WHERE player_id = $player_id" );
$arr = f_MFetch( $res );

if( !$arr ) die( 'Нет такого игрока' );

$login = $arr[0];

if( $player_id != $player->player_id )
{
	$res = f_MQuery( "SELECT * FROM player_ranks WHERE player_id = {$player->player_id}" );
	if( !mysql_num_rows( $res ) ) die( 'У вас недостаточно прав для просмотра этой страницы' );
	$arr = f_MFetch( $res );
	if( $arr[rank] != 1 && $arr[rank] != 5 && $arr[rank] != 2 ) die( 'У вас недостаточно прав для просмотра этой страницы' );
}

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">


<?

print( "<center><br>Информация о статистике побед над мобами игроком <b>$login</b><br><br>" );

$res = f_MQuery("SELECT m.name, w.wins FROM mobs as m, mob_wins as w WHERE w.player_id=$player_id AND m.mob_id=w.mob_id");
echo "<center><table border=1>";
echo "<tr><td>Имя моба</td><td>Количество побед</td></tr>";

while ($arr = f_MFetch($res))
{
	echo "<tr><td>$arr[0]</td><td>$arr[1]</td></tr>";
}

echo "</table></center>";

?>
