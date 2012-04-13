<?php

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "card.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$mid_php = 1;	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

if( isset( $HTTP_GET_VARS['cast'] ) )
{
	global $player;

	$id = $HTTP_GET_VARS['cast'];
	settype( $id, 'integer' );
	$res = f_MQuery( "SELECT * FROM player_cards WHERE player_id = {$player->player_id} AND card_id = $id AND number >= 10" );
	if( !f_MNum( $res ) ) RaiseError( "Попытка добавить в список заклинание, которого нет в книге", $id );

	$res = f_MQuery( "SELECT genre, cost, cast_description FROM cards WHERE card_id=$id" );
	$arr = f_MFetch( $res );
	if( !$arr ) RaiseError( "Попытка добавить в список заклинание, которого не существует", $id );
	$cost = $arr['cost'];
	$flavor = $arr['cast_description'];
	if( $arr[0] == 3 )
	{
		$tm = time( );
		f_MQuery( "DELETE FROM player_cooldowns WHERE till < $tm" );
		if ($id == 109 && $player->location==0)
			die("alert('Вы не можете использовать это заклинание в этой локации');");
		$res = f_MQuery( "SELECT till FROM player_cooldowns WHERE player_id={$player->player_id} AND spell_id=$id" );
		$arr = f_MFetch( $res );
		if( $arr ) die( "alert( 'Вы сможете использовать это заклинание через ".my_time_str( ( $arr[0] - $tm ) )."' );" );
		if ($id==103 && f_MValue("SELECT COUNT(effect_id) FROM player_effects WHERE effect_id=9 AND player_id=".$player->player_id))
			$cost = $cost / 2;
		f_MQuery( "INSERT INTO player_cooldowns ( player_id, spell_id, till ) VALUES ( {$player->player_id}, $id, $tm + $cost * 60 )" );
		if( $id == 103 )
		{
			f_MQuery( "UPDATE characters SET real_deaths = real_deaths - 1 WHERE player_id={$player->player_id} AND real_deaths >= 1" );
			$moo = f_MFetch( f_MQuery( "SELECT real_deaths FROM characters WHERE player_id={$player->player_id}" ) );
			$moo[0] *= 30; $moo[0] += 30;
			$player->syst2( "$flavor. При следующем поражении вы проведете у лекаря $moo[0] сек." );
		}
		if( $id == 151 )
		{
			$player->syst2( "$flavor" );
		}
		else if( $id == 109 )
		{
			include_once( "arrays.php" );
			include_once( "wear_items.php" );

			foreach( $item_types_all as $a=>$b )
				if( $a > 0 && HasItemInSlot( $a ) )
					UnWearItem( $a );
			$attrs = Array( 30,40,50 );
			$sum = 0;
			foreach( $attrs as $a=>$b )
			{
				$val = $player->GetActualAttr( $b );
				$sum += $val;
				$player->AlterActualAttrib( $b, -$val );
			}
			$player->SetRealAttr( 1000, $player->level * 3 );

			$player->syst2( '/items' );
			$player->syst2( "$flavor" );

			$player->SetTrigger( 41, 0 );
			if( $player->HasTrigger( 42 ) ) $player->SetTrigger( 44, 1 );
			$player->SetTrigger( 42, 0 );

			f_MQuery( "DELETE FROM player_cards WHERE card_id IN( 186, 185, 187 ) AND player_id={$player->player_id}" );
			f_MQuery( "DELETE FROM player_selected_cards WHERE card_id IN( 186, 185, 187 ) AND player_id={$player->player_id}" );
			
			checkZhorik( $player, 13, 1 ); // квест жорика переобучиться
		}
		else if( $id == 367 )
		{
			include_once( "guild.php" );
			$res = f_MQuery( "SELECT * FROM player_guilds WHERE player_id={$player->player_id}" );
			$po = 0;
			while( $arr = f_MFetch( $res ) )
			{
				for( $i = 0; $i < $arr['rank']; ++ $i ) $po += $rank_prices[$i];
				for( $i = 0; $i < $arr['rating']; ++ $i ) $po += $rank_prices[$i];
			}
			$player->syst2( "$flavor Вы получаете $po профессионального опыта." );
			f_MQuery( "UPDATE characters SET prof_exp = prof_exp + $po WHERE player_id={$player->player_id}" );
			f_MQuery( "UPDATE player_guilds SET rank=0, rating=0 WHERE player_id={$player->player_id}" );
			
			$player->prof_exp += $po;
			
			UpdateTitle( false );
		}
		else if( $id == 368 )
		{
			if( !$_GET['plogin'] )
			{
				f_MQuery( "DELETE FROM player_cooldowns WHERE spell_id=$id AND player_id={$player->player_id}" );
				echo "var q = prompt( 'Введите имя игрока' ); if( q ) query('spellbook_ref.php?cast=$id&plogin=' + encodeURIComponent( q ), '');";
				die( );
			}
			else
			{
				$login = conv_utf( $_GET['plogin'] );
				$pid = f_MValue( "SELECT player_id FROM characters WHERE login='$login'" );
				if( !$pid )
				{
					f_MQuery( "DELETE FROM player_cooldowns WHERE spell_id=$id AND player_id={$player->player_id}" );
					echo "alert( 'Игрок не найден' );";
					die( );
				}
				else
				{
					$res = f_MQuery( "SELECT * FROM player_permissions WHERE player_id = {$pid}" );
					if( mysql_num_rows( $res ) == 0 )
						f_MQuery( "INSERT INTO player_permissions ( player_id ) VALUES ( {$pid} )" );

					$tm = f_MValue( "SELECT silence FROM player_permissions WHERE player_id = $pid" );
					if( !$tm || $tm < time( ) ) $tm = time( );
					$tm += 5 * 60;
					f_MQuery( "UPDATE player_permissions SET silence = $tm, silence_reason = 'Заклинание Молчание от игрока {$player->login}' WHERE player_id = $pid" );
					$player->syst2( "$flavor" );
				}
			}
		}
		die( "parent.sb_main.pageNeutral();" );
	}
	$res = f_MQuery( "SELECT * FROM player_selected_cards WHERE player_id = {$player->player_id} AND card_id = $id AND staff=0" );
	if( f_MNum( $res ) ) die( "alert( 'Этот свиток уже поставлен на бой' );" );
	$res = f_MQuery( "SELECT count( card_id ) FROM player_selected_cards WHERE player_id = {$player->player_id} AND staff=0" );
	$arr = f_MFetch( $res );
	if( $arr[0] >= 8 ) die( "alert( 'У вас уже выбрано на бой восемь свитков' );" );
	f_MQuery( "DELETE FROM player_selected_cards WHERE player_id={$player->player_id} AND card_id={$id}" );
	f_MQuery("UPDATE player_cards SET number=10 WHERE player_id={$player->player_id} AND card_id=".$id);
	f_MQuery( "INSERT INTO player_selected_cards ( player_id, card_id ) VALUES( {$player->player_id}, {$id} )" );
	
	$res = f_MQuery( "SELECT * FROM cards WHERE card_id = $id" );
	$arr = f_MFetch( $res );
	$descr = cardGetSmallIcon( $arr );
	echo "parent.parent.char_ref.add_spell( $descr );";
	echo "parent.parent.char_ref.show_char( parent.document.getElementById( 'char_items' ) );";
	echo "parent.char_set_sb_events( )";
//	$player->syst2("/items");
	return;
}

