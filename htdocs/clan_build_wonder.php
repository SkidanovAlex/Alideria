<?

include_once( 'prof_exp.php' );

if( !isset( $mid_php ) ) die( );


// actions

if( ( $player->regime == 114 || $player->regime == 117 ) && $player->till <= time( ) + 2 )
{
	include_once( "prof_exp.php" );
	$val = 1;
	$coef = 1;
	$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=1" ) );
	if( $barr[0] )  $val = 2;
	if( $player->regime == 117 ) $coef = 6;
	$val *= $coef;
	f_MQuery( "UPDATE clan_wonders SET work=work + $val WHERE clan_id=$clan_id AND wonder_id=$cur_wonder" );
	f_MQuery( "UPDATE clan_wonders SET work=750 WHERE work > 750 AND stage < 10" );
	f_MQuery( "UPDATE clan_wonders SET work=2500 WHERE work > 2500" );
	f_MQuery( "LOCK TABLE player_wonders WRITE" );
	$res = f_MQuery( "SELECT * FROM player_wonders WHERE clan_id=$clan_id AND player_id={$player->player_id} AND wonder_id=$cur_wonder" );
	$arr = f_MFetch( $res );
	if( !$arr ) f_MQuery( "INSERT INTO player_wonders( clan_id, player_id, wonder_id, score ) VALUES ( $clan_id, {$player->player_id}, $cur_wonder, 1 )" );
	else f_MQuery( "UPDATE player_wonders SET score=score+$val WHERE clan_id=$clan_id AND player_id={$player->player_id} AND wonder_id=$cur_wonder" );
	f_MQuery( "UNLOCK TABLES" );
	AlterProfExp( $player, 12 * $coef );
	$player->AlterQuestValue( 983, $val );
	$player->SetRegime( 0 );
	$player->SetTill( 0 );

	$code=rand(1000,9999);

	f_MQuery( "LOCK TABlE player_num WRITE" );
	f_MQuery( "DELETE FROM player_num WHERE player_id = $player->player_id" );
	f_MQuery( "INSERT INTO player_num VALUES ( $player->player_id, $code )" );
	f_MQuery( "UNLOCK TABLES" );
}

if( isset( $_GET['num'] ) )
{
	$ipstr = addslashes( getenv( "REMOTE_ADDR" ) );
	if( $player->regime == 0 )
	{
		f_MQuery( "DELETE FROM clan_wonder_ips WHERE player_id={$player->player_id}" );
		if( f_MValue( "SELECT count( player_id ) FROM clan_wonder_ips WHERE ip='$ipstr' AND clan_id=$clan_id" ) )
		{
			echo "<script>alert( 'В течение последнего часа с вашего айпи другой персонаж делал работу для этого Ордена.' );</script>";
		}
		else
		{
    		$num = f_MValue( "SELECT number FROM player_num WHERE player_id={$player->player_id}" );
    		if( $num != $_GET['num'] ) echo "<font color=darkred>Введите правильный код в окне</font><br>";
    		else {
    			$player->SetRegime( 114 );
    			$player->SetTill( time( ) + 10 * 60 );
    			f_MQuery( "INSERT INTO clan_wonder_ips VALUES  ( {$player->player_id}, '{$ipstr}', ".time( ).", $clan_id )" );
    		}
		}
	}
}
else if( $player->regime == 114 && isset( $_GET['prolong'] ) )
{
	$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=4" ) );
	if( $barr[0] ) 
	{
		$player->SetRegime( 117 );
		$player->SetTill( $player->till + 50 * 60 );
	}
}
else if( isset( $_GET['cancel'] ) )
{
	if( $player->regime == 114 || $player->regime == 117 )
	{
		$player->SetRegime( 0 );
		$player->SetTill( 0 );
	}
}


// rendering

echo "<b>Строительство Чуда Света</b> - <a href=game.php?order=main>Назад</a><br>";
echo "<br>";

$res = f_MQuery( "SELECT stage, work FROM clan_wonders WHERE clan_id=$clan_id AND wonder_id=$cur_wonder" );
$arr = f_MFetch( $res );
	
$stage = (int)$arr[0];
$work = (int)$arr[1];

