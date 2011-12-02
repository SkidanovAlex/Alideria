<?

if( !$mid_php ) die( );

echo "Перед вами игровое поле, на котором расположено несколько камней. Ваша задача убрать все камни с поля. Вы можете убрать камень или положить его на клетку, где его нет, но в этом случае отшельник положит камни на клетки в одной строке и столбце с вашей, если их там не было, и уберет, если были.<br><br>";

echo "<table><tr><td>";

echo "<table style='border:1px solid black' cellspacing=0 cellpadding=0>";

$id = 0;
for( $i = 0; $i < 4; ++ $i )
{
	echo "<tr>";
	for( $j = 0; $j < 4; ++ $j )
	{
		$b = "1px solid black";
		$border = "";
		if( $j != 3 ) $border .= "border-right: $b;";
		if( $i != 3 ) $border .= "border-bottom: $b;";
		echo "<td onclick='query( \"quest_scripts/phrase194_ajax.php\", \"$id\" )' align=center valign=middle id=td$i$j style='width:48px;height:48px;cursor:pointer;{$border}background-color:#e0c3a0;'>";
		echo "&nbsp;</td>";
		++ $id;
	}
	echo "</tr>";
}

echo "</table>";

echo "</td><td valign=top><div id=txt>&nbsp;</div></td></tr></table>";

?>

<script>

var memo = new Array( );

function out( s, m )
{
	var id = 0;
	var need = false;
	for( var i = 0; i < 4; ++ i )
		for( var j = 0; j < 4; ++ j )
		{
			cur = memo[id];
			if( s.charAt( id ) == '.' ) moo = '#e0c3a0';
			else moo = 'darkred';
			if( cur == 'black' ) { memo[id] = moo; _( 'td' + i + '' + j ).style.backgroundColor = moo; }
			else if( cur != moo )
			{
				memo[id] = 'black'
				_( 'td' + i + '' + j ).style.backgroundColor = 'black';
				need = true;
			}
			++ id;
		}
	if( m == 1 ) _( 'txt' ).innerHTML = '<font color=darkred>Все камни убраны со стола! Вы прошли испытание.</font><br><a href=game.php?phrase=466>Дальше</a>';
	else
	{
		_( 'txt' ).innerHTML = 'Если вы хотите закончить задание позже, вы можете уйти.<br><a href=game.php?phrase=465>Уйти</a>';
	}

	if( need ) setTimeout( 'out( "' + s + '", ' + m + ');', 200 );
}

<?

f_MQuery( "LOCK TABLE player_mines WRITE" );

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

if( !$arr )
{
	$st = '';
	for( $i = 0 ; $i < 16; ++ $i )
	{
		$st .= '.';
	}
	for( $j = 0; $j < 10; ++ $j )
	{
		$id = mt_rand( 0, 15 );
		for( $i = 0 ; $i < 16; ++ $i ) if( $i % 4 == $id % 4 || floor( $i / 4 ) == floor( $id / 4 ) ) $st[$i] = ( $st[$i] == '.' ? 'x' : '.' );
	}

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
