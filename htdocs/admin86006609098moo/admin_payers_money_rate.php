<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

echo "<a href=index.php>На главную</a><br><br>";


$res = f_MQuery("SELECT SUM( have-had ) AS money, player_id FROM player_log WHERE item_id=-1 AND have>had AND player_id > 174 AND type > 2 GROUP BY player_id ORDER BY money DESC");
while( $arr = f_MFetch( $res ) )
{
	$pid = $arr['player_id'];


	$arr3 = f_MFetch( f_MQuery( "SELECT login FROM characters WHERE player_id=$pid" ) );

	echo "$arr3[0]: <b>".$arr['money']."</b><br>";
}

?>
