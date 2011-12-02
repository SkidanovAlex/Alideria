<?

if( !isset( $mid_php ) ) die( );

if( !isset( $external ) ) echo "<b>Логи Магазина Ордена</b> - <a href=game.php?order=shop_control_log>Логи Управления Магазином</a> - <a href=game.php?order=main>Назад</a><br>";

if( 0 == ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_CONTROL_SHOP ) )
{
	echo( "У вас нет прав работать с этим разделом Ордена<br><a href=game.php?order=main>Назад</a>" );
	return;
}

$page = $_GET['p'];
settype( $page, 'integer' );
if( $page < 0 ) $page = 0;
$start = $page * 20;

$res = f_MQuery( "SELECT shop_log.* FROM shop_log INNER JOIN shops ON shop_log.shop_id=shops.shop_id WHERE owner_id=$clan_id ORDER BY shop_log.entry_id DESC LIMIT $start, 21" );

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
	echo "<tr><td>".date( "d.m.Y H:i", $arr['timestamp'] )."</td><td><script>document.write( ".$plr->Nick( )." );</script></td><td>";
	if( $arr['number'] > 0 ) echo "<font color=darkred>Продал</font>";
	else echo "<font color=green>Купил</font>";
	echo "</td><td>";
	$iarr = f_MFetch( f_MQuery( "SELECT name FROM items WHERE item_id=$arr[item_id]" ) );
	echo "[".abs( $arr['number'] )."] ".$iarr[0];
	echo "</td><td><img width=11 height=11 src=images/money.gif> ";
	if( $arr['money'] > 0 ) echo "<font color=green>+$arr[money]</font>";
	else echo "<font color=darkred>$arr[money]</font>";
	echo "</td></tr>";
}

echo "</table>";

$arr = f_MFetch( $res );
                       
echo "<table width=100%><tr><td align=left>";

if( $external ) $script_name = "shop_control_logs.php?log1=1&";
else $script_name = "game.php?order=shop_log&";

if( $page > 0 ) echo "<a href={$script_name}p=".($page-1).">Предыдущая страница</a> ";
else echo "Предыдущая страница";
echo "</td><td align=right>";
if( $arr ) echo "<a href={$script_name}p=".($page+1).">Следующая страница</a> ";
else echo "Следующая страница";
echo "</td></tr></table>";

echo "</td></tr></table>";

?>
