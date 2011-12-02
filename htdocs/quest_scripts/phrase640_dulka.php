<?

if( !$mid_php ) die( );

echo "<table><tr><td vAlign=top>";

echo "<div id='dul' style='position:relative;top:0px;left:0px;width:319px;height:320px;'>";
echo "<img src='images/misc/q7/dul/bg.jpg' width=319 height=320>";
echo "<div id='dul1' style='position:absolute;top:0px;left:0px;width:319px;height:320px;'>";
echo "</div>";
echo "<div id='dul2' style='position:absolute;top:0px;left:0px;width:319px;height:320px;'>";
echo "<img src='empty.gif' width=319 height=320 onmousemove='mmove(event)' onclick='turn()'>";
echo "</div>";
echo "</div>";

echo "</td><td valign=top width=400>";

?>

<img src='images/misc/q7/dul/vertical.png' width=0 height=0>
<img src='images/misc/q7/dul/horizontal.png' width=0 height=0>

<div id=dscore style='width:145px;height:62px;background-image:url(images/misc/q7/dul/bg_s.jpg);text-align:center;vertical-align:middle;'>
&nbsp;
</div>
<br>
Твоя задача - отобрать у БезПонтов как можно больше
сундуков. А правила простые: Вы с БезПонтов ходите по
очереди. Каждый из Вас соединяет два сундука за ход. Но
при образовании квадрата, Вы захватываете територию
себе, отмечая это очень красноречивым символом.<br>
<br>
Кто больше отвоюет територии - тот и выиграл. Удачи.<br>
p.s. БезПонтов - жулик<br>
<br>
<br>
<div id=act><a href=game.php?phrase=1305>Выйти из игры</a></div>

<br>
<?


echo "</td></tr></table>";

?>
<div id=dbg>&nbsp;</div>


<script>

function newArr()
{
	var ret = [];
	for( var i = 0; i < 6; ++ i )
	{
		ret[i] = [];
		for( var j = 0; j < 6; ++ j )
		{
			ret[i][j] = 0;
		}
	}
	return ret;
}

var a = newArr();
var b = newArr();
var c = newArr();

var myTurn = 1;
var gameOver = 0;
var score = [0,0];

function check( id )
{
	var ret = 0;
	for( var i = 0; i < 5; ++ i )
		for( var j = 0; j < 5; ++ j ) if( !c[i][j] )
		{
			if( a[i][j] == 3 && ( a[i + 1][j] & 1 ) && ( a[i][j + 1] & 2 ) )
			{
				++ score[id];
				c[i][j] = 1 + id;
				ret = 1;
			}
		}
	if( score[0] + score[1] == 25 && score[0] > score[1] ) 
	{
		_( 'act' ).innerHTML = '<a href=game.php?phrase=1335>Игра пройдена! Дальше...</a>';
	}
	return ret;
}

function aiTurn( )
{
	if( gameOver )  return;
	myTurn = 0;
	var pos = [];
	for( var i = 0; i < 6; ++ i )
		for( var j = 0; j < 6; ++ j )
		{
			if( j < 5 && !( a[i][j] & 1 ) ) pos.push( [i, j, 1] );
			if( i < 5 && !( a[i][j] & 2 ) ) pos.push( [i, j, 2] );
			if( i < 5 && j < 5 ) if( !c[i][j] )
			{
				if( a[i][j] == 3 )
				{
					if( a[i + 1][j] & 1 )
					{
						a[i][j + 1] |= 2;
                   		check( 1 );
                   		setTimeout( aiTurn, 500 );
                  		ref( );
                   		return;
					}
					if( a[i][j + 1] & 2 )
					{
						a[i + 1][j] |= 1;
                   		check( 1 );
                   		setTimeout( aiTurn, 500 );
                  		ref( );
                   		return;
					}
				}
				else if( ( a[i + 1][j] & 1 ) && ( a[i][j + 1] & 2 ) )
				{
					if( a[i][j] & 1 )
					{
						a[i][j] |= 2;
                   		check( 1 );
                   		setTimeout( aiTurn, 500 );
                  		ref( );
                   		return;
					}
					if( a[i][j] & 2 )
					{
						a[i][j] |= 1;
                   		check( 1 );
                  		setTimeout( aiTurn, 500 );
                  		ref( );
                   		return;
					}
				}
			}
		}
	var which = Math.floor( Math.random( ) * pos.length );
	a[pos[which][0]][pos[which][1]] |= pos[which][2];
	if( check( 1 ) )
	{
		setTimeout( aiTurn, 500 );
	}
	else myTurn = 1;
	ref( );
}

