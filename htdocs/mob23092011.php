<?

include_once( "functions.php" );
include_once( "location_functions.php" );

class Mob
{
	var $mob_id;
	var $name;
	var $player_id;
	var $combat_id;
	var $level;

	function CreateMirrorMob( $id, $loc, $depth, $name, $avatar = false )
	{
		$lres = f_MQuery( "SELECT level FROM characters WHERE player_id=$id" );
		$larr = f_MFetch( $lres );

		$this->mob_id = -1;
		$this->name = $name;
		$this->level = $larr[0];

		f_MQuery( "INSERT INTO characters ( login, pswrddmd5, level, loc, depth, nick_clr, text_clr ) VALUES ( '{$this->name}', -1, {$this->level}, $loc, $depth, '000000', '000000' )" );
		$this->player_id = mysql_insert_id( );

		f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( {$this->player_id}, 56, 10 )" );
		f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( {$this->player_id}, 57, 10 )" );
		f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( {$this->player_id}, 58, 10 )" );

		$ares = f_MQuery( "SELECT * FROM player_attributes WHERE player_id=$id" );
		while( $aarr = f_MFetch( $ares ) )
			f_MQuery( "INSERT INTO player_attributes ( player_id, attribute_id, value, real_value, actual_value ) VALUES ( {$this->player_id}, $aarr[attribute_id], $aarr[value], $aarr[value], $aarr[value] )" );
			
		// Если передаём какой-то особый аватар, добавляем его
		if( $avatar !== false )
		{
			f_MQuery( 'INSERT INTO player_avatars( player_id, avatar ) VALUES( '.$this->player_id.', "'.$avatar.'" )' );		
		}
	}

