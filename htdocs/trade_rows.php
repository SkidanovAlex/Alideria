<?

if( !isset( $mid_php ) ) die( );

if( $player->level < 3 ) { echo( "<center><i>Эта локация доступна только с третьего уровня развития персонажа</i></center>" ); return; }

$id = $_GET['shop_id'];
settype( $id, 'integer' );
$sres = f_MQuery( "SELECT * FROM shops WHERE ( regime <> 3 OR owner_id={$player->clan_id} ) AND location = $loc AND place = $depth AND shop_id=$id" );
if( mysql_num_rows( $sres ) )
{
	$sarr = f_MFetch( $sres );
	print( "<b>$sarr[name]</b>" );
	if( $player->IsShopOwner( $sarr[shop_id] ) ) 
	{	
		print( "&nbsp;-&nbsp;<a target=_blank href=shop_controls.php?shop_id=$sarr[shop_id]>Управление</a>" );
		print( "&nbsp;-&nbsp;<a target=_blank href=shop_control_logs.php?log1=1>Логи Магазина</a>" );
		print( "&nbsp;-&nbsp;<a target=_blank href=shop_control_logs.php?log1=2>Логи Управления</a>" );
	}

	print( "&nbsp;-&nbsp;<a href=game.php>Вернуться к списку</a>" );
	print( "<br>" );

	include( "shop.php" );
	$stats = $player->getAllAttrNames( );
	$shop = new Shop( $sarr[shop_id] );
	$shop->ShowGoods( "position" );			
	
	print( "<iframe width=0 height=0 id=shop_ref name=shop_ref></iframe>" );
}
else
{
	echo "<b>Палатки Орденов</b><br>";
	$res = f_MQuery( "SELECT shops.*, clans.icon FROM shops, clans WHERE ( regime <> 3 OR owner_id={$player->clan_id} ) AND shops.owner_id=clans.clan_id AND location = $loc AND place = $depth ORDER BY clans.glory DESC, clans.clan_id" );
	
	echo "<ul>";
	while( $arr = f_MFetch( $res ) )
		echo "<li><img width=18 height=13 src=images/clans/$arr[icon]> <a href=game.php?shop_id=$arr[shop_id]>$arr[name]</a>";
	echo "</ul>";
}

?>