if( !$arr )
{
	if( 0 != ( getPlayerPermitions( $player->clan_id, $player->player_id ) & 4 ) )
	{
		if( isset( $_GET['place'] ) )
		{
			$cell = (int)$_GET['place'];
			if( $cell < 0 || $cell >= 3 ) RaiseError( "Неверная позиция для строительства ЧС", "$cell" );
			$moo = array( 6, 8, 12 ); $cell = $moo[$cell];
			f_MQuery( "LOCK TABLE clan_wonders WRITE" );
			$res = f_MQuery( "SELECT stage, work FROM clan_wonders WHERE clan_id=$clan_id AND wonder_id=$cur_wonder" );
			$arr = f_MFetch( $res );
			$res2 = f_MQuery( "SELECT stage, work FROM clan_wonders WHERE clan_id=$clan_id AND cell_id=$cell" );
			$arr2 = f_MFetch( $res2 );

			if( !$arr && !$arr2 )
			{
				f_MQuery( "INSERT INTO clan_wonders( clan_id, wonder_id, cell_id ) VALUES ( $clan_id, $cur_wonder, $cell )" );
				$res = f_MQuery( "SELECT stage, work FROM clan_wonders WHERE clan_id=$clan_id AND wonder_id=$cur_wonder" );
				$arr = f_MFetch( $res );
			}

			$stage = (int)$arr[0];
			$work = (int)$arr[1];

			f_MQuery( "UNLOCK TABLES" );
		}
		if( !$arr )
		{
			echo "<b>Выберите место для строительства:</b>";
			echo "<script>document.write( ".render_camp( $clan_id, false, true ).");</script>";
		}
	}
	else echo "<i>Только член Ордена с правами на строительство может начать строительство Чуда Света</i>";

}

