function my_word_str( num, a1, a2, a5 )
{
	if( num % 10 == 0 ) return a5;
	if( num % 100 >= 10 && num % 100 <= 19 ) return a5;
	if( num % 10 == 1 ) return a1;
	if( num % 10 >= 5 ) return a5;
	return a2;
}

function clog(arr)
{
	if( arr[0] == 0 ) return arr[1];
	if( arr[0] == 1 )
	{
		var clrs = new Array( "blue", "green", "red" );
		var arr0 = new Array( "����", "�������", "�����" );
		var arr1 = new Array( "����", "�������", "����" );
		var arr2 = new Array( "������ ����", "������ �������", "�������� ������" );
		var arr3 = new Array( "������ ����", "������ �������", "��������� ������" );
		var arr4 = new Array( "���� ����", "���� �������", "�������� ����" );
		var login1 = "<b>" + arr[1] + "</b>";
		var login2 = "<b>" + arr[2] + "</b>";
		var q = arr[3]; var genre1 = arr[4]; var genre2 = arr[5];

		var st;
		if( genre1 == genre2 )
		{
			if( q == 1 ) st = login1 + " � " + login2 + " ���������� � <b><font color=" + clrs[genre1] + ">" + arr3[genre1] + "</font></b>. ������ �� �������� ���������� � ������������.";
			if( q == 2 ) st = login1 + " � " + login2 + " ������� ������ <b><font color=" + clrs[genre1] + ">" + arr1[genre1] + "</font></b>, ������ �� ����������� � ������������.";
			if( q == 3 ) st = login1 + " � " + login2 + " �������� ������������ � �������� <b><font color=" + clrs[genre1] + ">" + arr4[genre1] + "</font></b>. �������� �� ����� ���������� � ������������.";
		}
		else
		{
			if( genre2 == (genre1 + 1) % 3 )
			{
				t = genre1;
				genre1 = genre2;
				genre2 = t;
				t = login1;
				login1 = login2;
				login2 = t;
			}
		
			if( q == 1 ) st = login2 + " �������� ������������ � ��������� " + arr4[genre2] + ", �� ������ ��������� �� ����� " + arr1[genre1] + " " + login1 + ".";
			if( q == 2 ) st = "� �������������� " + arr1[genre2] + " " + login2 + " � " + arr1[genre1] + " " + login1 + " ���������� ������ " + arr0[genre1] + ".";
			if( q == 3 ) st = arr2[genre2] + " " + login2 + " �������� " + arr3[genre1] + " " + login1 + ".";
		}
		return st + '<br>';
	}
	if( arr[0] == 2 )
	{
		clrs = new Array( "blue", "green", "red" );
		arr1 = new Array( "����", "�������", "����" );

		return "<b>" + arr[1] + "</b> �������� " + arr[2] + " ����������� <b><font color=" + clrs[arr[3]] + ">������ " + arr1[arr[3]] + "</font></b><br>";
	}
	if( arr[0] == 3 ) return "<b>" + arr[1] + "</b> �� ������� ����������<br>";
	if( arr[0] == 4 ) return "<b>" + arr[1] + "</b> ������� ���������� " + arr[2];
	if( arr[0] == 5 ) return "<b>" + arr[1] + "</b> �������� ������������ �����, �� �� ������ ��� �� ������ ������";
	if( arr[0] == 6 ) return "<br><b>" + arr[1] + "</b> ��������� ������ �������� � ��������������� " + arr[2] + " ��������!";
	if( arr[0] == 7 ) return "<b>" + arr[1] + "</b> ������� ���������� " + arr[2] + ". <b>" + arr[1] + "</b> ������� ���������� " + arr[2] + " ��� ���!!!";
	if( arr[0] == 8 )
	{
		var arr1 = new Array( '����', '�������', '����' );
		var ret = "<b>" + arr[1] + "</b> ������� " + arr[2] + " " + my_word_str( arr[2], '�������', "�������", "������" ) + ' ����������� ������ ' + arr1[arr[3]];
		if( arr[4] ) ret += " ����������� ������!!!";
		return ret;
	}
	if( arr[0] == 9 ) return "<br>";
	if( arr[0] == 10 ) return "<b>" + arr[1] + "</b> �������� ���������� ����������!!!<br>";
	if( arr[0] == 11 ) return "<b>" + arr[1] + "</b> �� ������� ���������� � �������� �� <b>" + arr[2] + "</b><br>";
	if( arr[0] == 12 ) return "<b>" + arr[1] + "</b> �� ����� ��������� ����������<br>";
	if( arr[0] == 13 ) return "<b>" + arr[1] + "</b> ������ <font color=red><b>" + arr[2] + "</b></font> ������.<br>";
	if( arr[0] == 14 ) return "<b>" + arr[1] + "</b> ��������������� <font color=blue><b>" + arr[2] + "</b></font> ������.<br>";
	if( arr[0] == 15 ) return "<b>" + arr[1] + "</b> ������ �� ��������� ���� <b>" + arr[2] + "</b><br>";
	if( arr[0] == 16 ) return "<font color=saddlebrown>";
	if( arr[0] == 17 )
	{
		var id = 1;
		var ret = "";
		if( arr[id] != -1 )
		{
			ret += "<b>" + arr[id] + "</b>&nbsp;[" + arr[id + 1] + "/" + arr[id + 2] + "]";
			id += 3;
		}
		else
		{
			ret += '<i>�����</i>';
			id += 1;
		}
		ret += " vs ";
		if( arr[id] != -1 )
		{
			ret += "<b>" + arr[id] + "</b>&nbsp;[" + arr[id + 1] + "/" + arr[id + 2] + "]";
			id += 3;
		}
		else
		{
			ret += '<i>�����</i>';
			id += 1;
		}
		return ret + "<br>";
	}
	if( arr[0] == 18 ) return "</font>";
	if( arr[0] == 19 ) return "<font color=red><b>" + arr[1] + "</b> ����������� �����</font><br>";
	if( arr[0] == 20 ) return "<img id=pair_img" + arr[1] + " onClick='upload_pair( " + arr[1] + " );' width=11 height=11 src=images/e_plus.gif>&nbsp;<u><font color=steelblue><b>" + arr[2] + "</b> ������ <b>" + arr[3] + "</b></font></u><br><div id=pair_div" + arr[1] + " style='display: none'><i>���������, ���� ��������</i></div>";
	if( arr[0] == 21 ) return "<img id=pair_img" + arr[1] + " onClick='upload_pair( " + arr[1] + " );' width=11 height=11 src=images/e_plus.gif>&nbsp;<u><font color=steelblue><b>" + arr[2] + "</b> ��� ���������</font></u><br><div id=pair_div" + arr[1] + " style='display: none'><i>���������, ���� ��������</i></div>";
	if( arr[0] == 22 ) return "<b>" + arr[1] + "</b> ���������� �����!<br>";
	if( arr[0] == 23 ) return "<img id=pair_img" + arr[1] + " onClick='upload_pair( " + arr[1] + " );' width=11 height=11 src=images/e_plus.gif>&nbsp;<u><font color=steelblue><b>���������� ���</b></font></u><br><div id=pair_div" + arr[1] + " style='display: none'><i>���������, ���� ��������</i></div>";
	if( arr[0] == 24 ) return "<b>" + arr[1] + "</b> ��������� ��� ���� � �������� �������� x10 ����<br>";
	if( arr[0] == 25 ) return "<b>" + arr[1] + ". <font color=khaki>" + arr[2] + "</b></font><br>";
	if( arr[0] == 26 ) return "<table cellspacing=0 cellpadding=0 border=0 style='width:100%;height:20px;' width=100%><tr style='height:5px'><td><img width=1 height=5 src=empty.gif border=0></td></tr><tr style='height:10px'><td background='images/misc/divider.gif'><img width=1 height=10 src=empty.gif border=0></td></tr><tr style='height:5px'><td><img width=1 height=5 src=empty.gif border=0></td></tr></table>";
	if( arr[0] == 27 ) return "<font color=blue>";
	if( arr[0] == 28 ) return arr[1] + " (<b>" + arr[2] + "</b>) ������� <b>" + arr[3] + "</b>, <b>" + arr[3] + "</b> �� �������� �����������!<br>";
	if( arr[0] == 29 ) return arr[1] + " (<b>" + arr[2] + "</b>) ������� <b>" + arr[3] + "</b>, <b>" + arr[3] + "</b> �������� " + arr[4] + " �����������!<br>";
	if( arr[0] == 30 ) return arr[1] + " (<b>" + arr[2] + "</b>) ������� " + arr[3] + " (<b>" + arr[4] + "</b>), " + arr[3] + " (<b>" + arr[4] + "</b>) �������� " + arr[5] + " �����������!<br>";
	if( arr[0] == 31 ) return " " + arr[1] + " (<b>" + arr[2] + "</b>) �������.<br>";
}

function c_log( arr )
{
	var st = '';
	for( i in arr ) if( arr[i] ) st += clog( arr[i] );
	return st;
}
