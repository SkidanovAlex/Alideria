<?

function add_noob_js( )
{
	echo "var n_els = new Array( );\n";
	echo "var n_pars = new Array( );\n";
	echo "function n_clear( ) { hideTooltip(); for( var i = 0; i < n_els.length; ++ i ) n_pars[i].removeChild( n_els[i] ); n_pars = new Array( ); n_els = new Array( ); }\n\n";
	echo "function follow(a){query( 'n_follow.php?a='+a,'' );}";
}

function do_noob( $parent, $x, $y, $abs, $txt, $follow = false, $fl = 0 )
{
	$abs = ( $abs ) ? 'absolute' : 'relative';
//	$ret = "<div style='z-index:150;position:$abs;top:{$y}px;left:{$x}px;width:282px;height:116px;'>";
	$ret .= "<table border=0 cellspacing=0 cellpadding=0 background=images/noob/ast.png width=282 height=116>";
	$ret .= "<tr><td style='width:108px;'>&nbsp;</td><td valign=top>";
	$ret .= "<div align=justify style='width:167px;height:100px;position:relative;top:7px;'><div id=n_text><small><b>".$txt."</b></small></div>";
	if( $follow ) $ret .= "<div id=n_follow style='position:absolute;right:2px;bottom:2px;'><a href='javascript:follow($fl)'><small><b>������</b></small></a></div>";
	$ret .= "</div>";
	$ret .= "</td></tr>";
	$ret .= "</table>";
//	$ret .= "</div>";
	if( gettype($x) != "integer" || $x >= 0 ) $lft = "left='{$x}px'"; else $lft = "right='".(-$x)."px'";
	if( gettype($y) != "integer" || $y >= 0 ) $top = "top='{$y}px'"; else $top = "bottom='".(-$y)."px'";

	echo "el = document.createElement( 'div' ); el.style.zIndex=150; el.style.position='$abs'; el.style.$lft; el.style.$top; el.style.width='282px'; el.style.height='116px'; el.innerHTML = '".addslashes($ret)."'; _( '$parent' ).appendChild( el ); n_pars.push( _( '$parent' ) ); n_els.push( el );";
}

function strelka( $parent, $x, $y, $abs, $kind )
{
   	$abs = ( $abs ) ? 'absolute' : 'relative';
	$ret = "<img src=images/noob/{$kind}.gif>";
	if( gettype($x) != "integer" || $x >= 0 ) $lft = "left='{$x}px'"; else $lft = "right='".(-$x)."px'";
	if( gettype($y) != "integer" || $y >= 0 ) $top = "top='{$y}px'"; else $top = "bottom='".(-$y)."px'";

	echo "el = document.createElement( 'div' ); el.style.zIndex=150; el.style.position='$abs'; el.style.$lft; el.style.$top; el.innerHTML = '".addslashes($ret)."'; _( '$parent' ).appendChild( el ); n_pars.push( _( '$parent' ) ); n_els.push( el );";
}