if( isset( $HTTP_GET_VARS['del'] ) )
{
	$lim = $HTTP_GET_VARS['del'];
	settype( $lim, 'integer' );
	$res = f_MQuery( "SELECT card_id FROM player_selected_cards WHERE player_id = {$player->player_id} AND staff=0 ORDER BY entry_id LIMIT $lim, 1" );
	if( !f_MNum( $res ) ) die( );
	$arr = f_MFetch( $res );
	$id = $arr[0];
	f_MQuery( "DELETE FROM player_selected_cards WHERE player_id = {$player->player_id} AND card_id = $id AND staff=0" );
	
	echo "parent.char_ref.del_spell( $lim );";
	echo "parent.char_ref.show_char( document.getElementById( 'char_items' ) );";
	echo "char_set_sb_events( )";
	return;
}

$action = 2;

if( isset( $HTTP_GET_VARS['action'] ) )
{
	$action = $HTTP_GET_VARS['action'];
	settype( $action, 'integer' );
}

echo $action;

$page = $HTTP_GET_VARS['page'];
settype( $page, 'integer' );
if( $page < 0 ) $page = 0;

$genre = $action - 3;
if( $genre < -1 ) $genre = -1;
else if( $genre == 7 ) $genre = 3;
else if( $genre > 2 ) $genre = 2;

