<?

if( !$mid_php ) die( );

$val = f_MValue( "SELECT f FROM player_mines WHERE player_id={$player->player_id}" );
$score = (int)substr( $val, 40 );

function pr_award($act)
{
	global $player;
	f_MQuery( "LOCK TABLE premiums WRITE" );
	$res = f_MQuery( "SELECT deadline FROM premiums WHERE player_id={$player->player_id} AND premium_id=$act" );
	$arr = f_MFetch( $res );
	$deadline = time( ) + 7 * 24 * 60 * 60;
	if( !$arr ) f_MQuery( "INSERT INTO premiums( player_id, premium_id, deadline ) VALUES ( {$player->player_id}, $act, $deadline )" );
	else if( $arr[0] < time( ) ) f_MQuery( "UPDATE premiums SET deadline=$deadline WHERE player_id={$player->player_id} AND premium_id=$act" ); 
	else f_MQuery( "UPDATE premiums SET deadline=deadline+7*24*60*60 WHERE player_id={$player->player_id} AND premium_id=$act" ); 
	f_MQuery( "UNLOCK TABLES" );
}

if( $player->HasTrigger( 95 ) )
{
	echo "<b>Менестрель:</b> Красивая игра, одно наслаждение налюбдать за тем, как играет мастер своего дела. Может еще одну партейку?<br><br>";
	echo "<li><a href=game.php?phrase=1175>Нет, спасибо</a>";
	echo "<li><a href=game.php?phrase=1176>Да, конечно</a>";
}

else if( $score < 20 )
{
	echo "<b>Менестрель:</b> Невероятно! Я не видел такого везения в пары с тех пор, как 10 лет назад.... что в принципе не важно, это было выше всех похвал, фортуна действительно на твоей стороне. Придется согласиться с Фавном, что я самый лучший игрок, но только после тех, кому несказанно везет. Эх, ладно, долг платежом красен. Обещал я Фавну, что вручу два самых дорогих премиума тому, кто сможет обыграть меня в пары, так что теперь они твои.<br><br><i>У вас активируется Премиум-Бои и Премиум-Монстры на неделю</i><br><br>";
	if( $player->HasTrigger( 94 ) )
	{
    	pr_award(0);
    	pr_award(5);
    	$player->SetTrigger( 94, 0 );
	}
	echo "<li><a href=game.php?phrase=1175>Поблагодарить менестреля и уйти</a>";
}
else if( $score == 20 )
{
	echo "<b>Менестрель:</b> Невероятно! Я твое мастерство игры почти сравнимо с моим! Только &laquo;почти&raquo;, конечно, но тем не менее, тем не менее... Придется согласиться с Фавном, что при должном везении кто-то может сыграть так же хорошо, как я. Эх, ладно, долго платежом красен. Обещал я Фавну, что вручу Премиум-Бои тому, кто сможет сыграть в пары так же хорошо как я, так что теперь он твой.<br><br><i>У вас активируется Премиум-Бои на неделю</i><br><br>";
	if( $player->HasTrigger( 94 ) )
	{
    	pr_award(0);
    	$player->SetTrigger( 94, 0 );
	}
	echo "<li><a href=game.php?phrase=1175>Поблагодарить менестреля и уйти</a>";
}
else if( $score > 20 )
{
	echo "<b>Менестрель:</b> Да! Да! Я говорил Фавну, что нет мне равных в эту игру! Говорил ему. А он мне не верил. Спасибо тебе большое за подтверждение моих слов, помог старику не потерять веру в себя в праздник. Но без награды я тебя тоже не оставлю, у нас с Фавном сегодня много премиумов есть, которые мы готовы дарить всем направо и налево. Вот и для тебя Премиум-Добыча есть, держи, теперь он твой.<br><br><i>У вас активируется Премиум-Добыча на неделю</i><br><br>";
	if( $player->HasTrigger( 94 ) )
	{
    	pr_award(1);
    	$player->SetTrigger( 94, 0 );
	}
	echo "<li><a href=game.php?phrase=1175>Поблагодарить менестреля и уйти</a>";
}

?>
