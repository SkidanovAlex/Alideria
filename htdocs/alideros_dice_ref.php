<?

$summ_v = Array( 4=>62, 31, 18, 12, 8, 7, 6, 6, 7, 8, 12, 18, 31, 62 );

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

if( $player->location != 2 || $player->depth != 8 )
{
	LogError( "Попытка поиграть в кубики в локации {$player->location} на глубине {$player->depth}" );
	die( );
}

$arr = @explode( "|", $HTTP_RAW_POST_DATA );
if( count( $arr ) == 0 || count( $arr ) > 140 ) die( );

$moo = Array( );
$stavka = 0;
$stavkas2 = 0;
for( $i = 0; $i < count( $arr ); ++ $i )
{
	$moo[$arr[$i]] = true;
	$stavka += 20;
	++ $stavka2;
}

$cres = f_MQuery( "SELECT stavkas FROM player_casino WHERE player_id = {$player->player_id}" );
$carr = f_Mfetch( $cres );
if( $carr ) $left = 0;
else $left = $carr[0];
$left = 45 - $left;

if( $left < $atavka2 ) die( 'alert( "Вы не можете сделать так много ставок" );' );

if( !$carr ) f_MQuery( "INSERT INTO player_casino ( player_id, stavkas ) VALUES ( {$player->player_id}, $stavka2 )" );
else f_MQuery( "UPDATE player_casino SET stavkas = stavkas + $stavka2 WHERE player_id={$player->player_id}" );

if( !$player->SpendMoney( $stavka ) ) die( 'alert( "У вас не хватает денег на выбранные ставки" );' );
$player->AddToLogPost( 0, -$stavka, 5, 0 );

$a = mt_rand( 1, 6 );
$b = mt_rand( 1, 6 );
$c = mt_rand( 1, 6 );

$winnings = 0;

$tripple = false;
if( $a == $b && $b == $c ) $tripple = true;

$double = 0;
if( $a == $b ) $double = $a;
if( $b == $c ) $double = $b;
if( $c == $a ) $double = $c;

$sum = $a + $b + $c;
if( $sum >= 4 && $sum <= 10 && $moo[0] && !$tripple ) $winnings += 2 * 20;
if( $sum >= 11 && $sum <= 17 && $moo[1] && !$tripple ) $winnings += 2 * 20;

if( $double != 0 && $moo[$double + 1] ) $winnings += 12 * 20;

if( $tripple && $moo[$a + 7] ) $winnings += 181 * 20;
if( $tripple && $moo[14] ) $winnings += 32 * 20;

if( $sum != 3 && $sum != 18 && $moo[$sum + 11] ) $winnings += ( $summ_v[$sum] + 1 ) * 20;

$id = 29;
for( $i = 1; $i <= 6; ++ $i ) for( $j = $i + 1; $j <= 6; ++ $j )
{
	if( $moo[$id] )
		if( $a == $i || $b == $i || $c == $i )
			if( $a == $j || $b == $j || $c == $j )
				$winnings += 7 * 20;
	++ $id;
}

for( $i = 1; $i <= 6; ++ $i ) if( $moo[$i + 43] )
{
	if( $tripple && $a == $i ) $winnings += 4 * 20;
	else if( $double == $i ) $winnings += 3 * 20;
	else if( $a == $i || $b == $i || $c == $i ) $winnings += 2 * 20;
}

echo "alert( 'Выпали числа $a, $b, $c. Сумма $sum\\nСтавка: $stavka дублонов\\nВыигрыш: $winnings дублонов\\nИтого: ".($winnings-$stavka)." дублонов' );";
$player->AddToLog( 0, $winnings, 5, 0 );
$player->AddMoney( $winnings );
f_MQuery( "UPDATE statistics SET casino_balance = casino_balance - $winnings + $stavka" );
echo "update_money( $player->money, $player->umoney );";
echo "fin( );";

?>

