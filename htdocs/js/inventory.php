<?

include( '../arrays.php' );
include( '../skin.php' );

echo "fupper = '".AddSlashes( GetScrollTableStart( ) )."';\n";
echo "flower = '".AddSlashes( GetScrollTableEnd( ) )."';\n";

print( "type_names = new Array( );\n" );

foreach( $item_types_all as $a=>$b ) printf( "type_names[$a] = '$b';\n" );


print( "type_names2 = new Array( );\n" );

printf( "type_names2[-1] = 'Все вещи'\n" );
foreach( $item_types as $a=>$b ) printf( "type_names2[$a] = '$b';\n" );


print( "type2_names = new Array( );\n" );
foreach( $item_types2 as $a=>$b ) printf( "type2_names[$a] = '$b';\n" );


?>



var items2 = new Array( );
var items = new Array( );
var slots = new Array( );
var type_nums = new Array( );
var type2_nums = new Array( );
var sets = new Array( );
var n = 0;
var cur_filter = -1;
var wear_level = 0;
var total_weight = 0;
var ioffset = 0;

for( q in type_names2 ) type_nums[q] = 0;
for( q in type2_names ) type2_nums[q] = 0;

function myf(n)
{
	return Number(n).toFixed(2);
}

function item_copy( from )
{
	var to = new Object( );
	to.item_id = from.item_id;
	to.name = from.name;
	to.image = from.image;
	to.descr = from.descr;
	to.weight = from.weight;
	to.type = from.type;
	to.type2 = from.type2;
	to.num = 0;
	to.slot = 0;
	
	return to;
}

function add_set( id, nm )
{
	sets[id] = nm;
}

function del_set( id )
{
	sets[id] = false;
}

function add_item( id, nm, img, descr, num, wg, slot, type, type2 )
{
	if( !items2[id] )
	{
		items2[id] = new Object( );
		items2[id].item_id = id;
		items2[id].name = nm;
		items2[id].image = img;
		items2[id].descr = descr;
		items2[id].num = 0;
		items2[id].weight = wg;
		items2[id].type = type;
		items2[id].type2 = type2;
		items2[id].slot = -1;
	}

	items[n] = item_copy( items2[id] );
	items[n].num = parseInt( num );
	items[n].slot = slot;
	
	slots[slot] = n;
	if( slot == 0 ) type_nums[type] += parseInt( num );
	if( slot == 0 && type == 0 ) type2_nums[type2] += parseInt( num );
	
	++ n;
}

function set_descr(id, new_descr)
{
	items2[id].descr = new_descr;
	items[slots[25]].descr = new_descr;
}

function alter_item( id, slot, _num )
{
	for( i = 0; i < n; ++ i ) if( items[i].item_id == id && items[i].slot == slot )
	{
		if( slot == 0 ) type_nums[items[i].type] += parseInt( _num );
		items[i].num += parseInt( _num );
		return;
	}

	items[n] = item_copy( items2[id] );
	items[n].num = parseInt( _num );
	items[n].slot = slot;
	
	slots[slot] = n;
	if( slot == 0 ) type_nums[items[n].type] += parseInt( _num );
	
	++ n;
}

function setfilter( a )
{
	ioffset = 0;
	cur_filter = parseInt( a );
	document.getElementById( 'inv_items' ).innerHTML = get_inv_html( );
	set_inv_events( );
}

function iscrolldown( )
{
	ioffset += 7;
	document.getElementById( 'inv_items' ).innerHTML = get_inv_html( );
	set_inv_events( );
}

function iscrollup( )
{
	if( ioffset ) ioffset -= 7;
	document.getElementById( 'inv_items' ).innerHTML = get_inv_html( );
	set_inv_events( );
}

function save_set( )
{
	var nm = prompt( 'Название комплекта' );
	if( nm ) query( 'items_additional.php?nm='+nm, '' );
}

function wear_set( id )
{
	query( 'items_additional.php?set='+id, '' );
}

function del_set_qry( id )
{
	if( confirm( 'Удалить комплект?' ) ) query( 'items_additional.php?del='+id, '' );
}

