<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );

f_MConnect( );

include( 'admin_header.php' );
if ($player->player_id==67573) die();

if (isset($_GET['npc_id'])) {
    $npc_id = (int)$_GET['npc_id'];
    $login = addslashes($_GET['login']);
    $player_id = f_MValue("SELECT player_id FROM characters WHERE login='$login'");

    if ($player_id) {
        f_MQuery("INSERT IGNORE INTO quest_editor_grants_npc(player_id, npc_id) VALUES($player_id, $npc_id)");
    }
}

else if (isset($_GET['del_npc'])) {
    $entry_id = (int)$_GET['del_npc'];
    f_MQuery("DELETE FROM quest_editor_grants_npc WHERE entry_id = $entry_id");
}

if (isset($_GET['quest_id'])) {
    $quest_id = (int)$_GET['quest_id'];
    $login = addslashes($_GET['login']);
    $player_id = f_MValue("SELECT player_id FROM characters WHERE login='$login'");

    if ($player_id) {
        f_MQuery("INSERT IGNORE INTO quest_editor_grants_quests(player_id, quest_id) VALUES($player_id, $quest_id)");
    }
}

else if (isset($_GET['del_q'])) {
    $entry_id = (int)$_GET['del_q'];
    f_MQuery("DELETE FROM quest_editor_grants_quests WHERE entry_id = $entry_id");
}

else if (isset($_GET['trigger_low'])) {
    $trigger_low = (int)$_GET['trigger_low'];
    $trigger_hi = (int)$_GET['trigger_high'];
    $login = addslashes($_GET['login']);
    $player_id = f_MValue("SELECT player_id FROM characters WHERE login='$login'");

    if ($player_id) {
        f_MQuery("INSERT INTO quest_editor_grants_triggers(player_id, lower, upper) VALUES($player_id, $trigger_low, $trigger_hi)");
    }
}

else if (isset($_GET['del_t'])) {
    $entry_id = (int)$_GET['del_t'];
    f_MQuery("DELETE FROM quest_editor_grants_triggers WHERE entry_id = $entry_id");
}


?>

<script src='../js/skin.js'></script>
<a href=index.php>На главную</a><br>
<b>Управление доступом к редактору квестов</b><br><br>
<table><tr><td><b><small>Дать права на NPС</small></b></td><td><b><small>Дать права на квест</small></b></td><td><b><small>Дать права на триггeры и значения</small></b></td></tr>
<tr><td valign=top><script>FLUl();</script>
<table>
<form action=admin_quest_grants.php method=get>
<tr><td>Логин персонажа: </td><td><input type=text name=login class=m_btn></td></tr>
<tr><td>NPC Id: </td><td><input type=text class=m_btn name=npc_id></td></tr>
<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=OK></td></tr>
</form>
</table>
<script>FLL();</script></td><td valign=top><script>FLUl();</script>
<table>
<form action=admin_quest_grants.php method=get>
<tr><td>Логин персонажа: </td><td><input type=text name=login class=m_btn></td></tr>
<tr><td>Quest Id: </td><td><input type=text class=m_btn name=quest_id></td></tr>
<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=OK></td></tr>
</form>
</table>
<script>FLL();</script></td><td valign=top><script>FLUl();</script>
<table>
<form action=admin_quest_grants.php method=get>
<tr><td>Логин персонажа: </td><td><input type=text name=login class=m_btn></td></tr>
<tr><td>От: </td><td><input type=text class=m_btn name=trigger_low></td></tr>
<tr><td>До: </td><td><input type=text class=m_btn name=trigger_high></td></tr>
<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=OK></td></tr>
</form>
</table>
<script>FLL();</script></td></tr></table>

<br>

<?

$res = f_MQuery("SELECT DISTINCT(player_id) FROM quest_editor_grants_npc UNION SELECT player_id FROM quest_editor_grants_triggers");

if (!f_MNum($res)) echo "<i>Пока тут пусто как в попе енота</i>";
else {
    echo "<table>";
    while ($arr = f_MFetch($res)) {
        $player_id = $arr[0];
        echo "<tr>";

        echo "<td valign=top><script>FLUl();</script><b>";
        echo f_MValue("SELECT login FROM characters WHERE player_id=$player_id");
        echo "</b><script>FLL();</script></td>";

        echo "<td valign=top><script>FLUl();</script>";
        $nres = f_MQuery("SELECT q.entry_id AS del, q.npc_id, npcs.name FROM quest_editor_grants_npc AS q LEFT OUTER JOIN npcs ON q.npc_id = npcs.npc_id WHERE q.player_id=$player_id");
        if (!f_MNum($nres)) echo "<i>Нет NPC</i>";
        while ($narr = f_MFetch($nres)) {
            $name = $narr['name'];
            if (!$name) $name = 'Несуществующий NPC';
            echo "({$narr[npc_id]}) $name [<a href='admin_quest_grants.php?del_npc={$narr[del]}'>x</a>]<br>";
        }
        echo "<script>FLL();</script></td>";

        echo "<td valign=top><script>FLUl();</script>";
        $qres = f_MQuery("SELECT q.entry_id AS del, q.quest_id, quests.name FROM quest_editor_grants_quests AS q LEFT OUTER JOIN quests ON q.quest_id = quests.quest_id WHERE q.player_id=$player_id");
        if (!f_MNum($qres)) echo "<i>Нет квестов</i>";
        while ($qarr = f_MFetch($qres)) {
            $name = $qarr['name'];
            if (!$name) $name = 'Несуществующий NPC';
            echo "({$qarr[quest_id]}) $name [<a href='admin_quest_grants.php?del_q={$qarr[del]}'>x</a>]<br>";
        }
        echo "<script>FLL();</script></td>";

        echo "<td valign=top><script>FLUl();</script>";
        $tres = f_MQuery("SELECT * FROM quest_editor_grants_triggers WHERE player_id=$player_id");
        if (!f_MNum($tres)) echo "<i>Нет триггеров</i>";
        while ($tarr = f_MFetch($tres))
        {
            echo "Триггеры: $tarr[lower] - $tarr[upper] [<a href='admin_quest_grants.php?del_t={$tarr[entry_id]}'>x</a>]<br>";
        }
        echo "<script>FLL();</script></td>";
        
        echo "</tr>";
    }
    echo "</table>";
}

?>


