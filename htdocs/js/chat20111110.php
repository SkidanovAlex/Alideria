var private_rooms = new Array( );
var new_messages = new Array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 );
var new_my_messages = new Array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 );
var cur_private = 0;
var msgs = new Array( );
var msgn = new Array( );
var n = 0;
var my_login = '';
var my_id = 0;
var saved_message = '';
var chat_timeout = 10000;
var awayMessage = '';
var awayUsersKnow = [];

function ge( b, a )
{
	return b.document.getElementById( a );
}

function refresh_params( )
{
	var st = '<table ceppspacing="0px" cellpadding="1px" border="0px"><tr>';
	for( i = 0; i < window.n; i ++ )
	{
		var chatRoomTitle = '';
		var chatRoomBackground = '';

		// ��������� ��������� ������ ����
		if( window.cur_private == i )
		{
			chatRoomBackground = '#ffffff';
			chatRoomTitle = '<nobr><b>' + window.private_rooms[i] + '</b>';
		}
		else
		{
			if( window.new_my_messages[i] ||
				 ( window.new_messages[i] && window.private_rooms[i] != '�����' && window.private_rooms[i] != '��������' && window.private_rooms[i] != '�����' && window.private_rooms[i] != '���������' && window.private_rooms[i] != '��� - ���' && window.private_rooms[i] != '��� - ����' && window.private_rooms[i].indexOf( '#' ) != 0 ) 
			  )
			{
				chatRoomBackground = '#e0c3a0 url(images/misc/blink-white.gif)';
			}
			else
			{
				chatRoomBackground = '#e0c3a0';
			}
			var chatRoomLastMessage = ''; 
			if( typeof( window.msgs[i][window.msgs[i].length - 1] ) != 'undefined' )
			{
				chatRoomLastMessage = window.msgs[i][window.msgs[i].length - 1].replace( /<.*?>/g, '' );
			}
			else
			{
				chatRoomLastMessage = '� ���� �����';
			}
			chatRoomTitle = '<nobr><a href="javascript://" onclick="window.parent.setPrivate( ' + i + ' )" title="' + chatRoomLastMessage + '">' + window.private_rooms[i] + '</a>';
		}
		
		// ��������� ���������� ������������� ��������� ��� ������ ����
		var chatNewMessages = '';
		if( window.cur_private != i )
		{
			if( window.new_messages[i] || window.new_my_messages[i] )
			{
				chatNewMessages = ' (<a href="javascript://" onclick="window.parent.nulledUnreadMessages( ' + i + ', true );" title="�������� ��� ���������, ��� �����������">' + window.new_messages[i] + '</a>';

				if( window.new_my_messages[i] > 0 )
				{
					chatNewMessages += '/<a href="javascript://" onclick="window.parent.nulledUnreadMessages( ' + i + ' );" title="�������� ������� ���������, ��� �����������">' + window.new_my_messages[i] + '</a>';
				}

				chatNewMessages += ')';
			}
		}
		
		st += '<td style="border: 1px solid black; background: ' + chatRoomBackground + ';">' + chatRoomTitle + chatNewMessages + '&nbsp;&nbsp;<img src="images/x.gif" style="width: 11px; height: 11px; border: 0px; cursor: pointer;" onclick="window.top.closePrivate( ' + i + ' );" title="�������" /></td>';
	}
	st += '</tr></table>';
		
	window.ge( window.chat_at, 'privates' ).innerHTML = st;
}

// ��������� ��������� ������������� ���������
function nulledUnreadMessages( chatId, allFlag )
{
	window.new_my_messages[chatId] = 0;
	if( allFlag == true )
		window.new_messages[chatId] =0;
	
	window.refresh_params( );
}

function refresh_chat( )
{
	chat.refr( );
}

function cleanPrivates( )
{
	n = cur_private = 0;
	parent.createPrivateRoom( '�����' );
}

function setPrivate( a )
{
	cur_private = a;
	new_messages[a] = false;
	new_my_messages[a] = false;

	if( ge( chat, 'ch' ) )	 // ���� ��� ��������
	{
		chat.msgn = msgn[a];
		for( i = 0; i < msgn[a]; ++ i )
			chat.msgs[i] = msgs[a][i];
		
		refresh_chat( );
	}
	
	refresh_params( );
}

