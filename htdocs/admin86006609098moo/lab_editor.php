<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include( '../functions.php' );
include( '../arrays.php' );
include_once( '../items.php' );

f_MConnect( );

include( 'admin_header.php' );

echo "Все изменения в типе лабиринта (цвет пола, потолка, стен на этажах, количество этажей) вступают в силу в 4 утра следующего дня.<br>";
echo "Все изменения в монстрах и вещах вступают в силу с началом следующего часа. При этом уже находящиеся в лабиринте монстры и вещи не исчезают.<br>";
echo "<br>";

echo "<b>Редактировать лабиринт: </b>";
$res = f_MQuery("SELECT lab_id FROM lab_spec ORDER BY lab_id");
while ($arr = f_MFetch($res))
{
    echo "&nbsp;<a href=lab_editor.php?lab={$arr[0]}>{$arr[0]}</a>";
}
echo "<br>";

echo "<b>Посмотреть карту: </b>";
$res = f_MQuery("SELECT lab_id FROM lab_spec ORDER BY lab_id");
while ($arr = f_MFetch($res))
{
    echo "&nbsp;<a href=lab_editor.php?map={$arr[0]}>{$arr[0]}</a>";
}
echo "<br>";
echo "<a href='index.php'>На главную</a>";

if (isset($_GET['lab']))
{
    $lab_id = (int)$_GET['lab'];

    if (isset($_GET['width']))
    {
        $w = (int)$_GET['width']; if ($w < 10) $w = 10; if ($w > 250) $w = 250;
        $h = (int)$_GET['height']; if ($h < 10) $h = 10; if ($h > 250) $h = 250;
        $d = (int)$_GET['depth']; if ($d < 1) $d = 1; if ($d > 50) $d = 50;
        f_MQuery("UPDATE lab_spec SET width=$w, height=$h, depth=$d WHERE lab_id=$lab_id");
        die ("<script>location.href='lab_editor.php?lab={$lab_id}';</script>");
    }

    $stats = f_MFetch(f_MQuery("SELECT * FROM lab_spec WHERE lab_id={$lab_id}"));
    $width = $stats['width'];
    $height = $stats['height'];
    $depth = $stats['depth'];

    echo "<h1>Редактируем лабиринт {$lab_id}</h1>";

    echo "<form action='lab_editor.php' method='GET'>";
    echo "<input type='hidden' name='lab' value='$lab_id'>";
    echo "<table>";
    echo "<tr><td>Ширина: </td><td><input name='width' value='$width'></td></tr>";
    echo "<tr><td>Высота: </td><td><input name='height' value='$height'></td></tr>";
    echo "<tr><td>Глубина: </td><td><input name='depth' value='$depth'></td></tr>";
    echo "<tr><td>&nbsp;</td><td><input value='Обновить' type='submit'></td></tr>";
    echo "</table>";
    echo "</form>";

    echo "<br><br><b>Редактировать этаж:</b> ";
    for ($i = 0; $i < $depth; ++ $i) echo "&nbsp;<a href='lab_editor.php?lab=$lab_id&z=$i'>$i</a>";
    
    if (isset($_GET['z']))
    {
        $level = (int)$_GET['z'];

        if (isset($_GET['item_id']))
        {
            $item_id = (int)$_GET['item_id'];
            $prob = (int)$_GET['prob'];
            f_MQuery("INSERT INTO lab_spec_items (lab_id, z, item_id, prob) VALUES ($lab_id, $level, $item_id, $prob)");
            die ("<script>location.href='lab_editor.php?lab={$lab_id}&z=$level';</script>");
        }
        if (isset($_GET['del_item']))
        {
            $entry_id = (int)$_GET['del_item'];
            f_MQuery("DELETE FROM lab_spec_items WHERE entry_id=$entry_id");
            die ("<script>location.href='lab_editor.php?lab={$lab_id}&z=$level';</script>");
        }
        if (isset($_GET['mob_id']))
        {
            $mob_id = (int)$_GET['mob_id'];
            $prob = (int)$_GET['prob'];
            f_MQuery("INSERT INTO lab_spec_mob (lab_id, z, mob_id, prob) VALUES ($lab_id, $level, $mob_id, $prob)");
            die ("<script>location.href='lab_editor.php?lab={$lab_id}&z=$level';</script>");
        }
        if (isset($_GET['del_mob']))
        {
            $entry_id = (int)$_GET['del_mob'];
            f_MQuery("DELETE FROM lab_spec_mob WHERE entry_id=$entry_id");
            die ("<script>location.href='lab_editor.php?lab={$lab_id}&z=$level';</script>");
        }

        echo "<h2>Редактируем этаж №$level</h2><br>";
        echo "<small>Каждые 10 минут на каждый этаж каждого лабиринта добавляется одна вещь и один монстр, если на этом этаже меньше 50 вещей или монстров соответственно.<br>Вероятность -- это относительное число. Если у одной вещи вероятность 12, а у другой -- 120, то вторая вещь появится в 10 раз вероятнее первой. Абсолютное значение числа не важно.<br><br></small>";

        $ires = f_MQuery("SELECT i.name, l.* FROM items as i RIGHT OUTER JOIN lab_spec_items as l ON i.item_id=l.item_id WHERE l.lab_id=$lab_id AND l.z=$level");
        if (!f_MNum($ires)) echo "<i>На этом этаже вещей еще на указано</i><br>";
        while ($iarr = f_MFetch($ires))
        {
            if (!$iarr['name']) $iarr['name'] = 'Какая-то шняга';
            echo "<b>{$iarr[name]}</b> с шансом <b>{$iarr[prob]}</b> (<a href='lab_editor.php?lab=$lab_id&z=$level&del_item=$iarr[entry_id]'>Удалить</a>)<br>";
        }

        echo "<form action='lab_editor.php' method='GET'>";
        echo "<input type='hidden' name='lab' value='$lab_id'>";
        echo "<input type='hidden' name='z' value='$level'>";
        echo "<table>";
        echo "<tr><td>ID вещи: </td><td><input name='item_id'></td></tr>";
        echo "<tr><td>Вероятность: </td><td><input name='prob'></td></tr>";
        echo "<tr><td>&nbsp;</td><td><input value='Добавить' type='submit'></td></tr>";
        echo "</table>";
        echo "</form>";

        $mres = f_MQuery("SELECT m.name, l.* FROM mobs as m RIGHT OUTER JOIN lab_spec_mob as l ON m.mob_id=l.mob_id WHERE l.lab_id=$lab_id AND l.z=$level");
        if (!f_MNum($mres)) echo "<i>На этом этаже монстров еще на указано</i><br>";
        while ($marr = f_MFetch($mres))
        {
            if (!$marr['name']) $marr['name'] = 'Какая-то шняга';
            echo "<b>{$marr[name]}</b> с шансом <b>{$marr[prob]}</b> (<a href='lab_editor.php?lab=$lab_id&z=$level&del_mob=$marr[entry_id]'>Удалить</a>)<br>";
        }

        echo "<form action='lab_editor.php' method='GET'>";
        echo "<input type='hidden' name='lab' value='$lab_id'>";
        echo "<input type='hidden' name='z' value='$level'>";
        echo "<table>";
        echo "<tr><td>ID монстра: </td><td><input name='mob_id'></td></tr>";
        echo "<tr><td>Вероятность: </td><td><input name='prob'></td></tr>";
        echo "<tr><td>&nbsp;</td><td><input value='Добавить' type='submit'></td></tr>";
        echo "</table>";
        echo "</form>";
    }
}

