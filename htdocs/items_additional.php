<?

header("Content-type: text/html; charset=windows-1251");

include_once( 'no_cache.php' );
include_once( 'functions.php' );
include_once( 'player.php' );
include_once( 'items.php' );
include_once( 'card.php' );

f_MConnect( );

if( !check_cookie( ) ) die( );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
$stats = $player->getAllAttrNames( );

$slots = array( 4, 5, 9, 6, 7, 8, 2, 3, 10, 11, 12, 13 );

if( isset( $_GET['set'] ) )
{                        
	if( $player->regime != 0 ) die( 'alert( "Вы заняты" );' );

	$set_id = (int)$_GET['set'];
	$res = f_MQuery( "SELECT str FROM player_sets WHERE entry_id=$set_id AND player_id={$player->player_id}" );
	$arr = f_MFetch( $res );
	if( $arr ) $a2 = explode( ':', $arr[0] );
	else $a2 = array( 0, 0, 0, 0, 0, 0, 0, 0, 0 ,0, 0, 0 );
	$res = f_MQuery( "SELECT weared, item_id FROM player_items WHERE player_id={$player->player_id} AND weared > 1 AND weared < 14" );
	while( $arr = f_MFetch( $res ) )
		$items[$arr['weared']] = $arr['item_id'];

	include_once( 'wear_items.php' );
	for( $i = 0; $i < 12; ++ $i )
	{
		$a1[$i] = $items[$slots[$i]];
		if( $a1[$i] && (int)$a1[$i] != (int)$a2[$i] ) 
		{
			if( UnwearItem( $slots[$i] ) == 0 )
			{
    			print( "parent.char_ref.unwear( {$slots[$i]} );" );
    			print( "parent.game.alter_item( {$a1[$i]}, {$slots[$i]}, -1 );" );
    			print( "parent.game.alter_item( {$a1[$i]}, 0, 1 );" );
			}
		}
		if( $a2[$i] && (int)$a1[$i] != (int)$a2[$i] ) 
		{
			$ok = false;
			if( WearItem( $a2[$i], $slots[$i] ) >= 0 ) $ok = true;
			else
			{
				$tarr = f_MFetch( f_MQuery( "SELECT parent_id FROM items WHERE item_id={$a2[$i]}" ) );
				if( !$tarr ) continue;
				$res = f_MQuery( "SELECT i.item_id FROM items AS i INNER JOIN player_items AS p ON i.item_id=p.item_id WHERE i.parent_id={$tarr[0]} AND p.player_id={$player->player_id} AND weared=0" );
				$arr = f_MFetch( $res );
				if( $arr )
				{
					$a2[$i] = $arr[0];
					if( WearItem( $a2[$i], $slots[$i] ) >= 0 ) $ok = true;
				}
			}

			if( $ok )
			{
    			$res = f_MQuery( "SELECT * FROM items WHERE item_id = {$a2[$i]}" );
        		$arr = f_MFetch( $res );
        		$arr['weared'] = $slots[$i];
        		$descr = itemFullDescr2( $arr );
        		print( "parent.char_ref.wear( $arr[item_id], '$arr[name]', '$descr', '$arr[image]', {$slots[$i]} );" );
        		print( "parent.game.alter_item( {$a2[$i]}, 0, -1 );" );
        		print( "parent.game.alter_item( {$a2[$i]}, {$slots[$i]}, 1 );" );
    		}
		}
	}

	if( $set_id > 0 )
	{
    	f_MQuery( "DELETE FROM player_selected_cards WHERE staff=0 AND player_id={$player->player_id}" );
    	for( $i = 7; $i >= 0; -- $i )
    	{
        	echo "parent.char_ref.del_spell( $i );";
    	}
    	for( $i = 12; $i < count( $a2 ); ++ $i )
    	{
    		$qres = f_MQuery( "SELECT count( card_id ) FROM cards WHERE card_id={$a2[$i]}" );
    		$qarr = f_MFetch( $qres );

    		if( $qarr[0] )
    		{
           		f_MQuery( "INSERT INTO player_selected_cards( player_id, card_id ) VALUES ( {$player->player_id}, {$a2[$i]} )" );
               	$res = f_MQuery( "SELECT * FROM cards WHERE card_id = {$a2[$i]}" );
               	$arr = f_Mfetch( $res );
               	$descr = cardGetSmallIcon( $arr );
               	echo "parent.char_ref.add_spell( $descr );";
        	}
    	}
    }

	?>
	parent.char_ref.show_char( parent.game.document.getElementById( 'char_items' ) );
	parent.game.char_set_events( );
	ref_inv();
	<?
}
else if( $_GET['del'] )
{
	$set_id = (int)$_GET['del'];
	f_MQuery( "DELETE FROM player_sets WHERE entry_id=$set_id AND player_id={$player->player_id}" );
	echo "del_set( $set_id );";
	echo "ref_inv( );";
}
else if( $_GET['nm'] )
{
	$res = f_MQuery( "SELECT count( player_id ) FROM player_sets WHERE player_id={$player->player_id}" );
	$arr = f_MFetch( $res );
	if( $arr[0] >= 20 )
		die( 'alert( "Вы можете создать не более двадцати комплектов для вещей. Если вам нужен новый комплект, придётся удалить один из старых." );' );

	$items = array( );
	$res = f_MQuery( "SELECT weared, item_id FROM player_items WHERE player_id={$player->player_id} AND weared > 1 AND weared < 14" );
	while( $arr = f_MFetch( $res ) )
		$items[$arr['weared']] = $arr['item_id'];
	$str = '';
	for( $i = 0; $i < 12; ++ $i )
		$str .= ":".(int)$items[$slots[$i]];

	$res = f_MQuery( "SELECT card_id FROM player_selected_cards WHERE staff=0 AND player_id={$player->player_id} ORDER BY entry_id" );
	while( $arr = f_MFetch( $res ) )
		$str .= ":".$arr['card_id'];

	$nm = substr( htmlspecialchars( $_GET['nm'], ENT_QUOTES ), 0, 20 );
	$str = substr( $str, 1 );
	f_MQuery( "INSERT INTO player_sets( player_id, name, str ) VALUES ( {$player->player_id}, '$nm', '$str' )" );

	echo "add_set( ".mysql_insert_id( ).", '$nm' );";
	echo "ref_inv( );";
}

?>