function getPrivateRoom( a )
{
	for( i = 0; i < n; ++ i )
		if( private_rooms[i] == a )
			return i;
		
	msgn[n] = 0;
	msgs[n] = new Array( );
	private_rooms[n] = a;

	if( a == 'undefined' )
	{
		msgs[n][0] = '<span style="padding-left:10px;">������� ����������� ������� �� ������:<ul style="margin-top:5px;margin-bottom:3px;"><li>����, ������, ������� � �������� - <a href="/forum.php?room=1" target="_blank">����</a>.</li><li>����, �����������, �������, ����� - <a href="/forum.php?room=5" target="_blank">����</a>.</li><li>������� � ������� ������� � ���, ��� � ��� ������� - <a href="/forum.php?room=3" target="_blank">����</a>.</li></ul></span>';
		msgn[n] = 1;
	}
	if( a == '�������' )
	{
		msgs[n][0] = '<span style="padding-left:40px;">��������� ����� ! �� ������� ������ � ��������������� �������.</span><ul style="margin-top:5px;margin-bottom:3px;"><li>����������, �� ����������� �� ��� � �������� ���������, �� ������� ��� ����� ���� ����� � ����� ���� ��� �� ������.</li><li>����������, �� ����������� �� ��� � ���������, ���������� � ����� ��� �������� � ���� � ���� ���������� ������������� <a href="javascript://" onclick="parent.closePrivateNamed( \'�������\' );parent.createPrivateRoom( \'undefined\' );parent.chat_in.Cursor();">undefined</a>.</li><li>����������� �� ��� ������ � ��������� ������ �����, ����������, ����������, �������� �������, � ����� �� ���������� � ���������, �� �� �� ������� ��������.</li></ul>';
		msgn[n] = 1;
	}
	if( a == '���' )
	{
		msgs[n][0] = '������� ������� �����, �����! =) ��� ������ ��� ���, �������� �������������� ��������.<br>� ���� �������� �� �������, ��������� � ����������, �������� � �������, ������� � ���������, � ����� �� ������ ��������� ������� �������. ��������� ���� ������ ����� ��, �� ������ ���� ������� �� �����������: � ������ �� ��������� ��� ����� �������.<br>���� �� ������ �������� �� ������ ����� ���������� ����� ��� ��������, �������� ��������� <a href=player_info.php?nick=%CF%EB%E0%EC%E5%ED%E8 target=_blank><b>�������</b></a>.';
		msgn[n] = 1;
	}
	if (a == 'Reincarnation')
	{
		msgs[n][0] = '<span style="padding-left:40px;">��������� ����� ! �� ������� ������ � ������������� Reincarnation.</span><br> ���� �� ������ ���������� �� ����, ������ �������� ���. � ������, ��� ����� �����. �� ����� ������ "������" � ����� ������. ����� ���������� � ���� �������.<br><br><span style="padding-left:10px;">������� ����������� ������� �� ������:<ul style="margin-top:5px;margin-bottom:3px;"><li>����, ������, ������� � �������� - <a href="/forum.php?room=1" target="_blank">����</a>.</li><li>����, �����������, �������, ����� - <a href="/forum.php?room=5" target="_blank">����</a>.</li><li>������� � ������� ������� � ���, ��� � ��� ������� - <a href="/forum.php?room=3" target="_blank">����</a>.</li></ul></span>';
		msgn[n] = 1;
	}
	if( a == '���������' )
	{
		msgs[n][0] = '����������� ���� � ��� �������.<br>���� �� �������� ���� - ������� ����. ���� �� ���������� ������ �� ���� - � ���� �������� ����� �� ����� ������ ��� ������������ � ���� ����������. <br>��! �� ����� ������ "������, ���-��� � �.�." � ����� ������ - ������ ����� ����� ������ ����� ������. ����� �������� � ���� �������.</b><br><li>� ��� - � �� ����� ������� �������� �� ��������� � ����������!</li></b>';
		msgn[n] = 1;
	}	if( a == '��������' )
	{
		msgs[n][0] = '�� ������� �������� ���. ����� ��������� ������ �������, �������� ��������� � <a target=_blank href=help.php?id=500>�������� ����</a>. �������, ��� �� ������ �������: � �������� ���� ��������� <u>������</u> ������� ����� ��� ����� (������, �������� �����, ��������� ����������). <b>��������� ������ ������ ������� � �������.</b> ���������� ����� ������� ���������� �� ����� ���������, �� ���������� ��������.';
		msgn[n] = 1;
	}
	if( n == 0 && a == '�����' )
	{
		msgs[n][0] = "������ ��� ����������� ������� � ����: <u>http://www.alideria.ru/?r=" + my_id + "</u>. <a target=_blank href=help.php?id=50000>����� ���������!</a>";
		msgn[n] = 1;
	}

	++ n;
	
	return n - 1;
}

