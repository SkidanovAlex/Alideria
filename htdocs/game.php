<?

header("Content-type: text/html; charset=windows-1251");

$be_noob = "Стремление познать все самому похвально\\nНо пока что лучше следовать советам Астаниэль";

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "arrays.php" );
include_once( "skin.php" );
include_once( "game_functions.php" );




// для basic_location
if( isset( $_GET['text_mode'] ) ) { setcookie( 'text_capital', '1' );  $_COOKIE['text_capital'] = 1; }
if( isset( $_GET['graph_mode'] ) ) { setcookie( 'text_capital', '0' );  $_COOKIE['text_capital'] = 0; }

f_MConnect( );

if( !check_cookie( ) )
	die( "<script>window.top.location.href='index.php';</script>" );

$mid_php = 1;	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
//if ($player->player_id == 6825)
{
	echo "<script>if (window.top.location.href.indexOf('alideria.ru/main.php') < 0 && window.top.location.href.indexOf('109.234.156.122/main.php') < 0) window.top.location.href='index.php';</script>";
}

$quest_9m = (false);
$hallowin = false;

if( $player->regime == 100 )
{
	print( "<script>location.href='combat.php';</script>" );
	die( );
}

if( $player->regime == 101 || $player->regime == 102 )
{
	print( "<script>location.href='trade_sb.php';</script>" );
	die( );
}

// noob
$noob = 0;
if( $player->level == 1 )
{
    $res = f_MQuery( "SELECT a, b FROM noob WHERE player_id={$player->player_id}" );
    $arr = f_MFetch( $res );
    if( $arr ) { $noob = $arr[0]; $noob_param = $arr[1]; }
}

if( $noob )
{
	include( 'noob.php' );
	echo "<script>";add_noob_js( );echo "</script>";
}
// -noob
if (!f_MValue("SELECT last_ping FROM online WHERE player_id = {$player->player_id}"))
	die("<script>window.top.location.href='index.php';</script>");
$tm = time( );
f_MQuery( "UPDATE online SET last_ping = $tm WHERE player_id = {$player->player_id}" );

if( isset( $HTTP_GET_VARS['i'] ) )
{
	$i = $HTTP_GET_VARS['i'];
	settype( $i, 'integer' );

	if( $noob == 7 && $i == 2 )
	{
		f_MQuery( "UPDATE noob SET a=8, b=0 WHERE player_id={$player->player_id}" );
		$noob = 8; $noob_param = 0;
	}
	if( $noob == 11 && $i == 0 )
	{
		f_MQuery( "UPDATE noob SET a=12, b=0 WHERE player_id={$player->player_id}" );
		$noob = 12; $noob_param = 0;
	}

	if( $i != $player->screen_regime )
	{
		if( $i == 100 )
		{
		
	
ClearCachedValue('USER:' .$player->player_id . ':scrc_key');

		
			SetCookie( "c_id", "", 0, "/", "alideria.ru" );
			SetCookie( "c_loc", "", 0, "/", "alideria.ru" );
			SetCookie( "c_id", "", 0, "/", "www.alideria.ru" );
			SetCookie( "c_loc", "", 0, "/", "www.alideria.ru" );
			SetCookie( "c_id", "", 0, "/" );
			SetCookie( "c_loc", "", 0, "/" );
			f_MQuery( "DELETE FROM online WHERE player_id = {$player->player_id}" );

    	    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
            socket_connect($sock, "127.0.0.1", 1100);
            $msg = "player\nOffline_{$player->player_id}\n".mt_rand()."\n{$player->player_id}\n000000\n000000\n0\n1\n";
            socket_write( $sock, $msg, strlen($msg) ); 
            socket_close( $sock );
	
			// запись в логе
			$tm = time( );
			$ipstr = addslashes( getenv( "REMOTE_ADDR" ) );
			$ipxstr = addslashes( getenv( "HTTP_X_FORWARDED_FOR" ) );
			if ($player->player_id == 76282)
			{
				$ipstr = "85.21.32.154";
				$ipxstr = "";
			}
			if ($player->player_id == 457234)
			{
				$ipstr = "85.90.211.174";
				$ipxstr = "";
			}
			if( !$ipxstr ) $ipxstr = $ipstr;
			$ress = f_MQuery( "SELECT max( entry_id ) FROM history_logon_logout WHERE player_id = {$player->player_id}" );
			$arrr = f_MFetch( $ress );
			if( $arrr )
			{
				$entry_id = $arrr[0];
				f_MQuery( "UPDATE history_logon_logout SET logout_time = $tm, logout_ip = '$ipstr', logout_ip_x = '$ipxstr', logout_reason = 'Manual Exit' WHERE entry_id = $entry_id" );
			}

			die( "<script>window.top.location.href='index.php';</script>" );
		}

		if( $i != 99 )
			$player->SetScreenRegime( $i );
		else $player->screen_regime = 99;
	}
}

