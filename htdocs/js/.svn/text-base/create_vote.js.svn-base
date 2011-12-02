var answers = [];

document.write( '<b>Добавление голосования:</b><br><input class=m_btn name=nm value="Вопрос"><br><input type=checkbox name=closed> Закрытое голосование (не видны списки проголосовавших)<br><br><b>Ответы:</b><div id=votes>&nbsp;</div><br><a href="javascript:sib(-1)">Добавить ответ</a><br><div id=add_inputs>&nbsp;</div><br><small>Для каждого варианта ответа вы можете создать несколько дочерних ответов. В этом случае игроки не смогут голосовать за сам вариант, а только за его дочерние ответы, и их голоса будут учитываться как для дочернего ответа, так и для родительского ответа. Например, в голосовании<br><b>Нравится ли вам Алидерия</b><br> - Да<br>&nbsp;&nbsp;&nbsp; - Да, потому что в ней лучшая боевая система;<br>&nbsp;&nbsp;&nbsp; - Да, потому что это просто лучшая игра на свете;<br> - Нет<br>&nbsp;&nbsp;&nbsp; - Нет, потому что она слишком хороша для меня;<br>&nbsp;&nbsp;&nbsp; - Нет, потому что я просто хочу навредничать;<br>игроки не смогут проголосовать просто Да или Нет, голоса можно будет отдать только за дочерние ответы, при этом голос за вариант &laquo;Да, потому что это просто лучшая игра на свете;&raquo; автоматически зачтется и за вариант &laquo;Да&raquo;</small>' );

function create_vote( )
{
	var st = '';
	for( i in answers ) st += '<input type=hidden name=ans' + i + ' value="' + answers[i][0] + '">';
	for( i in answers ) st += '<input type=hidden name=prn' + i + ' value="' + answers[i][1] + '">';
	_( 'add_inputs' ).innerHTML = st;
	document.vfrm.submit( );
}
                                                                                                           
function rec( id, prep )
{
	var ret = '';
	for( var i in answers ) if( answers[i][1] == id )
	{
		ret += prep + '<b>' + answers[i][0] + '</b> (<a href="javascript:sib(' + i + ')">Добавить под-ответ</a> | <a href="javascript:del(' + i + ',1)">Удалить</a>)<br>'
		ret += rec( i, prep + '&nbsp;&nbsp;&nbsp;' );
	}
	return ret;
}

function sib( id )
{
	var q;
	if( answers.length == 20 ) alert( 'Голосование не может содержать больше 20 ответов' );
	else if( q = prompt( 'Введите текст ответа:' ) )
	{
		answers.push( [q,id] );
		refr( );
	}
}

function del( id, conf )
{
	if( conf && !confirm( 'Удалить вариант ответа?' ) ) return;
	for( var i = id + 1; i < answers.length; ++ i )
	{
		if( answers[i][1] > id ) -- answers[i][1];
		answers[i - 1] = answers[i];
	}
	answers.pop( );
	for( var i = answers.length - 1; i >= 0; -- i ) if( answers[i][1] == id )
		del( i, false );
	refr( );
}

function refr( )
{
	var st = '';
	st += rec( -1, '' );
	if( answers.length == 0 ) st = '<i>Нет ответов</i>';
	_( 'votes' ).innerHTML = st;
}

refr( );
