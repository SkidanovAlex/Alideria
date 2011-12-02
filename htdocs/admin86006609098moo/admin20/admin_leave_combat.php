<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['login'] ) )
{
	$login = $HTTP_GET_VARS['login'];

	$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$login'" );
	$arr = f_MFetch( $res );
	if( !$arr ) printf( "<font color=red>Нет такого игрока</font><br>" );
	else do
	{
 	$player2 = new Player( $arr[0] );
		if( $player2->regime == 100 )
			$player2->LeaveCombat( );
	} while( $arr = f_MFetch( $res ) );
}

?>

<a href=index.php>На главную</a><br>
<b>Выкинуть персонажа из боя</b><br>
<table>
<form action=admin_leave_combat.php method=get>
<tr><td>Логин персонажа: </td><td><input type=text name=login class=m_btn></td></tr>
<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=Drop></td></tr>
</form>
</table>

<?

f_MClose( );

?>

