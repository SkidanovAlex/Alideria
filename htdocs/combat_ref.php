<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "/*Неверные настройки Cookie*/" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

$res = f_MQuery( "SELECT combat_id, side, ready, opponent_id, lcard, card_id, target, note_id FROM combat_players WHERE player_id={$player->player_id}" );
$arr = f_MFetch( $res );

if( !$arr )
{
	if( $player->regime == 100 ) $player->SetRegime( 0 );
	die( "parent.location.href='game.php';" );
}

if( $HTTP_RAW_POST_DATA == 'ref' )
	f_MQuery( "UPDATE characters SET refs=refs+1 WHERE player_id={$player->player_id}" );

$tm = time( );
f_MQuery( "UPDATE online SET last_ping = $tm WHERE player_id = {$player->player_id}" );

$combat_id = $arr[0];
$side = $arr[1];
$ready = $arr[2];
$opp_id = $arr[3];
$lcard = $arr[4];
$card_id = $arr[5];
$cr_target = $arr[6];
$my_note_id = $arr[7];

$cres = f_MQuery( "SELECT last_turn_made, timeout, cur_turn FROM combats WHERE combat_id=$combat_id" );
$carr = f_MFetch( $cres );
$ltm = $carr[0];
$rtmo = $carr[1];
$cur_turn = $carr[2];
$tm = time( );

$ok = 0;

// AI begin

if( $tm - $ltm > 8 ) // время на ход моба
{
	$res = f_MQuery( "SELECT * FROM combat_players WHERE combat_id = $combat_id AND ai = 1 AND ready = 0" );
	
	if( mysql_num_rows( $res ) )
	{
		include_once( 'combat_ai.php' );
		while( $arr = f_MFetch( $res ) )
		{
			processAI2( $arr['player_id'], $arr['row'] );
		}
	   	$ok = true;

    	// надо заново потырить всю инфу - processAI мог завершить ход
    	$cres = f_MQuery( "SELECT last_turn_made, timeout, cur_turn FROM combats WHERE combat_id=$combat_id" );
    	$carr = f_MFetch( $cres );
    	$ltm = $carr[0];
    	$rtmo = $carr[1];
    	$cur_turn = $carr[2];
    	$tm = time( );

    	$res = f_MQuery( "SELECT combat_id, side, ready, opponent_id, lcard, card_id, target, note_id FROM combat_players WHERE player_id={$player->player_id}" );
    	$arr = f_MFetch( $res );
    	$ready = $arr[2];
		$opp_id = $arr[3];
		$lcard = $arr[4];
		$card_id = $arr[5];
		$cr_target = $arr[6];
		$my_note_id = $arr[7];
	}
	
	if (($tm - $ltm) > $rtmo) // проверка на время таймаута
	{
		f_MQuery("LOCK TABLE combat_players WRITE");
		$fm = (int)f_MValue("SELECT count(player_id) FROM combat_players WHERE autoforce=1 AND ready < 2 AND combat_id=".$combat_id);
		if ($fm != 0)
		{
			f_MQuery( "UPDATE combat_players SET forces=forces+ 1, ready=1, card_id=-1 WHERE combat_id = $combat_id AND ready = 0" );
			//f_MQuery("UPDATE combat_players SET ready = 1 WHERE ready=0 AND combat_id=".$combat_id);
			include_once( 'combat_functions.php' );
			CheckTurnOver( $combat_id, 1, 'Ход форсирован автоматически<br>', true );
			f_MQuery("INSERT INTO autoforce_check (player_id, number) VALUES (".$player->player_id.", ".time().")");
		}
		else
			f_MQuery("UNLOCK TABLES");
	}

}

// AI end

$show_full_log_button = false;
$reset_log = false;

if( isset( $HTTP_GET_VARS['compact'] ) )
{
	$res = f_MQuery( "SELECT id FROM combat_log WHERE combat_id=$combat_id ORDER BY id DESC LIMIT 13,1" );
	$arr = f_MFetch( $res );
	if( !$arr ) $last_note = 0;
	else
	{
		$last_note = $arr[0];
		$show_full_log_button = true;
	}
}
else if( isset( $HTTP_GET_VARS['full'] ) )
{
	$last_note = 0;
	$reset_log = true;
}
else if( !isset( $HTTP_COOKIE_VARS['last_note'] ) )
{
	$last_note = 0;
}
else $last_note = $HTTP_COOKIE_VARS['last_note'];
settype( $last_note, "integer" );

