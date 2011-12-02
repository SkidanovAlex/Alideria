<?

include( 'player.php' );

f_MConnect( );

$res = f_MQuery( "SELECT * FROM post WHERE np>0 AND deadline < ".(time( )) );
while( $arr = f_MFetch( $res ) )
{
	$id = $arr['entry_id'];

	$att = false;
	$plr = new Player( $arr['sender_id'] );
	
	echo "<br>$plr->login<br>";
	
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
	if( $att ) $plr->syst3( "Никто не забрал вложения к письму &laquo;<b>$arr[title]</b>&raquo;. Вложение было возвращено вам обратно." );
	f_MQuery( "UPDATE post SET money=0, np=0, deadline=0 WHERE entry_id=$id" );
	f_MQuery( "DELETE FROM post_items WHERE entry_id=$id" );
}

?>
