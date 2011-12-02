<script src=js/skin.js></script>
<script src=functions.js></script>

<?

    echo "<br><center><table><tr><td><table style='border:1px solid black' cellspacing=0 cellpadding=0>";

    $id = 0;
    for( $i = 0; $i < 6; ++ $i )
    {
    	echo "<tr>";
    	for( $j = 0; $j < 18; ++ $j )
    	{
    		$b = "1px solid black";
    		$border = "";
    		if( $j != 17 ) $border .= "border-right: $b;";
    		if( $i != 5 ) $border .= "border-bottom: $b;";
    		echo "<td align=center valign=middle style='width:48px;height:48px;cursor:pointer;{$border}background-color:#e0c3a0;'>";
    		echo "<div style='position:relative;top:0px;left:0px;width:48px;height:48px;' id=td$i$j>&nbsp;</div>";
    		echo "</td>";
    		++ $id;
    	}
    	echo "</tr>";
    }

    echo "</table>";


    echo "</td></tr></table><small>Поле генерируется заново при каждом открытии страницы.<br></small><a href=waste.php>Начать заново</a></center>";
?>

<script>

var anim = new Array( );

function do_anim( )
{
	if( anim.length == 0 ) return;
	var a = anim[0];
	if( a.act == 0 || a.act == 1 )
	{
		var moo = a.stage;
		if( a.act == 1 ) moo = 48 - moo;
		if( a.stage > 48 ) a.stage = -1;
		else
		{
			td1 = _( 'td' + a.y1 + '' + a.x1 );
			td2 = _( 'td' + a.y2 + '' + a.x2 );
			if( a.x1 > a.x2 )
			{
				td1.style.left = ( - moo );
				td2.style.left = moo;
			}
			if( a.x1 < a.x2 )
			{
				td1.style.left = moo;
				td2.style.left = ( - moo );
			}
			if( a.y1 > a.y2 )
			{
				td1.style.top = ( - moo );
				td2.style.top = moo;
			}
			if( a.y1 < a.y2 )
			{
				td1.style.top = moo;
				td2.style.top = ( - moo );
			}
		}       
	}
	else if( a.act == 2 )
	{
		var t = F[cy][cx];
		F[cy][cx] = F[sy][sx];
		F[sy][sx] = t;
		if( check( ) )
		{
		}
    	else
    	{
    		var t = F[cy][cx];
    		F[cy][cx] = F[sy][sx];
    		F[sy][sx] = t;

			var o = new Object;
			o.act = 1; o.stage = 0; o.x1 = cx; o.y1 = cy; o.x2 = sx; o.y2 = sy;
			anim.push( o );
    	}
    	a.stage = -1;
	}
	else if( a.act == 3 )
	{
        for( var j = 0; j < 18; ++ j )
        {
        	var spaces = 0;
        	for( var i = 5; i >= 0; -- i )
			{
				v[i][j] = 0;
				if( F[i][j] == ' ' ) ++ spaces;
				else v[i][j] = spaces;
			}
		}
		a.stage = -1;
	}
	else if( a.act == 4 )
	{
		var go_on = false;
		for( var i = 0; i < 6; ++ i )
			for( var j = 0; j < 18; ++ j ) if( v[i][j] > 0 )
			{
				var moo = a.stage;
				if( moo > 49 * v[i][j] ) moo = 49 * v[i][j];
				else go_on = true;
				_( 'td' + i + '' + j ).style.top = moo + 'px';
			}
		if( !go_on ) a.stage = -1;
	}
	else if( a.act == 5 )
	{
        for( var j = 0; j < 18; ++ j )
        {
        	var spaces = 0;
        	for( var i = 5; i >= 0; -- i )
			{
				if( F[i][j] == ' ' )
				{
					for( var k = i - 1; k >= 0; -- k )
					{
						if( F[k][j] != ' ' )
						{
							F[i][j] = F[k][j];
							F[k][j] = ' ';
							break;
						}
					}
				}
			}
		}

		out2( );
		a.stage = -1;
 	}
 	else if( a.act == 8 )
 	{
        for( var i = 0; i < 6; ++ i )
        {
        	var spaces = 0;
        	for( var j = 17; j >= 0; -- j )
			{
				v[i][j] = 0;
				if( F[i][j] == ' ' ) ++ spaces;
				else v[i][j] = spaces;
			}
		}
		a.stage = -1;
	}
	else if( a.act == 6 )
	{
		var go_on = false;
		for( var i = 0; i < 6; ++ i )
			for( var j = 0; j < 18; ++ j ) if( v[i][j] > 0 )
			{
				var moo = a.stage;
				if( moo > 49 * v[i][j] ) moo = 49 * v[i][j];
				else go_on = true;
				_( 'td' + i + '' + j ).style.left = moo + 'px';
			}
		if( !go_on )
		{
			a.stage = -1;
            for( var i = 0; i < 6; ++ i )
            {
            	var spaces = 0;
            	for( var j = 17; j >= 0; -- j )
    			{
    				if( F[i][j] == ' ' )
    				{
    					for( var k = j - 1; k >= 0; -- k )
    					{
    						if( F[i][k] != ' ' )
    						{
    							F[i][j] = F[i][k];
    							F[i][k] = ' ';
    							break;
    						}
    					}
    				}
    			}
    		}
    		out2( );
		}
	}
	else if( a.act == 7 )
	{
		check( ); a.stage = -1;
	}

	if( a.stage == -1 )
	{
		for( var i = 1; i < anim.length; ++ i )
			anim[i - 1] = anim[i];
		anim.pop( );
	}
	else anim[0].stage += 8;
}