function get_used_items( )
{
	var ok;
	var total = 0;
	
	if( cur_filter != -1 && !( cur_filter < 100 && type_nums[cur_filter] ) && !( cur_filter >= 100 && type2_nums[cur_filter - 100] ) ) cur_filter = -1;
	
	ok = 0;
	for( i = 0; i < n; ++ i )
		if( items[i].slot > 0 && items[i].num > 0 && (items[i].slot != 1 && items[i].slot < 14) )
		{
			ok = 1;
			break;
		};
	if( ok )
	{
		wst = '';
		wst += '<table width=100%><tr><td align=center>' + fupper + '<b>Используемые вещи:</b><br>' + flower + "</td></tr><tr><td align=center>" + fupper;
		wst += '<table width=100% cellspacing=0 cellpadding=0>';
		for( slot in type_names ) if( slot != 0 )
		{
			i = slots[slot];
			if( items[i] && items[i].slot == slot && items[i].num > 0 && slot!=1 && slot<14 )
			{
				moo = myf( items[i].weight / 100.0 );
				wst += "<tr><td><a href='javascript:i_do(\"item=" + items[i].item_id + "&w=" + slot + "\")'>" + items[i].name + "</a></td><td align=right>" + moo + "</td></tr>";
			}
		}
		wst += '</table>';
		wst += flower + "</td></tr><tr><td align=center>" + fupper + 'Уровень обмундирования: ' + wear_level + flower;
		wst += '</td></tr></table>';
	}
	else wst = '&nbsp;';
	return wst;
}

function get_uping_items()
{
	var ok;
	var total = 0;
	
	if( cur_filter != -1 && !( cur_filter < 100 && type_nums[cur_filter] ) && !( cur_filter >= 100 && type2_nums[cur_filter - 100] ) ) cur_filter = -1;
	
	ok = 0;
	for( i = 0; i < n; ++ i )
		if( items[i].slot > 0 && items[i].num > 0 && (items[i].slot == 1 || items[i].slot >= 14) )
		{
			ok = 1;
			break;
		};
	if( ok )
	{
		wst = '';
		wst += '<table width=100%><tr><td align=center>' + fupper + '<b>Усиления:</b><br>' + flower + "</td></tr><tr><td align=center>" + fupper;
		wst += '<table width=100% cellspacing=0 cellpadding=0>';
		for( slot in type_names ) if( slot != 0 )
		{
			i = slots[slot];
			if( items[i] && items[i].slot == slot && items[i].num > 0 && (slot==1 || slot>=14) )
			{
				moo = myf( items[i].weight / 100.0 );
				wst += "<tr><td><a href='javascript:i_do(\"item=" + items[i].item_id + "&w=" + slot + "\")'>" + items[i].name + "</a></td><td align=right>" + moo + "</td></tr>";
			}
		}
		wst += '</table>';
		wst += flower + "</td></tr><tr><td align=center>" + fupper + 'Уровень обмундирования: ' + wear_level + flower;
		wst += '</td></tr></table>';
	}
	else wst = '&nbsp;';
	return wst;
}

function insert_into_chat(a, t)
{
	t=1;
	if (t==1)
		a = ' (вещь:'+a+') ';
	parent.chat_in.document.getElementById('inp').value += a;
	parent.chat_in.Cursor( );
}

function get_sets( )
{
	var wst = '<table width=100%><tr><td align=center>' + fupper + '<b>Комплекты вещей:</b><br>' + flower + "</td></tr><tr><td align=center>" + fupper;

	for( var i in sets ) if( sets[i] !== false )
		wst += '<a href="javascript:wear_set(' + i + ')">' + sets[i] + '</a> <img style="cursor:pointer;" onclick="del_set_qry(' + i + ');" width=11 height=11 src=images/e_close.gif title=Удалить><br>';
	wst += '<a href="javascript:wear_set(0)">Снять все</a><br>';
	wst += '<a href="javascript:save_set()">Сохранить</a><br>';

	wst += flower + "</td></tr>";
	wst += '</table>';
	return wst;
}

