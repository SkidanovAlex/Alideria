var cur_card = -1;
var q = new Array( );
var shown = new Array( );
var qn = 0;
var pg = 0;
var cur_turn = 0;
var card_offset = 0;
var card_left = 0;
var card_num = 0;
var ref_que = new Array( );
var ownSpell, targetSpell;

// intervals
var spell_roll_iv = 0;
var move_crs_iv = 0;

function rpng( src, s, id )
{
	if( document.all )
		return( "<div id=" + id + " style='" + s + "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader( src=\"" + src + "\", sizingMethod=\"scale\");'>&nbsp;</div>" );
	else
		return( "<img id=" + id + " src=" + src + " style=" + s + ">" );
}

function opng( src, w, h )
{
	if( document.all )
		document.write( "<div style='width:" + w + "px; height:" + h + "px;filter:progid:DXImageTransform.Microsoft.AlphaImageLoader( src=\"" + src + "\", sizingMethod=\"scale\");'></div>" );
	else
		document.write( "<img src=" + src + " width=" + w + " height=" + h + ">" );
}

function showLog( )
{
	if( _( 'logc' ).style.display == 'none' )
	{
		_( 'spellArea1' ).style.display = 'none';
		_( 'spellArea2' ).style.display = 'none';
		_( 'elGroup' ).style.display = 'none';
		_( 'settings' ).style.display = 'none';
		_( 'logc' ).style.display = '';
		
	}
	else
	{
		_( 'logc' ).style.display = 'none';
		_( 'spellArea1' ).style.display = '';
		_( 'spellArea2' ).style.display = '';
		_( 'elGroup' ).style.display = '';
	}
}

function showSettings( )
{
	if( _( 'settings' ).style.display == 'none' )
	{
		_( 'logc' ).style.display = 'none';
		_( 'spellArea1' ).style.display = '';
		_( 'spellArea2' ).style.display = '';
		_( 'elGroup' ).style.display = '';
		_( 'settings' ).style.display = '';
		
	}
	else
		_( 'settings' ).style.display = 'none';
}

function ref_plrs( )
{
	query( 'combat_load_plrs.php', '' );
}

function draw_spells( )
{
	for( var i = 0; i < spells_n; ++ i )
	{
		var dv = 'crds' + spell_ids[i];
		_( dv ).style.left = spell_xs[i];
		_( dv ).style.top = spell_ys[i];
	}
}

function select_card( a )
{
	select_card_ref( a );
	query( 'combat_ready.php?id=' + a + '&turn=' + cur_turn, 'rdy' );
}

var card_chg_que = new Array( );
function select_card_ref( a )
{
	if( ownSpell == a )
		return;
	var already = card_chg_que.length;
	if( cur_card == a )
		return;
	if( cur_card != -1 )
		card_chg_que.push( [cur_card, -1] );
	if( a != -1 )
		card_chg_que.push( [a, 1] );
	cur_card = a;
	if( already )
		return;
	function start_anim( )
	{
		var acard = card_chg_que[0][0];
		var adir = card_chg_que[0][1];
		for( var i = 1; i < card_chg_que.length; ++ i )
			card_chg_que[i - 1] = card_chg_que[i];
		card_chg_que.pop( );
		if( document.all )
			_( 'my_spell' ).innerHTML = '<div style="width:141px;height:141px;position:relative;left:0px;top:0px; filter:progid:DXImageTransform.Microsoft.AlphaImageLoader( src=\'images/spells/' + spell_img[acard] + '\', sizingMethod=\'scale\');"></div>';
		else
			_( 'my_spell' ).innerHTML = '<img style="width:141px;height:141px;border:0px;" src="images/spells/' + spell_img[acard] + '" />';
		var step = 0;
		var set_card_iv = 0;
		function anim( )
		{
			if( !Animation || step > 99 )
			{
				step = 100;
				clearInterval( set_card_iv );
			}
			if( adir == -1 )
				blur_opa( _('my_spell'), 1 - step / 100 );
			else
				blur_opa( _('my_spell'), step / 100 );

			if( step == 100 && card_chg_que.length )
				start_anim( );
			else
				step += 20;
		}
		set_card_iv = setInterval( anim, 50 );	
	}
	if( card_chg_que.length )
		start_anim( );
}

