<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'moder_header.php' );

$res = f_MQuery( "SELECT history_punishments.*, characters.login FROM history_punishments INNER JOIN characters ON history_punishments.player_id=characters.player_id ORDER BY entry_id DESC LIMIT 100" );

while( $arr = f_MFetch( $res ) )
{
	echo "[{$arr[login]}] by [{$arr[moderator_login]}] reason [{$arr[reason]}]<br>";
}

?>
