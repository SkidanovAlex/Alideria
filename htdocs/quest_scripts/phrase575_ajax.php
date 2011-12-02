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
if( !$arr || $arr[0] != 575 ) die( );

if( isset( $_GET['restart'] ) )
{
	f_MQuery( "DELETE FROM mahjong WHERE player_id={$player->player_id}" );
	die( "location.href='game.php';" );
}

$id1 = (int)$_GET['id1'];
$id2 = (int)$_GET['id2'];

if( $id1 < 0 || $id1 >= 144 ) RaiseError( "Попытка выбрать неверную клетку в игре в маджонг", "id1=$id1" );
if( $id2 < 0 || $id2 >= 144 || $id1 == $id2 ) RaiseError( "Попытка выбрать неверную клетку в игре в маджонг", "id2=$id2" );

include( "phrase575_functions.php" );

f_MQuery( "LOCK TABLE mahjong WRITE" );
$data = f_MValue( "SELECT data FROM mahjong WHERE player_id={$player->player_id}" );
if( !$data )
{
	RaiseError( "Игрок играет в Маджонг, но записи об этом нет в БД" );
}

if( $data[$id1] == '.' ) die( );

if( $data[$id1] != $data[$id2]  )
{
	die( "highlight( $id2 );" );
}

if( !mhIsFree( $id1 ) || !mhIsFree( $id2 ) ) die( );

$data[$id1] = '.';
$data[$id2] = '.';
f_MQuery( "UPDATE mahjong SET data='$data' WHERE player_id={$player->player_id}" );
f_MQuery( "UNLOCK TABLES" );

echo "unhighlight( );";

echo "_( 'mh_{$id1}' ).style.display = 'none';";
echo "_( 'mh_{$id2}' ).style.display = 'none';";

function moo( $i )
{
	$st = mhGetElemHtml( $i );
	if( $st == '' ) echo "_( 'mh_{$i}' ).style.display = 'none';";
	else echo "_( 'mh_{$i}' ).innerHTML = '".addslashes( mhGetElemHtml( $i ) )."';";
}

foreach( $mh_left[$id1] as $i ) moo( $i );
foreach( $mh_right[$id1] as $i ) moo( $i );
foreach( $mh_left[$id2] as $i ) moo( $i );
foreach( $mh_right[$id2] as $i ) moo( $i );

for( $i = 0; $i < 144; ++ $i )
{
	if( $mh_top[$i] == $id1 || $mh_top[$i] == $id2 )
	{
		moo( $i );
	}
}

mhFinishCheck( );

?>
