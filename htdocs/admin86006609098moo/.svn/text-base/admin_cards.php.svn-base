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
	$card_id = $HTTP_GET_VARS['card_id'];
	$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$login'" );
	$arr = f_MFetch( $res );
	if( !$arr ) printf( "<font color=red>Нет такого игрока</font><br>" );
	else
	{
		$ress = f_MQuery( "SELECT * FROM player_cards WHERE player_id = $arr[0] AND card_id = $card_id" );
		if( !f_MNum( $ress ) )
		{
			f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES( $arr[0], $card_id, 10 )" );
			printf( "<font color=blue>Карточка успешно добавлена</font><br>" );
		}
		else printf( "<font color=red>У игрока уже есть этот свиток</font><br>" );
	}
}

?>

<a href=index.php>На главную</a><br>
<b>Добавитьв свиток в книгу заклинаний персонажу</b><br>
<table>
<form action=admin_cards.php method=get>
<tr><td>Логин персонажа: </td><td><input type=text name=login class=m_btn></td></tr>
<tr><td>АйДи свитка: </td><td><input type=text name=card_id class=m_btn></td></tr>
<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=Добавить></td></tr>
</form>
</table>

<?

f_MClose( );

?>

