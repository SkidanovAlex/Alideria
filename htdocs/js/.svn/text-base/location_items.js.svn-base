loc_item_names = new Array( );
loc_items = new Array( );

function reset_loc_items( )
{
	loc_item_names = new Array( );
	loc_items = new Array( );
}

function add_loc_item( id, nm, num )
{
	loc_item_names[id] = nm;
	if( loc_items[id] == undefined ) loc_items[id] = 0;
	loc_items[id] += num;
}

function set_loc_items( id, num )
{
	loc_items[id] = num;
	show_loc_items( );
}

function show_loc_items( )
{
	var ok = false;
	for( i in loc_items ) if( loc_items[i] > 0 ) ok = true;
	
	var st = '&nbsp;';
	if( ok )
	{
		st = '<hr><b>Вещи тут:</b><br>';
		st += '<table>';
		for( i in loc_items ) if( loc_items[i] > 0 )
		{
			st += '<tr><td>[' + loc_items[i] + ']&nbsp;' + '<b>' + loc_item_names[i] + '</b>' + '&nbsp;</td><td>&nbsp;';
			st += '&nbsp;<input maxlength=4 id=liget' + i + ' class=btn40 value=1></td><td><img width=20 height=20 src=images/right_arrow.jpg alt=Взять border=0 style="cursor:pointer" onclick="get_loc_items( ' + i + ', \'\' + document.getElementById( \'liget' + i + '\' ).value )"></td></tr>';
		}
		st += '</table>';
	}
	document.getElementById( 'location_items' ).innerHTML = st;
}

function get_loc_items( id, num )
{
	query( "location_get_items.php", id + "|" + num );
}
