<?

header("Content-type: text/html; charset=windows-1251");

include( 'functions.php' );
include( 'player.php' );

$str = $HTTP_RAW_POST_DATA;

list($id, $num) = @explode( "|", $str );

settype( $id, 'integer' );
settype( $num, 'integer' );

if( $num < 0 ) die( );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
$d = false;
if ($player->location >=100 && $player->location <=200)
{
	$d = true;
	$cell_num = f_MValue("SELECT cell_num FROM dungeon_players WHERE player_id=".$player->player_id);
}
if ($d)
	$res = f_MQuery("SELECT number FROM dungeon_items WHERE group_number={$player->depth} AND cell_num=$cell_num AND item_id=".$id);
else
	$res = f_MQuery( "SELECT number FROM location_items WHERE location = {$player->location} AND depth = {$player->depth} AND item_id = $id" );
if( !f_MNum( $res ) ) die( "alert( 'Вероятно, вещь забрали до вас' );" );

$arr = f_MFetch( $res );
$ammount = $arr[0];

if( $ammount == $num )
	if ($d)
		f_MQuery("DELETE FROM dungeon_items WHERE group_number={$player->depth} AND cell_num=$cell_num AND item_id=".$id);
	else
		f_MQuery( "DELETE FROM location_items WHERE location = {$player->location} AND depth = {$player->depth} AND item_id = $id" );
else if( $ammount > $num )
	if ($d)
		f_MQuery("UPDATE dungeon_items SET number=number - $num WHERE group_number={$player->depth} AND cell_num=$cell_num AND item_id=".$id);
	else
		f_MQuery( "UPDATE location_items SET number = number - $num WHERE location = {$player->location} AND depth = {$player->depth} AND item_id = $id" );
else die( "alert( 'Вероятно, вещь забрали до вас' );" );

$player->AddToLog( $id, $num, 4, $player->location, $player->depth );
$player->AddItems( $id, $num );

$num = $ammount - $num;
print( "set_loc_items( $id, $num );" );

?>