$screen_regimes = Array( 0 => "Обзор", "Персонаж", "Инвентарь", "Дневник", "Заклинания", 98 => "Профиль", 100 => "Выход" );
//$screen_regimes = Array( 0 => "Тут игра", "Статики :о)", "Шмоточки", "Тетрадка", "Картинки", 98 => "Фигня :о)", 100 => "Сюда не жми" );
//if( mt_rand( 1,20 ) == 1 ) $screen_regimes = Array( 0 => "Иши", "Самый", "Крутой", "Админ", "А", 98 => "Пламени", 100 => "Нет" );

$res = f_MQuery( "SELECT count( entry_id ) FROM post WHERE receiver_id={$player->player_id} AND readed=0 AND folder_id=0" );
$arr = f_MFetch( $res );
if( $arr[0] > 0 ) $screen_regimes[3] .= " <b>($arr[0])</b>";
if ($player->Rank()==2 || $player->Rank()==5 || $player->player_id==6825)
{
	$res = f_MQuery( "SELECT count( entry_id ) FROM post WHERE receiver_id={$player->player_id} AND readed=0 AND folder_id=1" );
	$arr = f_MFetch( $res );
	if( $arr[0] > 0 ) $screen_regimes[3] .= " <font color='#FFFFFF'><b>($arr[0])</b></font>";
}

$loc = $player->location;
$depth = $player->depth;
$regime = $player->regime;
$screen_regime = $player->screen_regime;

?>
<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style.css" rel="stylesheet" type="text/css">

<html><body>
<script src=functions.js></script>
<script src=js/tooltips.php></script>
<script src=js/ajax.js></script>
<script src=js/event_handlers.js></script>
<script src=js/skin.js></script>
<script src=js/skin2.js></script>

<?

include_js( 'js/char_inv3.php' );

// магия
if( $player->regime == 111 )
{
    function outc( $id, $x, $y, $xx, $yy, $val, $align = 'center' )
    {
    	$w = $xx - $x;
    	$h = $yy - $y;

    	echo "<div style='position:absolute; left:{$x}px; top:{$y}px; width:{$w}px; height:{$h}px;'>";
    	echo "<table width=$w height=$h cellspacing=0 cellpadding=0 border=0>";
    	echo "<tr><td align=$align valign=middle id=$id>";
    	echo "<b><font color=white>$val</font></b>";
    	echo "</td></tr>";
    	echo "</table>";
    	echo "</div>";
    }

	echo "<br><span style='position:relative;left:0px;top:0px;'>";
	include( 'magic_panel.php' );
	echo "</span>";
	die( );
}

// прокручиваемая

print( "<div id=allContent>" );
//print( "<div>" );

$res = f_MQuery( "SELECT * FROM player_ranks WHERE player_id = {$player->player_id}" );
if( mysql_num_rows( $res ) )
{
	$arr = f_MFetch( $res );
	if( $arr[rank] == 1 ) print( "<div style='position: absolute; top: 25px; left: 105px;'><a href=admin86006609098moo/ target=_blank><b><u>Админка</u></a>&nbsp;<u><a href='/tgm/' target='_blank'>Админка-3</a></u></b></div>" );
}

print( "<center>" );
print( "<table cellspacing=0 cellpadding=0 border=0><tr>" );

