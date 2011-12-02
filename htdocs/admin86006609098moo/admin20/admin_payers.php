<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

echo "<a href=index.php>На главную</a><br><br>";

	$res2 = f_MQuery( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have>had AND player_id > 174 AND type > 2" );
	$arr2 = f_MFetch( $res2 );

	echo "ВСЕГО: <b>$arr2[0]</b><br><br>";

	$tm = time( ) - 24 * 3600 * 30;
	echo "За 30 дней: <b>".f_MValue( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have > had AND player_id > 174 AND type > 2 AND time > $tm" )."</b><br>";

	$tm = time( ) - 24 * 3600 * 7;
	echo "За семь дней: <b>".f_MValue( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have > had AND player_id > 174 AND type > 2 AND time > $tm" )."</b><br>";

	$tm = time( ) - 24 * 3600;
	echo "За последние сутки: <b>".f_MValue( "SELECT sum( have-had ) FROM player_log WHERE item_id=-1 AND have > had AND player_id > 174 AND type > 2 AND time > $tm" )."</b><br><br>";
	

echo "<br><br>Последние 100 платежей:<br>";
echo "<table border=1 bordercolor=0>";

echo "<tr><td align=center>&nbsp;<b>Время</b>&nbsp;</td><td align=center>&nbsp;<b>Игрок</b>&nbsp;</td><td align=center>&nbsp;<b>Таланты</b>&nbsp;</td><td align=center>&nbsp;<b>~ Прибыль</b>&nbsp;</td><td align=center>&nbsp;<b>Тип</b>&nbsp;</td></tr>";
$res = f_MQuery("SELECT time, player_id, (have-had) AS money, arg1 FROM player_log WHERE item_id=-1 AND have>had AND player_id > 174 AND type > 2 ORDER BY time DESC LIMIT 0, 100");
while( $arr = f_MFetch( $res ) )
{
	$pid = $arr['player_id'];
	$money = $arr['money'];
	$time = $arr['time'];
	$arg1 = $arr['arg1'];

	$arr3 = f_MFetch( f_MQuery( "SELECT login FROM characters WHERE player_id=$pid" ) );

	echo "<tr><td align=center>&nbsp;".date('Y-m-d H:i:s', $time)."&nbsp;</td><td>&nbsp;$arr3[0]&nbsp;</td><td align=center>&nbsp;$money&nbsp;</td>";

	if($arg1 == 0)
		echo "<td align=center>&nbsp;". ($money * 0.165) ."$&nbsp;</td>";
	else
		echo "<td align=center>&nbsp;". ($money * 0.33) ."$&nbsp;</td>";
		

	if($arg1 == 0)
		echo "<td align=center>&nbsp;SMS&nbsp;</td>";
	elseif($arg1 == 1)
		echo "<td align=center>&nbsp;WebMoney&nbsp;</td>";
	elseif($arg1 == 3)
		echo "<td align=center>&nbsp;RBK Money&nbsp;</td>";
	elseif($arg1 == 4)
		echo "<td align=center>&nbsp;2-Pay&nbsp;</td>";
	elseif($arg1 == 173)
		echo "<td align=center>&nbsp;Dealer: Ishi&nbsp;</td>";
	elseif($arg1 == 174)
		echo "<td align=center>&nbsp;Dealer: Noob&nbsp;</td>";

	echo "</tr>";
}

echo "</table>";

?>
