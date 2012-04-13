<script>begin_help( 'Энциклопедия - Вещи' );</script>

<?

include_js( "js/ajax.js" );

?>

<script>

loaded = new Array( );
loaded_using = new Array( );

function expand_where( id )
{
	if( document.getElementById( 'dvi' + id ).style.display == 'none' )
	{
		document.getElementById( 'imgi' + id ).src = 'images/e_minus.gif';
		document.getElementById( 'dvi' + id ).style.display = '';
		if( !loaded[id] ) query( 'help/enc_items_where.php', '' + id );
	}
	else
	{
		document.getElementById( 'imgi' + id ).src = 'images/e_plus.gif';
		document.getElementById( 'dvi' + id ).style.display = 'none';
	}
}

function expand_using( uid )
{
	if( document.getElementById( 'dvu' + uid ).style.display == 'none' )
	{
		document.getElementById( 'imgu' + uid ).src = 'images/e_minus.gif';
		document.getElementById( 'dvu' + uid ).style.display = '';
		if( !loaded_using[uid] ) query( 'help/enc_items_using.php', '' + uid );
	}
	else
	{
		document.getElementById( 'imgu' + uid ).src = 'images/e_plus.gif';
		document.getElementById( 'dvu' + uid ).style.display = 'none';
	}
}

</script>

<?

include_once( "functions.php" );
include_once( "items.php" );
include_once( "player.php" );
include_once( "skin.php" );

f_MConnect( );
$player = new Player( 172 );
$stats = $player->getAllAttrNames( );

$where = "";
if( isset( $_GET['item_id'] ) )
{
	$id = $_GET['item_id'];
	settype( $id, 'integer' );
	$where .= " AND item_id=$id";
	$lnk .= "&item_id=$id";
}
if( isset( $_GET['name'] ) )
{
	$name = htmlspecialchars( $_GET['name'], ENT_QUOTES );
	$where .= " AND LOWER(name) = LOWER('$name')";
}
if( isset( $_GET['type'] ) )
{
	$type = $_GET['type'];
	settype( $type, 'integer' );
	if( $type != -1 ) $where .= " AND type=$type";
	$lnk .= "&type=$type";
}
if( isset( $_GET['type2'] ) )
{
	$type2 = $_GET['type2'];
	settype( $type2, 'integer' );
	$where .= " AND type2=$type2";
	$lnk .= "&type2=$type2";
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

if( $where !== "" )
{
	if (isset( $_GET['item_id']))
		if ((int)f_MValue("SELECT parent_id FROM items WHERE item_id=".$id) != $id)
			$pi_id="";
		else
			$pi_id="item_id=parent_id AND";
	else
		$pi_id="item_id=parent_id AND";
	$p_num = (int)(( f_MValue( "SELECT count( item_id ) FROM items WHERE item_id=parent_id AND level < 50 $where" ) - 1 ) / 20) + 1;
	$page = (int)$_GET['page'];
	$plim = $page * 20;
	$res = f_MQuery( "SELECT * FROM items WHERE $pi_id level < 50 $where ORDER BY type, level LIMIT $plim, 20" );
	if( !f_MNum( $res ) ) echo "<center><i>Нет таких вещей</i></center>";
	else
	{
		echo "<center><table width=80%><tr><td>";
		ScrollLightTableStart();
		echo "<center><table>";

		$first = true;
		while( $arr = f_MFetch( $res ) )
		{
			if( !$first ) echo "<tr><td colspan=2><hr></td></tr>";
			$first = false;
			echo "<tr>";
			echo "<td valign=top width=150 align=center><img border=0 src='../images/items/".itemImage( $arr )."'><br><a href='/help.php?id=1010&item_id=$arr[item_id]' style='font-weight: bold;'>$arr[name]</a></td>";
			echo "<td valign=top align=left>";
			if( isset( $_GET['cheat'] ) )
				echo "<b>UIN: </b>$arr[item_id]<br>";
			echo "<b>Тип: </b>{$item_types[$arr[type]]}<br>";
			if( $arr['type'] == 0 ) echo "<b>Подтип: </b> {$item_types2[$arr[type2]]}<br>";
			if( $arr[level] ) echo "<b>Уровень: </b>$arr[level]<br>";
			echo "<b>Вес: </b>".($arr[weight])/100.0."<br>";
			echo "<b>Гос.Цена: </b>".($arr[price])."<br><br>";
			echo itemDescr( $arr );
			echo "<br><img width=11 height=11 src=images/e_plus.gif style='cursor:pointer' onclick='expand_where($arr[item_id])' id=imgi$arr[item_id]>&nbsp;Как можно получить<div id=dvi$arr[item_id] style='display:none'><i>Идет загрузка</i></div>";
			echo "<br><img width=11 height=11 src=images/e_plus.gif style='cursor:pointer' onclick='expand_using($arr[item_id])' id=imgu$arr[item_id]>&nbsp;Где используется<div id=dvu$arr[item_id] style='display:none'><i>Идет загрузка</i></div>";
			echo "<img src=/images/empty.gif width=150 height=0>";
			echo "</td></tr>";
		}

		echo "</table>";
		for( $i  =0; $i < $p_num; ++ $i )
		{
			if( $i ) echo " ";
			if( $i != $page ) echo "<a href=help.php?id=1010&{$lnk}&page=$i>"; else echo "<b>";
			echo $i + 1;
			if( $i != $page ) echo "</a>"; else echo "</b>";
		}
		echo "</center>";

		ScrollLightTableEnd( );
		echo "</td></tr></table></center>";
	}
}

		echo "<center><table width=80%><tr><td>";
		ScrollLightTableStart();
		echo "<center><table><form action=help.php method=get>";

		$item_types[-1] = "Любой";
		$levels = Array( 1 => 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25 );
		echo "<input type=hidden name=id value=1010>";
		echo "<tr><td><b>Тип: </b></td><td>".create_select_global( "type", $item_types, -1 )."</td></tr>";
		echo "<tr><td><b>Мин.уровень: </b></td><td>".create_select_global( "minlevel", $levels, 1 )."</td></tr>";
		echo "<tr><td><b>Макс.уровень: </b></td><td>".create_select_global( "maxlevel", $levels, 25 )."</td></tr>";
		echo "<tr><td>&nbsp;</td><td><input class=m_btn value=Показать type=submit></td></tr>";

		echo "</form></table></center>";

		ScrollLightTableEnd( );
		echo "</td></tr></table></center>";

?>

<script>end_help( );</script>