if( !$arr ) ;
else if( $stage < 10 )
{
	$stages = array( "1-ый", "2-ой", "3-ий", "4-ый", "5-ый", "6-ой", "7-ой", "8-ой", "9-ый", "10-ый" );
	echo "<table cellspacing=0 cellpadding=0 border=0>";
	echo "<tr><td>Текущая фаза строительства:&nbsp;</td><td><b>Подготовка, {$stages[$stage]} этап</b></td></tr>";
	echo "<tr><td>Необходимо выполнить работы:&nbsp;</td><td><b>750</b></td>";
	echo "<tr><td>Работы выполнено:&nbsp;</td><td><b>$work</b></td>";
	echo "<table><br>";

	echo "<b>Работать:</b><br>";
	if( $work == 750 && $player->regime == 0 )
	{
		echo "<i>Работа по текущему этапу завершена, ожидается поставка ресурсов на следующий этап</i><br>";
	}
	else if( $player->regime == 0 )
	{
        echo "<table cellspacing=0 cellpadding=0 border=0><tr><td><div id=num_img><img src=captcha/code.php width=90 height=40 border=1 bordercolor=black></div></td><td>&nbsp;";
		$oncl = 'location.href= "game.php?order=wonders&num=" + document.getElementById( "num" ).value;document.getElementById( "num" ).value="";';
		echo "<input onkeydown='e = event || window.event;if( e.keyCode == 13 ) { $oncl }' type=text class=te_btn size=4 maxlength=4 name=num id=num></td><td>&nbsp;<button class=n_btn onClick='$oncl' class=ss_btn>Работать</button></td></tr></table>";
		echo "<small>(Если вы не можете разобрать цифр, нажмите <a href=# onclick='reload();'>сюда</a>, чтобы обновить картинку).</small><br>";
		echo "Работа длится 10 минут и приносит 12ПО. <a href='javascript:premiums()'>Влияние Премиумов</a><br>";
		echo "<script src='js/numkeyboard.js'></script><script>showkeyboard('num');</script>";
				?>
				<script>function reload () {
                	var rndval = new Date().getTime(); 
                	document.getElementById('num_img').innerHTML = '<img width=90 height=40 src=captcha/code.php?rnd=' + rndval + ' border=1 bordercolor=black>';
                }function premiums() { alert( 'Премиум-работа увеличивает получаемый ПО до 18 единиц за раз;\nПремиум-свобода позволяет работать в течение часа без необходимости быть в сети;\nПремиум-добыча удваивает результативность заходов.' ); }</script>
                
				<?

	}
	else
	{
		$rem = $player->till - time( );
		include_js( 'js/timer.js' );
		echo "<script>show_timer_title = true;document.write( InsertTimer( $rem, '<font color=darkgreen><b>Вы работаете.</b></font><br>Осталось: <b>', '</b>', 1, 'location.href=\"game.php\"' ) );</script>";
		if( $player->regime == 114 )
		{
			$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=4" ) );
			if( $barr[0] ) 
			{
				echo "<a href=game.php?order=wonders&prolong>Работать 60 минут</a><br>";
			}
		}
		echo "<a href=game.php?order=wonders&cancel=1>Отменить работу</a><br>";
	}

	echo "<br>";

	echo "<b>Необходимо поставить на красную полку склада:</b><br><small>Ресурсы будут списаны в течение минуты после завершения работы</small><br>";
	echo "<table cellspacing=0 cellpadding=0 border=0>";
	$arr = $wonder_res[$stage];
	foreach( $arr as $item_id=>$number )
	{
		if( $item_id == 0 ) $name = "Дублоны";
		else if( $item_id == -1 ) $name = "Еда";
		else $name = "<a target=_blank href=help.php?id=1010&item_id=$item_id>".f_MValue( "SELECT name FROM items WHERE item_id=$item_id" )."</a>";
		if( $item_id == 0 ) $have = f_MValue( "SELECT money FROM clans WHERE clan_id=$clan_id" );
		else if( $item_id == -1 ) $have = f_MValue( "SELECT food FROM clans WHERE clan_id=$clan_id" );
		else $have = (int)f_MValue( "SELECT number FROM clan_items WHERE clan_id=$clan_id AND item_id=$item_id AND color=0" );
		if( $have < $number ) $st = "<small><font color=darkred>$have</font></small>/$number";
		else $st = "<small>$have</small>/$number";
		echo "<tr><td>$name&nbsp;</td><td><b>$st</b></td></tr>";
	}
	echo "</table>";

}
else
{
	$stage -= 10;
	$stages = array( "1-ый", "2-ой", "3-ий", "4-ый", "5-ый", "6-ой", "7-ой", "8-ой", "9-ый", "10-ый" );
	echo "<table cellspacing=0 cellpadding=0 border=0>";
	echo "<tr><td>Текущая фаза строительства:&nbsp;</td><td><b>Завершающая, {$stages[$stage]} этап</b></td></tr>";
	echo "<tr><td>Необходимо выполнить работы:&nbsp;</td><td><b>2500</b></td>";
	echo "<tr><td>Работы выполнено:&nbsp;</td><td><b>$work</b></td>";
	echo "<table><br>";

	echo "<b>Работать:</b><br>";
	if( $work == 2500 && $player->regime == 0 )
	{
		echo "<i>Работа по текущему этапу завершена, ожидается поставка ресурсов на следующий этап</i><br>";
	}
	else if( $player->regime == 0 )
	{
        echo "<table cellspacing=0 cellpadding=0 border=0><tr><td><div id=num_img><img src=captcha/code.php width=90 height=40 border=1 bordercolor=black></div></td><td>&nbsp;";
		$oncl = 'location.href= "game.php?order=wonders&num=" + document.getElementById( "num" ).value;document.getElementById( "num" ).value="";';
		echo "<input onkeydown='e = event || window.event;if( e.keyCode == 13 ) { $oncl }' type=text class=te_btn size=4 maxlength=4 name=num id=num></td><td>&nbsp;<button class=n_btn onClick='$oncl' class=ss_btn>Работать</button></td></tr></table>";
		echo "<small>(Если вы не можете разобрать цифр, нажмите <a href=# onclick='reload();'>сюда</a>, чтобы обновить картинку).</small><br>";
		echo "<script src='js/numkeyboard.js'></script><script>showkeyboard('num');</script>";
				?>
				<script>function reload () {
                	var rndval = new Date().getTime(); 
                	document.getElementById('num_img').innerHTML = '<img width=90 height=40 src=captcha/code.php?rnd=' + rndval + ' border=1 bordercolor=black>';
                }</script>
				<?

	}
	else
	{
		$rem = $player->till - time( );
		include_js( 'js/timer.js' );
		echo "<script>show_timer_title = true;document.write( InsertTimer( $rem, '<font color=darkgreen><b>Вы работаете.</b></font><br>Осталось: <b>', '</b>', 1, 'location.href=\"game.php\"' ) );</script>";
		if( $player->regime == 114 )
		{
			$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=4" ) );
			if( $barr[0] ) 
			{
				echo "<a href=game.php?order=wonders&prolong>Работать 60 минут</a><br>";
			}
		}
		echo "<a href=game.php?order=wonders&cancel=1>Отменить работу</a><br>";
	}

	echo "<br>";
}

?>
