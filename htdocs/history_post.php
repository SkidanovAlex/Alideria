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
}

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">


<?

print( "<center><br>Информация о почтовых переводах игрока <b>$login</b><br><br>" );

$page = $HTTP_GET_VARS['page'];
settype( $page, 'integer' );

$lim = $page * $entries_per_page;

$res = f_MQuery( "SELECT * FROM history_post WHERE player_id1 = $player_id OR player_id2 = $player_id ORDER BY entry_id DESC LIMIT $lim, $entries_per_page" );

$post_hist_types = array( "Отправлено", "Получено", "Возвращено" );
if( mysql_num_rows( $res ) )
{
	print( "<table border=1>" );
	while( $arr = f_MFetch( $res ) )
	{
		$tm = date( "d.m.Y H:i", $arr['time'] );
		print( "<tr><td vAlign=top>$tm<br>".$post_hist_types[$arr[type]] );
		print( "</td><td vAlign=top>" );
		$arr1 = f_MFetch( f_MQuery( "SELECT login FROM characters WHERE player_id = $arr[player_id1]" ) );
		$arr2 = f_MFetch( f_MQuery( "SELECT login FROM characters WHERE player_id = $arr[player_id2]" ) );
		print( "От: <b>$arr1[0]</b><br>Для: <b>$arr2[0]</b></td><td vAlign=top>$arr[val]" );
		print( "</td></tr>" );
	}
	print( "</table>" );
	
	$res = f_MQuery( "SELECT count( entry_id ) FROM history_post WHERE player_id1 = $player_id OR player_id2 = $player_id" );
	$arr = f_MFetch( $res );
	$val = $arr[0];
	$pages = ( $val - 1 ) / $entries_per_page + 1;
	settype(  $pages, 'integer' );
	print( "Страница: " );
	for( $i = 0; $i < $pages; ++ $i )
	{
		if( $i == $page ) print( "<b>" );
		else print( "<a href=history_post.php?player_id=$player_id&page=$i>" );
		print( $i + 1 );
		if( $i == $page ) print( "</b>" );
		else print( "</a>" );
		print( "&nbsp;&nbsp;&nbsp;" );
	}
}
else print( "<i>Нет переводов в обозримом прошлом</i>" );

?>