setInterval( do_anim, 40 );

var sx, sy;
var cx, cy;
var zz = false;
var F;
var frz = false;
var L = 0;
var v = new Array( );
for( var i = 0; i < 6; ++ i ) v[i] = new Array( );

function g_down( e )
{
	if( L ) return;
	if( anim.length ) return false;

	if( document.all )
	{
		if( event.button == 1 )
		{
			mx = window.event.clientX + document.body.scrollLeft;
			my = window.event.clientY + document.body.scrollTop;
		}
		else return;
	}
	else
	{
		if( e.which == 1 )
		{
			mx = e.pageX;
			my = e.pageY;
		}
		else return;
	}

	var hru = getAp( _( 'td00' ) );

	var x = parseInt( ( mx - hru.x ) / 49 );
	var y = parseInt( ( my - hru.y ) / 49 );

	if( x < 0 || x >= 18 || y < 0 || y >= 6 ) return;

	if( F[y][x] == ' ' ) return;
	sx = cx = x; sy = cy = y;
	zz = true;

	return false;
}

function g_move( e )
{
	if( !zz ) return;

	if( document.all )
	{
		if( event.button == 1 )
		{
			mx = window.event.clientX + document.body.scrollLeft;
			my = window.event.clientY + document.body.scrollTop;
		}
		else return;
	}
	else
	{
		if( e.which == 1 )
		{
			mx = e.pageX;
			my = e.pageY;
		}
		else return;
	}

	var hru = getAp( _( 'td00' ) );

	var x = parseInt( ( mx - hru.x ) / 49 );
	var y = parseInt( ( my - hru.y ) / 49 );

	if( x < 0 || x >= 18 || y < 0 || y >= 6 ) return;

	if( F[y][x] == ' ' ) return;

	if( Math.abs( x - sx ) + Math.abs( y - sy ) == 1 )
	{
    	if( ( cy != y || cx != x ) )
    	{
    		if( cx != sx || cy != sy )
    		{
    			var o = new Object;
    			o.act = 1; o.stage = 0; o.x1 = cx; o.y1 = cy; o.x2 = sx; o.y2 = sy;
    			anim.push( o );
    		}
    		cx = x;
    		cy = y;
    		var o = new Object;
    		o.act = 0; o.stage = 0; o.x1 = cx; o.y1 = cy; o.x2 = sx; o.y2 = sy;
    		anim.push( o );
    	}
	}
	else if( x == sx && y == sy )
	{
		if( cx != sx || cy != sy )
		{
			var o = new Object;
			o.act = 1; o.stage = 0; o.x1 = cx; o.y1 = cy; o.x2 = sx; o.y2 = sy;
			anim.push( o );
			cx = sx;
			cy = sy;
		}
	}
	else
	{
		if( cx != sx || cy != sy )
		{
			var o = new Object;
			o.act = 1; o.stage = 0; o.x1 = cx; o.y1 = cy; o.x2 = sx; o.y2 = sy;
			anim.push( o );
			cx = sx;
			cy = sy;
		}
	}

	return false;
}