function get_filters( )
{
	bnum = 0;
	for( q in type2_names ) if( type2_nums[q] ) ++ bnum;
	bnum2 = 0;
	for( q in type_names2 ) if( type_nums[q] || q == -1 ) ++ bnum2;

	var tp = ''; var bm = '';

	if( bnum2 > 2 )
	{
		if( bnum > 1 ) tp += '<table border=0 width=100%><colgroup><col width=50%><col width=50%><tr>';
		else tp += '<table width=100%><tr>';
		tp += '<td valign=top align=center>' + fupper + '<b>Фильтр</b>' + flower + '</td>';
		bm += '<td valign=top>' + fupper;
		for( q in type_names2 ) if( type_nums[q] || q == -1 )
		{
			if( q != cur_filter ) bm += '<a href="javascript:setfilter(' + q + ')" style="cursor: pointer">' + type_names2[q] + '</a><br>';
			else bm += '<b>' + type_names2[q] + '</b><br>';
		}
		bm += flower;
		if( bnum <= 1 ) bm += '</td></tr></table>';
	}
	if( bnum > 1 )
	{
		if( bnum2 > 2 ) tp += '</td><td align=center vAlign=top>';
		else tp += '<table width=100%><tr><td vAlign=top align=center>';

		tp += fupper + '<b>Ресурсы</b>' + flower + '</td>';
		bm += '<td valign=top>' + fupper;
		for( q in type2_names ) if( type2_nums[q] )
		{
			if( 100 + parseInt(q) != cur_filter ) bm += '<a href="javascript:setfilter(' + (100+parseInt(q)) + ')" style="cursor: pointer">' + type2_names[q] + '</a><br>';
			else bm += '<b>' + type2_names[q] + '</b><br>';
		}
		bm += flower;

		if( bnum2 > 2 ) bm += '</td></tr></table>';
		else bm += '</td></tr></table>';
	}
	return tp + '</tr><tr>' + bm;
}

var imode = 0;
function show_sets( )
{
	_( 'ifilters' ).style.display = 'none';
	_( 'ups_items' ).style.display = 'none';
	if( _( 'isets' ).style.display == 'none' )
	{
		_( 'isets' ).style.display = '';
		_( 'used_items' ).style.display = 'none';
		imode = 1;
	}
	else
	{
		_( 'isets' ).style.display = 'none';
		_( 'used_items' ).style.display = '';
		imode = 0;
	}
}

function show_ups( )
{
	_( 'ifilters' ).style.display = 'none';
	_( 'isets' ).style.display = 'none';
	if( _( 'ups_items' ).style.display == 'none' )
	{
		_( 'ups_items' ).style.display = '';
		_( 'used_items' ).style.display = 'none';
		imode = 3;
	}
	else
	{
		_( 'ups_items' ).style.display = 'none';
		_( 'used_items' ).style.display = '';
		imode = 0;
	}
}

function show_filters( )
{
	_( 'isets' ).style.display = 'none';
	_( 'ups_items' ).style.display = 'none';
	if( _( 'ifilters' ).style.display == 'none' )
	{
		_( 'ifilters' ).style.display = '';
		_( 'used_items' ).style.display = 'none';
		imode = 2;
	}
	else
	{
		_( 'ifilters' ).style.display = 'none';
		_( 'used_items' ).style.display = '';
		imode = 0;
	}
}

var ware = 0;

