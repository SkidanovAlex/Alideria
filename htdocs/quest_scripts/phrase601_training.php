<?

if( !$mid_php ) die( );

echo "������� ���� ������ - ��������� �� ������ �����, ����� ���� ���������� ����� �� 4 �������, ���� ������ �� 4 ������ �������, ������ ����� �����. �������� ��������: ���� ������ ������� <i>������ ����� �����</i>, � �� ����� ���� ������� �� �����!<br><br>";
echo "<li> <a href=game.php?phrase=1249>�� ���, � ���������� ��������������, ����� �������� � ��������� ���������!</a><br><br>";
echo "<div id=drawbox>&nbsp;</div>";

?>

<script>

var clrs = ['red', 'yellow', 'lime', 'white', 'pink', '#4040FF'];
var names = ['�������', '������', '�������', '�����', '�������', '�����'];
var clr;
var nmc;
var tmo = 0;
var sec = 4;

function check( id )
{
	if( id == clr ) alert( '���������� �����!' );
	else if( id == nmc ) alert( '�������! ���� ������������, �� ������ ����, ������� ��� ������� �� �������, � ���� ������� ����, ������� �������� �������.' );
	else alert( '�� �� ������ ����. �������� ��� ���!' );
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
			alert( '�� �� ������ �������� �� 4 �������!' );
			prep( );
		}
		else _( 'tmr' ).innerHTML = '<big><b><font color=white>' + sec + '</font></b></big>';
	}, 1000 );	
}

function prep( )
{
	var st = '<table cellspacing=0 cellpadding=0 style="background-color:black; width:300px; height:200px;"><tr><td width=100% height=100% align=center valign=middle>';
	st += "<a href='javascript:start()'><font color=white>�����!</font></a>";
	st += '</td></tr></table>';
	_( 'drawbox' ).innerHTML = st;
}

prep( );

</script>
