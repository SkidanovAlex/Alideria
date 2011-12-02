<?

header("Content-type: text/html; charset=windows-1251");

include_once( "../no_cache.php" );
include_once( "../functions.php" );
include_once( "../player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$res = f_MQuery( "SELECT talk_id FROM player_talks WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( !$arr || $arr[0] != 242 ) die( );

if( $player->HasTrigger( 71 ) || $player->HasTrigger( 72 ) ) die( );

$val = $player->GetQuestValue( 30 );
$dist = $player->GetQuestValue( 31 );
if( !$val ) die( );
-- $val;

$ltr = iconv("UTF-8", "CP1251", $HTTP_RAW_POST_DATA );

if( $ltr == 'ё' || $ltr == 'Ё' ) $ltr = 'Е';
if( $ltr == 'я' ) $ltr = 'Я';
if( $ltr == 'ч' ) $ltr = 'Ч';

$ltr = mb_strtoupper( $ltr );

$res = f_MQuery( "SELECT f FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

$quiz_ans = array( "ВОРОБЕЙ", "УТОПИЯ", "АСТАНИЭЛЬ", "ЛИСОЕЖ", "ГОВОРУН", "ПЛАМЕНИ" );

$st = $arr[0];
$rt = $quiz_ans[$val];

$ok = false;
$ok2 = false;
for( $i = 0; $i < strlen( $st ); ++ $i )
{
	if( $st[$i] == '.' && $rt[$i] == $ltr )
	{
		$st[$i] = $rt[$i];
		$ok = true;
	}
	if( $st[$i] == '.' ) $ok2 = true;
}

if( !$ok )
{
	echo "alert( 'Такой буквы нет, заяц удаляется от клетки' );";
	$dist += 2;
}
else
{
	echo "alert( 'Похоже, такая буква в слове есть, заяц подошел к клетке ближе' );";
	$dist -= 2;
	f_MQuery( "UPDATE player_mines SET f='$st' WHERE player_id={$player->player_id}" );
}
if( $dist <= 0 ) { $player->SetTrigger( 72, 1 ); $st = $rt; }

$player->SetQuestValue( 31, $dist );
echo "word( '$st', $dist );";

if( !$ok2 && $dist > 0 )  $player->SetTrigger( 71, 1 );

?>
