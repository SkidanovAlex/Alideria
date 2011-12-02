<?

if( $player->level < 3 ) { echo( "<center><i>Почта доступна только игрокам третьего уровня и выше.</i></center>" ); return; }

?>

<b>Отправка писем</b> - <span id=post_act><a href='javascript:open_attach()'>приложить к письму вещи</a></span><br>

<script>

function open_attach( )
{
	document.getElementById( 'letter_text' ).style.display = 'none';
	document.getElementById( 'attach' ).style.display = '';
	document.getElementById( 'post_act' ).innerHTML = "<a href='javascript:close_attach()'>вернуться к набору письма</a>";
}

function close_attach( )
{
	document.getElementById( 'attach' ).style.display = 'none';
	document.getElementById( 'letter_text' ).style.display = '';
	document.getElementById( 'post_act' ).innerHTML = "<a href='javascript:open_attach()'>приложить к письму вещи</a>";
}

var attached_items = new Array( );
var price = 10;

function refresh_attachments( )
{
	var st = '';
	price = 10;
	for( var i in attached_items ) if( attached_items[i] )
	{
		st += '<img title="Убрать" width=11 height=11 src=images/e_close.gif style="cursor:pointer;" onclick="unattach(' + i + ')"> [' + attached_items[i] + '] ' + items[i].name + '<br>';
		price += 10;
	}
	if( document.getElementById( 'money' ).value > 0 )
		price += 10;
	if( document.getElementById( 'np1' ).value > 0 )
		price += 10;
	st += '&nbsp;';
	document.getElementById( 'attachments' ).innerHTML = st;
	document.getElementById( 'dprice' ).innerHTML = '<b>' + price + '</b>';
}

function do_attach( id )
{
	var num = parseInt( document.getElementById('place'+id).value );

	if( num > items[id].number ) num = items[id].number;

    if( !attached_items[id] ) attached_items[id] = num;
	else attached_items[id] += num;

	remove_item( id, num );
	refresh_items( );

	refresh_attachments( );
}

function unattach( id )
{
	alter_item( id, attached_items[id] );
	refresh_items( );

	attached_items[id] = 0;

	refresh_attachments( );
}

function send_letter()
{
	if( confirm( 'Вы уверены, что хотите заплатить ' + price + ' дублонов и отправить письмо?' ) )
	{
		var u = document.getElementById( 'target' ).value;
		var t = document.getElementById( 'title' ).value;
		var m = document.getElementById( 'money' ).value;
		var txt = document.getElementById( 'text' ).value;
		var n1 = document.getElementById( 'np1' ).value;
		var n2 = document.getElementById( 'np2' ).value;

		var url = 'post_send.php?target=' + u + '&title=' + encodeURIComponent( t ) + '&money=' + m;
		url += '&np1=' + n1 + '&np2=' + n2;
		var id = 0;
		for( var i in attached_items ) if( attached_items[i] )
		{
			url += '&att' + id + '=' + i;
			url += '&num' + id + '=' + attached_items[i];
			++ id;
		}
		query( url, '' + txt );
		document.getElementById( 'send_btn' ).disabled = true;
		document.getElementById( 'send_btn' ).innerHTML = '<i>Идет отправка...</i>';
	}
}

function np_expand( )
{
	if( document.getElementById( 'np_div' ).style.display == '' )
	{
		document.getElementById( 'np_div' ).style.display = 'none';
		document.getElementById( 'np_img' ).src = 'images/e_plus.gif';
	}
	else
	{
		document.getElementById( 'np_div' ).style.display = '';
		document.getElementById( 'np_img' ).src = 'images/e_minus.gif';
	}
}

</script>

<div id=post_content>
<?

echo "<table><tr><td width=400 vAlign=top>";

echo "<div id=letter_text>";
echo "<table>";
echo "<tr><td>Адресат:</td><td><input class=m_btn id=target>&nbsp;<img width=11 height=11 src=images/i.gif style='cursor:pointer' onclick='window.top.oi(document.getElementById(\"target\").value)'></td></tr>";
echo "<tr><td>Заголовок:</td><td><input class=m_btn id=title></td></tr>";
echo "<tr><td vAlign=top>Текст:</td><td><textarea cols=20 rows=4 style='border:1px solid black' id=text></textarea></td></tr>";
echo "<tr><td>&nbsp;</td><td><button id=send_btn class=s_btn onclick='send_letter()'>Отправить</button></td></tr>";
echo "</table>";
echo "</div>";

echo "<div id=attach style='display:none'>";

    	include_js( "js/items_renderer.js" );
		$res = f_MQuery( "SELECT items.*,player_items.number FROM player_items,items WHERE player_id={$player->player_id} AND weared=0 AND items.item_id=player_items.item_id" );
		echo "<script>\n";
    	while( $arr = f_MFetch( $res ) )
    	{
    		echo "add_item( $arr[item_id], $arr[type], '$arr[name]', '".itemImage( $arr )."', '".itemFullDescr( $arr )."', $arr[number] );\n";
    	}
		echo "document.write( render_items( true, 'do_attach' ) );\n";
		echo "</script>\n";

echo "</div>";

echo "</td><td vAlign=top>";

echo "Цена отправки: <img width=11 height=11 src=images/money.gif> <span id=dprice><b>10</b></span><br><small>Цена не включает в себя вложенные дублоны</small><br>";
echo "<br><img width=11 height=11 src=images/e_plus.gif id=np_img style='cursor:pointer' onclick='np_expand()'>&nbsp;Наложенный платеж<br>";
echo "<div id=np_div style='display:none'><small>Вы можете запросить у получателя некоторую сумму за получение письма.</small><br><table cellspacin=0 cellpadding=0 border=0><tr><td>Стоимость:&nbsp;</td><td><input type=text class=btn40 id=np1 value=0 onkeyup='refresh_attachments( )'></td></tr><tr><td>Дней до возврата:&nbsp;</td><td><input type=text class=btn40 id=np2 value=5></td></tr></table></div>";
echo "<br><b>Вложения:</b><br>Дублоны: <input onkeyup='refresh_attachments( )' type=text id=money value=0 class=btn40><br><div id=attachments>&nbsp;</div>";

echo "</td></tr></table>";

?>
</div>
