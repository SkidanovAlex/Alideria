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
		st = '<a href="javascript: refr( );">��������</a><br>';
		
		if( c < 2 )
		{
			if( a ) st += '<font color=lime><b>��� ���</b></font><br>';
			else st += '<font color=red><b>��� ���������</b></font><br>';
		}
		else
		{
			if( c == 3 && a ) st += '<b>�� ���������</b>';
			else if( c == 3 ) st += '<b>�� ��������</b>';
			else st += '<b>���� ����������� � �����</b>';
			
			st += ', ';
			st += '�� ������ <a href=chess_ref.php?leave=1>�����</a> �� ����<br>';
		}
		
		st += '���������: ';
		
		if( c == 0 ) st += '<font color=white><b>OK</b></font>';
		if( c == 1 ) st += '<font color=aqua><b>���</b></font>';
		if( c == 2 ) st += '<font color=lime><b>���</b></font>';
		if( c == 3 ) st += '<font color=red><b>���</b></font>';
		if( c == 4 ) st += '<font color=lime><b>�����</b></font>';
		
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
				if( confirm( '�������� ���������� ��� �����, �� ��������?' ) )
				{
					ref.location.href = 'chess_draw.php?a=4';
				}
				else ref.location.href = 'chess_draw.php?a=5';
			}
			else if( a == 4 ) alert( '�������� ���������� �� �����' );
			else if( a == 5 ) alert( '�������� �������� ���� ����������� �� �����' );
			else if( a == 6 ) alert( '� ������� 50 ����� �� ���� ����������� ����� � ������ �����, �� ���������� ������ ��������� ���� ��������� � �����' );
			else if( a == 7 ) alert( '������� ������� ����� ����������� ������ ���, �� ���������� ������ ��������� ���� ��������� � �����' );
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
			ge( 'last_turn' ).innerHTML = '��������� ���: ' + st;
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
	die( "alert( '�� �� � ��������� ������' );</script>" );

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
<script>document.write( InsertTimer( 300, '<b>����� �� ��������: ', '</b>', 0, 'refr( );' ) );</script>
<a style='cursor: pointer' href='#' onClick='show_draw( );'>�������� ������</a><br>
<div id=draw name=draw style='display: none'>
<table border=1><tr><td>
<b>1.</b> <a target=ref href=chess_draw.php?a=1>���������� ��������� �����</a>;<br>
<b>2.</b> "������ ������������� � �����: ... ����� �������� �� ���������� ���� ����������, ��� ������ ��������� �������
�� ������� ���� 50 �����, � ������� ������� �� ���� ������ �� ���� ����� � ��
���� ����� �� ������� ����".<br>
<a target=ref href=chess_draw.php?a=2>����������, ���
� ������� 50 ����� �� ���� ����������� ����� � ������ �����</a>;<br>
<b>3.</b> "������ ������������� � �����: ... �� ���������� ���������, ���� ���� � �� ��
������� ��������� ��� ����, ������ ������� ���� ������ ��� ����� �� ��������.
������� ��������� �����������, ���� ������ ������ � ���� �� ������������ � �����
�������� ���� � �� �� ���� � ���� ��������� ����������� ���� ����� �������� (�.�.
���� �� ����� ���������� ������� �� �������� ����� ��������� ��� ������ �� �������)".<br>
<a target=ref href=chess_draw.php?a=3>����������, ��� ������ ������� �������� ����
����������� �� ������� ���� ������ ��� � ���� ����</a>;<br>
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
