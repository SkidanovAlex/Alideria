<?

if( !isset( $mid_php ) ) die( );

if( !isset( $external ) ) echo "<b>Логи Управления Магазином Ордена</b> - <a href=game.php?order=shop_log>Логи Магазина</a> - <a href=game.php?order=main>Назад</a><br>";

if( 0 == ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_CONTROL_SHOP ) )
{
	echo( "У вас нет прав работать с этим разделом Ордена.<br><a href=game.php?order=main>Назад</a>" );
	return;
}

$page = $_GET['p'];
settype( $page, 'integer' );
if( $page < 0 ) $page = 0;
$start = $page * 20;

$res = f_MQuery( "SELECT shop_control_logs.* FROM shop_control_logs INNER JOIN shops ON shop_control_logs.shop_id=shops.shop_id WHERE owner_id=$clan_id ORDER BY shop_control_logs.time DESC LIMIT $start, 21" );

if( f_MNum( $res ) == 0 )
{
	echo "<i>Логи пусты</i>";
	return;
}

echo "<table><tr><td>";

echo "<table style='border:1px solid black'>";

$i = 0;
while( $i < 20 && $arr = f_MFetch( $res ) )
{                  
	++ $i;
	$plr = new Player( $arr['player_id'] );
	echo "<tr><td valign=top><script>document.write( ".$plr->Nick( )." );</script><br>".date( "d.m.Y H:i", $arr['time'] )."</td><td valign=top>";
	echo $arr['descr'];
	echo "</td></tr>";
}

echo "</table>";

$arr = f_MFetch( $res );

if( $external ) $script_name = "shop_control_logs.php?log1=2&";
else $script_name = "game.php?order=shop_control_log&";

echo "<table width=100%><tr><td align=left>";
if( $page > 0 ) echo "<a href={$script_name}p=".($page-1).">Предыдущая страница</a> ";
else echo "Предыдущая страница";
echo "</td><td align=right>";
if( $arr ) echo "<a href={$script_name}p=".($page+1).">Следующая страница</a> ";
else echo "Следующая страница";
echo "</td></tr></table>";

echo "</td></tr></table>";

?>
