<?

include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

if( $player->regime == 100 ) die( '<script>parent.location.href="combat.php";</script>' );
$res = f_MQuery( "SELECT status FROM loc_texts WHERE loc={$player->location} AND depth={$player->depth}" );
$arr = f_MFetch( $res );

if( !$arr ) RaiseError( "Вы находитесь в локации, которой вообще говоря нет", "{$player->location}:{$player->depth}" );
if( $arr['status'] != 1 ) die( );

$a = $HTTP_GET_VARS['a'];
$b = $HTTP_GET_VARS['b'];
$c = $HTTP_GET_VARS['c'];
$d = $HTTP_GET_VARS['d'];

if( !isset( $b ) ) $b = -1;
if( !isset( $c ) ) $c = -1;
if( !isset( $d ) ) $d = -1;

settype( $a, 'integer' );
settype( $b, 'integer' );
settype( $c, 'integer' );
settype( $d, 'integer' );

$upload = false;

?>

<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="style2.css" rel="stylesheet" type="text/css">

<?

print( "<div id=moo name=moo>" );

$type = $a + 1;

$old_type = $player->getBetType( );
if( $old_type != -1 )
{
	$type = $old_type;
	$a = $type - 1;
}

if( ( $a == 1 || $a == 2 ) && $player->level < 3 )
{
	echo "<i>Эта часть Арены Героев откроется для вас только после достижения третьего уровня.</i>";
	$upload = true;
}

