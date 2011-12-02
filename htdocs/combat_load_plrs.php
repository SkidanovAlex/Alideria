<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "/*Неверные настройки Cookie*/" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$res = f_MQuery( "SELECT combat_id, side, ready, opponent_id, lcard, card_id, target, note_id FROM combat_players WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

if( !$arr )
{
	die( "location.href='game.php';" );
}

$combat_id = $arr[0];
$side = $arr[1];
$ready = $arr[2];
$enemy = 1 - $side;


	
	$res = f_MQuery( "SELECT * FROM combat_players WHERE combat_id=$combat_id AND side=$side AND player_id <> {$player->player_id} AND ready < 2" );

    print( "var st = '<br><center><a href=\"javascript:ref_plrs( );\">Обновить</a></center><br><table width=100%><colgroup><col width=*><col width=220><tr><td align=left valign=top><div>' + " );
	$player->ARect( $ready );
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr['player_id'] );
		print( "+" );
		$plr->ARect( $arr['ready'] );
	}
	print( " + '</div></td><td align=right valign=top><div>'" );
	             
	
	$res = f_MQuery( "SELECT * FROM combat_players WHERE combat_id=$combat_id AND side=$enemy AND ready < 2" );
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr['player_id'] );
		echo "+";
		$plr->ARect( $arr['ready'] );
	}
	print( " + '</div></td></tr></table>';\n" );

	echo "_( 'plrs' ).innerHTML = st;";


?>