$res = f_MQuery( "SELECT * FROM combat_log WHERE combat_id=$combat_id AND id > $last_note ORDER BY id" );

$arr = mysql_fetch_array( f_MQuery( "SELECT max( id ) FROM combat_log WHERE combat_id=$combat_id" ) );
$last_note = $arr[0];
setcookie( "last_note", $last_note );

echo "cur_turn = $cur_turn;";

if( $show_full_log_button )
{
	print( "addtolog( 0, \"<a onclick='query(\\\"combat_ref.php?full\\\",\\\"ref\\\");' style='cursor: pointer'>Загрузить весь лог</a>\" );" );
	$ok = 1;
}

if( $reset_log )
{
	print( "qn = 0;" );
	print( "pg = 0;" );
	echo "lid = 0;";
	echo "shown = new Array( );";
	$ok = 1;
}

while( $arr = f_MFetch( $res ) )
{
	if( $arr['string'][0] == ',' ) $arr['string'] = substr( $arr['string'], 1 );
	else $arr['string'] = "[0,\"".$arr['string']."\"]";
	print( "addtolog( $arr[id], [{$arr['string']}] );" );
	$ok = 1;
}

if( $opp_id )
{
	$plr = new Player( $opp_id );
	if( $plr->player_id == 0 ) $opp_id = 0;
}


print( "select_card_ref( $card_id );" );
print( "select_target_ref( $cr_target );" );

if( $ok || $HTTP_RAW_POST_DATA == 'rdy' )
{
	f_MQuery( "LOCK TABLE combat_animation WRITE" );
	$ares = f_MQuery( "SELECT scenario FROM combat_animation WHERE player_id={$player->player_id}" );
	$aarr = f_MFetch( $ares );
	if( $aarr ) f_MQuery( "DELETE FROM combat_animation WHERE player_id={$player->player_id}" );
	f_MQuery( "UNLOCK TABLES" );

	if( $aarr )
	{
    	$rcard = -1;
    	if( $opp_id )
    	{
    		$qarr = f_MFetch( f_MQuery( "SELECT lcard FROM combat_players WHERE player_id=$opp_id" ) );
    		$rcard = $qarr[0];
    	}
    	if( $lcard && $lcard != -1 )
    	{
    		$qarr = f_MFetch( f_MQuery( "SELECT image_large FROM cards WHERE card_id=$lcard" ) );
    		$lcard = "'".$qarr[0]."'";
    	} else $lcard = -1;
    	if( $rcard && $rcard != -1 )
    	{
    		$qarr = f_MFetch( f_MQuery( "SELECT image_large FROM cards WHERE card_id=$rcard" ) );
    		$rcard = "'".$qarr[0]."'";
    	} else $rcard = -1;
    	echo "ref_que.push( [1,$lcard,$rcard,$aarr[0]] );";
    }


	print( "reflog( );" );
	//print( "function ge( a ) { return document.getElementById( a ); }" );

/*	
	$res = f_MQuery( "SELECT * FROM combat_players WHERE combat_id=$combat_id AND side=$side AND player_id <> {$player->player_id} AND ready < 2" );

	print( "ge( 'my_side' ).innerHTML = " );
	$player->ARect( $ready );
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr['player_id'] );
		print( "+" );
		$plr->ARect( $arr['ready'] );
	}
	print( ";\n" );
*/
	
/*	$enemy = 1 - $side;
	$res = f_MQuery( "SELECT * FROM combat_players WHERE combat_id=$combat_id AND side=$enemy AND ready < 2" );
	print( "ge( 'his_side' ).innerHTML = ''" );
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr['player_id'] );
		echo "+";
		$plr->ARect( $arr['ready'] );
	}
	print( ";\n" );*/

	if( $my_note_id )
	{
		$res = f_MQuery( "SELECT data FROM combat_ajax_data WHERE note_id=$my_note_id" );
		$arr = f_MFetch( $res );
		$v1 = f_MValue( "SELECT val FROM combat_turn_desc WHERE player_id={$player->player_id}" );
		if( !$v1 ) $v1 = "3,[]";
		$opp2 = f_MValue( "SELECT opponent_id_2 FROM combat_players WHERE player_id={$player->player_id}" );
		$opp2 = (int)$opp2;
		$v2 = f_MValue( "SELECT val FROM combat_turn_desc WHERE player_id={$opp2}" );
		if( !$v2 ) $v2 = "3,[]";
		$opp_login = f_MValue( "SELECT login FROM characters WHERE player_id={$opp2}" );
//		LogError( "turn_desc( ['{$player->login}','{$opp_login}',$v1,$v2] )" );
		if( $opp2 == $opp_id ) echo "ref_que.push( [0,\"".addslashes("show_turn_details( '<center><small><b>".date("H:i:s",$ltm).", ход #".($cur_turn-1)."</b></small>' + turn_desc( ['{$player->login}','{$opp_login}',$v1,$v2] ) + '</center>' );" )."\"] );";
		else
		{
			echo "ref_que.push( [0,\"".addslashes("show_turn_details( c_log( [$arr[0]] ) );" )."\"] );";
//			$v2[0] = '3';
//			echo "ref_que.push( [0,\"".addslashes("show_turn_details( '<center><small><b>".date("H:i:s",$ltm).", ход #{$cur_turn}</b></small>' + turn_desc( ['{$player->login}','{$opp_login}',$v1,$v2] ) + '</center>' );" )."\"] );";
		}
//		if( $arr ) echo "ref_que.push( [0,\"".addslashes("show_turn_details( c_log( [$arr[0]] ) );" )."\"] );";
	}

//	echo "ref_que.push( [0,\"";
    
	print( "reset_creatures( );" );
	$res = f_MQuery( "SELECT * FROM combat_creatures WHERE player_id={$player->player_id}" );
	while( $arr = f_MFetch( $res ) )
	{
		$creature = new Creature( $arr['creature_id'] );
		$creature->attack = $arr['attack'];
		$creature->defence = $arr['defence'];
		$slot = $arr['slot_id'] + 1;
		print( "_( 'mycreat$slot' ).innerHTML = ".$creature->Stats( ).";" );
	}
	settype( $opp_id, 'integer' );
	$res = f_MQuery( "SELECT * FROM combat_creatures WHERE player_id=$opp_id" );
	while( $arr = f_MFetch( $res ) )
	{
		$creature = new Creature( $arr['creature_id'] );
		$creature->attack = $arr['attack'];
		$creature->defence = $arr['defence'];
		$slot = $arr['slot_id'] + 1;
		print( "_( 'hiscreat$slot' ).innerHTML = ".$creature->Stats( ).";" );
	}

	if( $opp_id )
		echo "load_opp( '<a href=player_info.php?id={$plr->player_id} target=_blank><b>{$plr->login}</b></a>', '".$plr->getAvatar( )."' );";
//		echo "load_opp( ".$plr->Nick2( ).", '".$plr->getAvatar( )."' );";
	else
		echo "load_opp( '<i>Нет оппонента</i>', 'empty.gif' );";

//	echo "\"];";
}

