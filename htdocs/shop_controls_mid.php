<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link rel="stylesheet" type="text/css" href="style2.css">
<html><body>

<?

include_once( "functions.php" );
include_once( "player.php" );
include_once( "shop.php" );
include_once( "arrays.php" );
include_once( "items.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$stats = $player->getAllAttrNames( );

$shop_id = $HTTP_GET_VARS['shop_id'];
settype( $shop_id, 'integer' );

if( !( $player->IsShopOwner( $shop_id ) ) )
	RaiseError( "Вы не являетесь владельцем этого магазина" );
	
$shop = new Shop( $shop_id );
	
?>

<div id=moo name=moo>&nbsp;</div>

<script>

	var type_names = new Array( );

	var p = new Array( );
	var names = new Array( );
	var descs = new Array( );
	var prices = new Array( );
	var types = new Array( );
	var imgs = new Array( );

	var lnums = new Array( );
	var rnums = new Array( );
	var rpb = new Array( );
	var rps = new Array( );
	var rgs = new Array( );
	var orpb = new Array( );
	var orps = new Array( );
	var orgs = new Array( );
	var left_filter = -1;
	var right_filter = -1;
	
	var left_types_by_index = new Array( );
	var right_types_by_index = new Array( );
	
	var player_money;
	var money_reserve;
	
	function ge( a )
	{
		return document.getElementById( a );
	}
	
	function toInt( a )
	{
		a = parseInt( a );
		if( isNaN( a ) ) a = 0;
		return a;
	}
	
	function toFloat( a )
	{
		a = parseFloat( a );
		if( isNaN( a ) ) a = 0.0;
		return a;
	}
	
	function get_money_str( a )
	{
		if( toInt( a / 10 ) % 10 == 1 ) return 'монет';
		if( a % 10 == 1 ) return "монета";
		if( a % 10 == 2 || a % 10 == 3 || a % 10 == 4 ) return 'монеты';
		return "монет";
	}
	
	function addtype( id, a )
	{
		type_names[id] = a;
	}
	
	function additem( id, nm, ih, price, img, type )
	{
		if( p[id] ) return;
		p[id] = 1;
		
		names[id] = nm;
		descs[id] = ih;
		prices[id] = price;
		imgs[id] = new Image;
		imgs[id].src = '../images/items/' + img;
		types[id] = type;
		
		lnums[id] = 0;
		rnums[id] = 0;
	}
	
	function getprice( a, b, c )
	{
		if( a == -1 ) return b * c / 100;
		return a;
	}
	
	function addtol( id, num )
	{
		lnums[id] += num;
	}
	
	function addtor( id, num, sell_price, buy_price, last_regime )
	{
		rnums[id] += num;
		rpb[id] = buy_price;
		rps[id] = sell_price;
		rgs[id] = last_regime;
		orpb[id] = buy_price;
		orps[id] = sell_price;
		orgs[id] = last_regime;
	}
	
	function item_img( a )
	{
		return '<img src=' + imgs[a].src + '>';
	}
	
	function item_desc( q, a, z )
	{
		var st = '';
		
		st += '[' + q + ']&nbsp;' + '<b>' + names[a] + '</b><br>';
		if( z )
			st += descs[a];
		
		return st;
	}
	
	function fromltor( a, i, q )
	{
		if( !q )
			q = toInt( ge( 'li' + a + '_' + i ).value );
		
		if( q < 0 ) q = 0;
		if( q > lnums[i] ) q = lnums[i];
		
		if( !rps[i] )
		{
			rps[i] = -1;
			rpb[i] = -1;
			rgs[i] = -1;
			orps[i] = -1;
			orpb[i] = -1;
			orgs[i] = -1;
		}
		
		rnums[i] += q;
		lnums[i] -= q;
		
		parent.bt.move( i, q );

		updateall( );		
	}
	
	function fromrtol( a, i, q )
	{
		if( !q )
			q = toInt( ge( 'ri' + a + '_' + i ).value );
		
		if( q < 0 ) q = 0;
		if( q > rnums[i] ) q = rnums[i];
		
		lnums[i] += q;
		rnums[i] -= q;
		
		parent.bt.move( i, - q );
		
		updateall( );		
	}
	
	function change_buy_price( a, i, q )
	{
		if( !q )
			q = toFloat( ge( 'rbp' + a + '_' + i ).value );
		
		if( q < 0 ) q = -1;
		if( q > 1000 * prices[i] && q > 250000 ) q = 1000 * prices[i];
		
		rpb[i] = q;
		
		parent.bt.ch[i] = 1;
		parent.bt.refr( );
		
		updateall( );		
	}
	
	function change_sell_price( a, i, q )
	{
		if( !q )
			q = toFloat( ge( 'rsp' + a + '_' + i ).value );
		
		if( q < 0 ) q = -1;
		if( q > 1000 * prices[i] && q > 250000 ) q = 1000 * prices[i];
		
		rps[i] = q;
		
		parent.bt.ch[i] = 1;
		parent.bt.refr( );
		
		updateall( );		
	}
	
	function change_regime( a, i )
	{
		q = toFloat( ge( 'rgm' + a + '_' + i ).selectedIndex - 1 );
		
		rgs[i] = q;
		
		parent.bt.ch[i] = 1;
		parent.bt.refr( );
		
		updateall( );		
	}
	
	function alter_money( a )
	{
		a = toInt( a );
		
		if( a > 0 ) { if( player_money < a ) a = player_money; }
		else { if( money_reserve < - a  ) a = - money_reserve; }
		
		player_money -= a;
		money_reserve += a;
		
		parent.bt.dmoney += a;
		parent.bt.refr( );
		
		updateall( );		
	}
	
	function ilm( a )
	{
		var st = '';
		
		st += '<table border=0 cellspacing=0 cellpadding=0 width=100%><tr><td valign=top width=50>';
		st += '<img src=../images/money.gif>';
		st += '</td><td valign=top width=100%>&nbsp;';
		st += player_money + '&nbsp;' + get_money_str( player_money );
		st += '</td><td valign=top align=right>';

		nm = 'mli';
		st += '<nobr>Добавить монеты: <input value=0 class=btn80 maxlength=9 id=' + nm + ' name=' + nm + ' type=text><button class=te_btn onClick="alter_money( ge( \'mli\' ).value );">Ok</button></nobr><br>'

		st += '</td></td></table>';
			
		return st;
	}
	
	function irm( a )
	{
		var st = '';
		
		st += '<table border=0 cellspacing=0 cellpadding=0 width=100%><tr><td valign=top width=50>';
		st += '<img src=../images/money.gif>';
		st += '</td><td valign=top width=100%>&nbsp;';
		st += money_reserve + '&nbsp;' + get_money_str( money_reserve );
		st += '</td><td valign=top align=right>';

		nm = 'mri';
		st += '<nobr>Снять монеты: <input value=0 class=btn80 maxlength=9 id=' + nm + ' name=' + nm + ' type=text><button class=te_btn onClick="alter_money( - ge( \'mri\' ).value );">Ok</button></nobr><br>'

		st += '</td></td></table>';
			
		return st;
	}
	
	function ilg( )
	{
		st = '';

		ok = 0;		
		for( i in lnums )
			if( lnums[i] > 0 && ( left_filter == -1 || types[i] == left_filter ) )
			{
				if( !ok )
					st = '<table  class="table1" border=1 bordercolor=silver>';
					
				st += '<tr>';
				st += '<td valign=top>' + item_img( i ) + '</td>';
				st += '<td valign=top>' + item_desc( lnums[i], i, 1 ) + '</td>';
				st += '<td valign=top>';
				nm = 'li0_' + i;
				st += '<nobr>Добавить товар:<input value=' + lnums[i] + ' class=btn40 size=2 id=' + nm + ' name=' + nm + ' type=text><button class=te_btn onClick="fromltor( 0, ' + i + ' )">Ok</button></nobr><br>'
				st += '</td>';
				ok = 1;
			}
		
		if( !ok ) st += '&nbsp;<i>Пусто</i>';
		else st += '</table>';
		
		return st;
	}
	
	function irg( )
	{
		st = '';
		ok = 0;
		
		for( i in rnums )
			if( rnums[i] > 0 && ( right_filter == -1 || types[i] == right_filter ) )
			{
				if( !ok )
					st = '<table  class="table1" border=1 bordercolor=silver>';
					
				a = 0;
				st += '<tr>';
				st += '<td valign=top>' + item_img( i ) + '</td>';
				st += '<td valign=top>' + item_desc( rnums[i], i, 1 ) + '</td>';
				st += '<td valign=top>';
				st += '<table cellspacing=0 cellpadding=0 border=0>';
				nm = 'ri' + a + '_' + i;
				st += '<tr><td><nobr>Снять&nbsp;товар:</td><td><input value=1 class=btn40 size=2 id=' + nm + ' name=' + nm + ' type=text><button class=te_btn onClick="fromrtol( ' + a + ', ' + i + ' )">Ok</button></nobr><br>'
				nm = 'rsp' + a + '_' + i;
				st += '<tr><td><nobr>Цена&nbsp;продажи (' + getprice( rps[i], prices[i], parent.tp.price_sell_mul ) + '):</td><td><input class=btn40 size=2 id=' + nm + ' name=' + nm + ' type=text value=' + rps[i] + '><button class=te_btn onClick="change_sell_price( ' + a + ', ' + i + ' )">Ok</button></nobr></td></tr>'
				nm = 'rbp' + a + '_' + i;
				st += '<tr><td><nobr>Цена&nbsp;покупки (' + getprice( rpb[i], prices[i], parent.tp.price_buy_mul ) + '):</td><td><input class=btn40 size=2 id=' + nm + ' name=' + nm + ' type=text value=' + rpb[i] + '><button class=te_btn onClick="change_buy_price( ' + a + ', ' + i + ' )">Ok</button></nobr></td></tr>'
				nm = 'rgm' + a + '_' + i;
				st += '<tr><td><nobr>Сохранять последний:</td><td><select onchange="change_regime( ' + a + ', ' + i + ' );" name=' + nm + ' id=' + nm + '><option value=-1' + ( rgs[i] == -1 ? " SELECTED" : "" ) + '>По умолч.<option value=0' + ( rgs[i] == 0 ? " SELECTED" : "" ) + '>Не сохранять<option value=1' + ( rgs[i] == 1 ? " SELECTED" : "" ) + '>Сохранять</select></nobr></td></tr>'
				st += '</table>';
				st += '</td>';
				ok = 1;
			}
		
		if( !ok ) st += '&nbsp;<i>Пусто</i>';
		else st += '</table>';
		
		return st;
	}
	
	function updateleft( )
	{
		ge( 'lmn' ).innerHTML = ilm( );
		ge( 'lgd' ).innerHTML = ilg( );
	}
	
	function updateright( )
	{
		ge( 'rmn' ).innerHTML = irm( );
		ge( 'rgd' ).innerHTML = irg( );
	}
	
	function updateall( )
	{
		updateleft( );
		updateright( );
	}
	
	function filter_select( a )
	{
		var q = 0;
		
		b = a.charAt( 0 );
		
		st = '';
		st += 'Фильтр: <select onchange="' + a + '_filter = ' + a + '_types_by_index[this.selectedIndex]; update' + a + '();">';
		for( i in type_names )
		{
			if( i == -1 ) ok = 1;
			else
			{
				ok = 0;
				for( j in eval( b + 'nums' ) )
					if( types[j] == i && eval( b + 'nums' )[j] > 0 ) ok = 1;
			}
				
			if( ok )
			{
				st += '<option' + ( ( eval( a + '_filter' ) == i ) ? ' SELECTED' : '' ) + '>' + type_names[i];
				if( a == "left" )
					left_types_by_index[q ++] = i;
				else
					right_types_by_index[q ++] = i;
			}
		}
		st += '</select>';
		
		return st;
	}
	
	function refr( )
	{
		var st = '';
		var i;
		var q = 0;
		
		st += '<table height=100% border=0 width=100% cellpadding=0><tr>';
		
		st += '<td valign=top width=45%><b>У персонажа</b><br>';
		st += '<br>';
		
		st += '<table width=100%><tr><td><hr width=8></td><td>Монеты</td><td width=100%><hr></td></tr></table>';
		st += '<div id=lmn name=lmn>&nbsp;</div>';
		st += '<table width=100%><tr><td><hr width=8></td><td>Товар</td><td width=100%><hr></td></tr></table>';

		st += filter_select( 'left' );
		st += '<div id=lgd name=lgd>&nbsp;</div>';

		st += '</td>';
		
		st += '<td valign=top width=1 style="background-color: gray"><img height=0 width=1>';
		st += '</td>';
		
		st += '<td valign=top width=55%><b>В магазине</b><br>';
		st += '<br>';
		
		st += '<table width=100%><tr><td><hr width=8></td><td>Монеты</td><td width=100%><hr></td></tr></table>';
		st += '<div id=rmn name=rmn>&nbsp;</div>';
		st += '<table width=100%><tr><td><hr width=8></td><td>Товар</td><td width=100%><hr></td></tr></table>';
		
		st += filter_select( 'right' );
		st += '<div id=rgd name=rgd>&nbsp;</div>';

		st += '</td>';
		
		st += '</tr></table>';
		
		ge( 'moo' ).innerHTML = st;

		updateall( );
	}

	addtype( -1, "Все&nbsp;Вещи" );
<?
	foreach( $item_types as $a=>$b ) 
		print( "\taddtype( $a, '$b' );\n" );
		
	print( "\n" );

	$res = f_MQuery( "SELECT items.* FROM items, player_items WHERE player_id = {$player->player_id} AND items.item_id = player_items.item_id AND weared=0 AND nodrop=0" );
	while( $arr = f_MFetch( $res ) )
	{
		$descr = itemDescr( $arr );
		print( "\tadditem( $arr[item_id], '$arr[name]', '$descr', $arr[price], '$arr[image]', $arr[type] );\n" );
	}

	$res = f_MQuery( "SELECT items.* FROM items, shop_goods WHERE shop_id = {$shop->shop_id} AND items.item_id = shop_goods.item_id" );
	while( $arr = f_MFetch( $res ) )
	{
		$descr = itemDescr( $arr );
		print( "\tadditem( $arr[item_id], '$arr[name]', '$descr', $arr[price], '$arr[image]', $arr[type] );\n" );
	}
		
	print( "\n" );
		
	$res = f_MQuery( "SELECT item_id, number FROM player_items WHERE player_id = {$player->player_id} AND weared=0" );
	while( $arr = f_MFetch( $res ) )
		print( "\taddtol( $arr[0], $arr[1] );\n" );
	
	print( "\n" );
		
	$res = f_MQuery( "SELECT item_id, number, buy_price, sell_price, regime FROM shop_goods WHERE shop_id = {$shop->shop_id}" );
	while( $arr = f_MFetch( $res ) )
		print( "\taddtor( $arr[0], $arr[1], $arr[3], $arr[2], $arr[4] );\n" );
	
?>
	
	player_money = <? print $player->money; ?>;
	money_reserve = <? print $shop->money; ?>;
	
	refr( );

</script>

</body></html>
