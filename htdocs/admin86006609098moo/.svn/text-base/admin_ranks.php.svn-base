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
</table>

<?

f_MClose( );

?>

