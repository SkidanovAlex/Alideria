<?

include_once( "functions.php" );
include_once( "card.php" );
include_once( "items.php" );
include_once( "player.php" );
include_once( "skin.php" );

f_MConnect( );
$player = new Player( 172 );
$stats = $player->getAllAttrNames( );

$per_page = 10;

$p = (int)$_GET['p'];
$lim = $p * $per_page;


$where = "";
$lnk = "";
if( isset( $_GET['spell_id'] ) )
{
	$id = $_GET['spell_id'];
	settype( $id, 'integer' );
	$where .= " AND card_id=$id";
	$lnk .= "&spell_id=$id";
}
if( isset( $_GET['card_id'] ) )
{
	$id = $_GET['card_id'];
	settype( $id, 'integer' );
	$where .= " AND card_id=$id";
	$lnk .= "&spell_id=$id";
}
if( isset( $_GET['name'] ) )
{
	$name = htmlspecialchars( $_GET['name'], ENT_QUOTES );
	$where .= " AND LOWER(name) = LOWER('$name')";
}
if( isset( $_GET['minlevel'] ) )
{
	$lvl = $_GET['minlevel'];
	settype( $lvl, 'integer' );
	$where .= " AND level>=$lvl";
	$lnk .= "&minlevel=$lvl";
}
if( isset( $_GET['maxlevel'] ) )
{
	$lvl = $_GET['maxlevel'];
	settype( $lvl, 'integer' );
	$where .= " AND level<=$lvl";
	$lnk .= "&maxlevel=$lvl";
}
if( isset( $_GET['genre'] ) )
{
	$genre = $_GET['genre'];
	settype( $genre, 'integer' );
	if( $genre != -1 ) $where .= " AND genre=$genre";
	$lnk .= "&genre=$genre";
}

if( $where !== "" )
{
	$res = f_MQuery( "SELECT * FROM cards WHERE status=0 $where AND parent=0 ORDER BY genre, level LIMIT $lim, $per_page" );
	if( !f_MNum( $res ) ) echo "<center><i>��� ����� ����������</i></center>";
	else
	{
		echo "<table><tr><td>";ScrollLightTableStart();
		echo "<div style='width:600px; height:480px; overflow:auto'>";
//		echo "<center><table width=80%><tr><td>";
//		ScrollLightTableStart();
		echo "<center><table id=s_table>";

		$first = true;
		$genres[3] = "�����������";
		while( $arr = f_MFetch( $res ) )
		{
			if( !$first ) echo "<tr><td colspan=2><hr></td></tr>";
			$first = false;
			echo "<tr>";
			if( $arr[genre] == 0 ) $clr = "blue";
			else if( $arr[genre] == 1 ) $clr = "green";
			else if( $arr[genre] == 2 ) $clr = "red";
			else $clr = "grey";
			echo "<td valign=top width=150 align=center><img border=0 src='../images/spells/$arr[image_large]'><br><font color=$clr><b>$arr[name]</b></font></td>";
			echo "<td valign=top align=left>";
			echo "������: <font color=$clr><b>{$genres[$arr[genre]]}</b></font><br>";
			echo "�������: <b>$arr[level]</b><br>";

			if( $arr['multy'] ) $arr['cost'] = '(������� ������) x 5';

			if( $arr[genre] != 3 ) echo "����: <span id=mana$arr[card_id]><b>$arr[cost]</b></span><br>";
			else echo "Cooldown: <b>$arr[cost]</b> �����<br>";
			echo "<br>";
			$ures = f_MQuery( "SELECT * FROM cards WHERE parent = $arr[card_id] ORDER BY mk" );
			if( f_MNum( $ures ) )
			{
				$st = '<script>manas'.$arr['card_id'].' = ['.$arr['cost'].']; effects'.$arr['card_id'].' = ["'.addslashes($arr['descr2']).'"]; reqs'.$arr['card_id'].' = ["'.ItemReqStr( $arr['req'] ).'"];';
				echo "<div id=menu$arr[card_id]><b>������:</b> <u>�������</u>";
				while( $uarr = f_MFetch( $ures ) )
				{
					echo "&nbsp;&nbsp;&nbsp;<a href='javascript:chg($arr[card_id],$uarr[mk])'>��$uarr[mk]</a>";
					$st .= 'manas'.$arr['card_id'].'['.$uarr['mk'].'] = '.$uarr['cost'].'; effects'.$arr['card_id'].'['.$uarr['mk'].'] = "'.addslashes($uarr['descr2']).'"; reqs'.$arr['card_id'].'['.$uarr['mk'].'] = "'.ItemReqStr( $uarr['req'] ).'";';
				}
				echo "</div>";
				$st .= '</script>';
				echo $st;
			}
			else echo "<b>������:</b><br>";
			echo "<div id=eff$arr[card_id]>$arr[descr2]</div><br><b>��������</b><br><i>$arr[descr]</i><br>";
			if( $arr['req'] )
			{
				echo "<br><b>�������:</b><br><div id=req$arr[card_id]>";
				echo ItemReqStr( $arr['req'] );
				echo "</div>";
			}
			echo "<img src=/images/empty.gif width=150 height=0>";
			echo "</td></tr>";
		}

		echo "</table></center>";

//		ScrollLightTableEnd( );
//		echo "</td></tr></table>";
		echo "</div>";
		ScrollLightTableEnd();echo "</td></tr></table>";

		$arr = f_MFetch( f_MQuery( "SELECT count( card_id ) FROM cards WHERE status=0 $where AND parent=0" ) );
		if( $arr[0] > 10 )
		{
		 	$pages = (int)(( $arr[0] - 1 ) / 10)+1;
		 	echo"<b>��������: </b>";
		 	for( $i = 0; $i < $pages; ++ $i )
		 	{
		 		if( $i == $p ) echo " <b>".($i + 1)."</b>";
		 		else echo " <a href=help.php?id=1011$lnk&p=$i>".($i+1)."</a>";
		 	}
		}

		echo "</center><br>";
	}
}

