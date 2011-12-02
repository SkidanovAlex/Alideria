<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">

<?

include_once( '../functions.php' );
include_once( '../arrays.php' );

f_MConnect( );

include( 'admin_header.php' );

function create_select( $nm, $arr, $val )
{
	$st = "<select name='$nm'>";
	
	foreach( $arr as $key=>$value )
	{
		$st .= "<option value=$key";
		if( $key == $val ) $st .= " selected";
		$st .= ">$value\n" ;
	}
	
	$st .= '</select>';
	
	return $st;
}

$ordertype = Array(0=>'по гос.цене', 1=>'по количеству');

echo "<a href=index.php>На главную</a><br><br>";

if( isset( $HTTP_GET_VARS['id'] )) $id=(int)$HTTP_GET_VARS['id'];
else $id=0;
if( isset( $HTTP_GET_VARS['srt'] )) $srt=(int)$HTTP_GET_VARS['srt'];
else $srt=0;

echo "<table>";
echo "<form action='admin_orden_items.php' method=get>";
echo "<tr><td>ID ордена:</td><td><input type=text name=id value=".$id."></td></tr>";
echo "<tr><td>Сортировать по убыванию:</td><td>".create_select('srt', $ordertype, $srt)."</td></tr>";
echo "<tr><td><input type=submit value=Ок></td></tr>";
echo "</form>";
echo "</table><br><br>";



if( isset( $HTTP_GET_VARS['id'] ))
{
	if (isset ( $HTTP_GET_VARS['item_id'] ) && isset ( $HTTP_GET_VARS['item_plc'] ) && isset ( $HTTP_GET_VARS['item_num'] ))
	{
		$iid = (int)$HTTP_GET_VARS['item_id'];
		$iplc = (int)$HTTP_GET_VARS['item_plc'];
		$inum = (int)$HTTP_GET_VARS['item_num'];
		if ($iid == 0) // Изымаем монеты
		{
			$inumm = f_MValue("SELECT money FROM clans WHERE clan_id=$id");
			if ($inum > $inumm || $inum <= 0)
			{
				echo "<script>alert('В казне ордена всего ".$inumm." монет. Вы патаетесь изъять ".$inum.".');</script>";
			}
			else
			{
				f_MQuery("LOCK TABLE clans WRITE");
				f_MQuery("UPDATE clans SET money=money-$inum WHERE clan_id=$id");
				f_MQuery("UNLOCK TABLES");
				f_MQuery("INSERT INTO clan_log (clan_id, action, arg0, arg1, arg2, time, player_id) VALUES ($id, 100, 0, $inum, 0, ".time().", $player->player_id)");
				echo "<script>alert('Вы изъяли у $id-го ордена $inum монет');</script>";
			}
		}
		else // Изымаем предметы
		{
			$inumis = f_MValue("SELECT number FROM clan_items WHERE clan_id=$id AND item_id=$iid AND color=$iplc");
			if ($inum > $inumis || $inum <= 0)
			{
				echo "<script>alert('На этой полке всего ".$inumis." предметов. Вы патаетесь изъять ".$inum.".');</script>";
			}
			else
			{
				f_MQuery("LOCK TABLE clan_items WRITE");
				if ($inum < $inumis)
					f_MQuery("UPDATE clan_items SET number = number - $inum WHERE clan_id=$id AND item_id=$iid AND color=$iplc");
				else
					f_MQuery("DELETE FROM clan_items WHERE clan_id=$id AND item_id=$iid AND color=$iplc");
				f_MQuery("UNLOCK TABLES");
				f_MQuery("INSERT INTO clan_log (clan_id, action, arg0, arg1, arg2, time, player_id) VALUES ($id, 100, $iid, $inum, $iplc, ".time().", $player->player_id)");
				echo "<script>alert('Вы изъяли у $id-го ордена $inum штук предмета $iid');</script>";
			}
		}
	}
	$srt_str = 'items.price';
	if (isset( $HTTP_GET_VARS['srt'] ))
	{
		$srt = (int)$HTTP_GET_VARS['srt'];
		if ($srt == 1) $srt_str = 'clan_items.number';
	}
	$id = (int)$HTTP_GET_VARS['id'];
	$res = f_MQuery("SELECT items.name, items.price, clan_items. * FROM items, clan_items WHERE clan_items.item_id = items.item_id AND clan_items.clan_id =".$id." ORDER BY ".$srt_str." DESC");
	echo "ID клана: <b>".$id."</b>&nbsp;В казне <b>".f_MValue("SELECT money FROM clans WHERE clan_id=".$id)."</b> монет<br>";
	echo "<form action='admin_orden_items.php' method=get>";
	echo "<input type=text name=id value=".$id." style='display:none'><input type=text name=srt value=".$srt." style='display:none'>";
	echo "<input type=text name=item_id value=0 style='display:none'><input type=text name=item_plc value=-1 style='display:none'><input type=text name=item_num value=0><input type=submit value=Изъять монеты>";
	echo "</form><br>";
	echo "<table border=1>";
	$clrs = Array( "красная", "пурпурная", "желтая", "синяя", "зеленая" );
	echo "<tr><td>ID предмета</td><td>Название предмета</td><td>Количество</td><td>Полка</td><td>Гос. цена</td><td>Штрафуем</td></tr>";
	
	while ($arr = f_MFetch($res))
	{
		echo "<tr><td>$arr[3]</td><td>$arr[0]</td><td>$arr[4]</td><td>".$clrs[$arr[5]]."</td><td>$arr[1]</td>";
		echo "<td><form action='admin_orden_items.php' method=get>";
		echo "<input type=text name=id value=".$id." style='display:none'><input type=text name=srt value=".$srt." style='display:none'>";
		echo "<input type=text name=item_id value=$arr[3] style='display:none'><input type=text name=item_plc value=$arr[5] style='display:none'><input type=text name=item_num value=0><input type=submit value=Изъять>";
		echo "</form></td>";
		echo "</tr>";
	}
	echo "</table><br>";
}

f_MClose( );

?>