// вещи

if( $HTTP_RAW_POST_DATA == 'take' || $ok || true )
{
	$res = f_MQuery( "SELECT items.image, items.image_large, items.name, combat_loot.number, combat_loot.entry_id FROM combat_loot, items WHERE combat_id=$combat_id AND combat_loot.item_id=items.item_id ORDER BY player_id<>{$player->player_id}, combat_loot.entry_id LIMIT 5" );
	$st = '';
	if( f_MNum( $res ) ) $st = "<img src='images/noob/right.gif'>";
	while( $arr = f_MFetch( $res ) )
	{
		$img = $arr['image_large'];
		if( !$img ) $img = $arr['image'];
		$st .= "<img title='[$arr[number]] $arr[name] (Забрать)' width=50 height=50 src=images/items/$img style='cursor:pointer;' onclick='take( $arr[entry_id] )'>";
	}
	echo "_( 'items' ).innerHTML = \"$st\";";
}

// вещи - конец


echo "ref_que.push( [0,\"";



$enemy = 1 - $side;
$opp_id = (int)$opp_id;
$res = f_MQuery( "SELECT player_id, ready FROM combat_players WHERE combat_id=$combat_id AND side=$enemy AND ( ready < 2 OR player_id=$opp_id ) ORDER BY player_id<>$opp_id, entry_id" );
$opp_rdy = 0;
$num = f_MNum( $res );