	function CreateDungeonMob( $level, $power, $wp, $np, $fp, $loc, $depth, $name, $avatar = false, $mob_id = -1 )
	{
		$this->mob_id = $mob_id;
		$this->name = $name;
		$this->level = $level;

		f_MQuery( "INSERT INTO characters ( login, pswrddmd5, level, loc, depth, nick_clr, text_clr ) VALUES ( '{$this->name}', $mob_id, {$this->level}, $loc, $depth, '000000', '000000' )" );
		$this->player_id = mysql_insert_id( );
		if( $avatar ) f_MQuery( "INSERT INTO player_avatars ( player_id, avatar ) VALUES ( {$this->player_id}, '$avatar' )" );

		$attrs[30] = $wp * $level;
		$attrs[40] = $np * $level;
		$attrs[50] = $fp * $level;
		
		$attrs[1] = $level * 50 + (int)( $level * 100 * $power / 15 ) + $level * mt_rand( - 5, 5 );

		$coefs = array( 100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100, 100 );
		for( $i = 0; $i < 100; ++ $i )
		{
			$id1 = mt_rand( 0, 11 );
			$id2 = mt_rand( 0, 11 );
			if( $id1 == $id2 ) continue;
			$val = mt_rand( 0, $coefs[$id1] );
			$coefs[$id1] -= $val;
			$coefs[$id2] += $val;
		}

		$attrs[131] = $attrs[30] + (int)( mt_rand( (int)( $level * $power / 4.0 ), (int)( $level * $power ) ) * $coefs[0] / 100.0 );
		$attrs[132] = $attrs[30] + (int)( mt_rand( (int)( $level * $power / 3.0 ), (int)( $level * $power * 2 / 3.0 ) ) * $coefs[1] / 100.0 );
		$attrs[141] = $attrs[40] + (int)( mt_rand( (int)( $level * $power / 4.0 ), (int)( $level * $power ) ) * $coefs[2] / 100.0 );
		$attrs[142] = $attrs[40] + (int)( mt_rand( (int)( $level * $power / 3.0 ), (int)( $level * $power * 2 / 3.0 ) ) * $coefs[3] / 100.0 );
		$attrs[151] = $attrs[50] + (int)( mt_rand( (int)( $level * $power / 4.0 ), (int)( $level * $power ) ) * $coefs[4] / 100.0 );
		$attrs[152] = $attrs[50] + (int)( mt_rand( (int)( $level * $power / 3.0 ), (int)( $level * $power * 2 / 3.0 ) ) * $coefs[5] / 100.0 );

		if( mt_rand( 1, 2 ) == 1 ) $attrs[13] = (int)($level * $power / 3.0);
		else $attrs[1] += (int)( $level * $power * 3 );

		$attrs[15] = mt_rand( 0, (int)($level * $power / 4.0) );
		$attrs[16] = mt_rand( 0, (int)($level * $power / 4.0) );

		if( $power >= 10 ) $attrs[222] = mt_rand( 1, (int)( $power / 5 ) );

		$attrs[101] = $attrs[1];

		$spells = array( 56, 57, 58 );
		if( $power >= 12 && $level >= 7 && 1 == mt_rand( 1, 2 ) ) $spells[] = 131;
		if( $power >= 6 && $level <= 12 && $level >= 7 ) $spells[] = 130;
//		if( $power >= 11 ) $spells[] = 145;
		if( $power >= 6 && $level <= 10 && $level >= 7 ) $spells[] = 132;

		if( $level >= 5 && $power >= 2 ) { $spells[] = 223; $spells[] = 225; $spells[] = 222; }
		if( $level >= 10 && $power >= 3 ) { $spells[] = 224; $spells[] = 286; }
		if( $power >= 5 ) $spells[] = 129;

		$res = f_MQuery( "SELECT card_id, parent FROM cards WHERE status=0 AND multy=0 AND card_id > 58 AND ( genre=0 AND ( level )*(mk+2) <= {$attrs[30]}*2 OR genre=1 AND ( level )*(mk+2) <= {$attrs[40]}*2 OR genre=2 AND ( level )*(mk+2) <= {$attrs[50]}*2 ) AND level<=$level ORDER BY (level)*(2+mk) DESC" );
		$gotten = array( );
		while( $arr = f_MFetch( $res ) )
		{
			if( $arr[0] == 302 || $arr[1] == 302 ) continue;
			if( $arr[0] == 321 || $arr[1] == 321 ) continue;
			if( $gotten[$arr[0]] || $gotten[$arr[1]] ) continue;
			if( mt_rand( 1, 2 ) == 1 )
			{
				$spells[] = $arr['card_id'];
				$gotten[$arr[0]] = 1;
				$gotten[$arr[1]] = 1;
			}
		}

		for( $i = 3; $i < count( $spells ); ++ $i )
		{
			$j = mt_rand( $i, count( $spells ) - 1 );
			$t = $spells[$i];
			$spells[$i] = $spells[$j];
			$spells[$j] = $t;
		}

		for( $i = 0; $i < count( $spells ) && $i < 8; ++ $i )
			f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( {$this->player_id}, {$spells[$i]}, 10 )" );
			
		foreach( $attrs as $id=>$value )
			f_MQuery( "INSERT INTO player_attributes ( player_id, attribute_id, value, real_value, actual_value ) VALUES ( {$this->player_id}, $id, $value, $value, $value )" );
	}
	
	function CreateMob( $a, $loc, $depth )
	{
		$res = f_MQuery( "SELECT * FROM mobs WHERE mob_id = $a" );
		$cres = f_MQuery( "SELECT * FROM mob_cards WHERE mob_id = $a" );
		$ares = f_MQuery( "SELECT * FROM mob_attributes WHERE mob_id = $a" );

		$this->mob_id = $a;
		
		$arr = f_MFetch( $res );
		$this->name = $arr['name'];
		$this->level = $arr['level'];
		f_MQuery( "INSERT INTO characters ( login, pswrddmd5, level, loc, depth, nick_clr, text_clr ) VALUES ( '$arr[name]', $a, $arr[level], $loc, $depth, '000000', '000000' )" );
		$this->player_id = mysql_insert_id( );
		f_MQuery( "INSERT INTO player_profile ( player_id, descr ) VALUES ( {$this->player_id}, '$arr[descr]' )" );
		if( $arr['avatar'] )f_MQuery( "INSERT INTO player_avatars ( player_id, avatar ) VALUES ( {$this->player_id}, '$arr[avatar]' )" );
		
		while( $carr = f_MFetch( $cres ) )
			f_MQuery( "INSERT INTO player_cards ( player_id, card_id, number ) VALUES ( {$this->player_id}, $carr[card_id], 10 )" );
			
		while( $aarr = f_MFetch( $ares ) )
		{
			f_MQuery( "INSERT INTO player_attributes ( player_id, attribute_id, value, real_value, actual_value ) VALUES ( {$this->player_id}, $aarr[attribute_id], $aarr[value], $aarr[value], $aarr[value] )" );
			if( $aarr[attribute_id] == 1 )
				f_MQuery( "INSERT INTO player_attributes ( player_id, attribute_id, value, real_value, actual_value ) VALUES ( {$this->player_id}, 101, $aarr[value], $aarr[value], $aarr[value] )" );
		}
	}
	
