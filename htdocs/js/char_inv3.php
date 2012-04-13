function char_set_events_noinv( )
{
	var ph;
	if( parent && parent.char_ref ) ph = parent.char_ref.pet_hint;
	else ph = pet_hint;
	
	for( i = 1; i <= 25; ++ i )
	{
		document.getElementById( 'item' + i ).onmousemove = safeTooltip;
		document.getElementById( 'item' + i ).onmouseout = hideTooltip;
	}
	var pt = document.getElementById('pot1');
	if (pt)
	for (i=1;i<=5;++i)
	{
		if (document.getElementById( 'pot' + i ))
		{
		document.getElementById( 'pot' + i ).onmousemove = safeTooltip;
		document.getElementById( 'pot' + i ).onmouseout = hideTooltip;
		}
	}
	if( ph != '' )
	{
	   	document.getElementById( 'pet_img' ).onmousemove = function(e) { showTooltipW( e, ph, 250 ); };
   		document.getElementById( 'pet_img' ).onmouseout = function(e) { hideTooltip( ); };
	}
}

var char_y_offset = 0;
var char_timeout = 0;

function safeTooltip( e )
{
	var cr = parent.char_ref;
	if( this.id.substr( 0, 4 ) == 'item' )
	{
		id = this.id.substr( 4 );
		-- id;
		if( cr ) { if( cr.p[id] ) showTooltipW( e, cr.items[id][2], 250 ); else showTooltip( e, cr.empty_hints[id] ); }
		else { if( p[id] ) showTooltipW( e, items[id][2], 250 ); else showTooltip( e, empty_hints[id] ); }
	}
	if(this.id.substr(0, 3) == 'pot')
	{
		id = this.id.substr( 3 );
		var hint_p = '';
		if (id==1) hint_p='Зелья';
		if (id==2) hint_p='Талисманы';
		if (id==3) hint_p='Медальоны';
		if (id==4) hint_p='Бальзамы';
		showTooltip(e, hint_p);
	}
}

