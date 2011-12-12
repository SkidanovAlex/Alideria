<?
	header( "Content-Type: text/html; charset=windows-1251" );
	$players = $_GET['players'];
	$orders = $_GET['orders'];
	if ( $players )
		$players = iconv("UTF-8", "WINDOWS-1251", $players);
	include "../functions.php";
	f_MConnect( );
	$write = array( );
	if ( $orders )
    {
		$orders = explode( ",", $orders );
		$oc = count( $orders );
		for ( $i = 0; $i < $oc; $i++ )
			if ( is_numeric( $orders[$i] ) )
				if ( $res = f_MQuery( "SELECT login FROM characters WHERE clan_id = ".(int)$orders[$i] ) )
					while ( $r = f_MFetch( $res ) )
						$players .= ','.$r['login'];
    }
	$pc = 0;
    if ($players)
    {
		$players = explode(",",$players);
        $pc = count($players);
        for ($i = 0; $i < $pc; $i++)
        {
        	$players[$i] = trim( $players[$i] );
			if ( isset( $players[$i][1] ) && !preg_match( "|[^a-z0-9A-Zà-ÿÀ-ß_-]+|is", $players[$i] ) )
			{
				$players[$i] = htmlspecialchars( $players[$i] );
				if ( $res = f_MQuery( "SELECT player_id FROM characters WHERE login = '".$players[$i]."'" ) )
				$player_id = f_MFetch( $res );
				$player_id = (int)$player_id['player_id'];
				if ( isset( $player_id ) && $res = f_MQuery( "SELECT loc,depth,clan_id,level,login,regime,nick_clr FROM characters WHERE player_id = ".$player_id ) )
					while ( $r = f_MFetch( $res ) )
					{
						if ($player_id == 172)
						{
							$r[0] = 2;
							$r[1] = 0;
							$r[5] = 0;
						}
						$write[$i] = $r;
						$ores = f_MQuery( "SELECT * FROM online WHERE player_id!=172 AND player_id={$player_id}" );
						if ( f_MNum( $ores ) )
							$write[$i]['online'] = '1';
						else
							$write[$i]['online'] = '0';
						$ores = f_MQuery( "SELECT combat_id FROM combat_players WHERE player_id!=172 AND player_id={$player_id}" );
						if ( $or = f_MFetch( $ores ) )
							$write[$i]['combat_id'] = $or['combat_id'];
					}
			}
		}
    }
	for ($i = 0; $i < $pc; $i++)
	{
		if ( !isset( $write[$i]) ) continue;
		if ( $write[$i]['online'] == 0 )
			echo '0@';
		else if ( isset ( $write[$i]['combat_id'] ))
			echo $write[$i]['combat_id'].'@';
		else if ( $write[$i]['regime'] != 0 )
			echo $write[$i]['regime'].'@';
		else
			echo '1@';
		echo $write[$i]['level'].'@'.$write[$i]['login'].'@'.$write[$i]['nick_clr'].'@'.$write[$i]['clan_id'].'@'.$write[$i]['loc'].'@';
		if( $write[$i]['loc'] == 1 )
		{
			$res = f_MQuery( "SELECT tile FROM forest_tiles WHERE location={$write[$i]['loc']} AND depth={$write[$i]['depth']}" );
			$arr = f_MFetch( $res );
			if( !$arr ) $arr[0] = 1;
			echo $arr[0].'#';
		}
		else
			echo 	$write[$i]['depth'].'#';
	}
	f_MClose( );
?>	