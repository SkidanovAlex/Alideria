<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "forest_functions.php" );
include_once( "phrase.php" );
include_once( "prof_exp.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

if( $player->location != 3 || $player->depth ==2 ) die( '/**/' );

?>

	plrs = new Array( );
	plrids = new Array( );
	cids = new Array( );

<?

	$depth = $player->depth;
	$loc = $player->location;

    $peace = ( $depth == 2 || $depth == 7 );

	$res = f_MQuery( "SELECT characters.login, characters.regime, characters.player_id, combat_id, 0 as mobik FROM characters INNER JOIN online ON characters.player_id = online.player_id LEFT JOIN combat_players ON characters.player_id = combat_players.player_id WHERE characters.loc = $loc AND characters.depth = $depth UNION
	                   SELECT characters.login, characters.regime, characters.player_id, combat_id, 1 as mobik FROM characters, combat_players WHERE characters.player_id = combat_players.player_id AND characters.loc = $loc AND characters.depth = $depth AND combat_players.ai = 1 AND combat_players.ready < 2" );
	$can_attack = !$peace;
	if( $player->regime != 0 ) $can_attack = 0;
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr[2] );
		$in_combat = ( $arr[1] == 100 ) ? 1 : 0;
		$moo = $can_attack?$can_attack:0;
		if( $arr[2] == $player->player_id ) $moo = 0;
		if( $arr['mobik'] ) $moo = 1;
		if( !$moo ) $in_combat = 0;
		if( !$plr->nick_clr ) $plr->nick_clr = 'FFFFFF';
		$arr['combat_id'] = (int)$arr['combat_id'];
		echo "river_add_player( ".$plr->Nick2().", $moo, $in_combat, $arr[2], $arr[combat_id] );";
	}
	echo "river_show_players();";

?>
