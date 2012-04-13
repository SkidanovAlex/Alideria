<?

if( !$mid_php )
	die( );

$place = $player->depth;
$loc = $player->location;

$x = $depth / 100;
$y = $depth % 100;
settype( $x, 'integer' );

print( "<script src=js/timer.js></script>" );
print( "<table width=100%><colgroup><col width=140><col width=*><col width=200><tbody><tr><td valign=top>");

ScrollLightTableStart();
echo "<img src=images/empty.gif id=loc_img width=170 height=127>";
ScrollLightTableEnd();

print( "<br><div align=justify id=fplr>&nbsp;</div>" );

print( "</td><td valign=top>" );

print( "<script src=js/ajax.js></script>\n" );
print( "<script>\n" );

print( "var title = '{$loc_names[$loc]}';\n" );


print( "</script>\n" );

$rx = ( $x + 50 ) % 100;
print( "<center><div id=forest_top>&nbsp;</div><hr width=40% color=gray size=1></center>" );

print( "<div align=justify id=forest_mid><i>Подождите, идет загрузка</i>" );

print( "</div>" );

print( "</td><td valign=top>" );

	ScrollLightTableStart();
	
	echo "<table width=189 height=189 background='images/rose.jpg'>";

	echo "<script>\n";
	echo "var dx = new Array( -1, 0, 1, -1, 1, -1, 0, 1 );\n";
	echo "var dy = new Array( -1, -1, -1, 0, 0, 1, 1, 1 );\n";
	echo "var dz = new Array( 'NW', 'N', 'NE', 'W', 'E', 'SW', 'S', 'SE' )";
	echo "</script>\n";

	$moo = 0;
	for( $jj = $y - 1; $jj <= $y + 1; ++ $jj )
	{
		echo "\n<tr height=63>";
		for( $ii = $x - 1; $ii <= $x + 1; ++ $ii )
		{
			$align = "center"; $valign = "middle";
			if( $jj == $y - 1 ) $valign = "top"; else if( $jj == $y + 1 ) $valign = "bottom";
			if( $ii == $x - 1 ) $align = "left"; else if( $ii == $x + 1 ) $align = "right";
			echo "<td width=63 height=63 align=$align valign=$valign>";
			$ok = false;
			if( $ii == $x && $jj == $y )
			{
				;
			}
			else
			{
				$nx = ( $ii + 100 ) % 100;
				$ny = ( $jj + 100 ) % 100;
				$nv = $nx * 100 + $ny;

				$dx = $ii - $x;
				$dy = $jj - $y;
				
				print( "<div id=forest_$moo>&nbsp;" );
				print( "</div>" );

				++ $moo;
			}
			echo "</td>";
		}
		echo "</tr>\n";
	}
		
	echo "</table>";

	ScrollLightTableEnd();

	print( "<br><div align=justify id=forest_act>&nbsp;</div>" );

print( "</td></tr></table>" );

print( "<div align=justify id=forest_tmr style='display:none;'>" );

	print( "<center><hr>" );
	print( "<script>document.write( InsertTimer( 120, '<b>', '</b>', 0, 'forest_go( 0, 0 );' ) );</script></center>" );

print( "</div>" );

$status = $arr[status];

?>

<script>

function forest_coord( x, y )
{
	document.getElementById( 'forest_top' ).innerHTML = '<b>' + title + ' [' + x + ', ' + y + ']</b>';
}

function forest_text( str )
{
	document.getElementById( 'forest_mid' ).innerHTML = str;
}

function forest_actions( str )
{
	document.getElementById( 'forest_act' ).innerHTML = str;
}

function forest_timer( str, val )
{
	if( val < 0 )
		document.getElementById( 'forest_tmr' ).style.display = 'none';
	else
	{
		show_timer_title = true;
		tm = ( new Date( ) ).getTime( );
		oink = val;
		b_oink = str;
		PingTimer( );

		document.getElementById( 'forest_tmr' ).style.display = '';
	}
}

function forest_dirs( a0, a1, a2, a3, a4, a5, a6, a7 )
{
	for( i = 0; i < 8; ++ i )
	{
		st = '<b><big>' + dz[i] + '</big></b>';
		vis = eval( 'a' + i );
		if( !vis ) document.getElementById( 'forest_' + i ).innerHTML = '<font color=silver>' + st + '</font>';
		else document.getElementById( 'forest_' + i ).innerHTML = '<a style="cursor:pointer" onclick="forest_go( ' + dx[i] + ', ' + dy[i] + ' )">' + st + '</a>';
	}
}

function forest_go( vx, vy )
{
	query( "forest_ref.php", vx + "|" + vy );
}

function forest_answer( )
{
	answer = document.getElementById( 'answr' ).value;
	query( "forest_ref.php", '>' + answer );
}

function forest_feather( id )
{
	query( "forest_ref.php", '>' + id );
}

function forest_attack( id )
{
	query( "forest_ref.php", '!' + id );
}

var plrs;
var plrids;
var cids;

function forest_clear_players( )
{
	plrs = new Array( );
	plrids = new Array( );
	cids = new Array( );
}

function forest_add_player( nick, can_attack, in_combat, id, combat_id )
{
	st = '<a title="Нельзя напасть"><img src="images/a_silver.gif" border=0></a>';
	if( can_attack ) st = '<a title="Напасть" style="cursor: pointer" onclick="forest_attack( ' + id + ' )"><img src="images/a_green.gif" border=0 width=11 height=11></a>';
	if( in_combat ) st = '<a title="Напасть" style="cursor: pointer" onclick="forest_attack( ' + id + ' )"><img src="images/a_red.gif" border=0 width=11 height=11></a>';
	st += '&nbsp;';
	st += nick;
	if( !cids[combat_id] )
		cids[combat_id] = new Array( );
	cids[combat_id].push( plrs.length );
	plrs[plrs.length] = st;
	plrids[plrids.length] = id;
}

function pref( )
{
	query( 'forest_ref.php','' );
}

function forest_show_players( )
{
   	st = '';
	for( c in cids ) if( c == 0 || cids[c].length > 1 )
	{
    	for( i in cids[c] ) st += plrs[cids[c][i]] + '<br>';
    	st += '<br>';
    }

	if( st != '' ) st = '<b>Игроки здесь:</b> (<a href="javascript:pref()">Обновить</a>)<br>' + st;
	document.getElementById( 'fplr' ).innerHTML = st;
}

function forest_show_npc(str)
{
	document.getElementById( 'here_you_can' ).innerHTML = str;
	return 0;
}

forest_go( 0, 0 );

</script>
