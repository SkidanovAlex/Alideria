<?

include_once( "functions.php" );
include_once( "items.php" );
include_once( "card.php" );
include_once( "arrays.php" );
include_once( 'attrib_relations.php' );
include_once( 'chat_channels_functions.php' );
include_once( 'quest_scripts/zhorik_checker.php' );
include_once( 'quest_race.php' );
include_once( "feathers.php" );

function strToInt3( $a )
{
	$st = '';
	if( $a < 100 ) $st .= "0";
	if( $a < 10 ) $st .= "0";
	$st .= $a;

	return $st;
}

class Player
{
	var $player_id;
	var $login;
	var $level, $exp;
	var $prof_exp;
	var $need_attrs;
	var $attrs;
	var $real_attrs;
	var $actual_attrs;
	var $location;
	var $depth;
	var $till;
	var $regime;
	var $screen_regime;
	var $rank = false;
	var $money;
	var $umoney;
	var $wear_level;
	var $items_weight;
	var $last_time_hp_updated;
	var $real_deaths;
	var $nick_clr;
	var $text_clr;
	var $clan_id;
	var $sex;
	var $tree_can;

	function Player( $id )
	{
		global $exp_table;

		$this->player_id = $id;
		$res = f_MQuery( "SELECT * FROM characters WHERE player_id={$this->player_id}" );
		$arr = mysql_fetch_array( $res );
		if( !$arr ) { $this->player_id = 0; return; }
		$this->login = $arr['login'];
		$this->level = $arr['level'];
		$this->exp = $arr['exp'];
		$this->prof_exp = $arr['prof_exp'];
		$this->need_attrs = true;
		$this->location = $arr['loc'];
		$this->depth = $arr['depth'];
		$this->till = $arr['go_till'];
		$this->regime = $arr['regime'];
		$this->screen_regime = $arr['screen_regime'];
		$this->money = $arr['money'];
		$this->umoney = $arr['umoney'];
		$this->wear_level = $arr['wear_level'];
		$this->items_weight = $arr['items_weight'];
		$this->last_time_hp_updated = $arr['last_time_hp_updated'];
		$this->real_deaths = $arr['real_deaths'];
		$this->nick_clr = $arr['nick_clr'];
		$this->text_clr = $arr['text_clr'];
		$this->clan_id = $arr['clan_id'];
		$this->sex = $arr['sex'];


		$this->tree_can = -1;
		if ($this->clan_id > 0)
		if (f_MValue("SELECT tree_active FROM clans WHERE clan_id={$arr['clan_id']}") >= 0)
		{
			$rtree = f_MQuery("SELECT tree_effects FROM player_clans WHERE player_id={$arr['player_id']}");
			if ($atree = f_MFetch($rtree))
				$this->tree_can = $atree[0];
			else
				$this->tree_can = 0;
		}

		while( $this->regime == 0 && $this->level < 25 && $exp_table[$this->level] && $exp_table[$this->level] <= $this->exp )
		{
    		if( f_MNum( f_MQuery( "SELECT * FROM tournament_busy_players WHERE player_id={$this->player_id}" ) ) )
    		{
    			break;
    		}

			++ $this->level;
			f_MQuery( "UPDATE characters SET level = $this->level WHERE player_id = $this->player_id" );
			$this->syst2( "Вы перешагиваете планку в <b>{$exp_table[$this->level - 1]}</b> опыта и переходите на <b>$this->level</b> уровень!" );
			f_MQuery("INSERT INTO player_log (player_id, item_id, type, arg1, time) VALUES ($this->player_id, -2, 997, $this->level, ".time().")");
			$this->AlterRealAttrib( 1000, 3 );
			$this->AlterActualAttrib( 101, 100 );
			//if( $this->level <= 5 )
			$this->SetRegime( 249 );

			checkZhorik( $this, 1, 1 );
		}

//        $this->SetTrigger(12345, 0);
	}

	function UploadInfoToJavaServer($ses_crc = null)
	{
	$crc = 0;
		global $test_server;
		if( $test_server ) return;
		$player_id = $this->player_id;

	    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_connect($sock, "127.0.0.1", 1100);
        $crc =/* $ses_crc !== null ? $ses_crc :*/ (int)f_MValue("SELECT session_crc FROM online WHERE player_id={$this->player_id} ORDER BY last_ping DESC");

        $msg = "player\n{$this->login}\n$crc\n{$this->player_id}\n{$this->nick_clr}\n{$this->text_clr}\n{$this->clan_id}\n{$this->level}\n";
        socket_write( $sock, $msg, strlen($msg) );

        socket_close( $sock );
        // ---------------------

        $res = f_MQuery( "SELECT combat_id, side FROM combat_players WHERE player_id={$this->player_id}" );
        $arr = f_MFetch( $res );
        if( !$arr ) $arr = array( 0, 0 );

	    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_connect($sock, "127.0.0.1", 1100);
        $msg = "combat\n{$this->player_id}\n$arr[0]\n$arr[1]\n";
        socket_write( $sock, $msg, strlen($msg) );
        socket_close( $sock );

		$res = f_MQuery( "SELECT target FROM ch_ignore WHERE player_id={$this->player_id}" );
		while( $arr = f_MFetch( $res ) )
		{
			if( $arr[0] == 0 )
			{
				// ---------------------
                $sock = socket_create(AF_INET, SOCK_STREAM, 0);
                socket_connect($sock, "127.0.0.1", 1100);
                $msg = "ignore\n$player_id\n0\n";
                socket_write( $sock, $msg, strlen($msg) );
                socket_close( $sock );
                // ---------------------
			}
			else if( $arr[0] == -1 )
			{
				// ---------------------
                $sock = socket_create(AF_INET, SOCK_STREAM, 0);
                socket_connect($sock, "127.0.0.1", 1100);
                $msg = "ignore\n$player_id\n-2\n";
                socket_write( $sock, $msg, strlen($msg) );
                socket_close( $sock );
                // ---------------------
			}
			else
			{
        		// ---------------------
                $sock = socket_create(AF_INET, SOCK_STREAM, 0);
                socket_connect($sock, "127.0.0.1", 1100);
                $msg = "ignore\n$player_id\n$arr[0]\n";
                socket_write( $sock, $msg, strlen($msg) );
                socket_close( $sock );
                // ---------------------
			}
		}
		if( $this->level < 2 )
		{
			// ---------------------
            $sock = socket_create(AF_INET, SOCK_STREAM, 0);
            socket_connect($sock, "127.0.0.1", 1100);
            $msg = "ignore\n$player_id\n-2\n";
            socket_write( $sock, $msg, strlen($msg) );
            socket_close( $sock );
            // ---------------------
		}

		$res = f_MQuery( "SELECT channel_id FROM ch_channels WHERE player_id={$this->player_id}" );
		while( $arr = f_MFetch( $res ) )
		{
       		// ---------------------
            $sock = socket_create(AF_INET, SOCK_STREAM, 0);
            socket_connect($sock, "127.0.0.1", 1100);
            $msg = "enter\n{$this->player_id}\n{$arr[0]}\n";
            socket_write( $sock, $msg, strlen($msg) );
            socket_close( $sock );
            // ---------------------
		}


		return $crc;
	}

	function UploadCombatToJavaServer( )
	{
		global $test_server;
		if( $test_server ) return;
        $res = f_MQuery( "SELECT combat_id, side FROM combat_players WHERE player_id={$this->player_id}" );
        $arr = f_MFetch( $res );
        if( !$arr ) $arr = array( 0, 0 );

	    $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_connect($sock, "127.0.0.1", 1100);
        $msg = "combat\n{$this->player_id}\n$arr[0]\n$arr[1]\n";
        socket_write( $sock, $msg, strlen($msg) );
        socket_close( $sock );
	}

	function Nick( )
	{
		if( !$this->level ) return "'<i>Моб</i>'";
		return "window.top.ii( $this->level, '$this->login', '$this->nick_clr', $this->clan_id, $this->sex )";
	}

	function Nick1( )
	{
		if( !$this->level ) return "'<i>Моб</i>'";
		return "window.top.ii( $this->level, \"$this->login\", \"$this->nick_clr\", $this->clan_id, $this->sex )";
	}

	function Nick2( )
	{
		if( !$this->level ) return "'<i>Моб</i>'";
		return "window.top.ii2( $this->level, '$this->login', '$this->nick_clr', $this->clan_id, $this->player_id )";
	}

	function checkWearLevel()
	{
		f_MQuery("LOCK TABLE characters WRITE, items WRITE, player_items WRITE");
		$wl = f_MValue("SELECT SUM(items.level) FROM items, player_items WHERE player_items.player_id={$this->player_id} AND player_items.item_id=items.item_id AND player_items.weared>0");
		if (!$wl) $wl = 0;
		f_MQuery("UPDATE characters SET wear_level=$wl WHERE player_id=".$this->player_id);
		f_MQuery("UNLOCK TABLES");
	}

	function syst( $a, $script_tags = true )
	{
		$tm = date( 'H:i', time( ) );
		if( $script_tags ) echo( "<script>" );
		print( "window.top.chat.syst( '$tm', '$a' );" );
		if( $script_tags ) echo( "</script>" );
	}

	function syst2( $a )
	{
		global $test_server;
		if( $test_server ) return;
		// ---------------------
        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
		socket_set_option( $sock, SOL_SOCKET, SO_REUSEADDR, 1 );
        socket_connect($sock, "127.0.0.1", 1100);
        $tm = date( "H:i", time( ) );
file_put_contents("log_syst2.txt", "say\n{$a}\n0\n{$this->player_id}\n0\n{$tm}\n");
        $msg = "say\n{$a}\n0\n{$this->player_id}\n0\n{$tm}\n";
        socket_write( $sock, $msg, strlen($msg) );
        socket_close( $sock );
        // ---------------------
	}

