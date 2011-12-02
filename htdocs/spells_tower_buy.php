<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "skin.php" );
include_once( "card.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

// array from spells_tower
$spell_ids = Array (

/* neutral */ 103, 109, 151,
               /* w */ /* n */ /* f */
    /* 2 */      152,    157,    162,
    /* 3 */      170,    175,    180,
    /* 4 */      193,    188,    202,
    /* 5 */      207,    212,    217

);

$id = $HTTP_RAW_POST_DATA;
settype( $id, 'integer' );

if( $id < 0 || $id >= count( $spell_ids ) )
{
	RaiseError( "Попытка выучить заклинание, которого нет в БТЗ", "$id" );
}
if( $player->regime != 0 )
{
	RaiseError( "Попытка выучить заклинание, будучи занятым", "$id" );
}

$spell_id = $spell_ids[$id];

$card = new Card( $spell_id );

$price = 50 * pow( (int)( ( 1 + $card->level ) / 2 ), 2 ) * ( 1.5 - 0.5 * ( $card->level % 2 ) );
if( $spell_id == 151 ) $price = 5000;


if( $player->money < $price )
	echo "alert( 'У вас не хватает денег на заклинание.' );";
else if( $card->playerCanLearn( $player->player_id ) )
{
	f_MQuery( "LOCK TABLE player_cards WRITE, cards WRITE" );
	$res = f_MQuery( "SELECT count( card_id ) FROM player_cards WHERE player_id={$player->player_id} AND ( card_id=$spell_id OR card_id IN ( SELECT card_id FROM cards WHERE parent = $spell_id ) )" );
	$arr = f_MFetch( $res );
	if( $arr[0] > 0 ) 
	{
		echo "alert( 'Этот свиток уже в вашей книге заклинаний!' );";
	}
	else
	{
		f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( {$player->player_id}, $spell_id, 10 )" );
		f_MQuery( "UNLOCK TABLES" );
		if( !$player->SpendMoney( $price ) )
		{
			LogError( "За 15 строк кода чувак где-то просрал $price Дублонов!!! И не расплатился за купленный свиток!" );
		}
		$player->AddToLogPost( 0, - $price, 17, $spell_id );
		echo "alert( 'Вы потратили $price дублонов и выучили заклинание {$card->name}!' );";
		echo "update_money( $player->money, $player->umoney );";
		echo "document.getElementById( 'towerbtn$id' ).innerHTML = '<i>Изучено</i>';";
	}
}
else echo "alert( 'Не все условия для изучения свитка соблюдены. Проверьте, достаточно ли хорошо вы знаете нужную магию, и вспомните, какой у вас уровень.' );";
    
?>
