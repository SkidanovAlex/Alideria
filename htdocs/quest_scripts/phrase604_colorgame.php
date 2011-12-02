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
	echo "<b>����������� ��������: </b>";
	if( !$player->sex ) echo "���-���-���, ����� �������. ��� ���� ����� �������, ����� ����� ���� ���� ��������. ��������� ��������� ����������� ���� � ������� � ���� ���� ���� ��������-���� � ��������-��������.";
	else echo "���-���-���, ����� ������. ��� ���� ����� �������, ����� ����� ���� ���� ��������. ��������� ��������� ����������� ���� � ���������� �������� �����, ������ ���� �������, ��������, ���������� �������. � ��� ��� �� �������� $wins ������, �� �� ��������� <b>$wins</b> ���� ��������-���� � ��������-��������.";
	echo "<br><br>";
	
	echo "<li> <a href=game.php?phrase=1251>������� �������!</a><br><br>";
	
	return;
}

echo "������� ���� ������ - ��������� �� ������ �����, ����� ���� ���������� ����� �� 4 �������, ���� ������ �� 4 ������ �������, ������ ����� �����. �������� ��������: ���� ������ ������� <i>������ ����� �����</i>, � �� ����� ���� ������� �� �����!<br><br>";
echo "<div id=drawbox>&nbsp;</div>";

?>

<script>

var clrs = ['red', 'yellow', 'lime', 'white', 'pink', '#4040FF'];
var names = ['�������', '������', '�������', '�����', '�������', '�����'];
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
		alert( '���������� �����!' );
		last_win = 1;
		++ wins;
	}
	else if( id == nmc ) alert( '�������! ���� ������������, �� ������ ����, ������� ��� ������� �� �������, � ���� ������� ����, ������� �������� �������.' );
	else alert( '�� �� ������ ����. �������� ��� ���!' );
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
			alert( '�� �� ������ �������� �� 3 �������!' );
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
	st += "<a href='javascript:start()'><font color=white>�����!</font></a>";
	st += '</td></tr></table>';
	_( 'drawbox' ).innerHTML = st;
}

prep( );

</script>
