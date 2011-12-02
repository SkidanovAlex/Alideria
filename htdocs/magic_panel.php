<?

require_once( 'magic_functions.php' );
include_js( 'js/timer.js' );

//LogError("TEST");
if( !$mid_php ) die( );

echo "<table><tr><td valign=top><div id=field style='position:relative; top:0px; left:0px;'>";

echo "<img src='images/magic/field.jpg' width=615 height=315 border=0><br><center><a href=help.php?id=45002 target=_blank>Немного об игре</a></center>";

outc( 'name1', 23, 26, 116, 43, 0 , 'left');
outc( 'name2', 400, 28, 595, 45, 0 , 'right');

outc( 'cur_turn', 500, 273, 600, 300, '');
outc( 'dtimeout', 23, 273, 115, 300, '<script>document.write( InsertTimer( 40, "<b><font color=white>", "</font></b>", 0, "hru();" ) );</script>');


outc( 'tree1', 118, 257, 155, 274, 0 );
outc( 'tree2', 460, 259, 497, 276, 0 );
outc( 'ditch1', 118, 277, 155, 294, 0 );
outc( 'ditch2', 460, 279, 497, 296, 0 );

outc( 'dwm1', 29, 60, 60, 105, 0, 'left' );
outc( 'dwp1', 77, 60, 108, 105, 0, 'right' );

outc( 'dnm1', 29, 137, 60, 182, 0, 'left' );
outc( 'dnp1', 77, 137, 108, 182, 0, 'right' );

outc( 'dfm1', 29, 213, 60, 258, 0, 'left' );
outc( 'dfp1', 76, 213, 106, 258, 0, 'right' );

$dr = 481;
outc( 'dwp2', 29 + $dr, 62, 60 + $dr, 107, 0, 'left' );
outc( 'dwm2', 77 + $dr, 62, 108 + $dr, 107, 0, 'right' );

outc( 'dnp2', 29 + $dr, 139, 60 + $dr, 184, 0, 'left' );
outc( 'dnm2', 77 + $dr, 139, 108 + $dr, 184, 0, 'right' );

outc( 'dfp2', 29 + $dr, 215, 60 + $dr, 260, 0, 'left' );
outc( 'dfm2', 76 + $dr, 215, 106 + $dr, 260, 0, 'right' );

echo "<img width=43 height=15 style='position:absolute;left:158px;top:260px' src=images/magic/t1.png>";
echo "<img width=43 height=15 style='position:absolute;left:414px;top:262px' src=images/magic/t1.png>";

echo "<img id=ts1 style='position:absolute;left:153px;top:168px;width:34px;height:0px' src=images/magic/t2.png>";
echo "<img id=ts2 style='position:absolute;left:409px;top:170px;width:34px;height:0px' src=images/magic/t2.png>";

echo "<img width=105 height=92 id=tt1 style='position:absolute;left:126px;top:168px' src=images/magic/t3.png>";
echo "<img width=105 height=92 id=tt2 style='position:absolute;left:382px;top:170px' src=images/magic/t3.png>";

echo "<img id=ds1 width=27 height=10 style='position:absolute;left:155px;top:281px' src=images/magic/dl.png>";
echo "<img id=ds2 width=26 height=9 style='position:absolute;left:434px;top:281px' src=images/magic/dr.png>";

echo "<img id=dd1 style='position:absolute;left:182px;top:281px;height:10px;width:0px;' src=images/magic/d.png>";
echo "<img id=dd2 style='position:absolute;left:434px;top:281px;height:10px;width:0px;' src=images/magic/d.png>";

echo "</div></td><td valign=top><div id=cards style='position:relative;left:0px;top:0px;'>&nbsp;";

$id = 0;
for( $i = 0; $i < 2; ++ $i )
	for( $j = 0; $j < 4; ++ $j )
	{
		if( $i == 1 && $j == 0 ) echo "<img src=images/magic/card.png style='z-index:5;position:absolute;left:0px;top:155px;width:96px;height:150px;'>";
		else{  echo "<img onclick='javascript:cast($id)' style='z-index:1;cursor:pointer;position:absolute; left:".(5+95*$j)."px;top:".(5+155*$i)."px;width:90px;height:145px;' id=card".($id).">"; ++ $id; }

	}

