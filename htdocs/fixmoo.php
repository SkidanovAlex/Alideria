<?

include( 'functions.php' );
include( 'player.php' ) ;

f_MConnect( );

glashSay( "Сыщик ишет помощников для выполнения нескольких заданий в Лабиринте Кошмаров. <a href=/forum.php?thread=8438&page=0&f=0 target=_blank>Подробнее...</a>" );

die();

include( "player.php" );

f_MConnect( );

$res = f_MQuery( "SELECT player_id FROM player_triggers WHERE trigger_id = 224" );
while( $arr = f_MFetch( $res ) )
{
	$num = f_MNum( f_MQuery( "SELECT * FROM player_weddings WHERE p0=$arr[0] OR p1=$arr[0]" ) );
	if( !$num )
	{
		$login = f_MValue( "SELECT login FROM characters WHERE player_id=$arr[0]" );
//		f_MQuery( "DELETE FROM player_triggers WHERE player_id=$arr[0] AND trigger_id >= 220 AND trigger_id <= 225" );
		echo $login;
	}
}


die( );


// price of wonder
include( "clan_wonders.php" );
include( "functions.php" );

f_MConnect( );

$id = 1;
$cur = 0;
$ans = 0;
foreach( $wonder_res as $arr )
{
	$cur = 0;
	echo "<b>$id</b><br>"; ++ $id;
	foreach( $arr as $item_id => $num )
	{
		echo f_MValue( "SELECT name FROM items WHERE item_id=$item_id" ).": $num<br>";
		$cur += $num * f_MValue( "SELECT price FROM items WHERE item_id=$item_id" );
		if( !$item_id ) $cur += $num;
	}
	echo "$cur<hr>";
	$ans += $cur;
}

echo $ans;

die( );
include( "player.php" );
f_MConnect( );

function pr_award($act)
{
	global $player;
	f_MQuery( "LOCK TABLE premiums WRITE" );
	$res = f_MQuery( "SELECT deadline FROM premiums WHERE player_id={$player->player_id} AND premium_id=$act" );
	$arr = f_MFetch( $res );
	$deadline = time( ) + 40 * 60 * 60;
	if( !$arr ) f_MQuery( "INSERT INTO premiums( player_id, premium_id, deadline ) VALUES ( {$player->player_id}, $act, $deadline )" );
	else if( $arr[0] < time( ) ) f_MQuery( "UPDATE premiums SET deadline=$deadline WHERE player_id={$player->player_id} AND premium_id=$act" ); 
	else f_MQuery( "UPDATE premiums SET deadline=deadline+7*24*60*60 WHERE player_id={$player->player_id} AND premium_id=$act" ); 
	f_MQuery( "UNLOCK TABLES" );
}

$res = f_MQuery( "SELECT player_id FROM characters WHERE length(pswrddmd5) > 10" );
while( $arr = f_MFetch( $res ) )
{
	$player = new Player( $arr[0] );
	pr_award( 0 );
	pr_award( 1 );
	pr_award( 2 );
	pr_award( 3 );
	pr_award( 4 );
}

die( );
$res = f_MQuery( "select * from player_log where type=21 and arg1=1000 and arg2=5" );
while( $arr = f_MFetch( $res ) )
{
	$plr = new Player( $arr['player_id'] );
	$plr->nick_clr = '000000';
	$plr->AddUMoney( 5 );
	f_MQuery( "UPDATE characters SET nick_clr='000000' WHERE player_id={$arr[0]}" );
	$plr->syst2( 'В связи с изменением системы покупки произвольного цвета ника вам возвращено пять талантов и установлен черный цвет. Приносим извинения за доставленные неудобства.' );
}

die( );

include( 'functions.php' );

f_MConnect( );

$res = f_MQuery( "SELECT i.*  FROM items as i INNER JOIN lake_items as l ON i.item_id=l.item_id" );
while( $arr = f_MFetch( $res ) )
{
	$id = 1100000000 + $arr['item_id'];
	echo "$id. N раз добыл ресурс $arr[name] (alideria.ru/images/items/{$arr[image]})\n";
}

die( );

$res = f_MQuery( "SELECT * FROM items WHERE type=25 ORDER BY effect" );
while( $arr = f_MFetch( $res ) )
{
	echo "\$fthrs[{$arr[effect]}] = array( '{$arr[name]}', '{$arr[image]}', '".addslashes($arr[descr])."' );\n";
}



die( );

include( 'functions.php' );
include( 'player.php' ) ;

f_MConnect( );

glashSay( "никогда :о(" );

