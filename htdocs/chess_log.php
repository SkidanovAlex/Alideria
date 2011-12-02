<?

include( "functions.php" );
include( "chess_functions.php" );

$img_dir = 'images/chess/';

print( "<script>var img_dir = '$img_dir';</script>\n" );

$link = f_MConnect( );

$id = $HTTP_GET_VARS[id];
settype( $id, 'integer' );

if( !mysql_num_rows( f_MQuery( "SELECT * FROM chess_opponents WHERE id=$id" ) ) )
	die( 'Нет такой игры' );
	
?>


<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">


<table width=356 height=352 background='images/chess/board.jpg' cellspacing=0 cellpadding=0 border=0>
<script>

	function dw( a ) { document.write( a ); };
	function ge( a ) { return document.getElementById( a ); };

	var figs = new Array( );
	var clrs = new Array( );
	for( i = 0; i < 8; ++ i )
	{
		figs[i] = new Array( );
		clrs[i] = new Array( );
		
		for( j = 0; j < 8; ++ j )
			figs[i][j] = -1;
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
			dw( '<td width=35 height=35><div style="position: absolute; top: ' + ( 35 + parseInt( ( 7 - i ) * 34.8 ) ) + '; left: ' + ( 35 + j * 35 ) + ';" id=f' + i + '' + j + ' name=f' + i + '' + j + '><img width=36 height=36 src=empty.gif></td>' );
		}
		dw( '<td>&nbsp;</td></tr>' );
	}

	dw( '<tr><td width=35><img height=41 width=35 src=images/empty.gif></td>' );
	for( i = 0; i < 8; ++ i )
		dw( '<td width=36 align=center>&nbsp;</td>' );
	dw( '<td>&nbsp;</td></tr>' );
</script>

</table>
<table width=320><tr><td align=center><a href='javascript: next_turn( );'>Следующий Ход</a></td></tr></table>

<script>

	cur_turn = 0

	function next_turn( )
	{
		ref.location.href = "chess_log_next.php?id=<? print $id; ?>&turn=" + cur_turn;
	}

	function placefig( y, x, id, clr )
	{
		var nm = '' + id;
		if( clr == 1 ) nm += 'b';
		ge( 'f' + y + '' + x ).innerHTML = '<table cellspacing=0 cellpadding=0 border=0 width=32 height=32 background=' + img_dir + 'fig' + nm + '.png><tr><td>&nbsp;</td></tr></table>';
		
		figs[y][x] = id;
		clrs[y][x] = clr;
	}
	function removefig( y, x )
	{
		ge( 'f' + y + '' + x ).innerHTML = '<img width=32 height=32 src=empty.gif>';
		
		figs[y][x] = -1;
	}
	function show_turn( y, x, ny, nx, id )
	{
		clr = clrs[y][x];
		removefig( y, x );
		removefig( ny, nx );
		if( nx != x || ny != y )
			placefig( ny, nx, id, clr );
	}

<?

$game = new game( -1 );

$game->describe( );

?>
</script>

<iframe id=ref name=ref width=0 height=0></iframe>
