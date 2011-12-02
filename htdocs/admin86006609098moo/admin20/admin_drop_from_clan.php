<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );
include_once( "../clan.php" );
include_once( "../clan_wonders.php" );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['login'] ) )
{
	$login = $HTTP_GET_VARS['login'];
	$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$login'" );
	$arr = f_MFetch( $res );
	if( !$arr ) printf( "<font color=red>Нет такого игрока</font><br>" );
	else
	{
		$plr = new Player( $arr[0] );
		$id = $plr->player_id;
		$clan_id = $plr->clan_id;
		f_MQuery( "DELETE FROM player_clans WHERE player_id=$id" );
		f_MQuery( "UPDATE characters SET clan_id=0 WHERE player_id=$id" );
		f_MQuery( "UPDATE characters SET regime=0, go_till=0 WHERE player_id=$id AND loc=2 AND depth=19" );
		f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0, arg1 ) VALUES ( $plr->clan_id, ".time( ).", {$player->player_id}, 10, $id, 2 )" );

		$plr = new Player( $id );
		$res = f_MQuery( "SELECT wonder_id FROM clan_wonders WHERE clan_id=$clan_id AND stage=100" );
		while( $arr = f_MFetch( $res ) )
			applyWonder( $arr[0], $plr, 0 );

		printf( "<font color=blue>Игрок успешно выкинут</font><br>" );
	}
}

?>

<a href=index.php>На главную</a><br>
<b>Выкинуть игрока из Ордена</b><br>
<table>
<form action=admin_drop_from_clan.php method=get>
<tr><td>Логин персонажа: </td><td><input type=text name=login class=m_btn></td></tr>
<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=Выкинуть></td></tr>
</form>
</table>

<?

f_MClose( );

?>