function phrase_prolong_premium($act, $days = 7)
{
	global $player;
	f_MQuery( "LOCK TABLE premiums WRITE" );
	$res = f_MQuery( "SELECT deadline FROM premiums WHERE player_id={$player->player_id} AND premium_id=$act" );
	$arr = f_MFetch( $res );
	$deadline = time( ) + $days * 24 * 60 * 60;
	if( !$arr ) f_MQuery( "INSERT INTO premiums( player_id, premium_id, deadline ) VALUES ( {$player->player_id}, $act, $deadline )" );
	else if( $arr[0] < time( ) ) f_MQuery( "UPDATE premiums SET deadline=$deadline WHERE player_id={$player->player_id} AND premium_id=$act" ); 
	else f_MQuery( "UPDATE premiums SET deadline=deadline+{$days}*24*60*60 WHERE player_id={$player->player_id} AND premium_id=$act" ); 
	f_MQuery( "UNLOCK TABLES" );
}

$logins = array( "Locutius", "hasar80", "лев" );

foreach( $logins as $login )
{
	$player_id = f_MValue( "SELECT player_id FROM characters WHERE login='$login'" );
	if( !$player_id ) echo "$login - FAIL<br>";
	else
	{
		$player = new Player( $player_id );
		phrase_prolong_premium( 0, 2 );
		phrase_prolong_premium( 1, 2 );
	}
}

echo "Moo!";


die( );
// -- -------
// starting experimental tourney

include( 'functions.php' );
include( 'player.php' );
include( 'tournament_order_functions.php' );
include( 'mob.php' );

f_MConnect( );

StartGroupTournament( 122 );

die( );

// --------------------------------
// Create mobs for order tourney

include( 'functions.php' );
include( 'player.php' );
include( 'mob.php' );

f_MConnect( );

for( $i = 0; $i < 5; ++ $i )
{
	$pids = array( );
	$count = 0;
    $a = "clan_id, tournament_id";
    $b = "1, 122";
	for( $j = 0; $j < 6; ++ $j )
	{
		if( $i == 0 && $j == 5 )
		{
			$pids[$j] = 0;
			continue;
		}
		++ $count;
    	$mob = new Mob;
    	$mob->CreateDungeonMob( 10, 2, 3, 3, 3, 2, 43, "OrderMob_{$i}_{$j}" );
    	$pids[$j] = $mob->player_id;
    	f_MQuery( "INSERT INTO player_clans ( player_id, clan_id ) VALUES ( {$pids[$j]}, 1 )" );
    	f_MQuery( "UPDATE characters SET clan_id=1 WHERE player_id={$pids[$j]}" );
    	$a .= ", slot_{$j}";
    	$b .= ", {$pids[$j]}";
    }
    $a .= ", count";
    $b .= ", $count";
    f_MQuery( "INSERT INTO tournament_group_bets ( $a ) VALUES ( $b )" );
}

die( );

// ---------------------
$sock = socket_create(AF_INET, SOCK_STREAM, 0);
socket_set_option( $sock, SOL_SOCKET, SO_REUSEADDR, 1 );
socket_connect($sock, "127.0.0.1", 1100);
socket_set_option( $sock, SO_REUSEADDR, 1 );
$msg = "check\n0\n\n";
socket_write( $sock, $msg, strlen($msg) ); 

	$val = socket_read( $sock, 100000, PHP_NORMAL_READ );
	settype( $val, 'integer' );

	$txt = '';
	for( $i = 0; $i < $val; $i += 512 )
	{
		$txt .= socket_read( $sock, min( $val - $i, 512 ), PHP_BINARY_READ );
	}
	echo $txt;

	socket_close( $sock );

die( );

include( 'functions.php' );
include( 'player.php' );
include( 'guild.php' );

/*f_MConnect( );

$res = f_MQuery( "SELECT content, receiver_id as player_id FROM post WHERE content LIKE 'Вы получили %'" );
echo f_MNum( $res );
while( $arr = f_MFetch(  $res) )
{
	$val = (int)substr( $arr[0], 12 );
	echo "$arr[player_id] $val<br>";
	f_MQuery( "UPDATE characters SET prof_exp=prof_exp + $val WHERE player_id=$arr[player_id]" );
	if( substr( $arr[0], 'химик' ) !== false )
	{
		$player = new Player( $arr['player_id'] );
		f_MQuery( "INSERT INTO post( sender_id, receiver_id, title, content, money, np, deadline ) VALUES ( 173, {$arr[player_id]}, 'Касательно сообщения про ПО', 'В результате ошибки администрации вместо гильдии стеклодувов был сброшен ранг всем членам гильдии алхимиков. ПО за Ранг было возвращено, пожалуйста, прокачайте его заново в зале гильдий. Приносим извинения за доставленные неудобства', '0', '0', '0' )" );
		$player->syst2( "У вас новое сообщение в дневнике" );
	}
}

die( );*/

