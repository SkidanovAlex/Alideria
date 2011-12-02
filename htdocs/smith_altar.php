<?

include_once( "guild.php" );
include_once( 'prof_exp.php' );

$guild_id = SMITH_GUILD;

$staff_ids = array( 14893, 14894, 14892 );
$staffs_in = implode( ",", $staff_ids );

$guild = new Guild( $guild_id );
if( !$guild->LoadPlayer( $player->player_id ) )
{
	if( !isset( $mid_php ) ) die( );
	
	echo "<br>Вы не состоите в <a href=help.php?id={$guilds[$guild_id][1]} target=_blank>Гильдии {$guilds[$guild_id][0]}</a> и не можете тут работать.<br>";
	echo "Вступить в гильдию можно в <a href=help.php?id=34274 target=_blank>Зале Гильдий</a> в <a href=help.php?id=34265 target=_blank>Городской Управе</a>.<br>";
	return;
}

$small_percent = min( 80, 20 + $guild->rating * 6 );
$large_percent = min( 90, 60 + $guild->rating * 3 );

if( !$mid_php )
{
	die( );
}

$msg = "";
if( $player->regime == 0 && isset( $_GET['smith_do'] ) )
{
	$item_id = (int)$_GET['smith_do'];
	$attr_id = (int)$_GET['attr'];
	$res = f_MQuery( "SELECT i.*, p.number FROM player_items as p INNER JOIN items as i ON p.item_id=i.item_id WHERE p.player_id={$player->player_id} AND i.item_id=$item_id AND ( i.item_id IN( $staffs_in ) OR i.parent_id IN ( $staffs_in ) )" );
	$arr = f_MFetch( $res );
	if( !$arr || !$arr['number'] ) RaiseError( "Попытка улучшить вещь, которой нету в инвентаре", "$item_id" );
	if( $arr['level'] > $guild->rank + 3 )
	{
		$msg = "Ваш ранг не достаточен для улучшения этого посоха.";
	}
	else
	{
    	$aa = ParseItemStr( $arr['effect'] );
    	if( $aa[131] ) { $pid = 131; $stone = 41; }
    	if( $aa[141] ) { $pid = 141; $stone = 103; }
    	if( $aa[151] ) { $pid = 151; $stone = 42; }

    	if( $attr_id != 13 && $attr_id != 15 && $attr_id != 16 && $attr_id != $pid )
    		RaiseError( "Попытка увеличить неверный аттрибут на алтаре кузнецов", "PID: $pid; AID: $attr_id" );

    	f_MQuery( "LOCK TABLE smith_altar WRITE" );
    	$num = f_MValue( "SELECT count( player_id ) FROM smith_altar WHERE player_id={$player->player_id}" );
    	if( $num ) die( "<script>location.href='game.php';</script>" );
    	f_MQuery( "INSERT INTO smith_altar( player_id, item_id, attribute_id ) VALUES ( {$player->player_id}, $item_id, $attr_id )" );
    	f_MQuery( "UNLOCK TABLES" );
        		$snum = 1;
        		if( $attr_id < 100 ) $snum = 2;
    	if( $player->DropItems( $stone, $snum ) )
    	{
    		if( $player->DropItems( $item_id ) )
    		{
    			$player->AddToLogPost( $stone, -$snum, 36, 0, 0 );
    			$player->AddToLogPost( $item_id, -1, 36, 0, 0 );
    			$player->SetRegime( 119 );
    			$player->SetTill( time( ) + 1 );
    		}
    		else
    		{
    			$player->AddItems( $stone, $snum );
    			f_MQuery( "DELETE FROM smith_altar WHERE player_id={$player->player_id}" );
    			echo "<font color=darkred>У вас нет этой вещи</font><br>";
    		}
    	}
    	else
    	{
    		f_MQuery( "DELETE FROM smith_altar WHERE player_id={$player->player_id}" );
    		echo "<font color=darkred>Нет достаточного количества драгоценных камней</font><br>";
    	}
	}
}
else if( $player->regime == 119 && time( ) + 2 >= $player->till )
{
	$item_id = -1;
	f_MQuery( "LOCK TABLE smith_altar WRITE" );
	$arr = f_MFetch( f_MQuery( "SELECT * FROM smith_altar WHERE player_id={$player->player_id}" ) );
	if( $arr )
	{
		$item_id = $arr['item_id'];
		f_MQuery( "DELETE FROM smith_altar WHERE player_id={$player->player_id}" );
	}
	f_MQuery( "UNLOCK TABLES" );
	if( $item_id != -1 )
	{
    	$ires = f_MQuery( "SELECT * FROM items WHERE item_id=$item_id" );
    	$iarr = f_MFetch( $ires );
    	$aa = ParseItemStr( $iarr['effect'] );
    	if( $aa[131] ) { $pid = 131; $stone = 41; }
    	if( $aa[141] ) { $pid = 141; $stone = 103; }
    	if( $aa[151] ) { $pid = 151; $stone = 42; }
   		$item_id = copyItem( $item_id, true );
   		$add = true;
   		$good = false;
    	if( $arr['attribute_id'] == $pid && mt_rand( 0, 99 ) < $large_percent ) { $aa[$pid] ++; $good = true; }
    	else if( $arr['attribute_id'] != $pid && mt_rand( 0, 99 ) < $small_percent ) { $aa[$arr['attribute_id']] ++; $good = true; }
    	else
    	{
    		$snum = 1;
    		if( $arr['attribute_id'] < 100 ) $snum = 2;
			$player->AddItems( $stone, $snum );
			$player->AddToLogPost( $stone, $snum, 36, 0, 2 );
    		$pst = AlterProfExp( $player, 8 );
    		if( $iarr['decay'] <= 1 )
    		{
    			$msg .= "В ходе работы над улучшением вещи она полностью сломалась. $pst";
    			$add = false;
    		}
    		else
    		{
    			f_MQuery( "UPDATE items SET decay = decay - 1 WHERE item_id=$item_id" );
    			$msg .= "В ходе работы над улучшением вещи ее прочность уменьшилась. Вещь улучшить не удалось. $pst";
    		}
    	}
    	if( $good )
    	{
    		$pst = AlterProfExp( $player, 12 );
    		$msg .= "Вещь успешно улучшена $pst";
    	}
    	if( $add )
    	{
    		ksort( $aa );
    		$eff = "";
    		foreach( $aa as $a=>$b ) $eff .= ":$a:$b";
    		$eff = substr( $eff, 1 ) . ".";
    		f_MQuery( "UPDATE items SET effect='$eff' WHERE item_id=$item_id" );
			$player->AddItems( $item_id );
			$player->AddToLogPost( $item_id, 1, 36, 0, 2 );
		}
		// тут отправляем первую половину msg
		// дальше msg будет дополняться и дополнения сразу отправляться
		$player->syst( $msg );
		if( $good ) if( $arr['attribute_id'] > 100 && mt_rand( 1, 100 ) <= 15 || $arr['attribute_id'] < 100 && mt_rand( 1, 100 ) <= 30 )
		{
			$msg .= "<br>Уровень вещи увеличился";
			$player->syst( "Уровень вещи увеличился" );
			f_MQuery( "UPDATE items SET level=level+1 WHERE item_id=$item_id" );
		}
	}
	$player->SetRegime( 0 );
	$player->SetTill( 0 );
}
else if( $player->regime == 119 && isset( $_GET['cancel'] ) )
{
	$item_id = -1;
	f_MQuery( "LOCK TABLE smith_altar WRITE" );
	$arr = f_MFetch( f_MQuery( "SELECT * FROM smith_altar WHERE player_id={$player->player_id}" ) );
	if( $arr )
	{
		$item_id = $arr['item_id'];
		f_MQuery( "DELETE FROM smith_altar WHERE player_id={$player->player_id}" );
	}
	f_MQuery( "UNLOCK TABLES" );
	if( $item_id != -1 )
	{
   		$snum = 1;
   		if( $arr['attribute_id'] < 100 ) $snum = 2;

    	$res = f_MQuery( "SELECT * FROM items WHERE item_id=$item_id" );
    	$arr = f_MFetch( $res );
    	$aa = ParseItemStr( $arr['effect'] );
    	if( $aa[131] ) { $pid = 131; $stone = 41; }
    	if( $aa[141] ) { $pid = 141; $stone = 103; }
    	if( $aa[151] ) { $pid = 151; $stone = 42; }
		$player->AddItems( $item_id );
		$player->AddItems( $stone, $snum );
		$player->AddToLogPost( $item_id, 1, 36, 0, 1 );
		$player->AddToLogPost( $stone, $snum, 36, 0, 1 );
	}
	$player->SetRegime( 0 );
	$player->SetTill( 0 );
}

