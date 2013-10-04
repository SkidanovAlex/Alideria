<?

if (!$mid_php) die();

include_once( 'guild.php' );

function PlayerHasRes($player, $res) {
    foreach ($res as $item_id => $num) {
        if ($player->NumberItems($item_id) < $num) {
            return false;
        }
    }
    return true;
}

// квест Меж Двух дубов
$task_names = array("Золотой Слиток", "Лепестки", "Драгоценный Камень");
$task_help_with = array("Обработкой Золота", "Изготовлением Лепестков", "Зачарованием Родолита", "Изготовлением Браслета Весны");
$task_res = array(array(30 => 1), array(15 => 10, 75594 => 5), array(105 => 1));
$task_cm = array("Обработан", "Готовы", "Зачарован");
$task_guild = array(104, 109, 106, 105);

for ($i = 0; $i < 4; ++ $i) {
    if ($i < 3) {
        $res = $task_res[$i];
    }
    else $res = array();
    $guild = new Guild($task_guild[$i]);
    if ($guild->LoadPlayer($player->player_id)) {
        $playerIds = f_MQuery("SELECT characters.player_id FROM characters INNER JOIN online ON characters.player_id=online.player_id WHERE loc=2 AND depth=12");

        while ($playerIdRow = f_MFetch($playerIds)) {
            $target = new Player($playerIdRow[0]);
            $ok = true;
            if ($target->HasTrigger(280 + $i)) $ok = false;
            else if (!PlayerHasRes($target, $res)) $ok = false;
            else if ($i == 3 && (!$target->HasTrigger(280) || !$target->HasTrigger(281) || !$target->HasTrigger(282))) $ok = false;

            if ($ok){
                if (isset($_GET['help_with']) && $_GET['help_with'] == $i && $_GET['help_whom'] == $target->player_id) {
                    $target->SetTrigger(280 + $i);
                    foreach ($res as $item_id => $num) {
                        $target->DropItems($item_id, $num);
                    }
                    if ($i == 3) {
                        $target->SetTrigger(279, 0);
                        $target->AddItems(87357);
                        $target->syst2("Игрок {$player->login} помогает вам изготовить <b>Браслет Весны</b>.");
                        $qres = f_MQuery( "SELECT * FROM player_quest_parts WHERE player_id={$target->player_id} AND quest_part_id = 315" );
                        if( !mysql_num_rows( $qres ) )
                        {
                            $target->syst2( "Информация о квесте <b>Меж двух дубов</b> обновлена." );
                            f_MQuery( "INSERT INTO player_quest_parts VALUES ( {$target->player_id}, 315 )" );
                        }
                    }
                    else $target->syst2("Игрок {$player->login} помогает вам с {$task_help_with[$i]}");
                }
                else {
                    echo "<a href=game.php?help_with=$i&help_whom={$target->player_id}>Помочь</a> игроку <script>document.write({$target->Nick2()});</script> с {$task_help_with[$i]}<br>";
                }
            }
        }
    }
}

if ($player->HasTrigger(279) && !$player->HasTrigger(283)) {
    echo "<br>";
    echo "<b>Работа над браслетом весны:</b><br>";
    echo "<table><tr><td><script>FLUl();</script><table>";

    for ($i = 0; $i < 3; ++ $i) {
        $name = $task_names[$i];
        $res = $task_res[$i];
        $cm = $task_cm[$i];

        echo "<tr><td><script>FUlt();</script>$name<script>FL();</script></td><td><script>FUlt();</script>";
        if ($player->HasTrigger(280 + $i)) {
            echo "<b><font color=darkgreen>$cm</font></b>";
        }
        else if (PlayerHasRes($player, $res)) {
            echo "<b>Ждем мастера</b>";
        }
        else {
            echo "<b><font color=darkred>Нет ресурсов</font></b>";
        }
        
        echo "<script>FL();</script></td></tr>";
    }
    echo "<tr><td><script>FUlt();</script>Браслет Весны<script>FL();</script></td><td><script>FUlt();</script>";
    if (!$player->HasTrigger(280) || !$player->HasTrigger(281) || !$player->HasTrigger(282)) {
        echo "<b><font color=darkred>Нет составных частей</font></b>";
    }
    else {
        echo "<b>Ждем мастера</b>";
    }
    echo "<script>FL();</script></td></tr>";

    echo "</table><script>FLL();</script></td></tr></table>";
}


?>
