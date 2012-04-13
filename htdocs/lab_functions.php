<?

function getNextStepInfo( $lab_id, $x, $y, $z, $dir )
{
    global $player;
    $updown = "";
    $larr = f_MFetch(f_MQuery("SELECT dir, cell_id FROM lab WHERE lab_id=$lab_id AND x=$x AND y=$y AND z=$z"));
    if ($larr[0] == -1 && $z > 0)
    {
        $updown .= "<li><a href=# onclick='query_up();'>Подняться вверх</a>";
    }
    if ($larr[0] == 1)
    {
        $updown .= "<li><a href=# onclick='query_down();'>Спуститься вниз</a>";
    }
    $monsters = f_MQuery("SELECT n.mob_id, n.name FROM mobs n INNER JOIN lab_quest_monsters q ON n.mob_id = q.mob_id WHERE q.player_id={$player->player_id} AND q.lab_id=$lab_id AND q.cell_id=$larr[1]");
    $hasMonsters = f_MNum($monsters);
    while ($monster = mysql_fetch_array($monsters))
    {
        $updown .= "<li><a href=#>Напасть на: $monster[1]</a>";
    }

    $npcs = f_MQuery("SELECT n.npc_id, n.name FROM npcs n INNER JOIN lab_quest_npcs q ON n.npc_id = q.npc_id WHERE q.player_id={$player->player_id} AND q.lab_id=$lab_id AND q.cell_id=$larr[1]");
    while ($npc = mysql_fetch_array($npcs))
    {
        if ($hasMonsters) $updown .= "<li><font color='#4e4e4e'>Поговорить с: $npc[1]</font>";
        else $updown .= "<li><a href='game_d.php?talk=$npc[0]'>Поговорить с: $npc[1]</a>";
    }
    $updown = addslashes($updown);
    
	$dxs = Array( -1, 0, 1, 0 );
	$dys = Array( 0, -1, 0, 1 );

	$x += $dxs[$dir];
	$y += $dys[$dir];
	$arr = f_MFetch( $res = f_MQuery( "SELECT cell_id FROM lab WHERE lab_id=$lab_id AND x=$x AND y=$y AND z=$z" ) );
	if( !$arr ) { echo "_( 'addinfo' ).innerHTML = '$updown';"; return; }
	$cell_id = $arr[0];
	$arr = f_MFetch( f_MQuery( "SELECT combat_id FROM lab_combats WHERE cell_id = $cell_id" ) );
	if( !$arr ) { echo "_( 'addinfo' ).innerHTML = '$updown';"; return; }
	$st = "";
	$res = f_MQuery( "SELECT player_id FROM combat_players WHERE combat_id=$arr[0]" );
	while( $arr = f_MFetch( $res ) )
	{
		$plr = new Player( $arr[0] );
		$st .= ' + '.$plr->Nick( ).' + "<br>"';
	}
	echo "_( 'addinfo' ).innerHTML = '$updown<br><b>Перед вами идет бой:</b><br>' + ".substr( $st , 2 ).";";
}

?>
