<?

function isStoneDepth( $a )
{
	return $a == 0 || $a == 5 || $a == 10;
}

function getStoneId( $depth )
{
	if( $depth == 0 )  return 479;
	if( $depth == 5 )
	{
		$id = mt_rand( 1, 3 );
		if( $id == 1 ) return 41;
		if( $id == 2 ) return 42;
		if( $id == 3 ) return 103;
	}
	if( $depth == 10 )
	{
		$id = mt_rand( 1, 3 );
		if( $id == 1 ) return 43;
		if( $id == 2 ) return 104;
		if( $id == 3 ) return 105;
	}
}

function getCaveImage( $depth )
{
	if( $depth == 0 ) return "images/locations/cave_0.jpg";
	if( $depth == 1 ) return "images/locations/cave_1.jpg";
	return "images/locations/capital_caves.jpg";
}

function getPossibleDirections( )
{
	global $player;
	global $till, $depth;
	global $attack;
	global $cant_move_wo_;
	global $noob;

	if( $player->regime || $till || $attack )
	{
		$ret = "";
		$ret .= "<li><font color=gray>Вернуться назад</font><br>";
		if( $player->depth == 1 && $player->level >= 3 )
			$ret .= "<li><font color=gray>В лабиринт кошмаров</font><br>";
		if( $cant_move_wo_ ) $ret .= "<li><i><font color=gray>Нельзя двигаться глубже без факела в руке</font></i><br>";
		else $ret .= "<li><span id=go_further><font color=gray>Двигаться глубже</font></span><br>";
		return $ret;
	}
	else if( $noob )
	{
    	ob_start( );
        		?>
        		<li><a href="#" onclick="alert('Не спеши возвращаться! Следуй инструкциям Астаниэль.');">Вернуться назад</a><br>
        		<li><span id=go_further style="position:relative;top:0px;left:0px;"><a href="#" onclick="if(ready_to_go_further)cave('dir=1'); else alert('Не спеши спускаться! Следуй инструкциям Астаниэль.');">Двигаться глубже</a></span><br>
        		<?
    	$ret = ob_get_contents();
    	ob_end_clean( );
    	return $ret;
	}

	ob_start( );
    		?>
    		
    		<li><a href="javascript:cave('dir=-1')">Вернуться назад</a><br>
    		<? if( $player->depth == 1 && $player->level >= 3 ) { ?> <li><a href="javascript:cave('dir=2')">В лабиринт кошмаров</a><br> <? } ?> 
    		<? if( $cant_move_wo_ )
    		{
    		?>
    		<li><i>Нельзя двигаться глубже без факела в руке</i><br>
    		<?
    		}
    		else
    		{
    		?>
    		<li><a href="javascript:cave('dir=1')">Двигаться глубже</a><br>
    		<?
    		}
    		?>
    		
    		<?
	$ret = ob_get_contents();
	ob_end_clean( );
	return $ret;
}

