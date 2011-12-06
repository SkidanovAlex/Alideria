<?

// included by clan_room.php

if( !$mid_php ) die( );

echo "<b>Постройки Ордена</b> - <a href=game.php?order=main>Назад</a><br>";
echo "<small>Глина, Камень и Древесина для постройки должны лежать на красной полке склада.</small><br>";

// actions
if( isset( $_GET['fast'] ) )
{
	// Ускоренная стройка за таланты
	
	// Убеждаемся, что что-то вообще строится
	$arr = f_MFetch( f_MQuery( "SELECT * FROM clan_build_queue WHERE clan_id={$player->clan_id} ORDER BY entry_id" ) );
	
	if( !$arr )
	{
		$player->syst2( 'Ваш Орден ничего не строит в данный момент' );	
	}
	elseif( $arr['deadline'] == 0 )
	{
		$player->syst2( 'Попробуй ещё раз' );	
	}
	else
	{
		// Пробуем оплатить
		if( $player->SpendUMoney( 1 ) )
		{
			$deadline = $arr['deadline'] - 3600;
			f_MQuery( "UPDATE clan_build_queue SET deadline = $deadline WHERE entry_id = $arr[entry_id]" );
			
			$buildPhrase = array( 'Боги довольны твоим поступком!', '<a href="/forum.php?thread=6141" target="_blank">Участвуй в конкурсе, придумай продолжение для этой фразы!</a>' );
			$player->syst2( 'Время ускорило свой ход на строительной площадке, вверенной прорабу <b>'.$player->login.'</b>. '.$buildPhrase[mt_rand( 0, 1 )] );
			$player->AddToLogPost(-1, -1, 1004, $player->clan_id);
			
			$Rein = new Player( 6825 );
			$Rein->syst2( 'Персонаж <a href="/player_info.php?nick='.$player->login.'" target="_blank"><b>'.$player->login.'</b></a> оплатил ускоренную стройку на <b>1 час</b>' );

		}
		else
		{
			$player->syst2( 'У тебя нехватает талантов для ускоренной стройки.' );	
		}
	}
}

if( isset( $_GET['que'] ) )
{
	$id = $_GET['que'];
	settype( $id, 'integer' );
	if( !isset( $buildings[$id] ) ) RaiseError( "Попытка построить несуществующее здание", "$id" );
	$level = getBLevel( $id );
	$can_build=  true;
	if( isset( $building_reqs[$id] ) && getBLevel( $building_reqs[$id] ) < $level + 1 )
	{
		if( getBLevel( $building_reqs[$id] ) < ($level + 1) )
			$can_build = false;
	}

	if( 0 == ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_BUILD ) )
		$player->syst( "У вас нет прав строить здания." );


	if( !$can_build ) RaiseError( 'Попытка добавить в очередь здание, требования для постройки которого не выполнены' );
	f_MQuery( "LOCK TABLE clan_build_queue WRITE" );
	$res = f_MQuery( "SELECT entry_id FROM clan_build_queue WHERE clan_id=$clan_id AND building_id=$id" );
	if( f_MNum( $res ) ) 
	{
		$player->syst( "Это здание уже в очереди на постройку" ); 
		f_MQuery( "UNLOCK TABLES" );
	}
	else
	{
		$res = f_MQuery( "SELECT count( building_id ) FROM clan_build_queue WHERE clan_id=$clan_id" );
		$arr = f_MFetch( $res );
		if( $arr[0] >= 6 )
		{
    		$player->syst( "В очереди не может быть больше 6-ти зданий" ); 
    		f_MQuery( "UNLOCK TABLES" );
		}
		else if( playerSpendControlPoint( $clan_id, $player->player_id ) )
		{
			f_MQuery( "INSERT INTO clan_build_queue ( clan_id, building_id ) VALUES ( $clan_id, $id )" );
			f_MQuery( "UNLOCK TABLES" );
			f_MQuery( "INSERT INTO clan_log ( clan_id, action, arg0, arg1, player_id, time ) VALUES ( $clan_id, 1, $id, 1, {$player->player_id}, ".time()." )" );
		}
	}
}
else if( isset( $_GET['unque'] ) )
{
	$id = $_GET['unque'];
	settype( $id, 'integer' );
	if( 0 == ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_BUILD ) )
		$player->syst( "У вас нет прав отменять постройку зданий." );
	else if( playerSpendControlPoint( $clan_id, $player->player_id ) )
	{
		f_MQuery( "DELETE FROM clan_build_queue WHERE clan_id=$clan_id AND building_id=$id" );
		f_MQuery( "INSERT INTO clan_log ( clan_id, action, arg0, arg1, player_id, time ) VALUES ( $clan_id, 1, $id, -1, {$player->player_id}, ".time()." )" );
	}
}

