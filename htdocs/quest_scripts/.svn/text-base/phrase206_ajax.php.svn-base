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
if( !$arr || $arr[0] != 206 ) die( );

$id = $HTTP_RAW_POST_DATA;

settype( $id, 'integer' );

if( $id < 0 || $id >= 36 ) RaiseError( "Попытка выбрать неверную клетку при игре в открытие замка" );

f_MQuery( "LOCK TABLE player_mines WRITE" );

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

$f = $arr['f'];
$lost = $arr['lost'];

$moo = 0;
if( $lost ) die( "out( '$f', 1 );" );
else
{
	$c = $f[$id];
	if( $c == '.' ) die( );
	else
	{
		function fswap( $a, $b )
		{
			global $f;
			$t = $f[$a];
			$f[$a] = $f[$b];
			$f[$b] = $t;
		}

		if( $id % 6 != 0 && $f[$id - 1] == '.' ) fswap( $id, $id-1 );
		if( $id % 6 != 5 && $f[$id + 1] == '.' ) fswap( $id, $id+1 );
		if( $id >= 6 && $f[$id - 6] == '.' ) fswap( $id, $id-6 );
		if( $id < 30 && $f[$id + 6] == '.' ) fswap( $id, $id+6 );
		f_MQuery( "UPDATE player_mines SET f='$f' WHERE player_id={$player->player_id}" );

		$et = '';
		for( $i = 0 ; $i < 18; ++ $i ) $et .= 'r';
		for( $i = 0; $i < 17; ++ $i ) $et .= 'g';
		$et .= '.';
		if( $f == $et )
		{
			$moo = 1;
			f_MQuery( "UPDATE player_mines SET lost=1 WHERE player_id={$player->player_id}" );
		}
		else $moo = 0;

		echo "out( '$f', $moo );";
	}
}

f_MQuery( "UNLOCK TABLES" );

if( $moo == 1 ) $player->SetTrigger( 48 );

?>