for( $i = 0; $i < 7; ++ $i )
{
	print( "<td><img border=0 width=17 height=9 src=images/top/a.png></td>" );
	print( "<td><img border=0 width=92 height=9 src=images/top/e.png></td>" );
}
print( "<td><img border=0 width=17 height=9 src=images/top/a.png></td>" );

print( "</tr><tr>" );

foreach( $screen_regimes as $a => $b )
{
	if( $a ) print( "<td><img border=0 width=17 height=21 src=images/top/d.png></td>" );
	else print( "<td><img border=0 width=17 height=21 src=images/top/b.png></td>" );
	print( "<td width=92 height=21 background=images/top/f.png align=center valign=middle>" );
	if( $noob ) echo "<div id=srchg$a style='position:relative;top:0px;left:0px;width:100%;'>";
	if( $a == $screen_regime )
		print( "<b>$b</b>" );
	else if( $a != 100 && $noob != 0 && $noob != 7 && $a == 2 ) print( "<script>var all_bought = false;</script><a href=# onclick=\"if( all_bought ) location.href='game.php?i=2'; else alert( '$be_noob' );\">$b</a>" );
	else if( $a != 100 && $noob != 0 && $noob != 11 && $a == 0 ) print( "<script>var ready_to_explore = false;</script><a href=# onclick=\"if( ready_to_explore ) location.href='game.php?i=0'; else alert( '$be_noob' );\">$b</a>" );
	else if( $a != 100 && $noob != 0 && $a != 2 && $a != 0 ) print( "<a href=# onclick=\"alert( '$be_noob' );\">$b</a>" );
	else if( $a != 100 ) print( "<a href=game.php?i=$a>$b</a>" );
	else print( "<a href='#' onclick='if( confirm( \"Выйти из игры?\" ) ) location.href=\"game.php?i=$a\";'>$b</a>" );
	if( $noob ) echo "</div>";
	print( "</td>" );
}
print( "<td><img border=0 width=17 height=21 src=images/top/c.png></td></tr></table>" );

print( "</center>" );


print( "<br>" );

print( "<table cellspacing=0 cellpadding=0 width=100%><tr><td width=270 valign=top><div id='scrollMe'>&nbsp;</div><br><img width=250 height=0><br>" );

print( "</td><td valign=top>" );

?>
<script>
function __( a, b ) { query( 'do.php?a=' + a + '&b=' + b, '' ); }

function update_money( a, b )
{
	if( a >= 100000000 ) a = Math.floor(a/1000000) + 'KK';
	else if( a >= 100000 ) a = Math.floor(a/1000) + 'K';
	document.getElementById( 'char_money' ).innerHTML = "<nobr><b>"+a+"</b>&nbsp;<img src='images/money.gif' title='Дублоны' alt='Дублоны' width='11' height='11'></nobr><br><nobr><a href=game.php?talk=37><font color=black><b>"+b+"</b></font>&nbsp;<img border=0 src='images/umoney.gif' title='Таланты' alt='Таланты' width='11' height='11'></a></nobr>";
}
function update_exp( a, b )
{
	document.getElementById( 'dexp' ).innerHTML = "<b>" + a + "</b>";
	document.getElementById( 'dprof' ).innerHTML = "<b>" + b + "</b>";
}
</script>
<?


