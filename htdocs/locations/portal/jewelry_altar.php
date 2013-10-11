<?

include_once( 'items.php' );
include_once( 'guild.php' );
include_once( 'prof_exp.php' );

$guild_id = JEWELRY_GUILD;
$guild = new Guild( $guild_id );
if( !$guild->LoadPlayer( $player->player_id ) )
{
	if( !isset( $mid_php ) ) die( );
	
	echo "<br>Вы не состоите в <a href=help.php?id={$guilds[$guild_id][1]} target=_blank>Гильдии {$guilds[$guild_id][0]}</a> и не можете тут работать.<br>";
	echo "Вступить в гильдию можно в <a href=help.php?id=34274 target=_blank>Зале Гильдий</a> в <a href=help.php?id=34265 target=_blank>Городской Управе</a>.<br>";
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


// НЕТ МЕСТА - проверим не завершилась ли работа в этом направлении

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




// НЕТ МЕСТА



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
		else echo "<font color=darkred>Нет достаточного количества ресурсов</font><br>";
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
		echo "<br><br>Вы занимаетесь обустройством уютного места для работы у алтаря.<br>";
		include_js( 'js/timer.js' );
		$rem = $player->till - time( );
		echo "<script>document.write( InsertTimer( $rem, 'Осталось: <b>', '</b>', 0, 'location.href=\"game.php\";' ) );</script>";
		?><script>function cancel_work() { if( confirm( "Прекратить обустройство места у алтаря?" ) ) location.href='game.php?jewelry_do=2'; }</script><?
		echo "<br><li><a href=javascript:cancel_work()>Отменить</a>";
	}
	else
	{
    	echo "Создание магических вещей на алтаре ювелиров требует полной сосредоточенности. Вам необходимо уютное место, на котором вы сможете провести столько времени, сколько будет необходимо, чтобы вещь улучшилась. При этом место, уютное для одного мага, для всех остальных ничем не лучше чем деревянные стулья в харчевне. Поэтому придется вам обустроить себе место самостоятельно. Процесс это не очень долгий, но требует определенных ресурсов. Вам понадобится:<br>";
    	echoItemsList( $seat_res );
    	echo "Шкурки, как известно, имеют свойство протираться, поэтому через месяц место придется обустраивать заново, если его вовремя не обновлять.<br>";
    	?><script>function buy_workship(){if( confirm( "Начать обустройство места у алтаря?" ) ) location.href='game.php?jewelry_do=1'; }</script><?
    	echo "<br><li><a href='javascript:buy_workship()'>Начать</a>";
	}
}





// ЕСТЬ МЕСТО