function closePrivate( id )
{
	if( private_rooms[id] == '�����' || private_rooms[id] == '�����' || private_rooms[id] == '��������' )
	{
		msgn[id] = 1;
		msgs[id] = new Array( );
		msgs[id][0] = "<center><b><i>��� ������</b></i></center>";
		new_messages[id] = 0;
		new_my_messages[id] = 0;

		if( cur_private == id )
		{
    		chat.msgn = 1;
    		chat.msgs = new Array( );
    		chat.msgs[0] = "<center><b><i>��� ������</i></b></center>";

			refresh_chat( );
		}
		else
			refresh_params( );

		return;
	}

	-- n;

	for( i = id; i < n; ++ i )
	{
		private_rooms[i] = private_rooms[ i + 1 ];
		msgs[i] = msgs[ i + 1 ];
		msgn[i] = msgn[ i + 1 ];
		new_messages[i] = new_messages[ i + 1 ];
		new_my_messages[i] = new_my_messages[ i + 1 ];
	}
	private_rooms[n] = '';
	msgs[n] = new Array( );
	msgn[n] = new Array( );
	new_messages[n] = 0;
	new_my_messages[n] = 0;
		
	if( cur_private >= id )
		setPrivate( cur_private - 1 );

	refresh_params( );
}

function closePrivateNamed( nm )
{
	for( i = 0; i < n; ++ i )
		if( private_rooms[i] == nm ) closePrivate( i );
}

function createPrivateRoom( a )
{
	id = getPrivateRoom( a );
	setPrivate( id );
	refresh_params( );
}

// ��������� ���������
function syst( tm, msg )
{
	channel_id = window.getPrivateRoom( '���������' );

	st = '[' + tm + '] ' + msg;
	window.msgs[channel_id][window.msgn[channel_id] ++] = st;

	if( window.cur_private == channel_id )
	{
		window.chat.msgs[window.chat.msgn ++] = st;
		window.refresh_chat( );
	}
	else
	{
		window.new_messages[channel_id] ++;
	}

	window.refresh_params( );
}