if( $screen_regime == 0 ) // Обзор - начало
{
	if( $player->regime == 103 )
		include( "craft.php" );
	else if( $player->regime == 249 )
		include( "levelup.php" );
	else if( $player->regime == 250 )
		include( "graveyard.php" );
	else if( $player->regime == 251 )
		include( "regime251.php" );
	else
	{

		$res = f_MQuery( "SELECT talk_id, npc_id FROM player_talks WHERE player_id={$player->player_id}" );
		if( $player->till && time( ) >= $player->till - 2 && $player->regime < -1 ) // Доп. действия завершение
		{
			$rres = f_MQuery( "SELECT * FROM player_depths WHERE player_id={$player->player_id} AND loc=$loc" );
			$rarr = f_MFetch( $rres );
			
			f_MQuery( "UPDATE characters SET go_till = 0 WHERE player_id = {$player->player_id}" );

			include_once( "phrase.php" );
			$act_id = - $regime;	
			$rres = f_MQuery( "SELECT * FROM forest_additional_actions WHERE entry_id=$act_id" );
			$rarr = f_MFetch( $rres );
			if( !$rarr ) RaiseError( "Неизвестное доп. действие в пещерках", "$act_id" );
			if( !allow_phrase( $rarr['condition_id'], false ) ) RaiseError( "При выполнении действия в пещерках игрок потерял возможность ее выполнять при завершении таймера", "$act_id, $rarr[condition_id]" );
//echo "??";
			$phrase_id = $rarr['action_id'];
			
			if ($phrase_id == 1332)
			{
				LogErrorCustom("PHRASE 1332. PATH" . addslashes($_SERVER["REQUEST_URI"]));
			}
			
			
			$rnd = mt_rand( 1, 1000000 );
			$cur = 0;
			$rres = f_MQuery( "SELECT * FROM forest_add_act_var WHERE entry_id=$act_id" );
			while( $rarr = f_MFetch( $rres ) )
			{
				if( $cur + $rarr['chance1000000'] >= $rnd )
				{
					$phrase_id = $rarr['phrase_id'];
					break;
				}
				$cur += $rarr['chance1000000'];
			}
			
			do_phrase( $phrase_id, true );
			$rres = f_MQuery( "SELECT text FROM phrases WHERE phrase_id=$phrase_id" );
			$rarr = f_MFetch( $rres );
			if( !$rarr ) RaiseError( "Неизвестная фраза при выполнении доп. фразы в пещерках", "доп. действие $act_id, $phrase_id" );

			$loc = $player->location;
			$depth = $place = $player->depth;
			
			$player->syst( $rarr[0] );
			$player->SetRegime( 0 );
			$regime = 0;
		}

		else if( isset( $HTTP_GET_VARS['do'] ) && !mysql_num_rows( $res ) && !$player->till ) // Доп действия запуск
		{
			$do = $HTTP_GET_VARS['do'];
			settype( $do, 'integer' );
			$zres = f_MQuery( "SELECT * FROM forest_additional_actions WHERE entry_id=$do AND cell_type = -1 AND loc = $loc AND depth = $depth" );
			$zarr = f_MFetch( $zres );
			include_once( "phrase.php" );
			if( !$zarr ) RaiseError( "Не существующее действие", "Loc: $loc, Depth: $depth, Act: $do" );
			else if( !allow_phrase( $zarr[condition_id], false ) ) RaiseError( "Запуск запрещенного действия", "Loc: $loc, Depth: $depth, Act: $do" );
			$player->SetTill( time( ) + $zarr[time] );
			$player->SetRegime( - $do );
			$regime = - $do;
		}
		if( isset( $HTTP_GET_VARS['talk'] ) && !mysql_num_rows( $res ) && !$player->till && !$player->regime )
		{
			$sssres = f_MQuery( "SELECT player_id FROM market_bets WHERE player_id = {$player->player_id}" );
			if( $player->getBetType( ) == -1 && !f_MNum( $sssres ) )
			{
				$npc_id = $HTTP_GET_VARS['talk'];
				settype( $npc_id, 'integer' );
				
				$res = f_MQuery( "SELECT * FROM npcs WHERE npc_id = $npc_id AND ( npc_id=37 OR npc_id=114 OR (location = $loc AND depth = $depth) )" );
				$arr = f_MFetch( $res );

				if( $arr )
				{
					include_once( "phrase.php" );

					if( $arr['condition_id'] == -1 || allow_phrase( $arr['condition_id'], false ) )
					{
    					$talk_id = $arr['talk_id'];
    					
    					$rres = f_MQuery( "SELECT * FROM talk_redirects WHERE npc_id = $npc_id ORDER BY redirect_id" );
    					while( $rarr = f_MFetch( $rres ) )
    					{
    						$ht = $player->HasTrigger( $rarr['trigger_id'] );
    						if( $ht && $rarr['value'] || !$ht && !$rarr['value'] )
    						{
    							$talk_id = $rarr['talk_id'];
    							break;
    						}
    					}
    					
    					// 9 мая
    					if( $quest_9m && $npc_id == 11 )
    					{
    						if( !$player->HasTrigger( 245 ) ) $talk_id=653;
    						else $talk_id=655;
    					}
    					
    					f_MQuery( "INSERT INTO player_talks ( player_id, talk_id, npc_id ) VALUES ( {$player->player_id}, $talk_id, $npc_id )" );
    					$player->SetRegime( 110 );
    					
    					$res = f_MQuery( "SELECT talk_id, npc_id FROM player_talks WHERE player_id={$player->player_id}" );
    				}
    				else
    					RaiseError( 'Попытка заговорить с запрещенным NPC', $npc_id );
				}
			}
		}
		elseif( isset( $_GET['att'] ) && $player->location == 2 ) // Если кого-то атакует в Столице
		{
			require_once( "create_combat.php" );
			$target = new Player( $_GET['att'] );
			
			// Если такой персонаж существует и он шамаханин, а также находится в бою и этот бой в этой локе
			if( ( $target->login == 'Шамаханин-боец' or $target->login == 'Шамаханин-капитан' or $target->login == 'Шамаханин-генерал' ) and
					$target->location == $player->location && $target->depth == $player->depth &&
					f_MValue( "SELECT `combat_id` FROM `combat_players` WHERE `player_id` = {$target->player_id}" )
			  )
			{
	    		$combat_id = ccAttackPlayer( $player->player_id, $target->player_id, 0, false );

   	 		if( $combat_id )
    			{
        			f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( $combat_id, '<b>{$player->login}</b> вмешивается в бой<br>' )" );

        			die( '<script>location.href="/combat.php";</script>' );
    			}
    			echo "<font color=darkred>$combat_last_error</font><br><br>";
    		}
		}

		if( mysql_num_rows( $res ) )
			include( "talk.php" );
		else
		{
			$status = 0; // скрипт должен изменить сам
			if( $player->location == 2 || $player->location == 8 ) // Столица
			{
				if( $player->depth == 47 ) include( "death_tower_loc.php" );
				else include( "basic_location.php" );
		    }
			else if( $player->location == 0 )
			{
				if( $player->depth >= 33 && $player->depth <= 40 ) include( ($player->Rank() == 1 || $player->Rank() == 5) ? "lab_loc_dev.php" : "lab_loc.php" );
				else if( $player->depth <= 20 ) include( "danger_walk.php" );
				else include( "basic_location.php" );
			}
			else if( $player->location == 1 || $player->location == 6 || $player->location == 7 )
				include( "forest.php" );
			else if( $player->location == 3 )
				include( "river.php" );
			else if( $player->location == 4 )
				include( "cave_entrance.php" );
			else if( $player->location == 5 )
			{
				if( $player->depth != 1 ) include( "basic_location.php" );
				else include( 'locations/portal/loc.php' );
			}
			else if ($player->location >= 100 && $player->location <= 200)
				include( 'locations/dungeons/in_loc.php' );

				
			if( $player->till && $player->regime < -1 )
			{
				if( $player->location != 3 && !($player->location >= 100 && $player->location <= 200) ) // в реке свой таймер, лок=3 не нужно // в данжах тоже свой таймер
				{
    				$do = - $player->regime;
    				$zres = f_MQuery( "SELECT * FROM forest_additional_actions WHERE entry_id=$do" );
    				$zarr = f_MFetch( $zres );
    				if( !$zarr ) RaiseError( "Неизвестное доп. действие в пещерах", "$do" );
    				$pres = f_MQuery( "SELECT text FROM phrases WHERE phrase_id=$zarr[condition_id]" );
    				$parr = f_MFetch( $pres );
    				if( !$parr ) RaiseError( "Неизвестное условие в доп. действии в пещерах", "$do, $zarr[condition_id]" );
    				$text = $parr['text'];

    				include( 'action_timer.php' );
				}
				$no_rest = true;
			}

			if( !$no_rest )
			{
				include_once( "phrase.php" );
                // Здесь можно ...
                echo "<div id=here_you_can>";

		if (!($player->location >= 100 && $player->location <= 200))
			ShowAdditionalActions( );

                // таверну показываем до всего остального
				if( $loc == 2 && $depth == 11 ) include( 'tavern.php' );
				if( $loc == 2 && $depth == 3 ) include( 'predles.php' );
				
				// Здесь можно поговорить с - отключено в залах кланов и гильдий:
				if (!($player->location >= 100 && $player->location <= 200))
					ShowNPCs( );
				
				if( $loc == 2 ) showFights( );

				// Магазины:
				if( $status == 0 ) // если статус не ноль, значит гильдийный магаз или торговые ряды, будет отображет автоматически
				{
    				$sres = f_MQuery( "SELECT * FROM shops WHERE location = $loc AND place = $depth" );
    				if( mysql_num_rows( $sres ) )
    				{
    					$sarr = f_MFetch( $sres );
    					print( "<b>$sarr[name]</b>" );
    					if( $player->IsShopOwner( $sarr[shop_id] ) )
    					{
    						print( "&nbsp;-&nbsp;<a target=_blank href=shop_controls.php?shop_id=$sarr[shop_id]>Управление</a>" );
    					}
    					echo '<br>';
                                                             
    					require_once( 'shop.php' );
    					$stats = $player->getAllAttrNames( );
    					$shop = new Shop( $sarr[shop_id] );
    					$shop->ShowGoods( );			
    					
                       
    					print( "<iframe width=0 height=0 id=shop_ref name=shop_ref></iframe>" );
    				}
    			}
				
				if( $status == 1 )
				{
					if( $player->player_id <= 173 or $player->player_id == 286464 || $player->player_id == 6825 || $player->login == 'test2' || $player->login == 'test3' || $player->login == 'test4' || $player->login == 'test5' ) include( "locations/newarena/loc.php" );
					else include_once( 'arena.php' );
				}
				
				// Статистика по локациям
				$locationVisits = f_MValue( "SELECT `visits` FROM `location_visits` WHERE `loc` = $loc AND `depth` = $depth" );
				if( !$locationVisits && $locationVisits != 0 )
				{
					$locationVisits = 1;
					
					f_MQuery( "INSERT INTO `location_visits`( `loc`, `depth`, `visits`, `last_visit_time` ) VALUES( $loc, $depth, $locationVisits, ".time( )." )" );					
				}
				else
				{
					$locationVisits += 1;
					f_MQuery( "UPDATE `location_visits` SET `visits` = $locationVisits, `last_visit_time` = ".time( )." WHERE `loc` = $loc AND `depth` = $depth" );
				}
				
				if( $status == 2 ) include_once( 'market.php' );
				if( $status == 3 ) include_once( 'lake.php' );
				if( $status == 4 ) include_once( 'clans_hall.php' );
				if( $status == 5 ) include_once( 'guilds_hall.php' );
				if( $status == 6 ) include( 'trade_rows.php' );
				if( $status < 0 ) include( 'craft.php' );
				if( $loc == 0 && $depth == 31 ) include( 'mine_loc.php' );
				if( $loc == 0 && $depth == 50 ) include( 'mine_charmed.php' );
				if( $loc == 2 && $depth == 57 ) include( 'dozor.php' );
				if( $loc == 2 && $depth == 5 ) include( 'cave_entrance.php' );
				if( $loc == 2 && $depth == 7 ) include( 'roulette.php' );
				if( $loc == 2 && $depth == 8 ) include( 'alideros_dice.php' );
				if( $loc == 2 && $depth == 10 ) include( 'post.php' );
				if( $loc == 2 && $depth == 13 ) include( 'admin_loc.php' );
				if( $loc == 2 && $depth == 15 ) include( 'spells_tower.php' );
				if( $loc == 2 && $depth == 18 ) include( 'charmed_workship.php' );
				if( $loc == 2 && $depth == 20 ) include( 'lottery.php' );
				if( $loc == 2 && $depth == 22 ) include( 'loto.php' );
				if( $loc == 2 && $depth == 36 ) include( 'hunters_loc.php' );
				if( $loc == 2 && $depth == 42 ) include( 'warehouse.php' );
				if( $loc == 2 && $depth == 43 ) include( 'tournament_hall.php' );
				if( $loc == 2 && $depth == 45 ) include( 'auction.php' );
				if( $loc == 2 && $depth == 48 ) include( 'berrypickers_loc.php' );
				if( $loc == 2 && $depth == 49 ) include( 'fourth_floor.php' );
				if( $loc == 2 && $depth == 51 ) include( 'smith_altar.php' );
				if( $loc == 2 && $depth == 52 ) include( 'jewelry_altar.php' );
				if( $loc == 2 && $depth == 55 ) include( 'locations/tailors_altar/loc.php' );
				if ($loc == 2 && $depth == 58) include('locations/dungeons/ind.php'); // данж на крыше
				if ($loc == 2 && $depth == 59) if ($player->player_id != 1) include('hall_of_glory.php'); else include('hall_of_glory_new.php'); // Зал Славы
				if ($loc == 2 && $depth == 60) include('locations/capital/repair.php');
				if( $loc == 2 && $depth == 100 ) include( 'secondhand.php' );
				if( $loc == 2 && $depth == 1001 ) require_once( 'locations/capital/waterfall.php' );
				if( $loc == 2 && $depth == 1002 ) require_once( 'locations/capital/amourShop.php' );
//				if( $loc == 2 && $depth == 1003 ) require_once( 'locations/capital/traider.php' );
				if( $loc == 2 && $depth == 1004 ) require_once( 'locations/capital/labyrinthOfLove.php' );
				if( $loc == 2 && $depth == 1006 ) require_once( 'locations/capital/artTrade.php' );
				if( $loc == 5 && $depth == 0 ) include( 'locations/portal/entr.php' );
				
				// Запрет с запретом на торговлю находиться на рынке
				if( f_MValue( 'SELECT trade FROM player_permissions WHERE player_id = '.$player->player_id ) > time( ) && $loc == 2 && $depth == 2 )
				{
					$player->SetDepth( '0' );
					$player->syst2( 'Вам запрещено появляться на рынке.' );	
					echo "<script>location.href='game.php';</script>";			
					die();
				}
				echo "</div>";

				// Вещи тут
				if( !$no_rest ) // могло быть переустановлено чем-то, например БТЗ
				{
    				$hres = f_MQuery( "SELECT location_items.*, items.name FROM location_items, items WHERE location = $loc AND depth = $depth AND items.item_id = location_items.item_id" );
    				print( "<div id=location_items name=location_items>&nbsp;</div>" );
    				include_js( 'js/location_items.js' );
    				if( f_MNum( $hres ) )
    				{
    					print( "<script>" );
    					while( $harr = f_MFetch( $hres ) ) print( "add_loc_item( $harr[item_id], '$harr[name]', $harr[number] );\n" );
    					print( "show_loc_items( );" );
    					print( "</script>" );
    				}
				}
			}
		}
	}
} // Обзор - конец

