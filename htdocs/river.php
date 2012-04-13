<?

if( !$mid_php ) die( );

include( 'tella_assault.php' );

$sm = array( );
for( $i = 0; $i < 7; ++ $i ) $sm[$i] = array( );
$sm[0][1] = $sm[1][0] = true;
$sm[2][1] = $sm[1][2] = true;
$sm[3][1] = $sm[1][3] = true;
$sm[4][1] = $sm[1][4] = true;
$sm[3][5] = $sm[5][3] = true;
$sm[6][4] = $sm[4][6] = true;
$sm[7][0] = $sm[0][7] = true;


echo "<table width=100% cellspacing=0 cellpadding=0><colgroup><col width=*><col width=300><tr>";

echo "<td valign=top>";

if( $player->regime == 0 && isset( $_GET['dir'] ) )
{
	if( $_GET['dir'] == -1 && ($depth == 0 || $depth == 7 ) )
	{
		$player->SetLocation( 2 );
		$player->SetDepth(0);
		die( "<script>location.href='game.php';</script>" );
	}
	$dir = (int)$_GET['dir'];
	if( $sm[$depth][$dir] )
	{
		$player->SetRegime( 1 + $dir );
		$player->SetTill( time( ) + 30 );
	}
}

if( $player->regime == 0 && isset( $_GET['monsters'] ) && $depth != 2 )
{
	$player->SetRegime( -1 );
	$player->SetTill( time( ) + 30 );
}

include_js( 'js/timer.js' );

$msg = '';

if( $player->regime != 0 )
{
	$rem = $player->till - time( );
	if( $rem < 2 && $player->regime > 0 )
	{
		$player->SetDepth( $player->regime - 1 );
		$player->SetTill( 0 );
		$player->SetRegime( 0 );
		$depth = $player->depth;
	}
	else if( $rem < 2 )
	{
		$res = f_MQuery( "SELECT mob_id FROM mobs WHERE loc=3 AND defend_depth=$depth ORDER BY rand() LIMIT 1" );
		$arr = f_MFetch( $res );

		if( $depth == 5 )
		{

			$hours = date( "H" );
			if( $hours != 16 && $hours != 20 ) $arr = false;
			else
			{
				$res2 = f_MQuery( "SELECT  last_time_aculoid_spawned FROM statistics" );
				$arr2 = f_MFetch( $res2 );
				if( time( ) - $arr2[0] < 60 * 60 + 5 ) $arr = false;
				else f_MQuery( "UPDATE statistics SET last_time_aculoid_spawned = ".time( ) );
			}

		}

		if( !$arr )
		{
    		$player->SetTill( 0 );
    		$player->SetRegime( 0 );
    		$msg = '<i>К сожалению, вы никого не нашли.</i><br><br>';
		}
		else
		{
			include_once( "mob.php" );
			$player->SetTill( 0 );
			$mob = new Mob;
			$mob->CreateMob( $arr[0], $loc, $player->depth );
			$mob->AttackPlayer( $player->player_id, 0, 0, true /* нападаем кроваво */ );
			$rnd = 5;
			while( $mob->level + 1 < $player->level && mt_rand( 1, $rnd ) == 1 )
			{
				$mob = new Mob;
    			$mob->CreateMob( $arr[0], $loc, $player->depth );
    			$mob->AttackPlayer( $player->player_id, 0, 0, true /* нападаем кроваво */ );
    			$rnd += 2;
			}
	   		$msg = ( "<br><font color=red><b>Внимание!</b> </font>Вас атакует <b>{$mob->name}</b>!!! <a href=combat.php>Продолжить</a><br>" );
	   		if ($arr[0] == 33) // Акулоид
	   		{
			                $mtr = mt_rand(2, 5);
			                while ($mtr > 0)
			                {
			                    $mob = new Mob;
			                    $mob->CreateMob( 48, $loc, $player->depth );
			                    $mob->AttackPlayer( $player->player_id, 0, 0, true /* нападаем кроваво */ );
			                    $mtr--;
			                }
	   		}
		}
	}
	else if( $_GET['cancel'] && $player->regime>=-1 )
	{
		$player->SetTill( 0 );
		$player->SetRegime( 0 );
	}
}

$res = f_MQuery( "SELECT title, text FROM loc_texts WHERE loc=$loc AND depth=$depth" );
$arr = f_MFetch( $res );

echo "<b>Река, $arr[0]</b><br>";
print( "<div align=justify>$arr[1]</div><br>" );

echo $msg;