	function syst3( $a, $folder_id=0 ) // do offline messaging
	{
		global $test_server;
		if( $test_server ) return;
		// ---------------------
        $sock = socket_create(AF_INET, SOCK_STREAM, 0);
        socket_set_option( $sock, SOL_SOCKET, SO_REUSEADDR, 1 );
        socket_connect($sock, "127.0.0.1", 1100);
        $tm = date( "H:i", time( ) );
        $msg = "say\nУ вас новое сообщение в дневнике\n0\n{$this->player_id}\n0\n{$tm}\n";
        socket_write( $sock, $msg, strlen($msg) );
        socket_close( $sock );
        // ---------------------

        f_MQuery( "INSERT INTO post( sender_id, receiver_id, title, content, money, np, deadline, folder_id ) VALUES ( 69055, {$this->player_id}, '".substr( $a, 0, 10 )."...', '$a', '0', '0', '0', {$folder_id} )" );
	}

	var $bet_type = false;
	function getBetType( )
	{
		if( $this->bet_type === false )
		{
			$this->allow_move = true;
			$res = f_MQuery( "SELECT combat_bets.type FROM combat_bets, player_bets WHERE player_id = {$this->player_id} AND combat_bets.bet_id = player_bets.bet_id" );
			if( f_MNum( $res ) )
			{
				$arr = f_MFetch( $res );
				$this->bet_type = $arr[0];
			} else $this->bet_type = -1;
		}
		return $this->bet_type;
	}

	function SetLocation( $a, $anyway = false )
	{
		$res = f_MQuery( "SELECT player_id FROM market_bets WHERE player_id = {$this->player_id}" );
		$lim = $this->MaxWeight( );
		if( !$anyway && $lim !== 'inf' && $this->items_weight > $lim * 100 )
		{
			$this->syst2( "Вы не можете сдвинуться с места под тяжестью ваших вещей. Следует выкинуть что-нибудь." );
			return false;
		}
		if( $this->getBetType( ) == -1 && !f_MNum( $res ) )
		{
			f_MQuery( "UPDATE characters SET loc = $a WHERE player_id={$this->player_id}" );
			$this->location = $a;
			return true;
		}
		return false;
	}

	function SetDepth( $a, $anyway = false )
	{
		$res = f_MQuery( "SELECT player_id FROM market_bets WHERE player_id = {$this->player_id}" );
		$lim = $this->MaxWeight( );
		if( !$anyway && $lim !== 'inf' && $this->items_weight > $lim * 100 )
		{
			$this->syst2( "Вы не можете сдвинуться с места под тяжестью ваших вещей. Следует выкинуть что-нибудь." );
			return false;
		}
		else if( f_MNum( f_MQuery( "SELECT * FROM tournament_busy_players WHERE player_id={$this->player_id}" ) ) or
					f_MValue( 'SELECT * FROM `tournament_announcements` WHERE `status` = 4 AND `tournament_id` = ( SELECT `tournament_id` FROM `tournament_group_bets` WHERE `slot_0` = '.$this->player_id.' OR `slot_1` = '.$this->player_id.' OR `slot_2` = '.$this->player_id.' OR `slot_3` = '.$this->player_id.' OR `slot_4` = '.$this->player_id.' OR `slot_5` = '.$this->player_id.' ORDER BY `tournament_id` DESC LIMIT 0, 1 )' ) )
		{
			$this->syst2( "Вы учавствуете в турнире и не можете покинуть зал." );
			return false;
		}
		if( $this->getBetType( ) == -1 && !f_MNum( $res ) )
		{
			f_MQuery( "UPDATE characters SET depth = $a WHERE player_id={$this->player_id}" );
			$this->depth = $a;

				if( $this->level > 5 )
				{
					$locationVisits = f_MValue( "SELECT `visits` FROM `location_visits` WHERE `loc` = {$this->location} AND `depth` = {$this->depth}" );
					$maxLocationVisits = f_MValue( 'SELECT `visits` FROM `location_visits` ORDER BY `visits` DESC LIMIT 0, 1' ) / 3;
					$minLocationVisits = f_MValue( 'SELECT `visits` FROM `location_visits` ORDER BY `visits` ASC LIMIT 0, 1' );

					$P = round( 100 - ( ( $locationVisits - $minLocationVisits ) / ( $maxLocationVisits - $minLocationVisits + 1 ) ) * 100 );

					// Атака шамахан

					if( $this->location == 2 && $this->depth != 50 && $this->depth != 46 && $this->depth != 49 && $P > mt_rand( 0, 100 ) ) // Рассчёт вероятности атаки
					{
						$kind = 2;
						if( $this->level < 11 )
						{
							$kind = 0;
						}
						elseif( $this->level < 16 )
						{
							$kind = 1;
						}

						require_once( 'create_combat.php' );
						require_once( 'mob.php' );

						$maxSham = f_MValue( "SELECT `last_visit_time` FROM `location_visits` WHERE `loc` = {$this->location} AND `depth` = {$this->depth}" );
						$maxSham = ( $maxSham == 2147483647 or !$maxSham ) ? time( ) : $maxSham;
						$maxSham = round( ( time( ) - $maxSham ) / 3600 - 0.5 );
						$maxSham = ( $maxSham < 1 ) ? 0 : $maxSham;

						$num = mt_rand( 0, $maxSham ); // Число шамахан-атакующих

						for( $i = 0; $i < $num; ++ $i )
						{
							$mob = new Mob;
							$shnames = array( 'Шамаханин-боец', 'Шамаханин-капитан', 'Шамаханин-генерал' );
							$shava = array( 'sham1.png', 'sham2.png', 'sham3.png' );
							$mob->CreateDungeonMob( $kind * 4 + 5, 7, $kind * 4 + 5, $kind * 4 + 5, $kind * 4 + 5, $this->location, $this->depth, $shnames[$kind], $shava[$kind] );
							$mob->AttackPlayer( $this, 12, 0, false );
							$combat_id = $mob->combat_id;

							$kind = mt_rand( 0, 2 ); // Если мобов вышло больше одного, остальные могут быть любого левела, и только первый атакующий более-менее адекватен игроку
						}

						if( $combat_id )
						{
							setCombatTimeout( $combat_id, 60 );

							$this->syst2( 'Засада!' );
							$this->syst2( '/combat' );
						}
					}
				}
				// !Шамаханские лазутчики

			return true;
		}
		return false;
	}

	function SetTill( $a )
	{
		f_MQuery( "UPDATE characters SET go_till = $a WHERE player_id={$this->player_id}" );
		$this->till = $a;
	}

	function SetRegime( $a, $feathers = false )
	{
		global $feathers_regime0;
		f_MQuery( "UPDATE characters SET regime = $a WHERE player_id={$this->player_id}" );
		$this->regime = $a;
		if( $feathers )
		{
            f_MQuery( "LOCK TABLE player_feathers WRITE" );
            $res = f_MQuery( "SELECT * FROM player_feathers WHERE player_id={$this->player_id} AND feather_id IN ( ".implode( ",", $feathers_regime0 )." )" );
            f_MQuery( "DELETE FROM player_feathers WHERE player_id={$this->player_id} AND feather_id IN ( ".implode( ",", $feathers_regime0 )." )" );
            f_MQuery( "UNLOCK TABLES" );

            while( $arr = f_MFetch( $res ) ) undoFeather( $this, $arr['feather_id'] );
        }
	}

	function SetScreenRegime( $a )
	{
		f_MQuery( "UPDATE characters SET screen_regime = $a WHERE player_id={$this->player_id}" );
		$this->screen_regime = $a;
	}

	function LoadAttrs( )
	{
		if( $this->need_attrs )
		{
			$this->attrs = Array( );
			$this->real_attrs = Array( );
			$this->actual_attrs = Array( );
			$res = f_MQuery( "SELECT attribute_id, value, actual_value, real_value FROM player_attributes WHERE player_id={$this->player_id}" );
			while( $arr = f_MFetch( $res ) )
			{
				$this->attrs[$arr['attribute_id']] = $arr['value'];
				$this->actual_attrs[$arr['attribute_id']] = $arr['actual_value'];
				$this->real_attrs[$arr['attribute_id']] = $arr['real_value'];
			}

			$this->need_attrs = false;
		}
	}

	function GetAttr( $a )
	{
		$this->LoadAttrs( );
		if( $a == 1 ) $this->UpdateHP( );

		if( !isset( $this->attrs[$a] ) )
			$this->attrs[$a] = 0;
		return $this->attrs[$a];
	}

	function GetRealAttr( $a )
	{
		$this->LoadAttrs( );
		if( $a == 1 ) $this->UpdateHP( );

		if( !isset( $this->real_attrs[$a] ) )
			$this->real_attrs[$a] = 0;
		return $this->real_attrs[$a];
	}

	function GetActualAttr( $a )
	{
		$this->LoadAttrs( );

		if( !isset( $this->actual_attrs[$a] ) )
			$this->actual_attrs[$a] = 0;
		return $this->actual_attrs[$a];
	}

	function SetAttr( $a, $b )
	{
		$this->LoadAttrs( );

		$this->AlterAttrib( $a, $b - $this->attrs[$a] );

		$this->attrs[$a] = $b;
	}

	function AlterAttrib( $a, $b )
	{
		global $attrib_rels;

		if( $a == 1 ) $this->UpdateHP( );
		if( $b )
		{
			if( isset( $attrib_rels[$a] ) ) foreach( $attrib_rels[$a] as $rel ) $this->AlterAttrib( $rel, $b );

			$res = f_MQuery( "SELECT * FROM player_attributes WHERE player_id={$this->player_id} AND attribute_id=$a" );
			if( !mysql_num_rows( $res ) )
				f_MQuery( "INSERT INTO player_attributes VALUES ( {$this->player_id}, $a, 0, 0, 0 )" );

			f_MQuery( "UPDATE player_attributes SET value = value + $b WHERE player_id={$this->player_id} AND attribute_id=$a" );
			if( !$this->need_attrs )
				$this->attrs[$a] += $b;
		}
	}

