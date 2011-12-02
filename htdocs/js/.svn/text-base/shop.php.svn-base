
var shop_regime;
var shop_filter = -1;

var items = new Array( );
var types = new Array( );
var ptypes = new Array( );
var shop_id = 0;
var n = 0;

<?

include( "../arrays.php" );

foreach( $item_types as $a=>$b ) print( "ptypes[$a] = '$b';\n" );

?>

types[-1] = "Все вещи";

function toInt( a )
{
	a = parseInt( a );
	if( isNaN( a ) ) a = 0;
	return a;
}

function get_money_str( a )
{
	if( toInt( a / 10 ) % 10 == 1 ) return 'монет';
	if( a % 10 == 1 ) return "монета";
	if( a % 10 == 2 || a % 10 == 3 || a % 10 == 4 ) return 'монеты';
	return "монет";
}

function shop_setRegime( a ) {
	shop_regime = a;
}

function shop_addItem( _id, _tp, _num, _mnum, _sp, _bp, _nm, _img, _descr, _pos ) {
	items[n] = new Object;

	items[n].item_id = _id;
	items[n].tp = _tp;
	items[n].num = _num;
	items[n].mnum = _mnum;
	items[n].sp = _sp;
	items[n].bp = _bp;
	items[n].nm = _nm;
	items[n].img = _img;
	items[n].descr = _descr;
	items[n].pos = _pos;
	
	types[_tp] = ptypes[_tp];
	
	++ n;
}

function shop_alterItem( _id, _num )
{
	for( i = 0; i < n; ++ i ) if( items[i].item_id == _id ) { items[i].num += _num; items[i].mnum -= _num; }
	document.getElementById( 'sh' ).innerHTML = shop_getInnerHtml( );
}

function bbc( a )
{
	val = toInt( document.getElementById( 'buy' + a ).value );
	if( val >= 0 ) val *= items[a].sp;
	else val *= items[a].bp;
	val = toInt( val );
	document.getElementById( 'bb' + a ).innerHTML = ' = ' + val + ' ' + get_money_str( val );
}

function ssc( a )
{
	val = toInt( document.getElementById( 'sell' + a ).value );
	if( val >= 0 ) val *= items[a].bp;
	else val *= items[a].sp;
	val = toInt( val );
	document.getElementById( 'ss' + a ).innerHTML = ' = ' + val + ' ' + get_money_str( val );
}

function buy( a )
{
	val = toInt( document.getElementById( 'buy' + a ).value );
	shop_ref.location.href = 'shop_buy_sell.php?shop_id=' + shop_id + '&item_id=' + items[a].item_id + '&number=' + val;
}

function sell( a )
{
	val = - toInt( document.getElementById( 'sell' + a ).value );
	shop_ref.location.href = 'shop_buy_sell.php?shop_id=' + shop_id + '&item_id=' + items[a].item_id + '&number=' + val;
}

function int_to_strK( a )
{
	var ret = '';
	while( a >= 10000 )
	{
		a = Math.floor( a / 1000 );
		ret += 'K';
	}
	return a + ret;
}

function shop_getInnerHtml( )
{
	var st = '<table width=100% border=0>';
	var ok = false;
	for( i = 0; i < n; ++ i )
	{
		if( shop_filter != -1 && shop_filter != items[i].tp ) continue;
		if( items[i].num == 0 ) continue;
		ok = true;
		st += '<tr>';
		st += '<td width=55 align=center valign=top><table width=50 height=50 cellspacing=0 cellpadding=0 border=0><tr><td background=images/items/bg.gif align=center vAlign=center><img src=images/items/' + items[i].img + '></td></tr></table><small>' + (items[i].mnum?('<font color=green>'+int_to_strK(items[i].mnum) + '</font> / '):'') + int_to_strK(items[i].num) + '</small></td>';
		st += '<td valign=top>' + items[i].descr + '</td>';
		if( shop_regime != 2 )
		{
			st += "<td width=80 valign=top><b>Купить:</b><br>";
			st += "<table border=0 cellspacing=0 cellpadding=0><tr><td><input type=text class=btn40 style='background-color:white' value=1 onKeyUp=bbc(" + i + ") name=buy" + i + " id=buy" + i + "></td><td><button onClick=buy(" + i + ") class=sss_btn>>>></button></td></tr></table>";
			st += " x " + items[i].sp + " " + get_money_str( items[i].sp ) + "<br>";
			st += "<div id=bb" + i + "> = " + toInt( items[i].sp ) + " " + get_money_str( items[i].sp ) + "</div>";
			st += "</td>";
		}
		if( shop_regime != 1 )
		{
			st += "<td width=80 valign=top><b>Продать:</b><br>";
			st += "<table border=0 cellspacing=0 cellpadding=0><tr><td><input type=text class=btn40 style='background-color:white' value=1 onKeyUp=ssc(" + i + ") name=sell" + i + " id=sell" + i + "></td><td><button onClick=sell(" + i + ") class=sss_btn>>>></button></td></tr></table>";
			st += " x " + items[i].bp + " " + get_money_str( items[i].bp ) + "<br>";
			st += "<div id=ss" + i + "> = " + toInt( items[i].bp ) + " " + get_money_str( items[i].bp ) + "</div>";
			st += "</td>";
		}
		st += '</tr>';
	}
	if( ok == false )
		st += '<tr><td><i>Пусто</i></td></tr>';
	st += '</table>';
	
	return st;
}

function shop_changeFilter( a )
{
	shop_filter = a;
	
	document.getElementById( 'sh' ).innerHTML = shop_getInnerHtml( );
	document.getElementById( 'fl' ).innerHTML = shop_getFilterHtml( );
}

function shop_getFilterHtml( )
{
	var st = '<table border=1 bordercolor=silver><tr><td align=center><b>Фильтр</b></td></tr><tr><td>';
	
	for( t in types )
	{
		if( t == shop_filter )
			st += '<b>' + types[t] + '</b><br>';
		else
			st += '<a href="javascript: void(0);" onclick="shop_changeFilter( ' + t + ' )">' + types[t] + '</a><br>';
	}
	
	st += '</td></tr></table>';
	
	return st;
}

function shop_showHtml( )
{
	document.write( '<table width=100%><tr><td width=450 valign=top><div id=sh name=sh>' );
	document.write( shop_getInnerHtml( ) );
	document.write( '</div></td><td valign=top><div id=fl name=fl>' );
	document.write( shop_getFilterHtml( ) );
	document.write( "</div></td></tr></table>" );
}
