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
if( !$arr || $arr[0] != 281 ) die( );

$id = $HTTP_RAW_POST_DATA;

settype( $id, 'integer' );

if( $id < -2 || $id >= 4 ) RaiseError( "Попытка выбрать неверный ответ в мире грез" );

$dead_line = $player->GetQuestValue( 115 );

f_MQuery( "LOCK TABLES player_mines WRITE, player_quest_values WRITE" );

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

$default_field = '3.......................';

if( !$arr )
{
	f_MQuery( "UNLOCK TABLES" );
	RaiseError( 'Нет начальных данных' );
}
else
{
	$st_f = $arr['f'];
	if ( $st_f[32] == '1' )
	{
		$st_f[32] = '0';
		$st_f[28] = '2';
	}
	$f = substr( $st_f, 0, 24 );
}
//echo "alert( '$f' );";
//$f = $default_field;
//f_MQuery( "LOCK TABLE player_mines WRITE" );

function to_num( $c )
{
	if ( $c == '.' )
		return -1;
	return ord( $c ) - ord( '0' );
}

//first answer is correct

$questions = array(

	array( "Что нужно, чтобы вывести Мая из себя?", array(
		"Писать в чате неграмотно, вести себя как невоспитанный юнец или девчонка.",
		"Забрать у Мая его любимую лисоежью виолончель.",
		"Зафлудить все творчество своими рассказами, бережно продумывая каждое слово и каждую букву.",
		"Здороваться с Маем вежливо каждый день." ) ),

	array( "Что нужно для того, чтобы Ishamael обратил на Вас внимание и Ваша просьба смогла бы быть им услышана?", array(
		"Четко, лаконично и по теме сформулировать свое обращение к нему в привате.",
		"Наорать на него, обозвав его любимый смайлик изобритением Сатаны, а его самого адептом зла.",
		"Обязательно попросить его добавить какие-то вещи.",
		"Терпеливо создать одну и ту же тему в каждом разделе форума." ) ),

	array( "Что нужно сделать, если Вы застряли в бою, на турнире или нашли какой либо баг?", array(
		"Создать соответствующую тему на форуме в «Ошибках игры» с полным описанием проблемы и скрином.",
		"Сразу же бежать к Пламени и забросать ему весь приват сообщениями о Вашей находке.",
		"Паниковать, материться в чат и брызгать слюной на монитор.",
		"Срочно перезагрузить компьютер, сплюнуть три раза и посыпать солью через плечо." ) ),

	array( "Если Вы недовольны апдейтом или каким либо нововведениями администрации, то что нужно делать?", array(
		"Попробовать разобраться во всем, не спеша, расспросить что и как, быть оптимистом, окружить себя позитивом и верой в светлое будущее.",
		"Сразу же угрожать тем, что Вы бросите игру и вообще все будет плохо.",
		"Материться, сподвигать остальных игроков на какие-то революционные действия и получить за это наказание.",
		"Найти номер телефона Ishamael и заспамить его смс-ками." ) ),

	array( "Если игрок-новичок в чате спрашивает какие-либо вопросы по игре и просит помощи, то что нужно сделать?", array(
		"Помочь, подсказать, объяснить все, что ему не ясно.",
		"Игнорировать, делая вид, что Вы самый занятой и важный игрок.",
		"Подшутить над ним, провести на Волчью Тропу в Западном лесу и оставить там. А что, хорошая ведь традиция.",
		"Отписать какой-то самый смешной смайлик, а лучше комбинацию из смайликов." ) ),

	array( "Что нужно сделать, чтобы Пламени ввел в игру дополнительные вещи?", array(
		"А ничего не делать. Пламени человек ответственный, как только сможет, так сразу и введет. И вообще он создал этот квест, потому вот так вот.",
		"Пинать Иши, Мая, Астаниэль, Utka и несколько загадочных тест-ботов.",
		"Сфотографировать свои свитера и джинсы, прислать Пламени фото с пометкой «недостающие картинки».",
		"Каждый день с утра до вечера писать ему в приват вопросы о том, когда будут вещи, какие они будут и сколько штук." ) ),

	array( "Если Вы увидели в чате Ка-Написа, то что нужно делать?", array(
		"Да ничего не делать – демиург как демиург, вроде Вы демиургов не видели.",
		"Попробовать заманить его в Реку и там всем Орденом дружненько набросится на него.",
		"Сразу же написать ему что-то типа: «Превеееед, ка-напед!»",
		"Забросать его смайликами, а лучше комбинациями из смайликов, а ещё лучше целыми смайло-историями." ) ),

	array( "Как звали двух Богов Алидерии?", array(
		"Да их трое было на самом деле: Астаниэль, Ка-Напис и Пламени.",
		"Фунус и Аэтас.",
		"Рабочий и крестьянка.",
		"Ромул и Рем." ) )
);