// ���������� ��������� � ���
function msg( id, tm, author, msg, channel, nick_clr, text_clr )
{
	channel_id = window.getPrivateRoom( channel );

	// �������� ��������� �����������
	if( msg.substr( 0, 5 ) == '/del ' && msg.length > 10 )
	{
		var q = msg.substr( 5 );
		for( var i = 0; i < n; ++ i )
			for( var j = 0; j < msgn[i]; ++ j )
				if( msgs[i][j].indexOf( q ) != -1 )
					msgs[i][j] = '<i>��������� ������� �����������</i>';
		for( var j = 0; j < window.chat.msgn; ++ j )
			if( window.chat.msgs[j].indexOf( q ) != -1 )
				window.chat.msgs[j] = '<i>��������� ������� �����������</i>';
			
		return;
	}

	// �����-�� ��������� ������� �� ���. ��������, ������ ��� � ���� ���������� ��������� ����� � ���������
	// �������� ��������� ������� � ������������ �������� �������� �������� � �����������.
	if( id != 0 && window.chat.lid > id )
		return;
	if( id != 0 ) 
		window.chat.lid = id;

	// ������
	var overTagsA = [ '(�)', '(!�)', '(�)', '(!�)', '(�)', '(!�)', '(�)', '(!�)' ];
	var overTagsB = [ '<i>', '</i>', '<u>', '</u>', '<s>', '</s>', '<b>', '</b>' ];

	for( i = 0; i < overTagsA.length; i ++ )
	{
		while( msg.indexOf( overTagsA[i] ) != -1 )
		{
			msg = msg.replace( overTagsA[i], overTagsB[i] );

			if( overTagsB[i][1] != '/' )
				msg += overTagsB[ i + 1 ];
		}
	}

	// ������ ������ ���������
	msg = msg.replace( /(http\:\/\/)([^ ><\"\'\)\(]+)/gi, '<a href="$1$2" target="_blank">$1$2</a>' );


	// ������� �� ���������, ������������ ���������
	if( author != window.my_login && msg.indexOf( window.my_login + ',' ) != -1 )
	{
		// ��������� ��������� �������
		tm = '<span style="background-color:red">' + tm + '</span>';
		
		// ��������� �������� ������ ���������
		if( channel_id != window.cur_private )
		{
			window.new_my_messages[channel_id] ++;
		}
	}

	// ������������
	if( awayMessage.length > 0 && author != window.my_login && typeof( awayUsersKnow[author] ) == 'undefined' &&
		( msg.indexOf( window.my_login + ',' ) != -1 ||
			 ( channel != '��� - ����' && channel != '��� - ���' && channel != '�����' && channel != '��������' && channel != '�����' && channel.indexOf( '@' ) != 0 && channel.indexOf( '#' ) != 0 )
			)
		)
	{
		// �������� �� ��������� ������� ������������� ������
		window.chat.query( '/chat_say.php?msg=' + encodeURIComponent( awayMessage ) + '&where=' + encodeURIComponent( author ), '' );
			
		// ��������, ��� ������ ������� ��� ������� ��������� � ����� ��� �������� �� ���������
		awayUsersKnow[author] = true;
	}

	if( msg.indexOf( '/me ' ) == 0 )
		st = '[' + tm + ']&nbsp;' + '<i><font style="cursor: pointer" onclick="parent.chat_who.nick(\'' + author + '\')" ondblclick="window.top.dblClickCreatePrivate( \'' + author + '\' );" onmouseout="menu_mout()" onmouseover="menu_mover()" oncontextmenu="show_menu(\'' + author + '\', event)">' + window.iin( author ) + "</font>" + msg.slice( 3 ) + '</i>';
	else
		st = '[' + tm + ']&nbsp;' + '<font color=#' + nick_clr + ' style="cursor: pointer" onclick="parent.chat_who.nick(\'' + author + '\')" ondblclick="window.top.dblClickCreatePrivate( \'' + author + '\' );" onmouseout="menu_mout()" onmouseover="menu_mover()" oncontextmenu="show_menu(\'' + author + '\', event)"><b>' + window.iin( author ) + ':&nbsp;</b></font><font color=#' + text_clr + '>' + msg + '</font>';
		
	window.msgs[channel_id][window.msgn[channel_id] ++] = st;
	if( window.cur_private == channel_id )
	{
		window.chat.msgs[window.chat.msgn ++] = st;
		window.refresh_chat( );
	}
	else
		window.new_messages[channel_id]++;

	window.refresh_params( );
}

function dblClickCreatePrivate( username )
{
	createPrivateRoom( username );

	window.chat_in.document.getElementById( 'inp' ).value = '';
}

function upal( )
{
	saved_message = window.top.chat_in.document.getElementById( 'inp' ).value;
	window.top.chat_in.document.getElementById( 'inp' ).disabled = true;
	window.top.chat_in.document.getElementById( 'sayButton' ).disabled = true;
	window.top.chat_in.document.getElementById( 'inp' ).value = '������ ���� �������� ����������';
}

function ok( )
{
	if( saved_message == '������ ���� �������� ����������' )
	{
		saved_message = '';
	}

	window.top.chat_in.document.getElementById( 'inp' ).disabled = false;
	window.top.chat_in.document.getElementById( 'sayButton' ).disabled = false;
	if( window.top.chat_in.document.getElementById( 'inp' ).value == '������ ���� �������� ����������' )
	{
		window.top.chat_in.document.getElementById( 'inp' ).value = saved_message;
	}
}