function select_target( a )
{
	query( 'combat_select_target.php?id=' + a, 'trg' );
}

function qref( )
{
	query( 'combat_ref.php', 'ref' );
}

function select_target_ref( a )
{
	if( a == targetSpell )
		return;
	targetSpell = a;
	var start1 = parseInt( _( 'crtl' ).style.top );
	var start2 = parseInt( _( 'crtr' ).style.top );
	var end = a * 29 + 12;
	var step = 0;
	if( move_crs_iv )
		clearInterval( move_crs_iv );
	function anim( )
	{
		if( !Animation || step > 159 )
		{
			step = 160;
			clearInterval( move_crs_iv );
		}
		var t1 = start1 + ( end - start1 ) * Math.sin( step / 120 * 1.5707963267948966192313216916398 );
		var t2 = start2 + ( end - start2 ) * Math.sin( ( step - 40 ) / 120 * 1.5707963267948966192313216916398 );
		if( step > 120 )
			t1 = end;
		if( step < 40 )
			t2 = start2;
		_( 'crtl' ).style.top = t1 + 'px';
		_( 'crtr' ).style.top = t2 + 'px';
		step += 10;
	}
	move_crs_iv = setInterval( anim, 50 );
}

var lid = -1;
function addtolog( id, a )
{
	if( id > lid )
	{
		a = c_log( a );
		var ok = 0;
		var t1 = a.indexOf( '<br>' );
		if( t1 == -1 )
			ok = 1;
		else
		{
			t2 = a.indexOf( '<br>', t1 + 1 );
			if( t2 == -1 )
				ok = 1;
		}
		if( ok && qn > 0 )
		{                    
			if( document.getElementById( "logdiv" + ( qn - 1 ) ) != undefined )
				q[qn - 1] = document.getElementById( "logdiv" + ( qn - 1 ) ).innerHTML;
			q[qn - 1] = a + q[qn - 1];
			if( document.getElementById( "logdiv" + ( qn - 1 ) ) != undefined )
				document.getElementById( "logdiv" + ( qn - 1 ) ).innerHTML = q[qn - 1];
		}
		else
			q[qn ++] = a;
		lid = id;
	}
}

function reflog( )
{
	st = '';
	
	for( a in shown )
		q[shown[a]] = document.getElementById( "logdiv" + shown[a] ).innerHTML;
	
	pgn = ( qn + 4 ) / 5;
	pgn = parseInt( pgn );
	if( pgn > 1 )
	{
		st += 'Страница: ';
		for( i = 0; i < pgn; ++ i )
			if( i == pg )
				st += "<b>" + ( i + 1 ) + '</b> ';
			else
				st += "<a href='#' onClick='gotopage( " + i + " )'>" + ( i + 1 ) + '</a> ';
			
		st += '<br>';
	}
	
	shown = new Array( );
	
	for( i = qn - pg * 5 - 1; i >= qn - pg * 5 - 5 && i >= 0; -- i )
	{
		st += "<div id=logdiv" + i + ">" + q[i] + "</div>";
		shown[i] = i;
	}

	st += '<li><a href="javascript:window.top.createPrivateRoom( \'Бой - Все\' )">Открыть боевой чат со всеми</a><br>';
	st += '<li><a href="javascript:window.top.createPrivateRoom( \'Бой - Свои\' )">Открыть боевой чат с союзниками</a><br>';

	_( 'log' ).innerHTML = st;
}

function reset_creatures( )
{
	for( i = 1; i <= 3; ++ i )
	{
		_( 'mycreat' + i ).innerHTML = '&nbsp';
		_( 'hiscreat' + i ).innerHTML = '&nbsp';
	}
}