	function SetRealAttr( $a, $b )
	{
		$this->LoadAttrs( );

		$this->AlterRealAttrib( $a, $b - $this->real_attrs[$a] );

		$this->real_attrs[$a] = $b;
	}


	function AlterRealAttrib( $a, $b )
	{
		global $attrib_rels;

		if( $a == 1 ) $this->UpdateHP( );
		if( $b )
		{
			if( isset( $attrib_rels[$a] ) ) foreach( $attrib_rels[$a] as $rel ) $this->AlterRealAttrib( $rel, $b );

			$res = f_MQuery( "SELECT * FROM player_attributes WHERE player_id={$this->player_id} AND attribute_id=$a" );
			if( !mysql_num_rows( $res ) )
				f_MQuery( "INSERT INTO player_attributes VALUES ( {$this->player_id}, $a, 0, 0, 0 )" );

			f_MQuery( "UPDATE player_attributes SET value = value + $b, real_value = real_value + $b WHERE player_id={$this->player_id} AND attribute_id=$a" );
			if( !$this->need_attrs )
				$this->attrs[$a] += $b;
		}
	}

	function AlterActualAttrib( $a, $b )
	{
		global $attrib_rels;

		if( $a == 1 ) $this->UpdateHP( );
		if( $b )
		{
			if( isset( $attrib_rels[$a] ) ) foreach( $attrib_rels[$a] as $rel ) $this->AlterActualAttrib( $rel, $b );

			$res = f_MQuery( "SELECT * FROM player_attributes WHERE player_id={$this->player_id} AND attribute_id=$a" );
			if( !mysql_num_rows( $res ) )
				f_MQuery( "INSERT INTO player_attributes VALUES ( {$this->player_id}, $a, 0, 0, 0 )" );

			f_MQuery( "UPDATE player_attributes SET value = value + $b, real_value = real_value + $b, actual_value = actual_value + $b WHERE player_id={$this->player_id} AND attribute_id=$a" );
			if( !$this->need_attrs )
			{
				$this->attrs[$a] += $b;
				$this->actual_attrs[$a] += $b;
			}
		}
	}

	function UpdateHP( $nasilno_naher = false )
	{
		if( !$nasilno_naher && $this->regime == 100 ) return;

		$this->LoadAttrs( );

		$tm = time( );
		$otm = $this->last_time_hp_updated;
		settype( $otm, 'integer' );
		$hp = $this->attrs[1];
		$max_hp = $this->attrs[101];

		if( $tm - $otm > 3600 ) $new_hp = $max_hp;
		else $new_hp = $hp + ( $tm - $otm ) * $max_hp / 300;
		settype( $new_hp, 'integer' );
		if( $new_hp > $max_hp ) $new_hp = $max_hp;

		$this->PingHP( );
		f_MQuery( "UPDATE player_attributes SET value = $new_hp WHERE player_id = $this->player_id AND attribute_id = 1" );
		$this->attrs[1] = $new_hp;
	}

	function PingHP( )
	{
		$tm = time( );
		$this->last_time_hp_updated = $tm;
		f_MQuery( "UPDATE characters SET last_time_hp_updated = $tm WHERE player_id = $this->player_id" );
	}

	function RestoreAttribs( )
	{
		f_MQuery( "UPDATE player_attributes SET value=real_value WHERE player_id={$this->player_id} AND attribute_id <> 1" );
		f_MQuery( "UPDATE player_attributes SET value=1 WHERE player_id={$this->player_id} AND attribute_id = 1 AND value <= 0" );

		$this->PingHP( );
	}

	function AddItems( $item_id, $number = 1 )
	{
		if( $number <= 0 ) return 0;

		f_MQuery( "LOCK TABLE player_items WRITE" );
		$res = f_MQuery( "SELECT number FROM player_items WHERE player_id = {$this->player_id} AND item_id={$item_id} AND weared=0" );
		if( mysql_num_rows( $res ) )
			f_MQuery( "UPDATE player_items SET number = number + $number WHERE player_id = {$this->player_id} AND item_id={$item_id} AND weared=0" );
		else
			f_MQuery( "INSERT INTO player_items ( player_id, item_id, number, weared ) VALUES ( {$this->player_id}, $item_id, $number, 0 )" );
		f_MQuery( "UNLOCK TABLES" );

		f_MQuery( "UPDATE characters SET items_weight = items_weight + ( SELECT weight FROM items WHERE item_id=$item_id ) * $number WHERE player_id={$this->player_id}" );
		$arr = f_MFetch( f_MQuery( "SELECT items_weight FROM characters WHERE player_id={$this->player_id}" ) );
		$this->items_weight = $arr[0];

		return $number;
	}

	function DropItems( $item_id, $number = 1 )
	{
		f_MQuery( "LOCK TABLE player_items WRITE" );
		$res = f_MQuery( "SELECT number FROM player_items WHERE player_id = {$this->player_id} AND item_id={$item_id} AND weared=0" );
		if( !mysql_num_rows( $res ) ) { f_MQuery( "UNLOCK TABLES" ); return 0; }
		else
		{
			$arr = f_MFetch( $res );
			if( $number == -1 ) // -1 = выбросить все - нельзя использовать
			{
				f_MQuery( "DELETE FROM player_items WHERE player_id = {$this->player_id} AND item_id={$item_id} AND weared=0" );
				f_MQuery( "UNLOCK TABLES" );
				return 1;
			}
			else if( $number < 0 ) { f_MQuery( "UNLOCK TABLES" ); return 0; }
			if( $arr[number] < $number ) { f_MQuery( "UNLOCK TABLES" ); return 0; }
			if( $arr[number] == $number )
				f_MQuery( "DELETE FROM player_items WHERE player_id = {$this->player_id} AND item_id={$item_id} AND weared=0" );
			else
				f_MQuery( "UPDATE player_items SET number = number - $number WHERE player_id = {$this->player_id} AND item_id={$item_id} AND weared=0" );
			f_MQuery( "UNLOCK TABLES" );
		}

		f_MQuery( "UPDATE characters SET items_weight = items_weight - ( SELECT weight FROM items WHERE item_id=$item_id ) * $number WHERE player_id={$this->player_id}" );

		return 1;
	}

	function DropItemsArr( $arr, $type, $arg1 = 0, $arg2 = 0 )
	{
		$dropped = array( );
		// stage 1: check all
		foreach( $arr as $a => $b ) if( $this->NumberItems( $a ) < $b ) return false;
		// stage 2: drop and check
		foreach( $arr as $a => $b ) if( !$this->DropItems( $a, $b ) )
		{
			foreach( $dropped as $a => $b ) $this->AddItems( $a, $b );
			return false;
		} else $dropped[$a] = $b;
		foreach( $arr as $a => $b ) $this->AddToLogPost( $a, - $b, $type, $arg1, $arg2 );
		return true;
	}

	function AddEffect( $effect_id, $type, $name, $descr, $image, $effect = "", $expires = -1 )
	{
		f_MQuery( "INSERT INTO player_effects( player_id, effect_id, type, name, description, image, effect, expires ) VALUES ( {$this->player_id}, $effect_id, $type, '$name', '$descr', '$image', '$effect', $expires )" );
		$player = $this;
		$arr = array( "effect"=>$effect );
		$mul = 1;
		include( "item_effect.php" );
	}

	function RemoveEffect( $effect_id , $all_effect = false)
	{
		f_MQuery( "LOCK TABLE player_effects WRITE" );
		if (!$all_effect)
		{
			$res = f_MQuery( "SELECT effect FROM player_effects WHERE player_id={$this->player_id} AND id={$effect_id}" );
			f_MQuery( "DELETE FROM player_effects WHERE player_id={$this->player_id} AND id={$effect_id}" );
		}
		else
		{
			$res = f_MQuery( "SELECT effect FROM player_effects WHERE player_id={$this->player_id} AND effect_id={$effect_id}" );
			f_MQuery( "DELETE FROM player_effects WHERE player_id={$this->player_id} AND effect_id={$effect_id}" );
		}
		f_MQuery( "UNLOCK TABLES" );

		$mul = -1;
		$player = $this;
		while( $arr = f_MFetch( $res ) )
		{
			include( "item_effect.php" );
		}
	}

	function MaxWeight( )
	{
		$res = f_MQuery( "SELECT till FROM player_cooldowns WHERE player_id={$this->player_id} AND spell_id=151" );
		$arr = f_MFetch( $res );
		if( $arr && $arr[0] - 480*60 + 5*60 > time( ) ) return "inf";

		return 50 + ( $this->level - 1 ) * 5 + ( $this->GetAttr( 223 ) ) * 5;
	}

	function SpendMoney( $a )
	{
		if( $this->money < $a )
		{
			return false;
		}
		$this->money -= $a;
		f_MQuery( "UPDATE characters SET money = money - $a WHERE player_id = {$this->player_id}" );
		return true;
	}

	function AddMoney( $a )
	{
		$this->money += $a;
		f_MQuery( "UPDATE characters SET money = money + $a WHERE player_id = {$this->player_id}" );
	}

	function SpendUMoney( $a )
	{
		if( $this->umoney < $a )
		{
			return false;
		}
		$this->umoney -= $a;
		f_MQuery( "UPDATE characters SET umoney = umoney - $a WHERE player_id = {$this->player_id}" );
		return true;
	}

	function AddUMoney( $a )
	{
		$this->umoney += $a;
		f_MQuery( "UPDATE characters SET umoney = umoney + $a WHERE player_id = {$this->player_id}" );
	}

