<?

if( !$mid_php ) die( );

echo "<div id = 'AllDiv' style='width:691px; height:334px; position:absolute; background-image:url(\"images/nevesom/astral_bg.jpg\");'>";
/*
echo "<div style=\"width:226px; height:132px; position:absolute; left:22px; top:37px; background-image:url('images/nevesom/big_text_bg.png'); padding:10px;background-repeat:no-repeat;font-size:10px;\">";
echo "Мир иллюзий. Созданный сильнейшими магами, этот мир создает поле клонов,  которые сводят с ума и обескураживают свою жертву. В таком поле легко потерять  бдительность и угодить в западню собственного разума, не сумев отличить самого себя. Это задачка для настоящего мага, только самый достойный сможет постигнуть решение...";
echo "<a href='#' onclick='query( \"quest_scripts/phrase280_ajax.php\", \"-1\" );'>Обновить мир</a>";
echo "</div>";
*/

$text = "{$player->login}, это Мир Астрала. Здесь действуют свои законы и правила. Придерживайся их и ты сможешь выполнить свою миссию. Как ты видишь, посреди пути злодей, но облик его скрыт под маской перерождения. Оборотень… Тебе нужно догнать его. Вы будете ходить по очереди по зыбким астральным плитам. Когда будешь ".(($player->sex)?'готова':'готов').", то просто нажми «Ходить» и ты сделаешь ход. Максимум на 3 хода, минимум на 1. Плиты бывают четырех градаций и таят в себе разнообразные уловки. Я время от времени буду помогать тебе. Да прибудет с тобой вера!";
echo "<div style=\"width:107px; height:89px; z-index: 1; position:absolute; left:12px; top:17px; background-image:url('images/astral/dragon_bg.png'); padding:15px 10px 10px 91px;background-repeat:no-repeat;font-size:10px;\">";
echo "<div id='DragonDiv' style='width:93px; height: 80px; overflow-x:hidden; overflow-y:auto'>$text</div>";
echo "</div>";

echo "<div style=\"width:688px; height:343px; padding:0px; position:absolute; left:12px; top:17px;\">
 <table border=0 cellspacing=0 cellpadding=0 >";

$field = array(
	array( 0, 0, 0, 8, 4, 1, 4 ,2, 3, 1 ),
	array( 0, 0, 0, 0, 0, 0, 0 ,0, 0, 4 ),
	array( 1, 2, 4, 1, 3, 2, 3 ,1, 2, 2 ),
	array( 3, 0, 0, 0, 0, 0, 0 ,0, 0, 0 ),
	array( 2, 2, 1, 4, 2, 3, 4 ,2, 1, 9 )
);

$id = 0;
$add = "";
for( $i = 0; $i < 5; ++ $i )
{
	echo "<tr>";
	for( $j = 0; $j < 10; ++ $j )
	{
		switch ( $field[$i][$j] )
		{
			case 1:
				$bg = 'background-image:url("images/astral/cell_1.png");';
				break;
			case 2:
				$bg = 'background-image:url("images/astral/cell_2.png");';
				break;
			case 3:
				$bg = 'background-image:url("images/astral/cell_3.png");';
				break;
			case 4:
				$bg = 'background-image:url("images/astral/cell_4.png");';
				break;
			case 8:
				$bg = 'background-image:url("images/astral/cell_no.png");';
				$y = $i * 59 + ( -20 );
				$x = $j * 66 + ( 0 );
				$add .= "<div style='position:absolute; width:61px; height:60px; top:{$y}; left:{$x}; z-index:1; background-image:url(\"images/astral/in.png\");'><img width=0></div>";
				break;
			case 9:
				$bg = 'background-image:url("images/astral/cell_no.png");';
				$y = $i * 59 + ( -20 );
				$x = $j * 66 + ( 3 );
				$add .= "<div style='position:absolute; width:61px; height:60px; top:{$y}; left:{$x}; z-index:1; background-image:url(\"images/astral/out.png\");'><img width=0></div>";
				break;
			default:
				$bg = '';
		}
		$onclick = "";
		echo "<td $onclick align=center valign=middle id='td$i$j' style='width:66px;height:59px;$bg'>";
		echo "<img width=0></td>";
		++ $id;
	}
	echo "</tr>";
}

echo "</table>$add";
echo "</div>";

echo "<div style='width:100px; height:334px; left:700px; position:absolute;'>";

echo "<div style=\"width:68px; height:240px; padding:0px; position:absolute; left:2px; top:1px;\">
 <table border=0 cellspacing=0 cellpadding=0 >";

