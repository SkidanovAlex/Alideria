<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$id = $_GET['id'];
settype( $id, 'integer' );

$res = f_MQuery( "SELECT * FROM post WHERE receiver_id={$player->player_id} AND entry_id={$id}" );
$arr = f_MFetch( $res );

if( !$arr ) die( "alert( 'У вас нет такого письма' );" );

$action = $_GET['act'];

if( $action == 'take' )
{
	$ok = false;
	if( $arr['np'] > 0 )
	{
		if( $player->level < 2 ) die( "alert( 'Вы еще слишком малы чтобы получить письма с наложенным платежом' );" );

		if( $player->SpendMoney( $arr['np'] ) )
		{
			$player->AddToLogPost( 0, - $arr['np'], 19, 2 );
			$ok = true;

			$plr = new Player( $arr['sender_id'] );
			$plr->AddMoney( $arr['np'] );
			$plr->AddToLogPost( 0, $arr['np'], 19, 2, $arr['receiver_id'] );
			$plr->syst3( "Игрок <b>{$player->login}</b> выплатил <b>$arr[np]</b> монет и забрал отправленное наложенным платежом письмо <b>$arr[title]</b>" );
		}
		else echo "alert( 'У вас недостаточно дублонов' );";
	}
	else $ok = true;

	if( $ok )
	{
		$player->AddMoney( $arr['money'] );
		$player->AddToLogPost( 0, $arr['money'], 19, 1, $arr['sender_id'] );
		$ares = f_MQuery( "SELECT * FROM post_items WHERE entry_id=$id" );
		while( $aarr = f_MFetch( $ares ) )
		{
			$player->AddItems( $aarr['item_id'], $aarr['number'] );
			$player->AddToLogPost( $aarr['item_id'], $aarr['number'], 19, 1, $arr['sender_id'] );
		}
		f_MQuery( "UPDATE post SET money=0, np=0, deadline=0 WHERE entry_id=$id" );
		f_MQuery( "UPDATE history_post SET type=1 WHERE post_entry_id=$id" );
		f_MQuery( "DELETE FROM post_items WHERE entry_id=$id" );
	}
}
else
{
	$att = false;
	$plr = new Player( $arr['sender_id'] );
	if( $arr['money'] > 0 ) $att = true;
	$plr->AddMoney( $arr['money'] );
	$plr->AddToLogPost( 0, $arr['money'], 19, 3 );
	$ares = f_MQuery( "SELECT * FROM post_items WHERE entry_id=$id" );
	while( $aarr = f_MFetch( $ares ) )
	{
		$att = true;
		$plr->AddItems( $aarr['item_id'], $aarr['number'] );
		$plr->AddToLogPost( $aarr['item_id'], $aarr['number'], 19, 3, $arr['receiver_id'] );
	}
	f_MQuery( "UPDATE history_post SET type=2 WHERE post_entry_id=$id" );
	if( $att ) $plr->syst3( "Игрок <b>{$player->login}</b> вернул все вложения к письму <b>$arr[title]</b> обратно" );
	if( $action == 'refuse' )
	{
		f_MQuery( "UPDATE post SET money=0, np=0, deadline=0 WHERE entry_id=$id" );
		f_MQuery( "DELETE FROM post_items WHERE entry_id=$id" );
	}
	else
	{
		f_MQuery( "DELETE FROM post WHERE entry_id=$id" );
		f_MQuery( "DELETE FROM post_items WHERE entry_id=$id" );
		echo "document.getElementById( 'ltr$id' ).innerHTML = '<font color=red>Удалено</font>';";
		die( "document.getElementById( 'qdescr' ).innerHTML = '&nbsp;';" );
	}
}

echo "update_money( $player->money, $player->umoney );";
echo "query( 'post_read.php?id=$id', '' );";

?>
