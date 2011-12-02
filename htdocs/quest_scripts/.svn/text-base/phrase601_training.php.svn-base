<?

if( !$mid_php ) die( );

echo "Правила игры просты - нажимаете на кнопку Старт, перед вами появляется слово на 4 секунды, ваша задача за 4 секунд указать, какого цвета слово. Обратите внимание: ваша задача сказать <i>какого цвета слово</i>, а не какой цвет написан на слове!<br><br>";
echo "<li> <a href=game.php?phrase=1249>Ну все, я достаточно натренировался, давай перейдем к реальному испытанию!</a><br><br>";
echo "<div id=drawbox>&nbsp;</div>";

?>

<script>

var clrs = ['red', 'yellow', 'lime', 'white', 'pink', '#4040FF'];
var names = ['Красный', 'Желтый', 'Зеленый', 'Белый', 'Розовый', 'Синий'];
var clr;
var nmc;
var tmo = 0;
var sec = 4;

function check( id )
{
	if( id == clr ) alert( 'Совершенно верно!' );
	else if( id == nmc ) alert( 'Неверно! Будь внимательнее, ты выбрал цвет, который был написан на надписи, а надо указать цвет, которым написана надпись.' );
	else alert( 'Ты не угадал цвет. Попробуй еще раз!' );
	if( tmo ) clearInterval( tmo );
	prep( );
}

function start( )
{
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
	
	sec = 4;
	tmo = setInterval( function( ) {
		-- sec;
		if( sec < 0 )
		{
			clearInterval( tmo );
			alert( 'Вы не успели ответить за 4 секунды!' );
			prep( );
		}
		else _( 'tmr' ).innerHTML = '<big><b><font color=white>' + sec + '</font></b></big>';
	}, 1000 );	
}

function prep( )
{
	var st = '<table cellspacing=0 cellpadding=0 style="background-color:black; width:300px; height:200px;"><tr><td width=100% height=100% align=center valign=middle>';
	st += "<a href='javascript:start()'><font color=white>Старт!</font></a>";
	st += '</td></tr></table>';
	_( 'drawbox' ).innerHTML = st;
}

prep( );

</script>
