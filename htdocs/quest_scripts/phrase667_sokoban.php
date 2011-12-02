<?

if( !$mid_php ) die( );

echo "<table><tr><td width='200' vAlign='top'>";

echo "Правила до смешного простые - толкай ящики на кристаллы, и будет тебе счастье. Если зайдешь в тупик (а ты зайдешь), просто нажми &laquo;Переиграть&raquo;. Всего тебе надо пройти три уровня. Если устанешь и захочешь уйти, нажми &laquo;Сдаться&raquo;. Но тогда в следующий раз придется начитать с первого уровня. Удачи!<br><br>";
echo "<div id='actionsDiv'>";

echo "<li> <a href='javascript:restartLevel()'>Переиграть</a><br>";
echo "<li> <a href='javascript:surrender()'>Сдаться</a><br>";

echo "</div>";

echo "</td><td valign='top'>";

echo "<div id='battlefield' style='position:relative;top:0px;left:0px;'>";

echo "&nbsp;";

echo "</div>";

echo "</td></tr></table>";

?>

<script>

var levels = [['.!#####', '.o..o..', '..##...', '#.OO#..', '.oOO#o#', '..OO#.#', '..##...', '.o..o..', '..#####'],['##.##', '....!', '.o.#.', '#.o#.', '#o.o.', '#.o.#', '#OOO#', '#OO##'],['###O..#', '.oOoO..', '!o#.#o.', '.oO.O..', '###o#o.', '##O.O..']];

var curLevel = 0;
var levelWidth = 0;
var levelHeight = 0;

var playerElem;
var playerPos;
var boxElems;
var boxPos;
var boxLeft;
var boxOnPlace;

var tmo = 0;
var gameDone = 0;

function inside( y, x )
{
	if( x < 0 || y < 0 || x >= levelWidth || y >= levelHeight || levels[curLevel][y].charAt( x ) == '#' ) return false;
	return true;
}

function getBoxByCoord( y, x )
{
	for( var i in boxPos ) if( boxPos[i][0] == y && boxPos[i][1] == x ) return i;
	return -1;
}

var goSoQ = [];
var goSoId = 0;

function processNextGoSo( )
{
	if( goSoQ.length > goSoId )
	{
		++ goSoId;
		goSo( goSoQ[goSoId - 1][0], goSoQ[goSoId - 1][1] );
	}
}

function goSo( dy, dx )
{
	if( gameDone ) return;
	if( tmo ) { goSoQ[goSoId] = ( [dy,dx] ); return; }
	
	var y = playerPos[0];
	var x = playerPos[1];
	var ox = x, oy = y;
	y += dy;
	x += dx;
	var nx = x + dx, ny = y + dy;
	
	if( !inside( y, x ) )
	{
		processNextGoSo( );
		return;
	}
	
	var boxToMove = getBoxByCoord( y, x );
	if( boxToMove != -1 )
	{
		if( getBoxByCoord( ny, nx ) != -1 || !inside( ny, nx ) )
		{
			processNextGoSo( );
			return;
		}
	}
	
	playerPos = [y,x];
	if( boxToMove != -1 )
	{
		boxPos[boxToMove] = [ny, nx];
		var newOnPlace = 0;
		var oldOnPlace = boxOnPlace[boxToMove];
		if( levels[curLevel][ny].charAt( nx ) == 'O' ) newOnPlace = 1;
		if( newOnPlace != oldOnPlace )
		{
			boxLeft += oldOnPlace;
			boxLeft -= newOnPlace;
			if( newOnPlace ) boxElems[boxToMove].src = 'images/misc/race/box_on_final.png';
			else boxElems[boxToMove].src = 'images/misc/race/box.png';
			
			boxOnPlace[boxToMove] = newOnPlace;
		}
	}
	
	if( dx == 1 ) playerElem.src = 'images/misc/race/man_right.png';
	if( dx == -1 ) playerElem.src = 'images/misc/race/man_left.png';
	
	var iteration = 0, maxIter = 5;
	tmo = setInterval( function() { 
		++ iteration;
		playerElem.style.left = Math.ceil( 5 + ( x * iteration + ox * ( maxIter - iteration ) ) * 33 / maxIter ) + "px";
		playerElem.style.top = Math.ceil( 5 + ( y * iteration + oy * ( maxIter - iteration ) ) * 33 / maxIter ) + "px";
		
		if( boxToMove != -1 )
		{
			boxElems[boxToMove].style.left = Math.ceil( 5 + ( nx * iteration + x * ( maxIter - iteration ) ) * 33 / maxIter ) + "px";
			boxElems[boxToMove].style.top = Math.ceil( 5 + ( ny * iteration + y * ( maxIter - iteration ) ) * 33 / maxIter ) + "px";
		}
		
		if( iteration >= maxIter ) { 
			clearInterval( tmo ); 
			tmo = 0; 
			if( boxLeft == 0 )
			{
				if( curLevel < 2 ) { ++ curLevel; restartLevel( ); }
				else { gameDone = 1; _( 'actionsDiv' ).innerHTML = "<li> <a href='game.php?phrase=1367'>Дальше!</a><br>"; }
			}
			else
			{
				processNextGoSo( );
			}
		}
	}, 50 );
}