else if( $a == 0 || $a == 1 || $a == 2 )
{
	printf( "<script src=js/arena.php></script>" );
	if( $b == 0 ) // Подача заявки
	{
		$res = f_MQuery( "SELECT * FROM player_bets WHERE player_id = {$player->player_id}" );
		if( !f_MNum( $res ) )
		{
			$tm = time( );
			f_MQuery( "INSERT INTO combat_bets ( time, leader, type, location, place ) VALUES ( $tm, {$player->player_id}, $type, {$player->location}, {$player->depth} )" );
			$bet_id = mysql_insert_id( );
			f_MQuery( "INSERT INTO player_bets ( bet_id, player_id, side ) VALUES ( $bet_id, {$player->player_id}, 0 )" );
		}
	} // Конец - подача заявки
	
	if( $b == 1 ) // Отзывание заявки
	{
		$res = f_MQuery( "SELECT bet_id FROM combat_bets WHERE leader={$player->player_id}" );
		if( f_MNum( $res ) )
		{
			$arr = f_MFetch( $res );
			f_MQuery( "DELETE FROM player_bets WHERE bet_id = $arr[0]" );
			f_MQuery( "DELETE FROM combat_bets WHERE bet_id = $arr[0]" );
		}
		f_MQuery( "DELETE FROM player_bets WHERE player_id = {$player->player_id}" );
	} // Конец - отзывание заявки
	
	if( $b == 2 ) // Запуск боя
	{
		$log_type = 0;
		if( $a == 0 ) $log_type = 1;
		if( $a == 1 ) $log_type = -1;
		if( $a == 2 ) $log_type = -2;
			
		$res = f_MQuery( "SELECT bet_id FROM combat_bets WHERE leader={$player->player_id}" );
		if( f_MNum( $res ) )
		{
			$arr = f_MFetch( $res );
			$lf = Array( );
			$rg = Array( );
			$cok = 0;
			if( $type == 1 || $type == 2 )
			{
				$res1 = f_MQuery( "SELECT player_id FROM player_bets WHERE bet_id = $arr[0] AND side = 0" );
				$res2 = f_MQuery( "SELECT player_id FROM player_bets WHERE bet_id = $arr[0] AND side = 1" );
				if( f_MNum( $res1 ) && f_MNum( $res2 ) )
				{
					while( $arr1 = f_MFetch( $res1 ) ) $lf[] = $arr1[0];
					while( $arr2 = f_MFetch( $res2 ) ) $rg[] = $arr2[0];
					$cok = 1;
				}
			}
			else
			{
				$res = f_MQuery( "SELECT player_id FROM player_bets WHERE bet_id = $arr[0] ORDER BY rand()" );
				if( f_MNum( $res ) % 2 )
					printf( "<script>alert( 'Количество участников должно быть четным. Сейчас их ".f_MNum( $res ).".' );</script>" );
				else
				{
					$q = f_MNum( $res );
					for( $i = 0; $i < $q / 2; ++ $i )
						if( $arr1 = f_MFetch( $res ) ) $lf[] = $arr1[0];
					for( $i = 0; $i < $q / 2; ++ $i )
						if( $arr1 = f_MFetch( $res ) ) $rg[] = $arr1[0];
					$cok = 1;
				}
			}
			if( $cok )
			{
				include( 'create_combat.php' );
				CreateCombat( $lf, $rg, $player->location, $player->depth, $log_type );

				f_MQuery( "DELETE FROM player_bets WHERE bet_id = $arr[0]" );
				f_MQuery( "DELETE FROM combat_bets WHERE bet_id = $arr[0]" );

				die( "<script>parent.location.href='combat.php';</script>" );
			}
		}
	} // Конец - запуск боя
	
	if( $b == 3 && $type == 1 ) // Отказ от оппонента (только дуэль)
	{
		$res = f_MQuery( "SELECT bet_id FROM combat_bets WHERE leader={$player->player_id}" );
		if( f_MNum( $res ) )
		{
			$arr = f_MFetch( $res );
			f_MQuery( "DELETE FROM player_bets WHERE bet_id = $arr[0] AND player_id <> {$player->player_id}" );
		}
	} // Конец - отказ от оппонента
	
	if( $c != -1 ) // Прием чужой заявки
	{
		if( $d != 0 && $d != 1 ) RaiseError( "Неверный параметр D в скрипте arena_ref. c=$c, d=$d" );
		$res = f_MQuery( "SELECT type FROM combat_bets WHERE bet_id = $c" );
		if( f_MNum( $res ) ) // Такая заявка вообще есть
		{
			$arr = f_MFetch( $res );
			$res2 = f_MQuery( "SELECT player_id FROM player_bets WHERE bet_id = $c AND side = $d" );
			if( $arr[type] != 1 || !f_MNum( $res2 ) ) // не пустим в дуэль, если уже кто-то висит
			{
				$res3 = f_MQuery( "SELECT player_id FROM player_bets WHERE player_id = {$player->player_id}" );
				if( !f_MNum( $res3 ) )
					f_MQuery( "INSERT INTO player_bets ( bet_id, player_id, side ) VALUES ( $c, {$player->player_id}, $d )" );
			}
		}
	} // Конец - прием чужой заявки
	
	$res = f_MQuery( "SELECT combat_bets.* FROM combat_bets, player_bets WHERE player_id = {$player->player_id} AND combat_bets.bet_id = player_bets.bet_id AND location={$player->location} AND place={$player->depth}" );
	if( !f_MNum( $res ) ) printf( "<li><a target=arena_ref href=arena_ref.php?a=$a&b=0>Подать заявку</a></li><br>" );
	else
	{
		printf( "<script>arena_status = 1;</script>" );
		printf( "<li><a target=arena_ref href=arena_ref.php?a=$a&b=1>Отозвать заявку</a></li>" );
		$arr = f_MFetch( $res );
		if( $arr[leader] == $player->player_id )
		{
			if( $type == 1 || $type == 2 )
			{
				$res2 = f_MQuery( "SELECT characters.login FROM player_bets, characters WHERE bet_id = $arr[bet_id] AND characters.player_id = player_bets.player_id AND player_bets.side = 1" );
				$arr2 = f_MFetch( $res2 );
				if( $arr2 )
				{
					if( $type == 1 )
					{
						printf( "<li><a target=arena_ref href=arena_ref.php?a=$a&b=2>Согласиться на бой с игроком <b>$arr2[0]</b></a></li>" );
						printf( "<li><a target=arena_ref href=arena_ref.php?a=$a&b=3>Отказаться от боя</a></li>" );
					}
					else printf( "<li><a target=arena_ref href=arena_ref.php?a=$a&b=2>Запустить бой</a></li><br>" );
				}
			}
			else if( $type == 3 )
			{
				$res2 = f_MQuery( "SELECT player_id FROM player_bets WHERE bet_id = $arr[bet_id] AND player_id <> {$player->player_id}" );
				$arr2 = f_MFetch( $res2 );
				if( $arr2 )
				{
					printf( "<li><a target=arena_ref href=arena_ref.php?a=$a&b=2>Запустить бой</a></li><br>" );
				}
			}
		}
	}
	
	$rres = f_MQuery( "SELECT * FROM combat_bets WHERE type=$type" );
	
	printf( "<br>" );
	if( f_MNum( $rres ) == 0 )
	{
		if( $type == 1 ) printf( "<i>Не подано ни одной заявки на дуэль</i>" );
		if( $type == 2 ) printf( "<i>Не подано ни одной заявки на групповой бой</i>" );
		if( $type == 3 ) printf( "<i>Не подано ни одной заявки на хаотичный бой</i>" );
	}
	else
	{
		if( $type == 1 )
		{
			printf( "<script>dstart();" );
			while( $rarr = f_MFetch( $rres ) )
			{
				$res1 = f_MQuery( "SELECT login FROM characters WHERE player_id = $rarr[leader]" );
				$arr1 = f_MFetch( $res1 );
				$res2 = f_MQuery( "SELECT characters.login FROM characters, player_bets WHERE bet_id = $rarr[bet_id] AND characters.player_id = player_bets.player_id AND player_bets.player_id <> $rarr[leader]" );
				$arr2 = f_MFetch( $res2 );
				printf( "dbet( $rarr[bet_id], '$arr1[0]', '$arr2[0]' );" );
			}
			printf( "dfin();</script>" );
		}
		else if( $type == 2 )
		{
			printf( "<script>\n" );
			while( $rarr = f_MFetch( $rres ) )
			{
				$res1 = f_MQuery( "SELECT login FROM characters WHERE player_id = $rarr[leader]" );
				$arr1 = f_MFetch( $res1 );
				printf( "\tmstart( '$arr1[0]' );\n" );
				printf( "\tmbet( '<u>$arr1[0]</u>' );\n" );
				$res2 = f_MQuery( "SELECT characters.login FROM characters, player_bets WHERE bet_id = $rarr[bet_id] AND characters.player_id = player_bets.player_id AND player_bets.player_id <> $rarr[leader] AND side = 0" );
				while( $arr2 = f_MFetch( $res2 ) )
					printf( "\tmbet( '$arr2[0]' );\n" );
				printf( "\tmmid( $rarr[bet_id] );\n" );
				$res2 = f_MQuery( "SELECT characters.login FROM characters, player_bets WHERE bet_id = $rarr[bet_id] AND characters.player_id = player_bets.player_id AND side = 1" );
				while( $arr2 = f_MFetch( $res2 ) )
					printf( "\tmbet( '$arr2[0]' );\n" );
				printf( "\tmfin( $rarr[bet_id] );\n" );
			}
			printf( "</script>" );
		}
		else if( $type == 3 )
		{
			printf( "<script>\n" );
			while( $rarr = f_MFetch( $rres ) )
			{
				$res1 = f_MQuery( "SELECT login FROM characters WHERE player_id = $rarr[leader]" );
				$arr1 = f_MFetch( $res1 );
				printf( "\tcstart( '$arr1[0]' );\n" );
				printf( "\tcbet( '<u>$arr1[0]</u>' );\n" );
				$res2 = f_MQuery( "SELECT characters.login FROM characters, player_bets WHERE bet_id = $rarr[bet_id] AND characters.player_id = player_bets.player_id AND player_bets.player_id <> $rarr[leader]" );
				while( $arr2 = f_MFetch( $res2 ) )
					printf( "\tcbet( '$arr2[0]' );\n" );
				printf( "\tcfin( $rarr[bet_id] );\n" );
			}
			printf( "</script>" );
		}
	}
	
	$upload = true;
}
else
{
	if( $a == 10 )
	{
		$res = f_MQuery( "SELECT * FROM combats WHERE place={$player->depth} AND location={$player->location}" );
		if( !f_MNum( $res ) ) print( "<i>Сейчас на этой арене не идет ни одного боя</i>" );
		else
		{
			print( "<table border=1 bordercolor=gray>" );
			while( $arr = f_MFetch( $res ) )
			{
				print( "<tr><td align=center>" );
				
				$res2 = f_MQuery( "SELECT login FROM characters, combat_players WHERE characters.player_id = combat_players.player_id AND combat_players.combat_id = $arr[0] AND combat_players.side=0 AND combat_players.ready < 2" );
				$ok = false;
				while( $arr2 = f_MFetch( $res2 ) )
				{
					if( $ok ) print( ", " );
					$ok = true;
					print( "<b>$arr2[0]</b>" );
				}
				
				print( "</td><td>VS</td><td align=center>" );
				
				$res2 = f_MQuery( "SELECT login FROM characters, combat_players WHERE characters.player_id = combat_players.player_id AND combat_players.combat_id = $arr[0] AND combat_players.side=1 AND combat_players.ready < 2" );
				$ok = false;
				while( $arr2 = f_MFetch( $res2 ) )
				{
					if( $ok ) print( ", " );
					$ok = true;
					print( "<b>$arr2[0]</b>" );
				}
				
				print( "</td><td><a href=combat_log.php?id=$arr[combat_id] target=_blank>Смотреть бой</a></td></tr>" );
			}
			print( "</table>" );
		}
	}

	if( $a == 11 )
	{
		$nick = trim( HtmlSpecialChars( $_GET['nick'] ) );
		print( "Укажите имя персонажа, бои которого вас интересуют: <input class=m_btn type=text id=nick name=nick><button class=sss_btn onClick='ar(\"11&nick=\"+document.getElementById( \"nick\" ).value)'>OK</button><hr>" );
		if( $nick )
		{
			print( "Последние бои персонажа <b>$nick</b>:<br>" );
			$res = f_MQuery( "SELECT * FROM history_combats, characters WHERE characters.login='$nick' AND characters.player_id=history_combats.player_id ORDER BY entry_id DESC limit 20" );
			if( !f_MNum( $res ) ) print( "<i>Похоже, этот персонаж не участвовал ни в одном сражении в последнее время.</i>" );
			else
			{
				print( "<table border=1 bordercolor=gray>" );
				while( $arr = f_MFetch( $res ) )
				{
					print( "<tr><td>" );
					
					$res2 = f_MQuery( "SELECT login FROM characters WHERE player_id = $arr[player_id]" );
					$arr2 = f_MFetch( $res2 );
					if( !$arr2 ) $moo = "Неизвестный персонаж";
					else $moo = $arr2[0];
					
					print( "$moo {$arr[str]}" );
					
					print( "</td><td><a href=combat_log.php?id=$arr[combat_id] target=_blank>Смотреть бой</a></td></tr>" );
				}
				print( "</table>" );
			}
		}
	}
	$upload = true;
}

print( "</div>" );

if( $upload )
{
	print( "<script>parent.setType( $a );</script>" );
	print( "<script>parent.document.getElementById( 'arena_body' ).innerHTML = document.getElementById( 'moo' ).innerHTML;</script>" );
	if( $a < 10 ) print( "<script>setTimeout( 'location.href=\"arena_ref.php?a=$a\";', 15000 );</script>" );
}

?>