function gotopage( a )
{
	pg = a;
	reflog( );
}

function ref_timer_cast( )
{
	query( 'combat_ref.php', 'tmr' );
	setTimeout( 'ref_timer_cast( )', 10000 );
}

function tstr( a )
{
	return " (<a href='javascript:take(" + a + ")'><u>Забрать</u></a>)";
}

function take( a )
{
	query( 'combat_take.php?a='+a,'' );
}

var roll_que = new Array( );
function roll_cards( a )
{
	if( spell_roll_iv )
	{
		roll_que.push( a );
		return;
	}

	var nspl;
	var ospl;
	var step;
	var adir = a;
	card_offset += a;
	if( a == -1 )
	{
		if( card_offset < 0 )
			card_offset += spells_n;
		nspl = card_offset;
		ospl = ( card_offset + card_num ) % spells_n;
	}
	if( a == 1 )
	{
		if( card_offset >= spells_n )
			card_offset -= spells_n;
		nspl = ( card_offset + card_num - 1 ) % spells_n;
		ospl = ( card_offset - 1 + spells_n ) % spells_n;
	}
	if( Animation )
		step = 141;
	else
		step = 1;
	_( 'crds' + spell_ids[nspl] ).style.display = '';
	blur_opa( _( 'crds' + spell_ids[nspl] ), 0 );
	function anim( )
	{
		if( step < 0 )
		{
			step = 0;
			_( 'crds' + spell_ids[ospl] ).style.display = 'none';
			clearInterval( spell_roll_iv );
			spell_roll_iv = 0;
		}
		var mn = 0;
		var mx = card_num;
		if( adir == 1 )
		{
			mn = -1;
			mx = card_num - 1;
		}
		for( var i = mn; i <= mx; ++ i )
		{
			var id = ( card_offset + i + spells_n ) % spells_n;
			_( 'crds' + spell_ids[id] ).style.left = ( card_left + 141 * i + adir * step ) + 'px';
		}
		blur_opa( _( 'crds' + spell_ids[ospl] ), Math.max( 2 * step / 141 - 1, 0 ) );
		blur_opa( _( 'crds' + spell_ids[nspl] ), Math.max( 0, 1 - 2 * step / 141 ) );
		if( step == 0 )
		{
			if( roll_que.length )
			{
				roll_cards( roll_que[0] );
				for( var i = 1; i < roll_que.length; ++ i )
					roll_que[i - 1] = roll_que[i];
				roll_que.pop( );
			}
		}
		else
			step -= 25;
	}
	spell_roll_iv = setInterval( anim, 50 );
}

function blur_coord( obj, sx, sy, ex, ey, step, mstep )
{
	if( step > mstep )
		step = mstep;
	if( step < 0 )
		step = 0;
	var x = parseInt( sx + ( ex - sx ) * step / mstep );
	var y = parseInt( sy + ( ey - sy ) * step / mstep );

	obj.style.left = x + 'px';
	obj.style.top = y + 'px';

	if( mstep == 200 ) // ..
	{
		var val = 200 - y;
		if( val > 141 )
			val = 141;
		if( val < 0 )
			val = 0;
		obj.style.clip = "rect(0px, 141px, " + val + "px, 0px)";
	}
}

function blur_opa( obj, opa )
{
	obj.style.opacity = opa;
	if( opa < 1 )
		obj.style.filter = 'alpha(opacity=' + parseInt( opa * 100 ) + ')';
	else
		obj.style.filter = '';
}

function blur_step( step, mstep, kind )
{
	step -= kind;
	if( step < mstep )
		return step;
	if( mstep + 60 < step )
		return mstep;
	if( mstep + 40 < step )
	{
		var q = ( step - mstep ) % ( 10 * 2 );
		if( q < 10 )
			return mstep - q / 4;
		return mstep - 2 + q / 4 - 2;
	}
	if( mstep + 20 < step )
	{
		var q = ( step - mstep ) % ( 10 * 2 );
		if( q < 10 )
			return mstep - q / 2;
		return mstep - 5 + q / 2 - 5;
	}
	var q = ( step - mstep ) % ( 10 * 2 );
	if( q < 10 )
		return mstep - q;
	return mstep - 10 + q - 10;
}