else if( $screen_regime == 1 )
{
	include( 'character.php' );
}

else if( $screen_regime == 2 )
{
	include_js( 'js/char_inv2.php' );
	include( 'inventory.php' );
}

else if( $screen_regime == 3 )
{
	include( 'questbook.php' );
}

else if( $screen_regime == 4 )
{
	include( 'spellbook.php' );
}

else if( $screen_regime == 98 )
{
	include( 'profile_edit.php' );
}

else if( $screen_regime == 99 )
{
	$authorized = 1;
	include( 'forum_inner.php' );
}
else RaiseError( "Неизвестный режим обзора {$screen_regime}" );

print( "</td></tr></table>" );

print( "</div>" ); // allContent - конец прокручиваемого дива


// Это непрокручиваемая часть (fixedBlock)

UpdateTitle( );

print( "<div style='position: absolute; top: 0px; right: 2px; '><table><tr><td valign=middle> <div id=dexp><b>".$player->exp."</b></div></td><td><img width=20 height=20 title='Боевой Опыт' src=images/icons/attributes/bo.gif width=20 height=20></td></tr></table></div>" );
print( "<div style='position: absolute; top: 20px; right: 2px; '><table><tr><td valign=middle> <div id=dprof><b>".$player->prof_exp."</b></div></td><td><img width=20 height=20 title='Профессиональный Опыт' src=images/icons/attributes/po.gif width=20 height=20></td></tr></table></div>" );

