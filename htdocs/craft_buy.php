<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$id = $_GET['id'];
settype( $id );

$res = f_MQuery( "SELECT count( recipe_id ) FROM player_recipes WHERE player_id={$player->player_id} AND recipe_id=$id" );
$arr = f_MFetch( $res );

if( $arr[0] > 0 ) die( "alert( 'Вы уже знаете этот рецепт' );" );

$res = f_MQuery( "SELECT level FROM recipes WHERE recipe_id=$id" );
$arr = f_MFetch( $res );

if( !$arr ) RaiseError( "Попытка выучить несуществующий рецепт", "$id" );

$item_level = $arr[0];
$price = 50 * pow( 2, (int)( ( 1 + $item_level ) / 2 ) ) * ( 1.5 - 0.5 * ( $item_level % 2 ) );

if( $player->SpendMoney( $price ) )
{
	f_MQuery( "INSERT INTO player_recipes( player_id, recipe_id ) VALUES ( {$player->player_id}, $id )" );
	$player->AddToLogPost( 0, -$price, 41, $id );
	echo( "alert( 'Рецепт успешно изучен' );" );
	echo "update_money( $player->money, $player->umoney );";
	echo "_( 'b$id' ).innerHTML = '&nbsp;';";
}
else die( "alert( 'У вас недостаточно дублонов' );" );

?>