function get_inv_html( )
{
	var wst = get_used_items( );
	var wst1 = get_uping_items();
	var sst = get_sets( );
	var fst = get_filters( );
	var has_more = false;
	var total = 0;

	function get_items( )
    {
    	var st = '';
    	var shown = 0;
    	for( i = ioffset; i < n; ++ i )
    		if( items[i].slot == 0 && items[i].num > 0 && ( cur_filter == -1 || cur_filter < 100 && items[i].type == cur_filter || cur_filter >= 100 && items[i].type == 0 && parseInt( items[i].type2 ) + 100 == cur_filter ) )
    		{
    			if( shown == 35 )
    			{
    				has_more = true;
    				break;
    			}

    			if( shown % 7 == 0 ) st += '<tr>';
    			st += '<td style="width:50px;height:50px;">';
    			var tt = '<a href=#><b>' + items[i].name + '</b></a><br><small><font color=darkgreen><b>Вес: ' + items[i].num + 'x' + myf( items[i].weight / 100.0 ) + '=' + myf( items[i].weight * items[i].num / 100.0 ) + '</b></font></small><br>' + items[i].descr;
    			st += "<div style='overflow:hidden;background:url(images/items/bg.gif);width:50px;height:50px;text-align:center;' id=nv" + i + "><img id=nvimg" + items[i].item_id + " onmouseout='hideTooltip()' onmousemove='showTooltipW( event, \"" + tt + "\", 250 )' width=50 height=50 src=images/items/" + items[i].image + ">"; // onclick='show_item(" + items[i].item_id + ")' ondblclick='use_item(" + items[i].item_id + ")'
    			if( items[i].num > 1 )
    			{
    //				st += '<div style="color:black;position:relative;left:-5px;"><b>' + items[i].num + '</b></div>';
    				st += '<div style="position:relative;left:2px;top:-17px;"><center><table cellspacing=0 cellpadding=0 style="color:white;border:1px solid white;background-color:black;"><tr><td><div style="margin-left:7px;margin-right:7px;"><small><b>' + items[i].num + '</b></small></div></td></tr></table></center></div>';
    			}
    			st += "</div>";
    			total += items[i].weight * items[i].num;
    			st += '</td>';
    			if( shown % 7 == 6 ) st += '</tr>';

                ++ shown;
    		}
    	if( shown <= 28 && ioffset > 0 )
    	{
    		ioffset -= 7;
    		return get_inv_html( );
    	}

    	for( ; shown < 35; ++ shown )
    	{
    		if( shown % 7 == 0 ) st += '<tr>';
    		st += '<td><img width=50 height=50 src=images/items/bg.gif></td>';
    		if( shown % 7 == 6 ) st += '</tr>';
    	}
    	return st;
    }

	if( cur_filter != -1 && !( cur_filter < 100 && type_nums[cur_filter] ) && !( cur_filter >= 100 && type2_nums[cur_filter - 100] ) ) cur_filter = -1;

	var ist = get_items( );

	var st = '<table background=images/invbg.jpg style="width:679px;height:329px;" cellspacing=0 cellpadding=0><tr><td style="width:679px;height:329px;" align=center valign=middle>';
	st += '<table style="width:669px;height:319px;" cellspacing=0 cellpadding=0><colgroup><col width=20><col width=420><col width=209><col width=20><tr><td>&nbsp;</td><td style="margin-top:2px;margin:bottom:12px;" valign=top style="height:319px;">';

	st += '<table width=420><colgroup><col width=50><col width=50><col width=50><col width=50><col width=50><col width=50><col width=50><col width=50><col width=30><tr><td colspan=8>';
	st += '<table width=100% cellspacing=0 cellpadding=0><tr><td><b>Инвентарь' + ( ware ? '</b> - <a href=game.php?warehouse>показать вещи в хранилище</a>' : ':</b>' ) + '</td><td align=right>';
	st += "<img src='images/money.gif' title='Дублоны' alt='Дублоны' width='11' height='11'>&nbsp;<b>"+global_money+"</b>&nbsp;&nbsp;<img src='images/umoney.gif' title='Таланты' alt='Таланты' width='11' height='11'>&nbsp;<b>"+global_umoney+"</b></td></tr></table>";
	st += '</td><td rowspan=6 valign=bottom>';

	st += '<table cellspacing=0 cellpadding=0><tr>';
	if( ioffset > 0 ) st += '<td id=btnup style="background:url(images/but_2.png);width:28px;height:28px;cursor:pointer;" onclick="iscrollup()"><img src=empty.gif style="width:28px;height:28px" onmouseover="_(\'btnup\').style.background=\'url(images/but_l2.png)\'" onmouseout="_(\'btnup\').style.background=\'url(images/but_2.png)\'"></td>';
	else st += '<td style="background:url(images/but_n2.png);width:28px;height:28px;"><img src=empty.gif width=28 height=28></td>';
	st += '</tr><tr>';
	if( has_more ) st += '<td id=btndn style="background:url(images/but_1.png);width:28px;height:28px;cursor:pointer;" onclick="iscrolldown()"><img src=empty.gif style="width:28px;height:28px" onmouseover="_(\'btndn\').style.background=\'url(images/but_l1.png)\'" onmouseout="_(\'btndn\').style.background=\'url(images/but_1.png)\'"></td>';
	else st += '<td style="background:url(images/but_n1.png);width:28px;height:28px;"><img src=empty.gif width=28 height=28></td>';
	st += '</tr></table>';

	st += '</td></tr>';

    st += ist;

	st += '</table>';

	st += '<table cellspacing=0 cellpadding=0 width=420><colgroup><col width=50%><col width=50%><tr><td>';
	moo = "<font color=darkblue><b>" + myf( total / 100.0 ) + "</b></font>";
	st += "&nbsp;<small>Вес показанных вещей: " + moo + "</small>";
	moo = "<font color=darkblue><b>" + myf( total_weight / 100.0 ) + "</b></font>";
	st += "</td><td>&nbsp;<small>Общий вес: <b>" + moo + "</b>";
	moo = "<font color=darkgreen><b>" + myf( can_carry ) + "</b></font>";
	st += ' из ' + moo + '</small>';
	st += '</td>';

	st += '</tr></table>';

	st += '</td><td valign=top><br><table width=100%><colgroup><col width=33%><col width=34%><col width=33%><tr><td>' + fupper + '<a href="javascript:show_filters()">Фильтр</a>' + flower + '</td><td>' + fupper + '<a href="javascript:show_ups()">Усиления</a>' + flower + '</td><td>' + fupper + '<a href="javascript:show_sets()">Комплекты</a>' + flower + '</td></tr></table>';
	st += '<div id=used_items '+((imode==0)?'':'style="display:none"')+'>' + wst + '</div>';
	st += '<div id=ups_items '+((imode==3)?'':'style="display:none"')+'>' + wst1 + '</div>';
	st += '<div id=isets '+((imode==1)?'':'style="display:none"')+'>' + sst + '</div>';
	st += '<div id=ifilters '+((imode==2)?'':'style="display:none"')+'>' + fst + '</div>';
	st += '</div>';
	st += '</td><td>&nbsp;</td></tr></table>';
	st += '</td></tr></table>';

	return st;
}

