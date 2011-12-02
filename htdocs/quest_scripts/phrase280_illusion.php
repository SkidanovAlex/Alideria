<?

if( !$mid_php ) die( );

echo "<div style='width:691px; height:334px; position:absolute; background-image:url(\"images/nevesom/astral_bg.jpg\");'>";

echo "<div style=\"width:226px; height:132px; position:absolute; left:22px; top:37px; background-image:url('images/nevesom/big_text_bg.png'); padding:10px;background-repeat:no-repeat;font-size:10px;\">";
echo "ћир иллюзий. —озданный сильнейшими магами, этот мир создает поле клонов,  которые свод€т с ума и обескураживают свою жертву. ¬ таком поле легко потер€ть  бдительность и угодить в западню собственного разума, не сумев отличить самого себ€. Ёто задачка дл€ насто€щего мага, только самый достойный сможет постигнуть решение...";
echo "<a href='#' onclick='query( \"quest_scripts/phrase280_ajax.php\", \"-1\" );'>ќбновить мир</a>";
echo "</div>";

echo "<div style=\"width:145px; height:89px; position:absolute; left:22px; top:200px; background-image:url('images/nevesom/dragon_text_bg.png'); padding:15px 10px 10px 91px;background-repeat:no-repeat;font-size:10px;\">";
echo "„тобы выбратьс€ из мира иллюзий необходимо уничтожить все свои клоны. „тобы уничтожать их, нужно их  просто напросто перепрыгивать.";
echo "</div>";

echo "<div style=\"width:468px; height:343px; padding:0px; position:absolute; left:270px; top:12px;\">
 <table border=0 cellspacing=0 cellpadding=0 >";

$field = array(
	array( 0, 0, 0, 0, 1, 0, 0 ,0, 0 ),
	array( 0, 0, 0, 1, 1, 1, 0 ,0, 0 ),
	array( 0, 0, 1, 1, 1, 1, 1 ,0, 0 ),
	array( 1, 1, 1, 1, 1, 1, 1 ,1, 1 ),
	array( 0, 0, 1, 1, 1, 1, 1 ,0, 0 ),
	array( 0, 0, 0, 1, 1, 1, 0 ,0, 0 ),
	array( 0, 0, 0, 0, 1, 0, 0 ,0, 0 ) );

$id = 0;
for( $i = 0; $i < 7; ++ $i )
{
	echo "<tr>";
	for( $j = 0; $j < 9; ++ $j )
	{
		if ( $field[$i][$j] )
		{
			$bg = "cursor:pointer;background-image:url(\"images/illusion/cell.png\");";
			$onclick = "onclick='query( \"quest_scripts/phrase280_ajax.php\", \"$id\" )'";
		}
		else
		{
			$bg = "";
			$onclick = "";
		}
		echo "<td $onclick align=center valign=middle id='td$i$j' style='width:45px;height:45px;$bg'>";
		echo "&nbsp;</td>";
		++ $id;
	}
	echo "</tr>";
}

echo "</table>";

echo "</div></div>";

/*
	Every cell coded (includes non-field),
	there're 63 cells, so code with 3 bits => 21 bytes,
	every byte from '0' to '7'
	next 2 bytes is sely, selx
	total 23 bytes
*/
?>

<script>

for ( var ii = 0; ii < 8; ++ ii )
	( new Image( ) ).src = 'images/illusion/smile_' + ii + '.gif';
( new Image( ) ).src = 'images/illusion/smile_disappear.gif';


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
	query( 'quest_scripts/phrase280_ajax.php', cell );
}

function moove( y, x, dir, cnt )
{
    var dirx = new Array( 1, 1, 0, -1, -1, -1, 0, 1 );
    var diry = new Array( 0, -1, -1, -1, 0, 1, 1, 1 );
    var ncnt = cnt - 1;
    var ny = y + diry[dir];
    var nx = x + dirx[dir];
    _('sml').style.left = ( x * 9 ) + "px";
    _('sml').style.top  = ( y * 9 ) + "px";
	if ( ncnt > 0 )
		setTimeout( "moove( " + ny + ", " + nx + ", " + dir + ", " + ncnt + " );", 60 );
}