function restartLevel( )
{
	levelWidth = levels[curLevel][0].length;
	levelHeight = levels[curLevel].length;
	
	_( 'battlefield' ).innerHTML = '';
	for( var i = 0; i < levelHeight; ++ i )
		for( var j = 0; j < levelWidth; ++ j )
		{
			var c = levels[curLevel][i].charAt( j );
			if( c != '#' )
			{
				var elem = document.createElement( 'img' );
				elem.src = ( c == 'O' ) ? 'images/misc/race/final.png': 'images/misc/race/empty.png';
				elem.style.position = 'absolute';
				elem.style.width = '34px';
				elem.style.height = '34px';
				elem.style.left = ( 5 + j * 33 ) + 'px';
				elem.style.top = ( 5 + i * 33 ) + 'px';
				_( 'battlefield' ).appendChild( elem );
			}
		}

	boxElems = [];
	boxPos = [];
	boxOnPlace = [];
	boxLeft = 0;
	for( var i = 0; i < levelHeight; ++ i )
		for( var j = 0; j < levelWidth; ++ j )
		{
			var c = levels[curLevel][i].charAt( j );
			if( c == '!' )
			{
				var elem = document.createElement( 'img' );
				elem.src = 'images/misc/race/man_right.png';
				elem.style.position = 'absolute';
				elem.style.width = '34px';
				elem.style.height = '34px';
				elem.style.left = ( 5 + j * 33 ) + 'px';
				elem.style.top = ( 5 + i * 33 ) + 'px';
				_( 'battlefield' ).appendChild( elem );
				playerElem = elem;
				playerPos = [i,j];
			}
			if( c == 'o' )
			{
				var elem = document.createElement( 'img' );
				elem.src = 'images/misc/race/box.png';
				elem.style.position = 'absolute';
				elem.style.width = '34px';
				elem.style.height = '34px';
				elem.style.left = ( 5 + j * 33 ) + 'px';
				elem.style.top = ( 5 + i * 33 ) + 'px';
				_( 'battlefield' ).appendChild( elem );
				boxElems.push( elem );
				boxPos.push( [i,j] );
				boxOnPlace.push( 0 );
				++ boxLeft;
			}
		}
}

function surrender( )
{
	if( confirm( 'Вы действительно хотите сдаться? В следующий раз придется начать игру заново.\n' ) )
		location.href= 'game.php?phrase=1366';
}

restartLevel( );

document.onkeydown = function( event ) {
	var e = event || window.event;
	var k = e.keyCode;
	if( k == 37 ) goSo( 0, -1 );
	if( k == 39 ) goSo( 0, 1 );
	if( k == 38 ) goSo( -1, 0 );
	if( k == 40 ) goSo( 1, 0 );
}

</script>
