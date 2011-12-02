<?

if( !isset( $mid_php ) ) die( );

if( 0 == ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_SEND_POST ) )
{
	echo( "У вас нет прав работать с этим разделом Ордена.<br><a href=game.php?order=main>Назад</a>" );
	return;
}
echo "<b>Почта Ордена</b> - <a href=game.php?order=main>Назад</a><br>";
echo "<br>";


if( isset( $_POST['txt'] ) && strlen( trim( $_POST['txt'] ) ) > 0 )
{
	$st = $_POST['txt'];
	$str = substr( $st, 0, 2000 );
	$str = htmlspecialchars( $str, ENT_QUOTES );
	$msg = substr( $st, 0, 201 );
	$msg = htmlspecialchars( $msg, ENT_QUOTES );
	if( strlen( $msg ) == 201 ) $msg = substr( $msg, 0, 180 )."...";

	$res = f_MQuery( "SELECT player_id FROM player_clans WHERE clan_id=$clan_id" );
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr[0] );
		$plr->syst2( $msg );
		f_MQuery( "INSERT INTO post( sender_id, receiver_id, title, content, money, np, deadline ) VALUES ( $player->player_id, $arr[0], 'Рассылка Ордену', '$str', '0', '0', '0' )" );
	}

	echo "<b><font color=darkgreen>Сообщение послано</font></b><br><a href=game.php?order=post>Послать еще одно сообщение</a>";
}
else
{
    echo "Послать сообщение всем членам Ордена:<br>";
    echo "<form action=game.php?order=post method=post>";
    echo "<textarea cols=30 rows=5 name=txt class=te_btn></textarea><br>";
    echo "<input type=submit class=s_btn value=Отправить>";
    echo "</form>";
}

?>
