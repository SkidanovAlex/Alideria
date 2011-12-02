<?

die( );

require_once("no_cache.php");
require_once("player.php");
require_once("shop.php");
require_once("v_functions.php");

$link = MConnect();
$login = NoHackerz($LDKCookie1, $LDKCookie2);
$player = new Player($login,'login');
$shop = $player->Shop();
$shop_id = $shop->id
if(!$player->CheckForShop($shop)) exit;
	
$res = MQuery( "select place_In, cur_location, place_regime from users where uid={$player->id}" );
$arr = mysql_fetch_array( $res );

$shop_location = $cur_location;
$shop_place = $place_in;

if( $shop_place == 2 && $shop_location == 50 )
	$shop_price = 2000;
else if( $shop_place == 6 && $shop_location == 19 )
	$shop_price = 1000;
else if( $shop_place == 13 && $shop_location == 6 )
	$shop_price = 1500;
else die( );

$cap = $shop->capacity;
$shop_name = $shop->name;
$q = $cap * $shop_price;

$shop_id = $shop->id;

MQuery( "DELETE FROM shops WHERE shop_id=$shop_id" );
MQuery( "DELETE FROM shop_goods WHERE shop_id=$shop_id" );
$player->AlterMoney( $q );
$player->MesToPrivate1( "Вы продали магазин '" + $shop_name + "' и получили $q монет" );

?>

<script>
	window.close( );
</script>