for( $i = 0; $i < 4; ++ $i )
{
	$add = "<img width=0>";
	echo "<tr>";
	$i1 = $i + 1;
	$bg = "background-image:url(\"images/astral/cell_{$i1}.png\");";
	switch ( $i )
	{
		case 0:
			$hint = 'Удача. Это самые приятные плиты. Здесь сама Фортуна оставила свой след. А по её стопам безопасно и приятно передвигаться. Тут можно встретить только положительные или нейтральные события.';
			break;
		case 1:
			$hint = 'Неудача. А вот здесь прошла сестра-злодейка Фортуны, оставив следы своей зависти, злости и желчи. На этих плитах не стоит задерживаться. Они могут отбросить назад, покалечить или перебросить Вас в другой мир.';
			break;
		case 2:
			$hint = 'Прыжок. Пожалуй, это что-то сродни плит удачи. Астральный мир вобрал частицу Астаниэль и любой, кто станет на эту плиту, будет двигаться быстрее.';
			break;
		case 3:
			$hint = 'Капкан. Смесь разрушительной силы Астрала и вечной силы природы. Ка-Напис своим умением оплетать и сковывать дал толчок к появлению этих ужасных плит. Если кто станет на неё, то следующий ход там и простоит.';
			break;
	}
	echo "<td align=center valign=middle style='width:66px;height:59px;$bg' title='$hint'>";
	echo "$add</td></tr>";
}

echo "</table>";
echo "</div>";

$move_url = "<font size='+1'><b><a href='#' onclick='moo( 1 );'>Ходить</a></b></font>";

echo "<div id='MoveDiv' style=\"width:100px; height:90px; padding:0px; position:absolute; left:2px; top:247px;\">";
echo "$move_url";
echo "</div>";

echo "</div>";

echo "</div>";

?>

<script>

for ( var ii = 0; ii < 4; ++ ii )
	( new Image( ) ).src = 'images/nevesom/smile_' + ii + '.gif';
for ( var ii = 0; ii < 4; ++ ii )
	( new Image( ) ).src = 'images/astral/obor_' + ii + '.gif';


var tmp = '.';
var dot_id = tmp.charCodeAt( 0 );
tmp = '0';
var zero_id = tmp.charCodeAt( 0 );

function charNum( c )
{
	if ( c == dot_id )
		return -1;
	return c - zero_id;
}

function moo( cell )
{
	query( 'quest_scripts/phrase278_ajax.php', '' + cell );
}

function moove( y, x, dir, cnt )
{
    var dirx = new Array( 1, 0, -1, 0 );
    var diry = new Array( 0, -1, 0, 1 );
    var ncnt = cnt - 1;
    var ny = y + diry[dir];
    var nx = x + dirx[dir];
    _('sml').style.left = Math.floor( x * 6.6 ) + "px";
    _('sml').style.top  = Math.floor( y * 5.9 ) + "px";
	if ( ncnt > 0 )
		setTimeout( "moove( " + ny + ", " + nx + ", " + dir + ", " + ncnt + " );", 60 );
}

function move( f, hint, y, x, dir, cnt, img_type )
{
	out( f, hint );
	var img_name = '';
	if ( img_type == 0 )
		img_name = 'images/nevesom/smile_' + dir + '.gif';
	else
		img_name = 'images/astral/obor_' + dir + '.gif';
	_( 'td' + y + '' + x ).innerHTML = '<div id="sml" style="position:relative;z-index:3"><img src="' + img_name + '"></div>';
    var dirx = new Array( 1, 0, -1, 0 );
    var diry = new Array( 0, -1, 0, 1 );
	moove( 0, 0, dir, cnt * 10 + 1 );
}

function activeMove( doActive )
{
	if ( doActive == true )
	{
		_( 'MoveDiv' ).innerHTML = "<? echo $move_url; ?>";
	}
	else
	{
		_( 'MoveDiv' ).innerHTML = '';
	}
}

function out( s, hint )
{
//	alert( s );
	var i, j;
	for( i = 0; i < 5; ++ i )
	{
		for( j = 0; j < 10; ++ j )
		{
			_( 'td' + i + '' + j ).innerHTML = '<img width=0>';
		}
	}
	var playery = charNum( s.charCodeAt( 0 ) );
	var	playerx = charNum( s.charCodeAt( 1 ) );
	if ( playerx >= 0 )
	{
		_( 'td' + playery + '' + playerx ).innerHTML = "<div style='position:relative; z-index:10;'><img src='images/nevesom/smile_3.gif'></div>";
	}
	var obory = charNum( s.charCodeAt( 2 ) );
	var	oborx = charNum( s.charCodeAt( 3 ) );
	if ( oborx >= 0 )
	{
		_( 'td' + obory + '' + oborx ).innerHTML = "<div style='position:relative; z-index:10;'><img src='images/astral/obor_3.gif'></div>";
	}

	if ( hint != '' )
		_( 'DragonDiv' ).innerHTML = hint;
}

<?
/*
$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( $arr )
{
	echo "out( '$arr[f]' )";
}
else
{
	echo "query( 'quest_scripts/phrase280_ajax.php', '' + ( 9 * 3 + 4 ) );";
}
*/
?>

activeMove( false );
moo( 0 );
</script>