else if (isset($_GET['map']))
{
    $lab_id = (int)$_GET['map'];
    echo "<h1>Карта лабиринта {$lab_id}</h1>";
    $stats = f_MFetch(f_MQuery("SELECT * FROM lab_spec WHERE lab_id={$lab_id}"));
    $width = $stats['width'];
    $height = $stats['height'];
    $lres = f_MQuery("SELECT DISTINCT(z) FROM lab WHERE lab_id={$lab_id} ORDER BY z");
    if (!f_MNum($lres)) echo "Этот лабиринт не существует в Алидерии";
    while ($larr = f_MFetch($lres))
    {
        $level = $larr[0];
        echo "<b>Уровень {$level}:</b><br>";
        $res = f_MQuery("SELECT * FROM lab WHERE lab_id={$lab_id} AND z={$level} ORDER BY y DESC, x");
        if (f_MNum($res) != $width * $height) echo "На этаже не {$width}x{$height} клеток. Что-то тут не ладно :о(<br>";
        else
        {
            echo "<table cellspacing=0 cellpadding=0>";
            for ($i = 0; $i < $height; ++ $i)
            {
                echo "<tr>";
                for ($j = 0; $j < $width; ++ $j)
                {
                    $arr = f_MFetch($res);
                    $clr = "white";
                    if ($arr['tex']) $clr = 'black';
                    else if ($arr['dir'] == -1) $clr = 'lime';
                    else if ($arr['dir'] == 1) $clr = 'red';
                    echo "<td style='background-color: $clr'>";
                    echo "<img src='empty.gif' width='10' height='10'>";
                    echo "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }

        echo "<br><br>";
    }
}

?>