	function AttackPlayer( $a, $win_action = 0, $win_action_param = 0, $real_death = true, $autoforce = false )
	{
		global $player;

		if ($autoforce == true) $af = 1;
		else $af = 0;		
		include_once( 'create_combat.php' );
		
		$this->combat_id = ccAttackPlayer( $this->player_id, $player->player_id, true, $real_death, false );
		
		f_MQuery( "UPDATE combat_players SET mob_id={$this->mob_id}, win_action = $win_action, win_action_param = $win_action_param WHERE combat_id = $this->combat_id AND player_id IN ( {$this->player_id}, {$player->player_id} )" );
		f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( {$this->combat_id}, '<b>{$this->name}</b> атакует персонажа <b>{$player->login}</b><br>' )" );
		f_MQuery( "UPDATE characters SET regime = 100 WHERE player_id = {$player->player_id} OR player_id = {$this->player_id}" );
		f_MQuery( "UPDATE combats SET type=1 WHERE combat_id={$this->combat_id}" );
		f_MQuery("UPDATE combat_players SET autoforce = ".$af." WHERE player_id = {$player->player_id}");
	}
};

// returns string to add to combat_log
function mobDrop( $mob_id, $loc, $depth )
{
	if( $loc == 0 && $depth == 33 ) // labirinth of nightmares
	{
		return '';
	}
	else
	{
		$ret = '';
		$res = f_MQuery( "SELECT items.*, mob_items.number, mob_items.chance FROM items INNER JOIN mob_items ON items.item_id=mob_items.item_id WHERE mob_id=$mob_id" );
		while( $arr = f_MFetch( $res ) )
		{
			if( mt_rand( 0, 9999 ) < $arr['chance'] )
			{
				if( $arr['name13'] == '' )  $arr['name13']  = $arr['name'];
				if( $arr['name2_m'] == '' )  $arr['name2_m']  = $arr['name'];
				$num = mt_rand( 1, $arr['number'] );
				$ret .= ", <a href=help.php?id=1010&item_id=$arr[item_id] target=_blank>".my_word_form( $num, $arr['name'], $arr['name13'], $arr['name2_m'] )."</a>";
				LocationAddItems( $loc, $depth, $arr['item_id'], $num );
			}
		}
		if( $ret != '' ) return "<b>На землю падает:</b> ".substr( $ret, 2 )."<br>";
		return $ret;
	}
}

// returns string to add to combat_log
function mobDrop2( $mob_id, $loc, $depth, $combat_id, $player_id = 0, $login = '', $premium = false )
{
	$ret = '';
	$res = f_MQuery( "SELECT items.*, mob_items.number, mob_items.chance FROM items INNER JOIN mob_items ON items.item_id=mob_items.item_id WHERE mob_id=$mob_id" );
	$player_id = (int)$player_id;
	while( $arr = f_MFetch( $res ) )
	{
		if( mt_rand( 0, 9999 ) < $arr['chance'] )
		{
			if( $arr['name13'] == '' )  $arr['name13']  = $arr['name'];
			if( $arr['name2_m'] == '' )  $arr['name2_m']  = $arr['name'];
			$num = mt_rand( 1, $arr['number'] );
			if( $premium && mt_rand( 0, 99 ) < 50 ) $num = $num * 2;
			f_MQuery( "INSERT INTO combat_loot( combat_id, item_id, number, player_id, expires ) VALUES ( $combat_id, $arr[item_id], $num, $player_id, ".(time()+15)." )" );
			$eid = mysql_insert_id( );
			$ret .= ", <a href=help.php?id=1010&item_id=$arr[item_id] target=_blank>".my_word_form( $num, $arr['name'], $arr['name13'], $arr['name2_m'] )."</a>\" + tstr( $eid ) + \"";
		}
	}
	$adds = '';
	if( $player_id ) $adds .= " перед игроком <b>$login</b>";
	if( $ret != '' ) return "<b>На землю$adds падает:</b> ".substr( $ret, 2 )."<br>";
	return $ret;
}

?>