if( !$opp_id || $num > 1 || $num == 1 && $plr->GetAttr( 1 ) <= 0 )
{
	echo "_( 'his_side' ).innerHTML = ''";
	while( $arr = f_MFetch( $res ) )
	{
		if( $arr['player_id'] == $opp_id ) $opp_rdy = (int)$arr[1];
		$plr = new Player( $arr[0] );
		echo "+";
		$plr->BRect( (int)$arr[1], 1, ($arr['player_id'] == $opp_id)?1:-1 );
	}
	echo ";hideava(1);";
}
else if( $opp_id )
{
	$qarr = f_MFetch( f_MQuery( "SELECT ready FROM combat_players WHERE player_id=$opp_id" ) );
	echo "_( 'his_side' ).innerHTML = ";
	$plr->BRect( (int)$qarr[0], 0, 1 );
	$opp_rdy = (int)$qarr[0];
	echo ";health(".$plr->GetAttr(1).",".$plr->GetAttr(101).",1);";
}




$res = f_MQuery( "SELECT player_id, ready FROM combat_players WHERE combat_id=$combat_id AND side=$side AND player_id <> {$player->player_id} AND ready < 2 ORDER BY entry_id" );
if( f_MNum( $res ) )
{
	print( "_( 'my_side' ).innerHTML = " );
	$player->BRect( $ready, 1, 1 );
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr[0] );
		echo " + ";
		$plr->BRect( (int)$arr[1], 1, -1 );
	}
	echo ";hideava(0);";

}
else
{
	print( "_( 'my_side' ).innerHTML = " );
	$player->BRect( $ready, 0, 1 );
	echo ";health(".$player->GetAttr(1).",".$player->GetAttr(101).",0);";
}

echo "\"] );";
if( $opp_id ) echo "opp_rdy( $opp_rdy );";

$res = f_MQuery( "SELECT ready FROM combat_players WHERE combat_id=$combat_id AND player_id={$player->player_id}" );
$arr = f_MFetch( $res );
if( $arr['ready'] == 2 )
{
	print( "_( 'leave' ).innerHTML = '<br><center><font color=darkred>Вы проиграли в этом бою. </font><br><br><table cellspacing=0 cellpadding=0 border=0><tr><td><img src=images/top/b.png></td><td><button class=n_btn id=rdy_td style=\\'cursor: pointer\\' onClick=\\'location.href=\"leave_combat.php\";\\'>Покинуть бой</button></td><td><img src=images/top/c.png></td></tr></table></center><br>';" );
	print( "_( 'leave' ).style.display = '';" );
	print( "_( 'txttmo' ).style.display = 'none';" );
}
else if( $arr['ready'] == 3 )
{
	$noob = false;
	if( $player->level == 1 )
	{
   		// new noob
        $res = f_MQuery( "SELECT a, b FROM noob WHERE player_id={$player->player_id}" );
        $arr = f_MFetch( $res );
        if( $arr ) { $noob = $arr[0]; $noob_param = $arr[1]; }
	}
	
	if( !$noob ) print( "_( 'leave' ).innerHTML = '<br><center><font color=blue>Вы победили в этом бою. </font><br><br><table cellspacing=0 cellpadding=0 border=0><tr><td><img src=images/top/b.png></td><td><button class=n_btn id=rdy_td style=\\'cursor: pointer\\' onClick=\\'location.href=\"leave_combat.php\";\\'>Покинуть бой</button></td><td><img src=images/top/c.png></td></tr></table></center><br>';" );
	else print( "_( 'leave' ).innerHTML = '<br><center><font color=blue>Вы победили в этом бою. </font><br><br><table cellspacing=0 cellpadding=0 border=0><tr><td><img src=images/top/b.png></td><td><button class=n_btn id=rdy_td style=\\'cursor: pointer\\' onClick=\\'alert(\"Прежде чем покинуть бой, получи последние инструкции от Астаниэль.\");\\'>Покинуть бой</button></td><td><img src=images/top/c.png></td></tr></table></center><br>';" );
	print( "_( 'leave' ).style.display = '';" );
	print( "_( 'txttmo' ).style.display = 'none';" );
}
else // если еще в бою
{
	//print( "_( 'leave' ).style.display = 'none';" ); -- по-моему нет смысла, до конца боя дивка спрятана и так
	

	// TO begin

	if( $tm - $ltm > $rtmo )
	{
		if( $arr['ready'] == 1 )	
			print( "_( 'txttmo' ).style.display = '';" );
	}
	else
	{
		print( "_( 'txttmo' ).style.display = 'none';" );
		$dtm = time( ) - $ltm - $rtmo + 120;
		$dtm *= 1000;
		print( "var d0=new Date( );" );
		print( "tm = d0.getTime( ) - $dtm;" );
		print( "PingTimer( );" );
	}

	// TO end
} // если еще в бою END

?>
