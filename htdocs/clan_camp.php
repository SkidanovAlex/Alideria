<?

include_once( "clan.php" );
include_once( "clan_wonders.php" );

$clan_id = $player->clan_id;

if( $player->clan_id == 0 )
{
	$player->SetRegime( 0, true );
	$player->SetTill( 0, true );
	$player->SetDepth( 19, true );
	die( "<script>location.href='game.php';</script>" );
}

$mode = $_GET['order'];
if( $player->regime >= 300 && $player->regime < 310 )
	$mode = 'cafe';
if( $player->regime == 114 || $player->regime == 117 )
	$mode = 'wonders';

if ($mode == 'tree')
	include ('clan_tree.php');
elseif( $mode == 'buildings' )
	include( 'clan_buildings.php' );
elseif( $mode == 'barracks' )
	include( 'clan_staff.php' );
elseif( $mode == 'silo' )
	include( 'clan_silo.php' );
elseif( $mode == 'cafe' )
	include( 'clan_cafe.php' );
elseif( $mode == 'shop_log' )
	include( 'clan_shop_log.php' );
elseif( $mode == 'shop_control_log' )
	include( 'clan_shop_control_log.php' );
elseif( $mode === 'leave' && $player->regime == 0 )
{
	$player->SetDepth( 19, true );
	die( "<script>location.href='game.php';</script>" );
}
else if( $mode == 'wonders' )
	include( "clan_build_wonder.php" );
else if( $mode == 'portal' )
	include( "clan_portal_entrance.php" );

else
{
	$st = render_camp( $clan_id, true );
	echo "<center><script>document.write( $st );</script>";
	if( isWonderNow( ) ) echo "<a href=game.php?order=wonders>Перейти к строительству Чуда Света</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<a href=game.php?order=buildings>Перейти к окну постройки</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=game.php?order=leave>Вернуться в зал собраний</a></center>";
}

?>
