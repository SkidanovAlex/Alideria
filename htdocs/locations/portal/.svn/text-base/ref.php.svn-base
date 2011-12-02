<?

include_once( 'items.php' );
include_once( 'locations/portal/func.php' );

if( !isset( $mid_php ) ) die( );

$cell_id = false;
$cell = false;
$keys = false;

function reloadInfo( )
{
	global $cell_id, $cell, $keys, $player;

    $arr = f_MFetch( f_MQuery( "SELECT * FROM portal_players WHERE player_id={$player->player_id}" ) );

    $cell_id = $arr['cell_id'];
    $keys = $arr['keys_mask'];

    $cell = f_MFetch( f_MQuery( "SELECT * FROM portal_maze WHERE cell_id=$cell_id" ) );
}

reloadInfo( );


if( $player->regime == 0 )
{
	$act = (int)$_GET['a'];
	if( $act == 1 ) // move
	{
		$dir = (int)$_GET['b'];
		$result = portal_move_player( $player, $dir, $cell, $player->clan_id, $keys );
		if( $result === -1 ) echo "alert( 'У вас нет потходящего ключа, чтобы открыть дверь' );";
		reloadInfo( );
		f_MQuery( "DELETE FROM portal_revealed_cells WHERE player_id={$player->player_id} AND cell_id={$cell_id}" );
		f_MQuery( "INSERT INTO portal_revealed_cells ( player_id, clan_id, cell_id, z, vis ) VALUES ( {$player->player_id}, {$player->clan_id}, $cell_id, {$cell[z]}, 0 )" );
	}
	else if( $act == 2 ) // attack monster
	{
		$entryId = (int)$_GET['b'];
		$arr = f_MFetch( f_MQuery( "SELECT * FROM portal_monsters WHERE clan_id={$player->clan_id} AND cell_id={$cell_id} AND entry_id={$entryId}" ) );
		$monsterId = $arr['monster_id'];
		include_once( 'create_combat.php' );
		$ok = true;
		$combat_id = -1;
		if( $arr['player_id'] >= 0 && f_MValue( "SELECT count(player_id) FROM characters WHERE player_id={$arr[player_id]}" ) )
		{
			$ready = f_MValue( "SELECT ready FROM combat_players WHERE player_id=$arr[player_id]" );
			if( $ready >= 2 )
			{
				echo "alert( 'Монстр только что закончил бой и не может быть атакован' );";
				$ok = false;
			}
			
			if( $ok )
			{
    			$combat_id = ccAttackPlayer( $player->player_id, $arr['player_id'], /*ai*/0, /*$bloody = */true, /*$lim25 = */true );
    			f_MQuery( "UPDATE combat_players SET ai=1 WHERE player_id={$arr[player_id]}" );
			}
		}
		else
		{
			$monster = $monsters[$monsterId];
			include_once( 'mob.php' );
			$mob = new Mob( );
			$mob->CreateDungeonMob( $monster[2], $monster[3], $monster[2], $monster[2], $monster[2], 5, 1, $monster[0], $monster[1].".jpg", $monster[5] );
			$mob->AttackPlayer( $player );
			$combat_id = $mob->combat_id;
			f_MQuery( "UPDATE portal_monsters SET player_id={$mob->player_id} WHERE entry_id=$entryId" );
		}
		
		if( $ok )
		{
			f_MQuery( "UPDATE combat_players SET win_action=13 WHERE combat_id=$combat_id" );
			f_MQuery( "UPDATE combats SET type=1 WHERE combat_id=$combat_id" );
			die( "location.href='combat.php';" );
		}
	}
}

echo "cm();";

$res = f_MQuery( "SELECT * FROM portal_monsters WHERE cell_id=$cell_id AND died+respawn < ".time() );
while( $arr = f_MFetch( $res ) )
{
	echo "am('{$monsters[$arr[monster_id]][0]}',{$arr[entry_id]});";
}

echo "refr( {$cell[x]}, {$cell[y]}, {$cell[walls]}, {$keys} );";

?>