function show_noob( $noob, $add = 0 )
{
	global $player;
    if( $noob == 1 )
    {
    	do_noob( 'capital_content', 10, 190, true, "����������� ����, {$player->login}! � - ���� �� ��������� ����� ����. ���� ����� ���������. � ��������� � ������������ ���������� ���� �� ��������� ����� ��������. �����������, ���� ���� ������!", true, $noob);
    	strelka( 'capital_content', 295, 267, true, 'left' );
    }
    if( $noob == 2 )
    {
    	do_noob( 'capital_content', 10, 10, true, "����� ����� ������� �������� - �����. ����, ����, ������ � �.�. ������ ����� �� ����� ������. ���� �� �����������, �� ������� �� ���� �� ������� ������� � ������ �������. �� ���� �� �����, ���������� ��� ������� �����.", true, $noob);
    	strelka( 'capital_content', 295, 87, true, 'left' );
    }
    if( $noob == 3 )
    {
    	do_noob( 'fixedBlock', 90, 177, true, "� ��� ��. �� ������ ���� �����. ������ ���� ������ ��� �����. �������, �������, ��������� ���� �� ������ ���������. �� ����� �� ������� �������, �������, �� � ��������. ������ �� ���� ����� ���� ������� ����.", true, $noob);
    	strelka( 'fixedBlock', 375, 254, true, 'left' );
    }
    if( $noob == 4 )
    {
    	do_noob( 'capital_content', 10, 10, true, "����� ����� ���-��, ����� ���� �����. �� ���� ����� ������ ����� �����. � ����� ���-�� ������, ��� ����� ������� � �������� ���. ��� ����� ����� �������� �� ������, �� ������� ��������� �������.", false, $noob);
    	strelka( 'capital_content', 165, 180, true, 'right' );
    	echo "selectArray[\"Sel11\"][6] = 'game.php?dir=9&tloc=2';";
    }
    if( $noob == 5 )
    {
    	do_noob( 'allContent', -240, 50, true, "���������. �� � �������� ����. ��� �� ������, ����� ����� �������. �� ��� ������, ������� ��� ����� ������, ��������� ���, �������� �� �����.", true, $noob);
    	strelka( 'allContent', -200, 127, true, 'left' );
    }
    if( $noob == 6 )
    {
    	$st = "��� ����� ���� �����. ��� ������� ������ ����� �������, �� ��� ��. ���� ";
    	if( $add & 1 ) $st .= "<font color=green>�����</font>";
    	else $st .= "<span id=nf1 style='color:darkred'>�����</span>";
    	$st .= ', ';
    	if( $add & 2 ) $st .= "<font color=green>������</font>";
    	else $st .= "<span id=nf2 style='color:darkred'>������</span>";
    	$st .= ', ';
    	if( $add & 16 ) $st .= "<font color=green>�����</font>";
    	else $st .= "<span id=nf16 style='color:darkred'>�����</span>";
    	$st .= ' � ';
    	if( $add & 32 ) $st .= "<font color=green>�������</font>";
    	else $st .= "<span id=nf32 style='color:darkred'>�������</span>";
    	$st .= ". �� ��, �� ����� ��������� ������. �� ��� ���� ���-��. ����� �� ������ ������ ����� ������ ����.";

    	do_noob( 'fixedBlock', 700, 50, true, $st, false, $noob);
//    	strelka( _( 'allContent' ), -190, 40, true, 'right' );
    }
    if( $noob == 7 )
    {
    	echo "scroll(0,0);";
    	echo "if( document.all ) _('allContent').scrollTop = 0;";
    	do_noob( 'srchg2', -1, 60, true, "�� ���. �� ���������  ����� ��� ���� ������. ������, ����� �� ������ ��������� ��� � ��������. ������ ����� ����� ���� �� ����. ��� ����� ������� � ���� ���������.", false, $noob);
    	strelka( 'srchg2', 20, 15, true, 'top' );
    }
    if( $noob == 8 )
    {
    	echo "position = getAp( _( 'nvimg153' ) );";
    	do_noob( 'allContent', "'+(55+position.x)+'", "'+(12+position.y)+'", true, "����� ��������� ����� �������. ��� ����� ������ ������ �� ��� ������ ������.", false, $noob);
    	strelka( 'allContent', "'+position.x+'", "'+(position.y-50)+'", true, 'bot' );
    }
    if( $noob == 9 )
    {
    	do_noob( 'fixedBlock', 300, 50, true, "������ ������ ����� ���� - ���������� �� ��� ������ � ���������� �� ��������������� ����� ����� ���������.", true, $noob);
    	strelka( 'fixedBlock', 585, 127, true, 'left' );
    }
    if( $noob == 10 )
    {
    	echo "position = getAp( _( 'nvimg133' ) );";
    	do_noob( 'allContent', "'+(55+position.x)+'", "'+(12+position.y)+'", true, "�������� ���������� ����� �� ����� ������. ���� �� ���������, ������ ����� �� ������� ������� �����, ��� �������.", false, $noob);
    	strelka( 'allContent', "'+position.x+'", "'+(position.y-50)+'", true, 'bot' );
    	strelka( 'fixedBlock', 68, 60, true, 'left' );
    }
    if( $noob == 11 )
    {
    	do_noob( 'fixedBlock', 400, 80, true, "�������� ����� ��� ��������� ����, � ����� ������� � ������ ������� �������. ��� ����� ���� ������ ������ �����.", false, $noob);
    	strelka( 'srchg0', -75, "-15", true, 'right' );
    }
    if( $noob == 12 )
    {
    	do_noob( 'fixedBlock', 400, 80, true, "����� �������� �� ������� ����� ������, ����� ������ � �������, ��� ���� ���� ���� ������ ���.", false, $noob);
    	strelka( 'n_go_to_main_street', "-50", "-15", true, 'right' );
    }
    if( $noob == 13 )
    {
    	do_noob( 'capital_content', 10, 10, true, "������ - ��� �����, ������ ��������. ���� �� �����, �������� �����, ��������� ����������� ����� �� ���������. ��������� � ���.", false, $noob);
    	strelka( 'capital_content', 155, 160, true, 'left' );
    	echo "selectArray[\"Sel8\"][6] = 'game.php?dir=5&tloc=2';";
    }
    if( $noob == 14 )
    {
    	do_noob( 'n_go_to_dungeon', -250, "0", true, "������ �� ���������� � ����� � ������. ����� ���������� � ���, ����� �� ��������������� �������.", false, $noob);
    	strelka( 'n_go_to_dungeon', "-50", "-15", true, 'right' );
    }
    if( $noob == 15 )
    {
    	do_noob( 'go_further', "-360", "0", true, "�� ���������� �� ������� ������� �����. ����� ��� ���������� ���������, ������� ���������� �� �������� ��������� ��� ������ � �����.", true, $noob);
    	strelka( 'go_further', "-75", "77", true, 'left' );
    }
    if( $noob == 16 )
    {
    	do_noob( 'go_further', "-385", "10", true, "������ �������� �� ����������, ������� ����� �����. ����� ����, ��� �� �������� � ����� ������ ���, � ������� ���� ��������� ���� � ���������� � ���. ��������, � ���� ���� ������� ��� ����.", true, $noob);
    	strelka( 'go_further', "-100", "87", true, 'left' );
    }
    if( $noob == 17 )
    {
    	do_noob( 'go_further', "-360", "0", true, "���� �� �� ���������� �������� ������, ��� ��������� ������� � �������� ����� ����������� � ������ ����� � �������� ��������. � �������� ������ �������� ������� ����� ���������� �����������.", true, $noob);
    	strelka( 'go_further', "-75", "77", true, 'left' );
    }
    if( $noob == 18 )
    {
    	do_noob( 'go_further', "-370", "20", true, "�� �������� �� ����, ��� �� ����������: � ������, � �������, � ���� ��� �� ���� - ��� ������� ��������� ������ ��������� ������ ������. �������� ������ � ������.", false, $noob);
    	strelka( 'go_further', "-50", "-15", true, 'right' );
    	echo "ready_to_go_further = true;";
    }
    if( $noob == 19 )
    {
    	do_noob( 'my_login', 300, 45, true, "����, �� � ���. ������� ��� � �������� ����� �������, � ������ �� ����� ���������� ���� ������: ����, ������� � ����. � ������� �� ������ ������ ���, � �������� ������ ���������� ������ ��������� ���� � ���.", true, $noob);
    	strelka( 'my_login', 575, 122, true, 'left' );
    }
    if( $noob == 20 )
    {
    	do_noob( 'my_login', 315, 40, true, "������ � ���� �������� ��� ����������, �� ������ ��� ������ ������. ������ �� ���� ���������� ������ ������� ���� ����������. ������� �� ������� ������, ���� ����� �������� ����� ���������� ����������.", true, $noob);
    	strelka( 'my_login', 590, 117, true, 'left' );
    }
    if( $noob == 21 )
    {
    	do_noob( 'my_login', 290, 35, true, "����� �������� �� ������ � ��������. �������� ���������� ���������� ������ ����. ��� ������� ���������� ������� ������, � ��� ����� ������� �������� ������ �����.", false, $noob);
    	strelka( 'crds56', 100, 50, true, 'right' );
    }
    if( $noob == 22 )
    {
    	do_noob( 'last_turn', "-375", 80, true, "�� ������ ���������, ��� ���� ������ ���� � ���������� ��������� ����� ���� �� �����. �� � ������ ���� ���������� ������������� ������� ������.", true, $noob);
    	strelka( 'last_turn', "-90", 157, true, 'left' );
    }
    if( $noob == 23 )
    {
    	do_noob( 'last_turn', "-365", 85, true, "��� ������, ��� �� ��������� ����������, � ���� ��������&nbsp;- ���. ������ ��������, ��� ����� ��������� ��������� � �������&nbsp;- ��� ����, ������� �� ��� �����. �� ������ ��� ���������� �� �������� �������?", true, $noob);
    	strelka( 'last_turn', "-80", 162, true, 'left' );
    }
    if( $noob == 24 )
    {
    	do_noob( 'last_turn', "-375", 80, true, "������ ��� ���� ���������� ����� ������ ����, � ���������� ���������&nbsp;- ������ �������. ������ ���� �������, ��� ������ �������, � ������ ������� �������, ��� ������ ����.", true, $noob);
    	strelka( 'last_turn', "-90", 157, true, 'left' );
    }
    if( $noob == 25 )
    {
    	do_noob( 'last_turn', "-385", 90, true, "�� ������ �������� ����: &laquo;���� ����� ������� �������, � ������� ������� ����, ������ �� ��� ������ �� ��������� ���������� ����?&raquo;", true, $noob);
    	strelka( 'last_turn', "-100", 167, true, 'left' );
    }
    if( $noob == 26 )
    {
    	do_noob( 'last_turn', "-375", 80, true, "������ ��� ������ ����, � ���� �������, �������, ��� ������ ����. ��� ����� ��������� ��������� ���� ������-�������-������ - ������ � ���� �����, ������ � ������ ��������� ��������� ������.", true, $noob);
    	strelka( 'last_turn', "-90", 157, true, 'left' );
    }
    if( $noob == 27 )
    {
    	do_noob( 'last_turn', "-365", 85, true, "����� ��������� ������ ���������� ������� ������.", false, $noob);
    	strelka( 'crds56', 100, 50, true, 'left' );
    }
    if( $noob == 28 )
    {
    	do_noob( 'last_turn', "-375", 80, true, "� �� � ���� �������� ������� ������ ����. � ���� ������ �� ���� �� ��� �� ������� ����������, ��, � ���� �����, �� ��� ��������� ��������� ����.", true, $noob);
    	strelka( 'last_turn', "-90", 157, true, 'left' );
    }
    if( $noob == 29 )
    {
    	do_noob( 'last_turn', "-365", 90, true, "���� ������, ������ ���� ���������, ������ ��� ����� �������� ������, ��� ���� ����� ������� ��. ������ � ������ ��������� 20 ��������, ��� ����� ��� ��� ������������.", true, $noob);
    	strelka( 'last_turn', "-80", 167, true, 'left' );
    }
    if( $noob == 30 )
    {
    	do_noob( 'last_turn', "-355", 100, true, "����� �� ��������� ���� ������ ���������� ������� 25 �����, ���� �� ������� ��� ����������. ����� �������� ���� �����, ������ �������� ����.", false, $noob);
    	strelka( 'crds57', 100, 50, true, 'right' );
    }
    if( $noob == 31 )
    {
    	do_noob( 'last_turn', "-355", 100, true, "� ���� ��� ��� �� �������, ���� �������� ������ ������ ���� � ��������� ����������, ������� ������� �������� ����.", true, $noob);
    	strelka( 'last_turn', "-70", 177, true, 'left' );
    }
    if( $noob == 32 )
    {
    	do_noob( 'last_turn', "-365", 80, true, "����� ��������� � ��������� ��� ������� ���������� �������� ����.", false, $noob);
    	strelka( 'crds57', 100, 50, true, 'right' );
    }
    if( $noob == 33 )
    {
    	do_noob( 'last_turn', "-355", 100, true, "����� �����, �� ���� ��������� ����������, � ������� ���. �� ������� ��������� ���������� ����� - ������ ���������� ����� ������� ����� � ��������� ����.", true, $noob);
    	strelka( 'last_turn', "-70", 177, true, 'left' );
    }
    if( $noob == 34 )
    {
    	do_noob( 'last_turn', "-355", 100, true, "����� �� �������� 75 �����, �� �������� ������ ������� �������� ���������, �� ������� ���� �������� ������� ���������� ����� ������������.", true, $noob);
    	strelka( 'last_turn', "-70", 177, true, 'left' );
    }
    if( $noob == 35 )
    {
    	do_noob( 'last_turn', "-345", 110, true, "����� �������� ����, �������� ������ � ������ ������� �����. �� ����� ���������� ������, ������� �� ������� ������� ����� ���� ������� �������� ��� ����.", true, $noob);
    	strelka( 'last_turn', "-60", 187, true, 'left' );
    }
    if( $noob == 36 )
    {
    	do_noob( 'last_turn', "-365", 80, true, "������ � ������� ���� ��������� � ����� � ������������� ���, �� ������� ��������� �������, �������, ��������, ����� ������ ������� ��� ����.", true, $noob);
    	strelka( 'last_turn', "-80", 157, true, 'left' );
    }
    if( $noob == 37 )
    {
    	do_noob( 'last_turn', "-355", 70, true, "�� ���� � ��������� �������� ����, ���� ���� ������ �������. ����� � �� �������!", true, $noob);
    	strelka( 'last_turn', "-70", 147, true, 'left' );
    }
}

?>
