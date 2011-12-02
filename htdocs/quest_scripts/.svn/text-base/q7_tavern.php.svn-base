<input onkeyup='q7_key(event)' class='edit_box' id='q7_in'> <button class='ss_btn' onclick='q7_say();'>Сказать</button>

<div id=chat>
&nbsp;
</div>
<br>

<script>

var q7_messages = [
	'А ма либэ БезПонтов, БезПонтов, Безпонтов! А-а-а-а, ма либэ БезПонтов, БезПонтов, Тов!',
	'Как же весело сидеть нам в харчевне, нам в харчевне! Отдыхать нам всей толпой как в деревне!',
	'Выпьем за честных и скромных людей! Тем более, что нас осталось так мало...',
	'До свидания! Трезвыми мы с вами сегодня не увидимся!',
	'Настало время когда буква "ы" используется чаще чем остальные',
	'Есть женщины в русских селеньях, их бабами нежно зовут: слона на бегу остановят, и хобот ему оторвут.',
	'Эй, ты меня уважаешь ?',
	'Какая же гадость это ваша заливная .. а кстати что это ?',
	'Как говорил один мой знакомый людоед: «На первое-второе — рассчитайсь!»',
	'"Время собирать камни!". Почка.'
];

var q7_logins = [
	'суперРыжик',
	"vertex",
	"Himani",
	"NoFace",
	"OlDo",
	"Морфий",
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
