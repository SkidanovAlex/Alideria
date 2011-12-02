<?

include( "../skin.php" );

?>

var arena_status = 0;

function arr()
{
	arena_ref.location.reload( );
}

function ar( a )
{
	document.getElementById( 'arena_body' ).innerHTML = "Подождите несколько секунд.<br>Если страница слишком долго не загружается, нажмите <a onClick='arr()' href='javascript:void(0)'>сюда</a>";
	arena_ref.location.href = 'arena_ref.php?a=' + a;
}

function dstart()
{
	document.write( "<table border=1 bordercolor=gray>" );
}

function dbet( id, a, b )
{
	sss = "<? echo AddSlashes( GetScrollTableStart( ) ); ?>";
	eee = "<? echo AddSlashes( GetScrollTableEnd( ) ); ?>";
	a = '<b>' + a + '</b>';
	if( b == '' && arena_status == 0 ) b = '<a target=arena_ref href=arena_ref.php?a=0&d=1&c=' + id + '>Принять заявку</a>';
	else b = '<b>' + b + '</b>';
	document.write( "<tr><td height=25 align=center width=200>" + sss + a + eee + "</td><td height=25 align=center width=200>" + sss + b + eee + "</td>" );
}

function dfin()
{
	document.write( "</table>" );
}

function mstart( a )
{
	a = "<b>" + a + "</b>";
	document.write( "<table border=1 bordercolor=gray>" );
	document.write( "<colgroup><col width=200><col width=200><tbody>" );
	document.write( "<tr><td colspan=2 align=center>Автор: " + a + "</td></tr>" );
	document.write( "<tr><td valign=top align=center>" );
}

function mbet( a )
{
	document.write( "<b>" + a + "</b><br>" );
}

function mmid( id )
{
	if( arena_status == 0 ) document.write( '<a target=arena_ref href=arena_ref.php?a=1&d=0&c=' + id + '>Принять заявку</a><br>' );
	document.write( "</td><td vAlign=top align=center>" );
}

function mfin( id )
{
	if( arena_status == 0 ) document.write( '<a target=arena_ref href=arena_ref.php?a=1&d=1&c=' + id + '>Принять заявку</a><br>' );
	document.write( "</td></tr></table><br><br>" );
}

var cn = 0;
function cstart( a )
{
	a = "<b>" + a + "</b>";
	document.write( "<table border=1 bordercolor=gray>" );
	document.write( "<colgroup><col width=200><tbody>" );
	document.write( "<tr><td align=center>Автор: " + a + "</td></tr>" );
	document.write( "<tr><td valign=top align=center>" );
	cn = 0;
}

function cbet( a )
{
	document.write( "<b>" + a + "</b><br>" );
	++ cn;
}

function cfin( id )
{
	if( arena_status == 0 ) document.write( '<a target=arena_ref href=arena_ref.php?a=2&d=0&c=' + id + '>Принять заявку</a><br>' );
	document.write( "Всего: <u>" + cn + "</u></td></tr></table><br><br>" );
}
