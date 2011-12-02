<?

header("Content-type: text/html; charset=windows-1251");

include_once( "../no_cache.php" );
include_once( "../functions.php" );
include_once( "../player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

if( $player->GetQuestValue( 25 ) > time( ) ) die( );

$res = f_MQuery( "SELECT talk_id FROM player_talks WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( !$arr || $arr[0] != 202 ) die( );

$id = $HTTP_RAW_POST_DATA;

settype( $id, 'integer' );

if( $id < 0 || $id >= 36 ) RaiseError( "Попытка выбрать неверную клетку при игре в сапер" );

f_MQuery( "LOCK TABLE player_mines WRITE" );

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

if( !$arr )
{
	do
	{
    	$a = Array( );
    	for( $i = 0; $i < 36; ++ $i ) $a[$i] = $i;
    	for( $i = 0; $i < 7; ++ $i )
    	{
    		$j = mt_rand( $i, 35 );
    		$t = $a[$i];
    		$a[$i] = $a[$j];
    		$a[$j] = $t;
    	}
    	$st = '';
    	for( $i = 0; $i < 36; ++ $i ) $st .= '.';
    	for( $i = 0; $i < 7; ++ $i ) $st[$a[$i]] = 'x';
    } while( $st[$id] == 'x' );

	f_MQuery( "INSERT INTO player_mines( player_id, f ) VALUES ( {$player->player_id}, '$st' )" );
	$f = $st;
	$lost = false;
}
else
{
	$f = $arr['f'];
	$lost = $arr['lost'];
}

$moo = 0;
if( $lost ) die( "out( '$f', 1 );" );
else if( strpos( $f, '.' ) === false ) echo "out( '$f', 2 );";
else
{
	$c = $f[$id];
	if( $c != '.' && $c != 'x' ) die( );
	if( $c == 'x' )
	{
		f_MQuery( "UPDATE player_mines SET lost=1 WHERE player_id={$player->player_id}" );
		f_MQuery( "UNLOCK TABLES" );
		$player->SetQuestValue( 25, time( ) + 30 * 60 );
		echo "out( '$f', 1 );";
		die( );
	}
	else
	{
		function moo( $id )
		{
			global $f;
			if( $f[$id] != '.' ) return;
    		$num = 0;
    		if( $id % 6 != 0 && $f[$id - 1] == 'x' ) ++ $num;
    		if( $id % 6 != 5 && $f[$id + 1] == 'x' ) ++ $num;
    		if( $id >= 6 && $f[$id - 6] == 'x' ) ++ $num;
    		if( $id < 30 && $f[$id + 6] == 'x' ) ++ $num;
    		if( $id % 6 != 0 && $id >= 6 && $f[$id - 7] == 'x' ) ++ $num;
    		if( $id % 6 != 5 && $id >= 6 && $f[$id - 5] == 'x' ) ++ $num;
    		if( $id % 6 != 0 && $id < 30 && $f[$id + 5] == 'x' ) ++ $num;
    		if( $id % 6 != 5 && $id < 30 && $f[$id + 7] == 'x' ) ++ $num;
    		$f[$id] = chr( ord( '0' ) + $num );
    		if( $num == 0 )
    		{
    			if( $id % 6 > 0 ) moo( $id - 1 );
    			if( $id % 6 < 5 ) moo( $id + 1 );
    			if( $id >= 6 ) moo( $id - 6 );
    			if( $id < 30 ) moo( $id + 6 );
        		if( $id % 6 != 0 && $id >= 6 ) moo( $id - 7 );
        		if( $id % 6 != 5 && $id >= 6 ) moo( $id - 5 );
        		if( $id % 6 != 0 && $id < 30 ) moo( $id + 5 );
        		if( $id % 6 != 5 && $id < 30 ) moo( $id + 7 );

    		}
    	}
    	moo( $id );
		f_MQuery( "UPDATE player_mines SET f='$f' WHERE player_id={$player->player_id}" );
		$moo = 0;
		if( strpos( $f, '.' ) === false ) $moo = 2;
		if( $moo == 0 ) $f = str_replace( 'x', '.', $f );
		echo "out( '$f', $moo );";
	}
}

f_MQuery( "UNLOCK TABLES" );

if( $moo == 2 ) $player->SetTrigger( 47 );

?>
