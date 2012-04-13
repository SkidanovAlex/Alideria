<?

header("Content-type: text/html; charset=windows-1251");

include_once( "no_cache.php" );
include_once( "functions.php" );
include_once( "player.php" );
include_once( "lab.php" );
include_once( "lab_functions.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "/*Неверные настройки Cookie*/" );
	
$player = new Player( $HTTP_COOKIE_VARS['c_id'] );
$lab_id = isLabLoc( $player->location, $player->depth );
if( $lab_id == -1 )
	die( );
$lab_id = 1;
if( $player->regime != 0 ) die( );

$dxs = Array( -1, 0, 1, 0 );
$dys = Array( 0, -1, 0, 1 );

f_MQuery( "LOCK TABLES lab WRITE, player_labs WRITE" );
$res = f_MQuery( "SELECT cell_id, dir FROM player_labs WHERE player_id={$player->player_id} AND lab_id={$lab_id}" );
$arr = f_MFetch( $res );
if( !$arr )
{
	$res = f_MQuery( "SELECT cell_id FROM lab WHERE lab_id=$lab_id AND z=0 AND dir=-1" );
	$arr = f_MFetch( $res );
	if( !$arr ) RaiseError( "А где собственно вход в лабиринт $lab_id?" );
	f_MQuery( "INSERT INTO player_labs ( player_id, lab_id, cell_id, dir ) VALUES ( {$player->player_id}, $lab_id, $arr[0], 0 )" );
	$dir = 0;
}
else $dir = $arr[1];
$res = f_MQuery( "SELECT x, y, z, dir FROM lab WHERE lab_id=$lab_id AND cell_id=$arr[0]" );
$arr = f_MFetch( $res );
f_MQuery( "UNLOCK TABLES" );

$cx = $arr[0];
$cy = $arr[1];
$cz = $arr[2];

if( isset( $_GET['do'] ) )
{
	f_MQuery("LOCK TABLE player_triggers WRITE");
	if (f_MValue("SELECT COUNT(*) FROM player_triggers WHERE trigger_id=12345 AND player_id=".$player->player_id))
		die();
	else
		$player->SetTrigger(12345);
	f_MQuery("UNLOCK TABLES");

	$do = $_GET['do'];
    if ($do == 'up' || $do == 'down')
    {
        $larr = f_MFetch(f_MQuery("SELECT dir FROM lab WHERE lab_id=$lab_id AND x=$cx AND y=$cy AND z=$cz"));
        $nz = $cz;
        if ($larr[0] == -1 && $cz > 0 && $do == 'up')
        {
            $nz = $cz - 1;
        }
        if ($larr[0] == 1 && $do == 'down')
        {
            $nz = $cz + 1;
        }
        if ($cz != $nz)
        {
            $res = f_MQuery( "SELECT tex, cell_id FROM lab WHERE lab_id=$lab_id AND x=$cx AND y=$cy AND z=$nz" );
            $arr = f_MFetch( $res );
            if( $arr && $arr[0] == 0 )
            {
                f_MQuery( "UPDATE player_labs SET cell_id=$arr[1] WHERE player_id={$player->player_id} AND lab_id=$lab_id" );
            }

			$res = f_MQuery( "SELECT x, y, z FROM player_labs, lab WHERE player_id={$player->player_id} AND player_labs.cell_id=lab.cell_id" );
			$arr = f_MFetch( $res );
		    echo "refr();";
			echo "document.getElementById( 'coords' ).innerHTML = 'Этаж: <b>$arr[z]</b>; Координаты: <b>$arr[x]x$arr[y]</b>';";
			getNextStepInfo( $lab_id, $arr[x], $arr[y], $arr[z], $dir );
        }
    }
	if( $do == 'left' )
	{
		$dir = ( $dir + 1 ) % 4;
		f_MQuery( "UPDATE player_labs SET dir=$dir WHERE lab_id=$lab_id AND player_id={$player->player_id}" );
		echo "showDir( $dir );";
		echo "refr();";
		getNextStepInfo( $lab_id, $arr[x], $arr[y], $arr[z], $dir );
	}
	if( $do == 'right' )
	{
		$dir = ( $dir + 3 ) % 4;
		f_MQuery( "UPDATE player_labs SET dir=$dir WHERE lab_id=$lab_id AND player_id={$player->player_id}" );
		echo "showDir( $dir );";
		echo "refr();";
		getNextStepInfo( $lab_id, $arr[x], $arr[y], $arr[z], $dir );
	}
	if( $do == 'go' )
	{
   		$cx += $dxs[$dir];
   		$cy += $dys[$dir];
		$res = f_MQuery( "SELECT tex, cell_id FROM lab WHERE lab_id=$lab_id AND x=$cx AND y=$cy AND z=$cz" );
		$arr = f_MFetch( $res );
		if( $arr && $arr[0] == 0 )
		{
			f_MQuery( "UPDATE player_labs SET cell_id=$arr[1] WHERE player_id={$player->player_id} AND lab_id=$lab_id" );
			$res2 = f_MQuery( "SELECT * FROM lab_items WHERE lab_id=$lab_id AND cell_id=$arr[1]" );
			f_MQuery( "DELETE FROM lab_items WHERE lab_id=$lab_id AND cell_id=$arr[1]" );
			while( $arr2 = f_Mfetch( $res2 ) )
			{
				$player->AddItems( $arr2['item_id'], 1 );
				$player->AddToLogPost( $arr2['item_id'], 1, 16, $lab_id, $arr[1] );
				$res3 = f_MQuery( "SELECT name, name4 FROM items WHERE item_id=$arr2[item_id]" );
				$arr3 = f_MFetch( $res3 );
				if( $arr3[1] == '' ) $nm = $arr3[0];
				else $nm = $arr3[1];
				$player->syst( "Вы нашли <b>$nm</b>.", false );
			}
			$res2 = f_MQuery( "SELECT * FROM lab_mobs WHERE lab_id=$lab_id AND cell_id=$arr[1]" );
			$arr2 = f_MFetch( $res2 );
			if( $arr2 )
			{
				$attack = false;
				f_MQuery( "LOCK TABLE lab_combats WRITE, combats WRITE, combat_players WRITE" );
				$res3 = f_MQuery( "SELECT * FROM lab_combats WHERE lab_id=$lab_id AND cell_id=$arr[1]" );
				$arr3 = f_MFetch( $res3 );
				if( $arr3 )
				{
					$res4 = f_MQuery( "SELECT player_id, ready FROM combat_players WHERE combat_id=$arr3[combat_id] AND ai=1" );
					$arr4 = f_MFetch( $res4 );
					f_MQuery( "UNLOCK TABLES" );
					if( $arr4 && $arr4['ready'] <= 1 )
					{
						include_once( "create_combat.php" );
						ccAttackPlayer( $player->player_id, $arr4['player_id'], 0 );
						f_MQuery( "UPDATE combat_players SET win_action=4, win_action_param=$arr[1] WHERE player_id={$player->player_id}" );
						f_MQuery( "INSERT INTO combat_log ( combat_id, string ) VALUES ( $arr3[combat_id], '<b>{$player->login}</b> вмешивается в бой<br>' )" );
						$attack = 1;
					}
				}
				else
				{
					f_MQuery( "UNLOCK TABLES" );
					include_once( "mob.php" );
					$mob = new Mob( );
					$mob->CreateMob( $arr2['mob_id'], $player->location, $player->depth );
                    // Do insert ignore, since for most of the mobs avatar is already there
					f_MQuery( "INSERT IGNORE INTO player_avatars ( player_id, avatar ) VALUES ( {$mob->player_id}, '$arr2[img]' )" );
					$mob->AttackPlayer( $player->player_id, 4, $arr[1] );
					f_MQuery( "INSERT INTO lab_combats ( lab_id, cell_id, combat_id ) VALUES ( $lab_id, $arr[1], {$mob->combat_id} )" );
					$attack = true;
				}
				if( $attack )
					echo "document.getElementById( 'lab_msg' ).innerHTML = '<b><font color=red>Внимание! </font></b> Житель лабиринта атаковал вас!<br><a href=combat.php>Продолжить</a>';";
			}
			echo "do_go();";

			$res = f_MQuery( "SELECT x, y, z FROM player_labs, lab WHERE player_id={$player->player_id} AND player_labs.cell_id=lab.cell_id" );
			$arr = f_MFetch( $res );
			echo "document.getElementById( 'coords' ).innerHTML = 'Этаж: <b>$arr[z]</b>; Координаты: <b>$arr[x]x$arr[y]</b>';";
			getNextStepInfo( $lab_id, $arr[x], $arr[y], $arr[z], $dir );
		}
		else
		{
    		$cx -= $dxs[$dir];
    		$cy -= $dys[$dir];
		}
	}
	if( $do == 'leave' )
	{
		$player->SetLocation( 0 );
		$player->SetDepth( 1 );
		f_MQuery( "DELETE FROM player_labs WHERE player_id={$player->player_id}" );
		echo "location.href='game.php';";
	}

	f_MQuery("LOCK TABLE player_triggers WRITE");
	$player->SetTrigger(12345, 0);
	f_MQuery("UNLOCK TABLES");
}

?>
