<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

echo "<a href=index.php>На главную</a><br><br>";

	$res2 = f_MQuery( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have>had AND player_id > 174 AND type=22 AND arg1=0" );
	$arr2 = f_MFetch( $res2 );

	echo "<br>SMS: <b>$arr2[0]</b><br>";
	$tm = time( ) - 3600 * 24 * 30;
	echo "За 30 дней: <b>".f_MValue( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have > had AND player_id > 174 AND type = 22 AND arg1=0 AND time > $tm" )."</b><br>";
	$tm = time( ) - 3600 * 24 * 7;
	echo "За 7 дней: <b>".f_MValue( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have > had AND player_id > 174 AND type = 22 AND arg1=0 AND time > $tm" )."</b><br>";

	$res2 = f_MQuery( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have>had AND player_id > 174 AND type=22 AND arg1=1" );
	$arr2 = f_MFetch( $res2 );

	echo "<br>WebMoney: <b>$arr2[0]</b><br>";
	$tm = time( ) - 3600 * 24 * 30;
	echo "За 30 дней: <b>".f_MValue( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have > had AND player_id > 174 AND type = 22 AND arg1=1 AND time > $tm" )."</b><br>";
	$tm = time( ) - 3600 * 24 * 7;
	echo "За 7 дней: <b>".f_MValue( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have > had AND player_id > 174 AND type = 22 AND arg1=1 AND time > $tm" )."</b><br>";

	$res2 = f_MQuery( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have>had AND player_id > 174 AND type=22 AND arg1=3" );
	$arr2 = f_MFetch( $res2 );

	echo "<br>RBK Money: <b>$arr2[0]</b><br>";
	$tm = time( ) - 3600 * 24 * 30;
	echo "За 30 дней: <b>".f_MValue( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have > had AND player_id > 174 AND type = 22 AND arg1=3 AND time > $tm" )."</b><br>";
	$tm = time( ) - 3600 * 24 * 7;
	echo "За 7 дней: <b>".f_MValue( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have > had AND player_id > 174 AND type = 22 AND arg1=3 AND time > $tm" )."</b><br>";

	$res2 = f_MQuery( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have>had AND player_id > 174 AND type=22 AND arg1=4" );
	$arr2 = f_MFetch( $res2 );

	echo "<br>2-Pay: <b>$arr2[0]</b><br>";
	$tm = time( ) - 3600 * 24 * 30;
	echo "За 30 дней: <b>".f_MValue( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have > had AND player_id > 174 AND type = 22 AND arg1=4 AND time > $tm" )."</b><br>";
	$tm = time( ) - 3600 * 24 * 7;
	echo "За 7 дней: <b>".f_MValue( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have > had AND player_id > 174 AND type = 22 AND arg1=4 AND time > $tm" )."</b><br>";

	$res2 = f_MQuery( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have>had AND player_id > 174 AND type=8 AND arg1=173" );
	$arr2 = f_MFetch( $res2 );

	echo "<br>Direct Ishi: <b>$arr2[0]</b><br>";
	$tm = time( ) - 3600 * 24 * 30;
	echo "За 30 дней: <b>".f_MValue( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have > had AND player_id > 174 AND type = 8 AND arg1=173 AND time > $tm" )."</b><br>";
	$tm = time( ) - 3600 * 24 * 7;
	echo "За 7 дней: <b>".f_MValue( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have > had AND player_id > 174 AND type = 8 AND arg1=173 AND time > $tm" )."</b><br>";

	$res2 = f_MQuery( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have>had AND player_id > 174 AND type=8 AND arg1=174" );
	$arr2 = f_MFetch( $res2 );

	echo "<br>Direct Noob: <b>$arr2[0]</b><br>";
	$tm = time( ) - 3600 * 24 * 30;
	echo "За 30 дней: <b>".f_MValue( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have > had AND player_id > 174 AND type = 8 AND arg1=174 AND time > $tm" )."</b><br>";
	$tm = time( ) - 3600 * 24 * 7;
	echo "За 7 дней: <b>".f_MValue( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have > had AND player_id > 174 AND type = 8 AND arg1=174 AND time > $tm" )."</b><br>";

?>
