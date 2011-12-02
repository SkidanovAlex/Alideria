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
	if( !$arr[rank] ) die( 'У вас недостаточно прав для просмотра этой страницы' );
}

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">


<?

print( "<center><br>Информация о наказаниях игрока <b>$login</b><br><br>" );

$page = $HTTP_GET_VARS['page'];
settype( $page, 'integer' );

$lim = $page * $entries_per_page;

$res = f_MQuery( "SELECT * FROM history_punishments WHERE player_id = $player_id ORDER BY entry_id DESC LIMIT $lim, $entries_per_page" );

if( mysql_num_rows( $res ) )
{
	print( "<table border=1>" );
	while( $arr = f_MFetch( $res ) )
	{
		$isadm = f_MValue("SELECT r . rank FROM player_ranks AS r, characters AS c WHERE r.player_id = c.player_id AND c.login =  '".$arr[moderator_login]."'");
			$tm = date( "d.m.Y H:i", $arr['time'] );
			if(  ($player->Rank() != 1 && $player->Rank() != 5) ) print( "<tr><td vAlign=top>Время: $tm<br>Модератор: <i>Скрыт</i><br>" );
			else print( "<tr><td vAlign=top>Время: $tm<br>Модератор: <b>$arr[moderator_login]</b><br>" );
			print( "</td><td vAlign=top>" );
		
			if( $arr[duration] )
			{
				print( "<font color=red><b>$arr[type]</b></font><br>Причина: $arr[reason]<br>Длительность: ".my_time_str( $arr[duration] ) );
			}
			else
				print( "<font color=blue><b>$arr[type]</b></font>" );
		
			if ($player->Rank() > 0) print("<br>Комментарий: ".$arr[comments]);
			print( "</td></tr>" );

	}
	print( "</table>" );
	
	$res = f_MQuery( "SELECT count( entry_id ) FROM history_punishments WHERE player_id = $player_id" );
	$arr = f_MFetch( $res );
	$val = $arr[0];
	$pages = ( $val - 1 ) / $entries_per_page + 1;
	settype(  $pages, 'integer' );
	print( "Страница: " );
	for( $i = 0; $i < $pages; ++ $i )
	{
		if( $i == $page ) print( "<b>" );
		else print( "<a href=history_punishments.php?player_id=$player_id&page=$i>" );
		print( $i + 1 );
		if( $i == $page ) print( "</b>" );
		else print( "</a>" );
		print( "&nbsp;&nbsp;&nbsp;" );
	}
}
else print( "<i>Нет наказаний в обозримом прошлом</i>" );

?>
