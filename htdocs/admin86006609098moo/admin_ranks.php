<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

//die( );

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['login'] ) )
{
	$login = $HTTP_GET_VARS['login'];
	$rank = $HTTP_GET_VARS['rank'];
	settype( $rank, 'integer' );

	$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$login'" );
	$arr = f_MFetch( $res );
	if( !$arr ) printf( "<font color=red>Нет такого игрока</font><br>" );
	else do
	{
		f_MQuery( "DELETE FROM player_ranks WHERE player_id = $arr[0]" );
		if( $rank != 0 ) f_MQuery( "INSERT INTO player_ranks ( player_id, rank ) VALUES ( $arr[0], $rank )" );
		print( "<font color=blue><b>Succeed</b></font><br>" );
		LogError( "SETTER: {$player->player_id}; SETS_TO: $login" );
	} while( $arr = f_MFetch( $res ) );
}

?>

<a href=index.php>На главную</a><br>
<b>Администрирование прав персонажей</b><br>
<table>
<form action=admin_ranks.php method=get>
<tr><td>Логин персонажа: </td><td><input type=text name=login class=m_btn></td></tr>
<tr><td>Права персонажа: </td><td><select class=m_btn name=rank><option value=0 SELECTED>Нет прав<option value=1>Администратор<option value=2>Модератор<option value=5>Глава модераторов</select></td></tr>
<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=OK></td></tr>
</form>
</table><br>

<?

$res = f_MQuery("SELECT c.player_id, c.login, r.rank FROM characters as c, player_ranks as r WHERE c.player_id=r.player_id ORDER BY r.rank, c.player_id");

$rank_name = Array(0 => "Нет прав", 1 => "Администратор", 2 => "Модератор", 5 => "Глава модераторов");

echo "<br><table border=1><tr><td>ID игрока</td><td>Имя игрока</td><td>Ранг</td><td>Права</td><tr>";
$prev_rank = -1;
while ($arr = f_MFetch($res))
{
	if ($arr[2] != $prev_rank && $prev_rank != -1) echo "<tr><td><hr></td></tr>";
	echo "<tr><td>$arr[0]</td><td>$arr[1]</td><td align=center>$arr[2]</td><td>".$rank_name[$arr[2]];
	if ($arr[2] == 0) echo " Какого хера оно тут?";
	echo "</td></tr>";
	$prev_rank = $arr[2];
}
echo "</table>";

f_MClose( );

?>

