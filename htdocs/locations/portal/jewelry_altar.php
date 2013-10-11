<?

include_once( 'items.php' );
include_once( 'guild.php' );
include_once( 'prof_exp.php' );

$guild_id = JEWELRY_GUILD;
$guild = new Guild( $guild_id );
if( !$guild->LoadPlayer( $player->player_id ) )
{
	if( !isset( $mid_php ) ) die( );
	
	echo "<br>�� �� �������� � <a href=help.php?id={$guilds[$guild_id][1]} target=_blank>������� {$guilds[$guild_id][0]}</a> � �� ������ ��� ��������.<br>";
	echo "�������� � ������� ����� � <a href=help.php?id=34274 target=_blank>���� �������</a> � <a href=help.php?id=34265 target=_blank>��������� ������</a>.<br>";
	return;
}

$item_ids = array( 15001, 15003, 15004 );
$items_in = implode( ",", $item_ids );

$recipes = array(
	array( 118 => 1, 97 => 10, 111 => 1, 274 => 2 ),
	array( 118 => 1, 99 => 10, 113 => 1, 275 => 2 ),
	array( 118 => 1, 94 => 10, 112 => 1, 276 => 2 )
);

function a_compare1( $a1, $a2, $param )
{
	if( $a1[$param] < $a2[$param] ) return -1;
	if( $a1[$param] > $a2[$param] ) return 1;
	return 0;
}

function a_compare( $a1, $a2 )
{
	$a = a_compare1( $a1, $a2, "parent_id" ); if( $a ) return $a;
	$a = a_compare1( $a1, $a2, "_value" ); if( $a ) return $a;
	$a = a_compare1( $a1, $a2, "max_decay" ); if( $a ) return $a;
	$a = a_compare1( $a1, $a2, "decay" ); if( $a ) return $a;
	return 0;
}

