<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "magic_functions.php" );
include_once( "waste_stats.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

function AddAnimation( $a, $b, $c, $d, $e=0 )
{   
	global $me;
	global $he;

	f_MQuery( "INSERT INTO magic_animation ( player_id, slot_r, slot1, slot2, card_id, alpha ) VALUES ( $me->player_id, $a, $b, $c, $d, $e )" );
	if( $a < 7 ) $a = 18;
	if( $b < 7 ) $b = 18;
	if( $c < 7 ) $d = 100;
	if( $c < 7 ) $c = 18;
	f_MQuery( "INSERT INTO magic_animation ( player_id, slot_r, slot1, slot2, card_id, alpha ) VALUES ( $he->player_id, $a, $b, $c, $d, $e )" );
}

$player_id = $HTTP_COOKIE_VARS['c_id'];

f_MQuery( "LOCK TABLE magic_players WRITE" );
$res = f_MQuery( "SELECT * FROM magic_players WHERE player_id=$player_id" );
$arr = f_MFetch( $res );

if( !$arr || $arr['status'] != 0 )
{
	f_MQuery( "UNLOCK TABLES" );
	die( "alert( 'Игра уже завершилась.' );" );
}

if( !$arr || !$arr['my_turn'] )
{
	f_MQuery( "UNLOCK TABLES" );
	die( "alert( 'Сейчас не ваш ход. Подождите оппонента.' );" );
}


$game_id = $arr['game_id'];
$turn_begin = $arr['turn_begin'];

f_MQuery( "UPDATE magic_players SET my_turn = 0, turn_begin = 0 WHERE player_id=$player_id" );
f_MQuery( "UNLOCK TABLES" );

$card_id = $_GET['id'];
settype( $card_id, 'integer' );

if( $card_id < 0 || $card_id >= 7 ) die( );

$res = f_MQuery( "SELECT player_id FROM magic_players WHERE game_id = $arr[game_id] AND player_id <> $player_id" );
$arr = f_MFetch( $res );

if( !$arr ) RaiseError( "В игре $arr[game_id] участвует только один игрок" );

$me = new Magician( $player_id );
$he = new Magician( $arr['player_id'] );

$place_id = $card_id;
$card_id = $me->cards[$card_id];
$mana = 0;

echo "/* Card: $card_id Mana: ".$manacost[$card_id].";*/";

if( $card_id < 28 ) $mana = $me->wm;
else if( $card_id < 59 ) $mana = $me->fm;
else $mana = $me->nm;

$do_not_cast = false;

if( $manacost[$card_id] > $mana ) 
{
//	f_MQuery( "UPDATE magic_players SET my_turn = 1 WHERE player_id=$player_id" );
//	die( 'alert( "У вас не хватает маны на то, чтобы разыграть эту карту." );' );
	$do_not_cast = true;;
}

if( !$do_not_cast )
{
    if( $card_id < 28 ) $me->wm -= $manacost[$card_id];
    else if( $card_id < 59 ) $me->fm -= $manacost[$card_id];
    else $me->nm -= $manacost[$card_id];
}

if( $turn_begin )
{
	$res = f_MQuery( "SELECT card_id, player_id, alpha FROM magic_cards WHERE game_id=$game_id AND player_id < 0 ORDER BY player_id DESC" );
	while( $arr = f_MFetch( $res ) )
	{
		f_MQuery( "UPDATE magic_cards SET player_id=0 WHERE game_id=$game_id AND card_id=$arr[card_id]" );
		AddAnimation( 7 - $arr['player_id'] - 1, 7 - $arr['player_id'] - 1, 17, $arr['card_id'], $arr['alpha'] );
	}
	$slot = 1;
}
else
{
    $res = f_MQuery( "SELECT min( player_id ) FROM magic_cards WHERE game_id=$game_id" );
    $arr = f_MFetch( $res );
    $slot = 1 - $arr[0];
}

AddAnimation( $place_id, $place_id, 7 + $slot - 1, $card_id, $do_not_cast?1:0 );

$res = f_MQuery( "SELECT card_id FROM magic_cards WHERE game_id=$game_id AND player_id=0 ORDER BY rand()" );
$arr = f_MFetch( $res );
$new_id = $arr[0];

AddAnimation( 7 + $slot - 1, 17, $place_id, $new_id );

f_MQuery( "DELETE FROM magic_cards WHERE game_id=$game_id AND card_id=$new_id" );
$me->cards[$place_id] = $new_id;

f_MQuery( "UPDATE magic_cards SET player_id=-$slot, alpha=0 WHERE game_id=$game_id AND card_id=$card_id" );
if( $do_not_cast ) f_MQuery( "UPDATE magic_cards SET alpha=1 WHERE game_id=$game_id AND card_id=$card_id" );

$tm = time( );
f_MQuery( "UPDATE magic SET last_turn_made=$tm WHERE game_id=$game_id" );

$money_a = f_MFetch( f_MQuery( "SELECT money FROM magic WHERE game_id=$game_id" ) );
$money = $money_a[0];

if( !$do_not_cast && magicCard( $card_id, $me, $he ) )
{
	$me->RemoveNegativeVals( );
	$he->RemoveNegativeVals( );

	if( $me->won( $he ) )
	{
		f_MQuery( "UPDATE magic_players SET status=1 WHERE player_id={$me->player_id}" );
		f_MQuery( "UPDATE magic_players SET status=2 WHERE player_id={$he->player_id}" );
		storeGame( 0, $me->player_id, $he->player_id, $money, true );
	}
	else if( $he->won( $me ) )
	{
		f_MQuery( "UPDATE magic_players SET status=1 WHERE player_id={$he->player_id}" );
		f_MQuery( "UPDATE magic_players SET status=2 WHERE player_id={$me->player_id}" );
		storeGame( 0, $he->player_id, $me->player_id, $money, true );
	}
	else f_MQuery( "UPDATE magic_players SET my_turn = 1 WHERE player_id=$player_id" );
}
else
{
	$me->RemoveNegativeVals( );
	$he->RemoveNegativeVals( );
	if( $me->won( $he ) )
	{
		f_MQuery( "UPDATE magic_players SET status=1 WHERE player_id={$me->player_id}" );
		f_MQuery( "UPDATE magic_players SET status=2 WHERE player_id={$he->player_id}" );
		storeGame( 0, $me->player_id, $he->player_id, $money, true );
	}
	else if( $he->won( $me ) )
	{
		f_MQuery( "UPDATE magic_players SET status=1 WHERE player_id={$he->player_id}" );
		f_MQuery( "UPDATE magic_players SET status=2 WHERE player_id={$me->player_id}" );
		storeGame( 0, $he->player_id, $me->player_id, $money, true );
	}
	else
	{
    	$he->Process( );
    	f_MQuery( "UPDATE magic_players SET my_turn = 1, turn_begin = 1 WHERE player_id={$he->player_id}" );
	}	
}

$me->Store( );
$he->Store( );

echo "hru();";

?>