if( $player->regime == 119 )
{
	$res = f_MQuery( "SELECT * FROM smith_altar WHERE player_id={$player->player_id}" );
	$arr = f_MFetch( $res );
	if( !$arr )
	{
		$player->SetRegime( 0 );
		$player->SetTill( 0 );
		RaiseError( "Игрок улучшает вещь на алтаре кузнецов, но записи об этом нет в БД.", "{$player->regime}" );
	}

	$iarr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id={$arr[item_id]}" ) );
	$aarr = f_MFetch( f_MQuery( "SELECT * FROM attributes WHERE attribute_id={$arr[attribute_id]}" ) );
	
	$rem = $player->till - time( );
	echo "<center><img width=50 height=50 src=images/items/{$iarr[image]}><br><b>{$iarr[name]}</b><br><br><font color={$aarr[color]}><b>{$aarr[name]}</b></font><br>";
	include_js( 'js/timer.js' );
	echo "<script>show_timer_title = true; document.write( InsertTimer( $rem, 'Осталось: <b>', '</b>', 0, 'location.href=\"game.php\";' ) );</script>";
	?><script>function cancel_inmprovement(){if(confirm('Отменить работу?')) location.href='game.php?cancel=1';}</script><?
	echo "<br><a href='javascript:cancel_inmprovement()'>Отменить</a></center>";
}
else
{
	if( $msg == "" ) $msg = "Перед улучшением вещей советуем ознакомиться с <a href=help.php?id=50100 target=_blank>описанием Алтаря Кузнецов</a>.";
	echo "<table width=100%><tr><td><script>FLUl();</script><b>$msg</b><script>FLL();</script></td></tr></table>";

    $res = f_MQuery( "SELECT i.*, p.number FROM player_items as p INNER JOIN items as i ON p.item_id=i.item_id WHERE ( i.item_id IN( $staffs_in ) OR i.parent_id IN ( $staffs_in ) ) AND player_id={$player->player_id} AND p.weared=0" );

    if( f_MNum( $res ) == 0 ) echo "<br><br><center><i>У вас нет ни одного изогнутого посоха.<br>Изогнутые посохи вы можете сделать в кузнице из обычных посохов, которые можно купить на третьем этаже Башни Тайных Знаний.</i></center>";
    else
    {

    	echo "<table width=600><colgroup><col width=120><col width=120><col width=120><col width=120><col width=120>";
    	while( $arr = f_MFetch( $res ) )
    	{
    		echo "<tr>";
    		echo "<td width=120 height=120><script>FUcm();</script>";
    		echo "<img src=images/items/$arr[image] width=50 height=50><br><small><b>";
    		if( $arr['number'] > 1 ) echo "[{$arr[number]}] ";
    		echo $arr['name'];
    		echo "<br>Уровень: $arr[level]<br>Прочность: $arr[decay]/$arr[max_decay]</b></small><script>FL();</script></td>";

    		$aa = ParseItemStr( $arr['effect'] );

    		echo "<td width=120 height=120><script>FUcm();</script>";
    		echo "<small><b>Основная характеристика:<br>";
    		if( $aa[131] )  { $stone = 41;  $attr = 131; echo "<font color=darkblue>Атака Магии Воды</font>"; }
    		if( $aa[141] )  { $stone = 103; $attr = 141; echo "<font color=darkgreen>Атака Магии Природы</font>"; }
    		if( $aa[151] )  { $stone = 42;  $attr = 151; echo "<font color=darkred>Атака Магии Огня</font>"; }
    		$iarr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$stone" ) );
    		echo "</b></small><br>";
    		echo "<big><b>".$aa[$attr]."</b></big>";
    		echo "<br><br><img width=12 height=12 src=images/items/{$iarr[image]}><small><b>x1</b></small>&nbsp;&nbsp;&nbsp;<b>{$large_percent}%</b><br><a href=game.php?smith_do=$arr[item_id]&attr=$attr>Увеличить</a>";
    		echo "<script>FL();</script></td>";

    		$ares = f_MQuery( "SELECT attribute_id, name, color FROM attributes WHERE attribute_id IN( 13, 15, 16 ) ORDER BY attribute_id" );
    		while( $aarr = f_MFetch( $ares ) )
    		{
        		echo "<td width=120 height=120><script>FUcm();</script>";
        		echo "<small><b>Дополнительная характеристика:<br>";
        		echo "<font color=$aarr[color]>$aarr[name]</font>";
        		echo "</b></small><br>";
        		$attr = $aarr[0];
        		echo "<big><b>".((int)$aa[$attr])."</b></big>";
        		echo "<br><br><a title=$iarr[name] target=_blank href=help.php?id=1010&item_id=$stone><img border=0 width=12 height=12 src=images/items/{$iarr[image]}></a><small><b>x2</b></small>&nbsp;&nbsp;&nbsp;<b><font color=darkred>{$small_percent}%</font></b><br><a href=game.php?smith_do=$arr[item_id]&attr=$attr>Увеличить</a>";
        		echo "<script>FL();</script></td>";
    		}

    		echo "</tr>";
    	}
    	echo "</table>";
    }
}

?>