$exec = "";
function getMidContent( )
{
	global $player;
	global $till, $depth, $regime;
	global $attack;
	global $cant_move_wo_;
	global $exec;
	global $noob;

	ob_start( );

	if( $player->regime == 107 )
	{
		$code=rand(1000,9999);

        f_MQuery( "LOCK TABLE player_num WRITE" );
        f_MQuery( "DELETE FROM player_num WHERE player_id = {$player->player_id}" );
        f_MQuery( "INSERT INTO player_num VALUES ( {$player->player_id}, $code )" );
        f_MQuery( "UNLOCK TABLES" );

		echo "<br><b>Чтобы продолжить исследование пещер, введите следующие цифры:</b><br>";
        echo "<table cellspacing=0 cellpadding=0 border=0><tr><td><div id=num_img><img src=captcha/code.php?rnd=".mt_rand()." width=90 height=40 border=1 bordercolor=black></div></td><td>&nbsp;";
        $oncl = 'cave("num=" + document.getElementById( "num" ).value);document.getElementById( "num" ).value="";';
        echo "<input onkeydown='e = event || window.event;if( e.keyCode == 13 ) { $oncl }' type=text class=te_btn size=4 maxlength=4 name=num id=num></td><td>&nbsp;<button onClick='$oncl' class=ss_btn>Дальше</button></td></tr></table>";
        echo "(Если вы не можете разобрать цифры, нажмите <a href=# onclick='reload();'>сюда</a>, чтобы обновить картинку).<br>";
	}
	else if( $player->regime == 100 )
	{
	}
	else if( !$till )
	{
	    if( $noob ) echo "<ul><li><a href=# onclick=\"alert('Тут, несомненно, много интересного, но пока лучше делать то, что предлагает Астаниэль.');\">Исследовать здесь</a><br></ul>";
	    else {
		
    		?>

    		<ul>
    		<li><a href=javascript:cave('dir=0')>Исследовать здесь</a><br>
    		<? if( isStoneDepth( $depth ) && $player->level>=2 ) { ?> <li><a href="javascript:cave('dir=2')">Искать камни</a><br> <? } ?>
    		</ul>
    		
    		<?
    	}

		$hres = f_MQuery( "SELECT location_items.*, items.name FROM location_items, items WHERE location = {$player->location} AND depth = {$player->depth} AND items.item_id = location_items.item_id" );
		$exec .= "reset_loc_items( );";
		if( f_MNum( $hres ) )
		{
			while( $harr = f_MFetch( $hres ) ) $exec .= "add_loc_item( $harr[item_id], '$harr[name]', $harr[number] );";
		}
		$exec .= "show_loc_items( );";
    }
    else if( $regime >= -1 )
    {
    	// режимы 0 и 4 - одно и тоже. 0 был раньше, но это было неверно. Сейчас режим 0 c till != 0 встречаться не должно
    	// но на всякий случай оставил проверки на 0 тоже
    	if( $regime == 0 || $regime == 4 ) $text = "Вы исследуете местность. До окончания осталось: ";
    	else if( $regime == 1 ) $text = "Вы двигаетесь глубже в пещеры. До окончания осталось: ";
    	else if( $regime == -1 ) $text = "Вы двигаетесь к выходу из пещер. До окончания осталось: ";
    	else if( $regime == 2 ) $text = "Вы ищете камни: ";
    	else $text = "Вы заняты непонятным действием, которым будете заниматься еще: ";

    	$tm = time( );
    	echo "<div id=d$tm>&nbsp;</div>";
		$exec .= "_( 'd$tm' ).innerHTML = NewTimer( ".( $till - $tm ).", '".$text."<b>', '</b>', 0, 'cave(\"ref=1\")' );";
//    	include( 'action_timer.php' );

    	if( $regime == -1 || $regime == 1 ) echo "<a href=\"javascript:cave('cancel=1')\">Прекратить передвижение</a>";
    	if( $regime == 0 || $regime == 4 ) echo "<a href=\"javascript:cave('cancel=1')\">Прекратить исследование</a>";
    	if( $regime == 2 ) echo "<a href=\"javascript:cave('cancel=1')\">Прекратить поиски</a>";
    }

	$ret = ob_get_contents();
	ob_end_clean( );
	return $ret;
}


function createStoneTable( )
{
	global $depth;
	global $player;

	$a = Array( 0, 1, 2, 3, 4, 5, 6, 7, 8 );
	for( $i = 0; $i < 7; ++ $i )
	{
		$j = mt_rand( $i, 8 );
		$t = $a[$i];
		$a[$i] = $a[$j];
		$a[$j] = $t;
	}
	$i0 = getStoneId( $depth );
	$i1 = getStoneId( $depth );
	$i2 = getStoneId( $depth );

	f_MQuery( "DELETE FROM player_stone_table WHERE player_id={$player->player_id}" );
	f_MQuery( "INSERT INTO player_stone_table VALUES( {$player->player_id}, $i0, {$a[0]}, $i1, {$a[1]}, $i2, {$a[2]}, 0 )" );
}

/*

При открытии страницы с пещерами выводится справа getPossibleActions
В центре выводится getMidContent, и все, что выводит game.php
$exec НЕ выполняется.

При обновлении справа выводится содержимое getPossibleActions
В центре выводится getMidContent, вызываются функции для
отображения NPC и доп-действий из game.php и выполняется $exec

$exec заполняется в getMidContent и содержит код для Вещи Тут
Это на самом деле тупо, потому что при открытии страницы вещи
выводятся через game.php, а при обновлении через danger_walk_functions.php,
что немного не гармонично :о)

*/


?>
