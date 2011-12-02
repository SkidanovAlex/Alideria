<?

include_js( 'event_handlers.js' );

?>

<div id=q7_act>
<li><a onmousedown='location.href="game.php?phrase=1298"' href=#>”йти</a>
</div>

<table cellspacing=0 cellpadding=0><tr><td>

<div id=word style='position:relative;top:0px;left:0px;'>

<img width=319 height=320 border=0 src=images/misc/q7/phrase/bg.jpg>

</div>

</td><td>

<div id=word2 style='position:relative;width:420px;height:300px;top:0px;left:0px;'>
&nbsp;
</div>

</td></tr></table>

<div id=dbg>&nbsp;</div>

<script>

function getTile( a, x, y, id )
{
	if( a == "" ) return "<img style='position:absolute;left:"+x+"px;top:"+y+"px;width:86px;height:23px;' src='images/misc/q7/phrase/btn.png'>";
	else
	{
		return "<table id=tbl" + id + " cellspacing=0 cellpadding=0 background='images/misc/q7/phrase/btn.png' style='position:absolute;left:"+x+"px;top:"+y+"px;width:86px;height:23px;'><tr><td align=center valign=middle><small>" + a + "</small></td></tr></table>";
	}
}

var xxx, yyy, zzz, ddd;
var fmove = function(e)
		{
			var x, y; 
        	if( document.all )
        	{
        		x = window.event.clientX;
        		y = window.event.clientY;
        	}
        	else
        	{
        		x = e.pageX;
        		y = e.pageY;
        	}
			if( zzz )
			{
				xx[ddd] = xx[ddd] - xxx + x;
				yy[ddd] = yy[ddd] - yyy + y;
				xxx = x;
				yyy = y;
				_( 'tbl' + ddd ).style.left = xx[ddd] + 'px';
				_( 'tbl' + ddd ).style.top = yy[ddd] + 'px';
				
				var tx = 15 + ( ddd % 3 ) * 100;
				var ty = Math.floor( ddd / 3 ) * 50 + 20;
				var p = getAp( _( 'word' ) );
				tx += p.x;
				ty += p.y;
				if( document.all || 1 )
				{
					p = getAp( _( 'word2' ) );
					tx -= p.x;
					ty -= p.y;
				}
				if( Math.abs( tx - xx[ddd] ) < 40 && Math.abs( ty - yy[ddd] ) < 15 && ( ddd == 0 || onboard[ddd - 1] ) )
				{
					zzz = 0;
					onboard[ddd] = 1;
					render( );
				}
			}
    		return false;
		};
var fup = function(e)
		{
			zzz = 0;
			render( );
    		return false;
		};
addHandler( document, 'mouseup', fup );
addHandler( document, 'mousemove', fmove );

var onboard = [];
var xx = [];
var yy = [];
var words = ['ј', 'вечной', 'и', 'счастливой', 'жизни', 'ордену', 'родному', 'желает', 'тот', 'кто', 'шар', 'отыщет', 'да', 'новых', 'друзей', 'обретет', 'вновь', 'он'];

for( var i = 0; i < 18; ++ i )
{
	xx[i] = Math.floor( Math.random( ) * 400 );
	yy[i] = Math.floor( Math.random( ) * 300 );
	onboard[i] = 0;
}

function render( )
{
	var st = '';
	for( var i = 0; i < 18; ++ i ) if( !onboard[i] )
	{
		st += getTile( words[i], xx[i], yy[i], i );
	}
	_( 'word2' ).innerHTML = st;
	for( var i = 0; i < 18; ++ i ) if( !onboard[i] )
	{
		function assign_events( i )
		{
    		_( 'tbl' + i ).onmousedown = function(e)
    		{
    			var x, y; 
            	if( document.all )
            	{
            		x = window.event.clientX;
            		y = window.event.clientY;
            	}
            	else
            	{
            		x = e.pageX;
            		y = e.pageY;
            	}
    			zzz = 1; xxx = x; yyy = y; ddd = i;
	    		return false;
    		}
    		_( 'tbl' + i ).onmouseup = fup;
    		_( 'tbl' + i ).onmousemove = fmove;
    	}
    	assign_events( i );
	}
	
	st = '<img width=319 height=320 border=0 src=images/misc/q7/phrase/bg.jpg>';
	for( var i = 0; i < 18; ++ i )
	{
		var qq;
		var x = 15 + ( i % 3 ) * 100;
		var y = Math.floor( i / 3 ) * 50 + 20;
		if( onboard[i] )  qq = getTile( words[i], x, y, i );
		else qq = getTile( '', x, y, -1 );
		st += qq;
	}
	_( 'word' ).innerHTML = st;
	
	var ok = 1;
	for( var i = 0; i < 18; ++ i ) if( !onboard[i] ) ok = 0;
	if( ok )
	{
		_('q7_act' ).innerHTML = "<li><a onmousedown='location.href=\"game.php?phrase=1299\"' href=#>‘раза собрана! ќбрадовать супер–ыжика!</a>";
	}
}

render( );

</script>