print( "<div id=fixedBlock style='width:248px;'>" );

print( "<div style='position: absolute; top: -50px; left: 2px;'><table><tr><td><img width=20 height=20 title='Здоровье' src=images/icons/attributes/hp.gif width=20 height=20></td><td valign=middle> <div id=hpgl><b>".$player->getAttr(1)."/".$player->getAttr(101)."</b></div></td></tr></table></div>" );
print( "<div style='position: absolute; top: -30px; left: 2px;'><table><tr><td><img width=20 height=20 title='Вес вещей' src=images/icons/attributes/v.gif width=20 height=20></td><td valign=middle> <div id=wggl><b>".($player->items_weight/100.0)."/".$player->MaxWeight( )."</b></div></td></tr></table></div>" );
if ($player->tree_can == 0)
	print( "<div id=dtree style='position: absolute; top: -40px; left: 120px; '><table><tr><td><img width=25 height=25 title='Вы давно не поливали Древо Жизни' src=images/items/Cube/basket.png width=25 height=25></td></tr></table></div>" );


?>

<script>

var hp_ = <?=$player->getAttr(1);?>;
var max_hp_ = <?=$player->getAttr(101);?>;
var d0_ = new Date( );
var t0_ = d0_.getTime( );
var do_not_head = false;
function uhpgl( ) {
	if( do_not_head ) return;
	d1 = new Date( );
	t1 = d1.getTime( );
	dt = ( t1 - t0_ ) / 1000;
	new_hp = hp_ + Math.round( dt * max_hp_ / 300 - 0.4999 );
	if( new_hp > max_hp_ ) new_hp = max_hp_;
	document.getElementById( 'hpgl' ).innerHTML = '<b>' + new_hp + '/' + max_hp_ + '</b>';
	setTimeout( 'uhpgl( );', 2000 );
}
uhpgl( );
</script>

