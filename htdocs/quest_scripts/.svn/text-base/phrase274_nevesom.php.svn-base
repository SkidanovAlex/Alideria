<?

if( !$mid_php ) die( );

echo "<div style='width:691px; height:334px; position:absolute; background-image:url(\"images/nevesom/astral_bg.jpg\");'>";

echo "<div style=\"width:226px; height:132px; position:absolute; left:22px; top:37px; background-image:url('images/nevesom/big_text_bg.png'); padding:10px;background-repeat:no-repeat;font-size:10px;\">";
echo "ћир невесомости. «десь действуют свои законы, отличные от законов параллельного мира. ¬еликие маги сходили с ума, пыта€сь выбратьс€ из этой коварной западни, уготованной им силами всемогущей воды. √лупцы остаютс€ здесь навеки, умные наход€т лишь развлечение...  то ты? ѕознай себ€, взгл€ни в свою душу. ѕоверь, сейчас самое врем€...";
echo "<a href='#' onclick='query( \"quest_scripts/phrase274_ajax.php\", \"-1\" );'>ќбновить мир</a>";
echo "</div>";

echo "<div style=\"width:145px; height:89px; position:absolute; left:22px; top:200px; background-image:url('images/nevesom/dragon_text_bg.png'); padding:15px 10px 10px 91px;background-repeat:no-repeat;font-size:10px;\">";
echo "„тобы выбратьс€ из мира невесомости, тебе нужно угодить в вихрь. ƒл€ этого нужно отталкиватьс€ от  границ мира и толкать тучи.";
echo "</div>";

echo "<div style=\"width:303px; height:260px; padding:5px 0px 0px 8px; position:absolute; left:325px; top:37px; background-image:url('images/nevesom/bg.png'); background-repeat:no-repeat;\">
 <table border=0 cellspacing=0 cellpadding=0 >";

$id = 0;
for( $i = 0; $i < 6; ++ $i )
{
	echo "<tr>";
	for( $j = 0; $j < 7; ++ $j )
	{
		echo "<td onclick='query( \"quest_scripts/phrase274_ajax.php\", \"$id\" )' align=center valign=middle id='td$i$j' style='width:40px;height:40px;cursor:pointer;'>";
		echo "&nbsp;</td>";
		++ $id;
	}
	echo "</tr>";
}

echo "</table>";

echo "</div></div>";

/*
  ABCDEFIJXY
  A-y тучки 1
  B-x тучки 1
  CDEF - тоже
  IJ - позици€ игрока
  XY - выбранна€ клетка, '..' иначе
  потом идут нарисованные стрелки
  ABCDEFGH
  AB - координаты угла 0 и тд
*/
?>

<script>

for ( var ii = 0; ii < 4; ++ ii )
	( new Image( ) ).src = 'images/nevesom/smile_' + ii + '.gif';

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
	query( 'quest_scripts/phrase274_ajax.php', cell );
}

function moove( y, x, dir, cnt )
{
	var dirx = new Array( 1, 0, -1, 0 );
	var diry = new Array( 0, -1, 0, 1 );
    var ncnt = cnt - 1;
    var ny = y + diry[dir];
    var nx = x + dirx[dir];
    _('sml').style.left = ( x * 10 ) + "px";
    _('sml').style.top  = ( y * 10 ) + "px";
	if ( ncnt > 0 )
		setTimeout( "moove( " + ny + ", " + nx + ", " + dir + ", " + ncnt + " );", 75 );
}

function move( f, y, x, dir, cnt, np )
{
    var man = false;
	if ( y == charNum( f.charCodeAt( 6 ) ) && x == charNum( f.charCodeAt( 7 ) ) ) //man
		man = true;
	out( f );
	if ( man )
		_( 'td' + y + '' + x ).innerHTML = '<div id=\'sml\' style="position:relative;"><img src=\'images/nevesom/smile_' + dir + '.gif\'></div>';
    else
		_( 'td' + y + '' + x ).innerHTML = '<div id=\'sml\' style="position:relative;"><img src=\'images/nevesom/cloud.png\'></div>';
	moove( 0, 0, dir, cnt * 4 + 1 );
	setTimeout( "moo( '" + np + "' );", 300 * cnt );
}

function out( s )
{
	var id = 0, i, j;
	for( i = 0; i < 6; ++ i )
	{
		for( j = 0; j < 7; ++ j )
		{
			_( 'td' + i + '' + j ).innerHTML = '&nbsp';
		}
	}
	var x, y, k;
	for ( k = 0; k < 3; ++ k )
	{
		y = charNum( s.charCodeAt( k * 2 ) );
		x = charNum( s.charCodeAt( k * 2 + 1 ) );
		_( 'td' + y + '' + x ).innerHTML = "<img src='images/nevesom/cloud.png'>";
	}
	y = charNum( s.charCodeAt( 6 ) );
	x = charNum( s.charCodeAt( 7 ) );
	var sely = charNum( s.charCodeAt( 8 ) );
	var	selx = charNum( s.charCodeAt( 9 ) );
	_( 'td' + y + '' + x ).innerHTML = "<img src='images/nevesom/smile_3.gif'>";
	for ( i = 0; i < 4; ++ i ) //arrows
	{
		y = charNum( s.charCodeAt( i * 2 + 10 ) );
		x = charNum( s.charCodeAt( i * 2 + 11 ) );
		if ( x >= 0 && y >= 0 )
			_( 'td' + y + '' + x ).innerHTML = "<img src='images/nevesom/arrow_" + i + ".png'>";
	}
}

<?

$res = f_MQuery( "SELECT * FROM player_mines WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( $arr )
{
	echo "out( '$arr[f]' )";
}
else
{
	echo "query( 'quest_scripts/phrase274_ajax.php', '24' );";
}

?>

</script>
