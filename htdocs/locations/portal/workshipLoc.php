<?

if (!$mid_php) die();

include_once( 'items.php' );
include_once( 'guild.php' );
include_once( 'prof_exp.php' );

$stats = $player->getAllAttrNames( );

if ($player->player_id != 173)
{
    echo "<i>Пока что тут пустыно. В углах комнаты стоят коробки, и повсюду валяются доски и инструменты.";
}
else
{
    $slotsPerType = array(2 => 3, 4 => 2, 12 => 1, 10 => 2, 9 => 2);
    $hpOnly = array(10 => true, 9 => true);
    $typeGuilds = array(2 => 104, 4 => 105, 9 => 105, 12 => 109, 10 => 109);


    $guildIds = array(104, 105, 109);
    $atLeastOneGuild = false;
    $myGuilds = array();
    foreach ($guildIds as $guildId) {
        $guild = new Guild( $guildId );
        $myGuilds[$guildId] = $guild->LoadPlayer($player->player_id);
        if ($myGuilds[$guildId]) $atLeastOneGuild = true;
    }

    if ($player->player_id == 173) $myGuilds[104] = true;

    if (!$atLeastOneGuild)
    {
        echo "<br><i>Для работы в Мастерской надо состоять в гильдии кузнецов, ювелиров или портных.</i>";
        return;
    }

    $skill = $player->GetQuestValue(6001);
    $breakChance = ceil(500000 / (100 + $skill)); // 10000 = ломате всегда

    $types = "";
    $help = "Ваш опыт работы в мастерской: <b>" . $player->GetQuestValue(6001) . "</b><br>";
    $help .= "Шанс испортить вещь при вставке руны: <b>" . ($breakChance / 100) . "%</b><br>";
    $help .= "<br>";
    $help .= "Вы можете встраивать следующие руны:<br>";
    foreach ($typeGuilds as $type=>$guild)
    {
        if ($myGuilds[$guild])
        {
            if ($types != "") $types .= ",";
            $types .= $type;
            $help .= "<b>{$item_types[$type]}:</b> ";
            $help .= "$slotsPerType[$type] ";
            if ($hpOnly[$type]) {
                if ($slotsPerType[$type] == 1) $help .= "руна на макс. жизнь.";
                else $help .= "руны на макс. жизнь";
            }
            else {
                if ($slotsPerType[$type] == 1) $help .= "любая руна";
                else $help .= "любые руны";
            }
            $help .= "<br>";
        }
    }

    echo "<br>";

    function getPrice($rune_item_id)
    {
        $item_price = f_MValue("SELECT price FROM items WHERE item_id=$rune_item_id");
        $ret = array();
        $ret[0] = $item_price * 5;
        // вообще оно всегда делится, ceil на всякий случай
        $ret[83469] = ceil($item_price / 2);
        $ret[83470] = ceil($item_price / 4);
        return $ret;
    }

    $priceImgs = array(0 => 'money.gif',
            83469 => 'items/res/bone.png',
            83470 => 'items/res/cherep.png');

    $priceHints = array(0 => 'дублоны',
            83469 => 'кости',
            83470 => 'черепа');

    if (isset($_GET['rune_id']) && isset($_GET['item_id']))
    {
        $item_id = (int)$_GET['item_id'];
        $rune_id = (int)$_GET['rune_id'];

        $ires = f_MQuery("SELECT * FROM items WHERE item_id=$item_id AND decay > 0 AND level <= $player->level");
        $iarr = f_MFetch($ires);
        if (!$iarr) RaiseError("Встраиваем руну в несуществующую вещь ($item_id)");
        $slot = -1;
        for ($i = (int)$slotsPerType[(int)$iarr['type']]; $i >= 1; -- $i) if (!$iarr["rune$i"]) { $slot = $i; break; }
        if ($slot == -1) RaiseError("Встраиваем руну в вещь, у которой нет слотов или все слоты заняты ($item_id)");
        if (!$myGuilds[$typeGuilds[$iarr['type']]]) RaiseError("Пытаемся встроить руну в вещь, не состоя в нужной гильдии ($item_id)");
        if ($player->DropItems($item_id))
        {
            $rune_item_id = f_MValue("SELECT item_id FROM runes WHERE rune_id=$rune_id");
            $rune_attr = f_MValue("SELECT attr_id FROM runes WHERE rune_id=$rune_id");
            $rune_value = f_MValue("SELECT value FROM runes WHERE rune_id=$rune_id");
            if (!$rune_item_id) RaiseError("Попытка использовать несуществующую руну");
            if (!$player->DropItems($rune_item_id))
            {
                $player->AddItems($item_id);
                RaiseError("Игрок попытался испльзовать руну, но потерял ее");
            }
            else
            {
                $ok = true;

                $cost = getPrice($rune_item_id);
                $money_cost = $cost[0];
                unset($cost[0]);

                if (!$player->SpendMoney($money_cost))
                {
                    $ok = false;
                }
                else
                {
                    $ok = $player->DropItemsArr($cost, 36, 0, 2);
                    if (!$ok)
                    {
                        $player->AddMoney($money_cost);
                    }
                }

                if (!$ok)
                {
                    $player->AddItems($rune_item_id);
                    $player->AddItems($item_id);
                    echo "Не хватает ресурсов<br><a href=game.php>Назад</a>";
                }
                else
                {
                    $player->AddToLogPost( 0, -$money_cost, 36, 0, 2 );
                    $player->AddToLogPost( $item_id, -1, 36, 0, 2 );
                    $player->AddToLogPost( $rune_item_id, -1, 36, 0, 2 );

                    // встраиваем
                    $aa = ParseItemStr( $iarr['effect'] );
                    $item_id = copyItem( $item_id, true );
                    $aa[$rune_attr] += $rune_value;

                    $eff = "";
                    foreach( $aa as $a=>$b ) $eff .= ":$a:$b";
                    $eff = substr( $eff, 1 ) . ".";

                    f_MQuery( "UPDATE items SET effect='$eff', rune$slot = $rune_id WHERE item_id=$item_id" );

                    $player->AddItems($item_id);
                    $player->AddToLogPost( $item_id, 1, 36, 0, 2 );
                    $player->AlterQuestValue(6001, 1); // увеличиваем рейтинг

                    echo "<b><font color=darkgreen>Руна успешно встроена</font></b> - <a href=game.php>Назад</a><br>";

                    if (mt_rand(0, 9999) < $breakChance)
                    {
                        f_MQuery( "UPDATE items SET decay = decay - 1 WHERE item_id=$item_id" );
                        echo "<br>В ходе работы прочность вещи уменьшилась.<br>";
                    }
                }
            }
        }
    }
    else if (isset($_GET['item_id']))
    {
        $attributes = array();
        $res = f_MQuery("SELECT * FROM attributes ORDER BY name");

        while ($arr = f_MFetch($res)) if ($arr['attribute_id'] != 555) {
            $attributes[(int)$arr['attribute_id']] = $arr['name'];
        }

        $item_id = (int)$_GET['item_id'];
        $ires = f_MQuery("SELECT i.*, p.number FROM player_items as p inner join items as i on p.item_id = i.item_id where p.player_id = {$player->player_id} AND i.item_id = $item_id AND weared=0 AND decay > 0 AND level <= $player->level");
        $iarr = f_MFetch($ires);
        if (!$iarr) RaiseError("Попытка улучшить вещь, которой у игрока нет");

        echo "<b>Встраиваем руну в $iarr[name]</b> -- <a href=game.php>Назад</a><br>Выберите руну:<br>";

        $addt = "";
        if ($hpOnly[$iarr['type']]) $addt = " AND attr_id = 101";
        $rres = f_MQuery("SELECT r.* FROM player_items as i inner join runes as r on r.item_id = i.item_id and player_id=$player->player_id $addt");

        if (!f_MNum($rres)) echo "<i>У вас нет подходящих рун</i>";
        else {
            echo "<table><tr><td valign=top><script>FLUl();</script>";
            echo "<table>";
            while ($rarr = f_MFetch($rres)) {
                echo "<tr>";

                echo "<td height=100%><script>FUlt();</script>";
                echo "<div style='width:50px;height:50px;'><center><img src='images/items/$rarr[image]'></center></div>";
                echo "<script>FL();</script></td>";

                echo "<td height=100%><script>FUlt();</script>";
                echo "<b>{$attributes[$rarr[attr_id]]} +{$rarr[value]}</b><br>";
                $cost = getPrice($rarr['item_id']);
                $hasRes = true;
                foreach ($cost as $cost_item => $cost_num)
                {
                    $clr = 'darkgreen';
                    if ($cost_item != 0 && $player->NumberItems($cost_item) < $cost_num) { $hasRes = false; $clr = 'darkred'; }
                    if ($cost_item == 0 && $player->money < $cost_num) { $hasRes = false; $clr = 'darkred'; }
                    echo "<img width=11 height=11 title=".$priceHints[$cost_item]." src='images/".$priceImgs[$cost_item]."'> <font color=$clr>$cost_num</font> &nbsp; ";
                }
                echo "<script>FL();</script></td>";

                echo "<td height=100%><script>FUlt();</script>";
                if ($hasRes)
                {
                    echo "<button onclick='location.href=\"game.php?item_id=$item_id&rune_id=$rarr[rune_id]\";' class=ss_btn>Выбрать</button>";
                }
                else
                {
                    echo "<i>Нет ресурсов</i>";
                }
                echo "<script>FL();</script></td>";

                echo "</tr>";
            }
            echo "</table>";
            echo "<script>FLL();</script></td></tr></table>";
        }
    }
    else
    {
        $ires = f_MQuery("SELECT i.*, p.number FROM player_items as p inner join items as i on p.item_id = i.item_id where p.player_id = {$player->player_id} AND type IN ($types) AND weared=0 AND rune1=0 AND decay > 0 AND level <= $player->level");
        if (!f_MNum($ires))
        {
            echo "<i>Нет ни одной вещи для улучшения</i>";
        }
        else
        {
            echo "<b>Выберите вещь, в которую хотите встроить руну:</b><br>";
            echo "<table><tr><td valign=top><script>FLUl();</script>";
            echo "<table>";
            while ($iarr = f_MFetch($ires)) {
                echo "<tr>";

                echo "<td height=100%><script>FUlt();</script>";
                echo "<div style='width:50px;height:50px;'><center><img src='images/items/".itemImage($iarr)."'></center></div>";
                echo "<script>FL();</script></td>";

                echo "<td height=100%><script>FUlt();</script>";
                echo "<b>[{$iarr[number]}] $iarr[name]</b><br>";
                echo itemDescr($iarr, false);
                echo "<script>FL();</script></td>";

                echo "<td height=100%><script>FUlt();</script>";
                echo "<button onclick='location.href=\"game.php?item_id=$iarr[item_id]\";' class=ss_btn>Выбрать</button>";
                echo "<script>FL();</script></td>";

                echo "</tr>";
            }
            echo "</table>";
            echo "<script>FLL();</script></td><td valign=top>$help</td></tr></table>";
        }
    }
}

?>