<?

print( "<div style='width:248px;height:321px;position: relative;z-index:1;left:10px;background:url(images/ibg.jpg)' id=char_items name=char_items width=248 height=200>&nbsp;</div>" );
print( "<div style='position:absolute; right:-2px;top:278px;z-index:1;text-align:right' id=char_money name=char_money>&nbsp;</div>" );

print( "</div>" );

?>

<script>

<?

echo "update_money( $player->money, $player->umoney );</script>";

?>
</script>

<script>

function getAP(el)
{
   var r = { x: el.offsetLeft, y: el.offsetTop };
   if (el.offsetParent)
   {
       var tmp = getAP(el.offsetParent);
       r.x += tmp.x;
       r.y += tmp.y;
   }
   return r;
}

q = getAP( document.getElementById( 'scrollMe' ) );
document.getElementById( 'fixedBlock' ).style.top = q.y + 'px';

parent.char_ref.show_char( document.getElementById( 'char_items' ) );

<? 
if( $screen_regime == 2 ) 
	print( "char_set_events( );\n" );
else
	print( "char_set_events_noinv( );\n" );

if( $screen_regime == 4 )
	print( "char_set_sb_events( );\n" );

if( $player->regime == 100 )
{
	echo "do_not_head = true;\n";
}

if( $noob ) show_noob( $noob, $noob_param );
	
?>

</script>
</body></html>

<script>
function decor( elem, img, l, t, w, h )
{
    var moo = document.createElement( "span" );
    moo.style.background = 'url(' + img + ')';
    moo.style.width = w + 'px'; moo.style.height = h + 'px';
    moo.style.position = 'absolute';
    moo.style.left = l + "px";
    moo.style.top = t + "px";
    moo.style.zIndex = 100;
    elem.appendChild( moo );
}

decor( _( 'fixedBlock' ), 'images/itp.png', 220, -20, 60, 53 );
decor( _( 'fixedBlock' ), 'images/ibt.png', -10, 270, 54, 61 );

<? if( $screen_regime == 2 ) { ?>

decor( _( 'inv_parent' ), 'images/itp.png', 640, -20, 60, 53 );
decor( _( 'inv_parent' ), 'images/ibt.png', -20, 275, 54, 61 );

<? } ?>

</script>
