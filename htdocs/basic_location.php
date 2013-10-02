<?

if( !$mid_php )
	die( );

$place = $player->depth;
$loc = $player->location;

// Действия

if( !isset( $HTTP_GET_VARS['dir'] ) ) $dir = -1;
else
{
	$dir = $HTTP_GET_VARS['dir'];
	settype( $dir, 'integer');
}

if( $dir != -1 && $player->regime == 0 )
{
	$ok = true;

	if( $ok )
	{
		if( isset( $HTTP_GET_VARS[tloc] ) ) $tloc = $HTTP_GET_VARS[tloc];
		else $tloc = $loc;
	
		settype( $tloc, "integer" );
	
		$res1 = f_MQuery( "SELECT * FROM loc_links WHERE loc1 = $loc AND depth1 = $place AND loc2 = $tloc AND depth2 = $dir" );
		$res2 = f_MQuery( "SELECT * FROM loc_links WHERE loc2 = $loc AND depth2 = $place AND loc1 = $tloc AND depth1 = $dir" );
		if( mysql_num_rows( $res1 ) || mysql_num_rows( $res2 ) )
		{
			if( $tloc != $loc )
			{
				if( $noob == 14 && $dir == 0 )
				{
					f_MQuery( "UPDATE noob SET a=15, b=0 WHERE player_id={$player->player_id}" );
					$noob = 15; $noob_param = 0;
				}

				$player->SetLocation( $tloc );
				if ($tloc==3 && $dir==0)
					$dir=7;
				$player->SetDepth( $dir );
				die( "<script>location.href='game.php';</script>" );
			}
			else
			{
				if( $player->SetDepth( $dir ) )
				{
					if( $player->level == 1 && $dir == 9 && $tloc == 2 )
					{
						include_once( 'player_noobs.php' );
						echo "<script>";
						PingNoob( 0 );
						echo "</script>";
					}
					if( $player->level == 1 && $dir == 23 && $tloc == 2 )
					{
						include_once( 'player_noobs.php' );
						echo "<script>";
						PingNoob( 1 );
						echo "</script>";
					}
					if( $player->level == 1 && $dir == 5 && $tloc == 2 )
					{
						include_once( 'player_noobs.php' );
						echo "<script>";
						PingNoob( 5 );
						echo "</script>";
					}

					if( $noob == 4 && $dir == 9 )
					{
						f_MQuery( "UPDATE noob SET a=5, b=0 WHERE player_id={$player->player_id}" );
						$noob = 5; $noob_param = 0;
					}
					if( $noob == 12 && $dir == 0 )
					{
						f_MQuery( "UPDATE noob SET a=13, b=0 WHERE player_id={$player->player_id}" );
						$noob = 13; $noob_param = 0;
					}
					if( $noob == 13 && $dir == 5 )
					{
						f_MQuery( "UPDATE noob SET a=14, b=0 WHERE player_id={$player->player_id}" );
						$noob = 14; $noob_param = 0;
					}
/*					if( $noob == 5 && $dir == 23 )
					{     // старая версия, где надо в магаз оружия и одежды
						f_MQuery( "UPDATE noob SET a=6, b=0 WHERE player_id={$player->player_id}" );
						$noob = 6; $noob_param = 0;
					} */

					$loc = $tloc;
					$place = $dir;
					$depth = $dir;
					if( $loc == 0 && $depth <= 20 || $loc == 5 && $depth == 1 )
						die( "<script>location.href='game.php';</script>" );
				}
			}
		}
	}
}

// Действия - конец

