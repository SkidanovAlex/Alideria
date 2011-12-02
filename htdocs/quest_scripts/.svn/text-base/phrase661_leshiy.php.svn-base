<?

if( !$mid_php ) die( );

echo "<table><tr><td vAlign=top>";

echo "<div id='dul' style='position:relative;top:0px;left:0px;width:319px;height:320px;'>";
echo "<img src='images/misc/le/pole.jpg' width=319 height=320>";
echo "<div id='dul1' style='position:absolute;top:30px;left:0px;width:319px;height:290px;'>";
echo "</div>";
echo "</div>";

echo "<td><td valign=top width=170>";

echo "<div style='position:relative;top:0px;left:0px;width:170px;height:171px;'>";
echo "<img src='images/misc/le/must-be.jpg' width=170 height=171>";
echo "<div id='etalon' style='position:absolute;top:30px;left:0px;width:170px;height:141px;'>Moo!";
echo "</div>";
echo "</div>";

echo "<br>";

echo "<div style='position:relative;top:0px;left:0px;width:170px;height:141px;'>";
echo "<img src='images/misc/le/fail-level.jpg' width=170 height=141>";
echo "<div id='lives_left' style='position:absolute;top:40px;left:0px;width:170px;'>Moo!";
echo "</div>";
echo "<div id='levels_left' style='position:absolute;top:100px;left:0px;width:170px;'>Moo!";
echo "</div>";
echo "</div>";

echo "</td><td valign=top width=400>";

?>

<br>
Текст: Чтобы пройти испытание Лешего, нужно на большом поле делать точные копии маленького.  У Вас есть несколько прав на ошибку, но постарайтесь не злить древесного человека.  Как только Вы пройдете это испытание, Вы сразу же сможете пройти в лес. 
<br>
<br>
<div id=act><a href=game.php?phrase=1358>Сдаться</a></div>

<br>
<?


echo "</td></tr></table>";

?>
<div id=dbg>&nbsp;</div>


<script>

var inGame = 0;
var level = -1;
var lives = 10;
var left = 0;
var field = [];
var started = 0;
var delays = [10000, 10, 7, 5, 3.5, 2.5, 1.5];
var inter = 0;

function le_click( id )
{
	if( field[id] >= 2 ) return;
	if( field[id] == 0 ) -- left;
	else -- lives;
	field[id] += 2;
	
	if( lives == 0 )
	{
		alert( "Попыток больше не осталось. Придется начинать заново..." );
		newGame( );
	}
	else if( left == 0 )
	{
		alert( "Отличная работа. Приступаем к следующему уровню!" );
		newLevel( );
	}
	else render( );
}

function newLevel( )
{
	++ level;
	if( level == 7 )
	{
		inGame = 0;
		_( 'act' ).innerHTML = "<a href=game.php?phrase=1359>Дальше</a>";
		render( );
		return;
	}
	
	else 
	{
		inGame = 1;
		field = [];
		var swords = Math.floor( Math.random( ) * 3 ) + 7;
		for( var i = 0; i < swords; ++ i ) field[i] = 0;
		for( var i = swords; i < 16; ++ i ) field[i] = 1;
		for( var i = 0; i < 16; ++ i )
		{
			var j = Math.floor( Math.random( ) * ( i + 1 ) );
			var t = field[i]; field[i] = field[j]; field[j] = t;
		}
		started = ( new Date( ) ).getTime( );
		left = swords;
		
		render( );
		if( inter ) clearInterval( inter );
		inter = setInterval( renderSmall, 150 );
	}
}

function newGame( )
{
	lives = 10;
	level = -1;
	newLevel( );
}

function render( )
{
	var st = '<center><table>';
	var id = 0;
	for( var i = 0; i < 4; ++ i )
	{
		st += '<tr>';
		for( var j = 0; j < 4; ++ j )
		{
			if( field[id] <= 1 ) src = 'negative.png';
			if( field[id] == 3 ) src = 'Negative-red.png';
			if( field[id] == 2 ) src = 'positive.png';
			st += '<td width=60 height=60><img width=60 height=60 src="images/misc/le/' + src + '" onclick="le_click(' + id + ')"></td>';
			++ id;
		}
		st += '</tr>';
	}
	st += "</table></center>";
	
	_( 'levels_left' ).innerHTML = "<center><b>" + ( 7 - level ) + "</b></center>";
	_( 'lives_left' ).innerHTML = "<center><b>" + lives + "</b></center>";
	
	_( 'dul1' ).innerHTML = st;
}

function renderSmall( )
{
	var st = '<center><table>';
	var id = 0;
	for( var i = 0; i < 4; ++ i )
	{
		st += '<tr>';
		for( var j = 0; j < 4; ++ j )
		{
			if( field[id] % 2 == 1 ) src = 'negative-sm.jpg';
			else src = 'positive-sm.jpg';
			st += '<td width=27 height=27><img width=27 height=27 src="images/misc/le/' + src + '" onclick="le_click(' + id + ')"></td>';
			++ id;
		}
		st += '</tr>';
	}
	st += "</table></center>";
	_( 'etalon' ).innerHTML = st;
	var currentTime = ( new Date( ) ).getTime( );
	var opa;
	if( currentTime - started > 1000 * delays[level] ) opa = 0;
	else opa = 1.0 - ( ( currentTime - started ) / ( delays[level] * 1000 ) );
//	var opa = 0.5;
	_( 'etalon' ).style.filter = 'alpha(opacity=' + parseInt( opa * 100 ) + ')';
	_( 'etalon' ).style.opacity = opa;
	
//	_( 'lives_left' ).innerHTML = currentTime;
//	_( 'levels_left' ).innerHTML = started;
}

newGame( );

</script>