if( $player->regime == 100 )
{
	// skip if in combat
}
else if( $player->regime != 0 )
{
	if( $player->regime > 0 )
	{
    	echo "<i>Вы плывете под водой...</i><br>";
    	$target= $player->regime - 1;
    	$res = f_MQuery( "SELECT title FROM loc_texts WHERE loc=3 AND depth=$target" );
    	$arr = f_MFetch( $res );
    	echo "Ваша цель: <b>$arr[0]</b><br><br>";
	} else if( $player->regime == -1 ) echo "<i>Вы ищете местных обитателей...</i><br><br>";
	else
	{
    				$do = - $player->regime;
    				$zres = f_MQuery( "SELECT * FROM forest_additional_actions WHERE entry_id=$do" );
    				$zarr = f_MFetch( $zres );
    				if( !$zarr ) RaiseError( "Неизвестное доп. действие в реке", "$do" );
    				$pres = f_MQuery( "SELECT text FROM phrases WHERE phrase_id=$zarr[condition_id]" );
    				$parr = f_MFetch( $pres );
    				if( !$parr ) RaiseError( "Неизвестное условие в доп. действии в реке", "$do, $zarr[condition_id]" );
    				$text = $parr['text'];
		echo "<i>".$text."</i><br><br>";
	}
	echo "<script>document.write( InsertTimer( $rem, 'Осталось: <b>', '</b>', 0, 'location.href=\"game.php\"' ) );</script>";
	if ($player->regime>=-1)
		echo "<a href=game.php?cancel=1>Остановиться</a>";
}
else if( $depth == 2 )
{
	echo "<i>Вы осматриваетесь по сторонам, и не видите ничего, что могло бы привлечь ваше внимание сейчас...</i>";
}
else if( $depth == 0 && ta_now( ) )
{
	echo "<font color=darkred><b>Внимание!</b></font> Речные монстры вышли из под контроля и угрожают спокойствию Теллы!!! Вы можете вступить в бой за любимый город!";
	ta_output( 3, "Толпу змей и рыб слева", "Толпа рыб и змей слева повержена", "Бой с толпой змей и рыб слева проигран", "Толпа змей и рыб еще слишком далеко" );
	ta_output( 4, "Группу пираний и пиявку в центре", "Группа пираний и пиявка в центре повержены", "Бой с группой пираний и пиявкой по центру проигран", "Группа пираний и пиявка еще слишком далеко" );
	ta_output( 5, "Акулоида справа", "Акулоид справа повержен", "Бой с Акулоидом справа проигран", "Акулоид еще слишком далеко" );
}
else
{
    $peace = ( $depth == 2 || $depth == 7 );

    if( isset( $_GET['att'] ) )
    {
   		$target_id = $_GET['att'];
    	settype( $target_id, 'integer' );
    	$cres = f_MQuery( "SELECT characters.player_id, 0 as mobik, characters.regime FROM characters, online WHERE characters.player_id = online.player_id AND characters.loc = $loc AND characters.depth = $depth AND characters.player_id = $target_id UNION
    	                  SELECT characters.player_id, 1 as mobik, characters.regime FROM characters, combat_players WHERE characters.player_id = combat_players.player_id AND characters.loc = $loc AND characters.depth = $depth AND combat_players.ai = 1 AND combat_players.ready < 2 AND characters.player_id = $target_id" );
    	$carr = f_MFetch( $cres );
    	if( !$carr )  echo "<font color=darkred>Здесь нет этого игрока. Вероятно, он успел уплыть отсюда или выбраться на берег.</font><br><br>";
    	else if( $target_id == $player->player_id ) RaiseError( "Возможно попытка напасть на самого себя в лесу" );
    	else if( $peace && !$carr['mobik'] ) echo "<font color=darkred>Тут нельзя нападать на игроков!</font><br><br>";
    	else if( $carr['regime'] == 250 ) echo "<font color=darkred>Игрок в настоящий момент находится в домике лекаря.</font><br><br>";
    	else if( $carr[0] == 173 ) echo "<font color=darkred>По-моему, вы пытаетесь атаковать Ишу. :оО</font><br><br>";
    	else if( $carr[0] == 3264 ) echo "<font color=darkred>Спасибо за проявленное любопытство, но нападать на Мая в реке не надо.</font><br><br>";
    	else
    	{
    		$target = new Player( $target_id );
    		$target->SetTill( 0 );
    		include( "create_combat.php" );
    		$combat_id = ccAttackPlayer( $player->player_id, $target_id, 0, true );
    		if( $combat_id )
    		{
	    		$target->syst2( "/combat" );

        		if( $target->regime == 100 ) f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( $combat_id, '<b>{$player->login}</b> вмешивается в бой<br>' )" );
        		else f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( $combat_id, '<b>{$player->login}</b> нападает в реке на персонажа <b>{$target->login}</b><br>' )" );

        		die( '<script>location.href="combat.php";</script>' );
    		}
    		echo "<font color=darkred>$combat_last_error</font><br><br>";
    	}
    }

    echo "<table width=100%><tr>";
    echo "<td valign=top width=50%>";
    echo "<b>Игроки тут</b> (<a href='javascript:ref_players()'>Обновить</a>):<br>";
    echo "<div id=players>&nbsp;</div>";

    ?>

    <script>
    var plrs = new Array( );
    var plrids = new Array( );
    var cids = new Array( );
    function ref_players()
    {
    	query("river_players.php","");
    }
    function river_add_player( nick, can_attack, in_combat, id, combat_id )
    {
    	st = '<a title="Нельзя напасть"><img src="images/a_silver.gif" border=0></a>';
    	if( can_attack ) st = '<a title="Напасть" style="cursor: pointer" href="game.php?att='+id+'"><img src="images/a_green.gif" border=0 width=11 height=11></a>';
    	if( in_combat ) st = '<a title="Напасть" style="cursor: pointer" href="game.php?att='+id+'"><img src="images/a_red.gif" border=0 width=11 height=11></a>';
    	st += '&nbsp;';
    	st += nick;

    	if( !cids[combat_id] )
    		cids[combat_id] = new Array( );
    	cids[combat_id].push( plrs.length );

    	plrs[plrs.length] = st;
    	plrids[plrids.length] = id;
    }

    function river_show_players( )
    {
    	st = '';
    	for( c in cids ) if( c == 0 || cids[c].length > 1 )
    	{
	    	for( i in cids[c] ) st += plrs[cids[c][i]] + '<br>';
	    	st += '<br>';
	    }
    	_( 'players' ).innerHTML = ( st );
    }

    <?

	$res = f_MQuery( "SELECT characters.login, characters.regime, characters.player_id, combat_id, 0 as mobik FROM characters INNER JOIN online ON characters.player_id = online.player_id LEFT JOIN combat_players ON characters.player_id = combat_players.player_id WHERE characters.loc = $loc AND characters.depth = $depth UNION
	                   SELECT characters.login, characters.regime, characters.player_id, combat_id, 1 as mobik FROM characters, combat_players WHERE characters.player_id = combat_players.player_id AND characters.loc = $loc AND characters.depth = $depth AND combat_players.ai = 1 AND combat_players.ready < 2" );
	$can_attack = !$peace;
	if( $player->regime != 0 ) $can_attack = 0;
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr[2] );
		$in_combat = ( $arr[1] == 100 ) ? 1 : 0;
		$moo = $can_attack?$can_attack:0;
		if( $arr[2] == $player->player_id ) $moo = 0;
		if( $arr['mobik'] ) $moo = 1;
		if( !$moo ) $in_combat = 0;
		if( !$plr->nick_clr ) $plr->nick_clr = 'FFFFFF';
		$arr['combat_id'] = (int)$arr['combat_id'];
		echo "river_add_player( ".$plr->Nick2().", $moo, $in_combat, $arr[2], $arr[combat_id] );";
	}
	echo "river_show_players();";

	echo "</script>";


    echo "</td>";
    echo "<td valign=top width=50%>";
    echo "<b>Местные обитатели:</b><br>";
    echo "<li STYLE='list-style-image: URL(\"images/dots/dot-attack.gif\")'><a href=game.php?monsters=1>Искать обитателя для атаки</a>";
    echo "</td>";
    echo "</tr></table>";
}


