<?

if( !$mid_php ) die( );

echo "<table><tr><td vAlign=top><div id=render_here>";

echo "&nbsp;";

echo "</div><td><td vAlign=top>";

?>

<img width=0 height=0 src=images/misc/tr1.png>
<img width=0 height=0 src=images/misc/tr2.png>
<img width=0 height=0 src=images/misc/tr3.png>
<img width=0 height=0 src=images/misc/tr4.png>

Твоя задача - рассадить ёлки по полянке. Каждая цифра на игровом поле показывает, сколько на этой клетке нужно вырастить ёлок. Всякий раз, когда ты кликаешь по клетке, в ней вырастает одна ёлка, и еще по одной ёлке добавляется на соседних по горизонтали и по вертикали клетках. Однако если в соседних клетках ёлок еще нет (а стоит цифра), то там ёлка не появится. <br>
Первую ёлку в клетке можно посадить только кликнув по полю с цифрой. В одной клетке не может расти больше четырех ёлок, и если ты добавишь еще, в клетке останется только одна ёлка. Если ты рассадил ёлки неправильно - не расстраивайся. Нажав кнопку "Новая посадка", ты начнешь высаживать елки заново.<br>
<br>
<li> <a href='game.php?refur=1'>Новая Посадка</a></li>
<li> <a href='game.php?phrase=1186'>Отчаяться и Уйти</a></li>
<li id=moo_span style='display:none;'> <a href='game.php?phrase=1187'>Обрадовать Деда Мороза</a></li>

</td></tr></table>

<script>

function refr(s)
{
	var id = 0;
	var st = '<table cellspacing=0 cellpadding=0 border=0 style="width:319px; height:320px;" background="images/misc/fur_field.jpg"><tr><td width=319 height=320><div style="position:relative;top:0px;left:0px;width:319px;height:320px;">';
	for( var i = 0; i < 3; ++ i )
		for( var j = 0; j < 3; ++ j )
		{
			var tp = 8 + 101 * i;
			var lf = 9 + 100 * j;
			var v = parseInt( s.charAt( id ) );
			var src = ( ( v < 5 ) ? 'e' + v : 'tr' + (v-4) ) + '.png';
			st += '<img onclick="query(\'quest_scripts/phrase563_ajax.php\',\'' + id + '\')" style="cursor:pointer;position:absolute;top:' + tp + 'px;left:' + lf + 'px" src=images/misc/' + src + ' width=100 height=100>';
			++ id;
		}
	st += '</div></td></tr></table>';
	_( 'render_here' ).innerHTML = st;
}

function do_win( )
{
	_( 'moo_span' ).style.display = '';
}

<?

f_MQuery( "LOCK TABLE player_mines WRITE" );

if( isset( $_GET['refur'] ) ) f_MQuery( "DELETE FROM player_mines WHERE player_id={$player->player_id}" );

$arr = f_MFetch( f_MQuery( "SELECT f, lost FROM player_mines WHERE player_id={$player->player_id}" ) );
if( !$arr )
{
	$f = '.........';
	function moo( $x, $y )
	{
		global $f;
		if( $x < 0 || $y < 0 || $x >= 3 || $y >= 3 ) return;
		$id = $x * 3 + $y;
		if( $f[$id] == '.' ) return;
		else if( $f[$id] == '1' ) $f[$id] = '2';
		else if( $f[$id] == '2' ) $f[$id] = '3';
		else if( $f[$id] == '3' ) $f[$id] = '4';
		else if( $f[$id] == '4' ) $f[$id] = '1';
	}
	function turn( $x, $y )
	{
		global $f;
		$id = $x * 3 + $y;
		if( $f[$id] == '.' ) $f[$id] = '1';
		else moo( $x, $y );
		moo( $x - 1, $y );
		moo( $x + 1, $y );
		moo( $x, $y - 1 );
		moo( $x, $y + 1 );
	}
	for( $i = 0; $i < 100 || substr_count($f, '.') > 0; ++ $i ) turn( mt_rand( 0, 2 ), mt_rand( 0, 2 ) );
	f_MQuery( "INSERT INTO player_mines ( player_id, f ) VALUES ( {$player->player_id}, '$f' )" );
	
	$moo = 0;
}
else { $f = $arr['f']; $moo = $arr['lost']; }

echo "refr('$f');";

if( $moo ) echo "do_win( );";

f_MQuery( "UNLOCK TABLES" );

?>
</script>
