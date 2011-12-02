<?

include_once( 'items.php' );
include_once( 'guild.php' );

$stats = $player->getAllAttrNames( );
foreach( $guilds as $a=>$b ) if( $b[3] )
	$stats[10000 + $a] = "Ранг в гильдии"; //.$b[0];

$fprofs = Array( );

$player->LoadAttrs( );

foreach( $guilds as $a=>$b ) if( $b[3] )
{
	$guild = new Guild( $a );
	$guild->LoadPlayer( $player->player_id );
	$player->attrs[10000 + $a] = $guild->rank;
	$prof_names[$a] = $b[0];
}

$craft_cost = 0;

function outRepairs( $res, $tm = false )
{
	global $player;
	global $guilds;
	global $craft_cost;

	$ok = false;
	$st = "<table border=0 cellpadding=0 cellspacing=0><tr><td>";

	$st .= GetScrollLightTableStart("left");
	$st .= "<table>";

	while( $arr = f_MFetch( $res ) )
	{
		$st .= "<tr><td align=center width=100 valign=top rowspan=5><img src=images/items/".itemImage( $arr )."><br>$arr[name]</td>";
		$st .= "<td width=150 valign=top><b>Текущая прочность: </b></td><td align=right>{$arr[decay]}/{$arr[max_decay]}</td></tr>";
		$new_decay = $arr[max_decay] - 1;
		$craft_cost = ceil( $arr[price] / 20 );
		$craft_time = outCraftTime( );
		$st .= "<tr><td valign=top><b>Получим прочность: </b></td><td align=right>{$new_decay}/{$new_decay}</td></tr>";
		$st .= "<tr><td><b>Время: </b></td><td align=right>$craft_time</td></tr>";
		$st .= "<tr><td><b>Стоимость: </b></td><td align=right>$craft_cost</td></tr>";
		if( $tm === false ) $st .= "<tr><td colspan=2><a href=game.php?repair=$arr[item_id]>Ремонтировать</a></td></tr>";
		else
		{
			$st .= ( "<script src=js/timer.js></script>" );
			$st .= ( "<tr><td colspan=2>&nbsp;</td></tr><tr><td colspan=3 align=center><script>document.write( InsertTimer( $tm, 'До окончания осталось: <b>', '</b>', 0, 'location.href=\"game.php\"' ) );show_timer_title = true;</script></td></tr>" );
			$st .= "<tr><td colspan=3 align=center><a href='javascript:if(confirm(\"Отменить работу? Затраченные монетки не будут возвращены.\")) location.href=\"game.php?cancel=1\";'>Отменить</a></td></tr>";
		}
	}             

	$st .= "</table>";
	$st .= GetScrollLightTableEnd();
	$st .= "</td></tr></table>";
	
	return $st;

}

