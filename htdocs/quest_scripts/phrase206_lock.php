<?

if( !$mid_php ) die( );

echo "Перед вами игровое поле, на котором расположено 18 красных и 17 зеленых камней. Вы можете двигать любой камень на свободное место. Ваша задача - расположить все красные камни сверху, все зеленые снизу, а свободной оставить правую нижнюю клетку.<br><br>";

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
		echo "<td onclick='query( \"quest_scripts/phrase206_ajax.php\", \"$id\" )' align=center valign=middle id=td$i$j style='width:36px;height:36px;cursor:pointer;{$border}background-color:#e0c3a0;'>";
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
			if( s.charAt( id ) == '.' ) _( 'td' + i + '' + j ).style.backgroundColor = '#e0c3a0';
			else if( s.charAt( id ) == 'r' ) _( 'td' + i + '' + j ).style.backgroundColor = 'darkred';
			else _( 'td' + i + '' + j ).style.backgroundColor = 'green';
			++ id;
		}
	if( m == 1 ) _( 'txt' ).innerHTML = '<font color=darkred>Вы подвинули последний камень и входная дверь со скрипом начала отворяться.</font><br><a href=game.php?phrase=482>Войти внутрь</a>';
	else
	{
		_( 'txt' ).innerHTML = 'Если вы хотите закончить задание позже, вы можете уйти. При этом замок вернется в исходное состояние.<br><a href=game.php?phrase=483>Уйти</a>';
	}
}

<?

f_MQuery( "LOCK TABLE player_mines WRITE" );

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

if( !$arr )
{
	$st = '';
	for( $i = 0 ; $i < 35; ++ $i )
	{
		if( $i % 6 < 3 ) $st .= 'r';
		else $st .= 'g';
	}
	$st .= '.';

	f_MQuery( "INSERT INTO player_mines( player_id, f ) VALUES ( {$player->player_id}, '$st' )" );
}
f_MQuery( "UNLOCK TABLES" );

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( $arr )
{
	$moo = 0;
	if( $arr['lost'] ) $moo = 1;
	echo "out( '$arr[f]', $moo )";
}

?>

</script>
