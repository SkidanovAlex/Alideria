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
if( !$arr || $arr[0] != 563 ) die( );

$id = $HTTP_RAW_POST_DATA;

settype( $id, 'integer' );

if( $id < 0 || $id >= 9 ) RaiseError( "Попытка выбрать неверную клетку при игре в ёлочки", $id );

f_MQuery( "LOCK TABLE player_mines WRITE" );

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

if( !$arr )
{
	RaiseError( "Нет игрового поля в игре в елочки" );
}
else
{
	$f = $arr['f'];
	$moo = $arr['lost'];
}

if( $moo )
{
	die( "do_win( );" );
}

if( strlen( $f ) == 9 ) $f = $f . $f;

	function moo( $x, $y )
	{
		global $f;
		if( $x < 0 || $y < 0 || $x >= 3 || $y >= 3 ) return;
		$id = $x * 3 + $y;
		if( $f[$id] == '.' ) return;
		else if( $f[$id] == '5' ) $f[$id] = '6';
		else if( $f[$id] == '6' ) $f[$id] = '7';
		else if( $f[$id] == '7' ) $f[$id] = '8';
		else if( $f[$id] == '8' ) $f[$id] = '5';
	}
	function turn( $x, $y )
	{
		global $f;
		$id = $x * 3 + $y;
		if( $f[$id] == '4' || $f[$id] == '3' || $f[$id] == '2' || $f[$id] == '1' ) $f[$id] = '5';
		else moo( $x, $y );
		moo( $x - 1, $y );
		moo( $x + 1, $y );
		moo( $x, $y - 1 );
		moo( $x, $y + 1 );
	}

turn( floor($id / 3), $id % 3 );

$moo = 1;
for( $i = 0; $i < 9; ++ $i )
{
	if( (int)$f[$i] != (int)$f[$i + 9] + 4 ) $moo = 0;
}

echo "refr('$f');";
f_MQuery( "UPDATE player_mines SET f='$f' WHERE player_id={$player->player_id}" );


f_MQuery( "UNLOCK TABLES" );

if( $moo == 1 )
{
	f_MQuery( "UPDATE player_mines SET lost=1 WHERE player_id={$player->player_id}" );
	$player->SetTrigger( 96 );
	echo "do_win();";
}

?>
