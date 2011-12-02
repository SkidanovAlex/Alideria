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

print( "<center><br>Информация о боях игрока <b>$login</b><br><br>" );

$page = $HTTP_GET_VARS['page'];
settype( $page, 'integer' );

$lim = $page * $entries_per_page;

$res = f_MQuery( "SELECT * FROM history_combats WHERE player_id = $player_id ORDER BY entry_id DESC LIMIT $lim, $entries_per_page" );

if( mysql_num_rows( $res ) )
{
	print( "<table border=1>" );
	while( $arr = f_MFetch( $res ) )
	{
		$tm = date( "d.m.Y H:i", $arr['time'] );
		print( "<tr><td>$tm</td><td vAlign=top>$arr[str]" );
		$pst = "---";
		if( $arr['polomka'] == 1 ) $pst = "<font color=green>поломка</font>";
		elseif( $arr['polomka'] == 2 ) $pst = "<font color=darkred>поломка</font>";
		print( "</td><td>$pst</td><td vAlign=top>" );
		print( "<a href=combat_log.php?id=$arr[combat_id] target=_blank>Смотреть бой</a></td></tr>" );
	}
	print( "</table>" );
	
	$res = f_MQuery( "SELECT count( entry_id ) FROM history_combats WHERE player_id = $player_id" );
	$arr = f_MFetch( $res );
	$val = $arr[0];
	$pages = ( $val - 1 ) / $entries_per_page + 1;
	settype(  $pages, 'integer' );
	print( "Страница: " );
	for( $i = 0; $i < $pages; ++ $i )
	{
		if( $i == $page ) print( "<b>" );
		else print( "<a href=history_fights.php?player_id=$player_id&page=$i>" );
		print( $i + 1 );
		if( $i == $page ) print( "</b>" );
		else print( "</a>" );
		print( "&nbsp;&nbsp;&nbsp;" );
	}
}
else print( "<i>Нет сражений в обозримом прошлом.</i>" );

?>
