<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "/*Неверные настройки Cookie*/" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$res = f_MQuery( "SELECT combat_id, side, ready FROM combat_players WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

if( !$arr )
{
	die( "Игрок не в бою" );
}

$combat_id=$arr[0];
$entry_id=(int)$_GET['a'];

f_MQuery( "LOCK TABLE combat_loot WRITE" );

$res = f_MQuery( "SELECT item_id, number, player_id, expires FROM combat_loot WHERE combat_id=$combat_id AND entry_id=$entry_id" );
$arr = f_MFetch( $res );

if( !$arr ) die( "alert( 'Эту вещь уже забрали' );" );
if( $arr['player_id'] && $arr['player_id'] != $player->player_id && $arr['expires'] > time( ) )
	die( "alert( 'В течение первых 15-ти секунд забрать вещи может только игрок, перед которым они упали' );" );

$item_id = $arr[0];
$number = $arr[1];

f_MQuery( "DELETE FROM combat_loot WHERE entry_id=$entry_id" );

f_MQuery( "UNLOCK TABLES" );

$arr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$item_id" ) );

if( $arr['name4'] == '' )  $arr['name4']  = $arr['name'];
if( $arr['name13'] == '' )  $arr['name13']  = $arr['name'];
if( $arr['name2_m'] == '' )  $arr['name2_m']  = $arr['name'];
$st = "<b>".$player->login."</b> забирает ".my_word_form2( $number, $arr['name4'], $arr['name13'], $arr['name2_m'] )."<br>";

$player->AddItems( $item_id, $number );
$player->AddToLogPost( $item_id, $number, 27 );
f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( $combat_id, '$st' )" );

// Widow quest
include_once( "quest_race.php" );
updateQuestStatus ( $player->player_id, 2505 );

?>
query('combat_ref.php','take');
