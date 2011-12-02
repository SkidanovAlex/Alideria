<?
header("Content-type: text/html; charset=windows-1251");

include_once( "../no_cache.php" );
include_once( "../functions.php" );
include_once( "../player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

//have finished this quest today
if ( $player->HasTrigger( 101 ) ) die( );
//have any task today

if ( $player->GetQuestValue( 102 ) > 0 ) die( );

$res = f_MQuery( "SELECT talk_id FROM player_talks WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( !$arr || $arr[0] != 259 ) die( );

$play_cost = $player->level * 10;
//$player->AddMoney( 10000 );
if ( !( $player->SpendMoney( $play_cost ) ) )
{
	$textt = "<br><i>К сожалению, у вас нет $play_cost монет...</i><br><br><ul><li><a href=game.php?phrase=610>Уйти</a></ul>";
	echo "talk_out(\"$textt\");";
	return;
}

require_once( 'zhorik_quest_add.php' );
global $prizes;
global $zh_quests;
$zh_questsn = count( $zh_quests );

//task is not completed
$player->SetTrigger( 102, 0 );
//set deadline
$sec_in_day = 24 * 60 * 60;
$deadline = ( (int)( time( ) / $sec_in_day + 1 ) ) * $sec_in_day; //new deadline

//
$player->SetQuestValue( 101, (int)$deadline );
//

//new quest from zhorik
$task_id = mt_rand( 1, $zh_questsn );

//
$player->SetQuestValue( 102, (int)$task_id );
//

//include items

require_once( '../kopka.php' );
$kopka = new Kopka( );
foreach ( $prizes as $key => $val )
{
	$kopka->AddItem( $key, $val );
}

$av_income_value = $player->level * AVERAGE_INCOME_PER_LVL;

$rolls = array( );
for ( $i = 0; $i < 3; ++ $i )
{
	$kopka->GetItemId( 3600, $av_income_value );
	$rolls[$i] = $kopka->item_id;
}
$item_src = array( );
$item_name = array( );

$inStr = "('{$rolls[0]}','{$rolls[1]}','{$rolls[2]}')";

$res = f_MQuery( "SELECT name, image, item_id FROM items WHERE item_id IN {$inStr};" );
while ( $arr = f_MFetch( $res ) )
{
	$it_id = $arr[2];
	$item_src[$it_id] = $arr[1];
	$item_name[$it_id] = $arr[0];
}

for ( $i = 0; $i < 3; ++ $i )
{
	echo "pict{$i} = new Image( 50, 50 );";
	echo "pict{$i}.src = 'images/items/{$item_src[$rolls[$i]]}';";
}
//echo "alert('ok');";

//prize
$win_uin = $rolls[mt_rand( 0, 2 )];
$player->SetQuestValue( 105, $win_uin );

$start_delay = 100;
$inter_start_delay = 1000;
$end_delay = 7000;
$inter_end_delay = 2000;
$anim_src = "images/misc/bandit_anim.gif";
echo "anim = new Image( 50, 50 );";
echo "anim.src = '$anim_src';";
//echo "alert( pict0.src );";
//draw anim
$start_text = "...";

echo "talk_out('$start_text');";

for ( $i = 0; $i < 3; ++ $i )
{
	echo "setTimeout( \"draw_r('$i','anim.src','Кручу-верчу');\", ". ( $start_delay + $inter_start_delay * $i). ");";
	echo "setTimeout( \"draw_r('$i','pict{$i}.src','{$item_name[$rolls[$i]]}');\", ". ( $end_delay + $inter_end_delay * $i ). ");";
}
$end_text = "<br><b>Жорик</b>: Твое задание на сей раз: <b>". get_current_task_text( $task_id ) ."</b>. Награда будет состоять из того, что тебе выпало. Правда, лишь из одного варианта, из какого именно, ты сможешь узнать только после того, как выполнишь задание. Удачи.<br><br><ul><li><a href=game.php?phrase=610>Отлично!</a></ul>";
echo "setTimeout( \"talk_out('$end_text');\", ". ( $end_delay + $inter_end_delay * 3 ) ." );";
/**/
?>