function enum_items( )
{
	global $player;
	global $item_ids;

	$ret = array( );
	$str = implode( ",", $item_ids );
	$res = f_MQuery( "SELECT * 
	                  FROM items as i INNER JOIN player_items as p
	                  ON i.item_id = p.item_id
	                  WHERE p.player_id={$player->player_id}
	                  AND ( i.item_id IN ( $str ) OR i.parent_id IN ( $str ) )
	                  AND p.weared=0
	                  ORDER BY i.parent_id, i.effect" );
	while( $arr = f_MFetch( $res ) )
	{
		// here we're checking whether item may be improved or not
		$aa = ParseItemStr( $arr['effect'] );
		$ok = true;
		foreach( $aa as $attr_id => $value ) // there must be only one element in array
		{
			if( $value == 100 ) $ok = false;

			$arr['_attr_id'] = $attr_id;
			$arr['_value'] = $value;
		}

		if( $ok )
			array_push( $ret, $arr );
	}
	usort( $ret, a_compare );
	// unique
	$n = 1;
	for( $i = 1; $i < count( $ret ); ++ $i )
		if( a_compare( $ret[$i - 1], $ret[$i] ) )
		{
			$ret[$n] = $ret[$i];
			++ $n;
		}
		else $ret[$i - 1]['number'] += $ret[$i]['number'];
	$ret = array_slice( $ret, 0, $n );
	return $ret;
}

function getSqlArr( )
{
	global $player;
	return f_MFetch( f_MQuery( "SELECT * FROM jewelry_altar WHERE player_id={$player->player_id}" ) );
}

$has_seat = false;
$arr = getSqlArr( );

if( !$arr || $arr['expires'] < time( ) ) $has_seat = false;
else $has_seat = true;

if( $player->regime >= 300 ) $has_seat = true;


// ��� ����� - �������� �� ����������� �� ������ � ���� �����������

if( !$has_seat && $player->regime == 119 && $player->till <= time( ) + 2 )
{
	f_MQuery( "LOCK TABLE jewelry_altar WRITE" );
	$val_until = time( ) + 30*24*3600;
	if( f_MValue( "SELECT count( player_id ) FROM jewelry_altar WHERE player_id={$player->player_id}" ) )
		f_MQuery( "UPDATE jewelry_altar SET expires=GREATEST($val_until, expires + 30*24*3600) WHERE player_id={$player->player_id}" );
	else f_MQuery( "INSERT INTO jewelry_altar ( player_id, expires ) VALUES ( {$player->player_id}, $val_until )" );
	f_MQuery( "UNLOCK TABLES" );
	$has_seat = true;
	$arr = f_MFetch( f_MQuery( "SELECT * FROM jewelry_altar WHERE player_id={$player->player_id}" ) );
	$player->SetRegime( 0 );
	$player->SetTill( 0 );
}




// ��� �����



if( !$has_seat )
{
	$seat_res = array( 87 => 10, 88 => 5, 89 => 4, 90 => 3, 91 => 1, 36 => 1 );
	if( $player->regime == 0 && $_GET['jewelry_do'] == 1 )
	{
		if( $player->DropItemsArr( $seat_res, 36, 1, 3 ) || $player->player_id == 173 )
		{
			$player->SetRegime( 119 );
			$player->SetTill( time( ) + 15 * 30 ); // 7.5 min
		}
		else echo "<font color=darkred>��� ������������ ���������� ��������</font><br>";
	}
	if( $player->regime == 119 && $_GET['jewelry_do'] == 2 )
	{
		foreach( $seat_res as $a => $b )
		{
			$player->AddItems( $a, $b );
			$player->AddToLogPost( $a, $b, 36, 1, 3 );
		}
		$player->SetRegime( 0 );
		$player->SetTill( 0 ); // 7.5 min
	}
	

	if( $player->regime == 119 )
	{
		echo "<br><br>�� ����������� ������������� ������� ����� ��� ������ � ������.<br>";
		include_js( 'js/timer.js' );
		$rem = $player->till - time( );
		echo "<script>document.write( InsertTimer( $rem, '��������: <b>', '</b>', 0, 'location.href=\"game.php\";' ) );</script>";
		?><script>function cancel_work() { if( confirm( "���������� ������������ ����� � ������?" ) ) location.href='game.php?jewelry_do=2'; }</script><?
		echo "<br><li><a href=javascript:cancel_work()>��������</a>";
	}
	else
	{
    	echo "�������� ���������� ����� �� ������ �������� ������� ������ �����������������. ��� ���������� ������ �����, �� ������� �� ������� �������� ������� �������, ������� ����� ����������, ����� ���� ����������. ��� ���� �����, ������ ��� ������ ����, ��� ���� ��������� ����� �� ����� ��� ���������� ������ � ��������. ������� �������� ��� ���������� ���� ����� ��������������. ������� ��� �� ����� ������, �� ������� ������������ ��������. ��� �����������:<br>";
    	echoItemsList( $seat_res );
    	echo "������, ��� ��������, ����� �������� �����������, ������� ����� ����� ����� �������� ������������ ������, ���� ��� ������� �� ���������.<br>";
    	?><script>function buy_workship(){if( confirm( "������ ������������ ����� � ������?" ) ) location.href='game.php?jewelry_do=1'; }</script><?
    	echo "<br><li><a href='javascript:buy_workship()'>������</a>";
	}
}





// ���� �����



if( $has_seat )
{
	// hack - ����������� ����
	if( $player->regime == 119 )
	{
		$player->SetRegime( 0 );
		$player->SetTill( 0 );
	}

	echo "<table width=100%><tr><td><script>FLUl();</script>";

	$mode = "work";
	if( $_GET['mode'] == 'prepare' ) $mode = 'prepare';
	
	// ������ �����
	if( $player->regime == 0 && isset( $_GET['do_color'] ) )
	{
		$clr = (int)$_GET['do_color'];
		if( $clr < 0 || $clr >= 3 ) RaiseError( "�������� ����� ����� �� ������ ��������", "$clr" );
		if( $player->DropItemsArr( $recipes[$clr], 36, 1, 4 ) || $player->player_id == 173 )
		{
			$player->SetRegime( 300 + $clr );
			$player->SetTill( time( ) + 10 * 60 );
		}
		else echo "<font color=darkred>������������ ��������</font><br>";
	}

	$titles = array( "�����", "�������", "�������" );
	$clrs = array( "darkblue", "darkgreen", "darkred" );

	// ���������� �����
	if( $player->regime >= 300 && $player->regime < 303 )
	{
		$id = $player->regime - 300;
		if( $player->till <= time( ) + 2 )
		{
			$player->SetRegime( 0 );
			$player->SetTill( 0 );
			$cols = array( "blues", "greens", "reds" );
			$col = $cols[$id];
			f_MQuery( "UPDATE jewelry_altar SET $col = $col + 1 WHERE player_id={$player->player_id}" );
			$PO = AlterProfExp( $player, 10 );
			$player->syst( "�� ��������� {$titles[$id]} ����� $PO" );
			$arr = getSqlArr( );
			$mode = 'prepare';
		}
		else if( isset( $_GET['cancel'] ) )
		{
    		foreach( $recipes[$id] as $a => $b )
    		{
    			$player->AddItems( $a, $b );
    			$player->AddToLogPost( $a, $b, 36, 1, 1 );
    		}
			$player->SetRegime( 0 );
			$player->SetTill( 0 );
			$mode = 'prepare';
		}
	}


	// �������� ������
	if( $player->regime == 0 && isset( $_GET['jewelry_do'] ) )
	{
		$carr = $arr; // �������� ���������� ������	

		$item_id = (int)$_GET['jewelry_do'];
		$res = f_MQuery( "SELECT i.*, p.number FROM player_items as p INNER JOIN items as i ON p.item_id=i.item_id WHERE p.player_id={$player->player_id} AND i.item_id=$item_id AND ( i.item_id IN( $items_in ) OR i.parent_id IN ( $items_in ) )" );
		$arr = f_MFetch( $res );
		if( !$arr || !$arr['number'] ) RaiseError( "������� �������� �� ������ �������� ����, ������� ���� � ���������", "$item_id" );

    	$aa = ParseItemStr( $arr['effect'] );
    	// we assume here that there's only one attribute on the item
    	foreach( $aa as $_attr_id => $_value )
    	{
    		$attr_id = $_attr_id;
    		$value = $_value;
    	}

    	$col = -1;
    	$cols = array( "blues", "greens", "reds" );
    	for( $i = 0; $i < 3; ++ $i ) if( $item_ids[$i] == $arr['parent_id'] ) $col = $cols[$i];
    	if( $col == -1 ) RaiseError( "����, ���������� �� ������ ��������, �� ����������, ��� ������������ ������", "ITEM ID: $item_id PARENT ID: $arr[parent_id]" );

    	$check = 3 * ( ( $value * 18 ) + 13 ); // pont :o)

		if( $carr[$col] <= 0 ) echo "<font color=darkred>������������ ������</font><br>";
    	else if( (int)($value / 10) > $guild->rating )
    	{
    		echo "<font color=darkred>��� ������� ������������ ��� ��������� ����</font><br>";
    	}
    	else if( $_GET['check'] == $check )
    	{  
    		-- $carr[$col];
    		++ $value;
    		if( $player->DropItems( $item_id, 1 ) )
    		{
	    		f_MQuery( "UPDATE jewelry_altar SET $col = $col - 1 WHERE player_id={$player->player_id}" );
        		$player->AddToLogPost( $item_id, -1, 36, 1, 0 );
       	   		$item_id = copyItem( $item_id, true );
    			f_MQuery( "UPDATE items SET effect='{$attr_id}:{$value}.' WHERE item_id=$item_id" );
        		$player->AddItems( $item_id, 1 );
        		$player->AddToLogPost( $item_id, 1, 36, 1, 2 );
    		}
    	}

    	$arr = $carr;
	}


	echo "���� ������ ����� ��������� ��� ��: <b>".date( "d.m.Y H:i", $arr['expires'] )."</b> &mdash; <a href=help.php?id=50200 target=_blank>�������� ������</a><br>";
	echo "������� ������: <b><font color=darkblue>$arr[blues]</font></b> �����, <b><font color=darkgreen>$arr[greens]</font></b> �������, <b><font color=darkred>$arr[reds]</font></b> �������";

	if( $player->regime == 0 )
	{
		if( $mode == 'prepare' ) echo " &mdash; <a href=game.php>���������</a>";
		else echo " &mdash; <a href=game.php?mode=prepare>�����������</a>";
	}
	
	echo "<script>FLL();</script></td></tr></table><br>";

	if( $player->regime >= 300 && $player->regime < 303 )
	{
		$id = $player->regime - 300;
		include_js( "js/timer.js" );

		$rem = $player->till - time( );
		echo "�� ����������� <b><font color={$clrs[$id]}>{$titles[$id]}</font> �����</b><br>";
		echo "<script>show_timer_title = true; document.write( InsertTimer( $rem, '��������: <b>', '</b>', 0, 'location.href=\"game.php\";' ) );</script>";
		?><script>function cancel_inmprovement(){if(confirm('�������� ������?')) location.href='game.php?cancel=1';}</script><?
		echo "<br><a href='javascript:cancel_inmprovement()'>��������</a></center>";
	}

	else if( $mode == 'prepare' )
	{
		echo "<table><tr>";
		for( $i = 0; $i < 3; ++ $i )
		{ 
			echo "<td align=center><script>FLUc();</script><b>����������� <font color={$clrs[$i]}>{$titles[$i]}</font> �����:</b><br>";
			echoItemsList( $recipes[$i] );
			echo "<a href=game.php?mode=prepare&do_color=$i>��������</a><script>FLL();</script></td>";
		}
		echo "</tr></table>";
	}
	else if( $mode == "work" )
	{
        $items = enum_items( );

        $table_started = false;
        $col_index = 0;
        $cols_per_row = 6;

        function StartRow( )
        {
        	global $table_started;
        	global $cols_per_row;

        	if( !$table_started )
        	{
        		echo "<table><tr><td><script>FLUl();</script><table><colgroup>\n";
        		for( $i = 0; $i < $cols_per_row; ++ $i ) echo "<col width=140>";
        		$table_started = true;
        	}
        	else echo "</tr>\n";
        	echo "<tr>\n";
        }
        function StartCell( )
        {
        	global $col_index;
        	global $cols_per_row;
        	if( $col_index == 0 )
        		StartRow( );
        	echo "<td width=140 height=150><script>FUcm();</script>\n";
        }

        function EndCell( )
        {
        	global $col_index;
        	global $cols_per_row;
        	++ $col_index;
        	if( $col_index == $cols_per_row ) $col_index = 0;
        	echo "<script>FL();</script></td>\n";
        }

        function EndTable( )
        {
        	global $col_index;
        	global $cols_per_row;
        	while( $col_index != 0 )
        	{
        		StartCell( );
        		echo "&nbsp;";
        		EndCell( );
        	}
        	echo "</tr></table><script>FLL();</script></td></tr></table>\n";
        }

        echo "\n\n";
        foreach( $items as $arr )
        {
        	$aa = ParseItemStr( $arr['effect'] );
        	// we assume here that there's only one attribute on the item
        	foreach( $aa as $_attr_id => $_value )
        	{
        		$attr_id = $_attr_id;
        		$value = $_value;
        	}
        	StartCell( );
        	echo "<img width=50 height=50 border=0 src=images/items/$arr[image]><br><small>[$arr[number]] <b>$arr[name]<br>���������: $arr[decay]/$arr[max_decay]<br>";
        	$aarr = f_MFetch( f_MQuery( "SELECT * FROM attributes WHERE attribute_id=$attr_id" ) );
        	echo "<font color=$aarr[color]>$aarr[name]</font>:<br>";
        	if( $attr_id == 313 ) echo "<br>";
        	echo "</b></small>";
        	echo "<big><b>{$value}%</b></big>";
        	$check = 3 * ( ( $value * 18 ) + 13 ); // pont :o)
        	echo "<br><small><a href=game.php?jewelry_do=$arr[item_id]&check=$check>�������� �� 1%</a></small>";
        	EndCell( );
        }

        if( $table_started ) EndTable( );
        else
        {
        	echo "<i>� ��� ��� �� ������ ������������� �������.</i><br>";
        }
    }
}


?>