define( TIME_FOR_QUEST, 12 );
define( MAX_WRONG, 5 );

function new_question_id( )
{
	global $questions;
	return mt_rand( ) % ( count( $questions ) );
}

$cnt = to_num( $f[0] );

function add_wrong( )
{
	global $f;
	global $cnt;
	$cnt = to_num( $f[0] );
	++ $cnt;
	if ( $cnt > MAX_WRONG )
		$cnt = MAX_WRONG;
	$f[0] = chr( ord( '0' ) + $cnt );
}

function add_correct( )
{
	global $f;
	global $cnt;
	$cnt = to_num( $f[0] );
	-- $cnt;
	if ( $cnt < 0 )
		$cnt = 0;
	$f[0] = chr( ord( '0' ) + $cnt );
}

$cur_question = to_num( $f[1] );
$quest_pos = to_num( $f[2] );
$correct_ans = to_num( $f[3] );
$quest_text = '';
$quest_arr = array( );

function shuffle_quest( )
{
	global $questions;
	global $cur_question;
	global $quest_arr;
	$quest_arr = $questions[$cur_question];
	shuffle( $quest_arr[1] );
	global $correct_ans;
	$correct_ans = array_search( $questions[$cur_question][1][0], $quest_arr[1] );
	global $f;
	$f[3] = chr( ord( '0' ) + $correct_ans );
}

function new_question( )
{
	global $f;
	global $id;
	global $cur_question;
	$cur_question = new_question_id( );
	global $dead_line;
	$dead_line = time( ) + TIME_FOR_QUEST;
	global $quest_pos;
	$quest_pos = mt_rand( ) % 3;
	$f[1] = chr( ord( '0' ) + $cur_question );
	$f[2] = chr( ord( '0' ) + $quest_pos );
	shuffle_quest( $cur_question );
}

if ( $id < 0 )
	$cur_question = -1;

if ( $cnt > 0 && ( $cur_question < 0 || $dead_line < time( ) ) )
{
	if ( $id >= 0 )
		$id = -1;
	new_question( );
	add_wrong( );
}

//check ans

if ( $cnt > 0 && $id >= 0 )
{
	if ( $id == $correct_ans )
		add_correct( );
	else
		add_wrong( );
	new_question( );
}

function generate_text( )
{
	global $quest_arr;
	$s = $quest_arr[0] . '<br>';
	$n = count( $quest_arr[1] );
	for ( $i = 0; $i < $n; ++ $i )
	{
		$i2 = $i + 1;
		$s .= "$i2. <a href=\\'#\\' onclick=\\'query( \"quest_scripts/phrase281_ajax.php\", \"$i\" );\\'>{$quest_arr[1][$i]}</a><br>";
	}
	return $s;
}

if ( $cnt == 0 )
{
	f_MQuery( "UNLOCK TABLES" );
	$player->SetTrigger( 116, 0 );
	$player->SetTrigger( 114 );
	echo "location.href='game.php?phrase=643';";
}
else
{
	$quest_text = generate_text( );
	$dragon_text = '';
	if ( $id >= -1 )
	{
		$dragon_text = "Еще $cnt вопрос";
		if ( $cnt >= 2 && $cnt <= 4 )
			$dragon_text .= 'а';
		else
		if ( $cnt >= 5 )
			$dragon_text .= 'ов';
	}
	echo "out( '$quest_pos', '$quest_text', '$dragon_text' );";

 	$st_f = $f . substr( $st_f, 24 );
    f_MQuery( "UPDATE player_mines SET f='$st_f' WHERE player_id={$player->player_id}" );
	f_MQuery( "UNLOCK TABLES" );
	$player->SetQuestValue( 115, $dead_line );
}

?>
