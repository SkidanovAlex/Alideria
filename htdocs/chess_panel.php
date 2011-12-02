<?

include_once( 'functions.php' );
include_once( 'chess_functions.php' );
//$link = f_MConnect( );

if( !$mid_php ) die( );

$img_dir = 'images/chess/';

print( "<script>var img_dir = '$img_dir';</script>\n" );

?>

<table style='position:relative;top:0px;left:0px;' cellspacing=0 cellpadding=0 border=0><tr><td style='position:relative;top:0px;left:0px;' valign=top>
<table style='position:relative;top:0px;left:0px;'  width=356 height=352 background='images/chess/board.jpg' cellspacing=0 cellpadding=0 border=0>
<script src=chess_timer.js></script>
<script>
	var mx, my, dg = 0, dg2 = 0;
	var l, t;
	var ol, ot;
	var q;
	var allow_move = 0;
	var adrw = 0;
	
	var figs = new Array( );
	var clrs = new Array( );
	for( i = 0; i < 8; ++ i )
	{
		figs[i] = new Array( );
		clrs[i] = new Array( );
		
		for( j = 0; j < 8; ++ j )
			figs[i][j] = -1;
	}
	
	if( document.all ) brows = 1;
	else brows = 0;

	function dummy( ) { return false; };
	function dw( a ) { document.write( a ); };
	function ge( a ) { return document.getElementById( a ); };
	function stats( a, c )
	{
		st = '<a href="javascript: refr( );">Обновить</a><br>';
		
		if( c < 2 )
		{
			if( a ) st += '<font color=lime><b>Ваш ход</b></font><br>';
			else st += '<font color=red><b>Ход оппонента</b></font><br>';
		}
		else
		{
			if( c == 3 && a ) st += '<b>Вы проиграли</b>';
			else if( c == 3 ) st += '<b>Вы победили</b>';
			else st += '<b>Игра завершилась в ничью</b>';
			
			st += ', ';
			st += 'Вы можете <a href=chess_ref.php?leave=1>выйти</a> из игры<br>';
		}
		
		st += 'Состояние: ';
		
		if( c == 0 ) st += '<font color=white><b>OK</b></font>';
		if( c == 1 ) st += '<font color=aqua><b>Шах</b></font>';
		if( c == 2 ) st += '<font color=lime><b>Пат</b></font>';
		if( c == 3 ) st += '<font color=red><b>Мат</b></font>';
		if( c == 4 ) st += '<font color=lime><b>Ничья</b></font>';
		
		st += '<br>';
		
		ge( 'whos_turn' ).innerHTML = st;
		
		allow_move = ( a && c < 2 );
	}
	function placefig( y, x, id, clr )
	{
		var nm = '' + id;
		if( clr == 1 ) nm += 'b';
		ge( 'f' + y + '' + x ).innerHTML = '<table cellspacing=0 cellpadding=0 border=0 width=32 height=32 background=' + img_dir + 'fig' + nm + '.png><tr><td>&nbsp;</td></tr></table>';
		ge( 'f' + y + '' + x ).onmousedown = assignq;
		
		figs[y][x] = id;
		clrs[y][x] = clr;
	}
	function removefig( y, x )
	{
		ge( 'f' + y + '' + x ).innerHTML = '<img width=32 height=32 src=empty.gif>';
		ge( 'f' + y + '' + x ).onmousedown = '';
		
		figs[y][x] = -1;
	}
	function ask_draw( a )
	{
		if( adrw != a )
		{
			adrw = a;
			if( a == 1 )
			{
				if( confirm( 'Оппонент предлагает вам ничью, вы согласны?' ) )
				{
					ref.location.href = 'chess_draw.php?a=4';
				}
				else ref.location.href = 'chess_draw.php?a=5';
			}
			else if( a == 4 ) alert( 'Оппонент согласился на ничью' );
			else if( a == 5 ) alert( 'Оппонент отклонил ваше предложение на ничью' );
			else if( a == 6 ) alert( 'В течение 50 ходов не было продвижения пешек и взятия фигур, по требованию вашего оппонента игра завершена в ничью' );
			else if( a == 7 ) alert( 'Текущая позиция стола повторяется третий раз, по требованию вашего оппонента игра завершена в ничью' );
		}
	}
	function show_turn( y, x, ny, nx, id )
	{
		var chr = '-';
		if( figs[ny][nx] != -1 ) chr = ':';
		clr = clrs[y][x];
		removefig( y, x );
		removefig( ny, nx );
		letters = new Array( 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H' );
		if( nx != x || ny != y )
		{
			placefig( ny, nx, id, clr );
			var st = letters[x] + ( 1 + y ) + chr + letters[nx] + ( 1 + ny );
			ge( 'last_turn' ).innerHTML = 'Последний ход: ' + st;
		}
	}
	function assignq( e )
	{
		q = this;
		ol = l = parseInt( q.style.left );
		ot = t = parseInt( q.style.top );
		x = parseInt( ( ol - 35 ) / 35 );
		y = parseInt( 7 - ( ot - 35 ) / 34.8 );
		
		if( clrs[y][x] == mycolor )
			dg2 = 1;

		return false;
	}
	function begindrag( e )
	{
		if( !allow_move ) return;
		
		if( brows )
		{
			if( event.button == 1 )
			{
				mx = window.event.x;
				my = window.event.y;
				dg = 1;
			}
		}
		else
		{
			if( e.which == 1 )
			{
				mx = e.screenX;
				my = e.screenY;
				dg = 1;
			}
		}

		return false;
	}
	function drag( e )
	{
		if( dg && dg2 )
		{
			if( brows )
			{
				l += window.event.x - mx;
				t += window.event.y - my;
				mx = window.event.x;
				my = window.event.y;
			}
			else
			{
				l += e.screenX - mx;
				t += e.screenY - my;
				mx = e.screenX;
				my = e.screenY;
			}
			q.style.left = l;
			q.style.top = t;
		}

		return false;
	}
	
	function enddrag( e )
	{
		if( dg && dg2 )
		{
			q.style.left = ol;
			q.style.top = ot;
			dg = 0;
			dg2 = 0;
			
			x = ( ol - 35 ) / 35;
			y = 7 - ( ot - 35 ) / 34.8;
			nx = ( l + 16 - 35 ) / 35; 
			ny = 7 - ( t - 16 - 35 ) / 34.8;
			
			x = parseInt( x );
			y = parseInt( y );
			nx = parseInt( nx );
			ny = parseInt( ny );
			
			if( x < 0 || y < 0 || x >= 8 || y >= 8 ) return;
			if( nx < 0 || ny < 0 || nx >= 8 || ny >= 8 ) return;
			
			ref.location.href = 'chess_make_turn.php?x=' + x + '&y=' + y + '&nx=' + nx + '&ny=' + ny;
		}

		return false;
	}
	
	ltrs = 'ABCDEFGH';
	dw( '<tr><td width=35><img height=34 width=35 src=images/empty.gif></td>' );
	for( i = 0; i < 8; ++ i )
		dw( '<td width=36 align=center>&nbsp;</td>' );
	dw( '<td width=43><img height=34 width=43 src=images/empty.gif></td></tr>' );

	for( i = 7; i >= 0; -- i )
	{
		dw( '<tr><td align=right>&nbsp;</td>' );
		for( j = 0; j < 8; ++ j )
		{
			dw( '<td width=35 height=35><div style="position: absolute; top: ' + ( 35 + parseInt( ( 7 - i ) * 34.8 ) ) + 'px; left: ' + ( 35 + j * 35 ) + 'px;" id=f' + i + '' + j + ' name=f' + i + '' + j + '><img width=36 height=36 src=empty.gif></td>' );
		}
		dw( '<td>&nbsp;</td></tr>' );
	}

	dw( '<tr><td width=35><img height=41 width=35 src=images/empty.gif></td>' );
	for( i = 0; i < 8; ++ i )
		dw( '<td width=36 align=center>&nbsp;</td>' );
	dw( '<td>&nbsp;</td></tr>' );
</script>
</table>
</td><td valign=top><br><br>

<script>
<?

$game_id = my_game_id( );

if( $game_id == -1 )
	die( "alert( 'Вы не в шахматной партии' );</script>" );

$game_color = my_game_color( );

print( "mycolor = $game_color;\n" );

$game = new game( $game_id );
$game->describe( );

print( "var lid={$game->turn};" );

?>

	r = 0;
	tmm = 0;
	
	function refr( )
	{
		if( !r )
		{
			r = 1;
			ref2.location.href='chess_ref.php?lid=' + lid;
			
			clearTimeout( tmm );
			tmm = setTimeout( 'r = 0; refr( );', 15000 );
		}
	}
	function reff( a )
	{
		r = 0;
		lid = a;
		clearTimeout( tmm );
		tmm = setTimeout( 'refr( );', 10000 );
	}
	
	function show_draw( )
	{
		if( ge( 'draw' ).style.display == 'none' )
			ge( 'draw' ).style.display = '';
		else
			ge( 'draw' ).style.display = 'none';
	}
	
document.onmousedown = begindrag;
document.onmousemove = drag;
document.onmouseup = enddrag;
document.body.onselect = dummy;

</script>

<div id=whos_turn name=whos_turn>&nbsp;</div>
<div id=last_turn name=last_turn>&nbsp;</div>
<script>document.write( InsertTimer( 300, '<b>Время до таймаута: ', '</b>', 0, 'refr( );' ) );</script>
<a style='cursor: pointer' href='#' onClick='show_draw( );'>Варианты Ничьей</a><br>
<div id=draw name=draw style='display: none'>
<table border=1><tr><td>
<b>1.</b> <a target=ref href=chess_draw.php?a=1>Предложить оппоненту ничью</a>;<br>
<b>2.</b> "Партия заканчивается в ничью: ... Когда играющий до совершения хода доказывает, что обеими сторонами сделано
по меньшей мере 50 ходов, в течение которых ни одна фигура не была взята и ни
одна пешка не сделала хода".<br>
<a target=ref href=chess_draw.php?a=2>Утверждать, что
в течение 50 ходов не было продвижения пешек и взятия фигур</a>;<br>
<b>3.</b> "Партия заканчивается в ничью: ... По требованию играющего, если одна и та же
позиция возникает три раза, причем очередь хода каждый раз будет за играющим.
Позиция считается повторенной, если фигуры одного и того же наименования и цвета
занимают одни и те же поля и если одинаковы возможности игры этими фигурами (т.е.
если за время повторения позиции не утрачено право рокировки или взятия на проходе)".<br>
<a target=ref href=chess_draw.php?a=3>Утверждать, что данная позиция игрового поля
встречается по меньшей мере третий раз в этой игре</a>;<br>
</td>
</tr></table>
</div>

<iframe width=0 height=0 style='border:0px' name=ref id=ref></iframe>
<iframe width=0 height=0 style='border:0px' name=ref2 id=ref2></iframe>

</td>
</tr></table>

<script>

	refr( );

</script>
