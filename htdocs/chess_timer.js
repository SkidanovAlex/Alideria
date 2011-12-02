var ok;

function GetTimeStr( a, b, c, d )
{
	d0 = new Date( );

	a = a + ( b - d0.getTime( ) ) / 1000;
	if( a > 0 ) ok = 1;
	if( a <= 0 )
	{
		if( ok ) eval( d );
		ok = 0;
		a = 0;
	}

	h = Math.round( a / 3600 - 0.5 );
	m = Math.round( ( a / 60 ) % 60 - 0.5 );
	s = Math.round( a % 60 );
	
	if( s == 60 )
	{
		++ m;
		s = 0;
	}

	if( c )
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

function InsertTimer( a, b, c, d, e )
{
	var d0 = new Date( );
	var t0 = d0.getTime( );

	var res = "";

	res += "<div id=moo>";
	res += b + GetTimeStr( a, t0, d, e ) + c;
	res += "</div>";

	res += "<script>";
	res += "oink = " + a + "; ";
	res += "b_oink = '" + b + "'; ";
	res += "c_oink = '" + c + "'; ";
	res += "d_oink = " + d + "; ";
	res += "e_oink = '" + e + "'; ";
	res += "tm = " + t0 + "; ";
	res += "var tmo;";
	res += "function la( ) { var dd = GetTimeStr( oink, tm, d_oink, e_oink ); if( dd != -1 ) { document.getElementById( 'moo' ).innerHTML = b_oink + dd + c_oink; clearTimeout( tmo ); tmo = setTimeout( 'la( );', 1000 ); } }";
	res += "setTimeout( 'la( );', 1000 );";
	res += "</scr" + "ipt>";

	return res;
}

