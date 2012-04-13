<?

if( !$mid_php ) die( );

include_once( 'craft_functions.php' );
include_once( 'prof_exp.php' );

$place = $player->depth;
$loc = $player->location;

if( $player->regime == 103 )
{
	$res = f_MQuery( "SELECT * FROM player_craft WHERE player_id = $player->player_id" );
	$arr = f_MFetch( $res );
	if( !$arr )
	{
		$player->SetRegime( 0 );
		die( "<script>location.href='game.php';</script>" );
	}
	
	$recipe_id = $arr[recipe_id];
	$deadline = $arr[deadline];
	$time = $deadline - time( );
	
	if( isset( $HTTP_GET_VARS['cancel_craft'] ) )
	{
		$res = f_MQuery( "SELECT * FROM recipes WHERE recipe_id = $recipe_id" );
		$arr = f_MFetch( $res );
		craftAddItems( $player, ParseItemStr( $arr[ingridients] ) );
		$player->syst( 'Вы досрочно завершаете работу' );
		f_MQuery( "DELETE FROM player_craft WHERE player_id = $player->player_id" );
		f_MQuery( "DELETE FROM player_craft_queue WHERE player_id = $player->player_id" );
		$player->SetRegime( 0 );
		die( "<script>location.href='game.php';</script>" );
	}
	
	else if( $time <= 2 )
	{
		// Government work
		f_MQuery( "UPDATE player_government_work SET completed=completed+1 WHERE player_id={$player->player_id} AND recipe_id=$recipe_id AND completed < number" );
		
		// Widow quest
	   	include_once( "quest_race.php" );
	   	updateQuestStatus ( $player->player_id, 2503 );

		
		$res = f_MQuery( "SELECT * FROM recipes WHERE recipe_id = $recipe_id" );
		$arr = f_MFetch( $res );
		craftAddItems( $player, ParseItemStr( $arr[result] ) );
		f_MQuery( "DELETE FROM player_craft WHERE player_id = $player->player_id" );
		$player->SetRegime( 0 );

		craftGetItemsList( ParseItemStr( $arr[result] ) );
		$spent = getCraftTime( );

		$pst = AlterProfExp( $player, ceil( 50 * $spent / 3600 ) );
		UpdateTitle( );
		$player->syst( "Вы завершаете работу над рецептом \"$arr[name]\" $pst" );

		die( "<script>location.href='game.php';</script>" );
	}

	$rres = f_MQuery( "SELECT * FROM recipes WHERE recipe_id = $recipe_id" );
	$st = outRecipes( $rres );
	
	print( "<center>" );
	$lres = f_MQuery( "SELECT title FROM loc_texts WHERE loc=$loc AND depth=$place" );
	$larr = f_MFetch( $lres );
	print( "<b>{$loc_names[$loc]}, {$larr[0]}</b><hr width=30% color=gray size=1>" );
	print( "Вы работаете над рецептом:<br>$st<br><br>" );

	print( "Вы можете досрочно <a href=game.php?cancel_craft><u>отменить работу</u></a><br><br>" );
	print( "<script src=js/timer.js></script>" );
	print( "<script>document.write( InsertTimer( $time, 'До окончания осталось: <b>', '</b>', 0, 'location.href=\"game.php\"' ) );show_timer_title = true;</script>" );

	$res = f_MQuery( "SELECT r.recipe_id, r.name FROM recipes as r, player_craft_queue as c WHERE r.recipe_id=c.recipe_id AND player_id={$player->player_id} ORDER BY c.entry_id" );
	if( f_MNum( $res ) )
	{
		echo "<br><b>Очередь:</b><br>";
		while( $arr = f_MFetch( $res ) )
		{
			echo "<a target=_blank href=help.php?id=1015&recipe_id=$arr[recipe_id]>$arr[name]</a><br>";
		}
	}

	print( "</center>" );
}
else
{
	$craft_type = - $status;
	$guild_id = 100 + $craft_type;

	$guild = new Guild( $guild_id );
    if( !$guild->LoadPlayer( $player->player_id ) )
    {
    	if( !isset( $mid_php ) ) die( );
    	
    	echo "<br>Вы не состоите в <a href=help.php?id={$guilds[$guild_id][1]} target=_blank>Гильдии {$guilds[$guild_id][0]}</a> и не можете тут работать.<br>";
    	echo "Вступить в гильдию можно в <a href=help.php?id=34274 target=_blank>Зале Гильдий</a> в <a href=help.php?id=34265 target=_blank>Городской Управе</a>.<br>";
    	return;
    }

    if( isset( $_GET['recipe'] ) ) RaiseError( 'Возможно использование программы-кликалки' );
    if( isset( $_GET['r0'] ) ) // запуск очереди
    {
    	$la1 = $_GET['num'];
    	$la2 = f_MFetch( f_MQuery( "SELECT number FROM player_num WHERE player_id={$player->player_id}" ) );
    	$la2 = $la2[0];

    	if( $la1 != $la2 ) $player->syst( 'Введите правильный код в окне!' );
    	else
    	{
        	$code=rand(1000,9999);

        	f_MQuery( "LOCK TABlE player_num WRITE" );
        	f_MQuery( "DELETE FROM player_num WHERE player_id = $player->player_id" );
        	f_MQuery( "INSERT INTO player_num VALUES ( $player->player_id, $code )" );
        	f_MQuery( "UNLOCK TABLES" );

        	$sum = 0;
        	$i = 0;
        	$log = '';
        	while( isset( $_GET["r$i"] ) )
        	{
        		$recipe_id = (int)$_GET["r$i"];
         		$tarr = f_MFetch( f_MQuery( "SELECT * FROM recipes WHERE recipe_id=$recipe_id" ) );
         		if( !$tarr ) RaiseError( "В очередь добавлен несуществующий рецепт", "$recipe_id" );
         		craftGetItemsList( ParseItemStr( $tarr[result] ) );
         		$sum += getCraftTime( );
         		$log .= ", $sum";
         		++ $i;
        	}

        	$mx = 900;
    		$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=2" ) );
    		if( $barr[0] ) $mx = 120 * 60; // прем включем всем

        	if( $sum > $mx && $i > 1 ) RaiseError( "Попытка запустить более чем $mx-секундную очередь", "[prem:{$barr[0]}] $log" );
        	f_MQuery( "LOCK TABLE player_craft_queue WRITE" );
        	f_MQuery( "DELETE FROM player_craft_queue WHERE player_id={$player->player_id}" );
        	for( $i = 1; isset( $_GET["r$i"] ); ++ $i )
        	{
        		$recipe_id = (int)$_GET["r$i"];
        		f_MQuery( "INSERT INTO player_craft_queue ( player_id, recipe_id ) VALUES ( {$player->player_id}, $recipe_id )" );
        	}
        	f_MQuery( "UNLOCK TABLES" );
        	$_GET['recipe'] = $_GET['r0'];
        }
    }
    else
    {
    	f_MQuery( "LOCK TABLE player_craft_queue WRITE" );
    	$tres = f_MQuery( "SELECT recipe_id, entry_id FROM player_craft_queue WHERE player_id={$player->player_id} ORDER BY entry_id LIMIT 1" );
    	$tarr = f_MFetch( $tres );
    	if( $tarr )
    	{
    		$_GET['recipe'] = $tarr[0];
    		f_MQuery( "DELETE FROM player_craft_queue WHERE entry_id=$tarr[1]" );
    	}
    	f_MQuery( "UNLOCK TABLES" );
    }


	if( isset( $_GET['recipe'] ) )
	{
		$recipe_id = $_GET['recipe'];
		settype( $recipe_id, "integer" );
		$rres = f_MQuery( "SELECT * FROM recipes WHERE recipe_id = $recipe_id" );
		$rarr = f_MFetch( $rres );
		$tm = date( "H:i", time( ) );
		if( !$rarr ) print( "<script>window.top.chat.syst( '$tm', 'Нет такого рецепта' );</script>" );
		else if( !$player->CheckItemReq( $rarr[req] ) ) print( "<script>window.top.chat.syst( '$tm', 'Вы не можете выполнить этот рецепт' );</script>" );
		else if( !craftDropItems( $player, ParseItemStr( $rarr[ingridients] ) ) ) print( "<script>window.top.chat.syst( '$tm', 'У вас нет всех необходимых ингридиентов' );</script>" );
		else
		{
			craftGetItemsList( ParseItemStr( $rarr[result] ) );
			$deadline = time( ) + getCraftTime( );
			f_MQuery( "INSERT INTO player_craft VALUES ( $player->player_id, $recipe_id, $deadline )" );
			$player->SetRegime( 103 );
			die( "<script>location.href='game.php';</script>" );
		}
	}

	if( isset( $_GET['shop_recipes'] ) )
	{
        $sres = f_MQuery( "SELECT * FROM shops WHERE location = $loc AND place = $depth" );
		$guild_id = 100 + $craft_type;
        if( mysql_num_rows( $sres ) )
        {
        	$sarr = f_MFetch( $sres );
        	print( "<b>$sarr[name]</b>" );
        	if( $player->IsShopOwner( $sarr[shop_id] ) ) print( "&nbsp;-&nbsp;<a target=_blank href=shop_controls.php?shop_id=$sarr[shop_id]>Управление</a>" );
			print( "&nbsp;-&nbsp;<a href=game.php>Вернуться к работе</a>" );
        	print( "<br>" );

        	include( "shop.php" );
        	$stats = $player->getAllAttrNames( );
        	$shop = new Shop( $sarr[shop_id] );
        	$shop->ShowGoods( );			
        	
        	print( "<iframe width=0 height=0 id=shop_ref name=shop_ref></iframe>" );
        }
	}
	else if( $_GET['buy_recipes'] )
	{
		$guild_id = 100 + $craft_type;
		include( 'craft_shop.php' );
	}
	else if( ( isset( $_GET['repair_panel'] ) || isset( $_GET['repair'] ) || $player->regime == 106 ) && ( $guild_id == 105 || $guild_id == 104 || $guild_id == 109 ) )
	{
		if( $guild_id == 104 ) echo "<b>Починка оружия, брони, щитов и шлемов</b>";
		if( $guild_id == 105 ) echo "<b>Починка колец, браслетов и амулетов</b>";
		if( $guild_id == 109 ) echo "<b>Починка перчаток, обуви и накидок</b>";

		if( $guild_id == 104 ) $rep_types = "2,10,8";
		if( $guild_id == 105 ) $rep_types = "4,6,9";
		if( $guild_id == 109 ) $rep_types = "11,12,13";

		if( $guild_id == 104 ) $rep_is = 3;
		if( $guild_id == 105 ) $rep_is = 2;
		if( $guild_id == 109 ) $rep_is = 1;

		$rank = $guild->rank;
		$max_level = $rank + 4;
		$cond = "items.repair=$rep_is AND decay < max_decay - 1 AND max_decay >= 2 AND player_id={$player->player_id} AND items.item_id = player_items.item_id AND weared = 0 AND level <= $max_level";

		$ok = true;
		if( $player->regime == 106 )
		{
			if( isset( $_GET['cancel'] ) )
			{
				$player->SetRegime( 0 );
				$player->SetTill( 0 );
				f_MQuery( "DELETE FROM player_craft WHERE player_id={$player->player_id}" );
				$ok = true;
			}
			else $ok = false;
		}
		else if( isset( $_GET['repair'] ) )
		{
			$item_id = $_GET['repair'];
			settype( $item_id, 'integer' );

			$res = f_MQuery( "SELECT price FROM player_items, items WHERE items.item_id=$item_id AND $cond" );

			if( f_MNum( $res ) )
			{
				$arr = f_MFetch( $res );
				$craft_cost = ceil( $arr[0] / 20 );
				if( $player->SpendMoney( $craft_cost ) )
				{
					$ok = false;
					$deadline = time( ) + getCraftTime( );
					if ($player->Rank() == 1) $deadline = time( ) + 10;
					$player->SetRegime( 106 );
					f_MQuery( "INSERT INTO player_craft ( player_id, recipe_id, deadline ) VALUES ( {$player->player_id}, $item_id, $deadline )" );
					$player->AddToLogPost( 0, -$craft_cost, 42 );
	    		} else $player->syst( "У вас недостаточно денег" );
			}
		}
		
		if( !$ok )
		{
			$res1 = f_MQuery( "SELECT * FROM player_craft WHERE player_id=$player->player_id" );
			$arr1 = f_MFetch( $res1 );
			if( !$arr1 ) RaiseError( "Игрок чинит вещь, но информации об этом нет" );
			$res = f_MQuery( "SELECT * FROM items WHERE item_id={$arr1[recipe_id]}" );
			$tm = $arr1['deadline'] - time( );
			if( $tm > 2 )
			{
				echo "<br><br>";
				echo outRepairs( $res, $tm );
			}
			else
			{
				$arr = f_MFetch( $res );
				$decay = $arr['max_decay'] - 1;
				$cres = f_MQuery( "SELECT item_id FROM items WHERE parent_id=$arr[parent_id] AND decay=$decay AND max_decay=$decay AND improved=0 AND clan_marked=0" );
				$carr = f_MFetch( $cres );
				if( $arr['improved'] || $arr['clan_marked'] )
				{
					$new_id = $arr['item_id'];
   					f_MQuery( "UPDATE items SET decay=$decay, max_decay=$decay WHERE item_id=$new_id" );
				}
				else
				{
    				if( $carr ) $new_id = $carr[0];
    				else
    				{
    					$new_id = copyItem( $arr['parent_id'] );
    					f_MQuery( "UPDATE items SET decay=$decay, max_decay=$decay WHERE item_id=$new_id" );
    				}
				}
				if( $player->DropItems( $arr['item_id'] ) )
					$player->AddItems( $new_id );
				f_MQuery( "DELETE FROM player_craft WHERE player_id={$player->player_id}" );
				$player->SetRegime( 0 );

				$craft_cost = ceil( $arr['price'] / 20 );
        		$spent = getCraftTime( );

				$pst = AlterProfExp( $player, ceil( 50 * $spent / 3600 ) );
				UpdateTitle( );
				$player->syst( "Вы успешно починили <b>{$arr[name]}</b> $pst. Новая прочность: <b>$decay/$decay</b>" );

				$ok = true;
			}
		}

		if( $ok )
		{
			echo " - <a href=game.php>Вернуться к работе</a><br>";

			echo "<br>Ваш ранг в гильдии {$guilds[$guild_id][0]}: <b>$rank</b><br>Вы можете чинить вещи, уровень которых не превышает: <b>$max_level</b>.<br><br>";

			$res = f_MQuery( "SELECT items.* FROM player_items, items WHERE $cond" );
			if( f_MNum( $res ) == 0 ) echo "<i>У вас нечего чинить</i>";
			else
			{
				echo outRepairs( $res );
			}
		}
	}
	else
	{
    	$craft_type = - $status;
		$guild_id = 100 + $craft_type;

		if( isset( $_GET['lvl'] ) )$lvl = $_GET['lvl'];
		else $lvl = -1;
		settype( $lvl, 'integer' );
		if( $lvl == -1 ) $qs = "";
		else $qs = " AND level = $lvl ";

    	print( "<b>$prof_locs[$craft_type]</b> - <a href=game.php?buy_recipes=1>Купить рецепты</a>" );
    	if( $guild_id == 105 || $guild_id == 104 || $guild_id == 109 )
    	{
	    	echo " - <a href=game.php?repair_panel=1>Ремонтировать вещи</a>";
    	}
    	echo "<br><div style='display:none;' id=show_que_div><br><a href='javascript:qrefr( )'>Показать текущую очередь</a></div>";
    	echo "<br>";
    	$res = f_MQuery( "SELECT recipes.* FROM recipes, player_recipes WHERE recipes.prof = 100 + $craft_type AND recipes.recipe_id = player_recipes.recipe_id AND player_recipes.player_id = {$player->player_id} $qs" );

    	$ok = ( mysql_num_rows( $res ) != 0 );

    	if( $_GET['all_recipes'] == 2 )
	    	$st = outRecipes( $res, 0, 1 );
	    else if( $_GET['all_recipes'] == 1 )
	    	$st = outRecipes( $res, 1, 1 );
	    else
			$st = outRecipes( $res, 2, 1 );

		echo "<div id=recipes>";
    	print( $st );

    	if( $ok ) 
    	{
    	    $q = $_GET['all_recipes'];
    	    settype( $q, 'integer' );
    		
    		if( $_GET['all_recipes'] == 1 ) echo "<br>Показаны только доступные рецепты. <br><li> <a href=game.php?all_recipes=2&lvl=$lvl>Показать все рецепты</a> <li> <a href=game.php?lvl=$lvl>Показать только доступные рецепты, для которых есть достаточное количество ресурсов</a>";
    		else if( $_GET['all_recipes'] == 2 ) echo "<br>Показаны все рецепты. <br><li> <a href=game.php?all_recipes=1&lvl=$lvl>Показать только доступные рецепты</a> <li> <a href=game.php?lvl=$lvl>Показать только доступные рецепты, для которых есть достаточное количество ресурсов</a>";
    		else echo "<br>Показаны только доступные рецепты, на которые хватает ресурсов в инвентаре. <br><li> <a href=game.php?all_recipes=2&lvl=$lvl>Показать все рецепты</a> <li> <a href=game.php?all_recipes=1&lvl=$lvl>Показать только доступные рецепты, включая те, для которых нет достаточного количества ресурсов</a>";
    		echo "<br><br>";
    		if( $lvl == -1 ) echo "Показаны рецепты всех уровней. Показать рецепты только для уровня:<br>";
    		else echo "Показаны только рецепты $lvl уровня. Показать рецепты только для уровня:<br>";
    		echo "<a href=game.php?all_recipes=$q&lvl=-1>Всех</a>";
    		for( $i = 1; $i <= 25; ++ $i )  echo "&nbsp;&nbsp;<a href=game.php?all_recipes=$q&lvl=$i>$i</a>";
    	}

    	$code=rand(1000,9999);

    	f_MQuery( "LOCK TABlE player_num WRITE" );
    	f_MQuery( "DELETE FROM player_num WHERE player_id = $player->player_id" );
    	f_MQuery( "INSERT INTO player_num VALUES ( $player->player_id, $code )" );
    	f_MQuery( "UNLOCK TABLES" );

    	echo "</div>";
    	echo "<div id=dque style='display:none'><div id=dquet>&nbsp;</div><table cellspacing=0 cellpadding=0 border=0><tr><td><img id=num_img width=90 height=40 src=captcha/code.php></td><td></td><td><input onkeydown='e = event || window.event;if( e.keyCode == 13 ) { sque(); }' class=btn40 maxlength=4 id=dnum></td></tr></table><li><a href='javascript:sque()'>Запустить очередь на производство</a>";
    	echo "<br>(Если вы не можете разобрать цифр, нажмите <a href=# onclick='reload();'>сюда</a>, чтобы обновить картинку).<br>";
		echo "<script src='js/numkeyboard2.js'></script><script>showkeyboard('dnum');</script>";

    	echo "</div>";

    	$mx = 15;
		$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$player->player_id} AND premium_id=2" ) );
		if( $barr[0] ) $mx = 120; // прем включен всем

    	?>

    	<script>
    	function reload () {

        	var rndval = new Date().getTime(); 

        	document.getElementById('num_img').src = 'captcha/code.php?rnd=' + rndval + '';
        };

    	var sumt = 0;
    	var qr = new Array( );
    	var qn = 0;
    	function que( id, nm, tm, moo )
    	{
    	    if( qn > 0 && sumt + tm > moo )
    	    {
    	    	alert( 'Суммарное время превысит ' + moo + ' секунд, добавить рецепт нельзя' );
    	    	return;
    	    }
    	    sumt += tm;
    		
    		var obj = new Object( );
    		obj.id = id;
    		obj.name = nm;
    		obj.time = tm;
    		obj.moo = moo;
    		qr[qn ++] = obj;
    		qrefr( );
    	}
    	function unque( id )
    	{
    		-- qn;
    		sumt -= qr[id].time;
    		for( var i = id; i < qn; ++ i ) qr[i] = qr[i + 1];
    		qrefr( );
    	}
    	function qrefr( )
    	{
    		var st = '<b>Текущая очередь</b> - <a href="javascript:cque()">Вернуться к списку, чтобы добавить еще рецепты</a><br><br><table><tr><td>' + rFUlt();
    		for( var i = 0; i < qn; ++ i )
    		{
    			st += '<img style="cursor:pointer" onclick="unque(' + i + ');" width=11 height=11 src=images/e_close.gif> <img title="Добавить еще одну" alt="Добавить еще одну" style="cursor:pointer" onclick="que(' + qr[i].id + ',\'' + qr[i].name + '\',' + qr[i].time + ',' + qr[i].moo + ');" width=11 height=11 src=images/e_plus.gif> [' + parseInt( qr[i].time / 60 ) + ':' + ((qr[i].time % 60 < 10)?'0':'') + '' + qr[i].time % 60 + '] <a href=help.php?id=1015&recipe_id=' + qr[i].id + ' target=_blank>' + qr[i].name + '</a><br>';
    		}
    		if( qn > 0 ) st += '<br><b>Итого: </b> [' + parseInt( sumt / 60 ) + ':' + ((sumt % 60 < 10)?'0':'') + '' + sumt % 60 + ']<br><small>Максимальная длина очереди - <b><?=$mx?></b> минут<br>Очередь будет выполняться сама только в случае если игра открыта</small>' + rFL() + '</td></tr></table>';
    		else
    		{
    			cque( );
    			return;
    		}
    		_( 'show_que_div' ).style.display = 'none';
    		_( 'dquet' ).innerHTML = st;
    		_( 'dque' ).style.display = '';
    		_( 'recipes' ).style.display = 'none';
    		_( 'dnum' ).focus( );
    		_( 'dnum_moo' ).style.display = 'none';
    	}
    	function cque( )
    	{
    		if( qn > 0 ) _( 'show_que_div' ).style.display = '';
	   		_( 'dque' ).style.display = 'none';
    		_( 'recipes' ).style.display = '';
    	}
    	function sque( )
    	{
    		var st = 'game.php';
			for( var i = 0; i < qn; ++ i )
			{
				st += (i?'&':'?');
				st += 'r' + i + '=' + qr[i].id;
			}
			st += '&num=' + _( 'dnum' ).value;
			location.href=st;
    	}
    	</script>

    	<?
   	}
}

?>
