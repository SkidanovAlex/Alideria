var empties = new Array( 'bg50hlm', 'bg50amt', 'bg50wpn', 'bg50wpn', 'bg25brc', 'bg25brc', 'bg25rng', 'bg25rng', 'bg50arm', 'bg50clk', 'bg50glv', 'bg50bts', 'bg25pot', 'bg25pot', 'bg25pot', 'bg25pot', 'bg25empty', 'bg25empty', 'bg25empty', 'bg25empty', 'bg25empty', 'bg25empty', 'bg25empty', 'bg25empty' );
var empty_hints = new Array( "Место для Шлема", "Место для Амулета", "Место для Оружия", "Место для Оружия", "Место для Браслета", "Место для Браслета", "Место для Кольца", "Место для Кольца", "Место для Брони", "Место для Плаща", "Место для Перчаток", "Место для Обуви", "Место для Зелья", "Место для Зелья", "Место для Зелья", "Место для Зелья", "Место для Талисмана", "Место для Талисмана", "Место для Талисмана", "Место для Талисмана", "Место для Медальона", "Место для Медальона", "Место для Медальона", "Место для Медальона" );

var items = new Array( );
var spells = new Array( );
var spells_s = new Array();
var p = new Array( );
var spell_num = 0;
var spell_s_num = 0;
var avatar_src = '';
var pet_src = 'empty.gif';
var pet_hint = '';

var xs = new Array( 50, 137,   6, 180,  10,  35,  60,  85,   8, 188,  20, 138,  30,  55,  80,  105,  18,  72,  126,  180,  18,  72,  126,  180,  18,  72,  126,  180 );
var ys = new Array(  8,   8,  57,  46, 255, 255, 255, 255, 129, 118, 195, 263, 285, 285, 285, 285, 18, 18, 18, 18, 18, 18, 18, 18, 18, 18, 18, 18 );
var ws = new Array( 50, 50, 50,  50,  25, 25, 25, 25, 50, 50, 50, 50, 25, 25, 25, 25, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50, 50 );

function g(a)
{
	return document.getElementById( a );
}

function convert_slot( a )
{
	if( a == 9 ) return 1;
	if( a == 10 ) return 0;
	if( a == 13 ) return 9;
	if( a == 12 ) return 10;
	if( a == 1 ) return 12;
	if( a > 13 ) return a - 1;

	return a;
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

function wear( id, nm, descr, img, loc )
{
	loc = convert_slot( loc );
	items[loc] = new Array( );
	items[loc][0] = id;
	items[loc][1] = nm;
	items[loc][2] = descr;
	items[loc][4] = img;
	p[loc] = 1;
}

function unwear( slot )
{
	p[convert_slot( slot )] = 0;
}

function add_spell( str )
{
	spells[spell_num ++] = str;
}

function add_spell_s(str)
{
	spells_s[spell_s_num ++] = str;
}

function del_spell( id )
{
	if( id >= spell_num ) return;
	-- spell_num;
	for( var i = id; i < spell_num; ++ i )
		spells[i] = spells[i + 1];
}

function del_spell_s( id )
{
	if( id >= spell_s_num ) return;
	-- spell_s_num;
	for( var i = id; i < spell_s_num; ++ i )
		spells_s[i] = spells_s[i + 1];
}

function show_char( elem )
{
	offs = getAbsolutePos( elem );
	for( i = 0; i < 24; ++ i )
	{
		g( 'item' + ( i + 1 ) ).style.left = xs[i];
		g( 'item' + ( i + 1 ) ).style.top = ys[i];
		if( !p[i] ) g( 'item' + ( i + 1 ) ).innerHTML = '<div><img width=' + ws[i] + ' height=' + ws[i] + ' src=images/items/bg/' + empties[i] + '.gif></div>';
		else g( 'item' + ( i + 1 ) ).innerHTML = '<div><img width=' + ws[i] + ' height=' + ws[i] + ' src=images/items/' + items[i][4] + '></div>';
	}

	if( g( 'csp0' ) ) for( i = 0; i < 8; ++ i )
	{
		g( 'csp' + i ).style.left = 15 + 27 * i;
		g( 'csp' + i ).style.top = 318;
		if( i >= spell_num ) g( 'csp' + i ).innerHTML = '<div><img width=25 height=25 src=images/spells/empty_small.gif></div>';
		else g( 'csp' + i ).innerHTML = '<div>' + spells[i] + '</div>';
	}

	if( g( 'csps0' ) ) for( i = 0; i < 8; ++ i )
	{
		g( 'csps' + i ).style.left = 15 + 27 * i;
		g( 'csps' + i ).style.top = 348;
		if( i >= spell_s_num ) g( 'csps' + i ).innerHTML = '<div><img width=25 height=25 src=images/spells/empty_small.gif></div>';
		else g( 'csps' + i ).innerHTML = '<div>' + spells_s[i] + '</div>';
	}

	g( 'avatar' ).style.left = 70;
	g( 'avatar' ).style.top = 30;
	g( 'avatar' ).src = avatar_src;
	
	if( pet_hint != '' )
	{
    	g( 'pet_img' ).style.left = 170;
    	g( 'pet_img' ).style.top = 170;
    	g( 'pet_img' ).src = pet_src;
   	}
	
	elem.innerHTML = g( 'moo' ).innerHTML;
}

function set_avatar( str )
{
	avatar_src = 'images/avatars/' + str;
}

function set_pet( str, hint )
{
	pet_src = 'images/pets/' + str + '.png';
	pet_hint = hint;
}

function remove_pet( )
{
	pet_src = 'empty.gif';
	pet_hint = '';
}
