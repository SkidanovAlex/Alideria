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
	$item_id = $HTTP_GET_VARS['item_id'];
	$number = $HTTP_GET_VARS['number'];
	$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$login'" );
	$arr = f_MFetch( $res );
	if( !$arr ) printf( "<font color=red>Нет такого игрока</font><br>" );
	else
	{
		$player2 = new Player( $arr[0] );
		$player2->AddToLog( $item_id, $number, 8, $player->player_id );
		if( $item_id == 0 ) $player2->AddMoney( $number );
		else if( $item_id == -1 ) $player2->AddUMoney( $number );
		else
			if ($number >=0)
				$player2->AddItems( $item_id, $number );
			else
				$player2->DropItems($item_id, -$number);
		printf( "<font color=blue>Шмотка успешно добавлена</font><br>" );
	}
}

?>

<a href=index.php>На главную</a><br>
<b>Добавить вещь персонажу</b><br>
<table>
<form action=admin_items.php method=get>
<tr><td>Логин персонажа: </td><td><input type=text name=login class=m_btn></td></tr>
<tr><td>АйДи вещи: </td><td><input type=text name=item_id class=m_btn></td></tr>
<tr><td>Количество: </td><td><input type=text name=number class=m_btn></td></tr>
<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=Добавить></td></tr>
</form>
</table>

<?

f_MClose( );

?>