for( $i = 0; $i < 10; ++ $i )
{
	echo "<img src=empty.gif style='cursor:pointer;position:absolute; left:".(-390+20*$i)."px;top:".(35+20*($i%2)+2*$i)."px;width:90px;height:145px;' id=card".($id).">";
	++ $id;
}

echo "<img src=empty.gif style='cursor:pointer;position:absolute; left:-90px;top:100px;width:90px;height:145px;' id=card18>";

echo "</div><div id=finst>&nbsp;</div></td></tr></table>";

?>

<script>

var krya = new Image( );
krya.src = 'images/magic/empty.png';

var xs = new Array( );
var ys = new Array( );

var id = 0;
for( var i = 0; i < 2; ++ i )
	for( var j = 0; j < 4; ++ j )
	{
		if( i == 1 && j == 0 )
		{
    		xs[17] = (5+95*j);
    		ys[17] = (5+155*i);
    		continue;
		}
		xs[id] = (5+95*j);
		ys[id] = (5+155*i);
		++ id;
	}
for( var i = 0; i < 10; ++ i )
{
	xs[id] = (-390+20*i);
	ys[id] = (35+20*(i%2)+2*i);
	++ id;
}
xs[18] = -715;
ys[18] = 100;

function refr( n2, n1, t2, t1, d2, d1, wp2, wp1, np2, np1, fp2, fp1, wm2, wm1, nm2, nm1, fm2, fm1 )
{
/*	if( anim )
	{
		animq.push( [n2, n1, t2, t1, d2, d1, wp2, wp1, np2, np1, fp2, fp1, wm2, wm1, nm2, nm1, fm2, fm1] );
		return;
	}
*/

	for( var i = 0; i < 19; ++ i ) if( i != 17 )
	{
		_( 'card' + i ).style.left = xs[i] + 'px';
		_( 'card' + i ).style.top = ys[i] + 'px';
	}

	_( 'name1' ).innerHTML = '<b><font color=silver>' + n1 + '</font></b>';
	_( 'name2' ).innerHTML = '<b><font color=silver>' + n2 + '</font></b>';

	_( 'tree1' ).innerHTML = '<b><font color=white>' + t1 + '</font></b>';
	_( 'tree2' ).innerHTML = '<b><font color=white>' + t2 + '</font></b>';
	_( 'ditch1' ).innerHTML = '<b><font color=white>' + d1 + '</font></b>';
	_( 'ditch2' ).innerHTML = '<b><font color=white>' + d2 + '</font></b>';

	_( 'dwp1' ).innerHTML = '<b><font color=navy>' + wp1 + '</font></b>';
	_( 'dwm1' ).innerHTML = '<b><font color=navy>' + wm1 + '</font></b>';
	_( 'dnp1' ).innerHTML = '<b><font color=green>' + np1 + '</font></b>';
	_( 'dnm1' ).innerHTML = '<b><font color=green>' + nm1 + '</font></b>';
	_( 'dfp1' ).innerHTML = '<b><font color=darkred>' + fp1 + '</font></b>';
	_( 'dfm1' ).innerHTML = '<b><font color=darkred>' + fm1 + '</font></b>';

	_( 'dwp2' ).innerHTML = '<b><font color=navy>' + wp2 + '</font></b>';
	_( 'dwm2' ).innerHTML = '<b><font color=navy>' + wm2 + '</font></b>';
	_( 'dnp2' ).innerHTML = '<b><font color=green>' + np2 + '</font></b>';
	_( 'dnm2' ).innerHTML = '<b><font color=green>' + nm2 + '</font></b>';
	_( 'dfp2' ).innerHTML = '<b><font color=darkred>' + fp2 + '</font></b>';
	_( 'dfm2' ).innerHTML = '<b><font color=darkred>' + fm2 + '</font></b>';

	if( t1 > 75 ) t1 = 75; if( t2 > 75 ) t2 = 75;
	if( d1 > 40 ) d1 = 40; if( d2 > 40 ) d2 = 40;

	_( 'tt1' ).style.top = ( 168 - t1 * 2 ) + 'px';
	_( 'tt2' ).style.top = ( 170 - t2 * 2 ) + 'px';
	_( 'ts1' ).style.top = ( 260 - t1 * 2 ) + 'px';
	_( 'ts2' ).style.top = ( 262 - t2 * 2 ) + 'px';
	_( 'ts1' ).style.height = ( t1 * 2 ) + 'px';
	_( 'ts2' ).style.height = ( t2 * 2 ) + 'px';

	if( d1 )
	{
		_( 'ds1' ).style.display = '';
		_( 'dd1' ).style.width = ( ( d1 - 1 ) * 3 ) + 'px';
	}
	else
	{
		_( 'ds1' ).style.display = 'none';
		_( 'dd1' ).style.width = '0px';
	}

	if( d2 )
	{
		_( 'ds2' ).style.display = '';
		_( 'dd2' ).style.width = ( ( d2 - 1 ) * 3 ) + 'px';
		_( 'dd2' ).style.left = ( 434 - ( d2 - 1 ) * 3 ) + 'px';
	}
	else
	{
		_( 'ds2' ).style.display = 'none';
		_( 'dd2' ).style.width = '0px';
	}

}

