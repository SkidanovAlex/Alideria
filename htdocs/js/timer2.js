var show_timer_title_2 = false;

function time_str( a, b, c, d )
{
	d0 = new Date( );

	a = a + ( b - d0.getTime( ) ) / 1000;
	a=Math.round(a);
	if( a <= 0 )
	{
		eval( d );

		window.top.document.title = window.top.tstr;

		return -1;
	}

	h = Math.round( a / 3600 - 0.5 );
	m = Math.round( ( a / 60 ) % 60 - 0.5 );
	s = Math.round( a % 60 );
	
	if( s == 60 )
	{
		++ m;
		s = 0;
	}

	if( c || h )
		res = h + ":" + ( ( m < 10 ) ? "0" : "" ) + m + ":" + ( ( s < 10 ) ? "0" : "" ) + s;
	else
		res = m + ":" + ( ( s < 10 ) ? "0" : "" ) + s;
		
	return res;
}

// a - время в секундах
// b - открывающиеся теги перед временем( не должна содержать в себе одинарных кавычек )
// c - закрывающиеся теги перед временем( не должна содержать в себе одинарных кавычек )
// d - 1 если нужен формат h:mm:ss и 0 если m:ss
// e - выполняется при достижении таймером нуля( не должна содержать в себе одинарных кавычек )

var is_first_timer2 = true;
function NewTimer( a, b, c, d, e )
{
	var rnd = is_first_timer2 ? '' : Math.random( );
	is_first_timer2 = false;

	var d0 = new Date( );
	var t0 = d0.getTime( );
	var iv;
	
	function process( )
	{
		var st = time_str( a, t0, d, e );
		if( st == -1 ) clearInterval( iv );
		else
		{
			document.getElementById( 'moor' + rnd ).innerHTML = b + st + c;
			if( show_timer_title_2 == true ) window.top.document.title='['+st+'] ' + window.top.tstr;
		}
	}

	var ret = '';
	ret += "<span id=moor" + rnd + ">";
	ret += b + time_str( a, t0, d, e ) + c;
	ret += "</span>";
	iv = setInterval( process, 1000 );
	return ret;
}
