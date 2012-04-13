<?

if( !isset( $mid_php ) ) die( );

echo "<b>Логи Ордена</b> - <a href=game.php?order=main>Назад</a><br>";

if( 0 == ( getPlayerPermitions( $clan_id, $player->player_id ) & $CAN_WATCH_LOG ) )
{
	echo( "У вас нет прав на работу с этими возможностями Ордена. Если они нужны вам, обратитесь к Главе или его заместителям.<br><a href=game.php?order=main>Назад</a>" );
	return;
}

$page = $_GET['p'];
settype( $page, 'integer' );
if( $page < 0 ) $page = 0;
$start = $page * 20;

$filter = "";
$lnk = "";
$pid = -1;
$act = -1;
if( isset( $_GET['pid'] ) ) 
{
	$pid = (int)$_GET['pid'];
	if( $pid != -1 ) { $filter .= " AND player_id=$pid"; $lnk .= "&pid=$pid"; }
}
if ($pid==-1 && isset($_GET['pname']))
{
	$pid = f_MValue("SELECT player_id FROM characters WHERE login='".$_GET['pname']."'");
	if ($pid > 0)
	{
		$filter .= " AND player_id=$pid"; $lnk .= "&pid=$pid";
	}
	else
		$pid = -1;
}
if( isset( $_GET['act'] ) )
{
	$act = (int)$_GET['act'];
	if( $act != -1 ) { $filter .= " AND action=$act"; $lnk .= "&act=$act"; }
}

$res = f_MQuery( "SELECT * FROM clan_log WHERE clan_id=$clan_id $filter ORDER BY entry_id DESC LIMIT $start, 21" );

if( f_MNum( $res ) == 0 )
{
	echo "<i>Логи пусты.</i>";
	return;
}

echo "<table><tr><td>";

echo "<table style='border:1px solid black'>";