var cur_imgs = new Array( );
function card_a( pos, cid, hid )
{
	var fname = 'images/magic/' + ((cid < 100)?1+cid:'empty') + '.png';
	if( cur_imgs[pos] != fname )
	{
		cur_imgs[pos] = fname;
   		_( 'card' + pos ).src = krya.src;
		_( 'card' + pos ).src = fname;
	}

		if( hid )
		{
			document.getElementById( 'card' + pos ).style.opacity=0.35;
			document.getElementById( 'card' + pos ).style.filter='alpha(opacity=35)';
		}
		else
		{
			document.getElementById( 'card' + pos ).style.opacity=1;
			document.getElementById( 'card' + pos ).style.filter='';
		}
}

function card( pos, cid )
{
	card_a( pos, cid, 0 );
}

function cl( pos )
{
	var fname = 'empty.gif';
	if( _( 'card' + pos ).src != fname ) _( 'card' + pos ).src = fname;
}

function cast( id )
{
	query( 'magic_cast.php?id='+id, '' );
}

var anim = false;
var iv = false;
var finished = false;
function hru( )
{
	if( !anim && !finished )
		query( 'magic_ref.php', '' );
}

function listen( )
{
	iv = setInterval( hru, 5000 );
}

var step;
var animq = new Array( );
function pra( r, a1, a2, c, e )
{
	step += 4;
	if( step >= 100 )
	{
		step = 100;
		anim = false;
		clearInterval( iv );

		while( animq.length > 0 )
		{
			if( animq[0].length == 5 )
			{
            	anim = true;
            	step = 0;
            	iv = setInterval( "pra( " + animq[0][0] + ", " + animq[0][1] + ", " + animq[0][2] + ", " + animq[0][3] + ", "+animq[0][4]+" );", 40 );
				for( var i = 1; i < animq.length; ++ i )
					animq[i - 1] = animq[i];
				animq.pop( );
				return;
    		}
    		else if( animq[0].length == 1 ) eval( animq[0][0] );

			for( var i = 1; i < animq.length; ++ i )
				animq[i - 1] = animq[i];
			animq.pop( );
		}

		anim = false;
		hru( );
		listen( );
	}
	var x = ( xs[a1] * ( 100 - step ) + xs[a2] * step ) / 100;
	var y = ( ys[a1] * ( 100 - step ) + ys[a2] * step ) / 100;
	card_a( r, c, e );
	_( 'card' + r ).style.left = parseInt( x ) + 'px';
	_( 'card' + r ).style.top = parseInt( y ) + 'px';
}
function doa( r, a1, a2, c, e )
{
	if( anim )
	{
		animq.push( [r, a1, a2, c, e] );
		return;
	}
	clearInterval( iv );
	anim = true;
	step = 0;
	iv = setInterval( "pra( " + r + ", " + a1 + ", " + a2 + ", " + c + ", "+e+" );", 40 );
}

function do_act( str )
{
	if( anim ) animq.push( [str] );
	else eval( str );
}

<?
refr($player->player_id);
?>

listen( );

</script>