function blur_process( obj, b1, b2, b3, b4, b5, sx, sy, ex, ey, step, mstep )
{
	blur_coord( obj, sx, sy, ex, ey, blur_step( step, mstep, 0 ), mstep );
	blur_coord( b1, sx, sy, ex, ey, blur_step( step, mstep, 10 ), mstep );
	blur_coord( b2, sx, sy, ex, ey, blur_step( step, mstep, 20 ), mstep );
	blur_coord( b3, sx, sy, ex, ey, blur_step( step, mstep, 30 ), mstep );
	blur_coord( b4, sx, sy, ex, ey, blur_step( step, mstep, 40 ), mstep );
	blur_coord( b5, sx, sy, ex, ey, blur_step( step, mstep, 50 ), mstep );
}

function do_design( )
{
	if( spell_roll_iv )
	{
		clearInterval( spell_roll_iv );
		spell_roll_iv = 0;
	}

	var w = screen_width( );
	var h = screen_height( );
	_( 'crs' ).style.top = 160 + 'px';
	_( 'crs' ).style.left = ( w - _( 'crs' ).offsetWidth ) / 2 + 'px';
	_( 'last_turn' ).style.top = 40 + 'px';
	_( 'last_turn' ).style.left = ( w - 350 ) / 2 + 'px';
	_( 'leave' ).style.top = 170 + 'px';
	_( 'leave' ).style.left = ( w - 315 ) / 2 + 'px';
	_( 'leave' ).style.width = 300 + 'px';
	_( 'tmodv' ).style.top = 145 + 'px';
	_( 'tmodv' ).style.left = ( w - _( 'tmodv' ).offsetWidth ) / 2 + 'px';
	_( 'txttmo' ).style.top = 50 + 'px';
	_( 'txttmo' ).style.left = ( w - 400 ) / 2 + 'px';
	_( 'logc' ).style.width = ( w - 350 ) + 'px';
	_( 'logc' ).style.height = ( h - 70 ) + 'px';
	_( 'logc' ).style.marginLeft = -Math.round( ( w - 350 ) / 2 ) - 5 + 'px';
	_( 'log' ).style.width = ( w - 350 ) + 'px';
	_( 'log' ).style.height = ( h - 80 ) + 'px';
	_( 'items' ).style.left = ( w + _( 'crs' ).offsetWidth ) / 2 + 'px';
	_( 'items' ).style.width = ( w - _( 'crs' ).offsetWidth ) / 2 - 160 + 'px';
	_( 'items' ).style.top = 200 + 'px';
	_( 'lttrash' ).style.top = '170px';
	_( 'rttrash' ).style.top = '170px';
	_( 'lbtrash' ).style.top = '197px';
	_( 'rbtrash' ).style.top = '197px';
	_( 'lttrash' ).style.left = ( w / 2 - 205 - 10 ) + 'px';
	_( 'rttrash' ).style.left = ( w / 2 - 10 ) + 'px';
	_( 'lbtrash' ).style.left = ( w / 2 - 205 - 10 ) + 'px';
	_( 'rbtrash' ).style.left = ( w / 2 - 10 ) + 'px';

	// cards
	if( parseInt( ( w - 270 ) / 141 ) >= spells_n )
	{
		card_num = spells_n;
		card_left = ( w - 141 * spells_n ) / 2;
		card_offset = 0;
		_( 'crdsl' ).style.display = 'none';
		_( 'crdsr' ).style.display = 'none';
	}
	else
	{
		card_num = Math.max( 1, parseInt( ( w - 270 - 130 ) / 141 ) );
		card_left = ( w - 141 * card_num ) / 2;
		_( 'crdsl' ).style.display = '';
		_( 'crdsr' ).style.display = '';
		_( 'crdsl' ).style.top = '280px';
		_( 'crdsr' ).style.top = '280px';
		_( 'crdsl' ).style.left = ( card_left - 65 ) + 'px';
		_( 'crdsr' ).style.left = ( card_left + 141 * card_num - 5 ) + 'px';
	}
	for( var i = 0; i < card_num; ++ i )
	{
		var id = ( i + card_offset ) % spells_n;
		_( 'crds' + spell_ids[id] ).style.left = ( card_left + i * 141 ) + 'px';
		_( 'crds' + spell_ids[id] ).style.top = '240px';
		_( 'crds' + spell_ids[id] ).style.display = '';
		blur_opa( _( 'crds' + spell_ids[id] ), 1 );
	}
	for( var i = card_num; i < spells_n; ++ i )
	{
		var id = ( i + card_offset ) % spells_n;
		_( 'crds' + spell_ids[id] ).style.top = '240px';
		_( 'crds' + spell_ids[id] ).style.display = 'none';
	}
	roll_que = new Array( );
}

