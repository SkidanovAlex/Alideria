<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

//system( 'cd ../../chat' );
//system( 'restart_chat' );

$res = f_MQuery( "SELECT player_id FROM online" );
while( $arr = f_MFetch( $res ) )
{
	$player = new Player( $arr[0] );
	$player->UploadInfoToJavaServer( );
}

?>
<a href=index.php>На главную</a><br>
