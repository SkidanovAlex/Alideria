<?

$numbers = Array( 0, 32, 15, 19, 4, 21, 2, 25, 17, 34, 6, 27, 13, 36, 11, 30, 8, 23, 10, 5, 24, 16, 33, 1, 20, 14, 31, 9, 22, 18, 29, 7, 28, 12, 35, 3, 26 );
$colors = Array( -1 );

for( $i = 0; $i < 36; ++ $i ) $colors[$numbers[$i + 1]] = $i % 2;

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

if( $player->location != 2 || $player->depth != 7 )
{
	LogError( "Попытка поиграть в рулетке в локации {$player->location} на глубине {$player->depth}" );
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
if( !$carr ) $left = 0;
else $left = $carr[0];
$left = 45 - $left;

if( $left < $stavka2 ) die( 'alert( "Вы не можете сделать так много ставок" );' );

if( !$carr ) f_MQuery( "INSERT INTO player_casino ( player_id, stavkas ) VALUES ( {$player->player_id}, $stavka2 )" );
else f_MQuery( "UPDATE player_casino SET stavkas = stavkas + $stavka2 WHERE player_id={$player->player_id}" );

if( !$player->SpendMoney( $stavka ) ) die( 'alert( "У вас не хватает денег на выбранные ставки" );' );
$player->AddToLogPost( 0, -$stavka, 5, 1 );

$number = mt_rand( 0, 36 );

$winnings = 0;
if( $moo[$number] ) $winnings += 20 * 36;
if( $colors[$number] == 0 && $moo[136] == 1 ) $winnings += 20 * 2;
if( $colors[$number] == 1 && $moo[137] == 1 ) $winnings += 20 * 2;
if( $number % 2 == 0 && $moo[135] == 1 ) $winnings += 20 * 2;
if( $number % 2 == 1 && $moo[138] == 1 ) $winnings += 20 * 2;
if( $number <= 18 && $moo[134] == 1 ) $winnings += 20 * 2;
if( $number >= 19 && $moo[139] == 1 ) $winnings += 20 * 2;
if( $number <= 12 && $moo[131] == 1 ) $winnings += 20 * 3;
if( $number >= 13 && $number <= 24 && $moo[132] == 1 ) $winnings += 20 * 3;
if( $number >= 25 && $moo[133] == 1 ) $winnings += 20 * 3;

// 12s
if( $number % 3 == 1 && $moo[36 + 34] == 1 ) $winnings += 20 * 3;
if( $number % 3 == 2 && $moo[36 + 35] == 1 ) $winnings += 20 * 3;
if( $number % 3 == 0 && $moo[36 + 36] == 1 ) $winnings += 20 * 3;

// cols
if( $moo[$number + 2 - ( $number + 2 ) % 3 + 72] ) $winnings += 20 * 12;

// vertical
if( $moo[$number + 72] && $number % 3 != 0 ) $winnings += 20 * 18;
if( $moo[$number + 71] && $number % 3 != 1 ) $winnings += 20 * 18;

// horizontal
if( $moo[$number + 36] && $number < 34 ) $winnings += 20 * 18;
if( $moo[$number + 33] && $number > 3 ) $winnings += 20 * 18;

// four
$n1 = 1;
$n2 = 2;
$n3 = 4;
$n4 = 5;
for( $val = 109; $val <= 130; ++ $val )
{
	if( $moo[$val] )
	{
		if( $n1 == $number || $n2 == $number || $n3 == $number || $n4 == $number )
			$winnings += 20 * 9;
	}
	if( $val % 2 == 1 )
	{
		++ $n1;
		++ $n2;
		++ $n3;
		++ $n4;
	}
	else
	{
		$n1 += 2;
		$n2 += 2;
		$n3 += 2;
		$n4 += 2;
	}
}

if( $number == 0 ) $winnings = 0;

/* квест старателей */
$qst = '';
if( $player->HasTrigger( 23 ) && !$player->HasTrigger( 24 ) )
{
	$qst .= "\\nВы выполняете задание гномуса.\\n";
	if( ($winnings-$stavka) > 0 )
	{
		$player->AlterQuestValue( 17, 1 );
		$qcnt = $player->GetQuestValue( 17 );
		$qst .= "Вы в плюсе, количество побед подряд становится ".$qcnt;
		if( $qcnt == 3 )
		{
			$qst .= "\\nПохоже, задание Гномуса выполнено. Вернитесь к нему и заберите бланк.";
			$player->SetTrigger( 24 );
			$player->SetTrigger( 23, 0 );
			$qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$player->player_id} AND quest_part_id = 40" );
			if( !mysql_num_rows( $qres ) )
			{
				$player->syst( "Информация о квесте <b>Бланк Старателей</b> обновлена.", false );
				f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$player->player_id}, 40 )" );
			}
		}
	}
	else
	{
		$qst .= "Вы не в плюсе, похоже победную серию придется начинать сначала";
		$player->SetQuestValue( 17, 0 );
	}
}
if( $winnings - $stavka > 0 )
	checkZhorik( $player, 16, 3 ); // квест жорика зарубить трижды в рулетку
/* квест конец */

echo "alert( 'Выпало число $number\\nСтавка: $stavka дублонов\\nВыигрыш: $winnings дублонов\\nИтого: ".($winnings-$stavka)." дублонов$qst' );";
$player->AddToLog( 0, $winnings, 5, 1 );
$player->AddMoney( $winnings );
f_MQuery( "UPDATE statistics SET casino_balance = casino_balance - $winnings + $stavka" );
echo "update_money( $player->money, $player->umoney );";
echo "fin( );";

?>

