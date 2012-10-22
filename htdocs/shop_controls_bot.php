<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link rel="stylesheet" type="text/css" href="style2.css">
<html><body>

<div id=report name=report>&nbsp;</div>

<?

include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$shop_id = $HTTP_GET_VARS['shop_id'];
settype( $shop_id, 'integer' );

if( !( $player->IsShopOwner( $shop_id ) ) )
	RaiseError( "Вы не являетесь владельцем этого магазина" );
	
?>

<script>

	var p = new Array( );
	var mv = new Array( );
	var ch = new Array( );
	var dmoney = 0;
	var dumoney = 0;
	
	var regimes = new Array( );
	regimes[0] = 'Продажа/Покупка';
	regimes[1] = 'Только Продажа';
	regimes[2] = 'Только Покупка';
	regimes[3] = 'Магазин Закрыт';
	regimes[4] = 'Сохранение Последнего';
	
	var regimes2 = new Array( );
	regimes2[-1] = 'По умолчанию';
	regimes2[0] = 'Не сохранять';
	regimes2[1] = 'Сохранять';
	
	var valus = new Array();
	valus[0] = 'Дублоны';
	valus[1] = 'Таланты';
	
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
	
	function get_money_str( a )
	{
		if( toInt( a / 10 ) % 10 == 1 ) return 'монет';
		if( a % 10 == 1 ) return "монету";
		if( a % 10 == 2 || a % 10 == 3 || a % 10 == 4 ) return 'монеты';
		return "монет";
	}
	
	function get_umoney_str( a )
	{
		if( toInt( a / 10 ) % 10 == 1 ) return 'талантов';
		if( a % 10 == 1 ) return "талант";
		if( a % 10 == 2 || a % 10 == 3 || a % 10 == 4 ) return 'таланта';
		return "талантов";
	}
	
	function move( a, b )
	{
		if( !mv[a] )
			mv[a] = 0;
		
		mv[a] += b;
		
		if( mv[a] == 0 )
			delete mv[a];
			
		refr( );
	}
	
	function cancelall( )
	{
		var i;
		
		if( confirm( 'Отменить все сделанные изменения?' ) )
		{
			parent.tp.shop_name = parent.tp.old_name;
			parent.tp.price_sell_mul = parent.tp.old_sell_mul;
			parent.tp.price_buy_mul = parent.tp.old_buy_mul;
			parent.tp.shop_regime = parent.tp.old_regime;
//			parent.tp.shop_capacity = parent.tp.old_capacity;
			parent.tp.refr( );
			
			for( i in ch )
			{
				parent.md.rps[i] = parent.md.orps[i];
				parent.md.rpb[i] = parent.md.orpb[i];
				parent.md.rgs[i] = parent.md.orgs[i];
			}
			
			for( i in mv )
			{
				if( mv[i] > 0 ) parent.md.fromrtol( parent.md.types[i], i, mv[i] );
				else if( mv[i] < 0 ) parent.md.fromltor( parent.md.types[i], i, - mv[i] );
			}
			
			parent.md.alter_money( - dmoney );			

			parent.md.refr( );
		}
		
		refr( );
	}
	
	function cmd( op, a1, a2, n )
	{
		var st = '';
		st = '&';
		
		st += 'cmd' + n + '=' + op;
		st += '&arg' + n + '=' + a1;
		st += '&opt' + n + '=' + a2;
		
		return st;
	}
	
	function accept( )
	{
		var cmdn;
		var st;
		
		if( confirm( 'Сохранить внесенные изменения?' ) )
		{
			cmdn = 0;
			st = '';
			if( parent.tp.shop_name != parent.tp.old_name )
				st += cmd( '0', parent.tp.shop_name, 0, cmdn ++ );
			if( parent.tp.price_sell_mul != parent.tp.old_sell_mul )
				st += cmd( '1', parent.tp.price_sell_mul, 0, cmdn ++ );
			if( parent.tp.price_buy_mul != parent.tp.old_buy_mul )
				st += cmd( '2', parent.tp.price_buy_mul, 0, cmdn ++ );
			if( parent.tp.shop_regime != parent.tp.old_regime )
				st += cmd( '3', parent.tp.shop_regime, 0, cmdn ++ );
//			if( parent.tp.shop_capacity != parent.tp.old_capacity )
//				st += cmd( '4', parent.tp.shop_capacity - parent.tp.old_capacity, 0, cmdn ++ );
			
			for( i in mv )
			{
				if( mv[i] > 0 ) st += cmd( '20', i, mv[i], cmdn ++ );
				else if( mv[i] < 0 ) st += cmd( '21', i, - mv[i], cmdn ++ );
			}

			for( i in ch )
			{
				st += cmd( '10', i, parent.md.rps[i], cmdn ++ );
				st += cmd( '11', i, parent.md.rpb[i], cmdn ++ );
				st += cmd( '12', i, parent.md.rgs[i], cmdn ++ );
				st += cmd( '102', i, parent.md.valu[i], cmdn ++ );
			}
			
			st += cmd( '100', dmoney, 0, cmdn ++ );
			st += cmd( '101', dumoney, 0, cmdn ++ );
			
			parent.location.href = 'shop_controls_acc.php?shop_id=' + <? print $shop_id; ?> + st;
		}
	}
	
	function refr( )
	{
		var i;
		var ok = 0;
		var sb = 1;
		var tok;
		var error = '';
		var a, b;
		var used_cap = 0;
		var close_msg = 0;
		
		st = '<b>Следующие действия будут применены:</b><br>';
		
		if( parent.tp.shop_name != parent.tp.old_name )
		{
			st += '<a color=white style="cursor: pointer" onClick="parent.tp.shop_name = parent.tp.old_name; parent.tp.refr( ); refr( );">[x]</a> ';
			st += 'Поменять название палатки на "' + parent.tp.shop_name + '". Было "' + parent.tp.old_name + '".<br>';
			ok = 1;
		}
		
/*		if( parent.tp.shop_capacity != parent.tp.old_capacity )
		{
			st += '<a color=white style="cursor: pointer" onClick="parent.tp.shop_capacity = parent.tp.old_capacity; parent.tp.refr( ); refr( );">[x]</a> ';
			st += 'Расширить магазин на ' + ( parent.tp.shop_capacity - parent.tp.old_capacity ) + '. Стоимость расширения:  ' + ( ( parent.tp.shop_capacity - parent.tp.old_capacity ) * parent.tp.capacity_cost ) + '.<br>';
			ok = 1;
		}
*/		
		if( parent.tp.price_sell_mul != parent.tp.old_sell_mul )
		{
			st += '<a color=white style="cursor: pointer" onClick="parent.tp.price_sell_mul = parent.tp.old_sell_mul; parent.tp.refr( ); refr( );">[x]</a> ';
			st += 'Поменять множитель на цену продажи на ' + parent.tp.price_sell_mul + '%. Было ' + parent.tp.old_sell_mul + '%.<br>';
			ok = 1;
		}
		
		if( parent.tp.price_buy_mul != parent.tp.old_buy_mul )
		{
			st += '<a color=white style="cursor: pointer" onClick="parent.tp.price_buy_mul = parent.tp.old_buy_mul; parent.tp.refr( ); refr( );">[x]</a> ';
			st += 'Поменять множитель на цену продажи на ' + parent.tp.price_buy_mul + '%. Было ' + parent.tp.old_buy_mul + '%.<br>';
			ok = 1;
		}
		
		if( parent.tp.shop_regime != parent.tp.old_regime )
		{
			st += '<a color=white style="cursor: pointer" onClick="parent.tp.shop_regime = parent.tp.old_regime; parent.tp.refr( ); refr( );">[x]</a> ';
			st += 'Поменять режим работы палатки на "' + regimes[parent.tp.shop_regime] + '". До изменения стоял режим "' + regimes[parent.tp.old_regime] + '".<br>';
			ok = 1;
		}
		
		for( i in mv )
		{
			if( mv[i] > 0 ) st += '<a color=white style="cursor: pointer" onClick="parent.md.fromrtol( parent.md.types[' + i + '], ' + i + ', ' + mv[i] + ' );">[x]</a> Добавить в палатку [' + mv[i] + ']&nbsp;<b>' + parent.md.names[i] + '</b>';
			else if( mv[i] < 0 ) st += '<a color=white style="cursor: pointer" onClick="parent.md.fromltor( parent.md.types[' + i + '], ' + i + ', ' + ( -mv[i] ) + ' );">[x]</a> Снять с палатки [' + ( - mv[i] ) + ']&nbsp;<b>' + parent.md.names[i] + '</b>';
			
			st += '. До изменения у вас: ' + ( parent.md.lnums[i] + mv[i] ) + ', в палатке: ' + ( parent.md.rnums[i] - mv[i] );
			st += '. После изменения у вас: ' + parent.md.lnums[i] + ', в палатке: ' + parent.md.rnums[i];
			st += '<br>';
			
			ok = 1;
		}
		
		for( i in parent.md.rnums )
		{
			if( parent.md.rnums[i] > 0 )
				++ used_cap;
		}
		
		for( i in ch )
		{
			tok = 0;
			if( ch[i] == 1 )
			{
				if( parent.md.rps[i] != parent.md.orps[i] )
				{
					st += '<a color=white style="cursor: pointer" onClick="parent.md.rps[' + i + '] = parent.md.orps[' + i + ']; parent.md.refr( ); refr( );">[x]</a> ';
					st += 'Поменять цену продажи товара <b>' + parent.md.names[i] + '</b> на ' + parent.md.rps[i] + '. Было ' + parent.md.orps[i] + '.<br>';
					tok = 1;
					
				}
				if( parent.md.rpb[i] != parent.md.orpb[i] )
				{
					st += '<a color=white style="cursor: pointer" onClick="parent.md.rpb[' + i + '] = parent.md.orpb[' + i + ']; parent.md.refr( ); refr( );">[x]</a> ';
					st += 'Поменять цену покупки товара <b>' + parent.md.names[i] + '</b> на ' + parent.md.rpb[i] + '. Было ' + parent.md.orpb[i] + '.<br>';
					tok = 1;
				}
				if( parent.md.rgs[i] != parent.md.orgs[i] )
				{
					st += '<a color=white style="cursor: pointer" onClick="parent.md.rgs[' + i + '] = parent.md.orgs[' + i + ']; parent.md.refr( ); refr( );">[x]</a> ';
					st += 'Поменять режим сохранения последнего экземпляра товара <b>' + parent.md.names[i] + '</b> на "' + regimes2[parent.md.rgs[i]] + '". Был "' + regimes2[parent.md.orgs[i]] + '".<br>';
					tok = 1;
				}
				if( parent.md.valu[i] != parent.md.ovalu[i] )
				{
					st += '<a color=white style="cursor: pointer" onClick="parent.md.valu[' + i + '] = parent.md.ovalu[' + i + ']; parent.md.refr( ); refr( );">[x]</a> ';
					st += 'Поменять валюту для товара <b>' + parent.md.names[i] + '</b> на "' + valus[parent.md.valu[i]] + '". Был "' + valus[parent.md.ovalu[i]] + '".<br>';
					tok = 1;
				}
				
				a = parent.md.getprice( parent.md.rps[i], parent.md.prices[i], parent.tp.price_sell_mul );
				b = parent.md.getprice( parent.md.rpb[i], parent.md.prices[i], parent.tp.price_buy_mul );
				if( b > a )
				{
					error += '<b><font color=red>Внимание!</font> Цена покупки товара ' + parent.md.names[i] + ' превышает цену продажи!!!</b><br>';
					close_msg = 1;
				}
			}
			if( !tok ) delete ch[i];
			else ok = 1;
		}
		
		if( dmoney != 0 )
		{
			st += '<a color=white style="cursor: pointer" onClick="parent.md.alter_money( - dmoney );">[x]</a> ';
			if( dmoney > 0 )
				st += 'Положить в палатку ' + dmoney + ' ' + get_money_str( dmoney );
			else
				st += 'Снять с палатки ' + ( - dmoney ) + ' ' + get_money_str( - dmoney );

			st += '. До изменения у вас: ' + ( parent.md.player_money + dmoney ) + ', в палатке: ' + ( parent.md.money_reserve - dmoney );
			st += '. После изменения у вас: ' + parent.md.player_money + ', в палатке: ' + parent.md.money_reserve;
			st += '<br>';
				
			ok = 1;
		}
		
		if( dumoney != 0 )
		{
			st += '<a color=white style="cursor: pointer" onClick="parent.md.alter_umoney( - dumoney );">[x]</a> ';
			if( dumoney > 0 )
				st += 'Положить в палатку ' + dumoney + ' ' + get_umoney_str( dumoney );
			else
				st += 'Снять с палатки ' + ( - dumoney ) + ' ' + get_umoney_str( - dumoney );

			st += '. До изменения у вас: ' + ( parent.md.player_umoney + dumoney ) + ', в палатке: ' + ( parent.md.umoney_reserve - dumoney );
			st += '. После изменения у вас: ' + parent.md.player_umoney + ', в палатке: ' + parent.md.umoney_reserve;
			st += '<br>';
				
			ok = 1;
		}
		
		if( used_cap > parent.tp.shop_capacity )
		{
			error += "<b><font color=red>Внимание!</font> Количество товара в палатке превышает ее вместимость!</b><br>";
			sb = 0;
		}
			
/*		if( ( ( parent.tp.shop_capacity - parent.tp.old_capacity ) * parent.tp.capacity_cost ) > parent.md.player_money )
		{
			error += "<b><font color=red>Внимание!</font> Не хватает монет на расширение палатки!</b><br>";
			sb = 0;
		}
*/		
		if( !ok )
			st += '<i>Нет изменений</i><br>';
			
		if( parent.tp.price_buy_mul > parent.tp.price_sell_mul )
		{
			error += '<font color=red><b>Внимание!</font> Множитель на цену покупки превышает множитель на цену продажи!!!</b><br>';
			close_msg = 1;
		}
		
		if( error != '' )
		{
			st += error;
			if( parent.tp.shop_regime != 3 )
			{
				sb = 0;
				close_msg = 1;
			}
		}
		
		if( close_msg )
		{
			// st += '<b> --- </b><br>';
			
			// Здесь можно добавить сообщение по поводу того, что персонаж может
			// поставить режим "Магазин закрыт" чтобы сохранить изменения с
			// ценами покупки превышающими цену продажи.
		}
		
		if( ok )
		{
			if( sb ) st += '<button class=c_btn onclick="accept( );">Применить</button>&nbsp;';
			st += '<button class=c_btn onClick="cancelall( );">Отменить все</button>';
		}
		
		ge( 'report' ).innerHTML = st;
	}
	
	refr( );

</script>

</body></html>
