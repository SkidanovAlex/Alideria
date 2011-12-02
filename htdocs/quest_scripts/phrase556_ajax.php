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
if( !$arr || $arr[0] != 556 ) die( );

$id = $HTTP_RAW_POST_DATA;

settype( $id, 'integer' );

if( $id < 0 || $id >= 20 ) RaiseError( "Попытка выбрать неверную клетку при игре в пары", $id );

f_MQuery( "LOCK TABLE player_mines WRITE" );

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

if( !$arr )
{
    	$a = Array( );
    	for( $i = 0; $i < 10; ++ $i ) { $a[] = $i; $a[] = $i; }
    	for( $i = 0; $i < 19; ++ $i )
    	{
    		$j = mt_rand( $i, 19 );
    		$t = $a[$i];
    		$a[$i] = $a[$j];
    		$a[$j] = $t;
    	}
    	$st = '';
    	for( $i = 0; $i < 20; ++ $i ) $st .= $a[$i];
    	for( $i = 0; $i < 20; ++ $i ) $st .= '.';
    	$st .= 0;

	f_MQuery( "INSERT INTO player_mines( player_id, f ) VALUES ( {$player->player_id}, '$st' )" );
	$f = $st;
	$lost = false;
}
else
{
	$f = $arr['f'];
	$lost = $arr['lost'];
}

$steps = (int)substr($f,40);
$of = substr( $f, 20 );

if( $f[$id + 20] != '.' ) die( "out( '$of', 0 );" );

$moo = 0;
if( substr_count($f, '.') == 0 ) echo( "out( '$of', 1 );" );
else if( substr_count($f,'.') % 2 == 1 )
{
	$cur = -1;
	for ($i = 0; $i < 20; ++ $i) if ($f[$i] == '#') $cur = $f[$i + 20];
	if ($cur == -1)
	{
		$player->SetRegime( 0 );
		f_MQuery( "DELETE FROM player_talks WHERE player_id={$player->player_id}" );
		RaiseError( "Ошибка работы квеста с игрой в пары", "cur = -1, f = '$f'" );
	}
	
	$pair = false;
	if( $cur == $f[$id] ) $pair = true;
	
	$f[$id + 20] = $f[$id];
	$f[$id] = '#';
	++ $steps;
	$f = substr($f,0,40).($steps);
	$of = substr( $f, 20 );
	
	if( $pair && substr_count($f, '.') == 0 ) $moo = 1;
	
	echo "out( '$of', $moo );";
	for( $i = 0; $i < 20; ++ $i ) if( $f[$i] == '#' )
	{
		$f[$i] = $f[$i + 20];
		if( !$pair ) $f[$i + 20] = '.';
	}
	
	f_MQuery( "UPDATE player_mines SET f='$f' WHERE player_id={$player->player_id}" );

	if( !$pair )
	{
		$of = substr( $f, 20 );
		echo "pr_tmo = setTimeout( function() {out( '$of', 0 );}, 2000);";
	}	
}
else
{
	$f[$id + 20] = $f[$id];
	$f[$id] = '#';
//	++ $steps;
	$f = substr($f,0,40).($steps);
	$of = substr( $f, 20 );
	f_MQuery( "UPDATE player_mines SET f='$f' WHERE player_id={$player->player_id}" );
	echo "out( '$of', 0 );";
}

f_MQuery( "UNLOCK TABLES" );

if( $moo == 1 ) $player->SetTrigger( 94 );

?>