	function CheckItemReq( $s )
	{
		$arr = ParseItemStr( $s );

		foreach( $arr as $a=>$b )
		{
		//	if( $this->player_id == 9571 ) echo "$a = $b = ".$this->GetAttr( $a )."<br>";
			if( $this->GetAttr( $a ) < $b )
				return 0;
		}
		//if( $this->player_id == 9571 ) die( );

		return 1;
	}

	function CheckItems( $s )
	{
		$arr = ParseItemStr( $s );

		foreach( $arr as $a=>$b )
		{
			if( $a == 0 && $this->money < $b ) return 0;
			if( $a != 0 && $this->NumberItems( $a ) < $b )
				return 0;
		}

		return 1;
	}


	function NumberItems( $item_id )
	{
		$res = f_MQuery( "SELECT number FROM player_items WHERE player_id = {$this->player_id} AND item_id={$item_id} AND weared=0" );
		if( !mysql_num_rows( $res ) ) return 0;
		$arr = f_MFetch( $res );
		return $arr[number];
	}

	function NumberItemsTotal( $item_id )
	{
		$res = f_MQuery( "SELECT number FROM player_items WHERE player_id = {$this->player_id} AND item_id={$item_id}" );
		if( !mysql_num_rows( $res ) ) return 0;
		$arr = f_MFetch( $res );
		return $arr[number];
	}

	function HasItem( $item_id )
	{
		$res = f_MQuery( "SELECT number FROM player_items, items WHERE player_id = {$this->player_id} AND items.parent_id={$item_id} AND player_items.item_id=items.item_id" );
		if( !mysql_num_rows( $res ) ) return false;
		return true;
	}

	function HasUnwearedItem( $item_id )
	{
		$res = f_MQuery( "SELECT number FROM player_items, items WHERE player_id = {$this->player_id} AND items.parent_id={$item_id} AND player_items.item_id=items.item_id AND weared = 0" );
		if( !mysql_num_rows( $res ) ) return false;
		return true;
	}

	function HasWearedItem( $item_id )
	{
		$res = f_MQuery( "SELECT number FROM player_items, items WHERE player_id = {$this->player_id} AND player_items.item_id=items.item_id AND items.parent_id={$item_id} AND weared != 0" );
		if( !mysql_num_rows( $res ) ) return false;
		return true;
	}

	function HasItemInSlot( $slot ) // по конкретному слоту
	{
		$res = f_MQuery( "SELECT * FROM player_items WHERE player_id = {$this->player_id} AND weared=$slot" );
		if( mysql_num_rows( $res ) )
			return 1;
		return 0;
	}


	function AddToLog( $item_id, $number, $type, $arg1 = 0, $arg2 = 0, $arg3 = 0 )
	{
		$had = 0;
		if( $item_id == 0 ) $had = $this->money;
		else if( $item_id == -1 )  $had = $this->umoney;
		else $had = $this->NumberItemsTotal( $item_id );

		$have = $had + $number;

		$tm = time( );
		f_MQuery( "INSERT INTO player_log ( player_id, item_id, had, have, type, arg1, arg2, arg3, time ) VALUES ( {$this->player_id}, $item_id, $had, $have, $type, $arg1, $arg2, $arg3, $tm )" );
	}

	function AddToLogPost( $item_id, $number, $type, $arg1 = 0, $arg2 = 0, $arg3 = 0 )
	{
		$had = 0;
		if( $item_id == 0 ) $had = $this->money;
		else if( $item_id == -1 )  $had = $this->umoney;
		else $had = $this->NumberItemsTotal( $item_id );
		$had -= $number;

		$have = $had + $number;

		$tm = time( );
		f_MQuery( "INSERT INTO player_log ( player_id, item_id, had, have, type, arg1, arg2, arg3, time ) VALUES ( {$this->player_id}, $item_id, $had, $have, $type, $arg1, $arg2, $arg3, $tm )" );
	}

	function HasTrigger( $a )
	{
		$res = f_MQuery( "SELECT * FROM player_triggers WHERE player_id = {$this->player_id} AND trigger_id = $a" );
		if( mysql_num_rows( $res ) ) return 1;

		return 0;
	}

	function SetTrigger( $a, $val = 1 )
	{
		$q = $this->HasTrigger( $a );
		if( $val != $q )
		{
			if( $val ) f_MQuery( "INSERT INTO player_triggers ( player_id, trigger_id ) VALUES ( {$this->player_id}, $a )" );
			else f_MQuery( "DELETE FROM player_triggers WHERE player_id = {$this->player_id} AND trigger_id = $a" );
		}
	}

	// Установка Значения. Может использоваться для чего угодно в смысле временных чисельных пометок
	function SetValue( $valueId, $value )
	{
		// Приведение к нужному формату
		$valueId = (int)$valueId;
		$value = (int)$value;

		// Обновление или установка значения
		if( f_MValue( 'SELECT * FROM player_values WHERE player_id = '.$this->player_id.' AND value_id = '.$valueId ) )
		{
			f_MQuery( 'UPDATE player_values SET value = '.$value.' WHERE player_id = '.$this->player_id.' AND value_id = '.$valueId );
		}
		else
		{
			f_MQuery( 'INSERT INTO player_values( player_id, value_id, value ) VALUES( '.$this->player_id.', '.$valueId.', '.$value.' )' );
		}
	}

	// Получаем Значение. Может использоваться для чего угодно в смысле временных
	function GetValue( $valueId )
	{
		// Приведение к нужному формату
		$valueId = (int)$valueId;

		return f_MValue( 'SELECT value FROM player_values WHERE player_id = '.$this->player_id.' AND value_id = '.$valueId );
	}


	function GetQuestValue( $a )
	{
		$res = f_MQuery( "SELECT value FROM player_quest_values WHERE player_id=$this->player_id AND value_id=$a" );
		$arr = f_MFetch( $res );
		if( !$arr ) return 0;
		return $arr[0];
	}

	function SetQuestValue( $value_id, $value )
	{
		$res = f_MQuery( "SELECT value FROM player_quest_values WHERE player_id=$this->player_id AND value_id=$value_id" );
		if( !f_MNum( $res ) ) f_MQuery( "INSERT INTO player_quest_values ( player_id, value_id, value ) VALUES ( $this->player_id, $value_id, $value )" );
		else f_MQuery( "UPDATE player_quest_values SET value=$value WHERE player_id=$this->player_id AND value_id=$value_id" );
	}

	function AlterQuestValue( $value_id, $value )
	{
		$res = f_MQuery( "SELECT value FROM player_quest_values WHERE player_id=$this->player_id AND value_id=$value_id" );
		if( !f_MNum( $res ) ) f_MQuery( "INSERT INTO player_quest_values ( player_id, value_id, value ) VALUES ( $this->player_id, $value_id, $value )" );
		else f_MQuery( "UPDATE player_quest_values SET value=value+$value WHERE player_id=$this->player_id AND value_id=$value_id" );
	}

	function MooDie( ) // die в пхп зарезервирован, пришлось немного поправить название
	{
	if ($this->level > 2) //малышей не отправляем к лекарю
	{
		$this->SetRegime( 250 );
		if ($this->location==3 && $this->level == 3)
			$this->SetDepth( 7, true );
		else
			$this->SetDepth( 0, true );
		$pen = 30;

		$barr = f_MFetch( f_MQuery( "SELECT count( player_id ) FROM premiums WHERE player_id={$this->player_id} AND premium_id=5" ) );
		if( $barr[0] ) $pen = 20;

		$tm = time( ) + $pen + $pen * $this->real_deaths;
		f_MQuery( "UPDATE characters SET real_deaths = real_deaths + 1, go_till = $tm WHERE player_id = {$this->player_id}" );
	}
	else
	{ //надо написать перебросы для малышей
		if ($this->location==3)
			$this->SetDepth(7, true);
		else
			$this->SetDepth(0, true);
		$msgs = Array(
		"",
		"",
		"",
		""
		);
		
		$this->syst2($msgs[mt_rand(0, 3)]);
	}
	
	}