function show_turn_details( s )
{
	_( 'last_turn' ).style.display = '';
	_( 'last_turn_inner' ).innerHTML = s;
}

function load_opp( login, ava )
{
	_( 'his_login' ).innerHTML = login;
	_( 'his_avatar' ).src = 'images/avatars/' + ava;
}

function health( a, m, who )
{
	var obj;
	if( who == 0 )
		obj = _( 'my_hp' );
	else
		obj = _( 'his_hp' );
	obj.innerHTML = '<b><font color=white>' + a + '/' + m + '</font></b>';
	if( who == 0 )
		obj = _( 'my_health' );
	else
		obj = _( 'his_health' );
	a = m - a;
	a = parseInt( 225 * a / m );
	if( a > 225 )
		a = 225;
	if( a < 0 )
		a = 0;
	obj.style.height = a + 'px';
	obj.style.top = ( 225 - a ) + 'px';
	if( who == 0 )
	{
		_( 'my_side_o' ).style.left = '0px';
		_( 'my_side_o' ).style.top = '0px';
		_( 'my_space' ).style.display = '';
	}
	else
	{
		_( 'his_side_o' ).style.left = '0px';
		_( 'his_side_o' ).style.top = '0px';
		_( 'his_space' ).style.display = '';
	}
}

function hideava( a )
{
	if( a == 0 )
	{
		_( 'my_side_o' ).style.left = '-10px';
		_( 'my_side_o' ).style.top = '-229px';
		_( 'my_space' ).style.display = 'none';
	}
	else
	{
		_( 'his_side_o' ).style.left = '10px';
		_( 'his_side_o' ).style.top = '-229px';
		_( 'his_space' ).style.display = 'none';
	}
}

function opp_rdy( a )
{
	if( a )
	{
		if( document.all )
			_( 'his_spell' ).innerHTML = '<div style="width:141px;height:141px;position:relative;left:0px;top:0px; filter:progid:DXImageTransform.Microsoft.AlphaImageLoader( src=\'images/rect/qu.png\', sizingMethod=\'scale\');"></div>';
		else
			_( 'his_spell' ).innerHTML = '<img width=141 height=141 border=0 src=images/rect/qu.png>';
	}
	else
		_( 'his_spell' ).innerHTML = '';
}

