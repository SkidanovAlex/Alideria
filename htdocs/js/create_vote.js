var answers = [];

document.write( '<b>���������� �����������:</b><br><input class=m_btn name=nm value="������"><br><input type=checkbox name=closed> �������� ����������� (�� ����� ������ ���������������)<br><br><b>������:</b><div id=votes>&nbsp;</div><br><a href="javascript:sib(-1)">�������� �����</a><br><div id=add_inputs>&nbsp;</div><br><small>��� ������� �������� ������ �� ������ ������� ��������� �������� �������. � ���� ������ ������ �� ������ ���������� �� ��� �������, � ������ �� ��� �������� ������, � �� ������ ����� ����������� ��� ��� ��������� ������, ��� � ��� ������������� ������. ��������, � �����������<br><b>�������� �� ��� ��������</b><br> - ��<br>&nbsp;&nbsp;&nbsp; - ��, ������ ��� � ��� ������ ������ �������;<br>&nbsp;&nbsp;&nbsp; - ��, ������ ��� ��� ������ ������ ���� �� �����;<br> - ���<br>&nbsp;&nbsp;&nbsp; - ���, ������ ��� ��� ������� ������ ��� ����;<br>&nbsp;&nbsp;&nbsp; - ���, ������ ��� � ������ ���� ������������;<br>������ �� ������ ������������� ������ �� ��� ���, ������ ����� ����� ������ ������ �� �������� ������, ��� ���� ����� �� ������� &laquo;��, ������ ��� ��� ������ ������ ���� �� �����;&raquo; ������������� �������� � �� ������� &laquo;��&raquo;</small>' );

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
		ret += prep + '<b>' + answers[i][0] + '</b> (<a href="javascript:sib(' + i + ')">�������� ���-�����</a> | <a href="javascript:del(' + i + ',1)">�������</a>)<br>'
		ret += rec( i, prep + '&nbsp;&nbsp;&nbsp;' );
	}
	return ret;
}

function sib( id )
{
	var q;
	if( answers.length == 20 ) alert( '����������� �� ����� ��������� ������ 20 �������' );
	else if( q = prompt( '������� ����� ������:' ) )
	{
		answers.push( [q,id] );
		refr( );
	}
}

function del( id, conf )
{
	if( conf && !confirm( '������� ������� ������?' ) ) return;
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
	if( answers.length == 0 ) st = '<i>��� �������</i>';
	_( 'votes' ).innerHTML = st;
}

refr( );