// left part of screen - finish
echo "</td>";

echo "<td valign=top align=right>";

	function outLoc( $name, $id )
	{
		global $player;
		global $sm;
		$cur = $player->depth;

		echo "<td>";

		if( $id == $cur ) echo "<script>FLUl();</script>";
		else echo "<script>FUlt();</script>";

		echo "<table><tr><td align=center valign=middle style='width:70px;height:60px;'>";

		echo "<small><b>".$name."</b></small>";
		if ($player->level>=4 || $cur!=7)
		if( $player->regime == 0 && $sm[$id][$cur] ) echo "<br><a href=game.php?dir=$id><u><small><b>Плыть</b></small></u></a>";
		if( $player->regime == 0 && $cur == 7 && $id == 7 ) echo "<br><a href=game.php?dir=-1><u><small><b>В Город</b></small></u></a>";
//		if( $player->player_id==6825 && $player->regime == 0 && $cur == 7 && $id == 7 ) echo "<br><a href=game.php?dir=-1><u><small><b>В Город</b></small></u></a>";

		echo "</td></tr></table>";

		if( $id == $cur ) echo "<script>FLL();</script>";
		else echo "<script>FL();</script>";
	}

	echo "<table>";

	echo "<tr>"; outLoc( 'Лягушатник', 7 ); outLoc( 'Подводный<br>Сад', 2 ); outLoc( 'Омут', 6 ); echo "</tr>";
	echo "<tr>"; outLoc( 'Теплые<br>Воды', 0 ); outLoc( 'Подводные<br>Равнины', 1 ); outLoc( 'Грот<br>Гейзеров', 4 );
	echo "<tr><td>&nbsp;</td>"; outLoc( 'Расщелина<br>Духов', 3 ); echo "<td>&nbsp;</td></tr>";
	echo "<tr><td>&nbsp;</td>"; outLoc( 'Обитель<br>Тьмы', 5 ); echo "<td>&nbsp;</td></tr>";

	echo "</table>";

echo "</td>";

echo "</tr></table>";

?>
