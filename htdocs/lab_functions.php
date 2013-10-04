<?

function getNextStepInfo( $lab_id, $x, $y, $z, $dir )
{
    global $player;
    $updown = "";
    $larr = f_MFetch(f_MQuery("SELECT dir, cell_id FROM lab WHERE lab_id=$lab_id AND x=$x AND y=$y AND z=$z"));
    if ($larr[0] == -1 && $z > 0)
    {
        $updown .= "<li><a href=# onclick='query_up();'>��������� �����</a>";
    }
    if ($larr[0] == 1)
    {
       $updown .= "<li><a href=# onclick='query_down();'>���������� ����</a>";
    }
    if ($larr[0] == -1 && $z == 0)
    {
       $updown .= "<li><a href=# onclick='if( confirm( \"����� �� ���������?\" ) ) query_leave();'>�������� ��������</a>";
    }
    $monsters = f_MQuery("SELECT n.mob_id, n.name FROM mobs n INNER JOIN lab_quest_monsters q ON n.mob_id = q.mob_id WHERE q.player_id={$player->player_id} AND q.lab_id=$lab_id AND q.cell_id=$larr[1]");
    $hasMonsters = f_MNum($monsters);
    while ($monster = mysql_fetch_array($monsters))
    {
        $updown .= "<li><a href=# onclick='query_quest_attack();'>������� ��: $monster[1]</a>";
    }

    $npcs = f_MQuery("SELECT n.npc_id, n.name, n.condition_id FROM npcs n INNER JOIN lab_quest_npcs q ON n.npc_id = q.npc_id WHERE q.player_id={$player->player_id} AND q.lab_id=$lab_id AND q.cell_id=$larr[1]");
    while ($npc = mysql_fetch_array($npcs))
    {
        include_once( "phrase.php" );

        if( $npc['condition_id'] == -1 || allow_phrase( $npc['condition_id'], false ) )
        {
            if ($hasMonsters) $updown .= "<li><font color='#4e4e4e'>���������� �: $npc[1]</font>";
            else $updown .= "<li><a href='game_d.php?talk=$npc[0]'>���������� �: $npc[1]</a>";
        }
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
	echo "_( 'addinfo' ).innerHTML = '$updown<br><b>����� ���� ���� ���:</b><br>' + ".substr( $st , 2 ).";";
}

function placeBug()
{
    global $player;
    $lab_id = 1;
    $x = 0; $y = 0;
    $res = f_MQuery("SELECT x, y FROM lab WHERE lab_id = 1 AND z = 0 AND dir = -1 LIMIT 1");
    while ($arr = f_MFetch($res))
    {
        $x = $arr['x'];
        $y = $arr['y'];
    }
    $npc_id = 175; $npc_img = "bug.png";
    $cell_id = f_MValue("SELECT cell_id FROM lab WHERE lab_id = 1 AND x = $x - 1 AND y = $y AND z = 0 LIMIT 1");
    f_MQuery("delete from lab_quest_npcs where player_id={$player->player_id} AND npc_id=$npc_id");
    f_MQuery("insert into lab_quest_npcs values(null, $lab_id, $cell_id, $npc_id, {$player->player_id}, '$npc_img');");
}

function labQuest1Place()
{
    global $player;
    $lab_id = 1;
    $cell_id = f_MValue("SELECT cell_id FROM lab WHERE lab_id = 1 AND z = 0 AND dir = 0 ORDER BY RAND() LIMIT 1");
    $mob_id = 18; $mob_img = "pp3.png";
    $npc_id = 166; $npc_img = "f1n.png";
    f_MQuery("delete from lab_quest_monsters where player_id={$player->player_id} AND mob_id=$mob_id");
    f_MQuery("delete from lab_quest_npcs where player_id={$player->player_id} AND npc_id=$npc_id");
    f_MQuery("insert into lab_quest_monsters values(null, $lab_id, $cell_id, $mob_id, {$player->player_id}, '$mob_img');");
    f_MQuery("insert into lab_quest_npcs values(null, $lab_id, $cell_id, $npc_id, {$player->player_id}, '$npc_img');");
}

function labQuest2Place()
{
    global $player;
    $lab_id = 1;
    $cell_id = f_MValue("SELECT cell_id FROM lab WHERE lab_id = 1 AND z = 0 AND dir = 0 ORDER BY RAND() LIMIT 1");
    $mob_id = 38; $mob_img = "dwarv.png";
    $npc_id = 167; $npc_img = "f1n.png";
    f_MQuery("delete from lab_quest_monsters where player_id={$player->player_id} AND mob_id=$mob_id");
    f_MQuery("delete from lab_quest_npcs where player_id={$player->player_id} AND npc_id=$npc_id");
    f_MQuery("insert into lab_quest_monsters values(null, $lab_id, $cell_id, $mob_id, {$player->player_id}, '$mob_img');");
    f_MQuery("insert into lab_quest_npcs values(null, $lab_id, $cell_id, $npc_id, {$player->player_id}, '$npc_img');");
}

function labQuest3Place()
{
    global $player;
    $lab_id = 1;
    $cell_id = f_MValue("SELECT cell_id FROM lab WHERE lab_id = 1 AND z = 1 AND dir = 0 ORDER BY RAND() LIMIT 1");
    $mask = $player->GetQuestValue(70);
    $mob_id = 10; $mob_img = "pp4.png";
    f_MQuery("delete from lab_quest_monsters where player_id={$player->player_id} AND mob_id=$mob_id");
    if (($mask & 1) == 0)
    {
        f_MQuery("insert into lab_quest_monsters values(null, $lab_id, $cell_id, $mob_id, {$player->player_id}, '$mob_img');");
    }
    $mob_id = 18; $mob_img = "pp3.png";
    f_MQuery("delete from lab_quest_monsters where player_id={$player->player_id} AND mob_id=$mob_id");
    if (($mask & 2) == 0)
    {
        f_MQuery("insert into lab_quest_monsters values(null, $lab_id, $cell_id, $mob_id, {$player->player_id}, '$mob_img');");
    }
    $mob_id = 22; $mob_img = "pp5.png";
    f_MQuery("delete from lab_quest_monsters where player_id={$player->player_id} AND mob_id=$mob_id");
    if (($mask & 4) == 0)
    {
        f_MQuery("insert into lab_quest_monsters values(null, $lab_id, $cell_id, $mob_id, {$player->player_id}, '$mob_img');");
    }
}

function labQuest4Place()
{
    global $player;
    $lab_id = 1;
    $cell_id = f_MValue("SELECT cell_id FROM lab WHERE lab_id = 1 AND z = 2 AND dir = 0 ORDER BY RAND() LIMIT 1");
    $mob_id = 25; $mob_img = "spider.png";
    f_MQuery("delete from lab_quest_monsters where player_id={$player->player_id} AND mob_id=$mob_id");
    f_MQuery("insert into lab_quest_monsters values(null, $lab_id, $cell_id, $mob_id, {$player->player_id}, '$mob_img');");
}

// ����� ��� ������� ����������, lab � players_labs ��������. �� ����� ��������� ����� � ��� ��� �����
function enterLab($lab_id)
{
    global $player;
	$res = f_MQuery( "SELECT cell_id FROM lab WHERE lab_id=$lab_id AND z=0 AND dir=-1" );
	$arr = f_MFetch( $res );
	if( !$arr ) RaiseError( "� ��� ���������� ���� � �������� $lab_id?" );
	f_MQuery( "INSERT INTO player_labs ( player_id, lab_id, cell_id, dir ) VALUES ( {$player->player_id}, $lab_id, $arr[0], 0 )" );

    f_MQuery("UNLOCK TABLES");

    f_MQuery("DELETE FROM lab_quest_monsters WHERE player_id={$player->player_id}");
    f_MQuery("DELETE FROM lab_quest_npcs WHERE player_id={$player->player_id}");
    if ($lab_id == 1)
    {
        placeBug();
    }
    // ����� ��������� �������
    if ($player->HasTrigger(255) && $lab_id == 1)
    {
        labQuest1Place();
    }
    // ����� ������������ �������
    if ($player->HasTrigger(260) && $lab_id == 1)
    {
        labQuest2Place();
    }
    // ����� ���������� ��������
    if ($player->HasTrigger(263) && $lab_id == 1)
    {
        labQuest3Place();
    }
    // 
    if ($player->HasTrigger(268) && $lab_id == 1)
    {
        labQuest4Place();
    }
    f_MQuery("LOCK TABLES lab WRITE"); // ���������� ��� �������, ��� ���� ��� ���������
}

?>