function tmo_or_bins( a )
{
	var step = 0;
	var aa = a;
	var iv = 0;
	function anim( )
	{
		if( step > 100 )
		{
			step = 100;
			clearInterval( iv );
			if( !aa )
			{
				for( var i = 1; i < ref_que.length; ++ i )
					ref_que[i - 1] = ref_que[i];
				ref_que.pop( );
				if( Animation )
				{
					_( 'lttrash' ).style.display = 'none';
					_( 'lbtrash' ).style.display = 'none';
					_( 'rttrash' ).style.display = 'none';
					_( 'rbtrash' ).style.display = 'none';
				}
			}
		}

		var a1 = step / 100; var a2 = a1;
		if( !aa )
			a2 = 1 - a2;
		else
			a1 = 1 - a1;
		blur_opa( _( 'last_turn' ), a1 );
		blur_opa( _( 'crs' ), a1 );
		blur_opa( _( 'lttrash' ), a2 );
		blur_opa( _( 'lbtrash' ), a2 );
		blur_opa( _( 'rttrash' ), a2 );
		blur_opa( _( 'rbtrash' ), a2 );
		step += 10;
	}
	if( Animation )
		iv = setInterval( anim, 50 );
	else if( !aa )
	{
		for( var i = 1; i < ref_que.length; ++ i )
			ref_que[i - 1] = ref_que[i];
		ref_que.pop( );
	}
}

var rsp1 = 0, rsp2 = 0;
var ww1 = 0, ww2 = 0;

function set_spells( a, b, c, d )
{
	if( !Animation )
		return;
	_( 'tmodv' ).style.display = 'none';
	rsp1 = a; rsp2 = b;
	ww1 = c; ww2 = d;
	var st = '';

	for( var i = 7; i >= 2; -- i )
		st += "<span style='display:none;position:absolute;left:0px;top:0px;' id=ss" + i + ">" + rpng( 'images/spells/' + a, 'width:141px;height:141px;', 's' + i ) + "</span>";
	for( var i = 7; i >= 2; -- i )
		st += "<span style='display:none;position:absolute;left:0px;top:0px;' id=vv" + i + ">" + rpng( 'images/spells/' + b, 'width:141px;height:141px;', 'v' + i ) + "</span>";
	_( 'sv_container' ).innerHTML = st;

	blur_opa( _( 'ss3' ), 0.6 );
	blur_opa( _( 'ss4' ), 0.4 );
	blur_opa( _( 'ss5' ), 0.3 );
	blur_opa( _( 'ss6' ), 0.2 );
	blur_opa( _( 'ss7' ), 0.1 );

	blur_opa( _( 'vv3' ), 0.6 );
	blur_opa( _( 'vv4' ), 0.4 );
	blur_opa( _( 'vv5' ), 0.3 );
	blur_opa( _( 'vv6' ), 0.2 );
	blur_opa( _( 'vv7' ), 0.1 );
}

