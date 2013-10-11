var di = new Array( );
var dx, dy, dz = 0, did, dloc, dit;
var dht;

var pr_d = 0;
var pr_x, pr_y;

var cr = parent.char_ref;

function g(a)
{
	return document.getElementById( a );
}

function unconvert_slot( a )
{
	if( a == 1 ) return 9;
	if( a == 0 ) return 10;
	if( a == 9 ) return 13;
	if( a == 10 ) return 12;
	if( a == 12 ) return 1;
	if( a > 12 ) return a + 1;
	return a;
}

function prepare_drag( e )
{
	if( e )
	{
		x = e.pageX;
		y = e.pageY;
	}
	else
	{
		x = window.event.clientX;
		y = window.event.clientY;
	}

	pr_d = 1;
	pr_x = x;
	pr_y = y;
	
	return false;
}

function getAbsolutePos(el)
{
   var r = { x: el.offsetLeft, y: el.offsetTop };
   if (el.offsetParent)
   {
       var tmp = getAbsolutePos(el.offsetParent);
       r.x += tmp.x;
       r.y += tmp.y;
   }
   return r;
}

function begin_drag( e )
{
	var x, y;
	var rx, ry;
	
	if( !pr_d )
	{
		if( this.id.substr( 0, 4 ) == 'item' )
		{
			id = this.id.substr( 4 );
			-- id;
			if( cr.p[id] ) showTooltipW( e, cr.items[id][2], 250 );
			else showTooltip( e, cr.empty_hints[id] );
		}
		return;
	}
	if( dz ) return;

	x = pr_x;
	y = pr_y;

	rx = -1;
	krolik = getAbsolutePos( g( 'char_items' ) );
	if( this.id.substr( 0, 4 ) == 'item' )
	{
		id = this.id.substr( 4 );
		-- id;
		if( cr.p[id] )
		{
			pos = getAbsolutePos( this );
			rx = pos.x - krolik.x;
			ry = pos.y - krolik.y;
			did = cr.items[id][0];
			dloc = id;
			st = '<table cellspacing=0 cellpadding=0 border=0><tr><td align=center valign=center>';
			st += '<img src=images/items/' + cr.items[id][4] + '>';
			st += '</td></tr></table>';
			dht = st;
		}
	}
	else if( this.id.charAt( 0 ) == 'n' )
	{
		id = this.id.substr( 2 );
//		pos = getAbsolutePos( this );
		rx = pr_x - 15 - document.body.scrollLeft - krolik.x;
		ry = pr_y - 15 - document.body.scrollTop - krolik.y;
		did = items[id].item_id;
		dloc = -1;
		st = '<table cellspacing=0 cellpadding=0 border=0><tr><td align=center valign=center>';
		st += '<img src=images/items/' + items[id].image + '>';
		st += '</td></tr></table>';
		dht = st;
	}

	if( rx == -1 )
	{
		pr_d = 0;
		return;
	}

	dx = x - rx;
	dy = y - ry;
	dz = 1;
	
	hideTooltip( );

	g( 'item_drag' ).innerHTML = dht;
	g( 'item_drag' ).style.left = rx;
	g( 'item_drag' ).style.top = ry;
	g( 'item_drag' ).style.display = '';
	
	return false;
}

function drag( e )
{
	var x, y;

	if( dz )
	{
		if( e )
		{
			x = e.pageX;
			y = e.pageY;
		}
		else
		{
			x = window.event.clientX;
			y = window.event.clientY;
		}

		nx = x - dx;
		ny = y - dy;

		g( 'item_drag' ).style.left = nx;
		g( 'item_drag' ).style.top = ny;
	}

	return false;
}

function end_drag( e )
{
	var x, y;
	var nid, nloc;

	pr_d = 0;

	if( dz )
	{
		dz = 0;

		var x, y;
		var rx, ry;

		if( e )
		{
			x = e.pageX - document.body.scrollLeft;
			y = e.pageY - document.body.scrollTop;
		}
		else
		{
			x = window.event.clientX/* + document.body.scrollLeft*/;
			y = window.event.clientY/* + document.body.scrollTop*/;
		}

		nid = -1;
		pos = getAbsolutePos( g( 'char_items' ) );
		for( i = 1; i <= 12; ++ i )
			if( x >= cr.xs[i - 1] + pos.x && x <= cr.xs[i - 1] + cr.ws[i - 1] + pos.x && y >= cr.ys[i - 1] + pos.y && y <= cr.ys[i - 1] + pos.y + cr.ws[i - 1] )
			{
				if( !cr.p[i - 1] )
				{
					nid = 1;
					nloc = i - 1;
				}
				else nid = 0;
			}

		g( 'item_drag' ).style.display = 'none';
		
		if( nid == 0 ) return;
		if( nid == -1 )
		{
			nloc = -1;
		}

		st = "item_id=" + did + "&from=" + unconvert_slot( dloc ) + "&to=" + unconvert_slot( nloc );
		parent.game_ref.location.href = "item_dragdrop.php?" + st;
	}
}

var chrtmo = 0;

function char_item_info( )
{
   	id = this.id.substr( 4 );
   	-- id;
	function doit( )
	{
    	if( cr.p[id] )
    	{
        	did = cr.items[id][0];
        	st = "item=" + did + "&w=" + unconvert_slot( id );
        	i_do( st );
        	hideTooltip( );
    	}
	}
	chrtmo = setTimeout( doit, 250 );
}

function char_unwear( )
{
	clearTimeout( chrtmo );
	setTimeout( 'clearTimeout( chrtmo );', 50 );
	id = this.id.substr( 4 );
	-- id;
	if( cr.p[id] )
	{
    	did = cr.items[id][0];
    	st = "item_id=" + did + "&from=" + unconvert_slot( id ) + "&to=-1";
    	parent.game_ref.location.href = "item_dragdrop.php?" + st;
	}
}

function char_set_events( )
{
	for( i = 1; i <= 25; ++ i )
	{
		document.getElementById( 'item' + i ).onmousedown = prepare_drag;
		document.getElementById( 'item' + i ).onmousemove = begin_drag;
		document.getElementById( 'item' + i ).onclick = char_item_info;
		document.getElementById( 'item' + i ).ondblclick = char_unwear;
		document.getElementById( 'item' + i ).onmouseout = hideTooltip;
	}
	
	for (i=1;i<=5;++i)
	{
		if (document.getElementById( 'pot' + i ))
		{
		document.getElementById( 'pot' + i ).onmousemove = safeTooltip;
		document.getElementById( 'pot' + i ).onmouseout = hideTooltip;
		}
	}

	document.getElementById( 'item_drag' ).onmousemove = drag;
	document.onmousemove = drag;

	document.getElementById( 'item_drag' ).onmouseup = end_drag;
	document.onmouseup = end_drag;
}

