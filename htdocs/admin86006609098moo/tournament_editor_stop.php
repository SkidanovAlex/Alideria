<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );
include( '../player.php' );

$id = $HTTP_GET_VARS['id'];
settype( $id, 'integer' );

f_MConnect( );

include( 'admin_header.php' );

$res = f_MQuery( "SELECT player_id FROM tournament_players WHERE tournament_id=$id" );
while( $arr = f_MFetch( $res ) )
{
	$plr = new Player( $arr[0] );
	if( $plr->regime == 100 )
		$plr->LeaveCombat( );
}

?>
