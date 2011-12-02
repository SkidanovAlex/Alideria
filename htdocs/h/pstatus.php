<?

include( '../functions.php' );
include( '../player.php' );

f_MConnect( );

if( isset( $_GET['id'] ) )
{
	$id = (int)$_GET['id'];
	$res = f_MQuery( "SELECT * FROM characters WHERE player_id=$id" );
}
else if( isset( $_GET['login'] ) )
{
	$login = $_GET['login'];
	$res = f_MQuery( "SELECT * FROM characters WHERE login='$login'" );
}
else die( "params: pstatus.php?id=# or pstatus.php?login=#<br>output format:<br>player_id login level online clan_id combat_id number_of_guilds guild1 rank1 guild2 rank2" );

$arr = f_MFetch( $res );
if( !$arr ) echo "0";
else
{
	echo "$arr[player_id]";
	echo " $arr[login]";
	echo " $arr[level]";
	$qarr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM online WHERE player_id=$arr[player_id]" ) );
	if( $qarr[0] ) echo " 1";
	else echo " 0";
	echo " $arr[clan_id]";
	$qarr = f_MFetch( f_MQuery( "SELECT combat_id FROM combat_players WHERE player_id=$arr[player_id]" ) );
	echo " ".((int)$qarr[0]);
	$qres = f_MQuery( "SELECT * FROM player_guilds WHERE player_id=$arr[player_id]" );
	echo " ".f_MNum( $qres );
	while($qarr = f_MFetch ( $qres ) ) echo " $qarr[guild_id] $qarr[rank]";
}

?>