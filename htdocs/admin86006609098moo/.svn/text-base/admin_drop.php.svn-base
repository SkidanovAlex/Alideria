<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

$res = f_MQuery( "select * from player_log where ( type=4 or type=3 ) order by entry_id desc limit 600" );
echo "<table>";
while( $arr = f_MFetch( $res ) )
{
	$parr = f_MFetch( f_MQuery( " SELECT login FROM characters WHERE player_id=$arr[player_id]" ) );
	$iarr = f_MFetch( f_MQuery( " SELECT * FROM items WHERE item_id=$arr[item_id]" ) );
	echo "<tr><td>$parr[0] ($arr[player_id])</td><td>$iarr[name] ($iarr[item_id], $iarr[price] дбл)</td><td>";
	$val = $arr[have] - $arr[had];
	if( $val > 0 ) $val = "<font color=green>+$val</font>";
	else $val = "<font color=darkred>$val</font>";
	$varr = f_MFetch( f_MQuery( " SELECT login_ip, login_ip_x FROM history_logon_logout WHERE player_id=$arr[player_id] ORDER BY entry_id DESC LIMIT 1" ) );
	echo "$val</td><td>$varr[0] ($varr[1])</td></tr>";
}
echo "</table>";


?>