	function LeaveCombat( $a = -1 )
	{
		global $feathers_combat;

		f_MQuery( "LOCK TABLE combat_players WRITE" );
		$res = f_MQuery( "SELECT ready, win_action, win_action_param, combat_id, bloody, log_type, mob_id, ai, side FROM combat_players WHERE player_id = {$this->player_id}" );
		$arr = f_MFetch( $res );

		if( !$arr )
		{
			f_MQuery( "UNLOCK TABLES" );
			return;
		}

		f_MQuery( "DELETE FROM combat_players WHERE player_id={$this->player_id}" );
		f_MQuery( "UNLOCK TABLES" );

		f_MQuery( "DELETE FROM combat_turn_desc WHERE player_id={$this->player_id}" );


		if ( $arr['win_action'] != 3)
		{
			$bal_id = f_MValue("SELECT item_id FROM player_items WHERE weared=25 AND player_id=".$this->player_id);
			if ($bal_id>0)
			{
				$bal_arr = f_MValue("SELECT decay FROM items WHERE item_id=".$bal_id);
				$mlhp = f_MValue("SELECT value FROM player_attributes WHERE attribute_id=101 AND player_id=".$this->player_id);
				$thp = f_MValue("SELECT value FROM player_attributes WHERE attribute_id=1 AND player_id=".$this->player_id);
				if ($thp>0)
					$lhp = $mlhp - $thp;
				else
					$lhp = $mlhp;
				$bal_arr = $bal_arr - $lhp;
				if ($lhp > 0)
				{
//					$bal_arr = $bal_arr - $lhp;
					if ($bal_arr>0)
					{
						if ($thp>0)
							f_MQuery("UPDATE player_attributes SET value=value+$lhp WHERE attribute_id=1 AND player_id=".$this->player_id);
						else
							f_MQuery("UPDATE player_attributes SET value=$lhp WHERE attribute_id=1 AND player_id=".$this->player_id);
						$this->syst2("Целебный бальзам восстановил Вам <b>".$lhp."</b> ".my_word_str($lhp, 'единицу', 'единицы', 'единиц')." здоровья.");
						f_MQuery("UPDATE items SET decay=".$bal_arr." WHERE item_id=".$bal_id);
					}
					else
					{
						$lhp=$lhp+$bal_arr;
						if ($thp>0)
							f_MQuery("UPDATE player_attributes SET value=value+$lhp WHERE attribute_id=1 AND player_id=".$this->player_id);
						else
							f_MQuery("UPDATE player_attributes SET value=$lhp WHERE attribute_id=1 AND player_id=".$this->player_id);
						$this->syst2("Целебный бальзам восстановил Вам <b>".$lhp."</b> ".my_word_str($lhp, 'единицу', 'единицы', 'единиц')." здоровья.");
						f_MQuery("DELETE FROM player_items WHERE player_id=".$this->player_id." AND weared=25");
						f_MQuery("DELETE FROM items WHERE item_id=".$bal_id);
						$this->syst2("Целебный бальзам выпит до дна.");
					}
				}
				
			}
		}


		if( $a == -1 ) $a = $arr['combat_id']; // вызывает смутные сомнения необходимости параметра $a вообще :оО

		$log_str = "";
		$v_polomka = 0;
		if( $arr['ready'] == 3 && ( $arr['ai'] == 0 || $arr['win_action'] == 3 || $arr['win_action'] == 12 || $arr['win_action'] == 6 && $arr['win_action_param'] == 1 ) ) // Победа в бою - возможно надо сделать определенные действия
		{
			if( $arr['win_action'] == 1 ) // Покорение Новой Глубины
			{
				$qres = f_MQuery( "SELECT * FROM player_depths WHERE player_id = {$this->player_id} AND loc = {$this->location}" );
				if( !mysql_num_rows( $qres ) )
					f_MQuery( "INSERT INTO player_depths VALUES ( {$this->player_id}, {$this->location}, $arr[win_action_param] )" );
				else f_MQuery( "UPDATE player_depths SET depth = $arr[win_action_param] WHERE player_id={$this->player_id} AND loc={$this->location}" );
			}
			else if( $arr['win_action'] == 2 )
			{
				f_MQuery( "UPDATE characters SET go_till = go_till - $arr[win_action_param] WHERE player_id = {$this->player_id}" );
			}
			else if( $arr['win_action'] == 3 )
			{
				$expires = time( ) + 125;
				f_MQuery( "INSERT INTO tournament_queue ( tournament_id, player_id, expires ) VALUES ( $arr[win_action_param], {$this->player_id}, $expires )" );
			}
			else if( $arr['win_action'] == 4 )
			{
				f_MQuery( "DELETE FROM lab_mobs WHERE cell_id=$arr[win_action_param]" );
			}
			else if( $arr['win_action'] == 5 )
			{
				$this->SetTrigger( 51 );
            	$av = "f".$this->sex."f.jpg";
            	f_MQuery( "LOCK TABLE player_avatars WRITE" );
            	f_MQuery( "DELETE FROM player_avatars WHERE player_id={$this->player_id}" );
            	f_MQuery( "INSERT INTO player_avatars( player_id, avatar ) VALUES ( {$this->player_id}, '$av' )" );
            	f_MQuery( "UNLOCK TABLES" );
				f_MQuery( "INSERT INTO player_talks ( player_id, talk_id, npc_id ) VALUES ( {$this->player_id}, 199, 33 );" );
				$this->syst2( "/items" );
			}
			else if( $arr['win_action'] == 6 )
			{
				if ($arr['ai'])
				{
					f_MQuery( "UPDATE forest_monster_camps SET combat_id=0, strazha_helper=0 WHERE combat_id=$a" );
				}
				else
				{
    				if ($arr['win_action_param'] == 0) $this->syst2( 'Атака с этого направления отражена! Вы можете подойти в Городскую Управу в любое удобное вам время за наградой.' );
    				else if ($arr['win_action_param'] == 1) $this->syst2( 'Вы успешно ликвидировали логово монстров в лесу. Вы можете подойти в Городскую Управу в любое удобное вам время за наградой.' );
    				f_MQuery( "INSERT INTO player_ta_winnings( player_id ) VALUES ( {$this->player_id} )" );
    				if ($arr['win_action_param'] == 0) f_MQuery( "UPDATE ta_combats SET combat_id=-2 WHERE combat_id=$a" );
    				else if ($arr['win_action_param'] == 1) f_MQuery( "DELETE FROM forest_monster_camps WHERE combat_id=$a" );
				}
			}
			else if( $arr['win_action'] == 7 )
			{
				f_MQuery( "UPDATE clans SET ta_lost=0 WHERE clan_id=$arr[win_action_param]" );
				$pres = f_MQuery( "SELECT t.player_id FROM ta_bets as t INNER JOIN player_clans as p ON t.player_id=p.player_id WHERE p.clan_id={$arr[win_action_param]}" );
				while( $parr = f_MFetch( $pres ) )
				{
					f_MQuery( "UPDATE characters SET regime=0 WHERE player_id=$parr[0]" );
					f_MQuery( "DELETE FROM ta_bets WHERE player_id=$parr[0]" );
				}
			}
			else if( $arr['win_action'] == 8 ) // подземелье
			{
				$val = f_MValue( "SELECT leader_id FROM caveexp_groups WHERE player_id={$this->player_id}" );
				if( $val )
				{
					f_MQuery( "UPDATE caveexp_groups SET ready=3 WHERE player_id={$this->player_id}" );
				}
				else
				{
					$mPunished = false;
					$marr2 = f_MFetch( f_MQuery( "SELECT fights, fights_reason FROM player_permissions WHERE player_id=$arr[0]" ) );
					if( $marr2 && $marr2[0] > time( ) )
					{
						$mPunished = true;
					}

 					$cstage = f_MValue( "SELECT stage FROM player_caveexp WHERE player_id={$this->player_id}" );
    				f_MQuery( "UPDATE player_caveexp SET stage=stage+1 WHERE player_id={$this->player_id}" );
    				f_MQuery( "UPDATE characters SET depth=depth+1 WHERE player_id={$this->player_id}" );

					if( $mPunished == false )
					{
    					$exp = (int)($cstage * $this->level * 6 * mt_rand( 75, 108 ) / 100);
					$exp_to_log = $exp;
    					// +50% при премиум-боях
    					if( f_MValue( 'SELECT * FROM `premiums` WHERE `player_id` = '.$this->player_id.' AND `premium_id` = 0' ) )
    					{
    						$premiumExp = round( $exp / 2 );
					}
					else
					{
						$premiumExp = 0;
					}

					$exp_to_log += $premiumExp;
					// Академия
					$academyLevel = f_MValue( "SELECT level FROM clan_buildings WHERE clan_id=( SELECT clan_id FROM characters WHERE player_id=".$this->player_id." ) AND building_id=4" );
					if( $academyLevel )
					{
						$bonus = ceil( $exp * ( 0.02 ) * $academyLevel );
						$exp_to_log += $bonus;
						$academyExp = " Ваша академия приносит вам дополнительно <b>$bonus</b> ".my_word_str( $bonus, "единицу опыта", "единицы опыта", "единиц опыта" ).".";
						f_MQuery( "UPDATE characters SET exp = exp + $bonus WHERE player_id = ".$this->player_id );
					}
					else
					{
						$academyExp = '';
					}

    					f_MQuery( "UPDATE characters SET exp=exp+$exp+$premiumExp WHERE player_id={$this->player_id}" );
    					$this->syst2( "Вы победили монстров, охранявших <b>$cstage</b> глубину подземелья, и получили <b>$exp</b>".my_word_str( $exp, ' единицу опыта', " единицы опыта", " единиц опыта" ).".$academyExp".( ( $premiumExp ) ? " Премиум-бои приносят ещё дополнительно <b>$premiumExp</b> ".my_word_str( $premiumExp, ' единицу опыта', " единицы опыта", " единиц опыта" )."." : '' ) );
					f_MQuery("INSERT INTO player_log (player_id, item_id, type, have, arg1, time) VALUES ($this->player_id, -2, 998, $exp_to_log, $cstage, ".time().")");
    				}
    				else
    				{
						$this->syst2( "Поскольку вы наказаны запретом на бои, никакого опыта вам не положено." );
    				}
    			}
			}
			else if( $arr['win_action'] == 9 )
			{
				f_MQuery( "UPDATE clans SET ta_lost=11 WHERE clan_id=$arr[win_action_param]" );
			}
			else if( $arr['win_action'] == 10 ) // клановый турнир, личный бой
			{
				f_MQuery( "UPDATE tournament_group_scores SET score=score+1, num_in_combat=num_in_combat-1 WHERE bet_id={$arr[win_action_param]}" );
			}
			else if( $arr['win_action'] == 11 ) // клановый турнир, групповой бой
			{
				f_MQuery( "LOCK TABLE tournament_group_scores WRITE" );
				f_MQuery( "UPDATE tournament_group_scores SET score=score+2, num_in_combat=num_in_combat-1 WHERE bet_id={$arr[win_action_param]} AND num_in_combat=1" );
				f_MQuery( "UPDATE tournament_group_scores SET num_in_combat=num_in_combat-1 WHERE bet_id={$arr[win_action_param]} AND num_in_combat > 1" );
				f_MQuery( "UNLOCK TABLES" );
			}
			else if( $arr['win_action'] == 12 )     // 9 may
			{
				if( $arr['ai'] == 0 ) $this->AlterQuestValue( 50, 1 );
				f_MQuery( "DELETE FROM quest_9m WHERE combat_id={$arr[combat_id]}" );
			}
			else if( $arr['win_action'] == 13 ) // portal maze
			{
			}
            else if ($arr['win_action'] == 14)
            {
                f_MQuery("DELETE FROM lab_quest_monsters WHERE entry_id={$arr[win_action_param]}");
            }

			$i_permit = true;
			$won = true;
			$mob_id = $arr['mob_id'];
			include( 'leave_combat_quest.php' );

			$log_str = "Одерживает победу ";
		}
		else if( $arr['ready'] == 2 )
		{
			$i_permit = true;
			$won = false;
			$mob_id = $arr['mob_id'];
			include( 'leave_combat_quest.php' );
			if( $arr['win_action'] == 6 && $arr['win_action_param'] == 1 )
			{
				if( f_MValue("SELECT count(player_id) FROM combat_players WHERE combat_id=$a AND ready = 3 AND ai=1") || f_MValue("SELECT count(player_id) FROM combat_players WHERE combat_id=$a AND ready < 2 AND ai=0")==0 )
					f_MQuery( "DELETE FROM forest_monster_camps WHERE combat_id=$a" );
			}
			if( $arr['win_action'] == 8 ) // подземелье
			{
				$val = f_MValue( "SELECT leader_id FROM caveexp_groups WHERE player_id={$this->player_id}" );
				if( $val )
				{
					f_MQuery( "UPDATE caveexp_groups SET ready=4 WHERE player_id={$this->player_id}" );
				}
				else
				{
    				$oberegs = f_MValue( "SELECT oberegs FROM player_caveexp WHERE player_id={$this->player_id}" );
    				if( $oberegs > 0 )
    				{
    					f_MQuery( "UPDATE player_caveexp SET oberegs=oberegs-1 WHERE player_id={$this->player_id}" );
    					$this->syst2( 'Вы проиграли в бою и потеряли оберег.' );
    				}
    				else if( $this->HasTrigger( 409 ) && mt_rand( 0, 99 ) < 30 )
    				{
    					$this->syst2( 'Вы проиграли в бою. Сработал эффект восхитительного сияющего перышка, вы можете продолжить исследование.' );
    				}
    				else
    				{
    					$this->syst2( 'Вы проиграли в бою. У вас нет оберега, вам пришлось покинуть подземелье.' );
    					$this->SetLocation( 2, true );
    					$this->SetDepth( 5, true );
    					$this->SetTrigger( 409, 0 );
    					f_MQuery( "DELETE FROM player_feathers WHERE player_id={$this->player_id} AND feather_id=36" );
    					f_MQuery( "UPDATE player_caveexp SET stage=0 WHERE player_id={$this->player_id}" );
    				}
    			}
			}
			else if( $arr['win_action'] == 10 || $arr['win_action'] == 11 ) // клановый турнир, любой бой
			{
				f_MQuery( "UPDATE tournament_group_scores SET num_in_combat=num_in_combat-1 WHERE bet_id={$arr[win_action_param]}" );
			}
		}

		$polomka_char = '.';
		if( $arr['ready'] == 2 ) $polomka_char = ',';
		$winmx = 7; $losemx = 3;
		if( $this->location == 2 && ( $this->depth == 1 || $this->depth == 43 ) )
		{
			$winmx = 1;
			$losemx = 10;
		}
		// feathers
		$slot_data = "";
		$def_pol = false;
		$def_not = false;
		if( $this->HasTrigger( 403 ) )
		{
			$def_not = true;
		}
		else if( $this->HasTrigger( 408 ) && $this->HasItemInSlot( 9 ) )
		{
			$def_pol = true;
			$slot_data = " AND weared=9";
		}
		else if( $this->HasTrigger( 406 ) && $this->HasItemInSlot( 2 ) )
		{
			$def_pol = true;
			$slot_data = " AND weared=2";
		}
		else if( $this->HasTrigger( 407 ) && $this->HasItemInSlot( 3 ) )
		{
			$def_pol = true;
			$slot_data = " AND weared=3";
		}
		if( !$def_not ) if( $def_pol || ( $arr['ready'] == 3 && mt_rand( 1, $winmx ) == 2 ) || ( $arr['ready'] == 2 && mt_rand( 1, $losemx ) == 2 ) ) // изношенность вещей
		{
    		$polomka_char = ':';
    		if( $arr['ready'] == 2 ) $polomka_char = ';';
    		$v_polomka = 1;
    		if( $arr['ready'] == 2 ) $v_polomka = 2;

			$iires = f_MQuery( "SELECT items.*, player_items.weared FROM player_items,items WHERE weared > 1 AND weared < 14 AND items.item_id=player_items.item_id AND player_id={$this->player_id} $slot_data ORDER BY rand() LIMIT 1" );
			$iiarr = f_MFetch( $iires );
			if( $iiarr )
			{
				$slot = $iiarr['weared'];
				$decay = $iiarr['decay'];
				-- $decay;

				if( !$iiarr['improved'] && !$iiarr['clan_marked'] )
				{
					$cres = f_MQuery( "SELECT item_id FROM items WHERE parent_id=$iiarr[parent_id] AND decay=$decay AND max_decay=$iiarr[max_decay] AND clan_marked=0 AND improved=0" );
					$carr = f_MFetch( $cres );
					if( $carr ) $item_id = $carr[0];
					else $item_id = copyItem( $iiarr['parent_id'] );
				} else $item_id = $iiarr['item_id'];

				if( $item_id != $iiarr['item_id'] )
					f_MQuery( "UPDATE player_items SET item_id=$item_id WHERE weared=$slot AND player_id={$this->player_id}" );
				if( $decay > 0 )
				{
					f_MQuery( "UPDATE items SET decay = $decay, max_decay = $iiarr[max_decay] WHERE item_id=$item_id" );
					$this->syst2( "В ходе боя прочность <b>{$iiarr[name2]}</b> уменьшилась до $decay/$iiarr[max_decay]" );
					$this->syst2( "/items" );
				}
				else
				{
					f_MQuery( "UPDATE items SET decay = $decay, max_decay = $iiarr[max_decay] WHERE item_id=$item_id" );
					include_once( 'wear_items.php' );
					UnWearItem( $slot, true );
					if( $iiarr['max_decay'] == 1 )
					{
						$this->DropItems( $item_id );
						// удалим пометку о том, что мертвая вещь принадлежит ордену
						if ( checkOrderItem( $item_id ) != false )
						{
							removeUniqueItem( $item_id, $this->clan_id ); // оно может не работать, кстати.
						}
						// -----8<----------
//						f_MQuery( "DELETE FROM items WHERE item_id=$item_id" );
					}
					$pmsg = "В ходе боя <b>{$iiarr[name]}</b> сломалась";
					if( $iiarr['kind_text'] != '' ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> {$iiarr[kind_text]}";
					else if( $iiarr['kind'] == 0 )
					{
						if( $iiarr['word_form'] == 2 ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> сломалась";
						else if( $iiarr['word_form'] == 3 ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> сломалось";
						else if( $iiarr['word_form'] == 4 ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> сломались";
						else $pmsg = "В ходе боя <b>{$iiarr[name]}</b> сломался";
					}
					else if( $iiarr['kind'] == 1 )
					{
						if( $iiarr['word_form'] == 2 ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> порвалась окончательно";
						else if( $iiarr['word_form'] == 3 ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> порвалось окончательно";
						else if( $iiarr['word_form'] == 4 ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> порвались окончательно";
						else $pmsg = "В ходе боя <b>{$iiarr[name]}</b> порвался окончательно";
					}
					else if( $iiarr['kind'] == 2 )
					{
						if( $iiarr['word_form'] == 2 ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> потухла";
						else if( $iiarr['word_form'] == 3 ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> потухло";
						else if( $iiarr['word_form'] == 4 ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> потухли";
						else $pmsg = "В ходе боя <b>{$iiarr[name]}</b> потух";
					}
					else if( $iiarr['kind'] == 3 )
					{
						if( $iiarr['word_form'] == 2 ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> разломалась на части";
						else if( $iiarr['word_form'] == 3 ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> разломалось на части";
						else if( $iiarr['word_form'] == 4 ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> разломались на части";
						else $pmsg = "В ходе боя <b>{$iiarr[name]}</b> разломался на части";
					}
					else if( $iiarr['kind'] == 4 )
					{
						if( $iiarr['word_form'] == 2 ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> пришла в полную негодность";
						else if( $iiarr['word_form'] == 3 ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> пришло в полную негодность";
						else if( $iiarr['word_form'] == 4 ) $pmsg = "В ходе боя <b>{$iiarr[name]}</b> пришли в полную негодность";
						else $pmsg = "В ходе боя <b>{$iiarr[name]}</b> пришёл в полную негодность";
					}
					$this->syst2( $pmsg );
					$this->syst2( "/items" );
				}
			}
		}

		$moo = f_MFetch( f_MQuery( "SELECT str FROM player_polomka WHERE player_id={$this->player_id}" ) );
		if( !$moo ) f_MQuery( "INSERT INTO player_polomka ( player_id, str ) VALUES ( {$this->player_id}, '$polomka_char' )" );
		else
		{
			if( strlen( $moo[0] ) >= 1000 ) $moo[0] = substr( $moo[0], 1 );
			$moo[0] .= $polomka_char;
			f_MQuery( "UPDATE player_polomka SET str = '$moo[0]' WHERE player_id={$this->player_id}" );
		}

		$no_set_zero_regime = false;
		if( $arr['ready'] == 2 )
		{
			if( $arr['bloody'] == 1 )
			{
				$this->MooDie( );
				$no_set_zero_regime = true;
			}
			$log_str = "Терпит поражение ";
		}

		if( $arr[bloody] ) $log_str .= "в кровавом бою";
		else if( $arr[log_type] > 0 )
		{
			if( $arr['ready'] == 3 ) $log_str .= "над ";
			else $log_str .= "от ";
			$res1 = f_MQuery( "SELECT login FROM characters WHERE player_id = $arr[log_type]" );
			if( !f_MNum( $res1 ) ) $log_str .= "<i>неизвестный персонаж</i>";
			else
			{
				$arr1 = f_MFetch( $res1 );
				$log_str .= $arr1[0];
			}
		}
		else if( $arr[log_type] == -1 ) $log_str .= "в групповом бою";
		else if( $arr[log_type] == -2 ) $log_str .= "в хаотичном бою";
		else if( $arr[log_type] == -5 ) $log_str .= "в турнирном бою";
		else $log_str .= "в квестовом бою";

		if( $arr['ready'] == 3 || $arr['ready'] == 2 )
		{
			$tm = time( );
			f_MQuery( "INSERT INTO history_combats ( player_id, time, str, combat_id, polomka ) VALUES ( {$this->player_id}, $tm, '$log_str', $arr[combat_id], $v_polomka )" );

			$tarr = f_MFetch( f_MQuery( "SELECT type FROM combats WHERE combat_id=$a" ) );
			$litera = ( $arr['ready'] == 3 ) ? 'w' : 'l';
			if( $tarr[0] == 1 ) $this->incStatistic( "npc_$litera" );
			else $this->incStatistic( "pvp_$litera" );
		}

		$this->RestoreAttribs( );
		if( !$no_set_zero_regime )
			$this->SetRegime( 0 );
		f_MQuery( "DELETE FROM combat_creatures WHERE player_id={$this->player_id}" );
		f_MQuery( "DELETE FROM combat_auras WHERE player_id={$this->player_id}" );
		f_MQuery( "DELETE FROM combat_animation WHERE player_id={$this->player_id}" );
		if( mysql_num_rows( f_MQuery( "SELECT player_id FROM combat_players WHERE combat_id=$a" ) ) == 0 )
		{
			f_MQuery( "DELETE FROM combats WHERE combat_id=$a" );
			f_MQuery( "DELETE FROM combat_loot WHERE combat_id=$a" );
			f_MQuery( "DELETE FROM lab_combats WHERE combat_id=$a" );
		}

        f_MQuery( "LOCK TABLE player_feathers WRITE" );
        $res = f_MQuery( "SELECT * FROM player_feathers WHERE player_id={$this->player_id} AND feather_id IN ( ".implode( ",", $feathers_combat )." )" );
        f_MQuery( "DELETE FROM player_feathers WHERE player_id={$this->player_id} AND feather_id IN ( ".implode( ",", $feathers_combat )." )" );
        f_MQuery( "UNLOCK TABLES" );

        while( $arr = f_MFetch( $res ) ) undoFeather( $this, $arr['feather_id'] );

        if( ( $this->HasTrigger( 230 ) && !$this->HasTrigger( 232 ) ) or $this->HasTrigger( 231 ) )
        {
        		$this->SetTrigger( 231, 0 );
				$this->SetTrigger( 232, 1 );
				$this->syst2( 'А теперь иди в Харчевню и продолжай квест' );
        }
   }

	function VisibleCardsTmp( $genre, $attr )
	{
		$st = "SELECT cards.card_id FROM player_cards, cards, player_attributes WHERE player_cards.player_id = {$this->player_id} AND player_attributes.player_id = {$this->player_id} AND player_cards.card_id = cards.card_id AND cards.genre = $genre AND player_attributes.attribute_id = $attr AND ( cards.cost <= player_attributes.value AND cards.cost > 0 AND cards.multy=0 OR cards.multy=1 AND player_attributes.value >= 5*{$this->level} )";
		$st .= " UNION ALL ";
		$st .= "SELECT cards.card_id FROM player_cards, cards WHERE player_cards.player_id = {$this->player_id} AND player_cards.card_id = cards.card_id AND cards.genre = $genre AND cards.cost = 0 AND cards.multy=0";
		return $st;
	}

	function VisibleCards( )
	{
		$ret = Array( );
		$query = $this->VisibleCardsTmp( 0, 130 ) . " UNION ALL " . $this->VisibleCardsTmp( 1, 140 ) . " UNION ALL " . $this->VisibleCardsTmp( 2, 150 );
		$res = f_MQuery( $query );
		while( $arr = f_MFetch( $res ) )
			$ret[] = $arr[0];

		return $ret;
	}

	function VisibleCardsGenre( $genre )
	{
		$ret = Array( );
		$query = $this->VisibleCardsTmp( $genre, 130 + $genre * 10 );
		$res = f_MQuery( $query );
		while( $arr = f_MFetch( $res ) )
			$ret[] = $arr[0];

		return $ret;
	}

	function ShowCards( $func )
	{
		$clrs = Array( "blue", "green", "red" );
		$res = f_MQuery( "SELECT cards.card_id FROM player_cards, cards WHERE player_cards.card_id=cards.card_id AND player_cards.player_id={$this->player_id} ORDER BY cards.genre, cards.name" );
		if( !mysql_num_rows( $res ) )
			print( "&nbsp;<i>Нет ни одного свитка</i><br>" );
		else
		{
			while( $arr = mysql_fetch_array( $res ) )
			{
				$card = new Card( $arr[0] );
				print( "<font style='cursor:pointer' onClick=\"" . $func . "( ".$card->card_id." )\"><div id=crd".$card->card_id." name=crd".$card->card_id.">&nbsp;<script>document.write( ".$card->Text( )." );</script><font color=".$clrs[$card->genre].">&nbsp;/{$card->cost} маны</font></div></font>" );
			}
		}
	}

	var $pattrs = false;
	var $sattrs = false;
	var $paclrs = false;
	var $saclrs = false;
	var $gimgs = false;
	var $gclrs = false;
	var $panames = false;
	var $sanames = false;
	var $allanames = false;

	function getPrimaryAttrs( )
	{
		if( $this->pattrs === false )
		{
			$this->pattrs = Array( );
			$this->paclrs = Array( );
			$this->panames = Array( );
			$res = f_MQuery( "SELECT * FROM attributes WHERE parent = -2 ORDER BY attribute_id" );
			while( $arr = f_MFetch( $res ) )
			{
				$this->pattrs[] = $arr[attribute_id];
				$this->paclrs[] = $arr[color];
				$this->panames[] = $arr[name];
			}
		}

		return $this->pattrs;
	}

	function getSecondaryAttrs( )
	{
		if( $this->sattrs === false )
		{
			$this->sattrs = Array( );
			$this->saclrs = Array( );
			$this->sanames = Array( );
			$this->saimgs = Array( );
			$res = f_MQuery( "SELECT * FROM attributes WHERE parent = -1 ORDER BY attribute_id" );
			while( $arr = f_MFetch( $res ) )
			{
				$this->sattrs[] = $arr[attribute_id];
				$this->saclrs[] = $arr[color];
				$this->saimgs[] = $arr[icon];
				$this->sanames[] = $arr[name];
			}
		}

 		return $this->sattrs;
	}

	function getAllAttrNames( )
	{
		if( $this->allanames === false )
		{
			$this->allanames = Array( );
			$res = f_MQuery( "SELECT * FROM attributes" );
			while( $arr = f_MFetch( $res ) )
			{
				$this->allanames[$arr[attribute_id]] = $arr[name];
				$this->gimgs[$arr[attribute_id]] = $arr[icon];
				$this->gclrs[$arr[attribute_id]] = $arr[color];
			}
		}

		return $this->allanames;
	}

	function OutAttrStr( $k, $v, $a = "", $show_incs = true, $inc_id = 1000, $litera = '' )
	{
		$stats = $this->getAllAttrNames( );

//		$attrs = $this->getSecondaryAttrs( );
		$aclrs = $this->gclrs;
		$aimgs = $this->gimgs;

		if( $this->GetAttr( $inc_id ) && $show_incs )
			$qstr = "&nbsp;<a href=inc_{$litera}attr.php?a=$v target=ref><img src=images/e_plus.gif border=0 width=11 height=11></a>";
		else $qstr = "";

		$q = $this->GetAttr( $v ) - $this->GetActualAttr( $v );
		if( $q == 0 )
			print( "<tr><td><img width=20 height=20 src='images/icons/attributes/$aimgs[$v]'></td><td width=150>&nbsp;<b><font color=$aclrs[$v]>$a$stats[$v]:&nbsp;</font></b></td><td align=right><b>".$this->GetActualAttr( $v )."</b>$qstr</td><td>&nbsp;</td></tr>" );
		else if( $q > 0 )
			print( "<tr><td><img width=20 height=20 src='images/icons/attributes/$aimgs[$v]'></td><td width=150>&nbsp;<b><font color=$aclrs[$v]>$a$stats[$v]:&nbsp;</font></b></td><td align=right><b>".$this->GetActualAttr( $v )."</b>$qstr</td><td><font color=#8080FF><b>+$q</b></font></td></tr>" );
		else
		{
			$q = - $q;
			print( "<tr><td><img width=20 height=20 src='images/icons/attributes/$aimgs[$v]'></td><td width=150>&nbsp;<b><font color=$aclrs[$v]>$a$stats[$v]:&nbsp;</font></b></td><td align=right><b>".$this->GetActualAttr( $v )."</b>$qstr</td><td><font color=darkred><b>-$q</b></font></td></tr>" );
		}
	}

	function OutAttrStr2( $k, $v, $a = "", $show_incs = true, $inc_id = 1000, $litera = '' )
	{
		$stats = $this->getAllAttrNames( );
		$aclrs = $this->gclrs;
		$aimgs = $this->gimgs;

		$val = $this->GetAttr( $v );
		if( $v == 130 ) $val = $this->GetAttr( 130 )."<sup>+".($this->GetAttr( 30 ) + $this->GetAttr( 33 ))."</sup>";
		if( $v == 140 ) $val = $this->GetAttr( 140 )."<sup>+".($this->GetAttr( 40 ) + $this->GetAttr( 42 ))."</sup>";
		if( $v == 150 ) $val = $this->GetAttr( 150 )."<sup>+".($this->GetAttr( 50 ) + $this->GetAttr( 51 ))."</sup>";
//		if ($val != 0)
			print( "<tr><td><img width=20 height=20 src='images/icons/attributes/$aimgs[$v]'></td><td width=150>&nbsp;<b><font color=$aclrs[$v]>$a$stats[$v]:&nbsp;</font></b></td><td align=right><b>".$val."</b></td><td>&nbsp;</td></tr>" );
	}

	function ShowPrimaryAttributes( )
	{
		global $stats;

		$attrs = $this->getPrimaryAttrs( );
		$anames = $this->panames;
		$aclrs = $this->paclrs;

		print( "<table cellspacing=0 cellpadding=0>" );
		foreach( $attrs as $k => $v )
		{
			$this->OutAttrStr( $k, $v, "", false );
		}
		print( "</table>" );
	}

	function ShowGlobalAttributes( )
	{
		global $stats;

		$attrs = Array( 14, 224, 223, 502 );

		print( "<table cellspacing=0 cellpadding=0>" );
		foreach( $attrs as $k => $v )
		{
			$this->OutAttrStr2( $k, $v, "", false );
		}
		print( "</table>" );
	}

	function ShowBattleAttributes( )
	{
		global $stats;

		$attrs = Array( 130, 131, 132, 140, 141, 142, 150, 151, 152 );

		print( "<table cellspacing=0 cellpadding=0>" );
		foreach( $attrs as $k => $v )
		{
			$this->OutAttrStr2( $k, $v, "", false );
		}
		print( "</table>" );
	}

	function ShowSecondaryAttributes( $show_incs = true )
	{
		global $stats;

		$attrs = $this->getSecondaryAttrs( );
		$anames = $this->sanames;
		$aclrs = $this->saclrs;

		print( "<table cellspacing=0 cellpadding=0>" );
		foreach( $attrs as $k => $v )
			$this->OutAttrStr( $k, $v, "", $show_incs, 1000, '' );
		print( "<tr><td colspan=4>&nbsp;</td></tr>" );
		if( $this->GetAttr( 1000 ) && $show_incs )
			print( "<tr><td colspan=2><b>Свободные Навыки:</td><td align=right><b>".$this->GetAttr( 1000 )."</b></td><td>&nbsp;</td></tr>" );
		print( "</table>" );
	}

	function ARect( $rdy = false )
	{
		print( "cc( $this->player_id, '{$this->login}', {$this->level}, ".$this->GetAttr( 1 ).", ".$this->GetAttr( 101 ) );
		print( ", ".$this->GetAttr( 30 ).", ".$this->GetAttr( 130 ).",".( $this->GetAttr( 30 ) + $this->GetAttr( 33 ) ).", ".$this->GetAttr( 131 ).", ".$this->GetAttr( 132 ) );
		print( ", ".$this->GetAttr( 40 ).", ".$this->GetAttr( 140 ).",".( $this->GetAttr( 40 ) + $this->GetAttr( 42 ) ).", ".$this->GetAttr( 141 ).", ".$this->GetAttr( 142 ) );
		print( ", ".$this->GetAttr( 50 ).", ".$this->GetAttr( 150 ).",".( $this->GetAttr( 50 ) + $this->GetAttr( 51 ) ).", ".$this->GetAttr( 151 ).", ".$this->GetAttr( 152 ) );
		print( ", ".$this->clan_id." " .( ( $rdy !== false ) ? ", $rdy" : "" ). ")" );
	}

	function BRect( $rdy = 0, $show_nick = 1, $mode = 1 )
	{
		print( "ccc( $this->player_id, '{$this->login}', {$this->level}, ".$this->GetAttr( 1 ).", ".$this->GetAttr( 101 ) );
		print( ", ".$this->GetAttr( 30 ).", ".$this->GetAttr( 130 ).",".( $this->GetAttr( 30 ) + $this->GetAttr( 33 ) ).", ".$this->GetAttr( 131 ).", ".$this->GetAttr( 132 ) );
		print( ", ".$this->GetAttr( 40 ).", ".$this->GetAttr( 140 ).",".( $this->GetAttr( 40 ) + $this->GetAttr( 42 ) ).", ".$this->GetAttr( 141 ).", ".$this->GetAttr( 142 ) );
		print( ", ".$this->GetAttr( 50 ).", ".$this->GetAttr( 150 ).",".( $this->GetAttr( 50 ) + $this->GetAttr( 51 ) ).", ".$this->GetAttr( 151 ).", ".$this->GetAttr( 152 ) );
		print( ", ".$this->GetAttr( 13 ).", ".$this->GetAttr( 15 ).",".$this->GetAttr( 16 ).", ".$this->GetAttr( 222 ) );
		print( ", ".$this->clan_id." " .( ( $rdy !== false ) ? ", $rdy" : "" ). "," . $show_nick . "," . $mode . ")" );
	}

	function ShowAttributes( $rdy = false, $combat = false )
	{
		print( "<div id=attrs{$this->player_id} name=attrs{$this->player_id}><script>\n" );

		echo( "document.write( " );
		if( !$combat ) $this->ARect( $rdy );
		else $this->BRect( $rdy, 0 );
		echo( " );\n" );

		if( $this->regime != 100 )
		{
			$q = $this->player_id;

			print( "var hp$q = {$this->attrs[1]};\n" );
			print( "var max_hp$q = {$this->attrs[101]};\n" );
			print( "var d0$q = new Date( );\n" );
			print( "var t0$q = d0$q.getTime( );\n" );
			print( "function updateHP$q( ) {\n" );
			print( "\td1 = new Date( );\n" );
			print( "\tt1 = d1.getTime( );\n" );
			print( "\tdt = ( t1 - t0$q ) / 1000;\n" );
			print( "\tnew_hp = hp$q + Math.round( dt * max_hp$q / 300 - 0.4999 );\n" );
			print( "\tif( new_hp > max_hp$q ) new_hp = max_hp$q;\n" );
			print( "\tw = Math.round( new_hp * 144 / max_hp$q );\n" );
			print( "\tdocument.getElementById( 'tbl$q' ).style.width = w;\n" );
			print( "\tdocument.getElementById( 'dhp$q' ).innerHTML = new_hp + '/' + max_hp$q;\n" );
			print( "\tsetTimeout( 'updateHP$q( );', 2000 );\n" );
			print( "}\n" );
			print( "updateHP$q( );" );
		}

		print( "</script></div>\n" );
	}

	function Rank( )
	{
		if( $this->rank === false )
		{
			$res = f_MQuery( "SELECT rank FROM player_ranks WHERE player_id = $this->player_id" );
			if( !mysql_num_rows( $res ) )
			{
				$this->rank = 0;
			}
			else
			{
				$arr = f_MFetch( $res );
				$this->rank = $arr['rank'];
			}
		}

		return $this->rank;
	}

	function UpdateWeightStr( $tags = true, $st = '' )
	{
		if(  $tags) echo "<script>";
		echo "{$st}document.getElementById( 'wggl' ).innerHTML = '<b>".($this->items_weight/100.0)."/".$this->MaxWeight( )."</b>';";
		if(  $tags) echo "</script>";
	}

	var $permission_array = false;
	function GetPermission( $type )
	{
		global $permissions;

		if( !$this->permission_array )
		{
			$this->permission_array = Array( );

			$arr = f_MFetch( f_MQuery( "SELECT * FROM player_permissions WHERE player_id = {$this->player_id}" ) );
			if( !$arr ) foreach( $permissions as $value ) $this->permission_array[$value] = 0;
			else
			{
				$tm = time( );
				foreach( $permissions as $value )
				{
					$this->permission_array[$value] = max( 0, $arr[$value] - $tm );
				}
			}
		}

		return $this->permission_array[$type];
	}

	function PermissionReason( $type )
	{
		global $permissions;

		$arr = f_MFetch( f_MQuery( "SELECT * FROM player_permissions WHERE player_id = {$this->player_id}" ) );
		if( !$arr ) return "";
		else
		{
			$tm = time( );
			foreach( $permissions as $value ) if(  $value == $type)
			{
				return $arr[$type."_reason"];
			}
			return "";
		}
	}

	function IsShopOwner( $shop_id )
	{
		if( $this->Rank( ) == 1 ) return true; // All shops are owned by admins

		global $CAN_CONTROL_SHOP;
		$res = f_MFetch( f_MQuery( "SELECT owner_id, location, place FROM shops WHERE shop_id = $shop_id" ) );
		if( $res['location'] == 2 && $res['place'] == 101 )
		{
			require_once( 'clan.php' );
			// проверим права на контроль магаза
			if( getPlayerPermitions( $this->clan_id, $this->player_id ) & $CAN_CONTROL_SHOP )
			{
				return ( $res[0] == $this->clan_id );
			}
			else
			{
				return false;
			}
		}
		elseif( $res[0] == $this->player_id )
		{
			return true;
		}

		return false;
	}

	function getAvatar( )
	{
		$res = f_MQuery( "SELECT avatar FROM player_avatars WHERE player_id={$this->player_id}" );
		$arr = f_MFetch( $res );
		if( $arr ) return $arr[0];
		return "f".$this->sex.".jpg";
	}

	function incStatistic( $str, $val = 1 )
	{
		$res = f_MQuery( "SELECT * FROM player_statistics WHERE player_id={$this->player_id}" );
		if( !f_MNum( $res ) ) f_MQuery( "INSERT INTO player_statistics ( player_id ) VALUES ( {$this->player_id} )" );
		f_MQuery( "UPDATE player_statistics SET $str = $str + $val WHERE player_id={$this->player_id}" );
	}
};

?>
