<?

die( );

include( 'functions.php' );
include( 'chess_functions.php' );

f_MConnect( );

$do = $HTTP_GET_VARS['do'];
settype( $do, 'integer' );

if( in_game( ) )
{
	die( '<script>parent.location.href="chess_panel.php";</script>' );
}

print( "<div id=moo name=moo>" );

if( $do == 1 )
{
	$res = f_MQuery( "SELECT * FROM chess_asks WHERE player1=$player_id OR player2=$player_id" );
	if( !mysql_num_rows( $res ) )
	{
		$tm = time( );
		f_MQuery( "INSERT INTO chess_asks ( player1, player2, time ) VALUES ( $player_id, -1, $tm )" );
	}
}
if( $do == 2 )
{
	$res = f_MQuery( "SELECT * FROM chess_asks WHERE player1=$player_id" );
	if( mysql_num_rows( $res ) )
	{
		$arr = mysql_fetch_array( $res );
		if( $arr[player2] != -1 )
		{
			f_MQuery( "DELETE FROM chess_asks WHERE player1=$player_id" );
			create_game( $arr[player1], $arr[player2] );
		}
	}
}
if( $do == 3 )
	f_MQuery( "UPDATE chess_asks SET player2=-1 WHERE player1=$player_id" );
if( $do == 4 )
	f_MQuery( "DELETE FROM chess_asks WHERE player1=$player_id" );
if( $do == 5 )
{
	$gm = $HTTP_GET_VARS['gm'];
	settype( $gm, 'integer' );
	$res = f_MQuery( "SELECT * FROM chess_asks WHERE player1=$player_id OR player2=$player_id" );
	if( !mysql_num_rows( $res ) )
	{
		$res = f_MQuery( "SELECT * FROM chess_asks WHERE id=$gm AND player2=-1" );
		if( mysql_num_rows( $res ) )
		{
			f_MQuery( "UPDATE chess_asks SET player2=$player_id WHERE id=$gm" );
		}
	}
}
if( $do == 6 )
	f_MQuery( "UPDATE chess_asks SET player2=-1 WHERE player2=$player_id" );

$res = f_MQuery( "SELECT * FROM chess_asks WHERE player1=$player_id OR player2=$player_id" );
if( !mysql_num_rows( $res ) )
	print( "<a target=ref href=chess_arena_ref.php?do=1>Подать заявку на игру в шахматы</a><br>" );
else
{
	$arr = mysql_fetch_array( $res );
	if( $arr[player1] == $player_id )
	{
		if( $arr[player2] != -1 )
		{
			print( "<a target=ref href=chess_arena_ref.php?do=2>Начать партию</a><br>");
			print( "<a target=ref href=chess_arena_ref.php?do=3>Отклонить встречную заявку</a><br>");
		}
		print( "<a target=ref href=chess_arena_ref.php?do=4>Отозвать заявку</a><br>");
	}
	else
		print( "<a target=ref href=chess_arena_ref.php?do=6>Отозвать встречную заявку</a><br>");
}

$res = f_MQuery( "SELECT * FROM chess_asks ORDER BY time DESC" );
if( !mysql_num_rows( $res ) )
	print( "<i>Не подано ни одной заявки</i><br>" );
else
{
	print( "<table border=1>" );
	while( $arr = mysql_fetch_array( $res ) )
	{
		$stm = date( "H:i", $arr[time] );
		print( "<tr><td>$stm</td><td width=200>".nick_by_id( $arr[player1] )."</td><td width=200>" );
		if( $arr[player2] == -1 )
		{
			if( $arr[player1] != $player_id )
			print( "<a target=ref href=chess_arena_ref.php?do=5&gm=$arr[id]>Принять</a>" );
		}
		else print( nick_by_id( $arr[player2] ) );
		print( "</td></tr>" );
	}
	print( "</table>" );
}

?>

</div>

<script>

parent.document.getElementById( 'chs' ).innerHTML = document.getElementById( 'moo' ).innerHTML;
parent.moo_ref( );

</script>