function turn( e )
{
	if( !myTurn ) return;
	var ok = 0;
	for( var i = 0; i < 6; ++ i )
	{
		for( var j = 0; j < 6; ++ j )
		{
			if( ( a[i][j] | b[i][j] ) != a[i][j] )
			{
				a[i][j] |= b[i][j];
				ok = 1;
			}
		}
	}
	if( !ok ) return;
	b = newArr();
	
	ref( );
	if( !check( 0 ) ) aiTurn( );
}

function mmove( e )
{
	var x, y;
	if( document.all )
	{
		var p = getAp( _( 'dul1' ) );
		x = window.event.clientX - p.x;
		y = window.event.clientY - p.y;
	}
	else
	{
		var p = getAp( _( 'dul1' ) );
		x = e.pageX - p.x;
		y = e.pageY - p.y;
	}

//	_( 'dbg' ).innerHTML = x + ' ' + y;
	
	x -= 13 + 15 - 20;
	y -= 15 + 12 - 20;
	xx = Math.floor( x / 53 );
	yy = Math.floor( y / 53 );
	x -= xx * 53;
	y -= yy * 53;
	
	b = newArr();
	
	if( xx < 0 || yy < 0 || xx > 5 || yy > 5 ) return;
	
	if( x > y && xx < 5 ) b[yy][xx] = 1;
	else if( y > x && yy < 5 ) b[yy][xx] = 2;
	
	ref( );
}

function ref( )
{
	var st = "";
	
	for( var i = 0; i < 6; ++ i )
		for( var j = 0; j < 6; ++ j )
		{
			var x = 13 + j * 53 + 10;
			var y = 15 + i * 53 + 7;

			if( c[i][j] )
			{
				st += '<img src="images/misc/q7/dul/dulia_anime_' + c[i][j] + '.gif" style="position:absolute;left:' + (x+1) + 'px;top:' + y + 'px;">';
			}

			if( ( a[i][j] & 1 ) || ( b[i][j] & 1 ) ) st += '<img src="images/misc/q7/dul/horizontal.png" style="position:absolute;left:' + x + 'px;top:' + y + 'px;">';
			if( ( a[i][j] & 2 ) || ( b[i][j] & 2 ) ) st += '<img src="images/misc/q7/dul/vertical.png" style="position:absolute;left:' + x + 'px;top:' + (15+y) + 'px;">';

			x -= 10; y -= 7;
			
			st += '<img src="images/misc/q7/dul/chest.png" style="position:absolute;left:' + x + 'px;top:' + y + 'px;">';
			st += '<img src="empty.gif" id=img' + (i*6+j)  + ' style="position:absolute;left:' + (x+1) + 'px;top:' + y + 'px;">';
		}

	_( 'dul1' ).innerHTML = st;
//	if( !document.all ) _( 'dscore' ).innerHTML = '<center><table cellspacing=0 cellpadding=0><tr><td><big><big><big><big><b>' + score[0] + '</b></big></big></big></big></td><td><img src=images/misc/q7/dul/dulia_anime_1.gif><img style="margin-left:-25px;" src=images/misc/q7/dul/dulia_anime_2.gif></td><td><big><big><big><big><b>' + score[1] + '</big></big></big></big></td></tr></table></center>';
	_( 'dscore' ).innerHTML = '<center><table cellspacing=0 cellpadding=0><tr><td><table cellspacing=0 cellpadding=0><tr><td><big><big><big><b>' + score[0] + '</b></big></big></big></td><td><img src=images/misc/q7/dul/dulia_anime_1.gif></td></tr></table></td><td><table style="margin-left:-25px;" cellspacing=0 cellpadding=0><tr><td><img src=images/misc/q7/dul/dulia_anime_2.gif></td><td><big><big><big><b>' + score[1] + '</big></big></big></td></tr></table></td></tr></table></center>';
}

ref( );

var lst = 0;
setInterval( function() { if( Math.random( ) < 0.7 ) return ; var a = Math.floor(Math.random() * 36); _( 'img' + a ).src = 'images/misc/q7/dul/chest.gif'; _( 'img' + lst ).src = 'empty.gif'; lst = a; }, 250 );

</script>
