<?

header("Content-type: text/html; charset=windows-1251");

$item_id = $_GET['item_id'];
$from = $_GET['from'];
$to = $_GET['to'];

settype( $item_id, "integer" );
settype( $from, "integer" );
settype( $to, "integer" );

if( $from == $to ) return;

include_once( "functions.php" );
include_once( "player.php" );
include_once( "arrays.php" );
include_once( "items.php" );
include_once( "wear_items.php" );
include_once( "feathers.php" );

f_MConnect( );

if( !check_cookie( ) )
	die( "Неверные настройки Cookie" );

$player = new Player( $HTTP_COOKIE_VARS['c_id'] );

if( $player->regime == 100 || $player->regime == 101 ) // нельзя во время боя или обмена
{
	die( );
}

$stats = $player->getAllAttrNames( );

print( "<script>" );

$pot_exp = false;

if( $from != -1 )
{
	if( $from == 1 || $from >= 14 && $from <= 24 )
	{
		$res = f_MQuery( "SELECT expires FROM player_potions WHERE player_id={$player->player_id} AND slot_id=$from" );
		$arr = f_MFetch( $res );
		if( $arr ) $pot_exp = $arr[0];
	}
	if( UnWearItem( $from ) == 0 ) // успешно сняли
	{
		print( "parent.char_ref.unwear( $from );" );
		print( "parent.game.alter_item( $item_id, $from, -1 );" );
		if( $pot_exp === false ) print( "parent.game.alter_item( $item_id, 0, 1 );" );
	}
}

if( $to != -1 )
{
	if( $to == 100 ) $to = -1;
	if( ( $to = WearItem( $item_id, $to ) ) >= 0 ) // успешно одели
	{
		if( $player->level == 1 || $player->player_id == 173 )
		{
			include_once( "noob.php" );
            $res = f_MQuery( "SELECT a, b FROM noob WHERE player_id={$player->player_id}" );
            $arr = f_MFetch( $res );
            if( $arr ) { $noob = $arr[0]; $noob_param = $arr[1]; }
            if( $noob == 8 && $item_id != 153 || $noob == 10 && $item_id != 133 ) die( "alert('Это не та вещь, которую просить одеть Астаниэль');</script>" );
			if( $noob == 8 || $noob == 10 ) echo "parent.game.query( 'n_follow.php?a=$noob', '' );";
			else if( $noob && $noob != 11 ) die( "alert('Подожди, пока Астаниэль не попросит тебя одеть вещи.');</script>" );
			if( $noob == 10 ) echo "parent.game.ready_to_explore = true;";

			else
			{
				include_once( 'player_noobs.php' );
				PingNoob( 3 );
			}
		}

		if( $pot_exp !== false )
			f_MQuery( "UPDATE player_potions SET expires = $pot_exp WHERE player_id={$player->player_id} AND slot_id = $to" );

		if( $to <= 24 )
		{
    		$res = f_MQuery( "SELECT * FROM items WHERE item_id = $item_id" );
    		$arr = f_MFetch( $res );
    		if( $arr['type'] < 20 || $arr['type'] == 30 || $arr['type'] == 35 )
    		{
        		$arr['weared'] = $to;
        		$descr = itemFullDescr2( $arr );
        		print( "parent.char_ref.wear( $arr[item_id], '$arr[name]', '$descr', '$arr[image]', $to );" );
        		print( "parent.game.alter_item( $item_id, $to, 1 );" );
    		}
		}
		else if( $to == 29 )
		{
			$hp = f_MValue( "SELECT value FROM player_attributes WHERE player_id={$player->player_id} AND attribute_id=1" );
			echo "hp_ = $hp;d0_ = new Date( );t0_ = d0_.getTime( );";
		}
		print( "parent.game.alter_item( $item_id, 0, -1 );" );
	}
	else if( $to == -3 && f_MValue( "SELECT type FROM items WHERE item_id=$item_id" ) == 25 )
	{
		if( canUseFeather( $player ) ) echo "parent.game.location.href='game.php?feather_id={$item_id}';";
	}
	else if( $to == -3 && f_MValue( "SELECT type FROM items WHERE item_id=$item_id" ) == 23 )
	{
		include_once( "instant_effects.php" );
		if( $player->DropItems( $item_id ) )
		{
			if( !useInstant( $item_id ) )
			{
				echo "alert( '".addslashes($last_instant_error)."' );";
				$player->AddItems( $item_id );
			}
			else
			{
				echo "alert( '".addslashes($last_instant_error)."' );";
				print( "parent.game.alter_item( $item_id, 0, -1 );" );
			}
		}
	}
	else print( "alert('".addslashes( getWearMessage( $to ) )."');" );
	
}

print( "parent.game.wear_level = $player->wear_level;\n" );

?>

parent.char_ref.show_char( parent.game.document.getElementById( 'char_items' ) );
parent.game.char_set_events( );
parent.game.document.getElementById( 'inv_items' ).innerHTML = parent.game.get_inv_html( );
parent.game.set_inv_events( );

</script>