$i = 0;
while( $i < 20 && $arr = f_MFetch( $res ) )
{                  
	++ $i;
	$plr = new Player( $arr['player_id'] );
	echo "<tr><td>".date( "d.m.Y H:i", $arr['time'] )."</td><td><script>document.write( ".$plr->Nick( )." );</script></td><td>";
	if( $arr['action'] == 1 )
	{
		if( $arr['arg1'] == 1 ) echo "Здание &laquo;".$buildings[$arr['arg0']]."&raquo; добавлено в очередь";
		else echo "Здание &laquo;".$buildings[$arr['arg0']]."&raquo; удалено из очереди";
	}
	else if( $arr['action'] == 2 )
	{
		if( $arr['arg0'] == 1 ) echo 'Добавлена новая вкладка на страницу Ордена';
		if( $arr['arg0'] == -1 ) echo 'Удалена вкладка со страницы Ордена';
		if( $arr['arg0'] == 0 ) echo 'Изменена одна из вкладок на странице Ордена';

	}
	else if( $arr['action'] == 3 )
	{
		if( $arr['arg0'] == 1 && $arr['arg1'] == 1 ) echo "Добавлено или переименовано звание $arr[arg2]";
   		if( $arr['arg0'] == 1 && $arr['arg1'] == 2 ) echo "Добавлена или переименована должность $arr[arg2]";
		if( $arr['arg0'] == -1 && $arr['arg1'] == 1 ) echo "Удалено звание $arr[arg2]";
   		if( $arr['arg0'] == -1 && $arr['arg1'] == 2 ) echo "Удалена должность $arr[arg2]";
	}
	else if( $arr['action'] == 4 )
	{
		if( $arr['arg0'] == 1 ) echo "Права звания $arr[arg1] изменены с <a href=clan_permitions.php?p=$arr[arg2] target=_blank>таких</a> на <a href=clan_permitions.php?p=$arr[arg3] target=_blank>такие</a>";
		if( $arr['arg0'] == 2 ) echo "Права должности $arr[arg1] изменены с <a href=clan_permitions.php?p=$arr[arg2] target=_blank>таких</a> на <a href=clan_permitions.php?p=$arr[arg3] target=_blank>такие</a>";
	}
	else if( $arr['action'] == 5 )
	{
		$trg = new Player( $arr['arg0'] );
		echo "Игроку <script>document.write( ".$trg->Nick( )." );</script> установлено звание: $arr[arg1], должность: $arr[arg2], баланс: $arr[arg3], ОУ: $arr[arg4]";
	}
	else if( $arr['action'] == 6 )
	{
		$clrs = Array( "красная", "пурпурная", "желтая", "синяя", "зеленая" );
		$arr2 = f_MFetch( f_MQuery( "SELECT name FROM items WHERE item_id=$arr[arg0]" ) );
		if( $arr[arg1] > 0 )
			echo "Добавлено на склад: [$arr[arg1]] $arr2[0] - {$clrs[$arr[arg2]]} полка";
		else
		{
			$arr[arg1] = - $arr[arg1];
			echo "Взято со склада: [$arr[arg1]] $arr2[0] - {$clrs[$arr[arg2]]} полка";
		}
	}
	else if( $arr['action'] == 7 )
	{
		$number = $arr['arg0'];
		if( $number > 0 ) echo "В казну добавлено $number дублонов";
		else
		{
			$number = - $number;
			echo "Из казны взято $number дублонов";
		}
	}
	else if( $arr['action'] == 8 )
	{
		if( $arr['arg0'] == 0 ) echo "Восстановлено здоровье в Столовой";
		else echo "Приготовлено $arr[arg1] единиц еды";
	}
	else if( $arr['action'] == 9 )
	{
		$shelves2 = Array( 'Красной', 'Пурпурной', 'Желтой', 'Синей', 'Зеленой' );
		if( $arr['arg0'] == 1 ) echo "Изменено название для {$shelves2[$arr[arg1]]} полки";
		if( $arr['arg0'] == 2 ) echo "Удалено название для {$shelves2[$arr[arg1]]} полки";

	}
	else if( $arr['action'] == 10 )
	{
		$pres = f_MQuery( "SELECT login FROM characters WHERE player_id=$arr[arg0]" );
		$parr = f_MFetch( $pres );
		if( $arr['arg1'] == 1 ) echo "Игрок $parr[0] принят в Орден";
		if( $arr['arg1'] == 2 ) echo "Игрок $parr[0] отчислен из Ордена";

	}
	else if ( $arr['action'] == 100 )
	{
		if ($arr[arg0] == 0)
		{
			echo "Изъято в пользу государства: $arr[arg1] монет";
		}
		else
		{
			$clrs = Array( "красная", "пурпурная", "желтая", "синяя", "зеленая" );
			$arr2 = f_MFetch( f_MQuery( "SELECT name FROM items WHERE item_id=$arr[arg0]" ) );
			echo "Изъято в пользу государства: [$arr[arg1]] $arr2[0] - {$clrs[$arr[arg2]]} полка";
		}
	}

	else echo "НЕИЗВЕСТНОЕ ДЕЙСТВИЕ! ПРЕДУПРЕДИТЕ ОРДЕН НАЧАЛА!";
	echo "</td></tr>";
}

echo "</table>";

$arr = f_MFetch( $res );
                       
echo "<table width=100%><tr><td align=left>";
if( $page > 0 ) echo "<a href=game.php?order=log&p=".($page-1)."$lnk>Предыдущая страница</a> ";
else echo "Предыдущая страница";
echo "</td><td align=right>";
if( $arr ) echo "<a href=game.php?order=log&p=".($page+1)."$lnk>Следующая страница</a> ";
else echo "Следующая страница";
echo "</td></tr></table>";

echo "</td><td valign=top>";

echo "<b>Фильтр:</b><br>";
echo "<form action=game.php method=get><input type=hidden name=order value=log>";
echo "<table>";

$pids = array( -1 => "Все игроки" );
$res = f_MQuery( "SELECT player_id, login FROM characters WHERE clan_id=$clan_id" );
while( $arr = f_MFetch( $res ) ) $pids[$arr[0]] = $arr[1];

$acts = array( -1 => "Все записи", 1 => "История Построек", 2 => "История Страниц Ордена", 3 => "История Званий и Должностей (кроме прав)", 4 => "История Изменения Прав", 5 => "История Управления Составом", 6 => "История Склада", 7 => "История Казначейства", 8 => "История Столовой" );

echo "<tr><td valign=top>Персонаж:</td><td>".create_select_global( 'pid', $pids, $pid );
echo "<br><input type=text class=edit_box name=pname style='width: 100%'>";
echo "</td></tr>";
echo "<tr><td>Логи:</td><td>".create_select_global( 'act', $acts, $act )."</td></tr>";
echo "<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=Показать></td></tr>";

echo "</table>";
echo "</form>";

echo "</td></tr></table>";

?>
