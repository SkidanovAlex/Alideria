<?

if( !isset( $mid_php ) ) die( );

$stats = $player->getAllAttrNames( );
$blevel = getBLevel( 3 );

echo "<b>����� ������</b> - <a href=game.php?order=main>�����</a><br>";

if( isset( $_GET['do_take'] ) )
{
	include_js( "js/items_renderer_silo1.js" );
	echo "<script>function doSilo(id,clr){query('clan_silo_ref.php',''+id+'|-'+document.getElementById('place'+id+'_'+clr).value+'|'+clr);}</script>";
	echo "������ ����� �� ������ - <a href=game.php?order=silo>�����</a></br>";
	$res = f_MQuery( "SELECT items.*,clan_items.number,clan_items.color FROM clan_items,items WHERE clan_id=$clan_id AND items.item_id=clan_items.item_id" );
	echo "<script>\n";
	echo "item_err = '�� ������ ��� �� ����� ����';";
	while( $arr = f_MFetch( $res ) )
	{
		echo "add_item( $arr[item_id], $arr[type], '$arr[name]', '".itemImage( $arr )."', '".itemFullDescr( $arr )."', $arr[number], $arr[color] );\n";
	}
	echo "document.write( render_items( true, 'doSilo' ) );\n";
	echo "</script>\n";
}
else if( isset( $_GET['do_put'] ) )
{
	include_js( "js/items_renderer1.js" );
	$nms = array( '������� �����', "��������� �����", "������ �����", "����� �����", "������� �����" );
	$res = f_MQuery( "SELECT shelf_id, name FROM clan_shelf_names WHERE clan_id=$clan_id" );
	while( $arr = f_MFetch( $res ) ) $nms[$arr[0]] = $arr[1];
	echo "<div id=color_choose style='position:absolute;left:0px;top:0px;display:none;'><table><tr><td><script>FUlt();</script><b>�������� ��:</b><br><font onclick='DoPut(0)' color=red style='cursor:pointer'>$nms[0]</font><br><font onclick='DoPut(1)' color=purple style='cursor:pointer'>$nms[1]</font><br><font onclick='DoPut(2)' color=yellow style='cursor:pointer'>$nms[2]</font><br><font onclick='DoPut(3)' color=blue style='cursor:pointer'>$nms[3]</font><br><font onclick='DoPut(4)' color=green style='cursor:pointer'>$nms[4]</font><br><script>FL();</script></td></tr></table></div>";
	echo "<script>put_id = -1; function doSilo(id){ put_id = id; o = document.getElementById( 'color_choose' ); pos = getAP( document.getElementById( 'place'+id ) );o.style.display='';o.style.left=pos.x;o.style.top=pos.y+20; }</script>";
	echo "<script>function DoPut(clr){doMoo();query('clan_silo_ref.php',''+put_id+'|'+document.getElementById('place'+put_id).value + '|' + clr);}</script>";
	echo "���������� ����� �� ����� - <a href=game.php?order=silo>�����</a></br>";
	$res = f_MQuery( "SELECT items.*,player_items.number FROM player_items,items WHERE player_id={$player->player_id} AND weared=0 AND nodrop=0 AND items.item_id=player_items.item_id" );
	echo "<script>\n";
	echo "item_err = '� ��� ��� �� ����� ����';";
	while( $arr = f_MFetch( $res ) )
	{
		echo "add_item( $arr[item_id], $arr[type], '$arr[name]', '".itemImage( $arr )."', '".itemFullDescr( $arr )."', $arr[number] );\n";
	}
	echo "document.write( render_items( true, 'doSilo' ) );\n";
	echo "</script><script>";
	echo "function doMoo() { document.getElementById( 'color_choose' ).style.display='none'; }\n";
	echo "document.getElementById( 'items_div' ).onmousedown=doMoo;";
	echo "</script>\n";
}
else if( isset( $_GET['do_shelves'] ) && 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_CONTROL_SHOP ) )
{
	echo "<br>";
	$shelves = Array( 'red', 'purple', 'yellow', 'blue', 'green' );
	$shelves2 = Array( '�������', '���������', '������', '�����', '�������' );

	if( isset( $_POST['adds'] ) )
	{
		$id = (int)$_POST['adds'];
		$txt = htmlspecialchars( $_POST['txt'], ENT_QUOTES );
		f_MQuery( "LOCK TABLE clan_shelf_names WRITE" );
		f_MQuery( "DELETE FROM clan_shelf_names WHERE clan_id=$clan_id AND shelf_id=$id" );
		f_MQuery( "INSERT INTO clan_shelf_names ( clan_id, shelf_id, name ) VALUES ( $clan_id, $id, '$txt' )" );
		f_MQUery( "UNLOCK TABLES" );
		f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0, arg1 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 9, 1, $id )" );
	}
	else if( isset( $_GET['del'] ) )
	{
		$id = (int)$_GET['del'];
		f_MQuery( "DELETE FROM clan_shelf_names WHERE clan_id=$clan_id AND shelf_id=$id" );
		f_MQuery( "INSERT INTO clan_log ( clan_id, time, player_id, action, arg0, arg1 ) VALUES ( $clan_id, ".time( ).", {$player->player_id}, 9, 2, $id )" );
	}

	$res = f_MQuery( "SELECT * FROM clan_shelf_names WHERE clan_id=$clan_id ORDER BY shelf_id" );
	if( !f_MNum( $res ) ) echo "<i>� ��� ��� �������� �� � ����� �� �����</i><br>";
	else while( $arr = f_MFetch( $res ) )
	{
		echo "<font color={$shelves[$arr[shelf_id]]}>{$shelves2[$arr[shelf_id]]}</font> ����� - �������� �� <b>$arr[name]</b> (<a href=game.php?order=silo&do_shelves=1&del={$arr[shelf_id]}>�������</a>)<br>";
	}
	echo "<br>";
	echo "<form action='game.php?order=silo&do_shelves=1' method=post>";
	echo "<table><tr><td valign=top>�����:</td><td>".create_select_global( 'adds', $shelves2, 0 ),"</td></tr>";
	echo "<tr><td valign=top>�����:</td><td><input type=text name=txt class=m_btn><br><small>����� ������ ���� ������������ �����<br><i>�������� �� ...</i></small></td></tr>";
	echo "<tr><td>&nbsp;</td><td><input type=submit value='��������' class=s_btn></td></tr>";

	echo "</form>";
}
else if( isset( $_GET['do_weaponary'] ) )
{
	echo "����� ���������� - <a href=game.php?order=silo>�����</a></br>";
	echo "<table width=710><tr>"; // inner table, which contains both silo items and taken items
	echo "<td valign=top width=500><script>FLUl();</script>";
	echo "<div id=silo_items>&nbsp;</div>";
	echo "<script>FLL();</script></td><td valign=top width=200><script>FLUl();</script>";
	echo "<div id=silo_taken_items>&nbsp;</div>";
	echo "<script>FLL();</script></td></tr></table><img src='images/e_none.gif' width=0 height=0><img src='images/e_check.gif' width=0 height=0>";
	
	$res = f_MQuery( "SELECT items.*,SUM(clan_items.number) AS number,clan_items.color FROM clan_items,items WHERE clan_id=$clan_id AND items.item_id=clan_items.item_id AND (items.type >= 1 AND items.type < 20 OR items.type = 30 OR items.type = 35) GROUP BY parent_id, decay, max_decay, color ORDER BY level" );
	echo "<script>\n"; ?>
	var items = [];
	var types = [];
	function add_item(id,tp,nm,img,desc,num,clr) {
		if( !items[id * 10 + clr] )
		{
			items[id * 10 + clr] = {
				'id' : id,
    			'type': tp,
    			'name': nm,
    			'image': img,
    			'desc': desc,
    			'number': num,
    			'color': clr
    		};
    		if( !types[tp] ) types[tp] = [];
    		types[tp].push( id * 10 + clr ); 
    	}
		else items[id * 10 + clr].number += num;
	}
	function remove_item( id, num, clr )
	{
		items[id * 10 + clr].number -= num;
	}
	var shelves = ['�������', "���������", "������", "�����", "�������"];
	var shelves2 = ['�������', "���������", "������", "�����", "�������"];
	function ref_taken( )
	{
		var st = '';
		var all_checked = 1;
		for( var i in taken_items ) if( taken_items[i].still_here )
		{
			st += '<tr><td><img src=images/e_' + (taken_items[i].checked ? 'check' : 'none') + '.gif width=11 height=11 onclick="taken_items[' + i + '].checked = ' + (1 - taken_items[i].checked) + ';ref_taken();"></td><td><a href=help.php?id=1010&item_id=' + taken_items[i].id + ' target=_blank>' + taken_items[i].name + '</td></tr>';
			if( !taken_items[i].checked ) all_checked = 0;
		}
		if( st == "" ) st = '<i>�� ���� �� �������� ��������� ����������</i>';
		else
		{
			st = '<table><tr><td><img src=images/e_' + (all_checked ? 'check' : 'none') + '.gif width=11 height=11 onclick="for( var i in taken_items ) taken_items[i].checked = ' + (1 - all_checked) + '; ref_taken();"></td><td>���</td></tr>' + st + "</table>";
			st += '<br><b>��������� ����:</b><br><li><a href="javascript:put_on_shelf(-1)">������ �� ������</a>';
			for( var i in shelves2 ) st += '<li><a href="javascript:put_on_shelf(' + i + ')">�������� �� ' + shelves2[i] + ' �����</a>';
		}
		_( 'silo_taken_items' ).innerHTML = st;
	}
	function put_on_shelf( id )
	{
		var st = '';
		for( var i in taken_items ) if( taken_items[i].still_here && taken_items[i].checked )
		{
			if( st != '' ) st += '|';
			st += taken_items[i].id; 
		}
		query( 'clan_silo_ref.php?list_action=' + id, st );
	}
	function ref() {
		var st = '';
		var rtypes = [];
		for( var i in types ) {
			var ok = 0;
			for( var j in types[i] ) if( items[types[i][j]].number > 0 ) { ok = 1; break; }
			if( ok ) rtypes.push( types[i] );
		}
		for( var i in rtypes ) {
			st += '<table>';
			var id = 0;
			for( var j in rtypes[i] ) {
				var it = items[rtypes[i][j]];
				if( it.number > 0 ) {
					var lst = '';
					if( id % 9 == 0 ) lst += '<tr>';
					lst += '<td><img onmouseout="hideTooltip(event)" onmousemove="showTooltipW(event,\'�����: <b>' + shelves[it.color] + '</b><br><br>'+it.desc+'\',300)" width=50 height=50 src=images/items/' + it.image + '><br><small>[' + it.number + ']</small> <a href="javascript:void(0)" onclick="take(' + it.id +', ' + it.color + ')">�����</a><td>';
					if( id % 9 == 8 ) lst += '</tr>';
					st += lst;
					++ id;
				}
			}
			while( id % 9 != 0 )
			{
				st += '<td>&nbsp;</td>';
				if( id % 9 == 8 ) st += "</tr>";
				++ id;
			}
			st += '</table>';
			_( 'silo_items' ).innerHTML = st;
		}
		ref_taken( );
	} function refresh_items( ) { ref(); };
	function take( id, color ) {
		query( 'clan_silo_ref.php?weap=1', id + "|-1|" + color );
	}
	var taken_items = [];
	function add_taken_item( id, nm )
	{
		taken_items.push( { 'id': id, 'name': nm, 'checked': 0, 'still_here': 1 } );
	}
	function remove_taken_item( id )
	{
		for( var i in taken_items ) if( taken_items[i].id == id && taken_items[i].still_here && taken_items[i].checked )
		{
			taken_items[i].still_here = 0;
			break;
		}
	}
	<?
	echo "item_err = '�� ������ ��� �� ����� ����';";
	while( $arr = f_MFetch( $res ) )
	{
		echo "add_item( $arr[item_id], $arr[type], '".addslashes($arr[name])."', '".itemImage( $arr )."', '".addslashes(itemFullDescr( $arr ))."', $arr[number], $arr[color] );\n";
	}
	$res = f_MQuery( "SELECT items.* FROM player_clan_items INNER JOIN items ON player_clan_items.item_id=items.item_id WHERE player_id={$player->player_id}" );
	while( $arr = f_MFetch( $res ) )
	{
		echo "add_taken_item( $arr[parent_id], '".addslashes($arr[name])."' );";
	}
	echo "ref();</script>\n";
}
else if ( isset( $_GET['do_seal'] ) && 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_CONTROL_SHOP ) ) // ������, ����������
{
?>
<style type="text/css">
	.orderItems {
		margin-top: 5px;
		padding: 2px;
	}
	.orderItems td {
		vertical-align: top;
		margin: 2px;
		padding: 2px;
		border-bottom: 1px dashed #DE9751;
	}
	.orderItems th {
		text-align: center;
		padding: 3px;
		font-weight: bold;
		color: black;
		font-size: 12px;
		background-color: #DE9751;
	}
</style>
<?
	echo "<b>������ �������</b> - ����� �������� ������ ����� ��������� �������������� ������ ������. ";
	echo "<a href=game.php?order=silo>��������� �� �����</a><br>";
	$siloShelves = array ( "�������", "���������", "������", "�����", "�������" );
	$siloColors = array ( "red", "purple", "yellow", "blue", "green" );
	
	if ( isset( $_GET['seal_action'] ) )
	{
		$action = $_GET['seal_action'];
		settype( $action, 'integer' );
		
		// seal_action=1
		// ������� ��������� ������
		if ( $action == 1 && isset( $_GET['item_id'] ) && isset( $_GET['color'] ) )
		{
			$myItemId = $_GET['item_id'];
			$myColor = $_GET['color'];
			settype( $myItemId, 'integer' );
			settype( $myColor, 'integer' );
			if( !createUniqueItem( $myItemId, $clan_id, $myColor ) )
				echo "<font color=red>�� ������� ������� ���� #$myItemId ���������. ��������.</font><br>";
			else
				echo "<font color=green>��������� ���� ������ �������� ������� ������ ������.</font><br>";
		}
		
		// seal_action=2
		// ������� ������ � �������� �� ����� ������
		if ( $action == 2 && isset( $_GET['item_id'] ) )
		{
			$myItemId = $_GET['item_id'];
			settype( $myItemId, 'integer' );
			
			if( !removeUniqueItem( $myItemId, $clan_id ) )
				echo "<font color=red>�� ������� ������ ������ � �������� #$myItemId, ����� ������.</font><br>";
			else
				echo "<font color=green>��������� ���� ������ �� �������� ������� ������.</font><br>";
		}
		
		// seal_action=3
		// ������� ������ �� ����� ������
		if ( $action == 3 && isset( $_GET['item_id'] ) && isset( $_GET['color'] ) )
		{
			$myItemId = $_GET['item_id'];
			settype( $myItemId, 'integer' );
			$myColor = $_GET['color'];
			settype( $myColor, 'integer' );
			
			// ��� ������ ������������� ����� ������ ������� �����
			if ( $myColor > 4 || $myColor < 0 )
				$myColor = 0;
			
			if( !returnUniqueItem( $myItemId, $clan_id, $myColor ) )
				echo "<font color=red>�� ������� ������� ������� #$myItemId �� �����, ��������.</font><br>";
			else
				echo "<font color=green>��������� ���� ���������� �� ��������� ����� ���������� ������.</font><br>";
		}
	}

	// �������� ���� �������
	//if ( $_GET['do_seal'] == 1 )
	{
		// ������������ ������ ��� ��������� ��� ��������
		echo "<ul><li><a href=game.php?order=silo&do_seal=2>���������� ��� ����, �� ������� ����� ��������� ������</a></li>";
		echo "<li><a href=game.php?order=silo&do_seal=3>���������� ���������� ����, ����������� �� ������ ������</a></li>";
		echo "<li><a href=game.php?order=silo&do_seal=4>���������� ���������� ����, ����������� � �������</a></ul></li>";
	}
	
	// �������� ������� "���� ��� ������ �� ������"
	if ( $_GET['do_seal'] == 2 )
	{
		// ������� ��� ������ ����� ��� ������
		//style=\"border-width: 1px; border-style: dashed; border-color: black;\"
		echo "<b>����������� �� ������ ������ ���� ��� ������:</b><br>";
		echo "<table class=orderItems><tr><th width=340>�������� ����</th><th width=120>�����</th><th width=240>��������</th></tr>";
		
		// ���� ������������� ������ �_� ���, ������� ���, ����������!
		// �� �������� ��� ���� ����������� ���� �� ������ ������, �������� ��� ���� ���������� ����
		$res = f_MQuery( "SELECT items.name, items.item_id, items.parent_id, clan_items.item_id, clan_items.color, clan_items.number, items.type, items.level, items.decay, items.max_decay, items_order.unique_id FROM items INNER JOIN clan_items ON items.item_id = clan_items.item_id LEFT OUTER JOIN items_order ON items.item_id=items_order.unique_id  WHERE clan_items.clan_id=$clan_id AND items.type > 1 AND items.type < 20 ORDER BY items.level, items.name, items.item_id" );
		while ( $arr = f_MFetch( $res ) ) if( !$arr['unique_id'] )
		{
			$color = $arr["color"];
			echo "<tr class=bodyTr><td><a href=help.php?id=1010&item_id=$arr[parent_id] target=_blank>$arr[name]</a> <small>($arr[decay]/$arr[max_decay], $arr[level] ��, $arr[number]��)</small></td><td align=center><font color=$siloColors[$color]>$siloShelves[$color]</font></td><td align=center><a href=game.php?order=silo&do_seal=2&seal_action=1&item_id=$arr[item_id]&color=$color>��������</a></td></tr>";
		}
		echo "</table>";
		// -----8<--------------------
	}
	
	// ���� � ������� �� ������
	if ( $_GET['do_seal'] == 3 )
	{
		// ������� ��� ������ ���������� �����, ����������� �� ������
		echo "<b>������������� ������ ����, ����������� �� ������:</b><br>";
		echo "<table class=orderItems><tr><th width=340>�������� ����</th><th width=120>�����</th><th width=240>��������</th></tr>";
		
		// ��� ������� ����� ���������� � ���� �������� � �������� ����� NOT
		$res = f_MQuery( "SELECT items.name, items.item_id, items.parent_id, clan_items.item_id, clan_items.color, items.type, items.level, items.decay, items.max_decay, items_order.unique_id FROM items, clan_items, items_order WHERE clan_items.clan_id=$clan_id AND items.item_id=clan_items.item_id AND items.type > 1 AND items.type < 20 AND items.item_id=items_order.unique_id ORDER BY items.level, items.name, items.item_id" );
		while ( $arr = f_MFetch( $res ) )
		{
			$color = $arr["color"];
			echo "<tr class=bodyTr><td><a href=help.php?id=1010&item_id=$arr[parent_id] target=_blank>$arr[name]</a> <small>($arr[decay]/$arr[max_decay], $arr[level] ��.)</small></td><td align=center><font color=$siloColors[$color]>$siloShelves[$color]</font></td><td align=center><a href=game.php?order=silo&do_seal=3&seal_action=2&item_id=$arr[item_id]>������ ������</a></td></tr>";
		}
		echo "</table>";
		// -----8<--------------------
	}
	
	// ���� � ������� � �������, � ���������� � � �������� ��������� 
	if ( $_GET['do_seal'] == 4 )
	{
		// ������� ��� ������ ���������� �����, ����������� � ������� � ���������
		echo "<br><b>������������� ������ ����, ����������� � ��������� �������:</b><br>";
		echo "<table class=orderItems><tr><th width=300>�������� ����</th><th width=180>�����</th><th width=200>��������</th></tr>";
		
		// ��� ������� ����� ���������� � ���� �������� � �������� ����� NOT
		$res = f_MQuery( "SELECT items.name, items.item_id, items.parent_id, player_items.item_id, player_items.player_id, items.type, items.level, items.decay, items.max_decay, items_order.unique_id, characters.player_id FROM items, player_items, items_order, characters WHERE items.item_id=player_items.item_id AND items.type > 1 AND items.type < 20 AND characters.player_id=player_items.player_id AND items.item_id=items_order.unique_id AND items_order.order_id=$clan_id ORDER BY items.level, items.name, items.item_id" );
		while ( $arr = f_MFetch( $res ) )
		{
			$plr = new Player( $arr["player_id"] );
						echo "<tr class=bodyTr><td><a href=help.php?id=1010&item_id=$arr[parent_id] target=_blank>$arr[name]</a> <small>($arr[decay]/$arr[max_decay], $arr[level] ��.)</small></td><td><script>document.write( ".$plr->Nick( )." );</script></td><td align=center><a href=game.php?order=silo&do_seal=4&seal_action=2&item_id=$arr[item_id]>������ ������</a> | &nbsp; <a href=game.php?order=silo&do_seal=4&seal_action=3&item_id=$arr[item_id]&color=0><img src=images/shelves/red.gif border=0 title=\"������� �� ������� ����� ������\"></a> &nbsp; <a href=game.php?order=silo&do_seal=4&seal_action=3&item_id=$arr[item_id]&color=1><img src=images/shelves/purple.gif border=0 title=\"������� �� ��������� ����� ������\"></a> &nbsp; <a href=game.php?order=silo&do_seal=4&seal_action=3&item_id=$arr[item_id]&color=2><img src=images/shelves/yellow.gif border=0 title=\"������� �� ������ ����� ������\"></a> &nbsp; <a href=game.php?order=silo&do_seal=4&seal_action=3&item_id=$arr[item_id]&color=3><img src=images/shelves/blue.gif border=0 title=\"������� �� ����� ����� ������\"></a> &nbsp; <a href=game.php?order=silo&do_seal=4&seal_action=3&item_id=$arr[item_id]&color=4><img src=images/shelves/green.gif border=0 title=\"������� �� ������� ����� ������\"></a></td></tr>";
		}
		echo "</table>";
		// -----8<--------------------
		
		// ������� ��� ������ ���������� �����, ����������� � ������� � ���������
		echo "<br><b>������������� ������ ����, ����������� � ���������� �������:</b><br>";
		echo "<table class=orderItems><tr><th width=300>�������� ����</th><th width=180>�����</th><th width=200>��������</th></tr>";
		
		$res = f_MQuery( "SELECT items.name, items.item_id, items.parent_id, player_warehouse_items.item_id, player_warehouse_items.player_id, items.type, items.level, items.decay, items.max_decay, items_order.unique_id, characters.player_id FROM items, player_warehouse_items, items_order, characters WHERE items.item_id=player_warehouse_items.item_id AND items.type > 1 AND items.type < 20 AND characters.player_id=player_warehouse_items.player_id AND items.item_id=items_order.unique_id AND items_order.order_id=$clan_id ORDER BY items.level, items.name, items.item_id" );
		while ( $arr = f_MFetch( $res ) )
		{
			$plr = new Player( $arr["player_id"] );
						echo "<tr class=bodyTr><td><a href=help.php?id=1010&item_id=$arr[parent_id] target=_blank>$arr[name]</a> <small>($arr[decay]/$arr[max_decay], $arr[level] ��.)</small></td><td><script>document.write( ".$plr->Nick( )." );</script></td><td align=center><a href=game.php?order=silo&do_seal=4&seal_action=2&item_id=$arr[item_id]>������ ������</a> | &nbsp; <a href=game.php?order=silo&do_seal=4&seal_action=3&item_id=$arr[item_id]&color=0><img src=images/shelves/red.gif border=0 title=\"������� �� ������� ����� ������\"></a> &nbsp; <a href=game.php?order=silo&do_seal=4&seal_action=3&item_id=$arr[item_id]&color=1><img src=images/shelves/purple.gif border=0 title=\"������� �� ��������� ����� ������\"></a> &nbsp; <a href=game.php?order=silo&do_seal=4&seal_action=3&item_id=$arr[item_id]&color=2><img src=images/shelves/yellow.gif border=0 title=\"������� �� ������ ����� ������\"></a> &nbsp; <a href=game.php?order=silo&do_seal=4&seal_action=3&item_id=$arr[item_id]&color=3><img src=images/shelves/blue.gif border=0 title=\"������� �� ����� ����� ������\"></a> &nbsp; <a href=game.php?order=silo&do_seal=4&seal_action=3&item_id=$arr[item_id]&color=4><img src=images/shelves/green.gif border=0 title=\"������� �� ������� ����� ������\"></a></td></tr>";
		}
		echo "</table>";
		// -----8<--------------------
		
		// ������� ��� ������ ���������� �����, ����������� ������ �������� ��������
		echo "<br><b>������������� ������ ����, ����������� � �������� ���������:</b><br>";
		echo "<table class=orderItems><tr><th width=300>�������� ����</th><th width=180>�����</th><th width=200>��������</th></tr>";
		
		$res = f_MQuery( "SELECT items.name, items.item_id, items.parent_id, post_items.item_id, post_items.entry_id, post.receiver_id, items.type, items.level, items.decay, items.max_decay, items_order.unique_id, characters.player_id FROM items, post, post_items, items_order, characters WHERE items.item_id=post_items.item_id AND post.entry_id=post_items.entry_id AND items.type > 1 AND items.type < 20 AND characters.player_id=post.receiver_id AND items.item_id=items_order.unique_id AND items_order.order_id=$clan_id ORDER BY items.level, items.name, items.item_id" );
		while ( $arr = f_MFetch( $res ) )
		{
			$plr = new Player( $arr["player_id"] );
						echo "<tr class=bodyTr><td><a href=help.php?id=1010&item_id=$arr[parent_id] target=_blank>$arr[name]</a> <small>($arr[decay]/$arr[max_decay], $arr[level] ��.)</small></td><td><script>document.write( ".$plr->Nick( )." );</script></td><td align=center><a href=game.php?order=silo&do_seal=4&seal_action=2&item_id=$arr[item_id]>������ ������</a> | &nbsp; <a href=game.php?order=silo&do_seal=4&seal_action=3&item_id=$arr[item_id]&color=0><img src=images/shelves/red.gif border=0 title=\"������� �� ������� ����� ������\"></a> &nbsp; <a href=game.php?order=silo&do_seal=4&seal_action=3&item_id=$arr[item_id]&color=1><img src=images/shelves/purple.gif border=0 title=\"������� �� ��������� ����� ������\"></a> &nbsp; <a href=game.php?order=silo&do_seal=4&seal_action=3&item_id=$arr[item_id]&color=2><img src=images/shelves/yellow.gif border=0 title=\"������� �� ������ ����� ������\"></a> &nbsp; <a href=game.php?order=silo&do_seal=4&seal_action=3&item_id=$arr[item_id]&color=3><img src=images/shelves/blue.gif border=0 title=\"������� �� ����� ����� ������\"></a> &nbsp; <a href=game.php?order=silo&do_seal=4&seal_action=3&item_id=$arr[item_id]&color=4><img src=images/shelves/green.gif border=0 title=\"������� �� ������� ����� ������\"></a></td></tr>";
		}
		echo "</table>";
		// -----8<--------------------
	}
}
else
{
    echo "<br>������� ������: <b>$blevel</b><br>";
    echo "�����������: <b>".getSiloCurCapacity( $clan_id )."/".getSiloCapacity( $blevel )."</b><br>";
    echo "��� �����: <b>".(getSiloCurWeight( $clan_id )/100.0)."/".getSiloWeight( $blevel )."</b><br>";

	echo "<ul>";
	echo "<li><a href=game.php?order=silo&do_put=1>�������� ���� �� �����</a><br>";
	echo "<li><a href=game.php?order=silo&do_take=1>����� ���� �� ������</a><br>";

	if( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_CONTROL_SHOP ) )
	{
		echo "<li><a href=game.php?order=silo&do_shelves=1>��������� �������� �����</a><br>";
	}
	
	echo "<br>��������� ������� ��� ��������� ���������� �� ������ � ����� ������� �� �����:<br><li><a href=game.php?order=silo&do_weaponary=1>���������� � �����������</a>";

	if( ( 0 != ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_CONTROL_SHOP ) ) )
	{
		echo "<br><br><li>�� ������ ������ � ������ ������� � <a href=game.php?order=silo&do_seal=1>�������� ���� ������ �������</a>";
	}

	echo "</ul>";
}


?>
