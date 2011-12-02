var show_timer_title = false;

function GetTimeStr( a, b, c, d )
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

var is_first_timer = true;
function InsertTimer( a, b, c, d, e )
{
	var d0 = new Date( );
	var t0 = d0.getTime( );

	var res = "";

	var rnd;
	if( is_first_timer ) rnd = "";
	else rnd = parseInt( Math.random( ) * 10000 );
	is_first_timer = false;

	res += "<div id=moot" + rnd + ">";
	res += b + GetTimeStr( a, t0, d, e ) + c;
	res += "</div>";

	res += "<script>";
	res += "var tmo" + rnd + ";";
	res += "var tm" + rnd + " = " + t0 + "; ";
	res += "var oink" + rnd + " = " + a + "; ";
	res += "var b_oink" + rnd + " = '" + b + "'; ";
	res += "function la" + rnd + "( ) { ";
	res += "var c_oink = '" + c + "'; ";
	res += "var d_oink = " + d + "; ";
	res += "var e_oink = '" + e + "'; ";
	res += "var dd = GetTimeStr( oink" + rnd + ", tm" + rnd + ", d_oink, e_oink ); if( dd != -1 ) { document.getElementById( 'moot" + rnd + "' ).innerHTML = b_oink" + rnd + " + dd + c_oink; " + ( ( rnd == '' ) ? "if( show_timer_title == true ) window.top.document.title='['+dd+'] ' + window.top.tstr;" : "" ) + "clearTimeout( tmo" + rnd + " ); tmo" + rnd + " = setTimeout( 'la" + rnd + "( );', 1000 ); } }";
	res += "setTimeout( 'la" + rnd + "( );', 1000 );";
	res += "</scr" + "ipt>";

	return res;
}

function StopUpdatingTitle( )
{
	window.top.document.title = window.top.tstr;
	show_timer_title = false;
}

function PingTimer( )
{
	la( );
}
