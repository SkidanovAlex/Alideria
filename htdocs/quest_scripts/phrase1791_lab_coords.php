<?

if (!$mid_php) die ();

function PrintDesc($level) {
    $res = f_MQuery("SELECT * FROM lab WHERE lab_id=1 AND dir=1 AND z=$level");
    $arr = f_MFetch($res);
    echo "<b>{$arr[x]}x{$arr[y]}</b>";
}

?>

<b>���</b>: ������� �� �������� ����������. ������, ���� ���������, ��� ������� ��������.<br>
������� �� ������ ����: <? PrintDesc(0); ?><br>
������� �� ������ ����: <? PrintDesc(1); ?><br>

<?

$print = false;
$what = '';
if ($player->HasTrigger(255))
{
    $print = true;
    $npc_id = 166;
    $mob_ids = "(18)";
    $what = "� ��� � ������� ����� ����� �������� �� �����������";
}
if ($player->HasTrigger(260))
{
    $print = true;
    $npc_id = 167;
    $mob_ids = "(38)";
    $what = "� ��� � ������� ����� ����� �������� �� �����������";
}
if ($player->HasTrigger(263))
{
    $print = true;
    $npc_id = -1;
    $mob_ids = "(10, 18, 22)";
    $what = "� ��� � ������� ����� �������������� ���� � ���-�� ��������� �� �����������";
}
if ($player->HasTrigger(268))
{
    $print = true;
    $npc_id = -1;
    $mob_ids = "(25)";
    $what = "� ��� � ������� ����� ��������� ����� �� �����������";
}

if ($print) {
    $cell_id = f_MValue("SELECT cell_id FROM lab_quest_monsters WHERE player_id={$player->player_id} AND mob_id IN $mob_ids UNION SELECT cell_id FROM lab_quest_npcs WHERE player_id={$player->player_id} AND npc_id=$npc_id");
    if ($cell_id) {
        $res = f_MQuery("SELECT * FROM lab WHERE lab_id=1 AND cell_id=$cell_id");
        $arr = f_MFetch($res);
        echo "<br>";
        echo $what . " <b>{$arr[x]}x{$arr[y]}</b> �� <b>{$arr[z]}</b>-�� �����";
        echo "<br>";
    }
}

?>

<ul>
<?

if ($player->HasTrigger(284)) echo "<li> <a href='game.php?phrase=2558'>�������, ����� �������!</a>";
else echo "<li> <a href='game.php?phrase=2559'>��������, ��� �������� �����-�� �������?</a>";

?>
</ul>

