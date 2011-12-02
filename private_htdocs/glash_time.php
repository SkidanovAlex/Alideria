<?
require_once("time_functions.php");


include( 'functions.php' );
include( 'player.php' );

f_MConnect( );

$res = f_MQuery( "SELECT * FROM auction ORDER BY rand() LIMIT 3" );

if( !f_MNum( $res ) ) die( );

$random = mt_rand(1,2); // Делаем случайную выборку для вида сообщения

if($random == 1) // Стандартная
{

$st = "<img src=images/smiles/writer.gif> На Аукционе кипит торговля! Не пропустите!";

while( $arr = f_MFetch( $res ) )
{
	$iarr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$arr[item_id]" ) );
	if( '' == $iarr['name13'] ) $iarr['name13'] = $iarr['name'];
	if( '' == $iarr['name2_m'] ) $iarr['name2_m'] = $iarr['name'];
	$nm = my_word_str( $arr['number'], $iarr['name'], $iarr['name13'], $iarr['name2_m'] );
	if( $arr['number'] > 1 ) $nm = $arr['number'].' '.$nm; 

	$price = min( $arr['immediately_price'], $arr['cur_price'] + $arr['step'] );
	$st .= " <b>".$nm."</b> всего за <b>$price</b> ".my_word_str( $price, "дублон", "дублона", "дублонов" )."!";
}
}
else
{
$st = "Так. Что там у нас сейчас на Аукционе? (развернул свиток) О! ";

while( $arr = f_MFetch( $res ) )
{
	$iarr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$arr[item_id]" ) );
	if( '' == $iarr['name13'] ) $iarr['name13'] = $iarr['name'];
	if( '' == $iarr['name2_m'] ) $iarr['name2_m'] = $iarr['name'];
	$nm = my_word_str( $arr['number'], $iarr['name'], $iarr['name13'], $iarr['name2_m'] );
	if( $arr['number'] > 1 ) $nm = $arr['number'].' '.$nm; 

	$price = min( $arr['immediately_price'], $arr['cur_price'] + $arr['step'] );
	$st .= "<b>".$nm."</b> за <b>$price</b> ".my_word_str( $price, "дублон", "дублона", "дублонов" ).", ";
}
$st .= "и это ещё не всё! Не забудьте заглянуть в Палатку Аукциона на Ярмарке Теллы!";
}

//echo $st;

// ---------------------
$plr = new Player( 69055 );
$plr->UploadInfoToJavaServer( );

$sock = socket_create(AF_INET, SOCK_STREAM, 0);
socket_connect($sock, "127.0.0.1", 1100);
$tm = date( "H:i", time( ) );
$st = "say\n{$st}\n69055\n0\n-5\n{$tm}\n";
socket_write( $sock, $st, strlen($st) ); 
socket_close( $sock );
// ---------------------

$tm = time( );

$res = f_MQuery( "SELECT * FROM tournament_announcements WHERE date > $tm ORDER BY rand()" );
$arr = f_MFetch( $res );
if( $arr && mt_rand( 1, 4 ) == 1 )
{
	if( $arr['type'] == 0 ) $st = "Вниманию игроков $arr[min_level] уровня! Не пропустите турнир <b>&quot;$arr[name]&quot;</b>, который состоится <b>".date( "d.m.Y", $arr['date'] )."</b> в <b>".date( "H:i", $arr['date'] )."</b>! Запись заканчивается за пять минут до начала турнира!";
	else if( $arr['type'] == 1 ) $st = "Вниманию всех игроков! Не пропустите турнир <b>&quot;$arr[name]&quot;</b>, который состоится <b>".date( "d.m.Y", $arr['date'] )."</b> в <b>".date( "H:i", $arr['date'] )."</b>! Запись заканчивается за пять минут до начала турнира!";
	else if( $arr['type'] == 2 ) $st = "Вниманию всех Орденов! Не пропустите турнир <b>&quot;$arr[name]&quot;</b>, который состоится <b>".date( "d.m.Y", $arr['date'] )."</b> в <b>".date( "H:i", $arr['date'] )."</b>! Запись заканчивается за пять минут до начала турнира!";
//	$st = "Вниманию игроков, записавшихся на один из турниров! К сожалению, в Городской Управе потеряли папку с именами записавшихся, необходимо записаться заново!";
    // ---------------------
    $plr = new Player( 69055 );
    $plr->UploadInfoToJavaServer( );

    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
    socket_connect($sock, "127.0.0.1", 1100);
    $tm = date( "H:i", time( ) );
    $st = "say\n{$st}\n69055\n0\n0\n{$tm}\n";
    socket_write( $sock, $st, strlen($st) ); 
    socket_close( $sock );
    // ---------------------
}
else if( mt_rand( 1, 2 ) == 1 )
{
	$st = f_MValue( "SELECT phrase FROM glash_phrases WHERE chat=0 ORDER BY rand() LIMIT 1" );
	
    // ---------------------
    $plr = new Player( 69055 );
    $plr->UploadInfoToJavaServer( );

    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
    socket_connect($sock, "127.0.0.1", 1100);
    $tm = date( "H:i", time( ) );
    $st = "say\n{$st}\n69055\n0\n0\n{$tm}\n";
    socket_write( $sock, $st, strlen($st) ); 
    socket_close( $sock );
    // ---------------------
}

$res = f_MQuery( "SELECT DISTINCT chat FROM glash_phrases WHERE chat > 0" );
while( $arr = f_MFetch( $res ) ) if( mt_rand( 0, 1 ) == 1 )
{
    // ---------------------
    $plr = new Player( 69055 );
    $plr->UploadInfoToJavaServer( );

    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
    socket_connect($sock, "127.0.0.1", 1100);
    $tm = date( "H:i", time( ) );
    $st = "enter\n69055\n{$arr[0]}\n";
    socket_write( $sock, $st, strlen($st) ); 
    socket_close( $sock );

	$st = f_MValue( "SELECT phrase FROM glash_phrases WHERE chat={$arr[0]} ORDER BY rand() LIMIT 1" );
//	echo $st;
	
    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
    socket_connect($sock, "127.0.0.1", 1100);
    $tm = date( "H:i", time( ) );
    $st = "say\n{$st}\n69055\n0\n{$arr[0]}\n{$tm}\n";
    socket_write( $sock, $st, strlen($st) ); 
    socket_close( $sock );
    // ---------------------
}

?>