function move( f, y, x, dir, cnt, np )
{
//	alert( f );
	out( f );
	_( 'td' + y + '' + x ).innerHTML = '<div id=\'sml\' style="position:relative;z-index:3"><img src=\'images/illusion/smile_' + dir + '.gif\'></div>';
    var dirx = new Array( 1, 1, 0, -1, -1, -1, 0, 1 );
    var diry = new Array( 0, -1, -1, -1, 0, 1, 1, 1 );
    var ny = y + diry[dir];
    var nx = x + dirx[dir];
    _( 'td' + ny + '' + nx ).innerHTML = "<img src='images/illusion/smile_disappear.gif'>";
    ny = y + 2 * diry[dir];
    nx = x + 2 * dirx[dir];
    _( 'td' + ny + '' + nx ).innerHTML = "&nbsp";
	moove( 0, 0, dir, cnt * 5 + 1 );
	setTimeout( "moo( '" + np + "' );", 300 * cnt );
}

function infield( y, x )
{
	if ( y < 0 || x < 0 || y >= 7 || x >= 9 )
		return false;
	if ( y == 3 )
		return true;
	return Math.abs( x - 4 ) <= 3 - Math.abs( y - 3 );
}

function out( s )
{
	var id = 0, i, j;
	for( i = 0; i < 7; ++ i )
	{
		for( j = 0; j < 9; ++ j )
		{
			_( 'td' + i + '' + j ).innerHTML = '&nbsp';
		}
	}
	var x, y, k, kk;
	var field = new Array( 7 );
	for ( k = 0; k < 7; ++ k )
	{
	    field[k] = new Array( 9 );
	    for ( kk = 0; kk < 9; ++ kk )
	        field[k][kk] = 0;
	}
	for ( k = 0; k < 21; ++ k )
	{
		id = charNum( s.charCodeAt( k ) );
        y = Math.floor( k / 3 );
		x = ( k % 3 ) * 3;
        for ( kk = 0; kk < 3; ++ kk )
        {
        	if ( ( 1 << kk ) & id )
		    {
		    	field[y][x + kk] = 1;
//		    	_( 'td' + y + '' + ( x + kk ) ).innerHTML = "<img src='images/illusion/smile_no_anim.gif'>";
		    }
	    }
	}
	for ( i = 0; i < 7; ++ i )
		for ( j = 0; j < 9; ++ j )
           if ( field[i][j] )
           {
		    	_( 'td' + i + '' + j ).innerHTML = "<img src='images/illusion/smile_no_anim.gif'>";
           }
	var sely = charNum( s.charCodeAt( 21 ) );
	var	selx = charNum( s.charCodeAt( 22 ) );
	if ( sely >= 0 && selx >= 0 )
	{
		_( 'td' + sely + '' + selx ).innerHTML = "<img src='images/illusion/smile_6.gif'>";
	    var dirx = new Array( 1, 1, 0, -1, -1, -1, 0, 1 );
	    var diry = new Array( 0, -1, -1, -1, 0, 1, 1, 1 );
        var x1, y1, x2, y2;
	    for ( i = 0; i < 8; ++ i ) //arrows
	    {
	        y1 = sely + diry[i];
	        x1 = selx + dirx[i];
	        y2 = sely + 2 * diry[i];
	        x2 = selx + 2 * dirx[i];
	        if ( infield( y1, x1 ) && infield( y2, x2 ) && field[y1][x1] && !field[y2][x2] )
	            _( 'td' + y2 + '' + x2 ).innerHTML = "<img src='images/illusion/cell_high.png'>";
	    }
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
	echo "query( 'quest_scripts/phrase280_ajax.php', '' + ( 9 * 3 + 4 ) );";
}

?>

</script>
