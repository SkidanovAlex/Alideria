<?

$entries_per_page = 40;

include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$player_id = $HTTP_GET_VARS['player_id'];

if( !$player_id ) $player_id = $player->player_id;

settype( $player_id, 'integer' );

$res = f_MQuery( "SELECT login FROM characters WHERE player_id = $player_id" );
$arr = f_MFetch( $res );

if( !$arr ) die( 'Нет такого игрока' );

$login = $arr[0];

if( $player_id != $player->player_id )
{
	$res = f_MQuery( "SELECT * FROM player_ranks WHERE player_id = {$player->player_id}" );
	if( !mysql_num_rows( $res ) ) die( 'У вас недостаточно прав для просмотра этой страницы' );
	$arr = f_MFetch( $res );
	if( $arr[rank] != 1 && $arr[rank] != 5 && $arr[rank] != 2 ) die( 'У вас недостаточно прав для просмотра этой страницы' );
	if ($arr[rank] != 1 && ($player_id==6825 || $player_id==172 || $player_id==173)) die( 'У вас недостаточно прав для просмотра этой страницы' );
	
	$moder_rank = $arr[rank];
}

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">


<?

print( "<center><br>Информация о визитах игрока <b>$login</b><br><br>" );

$page = $HTTP_GET_VARS['page'];
settype( $page, 'integer' );

$lim = $page * $entries_per_page;

$res = f_MQuery( "SELECT * FROM history_logon_logout WHERE player_id = $player_id ORDER BY entry_id DESC LIMIT $lim, $entries_per_page" );

if( mysql_num_rows( $res ) )
{
	print( "<table border=1>" );
	while( $arr = f_MFetch( $res ) )
	{
		if( $moder_rank == 2 )
		{
			$arr['login_ip'] = md5( $arr['login_ip'].'DksDS$kfdsl04$3dfgkl' );
			$arr['login_ip_x'] = md5( $arr['login_ip_x'].'DksDS$kfdsl04$3dfgkl' );
			$arr[logout_ip] = md5($arr[logout_ip].'DksDS$kfdsl04$3dfgkl' );
			$arr[logout_ip_x] = md5( $arr[logout_ip_x].'DksDS$kfdsl04$3dfgkl' );
		}
		$arr['login_ip'] = htmlspecialchars($arr['login_ip']);
		$arr['login_ip_x'] = htmlspecialchars($arr['login_ip_x']);
		$tm = date( "d.m.Y H:i", $arr['login_time'] );
		print( "<tr><td vAlign=top>Вход: $tm<br>ip: $arr[login_ip]<br>" );
		if( $arr[login_ip] != $arr[login_ip_x] )
			print( "скрытый ip: $arr[login_ip_x]<br>" );
		print( "</td><td vAlign=top>" );
		
		if( !$arr[logout_time] ) print( "<i>Нет парной записи о выходе</i>" );
		else
		{
			$tm = date( "d.m.Y H:i", $arr['logout_time'] );
			print( "Выход: $tm<br>ip: $arr[logout_ip]<br>" );
			if( $arr[logout_ip] != $arr[logout_ip_x] )
				print( "скрытый ip: $arr[logout_ip_x]<br>" );
			print( "Причина: $arr[logout_reason]<br>" );
			print( "Время в сети: ".my_time_str( $arr['logout_time'] - $arr['login_time'] )."<br>" );
		}
		
		print( "</td></tr>" );
	}
	print( "</table>" );
	
	$res = f_MQuery( "SELECT count( entry_id ) FROM history_logon_logout WHERE player_id = $player_id" );
	$arr = f_MFetch( $res );
	$val = $arr[0];
	$pages = ( $val - 1 ) / $entries_per_page + 1;
	settype(  $pages, 'integer' );
	print( "Страница: " );
	for( $i = 0; $i < $pages; ++ $i )
	{
		if( $i == $page ) print( "<b>" );
		else print( "<a href=history_visits.php?player_id=$player_id&page=$i>" );
		print( $i + 1 );
		if( $i == $page ) print( "</b>" );
		else print( "</a>" );
		print( "&nbsp;&nbsp;&nbsp;" );
	}
}
else print( "<i>Не заходил в игру в обозримом прошлом</i>" );

?>
