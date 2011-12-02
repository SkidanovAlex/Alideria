<?

if( !$mid_php ) die( );

include_js( "js/timer.js" );


?>

<div id=field style='position:relative;left:0px;top:0px;'>

<table style='width:500px;height:220px;' cellspacing=0 cellpadding=0 border=0 bgcolor=navy>
<tr><td>
&nbsp;
</td></tr></table>

<div id=boat0 style='position:absolute;left:480px;top:-10px;'><img src=images/misc/red_boat.gif width=30 height=37></div>
<div id=boat1 style='position:absolute;left:480px;top:20px;'><img src=images/misc/blue_boat.gif width=30 height=37></div>
<div id=boat2 style='position:absolute;left:480px;top:50px;'><img src=images/misc/green_boat.gif width=30 height=37></div>
            
</div>

Ваш кораблик <b><font color=darkred>красный</font></b>

<script>

<?

$val = $player->GetQuestValue( 19 ) - 1;
if( $val == -1 )
{
	$val = mt_rand( 0, 2 );
	$player->SetQuestValue( 19, $val + 1 );
}

$pts = Array( );
$pts[0] = Array( );
$pts[1] = Array( );
$pts[2] = Array( );

for( $i = 0; $i < 3; ++ $i ) $pts[$i][0] = 0;

$who_won = false;

if ( $val == 0 ) $str = "<font color=darkred>Красный</font>";
if ( $val == 1 ) $str = "<font color=blue>Синий</font>";
if ( $val == 2 ) $str = "<font color=darkgreen>Зеленый</font>";
$str .= " кораблик первый приплыл к финишу.<br>";
if( $val == 0 ) $str .= "<a href=game.php?phrase=407>Дальше</a>";
else $str .= "<a href=game.php?phrase=408>Дальше</a>";

for( $step = 1; $step <= 100; ++ $step )
{
	$ok = true;
	for( $j = 0; $j < 3; ++ $j )
	{
		$pts[$j][$step] = $pts[$j][$step - 1] + mt_rand( 5, 15 );
		if( $pts[$j][$step] >= 500 ) 
		{
			if( $ok ) 
			{
				$pts[$j][$step] = 500;
				if( $who_won === false )
					$who_won = $j;
			}
			else $pts[$j][$step] = 499;
			$ok = false;
		}
	}
}

$id = 0;
echo "var pts = [";
for( $q = 0; $q < 3; ++ $q )
{
	if( $q ) echo ",";
	if( $q == $val ) $i = $who_won;
	else
	{
		if( $id == $who_won ) ++ $id;
		$i = $id;
        ++ $id;
	}
	echo "[";
	for( $j = 0; $j <= 100; ++ $j )
	{
		if( $j ) echo ",";
		echo $pts[$i][$j];
	}
	echo "]";
}
echo '];';

?>

var cur_step = 0;
var cur_offset = 0;

function process( )
{
	cur_offset += 2;
	if( cur_offset == 10 )
	{
		++ cur_step;
		cur_offset = 0;
	}

	for( var i = 0; i < 3; ++ i )
	{
		x = pts[i][cur_step] + ( pts[i][cur_step + 1] - pts[i][cur_step] ) * cur_offset / 10;
		y = x / 4;
		x = parseInt( 480 - x );
		y = parseInt( -10 + 30 * i + y );
		document.getElementById( 'boat' + i ).style.left = x;
		document.getElementById( 'boat' + i ).style.top = y;

		if( x == -20 && document.getElementById( "start_timer" ).style.display == 'none' )
		{
			document.getElementById( "start_timer" ).innerHTML = "<?=$str ?>";
			document.getElementById( "start_timer" ).style.display='';
		}
	}
}

function doStart( )
{
	document.getElementById( "start_timer" ).style.display='none';
	it = setInterval( 'process()', 100 );
}

document.write( "<div id=start_timer>" );
document.write( InsertTimer( 10, "До старта осталось: <b>", "</b>", 0, "doStart()" ) );
document.write( "</div>" );

</script>
