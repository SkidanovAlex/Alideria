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
    } else $player->syst( "� ��� ������������ �����" );
}

if( !$arr )
{
	echo "<br>� ��������� ����� �������� ����� ����� �����.<br>";
	echo "������ ��������� ������� � ����� <b>200</b> <img width=11 height=11 src=images/money.gif> � ������.<br>";
	echo "<ul><li><a href=# onclick='if(confirm(\"�� �������, ��� ������ ������ ����� � ��������� �� ���� ������ �� 200 ��������?\"))location.href=\"game.php?do_buy=1\"'>������ ����� � ���������</a></ul>";
}
else
{
    if( isset( $_GET['do_take'] ) )
    {
    	include_js( "js/items_renderer1.js" );
    	echo "<script>function doWarehouse(id){query('warehouse_ref.php',''+id+'|-'+document.getElementById('place'+id).value);}</script>";
    	echo "<ul><li><a href=game.php>�����</a></ul>";
		$res = f_MQuery( "SELECT items.*,player_warehouse_items.number FROM player_warehouse_items,items WHERE player_id={$player->player_id} AND items.item_id=player_warehouse_items.item_id" );
		echo "<script>\n";
		echo "item_err = '� ��������� ��� �� ����� ����';";
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
    	echo "<ul><li><a href=game.php>�����</a></ul>";
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
		echo "<br>�� �������� ������ ��������� ��: <b>$str</b><br>";
		echo "��� ��� <b>".my_time_str( $arr[0] - time( ) )."</b><br>";
		echo "�� ������ �������� ��� ����� �� ������ �� <b>200</b> <img width=11 height=11 src=images/money.gif>.<br>";
		echo "<ul><li><a href=# onclick='if(confirm(\"�� �������, ��� ������ �������� ����� � ��������� �� ���� ������ �� 200 ��������?\"))location.href=\"game.php?do_buy=1\"'>�������� ����� � ���������</a><br><br>";


    	echo "<li><a href=game.php?do_put=1>�������� ���� � ���������</a>";
    	echo "<li><a href=game.php?do_take=1>����� ���� �� ���������</a>";
    	echo "</ul>";
    	$arr = f_MFetch( f_MQuery( "SELECT sum( i.weight * w.number ) FROM items as i INNER JOIN player_warehouse_items as w ON i.item_id=w.item_id WHERE w.player_id={$player->player_id}" ) );
    	$weight = $arr[0];
    	if( !$weight ) $weight = 0;
    	$weight /= 100.0;
    	echo "<br>����� ��� ���� ����� � ���������: <b>$weight</b><br>";
    }
}

?>