// queue

$res = f_MQuery( "SELECT * FROM clan_build_queue WHERE clan_id=$clan_id ORDER BY entry_id" );
$arr = f_MFetch( $res );

echo "<table><tr><td><script>FLUl();</script><table>";
echo "<colgroup><col width=145><col width=145><col width=145><col width=145><col width=145>";

echo "<tr height=135><td width=145 height=135><script>FUcm();</script>";
if( !$arr ) echo "<img width=100 height=100 src=empty.gif><br>&nbsp;";
else
{
	echo "<table><tr><td><script>FLUl();</script><a title=Отменить href='#' onclick='if( confirm( \"Отменить постройку? Если работа уже начата, ресурсы возвращены не будут!\" ) ) location.href=\"game.php?order=buildings&unque=$arr[building_id]\";'><img border=0 width=120 src=images/camp/c/{$arr[building_id]}.png></a><script>FLL();</script></td></tr></table><a title=Отменить href='#' onclick='if( confirm( \"Отменить постройку? Если работа уже начата, ресурсы возвращены не будут!\" ) ) location.href=\"game.php?order=buildings&unque=$arr[building_id]\";'><b>".$buildings[$arr[building_id]]." ур. ".(1+getBLevel($arr[building_id]))."</b></a>";
}
echo "<script>FL();</script></td>";
echo "<td colspan=4><script>FUcm();</script>";

// progress bar

if( $arr )
{
	if( $arr['deadline'] == 0 )
	{
    	echo "<table width=100%><colgroup><col width=150><col width=*><tbody><tr><td>";
    	$id = $arr['building_id'];
    	$level = getBLevel( $id );
    	$qarr = getBuildingCost( $id, $level + 1 );
    	$rarr = getBuildResources( $clan_id );
    	$ok = true;
    	foreach( $qarr as $a=>$b )
    	{
    		if( !$item_names[$a] ) $item_names[$a] = f_MValue( "SELECT name FROM items WHERE item_id=$a" );
    		echo $item_names[$a].": ";
    		if( isset( $rarr[$a] ) ) 
    		{	
    			echo "<b>{$rarr[$a]}</b>/";
    			if( $rarr[$a] < $b ) $ok = false;
    		}
    		else if( $a > 0 )
    		{
    			$rarr[$a] = itemsSiloNum( $clan_id, $a );
    			echo "<b>{$rarr[$a]}</b>/";
    			if( $rarr[$a] < $b ) $ok = false;
    		}
    		echo "<b>$b</b>";
    		if( $a == -1 ) echo " ".my_word_str( $b, "час", "часа", "часов" );
    		echo "<br>";
    	}
    	if( !$ok ) echo "</td><td align=center valign=middle><i>Ожидается поставка ресурсов на склад Ордена.</i></td></tr></table>";
    	else echo "</td><td align=center valign=middle><i>Ресурсы на складе. Работа начнется в течение минуты.</i></td></tr></table>";
    }
    else
    {
    	include_js( 'js/timer.js' );
		$id = $arr['building_id'];
    	$level = getBLevel( $id );
    	$qarr = getBuildingCost( $id, $level + 1 );
    	$left = $arr['deadline'] - time( ) + 5;

    	echo "<b>Идет работа по постройке здания</b><br><script>document.write( InsertTimer( $left, 'До окончания осталось: <b>', '</b>', 1, 'location.href=\"game.php?order=buildings\";' ) );</script>";
    	echo "<br /><br />";
    	echo "За <b><img src='/images/umoney.gif' /> 1</b> можно <a href='/game.php?order=buildings&fast=1'>купить</a> <b>1 час мгновенной стройки</b>";
    }
}
else echo "<table><tr><td><i>Ваш Орден ничего не строит в данный момент.</i></td></tr></table>";