function check( )
{
	var NF = new Array( );
	var ret = false;
	for( var i = 0; i < 6; ++ i )
	{
		NF[i] = new Array( );
		for( var j = 0; j < 18; ++ j ) if( F[i][j] != ' ' )
		{
			var k = 1;
			var jj, ii;
			jj = j + 1; while( jj < 18 && F[i][j] == F[i][jj] ) { ++ k; ++ jj; }
   			jj = j - 1; while( jj >= 0 && F[i][j] == F[i][jj] ) { ++ k; -- jj; }
   			if( k >= 3 )
   			{
   				NF[i][j] = ' ';
   				ret = true;
   				continue;
   			}
   			k = 1;
			ii = i + 1; while( ii < 6 && F[i][j] == F[ii][j] ) { ++ k; ++ ii; }
   			ii = i - 1; while( ii >= 0 && F[i][j] == F[ii][j] ) { ++ k; -- ii; }
			if( k >= 3 )
			{
				NF[i][j] = ' ';
				ret = true;
				continue;
			}
			NF[i][j] = F[i][j];
		} else NF[i][j] = ' ';
	}
	if( ret )
	{
		F = NF;
		out2( );
		var o = new Object( );
		o.act = 8; o.stage = 0; anim.push( o );
		var o = new Object( );
		o.act = 6; o.stage = 0; anim.push( o );
		var o = new Object( );
		o.act = 3; o.stage = 0; anim.push( o );
		var o = new Object( );
		o.act = 4; o.stage = 0; anim.push( o );
		var o = new Object( );
		o.act = 5; o.stage = 0; anim.push( o );
		var o = new Object( );
		o.act = 7; anim.push( o );
	}

	return ret;
}

function g_up( )
{
	if( !zz ) return;
	zz = false;
	if( cx != sx || cy != sy )
	{
		var o = Object;
		o.act = 2;
		anim.push( o );
	}
}

function out2( )
{
	var la = 9;
	for( var i = 0; i < 6; ++ i )
		for( var j = 0; j < 18; ++ j )
		{
			if( F[i][j] == '0' ) -- la;
			if( F[i][j] == ' ' ) _( 'td' + i + '' + j ).innerHTML = '&nbsp;';
			else if( F[i][j] == '0' ) _( 'td' + i + '' + j ).innerHTML = '<img width=48 height=48 src="images/items/coal.gif">';
			else _( 'td' + i + '' + j ).innerHTML = '<table background=images/misc/' + F[i][j] + '.png width=48 height=48 cellspacing=0 cellpadding=0 border=0><tr><td><img src=empty.gif style="width:48px;height:48px;"></td></tr></table>';
			_( 'td' + i + '' + j ).style.left = '0px';
			_( 'td' + i + '' + j ).style.top = '0px';
		}
}

function out( s, m )
{
	F = new Array( );
	L = m;
	var id = 0;
	for( var i = 0; i < 6; ++ i )
	{
		F[i] = new Array( );
		for( var j = 0; j < 18; ++ j )
		{
			F[i][j] = s.charAt( id );
			++ id;
		}
	}
	out2( );
}

var st = new Array( );
for( var i = 0; i < 18; ++ i ) st.push( '1' );
for( var i = 0; i < 18; ++ i ) st.push( '2' );
for( var i = 0; i < 18; ++ i ) st.push( '3' );
for( var i = 0; i < 18; ++ i ) st.push( '4' );
for( var i = 0; i < 18; ++ i ) st.push( '5' );
for( var i = 0; i < 18; ++ i ) st.push( '6' );
for( var i = 0; i < 108; ++ i )
{
	j = parseInt( Math.random( ) * ( 108 - i ) ) + i;
	t = st[i];
	st[i] = st[j];
	st[j] = t;
}
var sst = '';
for( var i = 0; i < 108; ++ i ) sst += st[i];

out( sst, 0 );
check();

function dummy() {return false;}

document.onmouseup = g_up;
document.onselect = dummy;
document.onmousedown = g_down;
document.onmousemove = g_move;
document.body.onselect = dummy;

</script>

