<?

if( !isset( $mid_php ) ) die( );

$stats = $player->getAllAttrNames( );

$tm = time( );
$res = f_MQuery( "SELECT expires FROM player_warehouse WHERE player_id = {$player->player_id} AND expires > $tm" );
$arr = f_MFetch( $res );

if( isset( $_GET['do_buy'] ) )
{
	if( $player->SpendMoney( 200 ) )
	{
		$player->AddToLogPost( 0, -200, 12 );
    	if( !$arr ) $expires = time( ) + 604800;
    	else $expires = $arr[0] + 604800;

    	f_MQuery( "LOCK TABLES player_warehouse WRITE" );
    	f_MQuery( "DELETE FROM player_warehouse WHERE player_id = {$player->player_id}" );
    	f_MQuery( "INSERT INTO player_warehouse ( player_id, expires ) VALUES ( {$player->player_id}, $expires )" );
    	f_MQuery( "UNLOCK TABLES" );
    	
    	include_once( "quest_race.php" );
    	updateQuestStatus ( $player->player_id, 2512 );

    	die( "<script>location.href='game.php';</script>" );
    } else $player->syst( "У вас недостаточно денег" );
}

if( !$arr )
{
	echo "<br>В хранилище можно оставить часть ваших вещей.<br>";
	echo "Услуги хранилища платные и стоят <b>200</b> <img width=11 height=11 src=images/money.gif> в неделю.<br>";
	echo "<ul><li><a href=# onclick='if(confirm(\"Вы уверены, что хотите купить место в хранилище на одну неделю за 200 дублонов?\"))location.href=\"game.php?do_buy=1\"'>Купить место в хранилище</a></ul>";
}
else
{
    if( isset( $_GET['do_take'] ) )
    {
    	include_js( "js/items_renderer1.js" );
    	echo "<script>function doWarehouse(id){query('warehouse_ref.php',''+id+'|-'+document.getElementById('place'+id).value);}</script>";
    	echo "<ul><li><a href=game.php>Назад</a></ul>";
		$res = f_MQuery( "SELECT items.*,player_warehouse_items.number FROM player_warehouse_items,items WHERE player_id={$player->player_id} AND items.item_id=player_warehouse_items.item_id" );
		echo "<script>\n";
		echo "item_err = 'В хранилище нет ни одной вещи';";
    	while( $arr = f_MFetch( $res ) )
    	{
    		echo "add_item( $arr[item_id], $arr[type], '$arr[name]', '".itemImage( $arr )."', '".itemFullDescr( $arr )."', $arr[number] );\n";
    	}
		echo "document.write( render_items( true, 'doWarehouse' ) );\n";
		echo "</script>\n";
    }
    else if( isset( $_GET['do_put'] ) )
    {
    	include_js( "js/items_renderer1.js" );
    	echo "<script>function doWarehouse(id){query('warehouse_ref.php',''+id+'|'+document.getElementById('place'+id).value);}</script>";
    	echo "<ul><li><a href=game.php>Назад</a></ul>";
		$res = f_MQuery( "SELECT items.*,player_items.number FROM player_items,items WHERE player_id={$player->player_id} AND weared=0 AND items.item_id=player_items.item_id" );
		echo "<script>\n";
    	while( $arr = f_MFetch( $res ) )
    	{
    		echo "add_item( $arr[item_id], $arr[type], '$arr[name]', '".itemImage( $arr )."', '".itemFullDescr( $arr )."', $arr[number] );\n";
    	}
		echo "document.write( render_items( true, 'doWarehouse' ) );\n";
		echo "</script>\n";
    }
    else
    {
		$str = date( "d.m.Y H:i", $arr[0] );
		echo "<br>Вы оплатили услуги хранилища до: <b>$str</b><br>";
		echo "Это еще <b>".my_time_str( $arr[0] - time( ) )."</b><br>";
		echo "Вы можете продлить это время на неделю за <b>200</b> <img width=11 height=11 src=images/money.gif>.<br>";
		echo "<ul><li><a href=# onclick='if(confirm(\"Вы уверены, что хотите продлить место в хранилище на одну неделю за 200 дублонов?\"))location.href=\"game.php?do_buy=1\"'>Продлить место в хранилище</a><br><br>";


    	echo "<li><a href=game.php?do_put=1>Положить вещи в хранилище</a>";
    	echo "<li><a href=game.php?do_take=1>Взять вещи из хранилища</a>";
    	echo "</ul>";
    	$arr = f_MFetch( f_MQuery( "SELECT sum( i.weight * w.number ) FROM items as i INNER JOIN player_warehouse_items as w ON i.item_id=w.item_id WHERE w.player_id={$player->player_id}" ) );
    	$weight = $arr[0];
    	if( !$weight ) $weight = 0;
    	$weight /= 100.0;
    	echo "<br>Общий вес всех вещей в хранилище: <b>$weight</b><br>";
    }
}

?>