function set_inv_events( )
{
	var shown = 0;
	var has_more = false;
	for( i = ioffset; i < n && shown < 35; ++ i )
		if( items[i].slot == 0 && items[i].num > 0 && ( cur_filter == -1 || items[i].type == cur_filter ) )
		{
			g( 'nv' + i ).onmousedown = prepare_drag;
			g( 'nv' + i ).onmousemove=begin_drag;
			var tmp1 = function( v ) { return function() { show_item( v ); } }
			var tmp2 = function( v ) { return function() { use_item( v ); } }
			var cur_id = items[i].item_id;
			g( 'nv' + i ).onclick = tmp1( cur_id );
			g( 'nv' + i ).ondblclick = tmp2( cur_id );
			++ shown;
		}
}

function ref_inv( )
{
	document.getElementById( 'inv_items' ).innerHTML = get_inv_html( );
	set_inv_events( );
}

function drop( iid ) { if( confirm( 'Действительно выбросить вещь?' ) ) i_do('item=' + iid + '&w=0&drop'); }
function dropall( iid ) { if( confirm( 'Действительно выбросить все экземпляры данной вещи?' ) ) i_do('item=' + iid + '&w=0&dropall'); }
function hide_item( ) { _( 'item' ).style.display = 'none'; }
function i_do(s) { query( 'inventory.php?' + s,'' ); }

var stmo = 0;
var st_dont_show_info = false;

function show_item( iid )
{
	function doit( )
	{
		if( st_dont_show_info ) return;
    	_( 'item' ).style.display = '';
    	_( 'item' ).innerHTML = rFLUl() + '<br><br><center><i>...</i></center><br><br>' + rFLL();
    	hideTooltip( );
    	i_do("item=" + iid + "&w=0");
	}
	stmo = setTimeout( doit, 500 );
}
function use_item( iid )
{
	st_dont_show_info = true;
	setTimeout( 'st_dont_show_info = false;', 1000 );
	parent.game_ref.location.href = "item_dragdrop.php?item_id=" + iid + "&from=-1&to=100";
}