// end of progress bar

echo "<script>FL();</script></td></tr>";
                                     
echo "<tr height=135>";

for( $i = 0; $i < 5; ++ $i )
{
	if( $arr ) $arr = f_MFetch( $res );
	echo "<td width=145 height=135><script>FUcm();</script>";

    if( !$arr ) echo "<img width=100 height=100 src=empty.gif><br>&nbsp;";
    else
    {
    	echo "<table><tr><td><script>FLUl();</script><a title=Удалить href='#' onclick='if( confirm( \"Убрать постройку из очереди?\" ) ) location.href=\"game.php?order=buildings&unque=$arr[building_id]\";'><img border=0 width=120 src=images/camp/c/{$arr[building_id]}.png></a><script>FLL();</script></td></tr></table><a title=Удалить href='#' onclick='if( confirm( \"Убрать постройку из очереди?\" ) ) location.href=\"game.php?order=buildings&unque=$arr[building_id]\";'><b>".$buildings[$arr[building_id]]." ур. ".(1+getBLevel($arr[building_id]))."</b></a>";
    }

	echo "<script>FL();</script></td>";
}

echo "</tr>";

echo "</table><script>FLL();</script></td></tr></table>";

// buildings list
echo "<table><tr><td><script>FLUl();</script><table>";

foreach( $buildings as $id=>$name ) if( ($id != 14 && $id != 6) || $clan_id == 1 )
{
	echo "<tr height=135>";
	echo "<td width=135 height=135><script>FUcm();</script><table><tr><td><script>FLUl();</script><img width=120 src=images/camp/c/$id.png><script>FLL();</script></td></tr></table><script>FL();</script></td>";
    $level = getBLevel( $id );                                                                                                        
    
	echo "<td width=420 height=135><script>FUlt();</script><b>$name, уровень $level</b><br>".$building_descriptions[$id]."<br><br>".getBuildingStatus( $id, $level )."<script>FL();</script></td>";
	if( $id == 1 && $level >= 11 )
	{
		echo "<td>&nbsp;</td>";
		continue;
	}
	echo "<td width=170 height=135><script>FUlt();</script>";
	echo "<b>Стоимость улучшения:</b><br>";
	$arr = getBuildingCost( $id, $level + 1 );
	foreach( $arr as $a=>$b )
	{
		if( !$item_names[$a] ) $item_names[$a] = f_MValue( "SELECT name FROM items WHERE item_id=$a" );
		echo $item_names[$a].": <b>$b</b>";
		if( $a == -1 ) echo " ".my_word_str( $b, "час", "часа", "часов" );
		echo "<br>";
	}
	$can_build = true;
	if( isset( $building_reqs[$id] ) && getBLevel( $building_reqs[$id] ) < $level + 1 )
	{
		echo "Требует: <b>".$buildings[$building_reqs[$id]].", ур. ".($level + 1)."</b><br>";
		if( getBLevel( $building_reqs[$id] ) < ($level + 1) )
			$can_build = false;
	}
	if( 0 == ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_BUILD ) ) $can_build = false;

	if( $can_build ) echo "<a href=game.php?order=buildings&que=$id>Добавить в Очередь</a>";
	echo "<script>FL();</script></td>";
	echo "</tr>";
}

echo "</table><script>FLL();</script></td></tr></table>";

?>