$res = f_MQuery( "SELECT text, title, img, status FROM loc_texts WHERE loc=$loc AND depth=$place" );
if( mysql_num_rows( $res ) )
{
	$arr = f_MFetch( $res );
	if ($loc == 2 && $depth == 56 && !$player->HasTrigger(12209))
	{
		$arr[0] = "Первый снег невесомыми хлопьями кружил в воздухе. Он уже не таял у самой земли, а ложился ровным пушистым слоем. Местами уже встречались приличные сугробы. Река, плавно текущая еще вчера, за одну ночь спряталась под ледяной панцирь. В ее зеркальной глади, чуть припорошенной снегом, отражалась большая елка.";
		$arr[1]="Ёлка";
		$arr['img']="";
	}
	if( $arr['img'] == '' )  $arr['img'] = 'no_logo.gif';

	//if( $loc == 5 && ( $depth != 1 ) )
	// @dmitry: 
	if( $loc == 5 && $depth == 0 )
	{
		$no_rest = true;
		print( "<table cellspacing=0 cellpadding=0 width=100%><tr><td valign=top>");
		include( "locations/portal/entr.php" );
	}
	else if( $loc == 2 && $depth == 0 && !$_COOKIE['text_capital'] ) // вставить сюда нормальное условие что лока с картинкой и что у игрока эта абилка не выключена
	{
		$no_rest = true;
		print( "<table cellspacing=0 cellpadding=0 width=100%><tr><td valign=top>");
		if( true) include( "capital_8m.php" ); //include( "capital_9m.php" );
		else include( "capital.php" ); //  include( "capital.php" );
	}
	else if( $loc == 2 && $depth == 50 )
	{
		$no_rest = true;
		print( "<table cellspacing=0 cellpadding=0 width=100%><tr><td valign=top>");
		include( "clan_camp.php" );
	}
	else if( $loc == 2 && $depth == 3 )
	{
		$no_rest = false;
		print( "<table cellspacing=0 cellpadding=0 width=100%><tr><td valign=top>");
		include( "locations/almost_forest.php" );
	}
	else
	{
		print( "<table width=100%><colgroup><col width=120><col width=*><col width=200><tbody><tr><td valign=top>");

		ScrollLightTableStart();
		if( $arr[img]!="no_logo.gif" && file_exists( 'images/locations/'.str_replace('.jpg','_.jpg',$arr[img]) ) )
			echo "<img onclick=\"window.open('images/locations/".str_replace('.jpg','_.jpg',$arr[img])."', '_blank', 'width=700,height=528,toolbar=no,status=no,scrollbars=no,menubar=no,resizable=no')\" style='cursor:pointer' src=images/locations/$arr[img] width=170 height=127>";
		else
			echo "<img src=images/locations/$arr[img] width=170 height=127>";
		ScrollLightTableEnd();

		print( "</td><td valign=top>" );

		print( "<center><b>{$loc_names[$loc]}, {$arr[1]}</b><hr width=40% color=gray size=1></center>" );
		$arr[0] = str_replace( "\n", "<br>", $arr[0] );
		if( $loc == 2 && $depth == 0 ) $arr[0] .= "<br><br><li><a href=game.php?graph_mode>Перейти в графический режим</a>";
		print( "<div align=justify>$arr[0]</div>" );
		print( "</td><td valign=top>" );

		ScrollLightTableStart();

		echo "<table width=190>";

		$res2 = f_MQuery( "SELECT loc_links.loc2, loc_links.depth2, loc_texts.title2 FROM loc_links, loc_texts WHERE loc1 = $loc AND depth1 = $place AND loc = loc2 AND depth = depth2 ORDER BY loc2, depth2" );
		while( $arr2 = f_MFetch( $res2 ) )
		{
			echo "<tr><td>";
			if( $noob == 6 ) echo "<li><a href=# onclick='alert( \"Сначала следует купить все, что перечислила Астаниэль\" );'>$arr2[2]</a>";
			else if( $noob ) print( "<li><a href=# onclick='alert( \"$be_noob\" );'>$arr2[2]</a>" );
			else print( "<li><a href=game.php?dir=$arr2[1]&tloc=$arr2[0]>$arr2[2]</a>" );
			echo "</td></tr>";
		}

		$res2 = f_MQuery( "SELECT loc_links.loc1, loc_links.depth1, loc_texts.title2 FROM loc_links, loc_texts WHERE loc2 = $loc AND depth2 = $place AND loc = loc1 AND depth = depth1 ORDER BY loc1, depth1" );
		while( $arr2 = f_MFetch( $res2 ) )
		{
			echo "<tr><td>";
			if( $noob == 6 ) echo "<li><a href=# onclick='alert( \"Сначала следует купить все, что перечислила Астаниэль\" );'>$arr2[2]</a>";
			else if( $noob == 12 && $arr2[1] == 0 ) print( "<li><span style='position:relative;top:0px;left:0px;' id=n_go_to_main_street><a href=game.php?dir=$arr2[1]&tloc=$arr2[0]>$arr2[2]</a></span>" );
			else if( $noob == 14 && $arr2[1] == 0 && $arr2[0] == 0 ) print( "<li><span style='position:relative;top:0px;left:0px;' id=n_go_to_dungeon><a href=game.php?dir=$arr2[1]&tloc=$arr2[0]>$arr2[2]</a></span>" );
			else if( $noob ) print( "<li><a href=# onclick='alert( \"$be_noob\" );'>$arr2[2]</a>" );
			else print( "<li><a href=game.php?dir=$arr2[1]&tloc=$arr2[0]>$arr2[2]</a>" );
			echo "</td></tr>";
		}
		
		//if( $player->location == 5 && $player->depth == 0 ) echo "<tr><td><li><a href='javascript:leave_portal();'>В Реальный Мир</a></td></tr>";

		echo "</table>";

		ScrollLightTableEnd();

	}
	print( "</td></tr></table>" );

	$status = $arr[status];
}
else RaiseError( "Несуществующая локация: $loc : $place" );

?>