function outRecipes( $res, $sb = 0, $btn = false )
{
	global $player;
	global $guilds;

	if( $btn ) // check for premium
	{
		$premium = false;
    	$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=2" ) );
    	if( $barr[0] ) $premium = true;
	}

	$ok = false;
	$st = "<table border=0 cellpadding=0 cellspacing=0><tr><td>";
	while( $arr = f_MFetch( $res ) )
	{
		if( !$sb ) $canDo = true;
		else $canDo = $player->CheckItemReq( $arr['req'] );
		if( $sb < 2 && !$btn ) $canDo2 = true;
		else $canDo2 = $player->CheckItems( $arr['ingridients'] );

		if( ( $canDo ) && ( $canDo2 || $sb < 2 ) )
		{
			$st .= GetScrollLightTableStart("left");

			$resstr = craftGetItemsList( ParseItemStr( $arr[result] ) );
			$ok = true;
			$ss = ( $btn && $canDo2 ) ? ( $premium ? " &nbsp; <a href='javascript:que($arr[recipe_id],\"$arr[name]\",".getCraftTime( ).",7200)'>В очередь >>></a>" : " &nbsp; <a href='javascript:que($arr[recipe_id],\"$arr[name]\",".getCraftTime( ).",900)'>В очередь >>></a>" ) : "";
			$st .= "<table border=0><tr><td colspan=3>";

			$st .= GetScrollTableStart("center");

			$st .= "<table width=100% cellspacing=0 cellpadding=0 border=0><tr><td><b>$arr[name]</b></td><td align=right> Время: ".outCraftTime( )."$ss</td></tr>";
			if( !$btn ) $st .= "<tr><td>Гильдия: <b>{$guilds[$arr[prof]][0]}</b></td><td align=right>Ранг: <b>$arr[rank]</b></td></tr>";
			$st .= '</table>';

			$st .= GetScrollTableEnd();

			$st .= "</td></tr><tr><td vAlign=top height=100%>";

			$st .= GetScrollTableStart("center");

			$st .= "<center><u>Ингредиенты:</u></center>";
			$st .= craftGetItemsList( ParseItemStr( $arr[ingridients], 165 ), 200, $btn );

			$st .= GetScrollTableEnd();

			$st .= "</td><td vAlign=top height=100%>";

			$st .= GetScrollTableStart("center");

			$st .= "<center><u>Результат</u></center>";
			$st .= $resstr;

			$st .= GetScrollTableEnd();

			$st .= "</td><td vAlign=top width=165 height=100%>";

			$st .= GetScrollTableStart("center");

			$st .= "<center><u>Требования</u></center>";
			$st .= ItemReqStr( $arr[req] );
			if( $arr['level'] ) $st .="<b>Уровень:</b> $arr[level]";

			$st .= GetScrollTableEnd();

			$st .= "</td></tr></table>";

			$st .= GetScrollLightTableEnd();

			$st .= "<br>";
		}
	}
	$st .= "</td></tr></table>";
	
	if( !$ok ) $st = "<i>Нет доступных рецептов</i><br>";
	
	return $st;
}

function outRecipes2( $res, $sb = 0, $btn = false )
{
	global $player;
	global $guilds;

	$ok = false;
	$st = "<table border=0 cellpadding=0 cellspacing=0><tr><td>";
	while( $arr = f_MFetch( $res ) )
	{
		if( !$sb ) $canDo = true;
		else $canDo = $player->CheckItemReq( $arr[req] );

		if( $canDo || !$sb )
		{
			$resstr = craftGetItemsList( ParseItemStr( $arr[result], 165 ) );
			$ok = true;
			$ss = ( $btn && $canDo ) ? " &nbsp; <a href=game.php?recipe=$arr[recipe_id]>Делать >>></a>" : "";
			$st .= "<table border=0><tr><td colspan=3>";

			$st .= "'+rFUct()+'";

			$st .= "<table width=100% cellspacing=0 cellpadding=0 border=0><tr><td><b>$arr[name]</b></td><td align=right> Время: ".outCraftTime( )."$ss</td></tr>";
			if( !$btn ) $st .= "<tr><td>Гильдия: <b>{$guilds[$arr[prof]][0]}</b></td><td align=right>Ранг: <b>$arr[rank]</b></td></tr>";
			$st .= '</table>';

			$st .= "'+rFL()+'";

			$st .= "</td></tr><tr><td vAlign=top height=100%>";

			$st .= "'+rFUlt()+'";

			$st .= "<center><u>Ингредиенты:</u></center>";
			$st .= craftGetItemsList( ParseItemStr( $arr[ingridients], 165 ) );

			$st .= "'+rFL()+'";

			$st .= "</td><td vAlign=top height=100%>";

			$st .= "'+rFUlt()+'";

			$st .= "<center><u>Результат</u></center>";
			$st .= $resstr;

			$st .= "'+rFL()+'";

			$st .= "</td><td vAlign=top width=165 height=100%>";

			$st .= "'+rFUct()+'";

			$st .= "<center><u>Требования</u></center>";
			$st .= ItemReqStr( $arr[req] );
			if( $arr['level'] ) $st .="<b>Уровень:</b> $arr[level]";

			$st .= "'+rFL()+'";

			$st .= "</td></tr></table>";
		}
	}
	$st .= "</td></tr></table>";
	
	if( !$ok ) $st = "<i>Нет доступных рецептов</i><br>";
	
	return $st;
}