/*		echo "<center><table width=80%><tr><td>";
		ScrollLightTableStart();
		echo "<center><table><form action=help.php method=get>";

		$genres[-1] = "�����";
		$sub_genres[0] = "��� ���������";
		$sub_genres[-1] = "�����";
		$levels = Array( 1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16 );
		echo "<input type=hidden name=id value=1011>";
		echo "<tr><td><b>������: </b></td><td>".create_select_global( "genre", $genres, -1 )."</td></tr>";
		echo "<tr><td><b>���.�������: </b></td><td>".create_select_global( "minlevel", $levels, 1 )."</td></tr>";
		echo "<tr><td><b>����.�������: </b></td><td>".create_select_global( "maxlevel", $levels, 16 )."</td></tr>";
		echo "<tr><td>&nbsp;</td><td><input class=m_btn value=�������� type=submit></td></tr>";

		echo "</form></table></center>";

		ScrollLightTableEnd( );

		echo "</td></tr></table></center>";
*/
include_js( 'functions.js' );

?>

<script>
function chg( id, mk )
{
	st = '<b>������:</b> ';
	for( var i = 0; i < 5; ++ i )
	{
		var q = '��' + i; if( !i ) q = '�������';
		if( i ) st += '&nbsp;&nbsp;&nbsp;';
		if( i == mk ) st += '<u>' + q + '</u>';
		else st += '<a href="javascript:chg(' + id + ',' + i + ')">' + q + '</a>';
	}
	_( 'menu' + id ).innerHTML = st;
	_( 'eff' + id ).innerHTML = eval( 'effects' + id + '[' + mk + ']' );
	_( 'mana' + id ).innerHTML = '<b>' + eval( 'manas' + id + '[' + mk + ']' ) + '</b>';
	_( 'req' + id ).innerHTML = eval( 'reqs' + id + '[' + mk + ']' );
}
</script>
