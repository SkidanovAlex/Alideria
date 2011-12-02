<?

if( !$mid_php ) die( );

echo "<b>Старик: </b> Чтобы подманить зайца, нужно читать заклинания, заклинания. Ты жди зайца, а я читать буду.<br>";

?>

<div style='position:relative;top:0px;left:0px;'>

<img src=images/misc/harefield.jpg width=462 height=117>
<table style='position:absolute;left:30px;top:32px;'>

<tr>

<td id=cage><img width=50 height=50 src=images/misc/cage.gif></td>
<td valign=middle><img src=images/misc/hares.gif id=leng height=12 width=150></td>
<td id=cage2><img width=50 height=50 src=images/misc/m2_hare.gif></td>


</tr>

</table>

</div>

<div id=letters>
</div>

<?

f_MQuery( "LOCK TABLE player_quest_values WRITE" );

$quiz = array(

"Беги зайчик в клетку, беги не робей, будешь большим и сильным - как ...",
"Здесь нет никого, малыш, только ты и я, страна у нас большая страна - ...",
"Прекрасная и теплая как летняя капель, имя всем известное ее - ... ",
"Хитрый, гордый, рыжий, ни на кого не похож, мордочку прячет в кустах ...",
"Не нужно ему нот, не нужно ему струн, восседает над Теллой гордый ...",
"Нету лучше имени, нет звучнее звания, горячий как огонь, демиург шагает ... "
);

$quiz2 = array(
"маленькая птичка, любит купаться в лужах",
"вымышленная, нереальная страна",
"имя демиурга Алидерии",
"существо, и имя у него как месяц в году",
"имя дракона",
"имя демиурга Алидерии"
);

// есть копия в эджакс
$quiz_ans = array( "ВОРОБЕЙ", "УТОПИЯ", "АСТАНИЭЛЬ", "ЛИСОЕЖ", "ГОВОРУН", "ПЛАМЕНИ" );

$val = $player->GetQuestValue( 30 );
$dist = $player->GetQuestValue( 31 );
if( !$val )
{
	$val = mt_rand( 1, count( $quiz ) );
	$player->SetQuestValue( 30, $val );

	f_MQuery( "UNLOCK TABLES" );
	$st = '';
	$dist = 0;
	for( $i = 0; $i < strlen( $quiz_ans[$val - 1] ); ++ $i )
	{
		$dist += 2;
		for( $j = 0; $j < $i; ++ $j ) if( $quiz_ans[$val - 1][$j] == $quiz_ans[$val - 1][$i] )
		{
			$dist -= 2;
			break;
		}
		$st .= '.';
	}
	f_MQuery( "DELETE FROM player_mines WHERE player_id={$player->player_id}" );
    f_MQuery( "INSERT INTO player_mines ( player_id, f ) VALUES ( {$player->player_id}, '$st' )" );

    $dist -= 2;
	$player->SetQuestValue( 31, $dist );
}
else
{
	f_MQuery( "UNLOCK TABLES" );
	$res = f_MQuery( "SELECT f FROM player_mines WHERE player_id={$player->player_id}" );
	$arr = f_MFetch( $res );
	$st = $arr[0];
}

-- $val;
echo "<div id=quiz><b>Старик: </b> <i>{$quiz[$val]}</i> Ах, память моя дырявая, конец-то заклинания я и не помню. Помню, что это {$quiz2[$val]}, а само слово не помню, помоги мне вспомнить слово, иначе не поймать нам зайца</div>";

?>

<script>

function word( s, d )
{
	if( d < 0 ) d = 0;

	var st = '<table><tr>';
	var ok = 0;
	for( var i = 0; i < s.length; ++ i )
	{
		if( s.charAt( i ) == '.' ) ok = 1;
		st += '<td><table cellspacing=0 cellpadding=0><tr><td style="width:34px;height:34px;" background=images/misc/hareq.png align=center valign=middle><big><b>' + s.charAt( i ) + '</b></big></td></tr></table></td>';
	}
	st += "<td><b>Буква: </b></td><td><input class=btn40 maxlength=1 type=text id=ltr></td><td><button onclick='suppose();' class=n_btn>Готов</button></td>";
	st += '</tr></table>';
	_( 'letters' ).innerHTML = st;
	_( 'leng' ).style.width = ( 10 * d ) + 'px';

	if( d <= 0 ) _( 'cage' ).innerHTML = '<img src=images/items/quest/cagehare.gif>';
	if( d <= 0 ) _( 'cage2' ).innerHTML = '&nbsp;';

	if( d <= 0 ) _( 'quiz' ).innerHTML = 'Заяц попался в клетку! <a href=game.php?phrase=572>Дальше</a>';
	else if( !ok ) _( 'quiz' ).innerHTML = 'Заяц так и не добежал до клетки. <a href=game.php?phrase=573>Дальше</a>';
}

function suppose( )
{
	query( "quest_scripts/phrase242_ajax.php", _( 'ltr' ).value );
}

<?

echo "word( '$st', $dist );";

?>

</script>
