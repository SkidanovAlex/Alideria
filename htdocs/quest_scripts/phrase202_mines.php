<?

if( !$mid_php ) die( );

if( $player->GetQuestValue( 25 ) > time( ) )
{
	echo "����� ���� ����� ���� �������� ������� �������. ������, ������ ������ � ���������� �� �������. ���� ���������, ���� ����� ��������.<br><br><a href=game.php?phrase=476>���������</a>";
	return;
}

echo "����� ���� 36 ����, ��� ������������ ����� ���� ����. ����� � ���� ������ ��� ���� ����, � 29-�� - ����. �� ������ ���������� ������ ������ � ����� �����, ���� �� ������ ������ � ����, � ������� 30 ����� �� �� ������� ������� � ����������. ���� ��� �������, � ������ ������ �� � ����, �� ��� �� ������� ������, ������� ��� ����� � ��� ������, ���� ����� ������.<br><br>";

echo "<table><tr><td>";

echo "<table style='border:1px solid black' cellspacing=0 cellpadding=0>";

$id = 0;
for( $i = 0; $i < 6; ++ $i )
{
	echo "<tr>";
	for( $j = 0; $j < 6; ++ $j )
	{
		$b = "1px solid black";
		$border = "";
		if( $j != 5 ) $border .= "border-right: $b;";
		if( $i != 5 ) $border .= "border-bottom: $b;";
		echo "<td onclick='query( \"quest_scripts/phrase202_ajax.php\", \"$id\" )' align=center valign=middle id=td$i$j style='width:36px;height:36px;cursor:pointer;{$border}background-color:#e0c3a0;'>";
		echo "&nbsp;</td>";
		++ $id;
	}
	echo "</tr>";
}

echo "</table>";

echo "</td><td valign=top><div id=txt>&nbsp;</div></td></tr></table>";

?>

<script>

function out( s, m )
{
	var id = 0;
	for( var i = 0; i < 6; ++ i )
		for( var j = 0; j < 6; ++ j )
		{
			if( s.charAt( id ) == '.' ) ;
			else if( s.charAt( id ) == 'x' ) _( 'td' + i + '' + j ).style.backgroundColor = 'darkred';
			else { _( 'td' + i + '' + j ).innerHTML = s.charAt( id ); _( 'td' + i + '' + j ).style.backgroundColor = 'green' }
			++ id;
		}
	if( m == 1 ) _( 'txt' ).innerHTML = '<font color=darkred>�� ������ ������ ����� � ����. ���� ����� ������� ������� ��������� �� ���� ������.<br>� ������� ��������� �������� ������ � ���������� ����� �� ���������.</font><br><a href=game.php?phrase=476>�����</a>';
	if( m == 2 ) _( 'txt' ).innerHTML = '<font color=darkred>�� ������ ��������� ������ � ����� ������ ������������ ���� ���� ���. ������ ������ � ���������� �� �������� �����.</font><br><a href=game.php?phrase=477>���� ������</a>';
}

<?

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( $arr )
{
	$moo = 0;
	if( $arr['lost'] ) $moo = 1;
	else if( strpos( $arr['f'], '.' ) === false ) $moo = 2;
	if( $moo == 0 ) $arr['f'] = str_replace( 'x', '.', $arr['f'] );
	echo "out( '$arr[f]', $moo )";
}

?>

</script>
