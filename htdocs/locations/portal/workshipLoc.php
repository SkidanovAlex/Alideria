<?

if (!$mid_php) die();

include_once( 'items.php' );
include_once( 'guild.php' );
include_once( 'prof_exp.php' );

$stats = $player->getAllAttrNames( );

if (!$player->player_id == 173)
{
    echo "<i>���� ��� ��� �������. � ����� ������� ����� �������, � ������� �������� ����� � �����������.";
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
        echo "<br><i>��� ������ � ���������� ���� �������� � ������� ��������, �������� ��� �������.</i>";
        return;
    }

    $types = "";
    $help = "";
    foreach ($typeGuilds as $type=>$guild)
    {
        if ($myGuilds[$guild])
        {
            if ($types != "") $types .= ",";
            $types .= $type;
            $help .= "<b>{$item_types[$type]}:</b> ";
            $help .= "$slotsPerType[$type] ";
            if ($hpOnly[$type]) {
                if ($slotsPerType[$type] == 1) $help .= "���� �� ����. �����.";
                else $help .= "���� �� ����. �����";
            }
            else {
                if ($slotsPerType[$type] == 1) $help .= "����� ����";
                else $help .= "����� ����";
            }
            $help .= "<br>";
        }
    }

    echo "<br>";

    if (isset($_GET['rune_id']) && isset($_GET['item_id']))
    {
        $item_id = (int)$_GET['item_id'];
        $rune_id = (int)$_GET['rune_id'];

        $ires = f_MQuery("SELECT * FROM items WHERE item_id=$item_id");
        $iarr = f_MFetch($ires);
        if (!$iarr) RaiseError("���������� ���� � �������������� ���� ($item_id)");
        $slot = -1;
        for ($i = (int)$slotsPerType[(int)$iarr['type']]; $i >= 1; -- $i) if (!$iarr["rune$i"]) { $slot = $i; break; }
        if ($slot == -1) RaiseError("���������� ���� � ����, � ������� ��� ������ ��� ��� ����� ������ ($item_id)");
        if (!$myGuilds[$typeGuilds[$iarr['type']]]) RaiseError("�������� �������� ���� � ����, �� ������ � ������ ������� ($item_id)");
        if ($player->DropItems($item_id))
        {
            $rune_item_id = f_MValue("SELECT item_id FROM runes WHERE rune_id=$rune_id");
            $rune_attr = f_MValue("SELECT attr_id FROM runes WHERE rune_id=$rune_id");
            $rune_value = f_MValue("SELECT value FROM runes WHERE rune_id=$rune_id");
            if (!$rune_item_id) RaiseError("������� ������������ �������������� ����");
            if (!$player->DropItems($rune_item_id))
            {
                $player->AddItems($item_id);
                LogError("����� ��������� ������� ����, �� ������� ��");
            }
            else
            {
                $ok = true;

                // ����� ���� ��������� �� ����
                // ���� � ������ ��� ���������� ����� ��� �����, �� ��������� ok � false
                // ���� � ������ �� �������� ������������ ���� � ���� ������.

                if (!$ok)
                {
                    $player->AddItems($rune_item_id);
                    $player->AddItems($item_id);
                    echo "�� ������� ��������<br><a href=game.php>�����</a>";
                }
                else
                {
                    $player->AddToLogPost( $item_id, -1, 36, 0, 2 );
                    $player->AddToLogPost( $rune_item_id, -1, 36, 0, 2 );

                    // ����������
                    $aa = ParseItemStr( $iarr['effect'] );
                    $item_id = copyItem( $item_id, true );
                    $aa[$rune_attr] += $rune_value;

                    $eff = "";
                    foreach( $aa as $a=>$b ) $eff .= ":$a:$b";
                    $eff = substr( $eff, 1 ) . ".";

                    f_MQuery( "UPDATE items SET effect='$eff', rune$slot = $rune_id WHERE item_id=$item_id" );

                    $player->AddItems($item_id);
                    $player->AddToLogPost( $item_id, 1, 36, 0, 2 );
                    $player->AlterQuestValue(6001, 1); // ����������� �������

                    echo "<b><font color=darkgreen>���� ������� ��������</font></b> - <a href=game.php>�����</a>";
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
        $ires = f_MQuery("SELECT i.*, p.number FROM player_items as p inner join items as i on p.item_id = i.item_id where p.player_id = {$player->player_id} AND i.item_id = $item_id AND weared=0");
        $iarr = f_MFetch($ires);
        if (!$iarr) RaiseError("������� �������� ����, ������� � ������ ���");

        echo "<b>���������� ���� � $iarr[name]</b> -- <a href=game.php>�����</a><br>�������� ����:<br>";

        $addt = "";
        if ($hpOnly[$iarr['type']]) $addt = " AND attr_id = 101";
        $rres = f_MQuery("SELECT r.*, i.item_id FROM player_items as i inner join runes as r on r.item_id = i.item_id and player_id=$player->player_id $addt");

        if (!f_MNum($rres)) echo "<i>� ��� ��� ���������� ���</i>";
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
                echo "<script>FL();</script></td>";

                echo "<td height=100%><script>FUlt();</script>";
                echo "<button onclick='location.href=\"game.php?item_id=$item_id&rune_id=$rarr[rune_id]\";' class=ss_btn>�������</button>";
                echo "<script>FL();</script></td>";

                echo "</tr>";
            }
            echo "</table>";
            echo "<script>FLL();</script></td></tr></table>";
        }
    }
    else
    {
        $ires = f_MQuery("SELECT i.*, p.number FROM player_items as p inner join items as i on p.item_id = i.item_id where p.player_id = {$player->player_id} AND type IN ($types) AND weared=0 AND rune1=0");
        if (!f_MNum($ires))
        {
            echo "<i>��� �� ����� ���� ��� ���������</i>";
        }
        else
        {
            echo "<b>�������� ����, � ������� ������ �������� ����:</b><br>";
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
                echo "<button onclick='location.href=\"game.php?item_id=$iarr[item_id]\";' class=ss_btn>�������</button>";
                echo "<script>FL();</script></td>";

                echo "</tr>";
            }
            echo "</table>";
            echo "<script>FLL();</script></td><td valign=top>$help</td></tr></table>";
        }
    }
}

?>

