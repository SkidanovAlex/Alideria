<?

include_once( 'locations/portal/stuff.php' );

$monsters = array( );

// name, avatar, level(x,x+1), power, respawn
$monsters[0] = array( "Скелет-Воин",      "skel1",    6,  3, 3600,  36 );
$monsters[1] = array( "Скелет-Лучник",    "skel2",    8,  4, 4800,  35 );
$monsters[2] = array( "Скелет-Рыцарь",    "skel3",   10,  5, 7200,   8 );
$monsters[3] = array( "Леорик",           "skel4",   14, 10, 43200,  7 );

$monsters[4] = array( "Зомби-Каменщик",   "zombie1", 12,  7, 4800 );
$monsters[5] = array( "Зомби-Охотник",    "zombie2", 14,  8, 7200 );
$monsters[6] = array( "Зомби-Горец",      "zombie3", 16,  9, 9600 );
$monsters[7] = array( "Мясник",           "zombie4", 20, 14, 64800 );

$monsters[8] = array( "Лич-Элементалист", "lich1",   18, 11, 7200 );
$monsters[9] = array( "Лич-Зверобой",     "lich2",   20, 12, 9600 );
$monsters[10] = array( "Лич-Кукольщик",   "lich3",   22, 13, 10800 );
$monsters[11] = array( "Геровирон",       "lich4",   25, 18, 86400 );

function portal_swap_items( $player_id ) // expensive function
{
	f_MQuery( "LOCK TABLE player_portal_stored_items WRITE, player_items WRITE" );
	
	$check = f_MValue( "SELECT count( item_id ) FROM player_items WHERE player_id=$player_id AND weared != 0" );
	if( $check )
	{
		f_MQuery( "UNLOCK TABLES" );
		return false;
	}
	
	$here = f_MQuery( "SELECT * FROM player_items WHERE player_id=$player_id" );
	$there = f_MQuery( "SELECT * FROM player_portal_stored_items WHERE player_id=$player_id" );
	
	f_MQuery( "DELETE FROM player_items WHERE player_id=$player_id" );
	f_MQuery( "DELETE FROM player_portal_stored_items WHERE player_id=$player_id" );
	
	while( $arr = f_MFetch( $here ) ) f_MQuery( "INSERT INTO player_portal_stored_items ( player_id, item_id, number ) VALUES ( {$arr[player_id]}, {$arr[item_id]}, {$arr[number]} )" );
	while( $arr = f_MFetch( $there ) ) f_MQuery( "INSERT INTO player_items ( player_id, item_id, number, weared ) VALUES ( {$arr[player_id]}, {$arr[item_id]}, {$arr[number]}, 0 )" );
	
	f_MQuery( "UNLOCK TABLES" );

	$sum = 0;
	$res2 = f_MQuery( "SELECT items.weight, player_items.number FROM items, player_items WHERE items.item_id = player_items.item_id AND player_items.player_id = $player_id" );
	while( $arr2 = f_MFetch( $res2 ) )
	{
		$sum += $arr2[0] * $arr2[1];
	}
	
	f_MQuery( "UPDATE characters SET items_weight=$sum WHERE player_id=$player_id" );
	
	return true;
}

function portal_move_player( $player, $dir, $cell, $clan_id, $player_keys )
{
	global $mdx, $mdy;
	
	$wall = ( $cell['walls'] >> ( 3 * $dir ) ) & 7;
	if( $wall >= 5 || $wall == 0 || ( $wall != 1 && ( $player_keys & ( 1 << ( $wall - 1 ) ) ) != 0 ) )
	{
		$x = $cell['x'] + $mdx[$dir];
		$y = $cell['y'] + $mdy[$dir];
		$z = $cell['z'];
		$arr = f_MFetch( f_MQuery( "SELECT cell_id, keys_mask FROM portal_maze WHERE x=$x AND y=$y AND z=$z AND clan_id={$clan_id}" ) );
		if( $arr )
		{
            $cell_id = $arr['cell_id'];
            $keys = $arr['keys_mask'];
            
            if( $wall > 1 && $wall <= 4 ) // дверь, надо ее открыть
            {
            	$new_walls = $cell['walls'];
            	$new_walls &= ~( 7 << ( 3 * $dir ) );
            	$new_walls |= ( ($wall + 3) << ( 3 * $dir ) );
            	f_MQuery( "UPDATE portal_maze SET walls=$new_walls WHERE cell_id={$cell[cell_id]}" );
            }

            $cell = f_MFetch( f_MQuery( "SELECT * FROM portal_maze WHERE cell_id=$cell_id" ) );
			
			f_MQuery( "UPDATE portal_players SET cell_id=$cell_id, keys_mask = keys_mask | $keys WHERE player_id={$player->player_id}" );

            if( $wall > 1 && $wall <= 4 ) // дверь, надо ее открыть
            {
            	$dir = ( $dir + 2 ) % 4;
            	$new_walls = $cell['walls'];
            	$new_walls &= ~( 7 << ( 3 * $dir ) );
            	$new_walls |= ( ($wall + 3) << ( 3 * $dir ) );
            	f_MQuery( "UPDATE portal_maze SET walls=$new_walls WHERE cell_id={$cell[cell_id]}" );
            }
		}
		return true;
	}
	else if( $wall > 1 ) return -1;
	return -2;
}

?>
