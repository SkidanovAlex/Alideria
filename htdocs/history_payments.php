<?
	$entries_per_page = 40;
	require_once( 'player.php' );

	f_MConnect( );

	if( !check_cookie( ) )
	{
		die( "Неверные настройки Cookie" );
	}

	$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

	$player_id = $HTTP_GET_VARS['player_id'];

	if( !$player_id )
		$player_id = $player->player_id;

settype( $player_id, 'integer' );

$res = f_MQuery( "SELECT login FROM characters WHERE player_id = $player_id" );
$arr = f_MFetch( $res );

if( !$arr ) die( 'Нет такого игрока' );

$login = $arr[0];

if( $player_id != $player->player_id )
{
	$res = f_MQuery( "SELECT * FROM player_ranks WHERE player_id = {$player->player_id} AND rank = 1" );
	if( !mysql_num_rows( $res ) ) die( 'У вас недостаточно прав для просмотра этой страницы' );
}

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">


<?

print( "<center><br>Информация о покупках талантов игрока <b>$login</b><br><br>" );

$page = $HTTP_GET_VARS['page'];
settype( $page, 'integer' );

$lim = $page * $entries_per_page;

$res = f_MQuery( "SELECT time, ( have - had ) AS money, arg1 FROM player_log WHERE player_id = $player_id AND item_id = -1 AND have > had AND type > 2 ORDER BY time DESC LIMIT $lim, $entries_per_page" );

if( mysql_num_rows( $res ) )
{
	$providers = array( 0 => 'SMS', 1 => 'WebMoney', 3 => 'RBK Money', 4 => '2-Pay', 173 => 'Администратор Ishamael', 174 => 'Администратор Пламени' );
	
	print( "<table border=1 cellpadding=3 cellspacing=0>" );
	while( $arr = f_MFetch( $res ) )
	{
		$tm = date( "d.m.Y H:i", $arr['time'] );
		echo '<tr><td>'.$tm.'</td><td style="width: 50px;"><img src="/images/umoney.gif" /> '.$arr['money'].'</td><td>'.$providers[$arr['arg1']].'</td></tr>';
		}
	print( "</table>" );
	
	$res = f_MQuery( "SELECT count( entry_id ) FROM player_log WHERE player_id = $player_id AND item_id = -1 AND have > had AND type > 2" );
	$arr = f_MFetch( $res );
	$val = $arr[0];
	$pages = ( $val - 1 ) / $entries_per_page + 1;
	settype(  $pages, 'integer' );
	print( "Страница: " );
	for( $i = 0; $i < $pages; ++ $i )
	{
		if( $i == $page ) print( "<b>" );
		else print( '<a href="/history_payments.php?player_id='.$player_id.'&page='.$i.'">' );
		print( $i + 1 );
		if( $i == $page ) print( "</b>" );
		else print( "</a>" );
		print( "&nbsp;&nbsp;&nbsp;" );
	}
}
else print( "<i>Нет покупок талантов в обозримом прошлом.</i>" );

?>
