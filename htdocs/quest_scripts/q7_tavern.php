<input onkeyup='q7_key(event)' class='edit_box' id='q7_in'> <button class='ss_btn' onclick='q7_say();'>�������</button>

<div id=chat>
&nbsp;
</div>
<br>

<script>

var q7_messages = [
	'� �� ���� ���������, ���������, ���������! �-�-�-�, �� ���� ���������, ���������, ���!',
	'��� �� ������ ������ ��� � ��������, ��� � ��������! �������� ��� ���� ������ ��� � �������!',
	'������ �� ������� � �������� �����! ��� �����, ��� ��� �������� ��� ����...',
	'�� ��������! �������� �� � ���� ������� �� ��������!',
	'������� ����� ����� ����� "�" ������������ ���� ��� ���������',
	'���� ������� � ������� ��������,��� ������ ����� �����:������ �� ���� ���������,�� ����� ��� �������.',
	'��, �� ���� �������� ?',
	'����� �� ������� ��� ���� �������� .. � ������ ��� ��� ?',
	'��� ������� ���� ��� �������� ������: ��� ������-������ � �����������!�',
	'"����� �������� �����!". �����.'
];

var q7_logins = [
	'����������',
	"vertex",
	"Himani",
	"NoFace",
	"OlDo",
	"������",
	"Ariel"
];

var q7_msgs = ['', '', '', '', '', '', '', '', '', ''];

q7_key = function(e)
{
	e = e || window.event;
	if( e.keyCode == 13 )
		q7_say( );
}

q7_say = function()
{
	q7_add( q7_login, _( 'q7_in' ).value );
	_( 'q7_in' ).value = "";
}

q7_add = function(a,b)
{
	if( q7_msgs.length == 10 )
	{
		for( var i = 0; i < 9; ++ i ) q7_msgs[i] = q7_msgs[i + 1];
		q7_msgs.pop( );
	}
	q7_msgs.push( '<b>' + a + '</b>: ' + b );
	q7_refr( );
}

q7_refr = function()
{
	var st = '';
	for( var i in q7_msgs ) st = q7_msgs[i] + "<br>" + st;
	_( 'chat' ).innerHTML = st;
}

q7_tmr = function( ) {
	q7_add( q7_logins[Math.floor(Math.random() * q7_logins.length)], q7_messages[Math.floor(Math.random() * q7_messages.length)] );
}

q7_tmr( );
setInterval( q7_tmr, 10000 );

</script>