if( $has_seat )
{
	// hack - исправление бага
	if( $player->regime == 119 )
	{
		$player->SetRegime( 0 );
		$player->SetTill( 0 );
	}

	echo "<table width=100%><tr><td><script>FLUl();</script>";

	$mode = "work";
	if( $_GET['mode'] == 'prepare' ) $mode = 'prepare';
	
	// запуск смеси
	if( $player->regime == 0 && isset( $_GET['do_color'] ) )
	{
		$clr = (int)$_GET['do_color'];
		if( $clr < 0 || $clr >= 3 ) RaiseError( "Неверный номер смеси на алтаре ювелиров", "$clr" );
		if( $player->DropItemsArr( $recipes[$clr], 36, 1, 4 ) || $player->player_id == 173 )
		{
			$player->SetRegime( 300 + $clr );
			$player->SetTill( time( ) + 10 * 60 );
		}
		else echo "<font color=darkred>Недостаточно ресурсов</font><br>";
	}

	$titles = array( "синюю", "зеленую", "красную" );
	$clrs = array( "darkblue", "darkgreen", "darkred" );

	// завершение смеси
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
			$player->syst( "Вы получаете {$titles[$id]} смесь $PO" );
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


	// улучшаем амулет
	if( $player->regime == 0 && isset( $_GET['jewelry_do'] ) )
	{
		$carr = $arr; // запомним количество смесей	

		$item_id = (int)$_GET['jewelry_do'];
		$res = f_MQuery( "SELECT i.*, p.number FROM player_items as p INNER JOIN items as i ON p.item_id=i.item_id WHERE p.player_id={$player->player_id} AND i.item_id=$item_id AND ( i.item_id IN( $items_in ) OR i.parent_id IN ( $items_in ) )" );
		$arr = f_MFetch( $res );
		if( !$arr || !$arr['number'] ) RaiseError( "Попытка улучшить на алтаре ювелиров вещь, которой нету в инвентаре", "$item_id" );

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
    	if( $col == -1 ) RaiseError( "Вещь, улучшаемая на алтаре ювелиров, не распознана, как божественный амулет", "ITEM ID: $item_id PARENT ID: $arr[parent_id]" );

    	$check = 3 * ( ( $value * 18 ) + 13 ); // pont :o)

		if( $carr[$col] <= 0 ) echo "<font color=darkred>Недостаточно смесей</font><br>";
    	else if( (int)($value / 10) > $guild->rating )
    	{
    		echo "<font color=darkred>Ваш рейтинг недостаточен для улучшения вещи</font><br>";
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


	echo "Ваше уютное место прослужит вам до: <b>".date( "d.m.Y H:i", $arr['expires'] )."</b> &mdash; <a href=help.php?id=50200 target=_blank>Описание Алтаря</a><br>";
	echo "Готовых смесей: <b><font color=darkblue>$arr[blues]</font></b> синей, <b><font color=darkgreen>$arr[greens]</font></b> зеленой, <b><font color=darkred>$arr[reds]</font></b> красной";

	if( $player->regime == 0 )
	{
		if( $mode == 'prepare' ) echo " &mdash; <a href=game.php>Вернуться</a>";
		else echo " &mdash; <a href=game.php?mode=prepare>Приготовить</a>";
	}
	
	echo "<script>FLL();</script></td></tr></table><br>";

	if( $player->regime >= 300 && $player->regime < 303 )
	{
		$id = $player->regime - 300;
		include_js( "js/timer.js" );

		$rem = $player->till - time( );
		echo "Вы производите <b><font color={$clrs[$id]}>{$titles[$id]}</font> смесь</b><br>";
		echo "<script>show_timer_title = true; document.write( InsertTimer( $rem, 'Осталось: <b>', '</b>', 0, 'location.href=\"game.php\";' ) );</script>";
		?><script>function cancel_inmprovement(){if(confirm('Отменить работу?')) location.href='game.php?cancel=1';}</script><?
		echo "<br><a href='javascript:cancel_inmprovement()'>Отменить</a></center>";
	}

	else if( $mode == 'prepare' )
	{
		echo "<table><tr>";
		for( $i = 0; $i < 3; ++ $i )
		{ 
			echo "<td align=center><script>FLUc();</script><b>Приготовить <font color={$clrs[$i]}>{$titles[$i]}</font> смесь:</b><br>";
			echoItemsList( $recipes[$i] );
			echo "<a href=game.php?mode=prepare&do_color=$i>Готовить</a><script>FLL();</script></td>";
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
        	echo "<img width=50 height=50 border=0 src=images/items/$arr[image]><br><small>[$arr[number]] <b>$arr[name]<br>Прочность: $arr[decay]/$arr[max_decay]<br>";
        	$aarr = f_MFetch( f_MQuery( "SELECT * FROM attributes WHERE attribute_id=$attr_id" ) );
        	echo "<font color=$aarr[color]>$aarr[name]</font>:<br>";
        	if( $attr_id == 313 ) echo "<br>";
        	echo "</b></small>";
        	echo "<big><b>{$value}%</b></big>";
        	$check = 3 * ( ( $value * 18 ) + 13 ); // pont :o)
        	echo "<br><small><a href=game.php?jewelry_do=$arr[item_id]&check=$check>Улучшить на 1%</a></small>";
        	EndCell( );
        }

        if( $table_started ) EndTable( );
        else
        {
        	echo "<i>У вас нет ни одного божественного амулета.</i><br>";
        }
    }
}


?>