// returning of rank PO

$rank_price_sum = array( 500 );
for( $i = 1; $i < 24; ++ $i )
	$rank_price_sum[$i] = $rank_price_sum[$i - 1] + $rank_prices[$i];

print_r($rank_price_sum);


$a = $rank_price_sum[9-1]-$rank_price_sum[1-1];
$b = $rank_price_sum[3-1]-$rank_price_sum[1-1];
print( $a + $b );

die( );

f_MConnect( );

$res = f_MQuery( "SELECT * FROM player_guilds" );
while( $arr = f_MFetch( $res ) )
{
	//if( $arr['player_id'] != 173 ) continue;
	if( $arr['guild_id'] == GLASSBLOWING_GUILD )
	{
		$player = new Player( $arr['player_id'] );
		f_MQuery( "UPDATE player_guilds SET rank=0, rating=0 WHERE player_id=$arr[player_id] AND guild_id=$arr[guild_id]" );
		$rt = $rank_price_sum[$arr['rank'] - 1] + $rank_price_sum[$arr['rating'] - 1];
		if( $rt )
		{
    		f_MQuery( "UPDATE characters SET prof_exp=prof_exp+$rt WHERE player_id=$arr[player_id]" );
    		f_MQuery( "INSERT INTO post( sender_id, receiver_id, title, content, money, np, deadline ) VALUES ( 173, {$arr[player_id]}, 'ПО за неверно прокаченный ранг и рейт в алхимиках', 'Вы получили $rt ПО за ошибочно распределенные ранг и рейтинг в гильдии алхимиков', '0', '0', '0' )" );
    		$player->syst2( "У вас новое сообщение в дневнике" );
		}
	}
}

die( );

// ----------------------------------------
// fixing recipes

include( 'functions.php' );
include( 'items.php' );
include( 'attrib_relations.php' );
f_MConnect( );

echo "Moo!";
$res = f_MQuery( "SELECT * FROM recipes" );
while( $arr = f_MFetch( $res ) )
{
	$id = $arr['result'];
	settype( $id, 'integer' );
	$iarr = f_MFetch( f_MQuery( "SELECT * FROM items WHERE item_id=$id" ) );
	$req = "";
	$a1 = ParseItemStr( $iarr['effect'] );
	$a2 = ParseItemStr( $iarr['req'] );
	$moo = Array( );
	foreach( $a1 as $a=>$b ) if( $a != 1 && $a != 101 )
	{
		foreach( $attrib_rels as $p=>$q )
		{
			$attr = -1;
			foreach( $q as $x ) if( $x == $a ) $attr = $p;
			if( $p == $a ) $attr = $p;
			if( $a == 33 || $a == 42 || $a == 51 ) echo "MOO!!!";
			if( $attr != -1 ) $moo[$attr] = max( $moo[$attr], $b );
		}
	}
	foreach( $a2 as $a=>$b ) if( $a != 1 && $a != 101 )
	{
		foreach( $attrib_rels as $p=>$q )
		{
			$attr = -1;
			foreach( $q as $x ) if( $x == $a ) $attr = $p;
			if( $p == $a ) $attr = $p;
			if( $attr != -1 ) $moo[$attr] = max( $moo[$attr], $b );
		}
	}
	$req = "";
	foreach( $moo as $a=>$b )
	{
		if( $req != "" ) $req .= ":";
		$b = $iarr['level'] * 2;
		$req .= "$a:$b";
	}
	if( $req != "" ) $req .= ".";
	$rank = 0;
	if( $iarr[level] > 3 )
	{
		if( strlen( $req ) == 0 ) ;
		else if( $req[strlen( $req ) - 1] == '.' ) $req[strlen( $req ) - 1] = ':';
		else $req = $req . ":";
		$req .= 10000 + $arr[prof];
		$req .= ":";
		$req .= ( $iarr['level'] - 3 );
		$rank = ( $iarr['level'] - 3 );
		$req .= ".";
	}
	if( $iarr[level] < 1 ) $iarr[level] = 1;
	if( $req == "." ) $req = "";
	f_MQuery( "UPDATE recipes SET req='$req',level=$iarr[level] WHERE recipe_id=$arr[recipe_id]" );
}

echo "Moo!";

die( );


?>
