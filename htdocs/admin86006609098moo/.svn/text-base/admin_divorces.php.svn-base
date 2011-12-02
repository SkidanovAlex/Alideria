<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['login1'] ) )
{
	$login1 = $HTTP_GET_VARS['login1'];
	$login2 = $HTTP_GET_VARS['login2'];
	$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$login1' OR login='$login2'" );
	if( mysql_num_rows( $res ) != 2 ) printf( "<font color=red>Одно из имен введено неверно</font><br>" );
	else
	{
		$arr = f_MFetch ($res ); $id1 = $arr[0];
		$arr = f_MFetch( $res ); $id2 = $arr[0];
		$num = f_MValue( "select count( * ) from player_weddings where p0=$id1 and p1=$id2 OR p1=$id1 and p0=$id2" );
		if( !$num ) printf( "<font color=red>Указанные игроки не женаты</font><br>" );
		else
		{
			f_MQuery( "delete from player_weddings where p0=$id1 and p1=$id2 OR p1=$id1 and p0=$id2" );
			f_MQuery( "delete from player_triggers where (player_id=$id1 or player_id=$id2) AND (trigger_id >= 220 AND trigger_id <= 225)" );
			printf( "<font color=blue>Развод кроликов успешно осуществлен</font><br>" );
		}
	}
}

?>

<a href=index.php>На главную</a><br>
<b>Добавитьв вещь персонажу</b><br>
<table>
<form action=admin_divorces.php method=get>
<tr><td>Логин мужика: </td><td><input type=text name=login1 class=m_btn></td></tr>
<tr><td>Логин телочки: </td><td><input type=text name=login2 class=m_btn></td></tr>
<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=Развести></td></tr>
</form>
</table>

<?

f_MClose( );

?>