function render_turn( )
{
	ownSpell = -1001;
	targetSpell = -1001;
	tmo_or_bins( 1 );
	if( Animation )
	{
		_( 'lttrash' ).style.display = '';
		_( 'lbtrash' ).style.display = '';
		_( 'rttrash' ).style.display = '';
		_( 'rbtrash' ).style.display = '';
	}
	var step = 0;
	var mstep = 300;
	var w = screen_width( );
	var h = screen_height( );
	var c1 = getAp( _( 'my_spell' ) );
	var c2 = getAp( _( 'his_spell' ) );
	var sx1 = 160; var sy1 = 39;
	var ex1 = w / 2 - 141 + 10; var ey1 = 25;
	var sx2 = w - 301; var sy2 = 39;
	var ex2 = w / 2 - 10; var ey2 = 25;
	var zx1 = ex1 - 90;
	var zx2 = ex2 + 90;
	var zy = ey1 + 200;
	var iv = 0;
	function moo1( )
	{
		if( Animation && ( step < mstep + 100 ) )
		{
			if( rsp1 != -1 )
				blur_process( _( 'ss2' ), _( 'ss3' ), _( 'ss4' ), _( 'ss5' ), _( 'ss6' ), _( 'ss7' ), sx1, sy1, ex1, ey1, step, mstep );
			if( rsp2 != -1 )
				blur_process( _( 'vv2' ), _( 'vv3' ), _( 'vv4' ), _( 'vv5' ), _( 'vv6' ), _( 'vv7' ), sx2, sy2, ex2, ey2, step, mstep );
			if( step == 0 )
				for( var i = 2; i <= 7; ++ i )
				{
					if( rsp1 != -1 )
						_( 'ss' + i ).style.display = '';
					if( rsp2 != -1 )
						_( 'vv' + i ).style.display = '';
				}
			step += 10;
		}
		else if( Animation && ( step < 600 || step < 700 && ( ww1 || ww2 ) ) )
		{
			if( rsp1 != -1 && !ww1 && step < 600 )
				blur_process( _( 'ss2' ), _( 'ss3' ), _( 'ss4' ), _( 'ss5' ), _( 'ss6' ), _( 'ss7' ), ex1, ey1, zx1, zy, step - 400, 200 );
			if( rsp2 != -1 && !ww2 && step < 600 )
				blur_process( _( 'vv2' ), _( 'vv3' ), _( 'vv4' ), _( 'vv5' ), _( 'vv6' ), _( 'vv7' ), ex2, ey2, zx2, zy, step - 400, 200 );
			if( rsp1 != -1 && ww1 && step >= 500 )
				blur_process( _( 'ss2' ), _( 'ss3' ), _( 'ss4' ), _( 'ss5' ), _( 'ss6' ), _( 'ss7' ), ex1, ey1, zx1, zy, step - 500, 200 );
			if( rsp2 != -1 && ww2 && step >= 500 )
				blur_process( _( 'vv2' ), _( 'vv3' ), _( 'vv4' ), _( 'vv5' ), _( 'vv6' ), _( 'vv7' ), ex2, ey2, zx2, zy, step - 500, 200 );
			step += 5;
		}
		else
		{	
			tmo_or_bins( 0 );
			clearInterval( iv );
			_( 'sv_container' ).innerHTML = '';
			_( 'tmodv' ).style.display = '';

		}
	}
	iv = setInterval( moo1, 40 );
}

function ref_proccess( )
{
	if( ref_que.length == 0 )
		return;
	
	var q = ref_que[0];
	if( q[0] == 2 )
		return; // animation is in progress now
	else if( q[0] == 0 ) 
	{
		for( var i = 1; i < ref_que.length; ++ i )
			ref_que[i - 1] = ref_que[i];
		ref_que.pop( );
	}
	if( q[0] == 0 )
		eval( q[1] );
	else
	{
		set_spells( q[1], q[2], q[3], q[4] );
		ref_que[0] = [2];
		render_turn( );
	}
}

function wheel( event )
{
	if( card_num == spells_n || roll_que.length )
		return;
	
	var wheelDelta = 0;
	var step = 300;
	
	if( !event ) 
		event = window.event;
	if( event.wheelDelta ) 
		wheelDelta = event.wheelDelta / 120;
	else if ( event.detail ) 
		wheelDelta = -event.detail / 3;
		
	if( wheelDelta < 0 )
		roll_cards( 1 );
	else
		roll_cards( -1 );
}