function craftDropItems( $player, $arr )
{
	f_MQuery( "LOCK TABLE player_items WRITE, items WRITE, characters WRITE" );
	foreach( $arr as $a=>$b )
	{
		if( $a ) 
		{
    		if( $player->NumberItems( $a ) < $b )
    		{
    			f_MQuery( "UNLOCK TABLES" );
    			return false;
    		}
		}
		else if( $player->money < $b )
		{
			f_MQuery( "UNLOCK TABLES" );
			return false;
		}
	}
	foreach( $arr as $a=>$b )
	{
		if( $a ) $player->DropItems( $a, $b );
		else $player->SpendMoney( $b );
	}
	f_MQuery( "UNLOCK TABLES" );
	foreach($arr as $a => $b)
	{
		if ($a) $player->AddToLogPost($a, -$b, 40);
		else $player->AddToLogPost(0, -$b, 40);
	}
	return true;
}

function craftAddItems( $player, $arr )
{
	foreach( $arr as $a=>$b )
	{
		if( $a ) {$player->AddItems( $a, $b ); $player->AddToLogPost($a, $b, 40);}
		else {$player->AddMoney( $b ); $player->AddToLogPost(0, $b, 40);}
		
	}
}

function craftGetItemsList( $arr, $width = "200", $show_plr = false )
{
	global $craft_cost;
	global $player;

	$craft_cost = 0;
	$money = 0;
	$qok = false;
	$query = "SELECT item_id, image, name, price FROM items WHERE ";
	foreach( $arr as $a=>$b )
	{
		if( $a == 0 )
		{
			$money += $b;
			continue;
		}
		$mp[$a] = $b;
		if( $qok ) $query .= " OR ";
		$qok = true;
		$query .= "item_id = $a";
	}
	$st = "<table width=$width cellspacing=0 cellpadding=0 border=0>";
	$ok = false;
	if( $qok )
	{
		$res = f_MQuery( $query );
		while( $arr = f_MFetch( $res ) )
		{
			$ok = true;
			$num = $mp[$arr[item_id]];
			if( $show_plr )
			{
				$vnum = $player->NumberItems( $arr['item_id'] );
				if( $vnum >= $num ) $num = "<small>".$vnum.'/</small><b>'.$num.'</b>';
				else $num = "<font color=red><small>".$vnum.'/</small></font><b>'.$num.'</b>';
			}
			$st .= "<tr><td><img src=/images/items/$arr[image] width=11 height=11>&nbsp;<a href=help.php?id=1010&item_id=$arr[item_id] target=_blank><b>$arr[name]</b></a></td><td align=right>$num</td></tr>";
			$craft_cost += $num * $arr[price];
		}
	}
	if( $money )
	{
		$ok = true;
		if( $show_plr )
		{
			if( $player->money >= $money ) $money = "<small>".$player->money.'/</small><b>'.$money.'</b>';
			else $money = "<font color=red><small>".$player->money.'/</small></font><b>'.$money.'</b>';
		}
		$st .= "<tr><td><img src=/images/money.gif width=11 height=11>&nbsp;<b>Дублонов</b></td><td align=right>$money</td></tr>";
		$craft_cost += $money;
	}
	if( !$ok ) $st = "<i>Пусто</i>";
	else $st .= "</table>";
	
	return $st;
}

function getCraftTime( )
{
	global $craft_cost;
	
	$craft_time = log( $craft_cost / 10 + 1 ) / log( 2 ) + 1e-7;
	$craft_time *= 20;
	
	settype( $craft_time, 'integer' );
	$craft_time *= 5;

	return $craft_time;
}

function outCraftTime( )
{
	$craft_time = getCraftTime( );

	$m = $craft_time / 60;
	$s = $craft_time % 60;
	settype( $m, 'integer' );
	settype( $s, 'integer' );
	if( $s < 10 ) $s = "0".$s;
	return "$m:$s";
}

?>
