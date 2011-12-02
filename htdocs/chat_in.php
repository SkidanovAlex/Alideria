<?

include_once( "functions.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player_id = $HTTP_COOKIE_VARS['c_id'];

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">


<table cellpadding=0 cellspacing=0 border=0 width=100%>
<tr>
<td width=5 bgcolor=#e0c3a0 background="images/chat/chat_corner_2.gif"><img src="empty.gif" width=5 height=5></td>
<td width=100% bgcolor=#e0c3a0 background="images/chat/chat_border_bottom.gif"><img src="empty.gif" width=5 height=5></td>
<td width=5 bgcolor=#e0c3a0 background="images/chat/chat_corner_3.gif"><img src="empty.gif" width=5 height=5></td>
<td width=17 bgcolor=#e3ac67 background="images/bg.gif"><img src="empty.gif" width=17 height=5></td>
<td width=5 bgcolor=#e0c3a0 background="images/chat/chat_corner_2.gif"><img src="empty.gif" width=5 height=5></td>
<td width=223 bgcolor=#e0c3a0 background="images/chat/chat_border_bottom.gif"><img src="empty.gif" width=223 height=5></td>
<td width=5 bgcolor=#e0c3a0 background="images/chat/chat_corner_3.gif"><img src="empty.gif" width=5 height=5></td>
<td width=17 bgcolor=#e3ac67 background="images/bg.gif"><img src="empty.gif" width=17 height=5></td>
</tr>
</table>


<table width=100% cellpading=0 cellspacing=0 height=30>
<tr>
<td background="images/chat/line_bottom.gif" valign=middle>

	<table width=100% cellpading=0 cellspacing=0>
	<tr><td width=20><a onclick='smiles()' style='cursor:pointer' title='Вставить Смайлик'><img width=19 height=19 border=0 src=images/insert_smiley.png></a></td>
	<td>
		<input class=edit_box type=text name=inp id=inp class=te_btn style='width: 100%' maxlength=200>
	</td>
	<td width=82>
		<button class=ss_btn onClick='say( );' id="sayButton">Сказать</button>
	</td>
	<td width=65 align=right>
	<div id=clock name=clock>&nbsp;</div>
	</td>
	</table>

</td>
</tr>
</table>

<script>var cctm=0;var ccst=0;function start_clock(a){cctm=a;d0=new Date();ccst=Math.round(d0.getTime()/1000-0.5);document.getElementById('clock').style.display='';process_clock();}
function process_clock(){var d1=new Date();var cur=Math.round(d1.getTime()/1000-0.5);var tt=cctm+cur-ccst;var h=0+Math.round((tt/3600)%24-0.5);var m=0+Math.round((tt/60)%60-0.5);var s=0+tt%60;
document.getElementById('clock').innerHTML='&nbsp;<b>'+((h<10)?'0':'')+h+':'+((m<10)?'0':'')+m+':'+((s<10)?'0':'')+s+'</b>&nbsp;';setTimeout('process_clock();',1000);}
var a = <? $tm = time( ); print( date( 'H', $tm ).' * 3600 + '.date( 'i', $tm ).' * 60 + '.date( 's' ) ); ?>;
start_clock( a );

var msgs = new Array( );
var num = 0;
var cur = 0;
var moo = '';
var window_smiles = null;

function Cursor( )
{
	var el = document.getElementById( 'inp' );
	if (el.createTextRange)
	{
		var r = el.createTextRange();
		r.collapse(false);
		r.select();
	}
	else if(el.selectionStart)
	{
		var end = el.value.length;
		el.setSelectionRange(end,end);
		el.focus();
	}
}

function hist_push( a )
{
	msgs[num ++] = a;
	cur = num;
}
function hist_get( a )
{
	if( cur < num )
		return msgs[a];
	else
		return moo;
}

function smiles()
{
	if (window_smiles != null && !window_smiles.closed)
		window_smiles.focus();
	else
		window_smiles = window.open( 'smiles.php','wsm','toolbar=no,status=no,menubar=no,scrollbars=yes,width=600,height=380,resizeble=no' );
}

function smile_call_back( s )
{
	document.getElementById( 'inp' ).value += s;
	Cursor( );
}

function ch_sett( )
{
	window.open('ch_settings.php','_blank','scrollbars=no,width=400,height=270,resizable=no');
}

function say( )
{
	var message = document.getElementById( 'inp' ).value;
	var savedMessage = message;
	var channel = window.top.private_rooms[window.top.cur_private];	
	

	document.getElementById( 'inp' ).value = '';

	// Пустое сообщение мы не отправляем
	if( message == '' )
	{
		return;
	}

	// Команды клиента
	if( message[0] == '/' )
	{
		command = message.split( ' ' );
		switch( command[0] )
		{
			case '/private':
			{
				window.top.createPrivateRoom( command[1] );
				Cursor( );
				
				return;
				break;
			}
			case '/away':
			{
				awayMessage = command[1];
				
				for( i = 1; i < command.length; i ++ )
					window.top.awayMessage += ' ' + command[i];
				
				window.top.awayUsersKnow = new Array( );
				
				alert( 'Включён автоответчик:\n\n' + window.top.awayMessage );
				
				return;
				break;	
			}
			case '/aviable':
			{
				window.top.awayMessage = '';
				
				alert( 'Автоответчик отключён' );
				
				return;
				break;
			}
			case '/info':
			{
				if( command[1] !== undefined )
				{
					open( "/player_info.php?nick=" + command[1] );
				}
				else
				{
					open( "/player_info.php?nick=" + channel );
				}
				
				return;
				break;
			}
		}
	}

	// Отключение автоответчика, если он был включён
	if( window.top.awayMessage.length > 0 )
	{
		window.top.awayMessage = '';
	}


	// Запоминаем сообщение в историю
	window.top.chat_in.hist_push( message );
	
	// Отправляем сообщение в чат
	window.top.chat_ref.location.href = '/chat_say.php?msg=' + encodeURIComponent( message ) + '&where=' + encodeURIComponent( channel );
}

function say_key( e )
{
	e = e || window.event;
	if( e.keyCode == 13 )
		say( );
	else if( e.keyCode == 38 )
	{
		if( cur == num )
			moo = document.getElementById( 'inp' ).value;
		-- cur;
		if( cur < 0 )
			cur = 0;
		document.getElementById( 'inp' ).value = hist_get( cur );
	}
	else if( e.keyCode == 40 )
	{
		cur ++;
		if( cur > num )
			cur = num;
		else
			document.getElementById( 'inp' ).value = hist_get( cur );
	}
}
var Chars = 'АБЦДЕФГХИЙКЛМНОПЭРСТЫВЩХУЗ      абцдефгхийклмнопэрстывщхуз';	
function trans( e )
{
	if( !translit )
		return;
	e = e || window.top.event;
	if( !e.ctrlKey && !e.altKey )
	{
		msg = document.getElementById( 'inp' ).value;
		if( e.keyCode > 64 && e.keyCode < 123 )
		{
			msg += Chars.charAt( e.keyCode - 65 + ( ( e.shiftKey === "undefined" || !e.shiftKey ) ? 32 : 0 ) );
			msg = msg.replace( /УА/, 'Я' );
			msg = msg.replace( /Уа/, 'Я' );
			msg = msg.replace( /уа/, 'я' );
			msg = msg.replace( /ЦХ/, 'Ч' );
			msg = msg.replace( /Цх/, 'Ч' );
			msg = msg.replace( /цх/, 'ч' );
			msg = msg.replace( /СХ/, 'Ш' );
			msg = msg.replace( /Сх/, 'Ш' );
			msg = msg.replace( /сх/, 'ш' );
			msg = msg.replace( /ЗХ/, 'Ж' );
			msg = msg.replace( /Зх/, 'Ж' );
			msg = msg.replace( /зх/, 'ж' );
			msg = msg.replace( /ЫЫ/, 'Ю' );
			msg = msg.replace( /Ыы/, 'Ю' );
			msg = msg.replace( /ыы/, 'ю' );
		}
		else if( e.keyCode == 39 )
			msg += 'ь';
		else if( e.keyCode == 34 )
			msg += 'ъ';
		else
			return true;
		document.getElementById( 'inp' ).value = msg;
		return false;
	}
}
<?
	include_once( 'player.php' );
	$player = new Player( $player_id );
	if( $player->HasTrigger( 322 ) )
		echo "translit = true;";
	else
		echo "translit = false;";
?>	
document.getElementById( 'inp' ).onkeydown = trans;
document.getElementById( 'inp' ).onkeyup = say_key;
</script>