var cccs = [];
function ccce( id )
{
	cccs[id] = - cccs[id];
	if( cccs[id] == 1 )
	{
		_( 'ccci' + id ).src = 'images/e_minus.gif';
		_( 'ccct' + id ).style.display = '';
	}
	else
	{
		_( 'ccci' + id ).src = 'images/e_plus.gif';
		_( 'ccct' + id ).style.display = 'none';
	}
}
function ccc( id, nick, lv, hp, mhp, wp, wm, wma, wa, wd, np, nm, nma, na, nd, fp, fm, fma, fa, fd, luck, resist, crit, regen, clan, rdy, show_nick, mode)
{
	tt = '<small><b><a target=_blank href=player_info.php?id=' + id + '>' + nick + '</a></b><br>[' + hp + '/' + mhp + ']</small>';
	st = '<div style="margin-bottom:5px;width:135px;position:relative;left:0px;top:0px;">';
	st += rFUlt();
	if( show_nick )
		st += '<center>' + tt + '</center>';

	if( !show_nick )
		cccs[id] = 1;
	else
		if( !cccs[id] )
			cccs[id] = mode;
	var src = 'images/e_minus.gif';
	if( cccs[id] == -1 )
		src = 'images/e_plus.gif';

	st += '<table ' + ( ( cccs[id] == -1 ) ? 'style="display:none;"' : '' ) + ' id=ccct' + id + ' width=125><colgroup><col width=25><col width=25><col width=25><col width=25><col width=25>';
	st += '<tr>';
	st += '<td align=center><img src=images/icons/attributes/w_ic1.gif width=20 height=20><br><font color=darkblue><small><b>' + wp + '</b></small></font></td>';
	st += '<td align=center><img src=images/icons/attributes/wm.gif width=20 height=20><br><font color=darkblue><small><b>' + wm + '</b></small></font></td>';
	st += '<td align=center><img src=images/icons/attributes/wa.gif width=20 height=20><br><font color=darkblue><small><b>' + wa + '</b></small></font></td>';
	st += '<td align=center><img src=images/icons/attributes/wd.gif width=20 height=20><br><font color=darkblue><small><b>' + wd + '</b></small></font></td>';
	st += '<td style="border-left:1px solid #F0D3B0" align=center><img src=images/icons/attributes/luck.gif width=20 height=20><br><font color=black><small><b>' + luck + '</b></small></font></td>';
	st += '</tr>';

	st += '<tr>';
	st += '<td align=center><img src=images/icons/attributes/e_ic1.gif width=20 height=20><br><font color=darkgreen><small><b>' + np + '</b></small></font></td>';
	st += '<td align=center><img src=images/icons/attributes/nm.gif width=20 height=20><br><font color=darkgreen><small><b>' + nm + '</b></small></font></td>';
	st += '<td align=center><img src=images/icons/attributes/na.gif width=20 height=20><br><font color=darkgreen><small><b>' + na + '</b></small></font></td>';
	st += '<td align=center><img src=images/icons/attributes/nd.gif width=20 height=20><br><font color=darkgreen><small><b>' + nd + '</b></small></font></td>';
	st += '<td style="border-left:1px solid #F0D3B0" align=center><img src=images/icons/attributes/o.gif width=20 height=20><br><font color=slateblue><small><b>' + resist + '</b></small></font></td>';
	st += '</tr>';

	st += '<tr>';
	st += '<td align=center><img src=images/icons/attributes/f_ic1.gif width=20 height=20><br><font color=darkred><small><b>' + fp + '</b></small></font></td>';
	st += '<td align=center><img src=images/icons/attributes/fm.gif width=20 height=20><br><font color=darkred><small><b>' + fm + '</b></small></font></td>';
	st += '<td align=center><img src=images/icons/attributes/fa.gif width=20 height=20><br><font color=darkred><small><b>' + fa + '</b></small></font></td>';
	st += '<td align=center><img src=images/icons/attributes/fd.gif width=20 height=20><br><font color=darkred><small><b>' + fd + '</b></small></font></td>';
	st += '<td style="border-left:1px solid #F0D3B0" align=center><img src=images/icons/attributes/c.gif width=20 height=20><br><font color=purple><small><b>' + crit + '</b></small></font></td>';
	st += '</tr>';

	/*	st += '<tr>';
	st += '<td align=center><img src=images/icons/attributes/r.gif width=20 height=20><br><font color=darkslateblue><small><b>' + regen + '</b></small></font></td>';
	st += '</tr>';*/

	st += '</table>';
	if( show_nick && rdy )
		st += '<div style="position:absolute;top:3px;right:3px;"><table style="width:26px;height:25px;" cellspacing=0 cellpadding=0 border=0 background=images/rect/ch.png><tr><td>&nbsp;</td></tr></table></div>';
	if( show_nick )
		st += '<div style="position:absolute;top:5px;left:5px;"><img src=' + src + ' width=11 height=11 style="cursor:pointer;" onclick=ccce(' + id + ') id=ccci' + id + '></div>';
	st += rFL();
	st += '</div>';

	return st;
}