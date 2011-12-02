<?

if( !$mid_php ) die( );

function pr_award($act, $len)
{
	global $player;
	f_MQuery( "LOCK TABLE premiums WRITE" );
	$res = f_MQuery( "SELECT deadline FROM premiums WHERE player_id={$player->player_id} AND premium_id=$act" );
	$arr = f_MFetch( $res );
	$deadline = time( ) + $len * 24 * 60 * 60;
	if( !$arr ) f_MQuery( "INSERT INTO premiums( player_id, premium_id, deadline ) VALUES ( {$player->player_id}, $act, $deadline )" );
	else if( $arr[0] < time( ) ) f_MQuery( "UPDATE premiums SET deadline=$deadline WHERE player_id={$player->player_id} AND premium_id=$act" ); 
	else f_MQuery( "UPDATE premiums SET deadline=deadline+$len*24*60*60 WHERE player_id={$player->player_id} AND premium_id=$act" ); 
	f_MQuery( "UNLOCK TABLES" );
}

if( isset( $_GET['wins'] ) || $player->HasTrigger( 217 ) )
{
	if( !$player->HasTrigger( 217 ) )
	{
		$player->SetTrigger( 217 );
		$wins = (int)$_GET['wins'];
		if( $wins < 1 ) $wins = 1;
		if( $wins > 7 ) $wins = 7;
		$player->SetQuestValue( 42, $wins );
		
		if( !$player->sex ) $d = 1;
		else $d = $wins;
    	pr_award(0, $d);
    	pr_award(5, $d);
	}
	
	$wins = $player->GetQuestValue( 42 );
	echo "<b>Шамаханский торговец: </b>";
	if( !$player->sex ) echo "Вах-вах-вах, какой молодец. Вот типе такой подарок, чтоби жизнь твоя била приятней. Гародские чиновники паздравляют тепя с победой и дают один день премиума-боев и премиума-монстров.";
	else echo "Вах-вах-вах, какая умница. Вот типе такой подарок, чтоби жизнь твоя била приятней. Гарадские чиновники паздравляют типя с праздником восьмого марта, желают типе красоты, здоровья, творческих успехов. А так как ты выиграла $wins партий, то ти получаишь <b>$wins</b> дней премиума-боев и премиума-монстров.";
	echo "<br><br>";
	
	echo "<li> <a href=game.php?phrase=1251>Спасибо большое!</a><br><br>";
	
	return;
}

echo "Правила игры просты - нажимаете на кнопку Старт, перед вами появляется слово на 4 секунды, ваша задача за 4 секунд указать, какого цвета слово. Обратите внимание: ваша задача сказать <i>какого цвета слово</i>, а не какой цвет написан на слове!<br><br>";
echo "<div id=drawbox>&nbsp;</div>";

?>

<script>

var clrs = ['red', 'yellow', 'lime', 'white', 'pink', '#4040FF'];
var names = ['Красный', 'Желтый', 'Зеленый', 'Белый', 'Розовый', 'Синий'];
var clr;
var nmc;
var tmo = 0;
var sec = 4;
var games = 0;
var wins = 0;
var last_win = 0;

function check( id )
{
	if( id == clr )
	{
		alert( 'Совершенно верно!' );
		last_win = 1;
		++ wins;
	}
	else if( id == nmc ) alert( 'Неверно! Будь внимательнее, ты выбрал цвет, который был написан на надписи, а надо указать цвет, которым написана надпись.' );
	else alert( 'Ты не угадал цвет. Попробуй еще раз!' );
	if( tmo ) clearInterval( tmo );
	prep( );
}

function start( )
{
	last_win = 0;
	++ games;
	clr = Math.floor( Math.random( ) * 6 );
	do {
		nmc = Math.floor( Math.random( ) * 6 );
	} while( nmc == clr );
	
	var st = '<table cellspacing=0 cellpadding=0 style="background-color:black; width:300px; height:200px;"><tr><td width=100% height=100% align=center valign=middle>';
	st += "<big><big><b><font color=" + clrs[clr] + ">" + names[nmc] + "</b></big></big></font><br><br>";
	for( var i = 0; i < 6; ++ i )
		st += "<a href='javascript:check(" + i + ")'><font color=white>" + names[i] + "</font></a><br>";
	st += "<br><div id='tmr'><big><b><font color=white>4</font></b></big></div>";
	st += '</td></tr></table>';
	_( 'drawbox' ).innerHTML = st;
	
	sec = 3;
	tmo = setInterval( function( ) {
		-- sec;
		if( sec < 0 )
		{
			clearInterval( tmo );
			alert( 'Вы не успели ответить за 3 секунды!' );
			prep( );
		}
		else _( 'tmr' ).innerHTML = '<big><b><font color=white>' + sec + '</font></b></big>';
	}, 1000 );	
}

function prep( )
{
	if( wins > 0 && games >= 7 && last_win )
	{
		location.href='game.php?wins=' + wins;
	}
	var st = '<table cellspacing=0 cellpadding=0 style="background-color:black; width:300px; height:200px;"><tr><td width=100% height=100% align=center valign=middle>';
	st += "<a href='javascript:start()'><font color=white>Старт!</font></a>";
	st += '</td></tr></table>';
	_( 'drawbox' ).innerHTML = st;
}

prep( );

</script>