if( $genre >= 0 ) $res = f_MQuery( "SELECT count( player_cards.card_id ) FROM player_cards, cards WHERE player_id = {$player->player_id} AND genre=$genre AND cards.card_id=player_cards.card_id AND number >= 10" );
else $res = f_MQuery( "SELECT count( player_cards.card_id ) FROM player_cards, cards WHERE player_id = {$player->player_id} AND cards.card_id=player_cards.card_id AND number >= 10" ); // AND genre!=3 
$arr = f_MFetch( $res );
$num = $arr[0];

if( $num == 0 ) $page = 0;
else
{
	$mx = ( $num + 7 ) / 8;
	settype( $mx, 'integer' );
	if( $page >= $mx ) $page = $mx - 1;
}

$lim = $page * 8;
if( $genre >= 0 ) $res = f_MQuery( "SELECT cards.card_id FROM player_cards, cards WHERE player_id = {$player->player_id} AND genre=$genre AND cards.card_id=player_cards.card_id  AND number >= 10 ORDER BY cards.level LIMIT $lim, 8" );
else $res = f_MQuery( "SELECT cards.card_id FROM player_cards, cards WHERE player_id = {$player->player_id} AND cards.card_id=player_cards.card_id  AND number >= 10 ORDER BY cards.level LIMIT $lim, 8" ); // AND genre != 3

print( "<script src=functions.js></script>" );

print( "<script>" );

$id = 0;
while( $arr = f_MFetch( $res ) )
{
	$card = new Card( $arr[0] );
	$card->LoadPlayer( $player );
	$cooldown = false;
	if( $card->genre == 3 )
	{
		$tm = time( );
		f_MQuery( "DELETE FROM player_cooldowns WHERE till < $tm" );
		$res2 = f_MQuery( "SELECT till FROM player_cooldowns WHERE player_id={$player->player_id} AND spell_id=$arr[0]" );
		$arr2 = f_MFetch( $res2 );
		if( $arr2 ) $cooldown = ( $arr2['till'] - $tm );
	}
	if( $cooldown === false ) print( "parent.sb_main.document.getElementById( 'spell0$id' ).innerHTML = '<span onclick=\"cast_spell(" . $card->card_id . ")\" style=\"cursor:pointer;\">' + ".$card->Image( $id )." + '</span>';" );
	else  print( "parent.sb_main.document.getElementById( 'spell0$id' ).innerHTML = '<span style=\"filter:progid:DXImageTransform.Microsoft.Alpha(opacity=40);-moz-opacity: 0.4;-khtml-opacity: 0.4;opacity: 0.4;\" onclick=\"cast_spell(" . $card->card_id . ")\" style=\"cursor:pointer;\">' + ".$card->Image( $id )." + '</span>';" );
	++ $id;
}
for( ; $id < 8; ++ $id )
{
	print( "parent.sb_main.document.getElementById( 'spell0$id' ).innerHTML = '<img width=141 height=141 border=0 src=\"images/spells/none.gif\">';" );
}

print( "parent.sb_main.sb_act = $action;parent.sb_main.sb_page=$page;" );
print( "</script>" );

?>
