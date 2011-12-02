<META http-equiv=Content-Type content="text/html; charset=windows-1251">
<link href="../style2.css" rel="stylesheet" type="text/css">


<?

include_once( '../functions.php' );
include_once( '../arrays.php' );
include_once( '../player.php' );
include_once( '../guild.php' );

f_MConnect( );

include( 'admin_header.php' );

if( isset( $HTTP_GET_VARS['login'] ) )
{
	$login = $HTTP_GET_VARS['login'];
	$item_id = $HTTP_GET_VARS['item_id'];
	settype( $item_id, 'integer' ) ;
	$where = '';
	if( $item_id != -2 ) $where = " AND item_id = $item_id";

	$res = f_MQuery( "SELECT player_id FROM characters WHERE login='$login'" );
	$arr = f_MFetch( $res );
	if( !$arr ) printf( "<font color=red>Нет такого игрока</font><br>" );
	else
	{
		$res = f_MQuery( "SELECT * FROM player_log WHERE player_id = $arr[0] $where ORDER BY entry_id DESC" );
		echo "<table border=1><tr><td><b>Когда</b></td><td><b>Название вещи</b></td><td><b>Изменение</b></td><td><b>Было</b></td><td><b>Стало</b></td><td><b>Источник</b></td></tr>";
		while( $arr = f_MFetch( $res ) )
		{
			if( $arr[item_id] == 0 ) $name = "Дублоны";
			else if( $arr[item_id] == -1 ) $name = "Таланты";
			else
			{
				$mres = f_MQuery( "SELECT name FROM items WHERE item_id = $arr[item_id]" );
				$marr = f_MFetch( $mres );
				if( !$marr ) $name = "Шняга #$arr[item_id]";
				else $name = $marr[0];
			}
			$num = $arr[have] - $arr[had];
			if( $arr['type'] == 0 ) $str = "Эффект <a href=phrase_editor.php?id=$arr[arg1]>фразы $arr[arg1]</a>";
			else if( $arr['type'] == 1 )
			{
				if( $arr[arg2] == 0 ) $str = "Результат копки в гильдии {$guilds[$arr[arg1]][0]}";
				else if( $arr[arg2] == 1 ) $str = "Результат копки в гильдии {$guilds[$arr[arg1]][0]} - событие";
				else if( $arr[arg2] == 2 ) $str = "Результат капканов в гильдии {$guilds[$arr[arg1]][0]}";
				else $str = "Результат копки в гильдии {$guilds[$arr[arg1]][0]} - непонятно что";
			}
			else if( $arr['type'] == 2 )
			{
				$mres = f_MQuery( "SELECT login FROM characters WHERE player_id = $arr[arg1]" );
				$marr = f_MFetch( $mres );
				if( !$marr ) $lg = "Кем-то удаленным ($arr[arg1])";
				else $lg = "<a href=../player_info.php?nick=$marr[0]>".$marr[0]."</a>";
				$str = "Сделка с $lg";

			}
			else if( $arr['type'] == 3 )
			{
				$str = "Выкинул на землю в локации $arr[arg1] в месте $arr[arg2]";
			}
			else if( $arr['type'] == 4 )
			{
				$str = "Подобрал с земли в локации $arr[arg1] в месте $arr[arg2]";
			}
			else if( $arr['type'] == 5 )
			{
				if( $arr['arg1'] == 0 ) $gm = "Кубики";
				else if(  $arr['arg1'] == 1 ) $gm = "Рулетка";
				else if(  $arr['arg1'] == 2 ) $gm = "Лотерея";
				else if(  $arr['arg1'] == 3 ) $gm = "Лото";

				$str = "Казино - $gm";
			}
			else if( $arr['type'] == 6 )
			{
				if( $arr[arg1] == -1 )  $sn = "Барахолка";
				else
				{
    				$mres = f_MQuery( "SELECT name FROM shops WHERE shop_id = $arr[arg1]" );
    				$marr = f_MFetch( $mres );
    				if( !$marr ) $sn = "Неизвестный";
    				else $sn = $marr[0];
    			}
				$str = "Сделка с магазином $sn ($arr[arg1])";
			}
			else if( $arr['type'] == 7 )
			{
				$str = "Нашел в пещерах на глубине $arr[arg1]";
			}
			else if( $arr['type'] == 8 )
			{
				$str = "Подарок от админа $arr[arg1]";
			}
			else if( $arr['type'] == 9 )
			{
				if( $arr[arg1] == 2 ) $do = " поиск цветочков";
				else if( $arr[arg1] == 8 ) $do = " ловля зайчиков";
				else if( $arr[arg1] == 6 ) $do = " рубля деревцев";
				$str = "Действие в лесу - $do ($arr[arg1])";
			}
			else if( $arr['type'] == 10 )
			{
				$str = "Получил за выигранный бой (верен только столбец Изменение)";
			}
			else if( $arr['type'] == 11 )
			{
				$str = "Прокушал в харчевне";
			}
			else if( $arr['type'] == 12 )
			{
				$str = "Хранилище";
			}
			else if( $arr['type'] == 13 )
			{
				$str = "Поиск Камушков";
			}
			else if( $arr['type'] == 14 )
			{
				$str = "Склад Ордена";
			}
			else if( $arr['type'] == 15 )
			{
				$str = "Аукцион - ставка, возврат ставки или получение лота";
			}
			else if( $arr['type'] == 16 )
			{
				$str = "Нашел в лабиринте";
			}
			else if( $arr['type'] == 17 )
			{
				$str = "Выучил закл $arr[arg1] в БТЗ";
			}
			else if( $arr['type'] == 18 )
			{
				$str = "Получил в Дозоре";
			}
			else if( $arr['type'] == 19 )
			{
				if( $arr['arg1'] == 0 )
					$str = "Отправил по почте ".f_MValue( "SELECT login FROM characters WHERE player_id=$arr[arg2]" ). " ( $arr[arg2] )";
				if( $arr['arg1'] == 1 )
					$str = "Получил по почте ".f_MValue( "SELECT login FROM characters WHERE player_id=$arr[arg2]" ). " ( $arr[arg2] )";
				if( $arr['arg1'] == 2 )
					$str = "Заплатил за наложенный платеж ".f_MValue( "SELECT login FROM characters WHERE player_id=$arr[arg2]" ). " ( $arr[arg2] )";
				if( $arr['arg1'] == 3 )
					$str = "Вернулось невостребованное по почте ".f_MValue( "SELECT login FROM characters WHERE player_id=$arr[arg2]" ). " ( $arr[arg2] )";
			}
			else if( $arr['type'] == 20 )
			{
				$str = "Подарил подарок у NPC";
			}
			else if( $arr['type'] == 21 )
			{
				if( $arr[arg1] == 1000 )
				{
					if( $arr[arg2] == 0 ) $str = "Продал фавну $arr[arg3] талантов";
					else if( $arr[arg2] == 1 ) $str = "Сбросил 30 сек у лекаря";
					else if( $arr[arg2] == 2 ) $str = "Купил набор смайлов игроку $arr[arg3]";
					else if( $arr[arg2] == 3 ) $str = "Сменил имя";
					else if( $arr[arg2] == 4 ) $str = "Сменил пол";
					else if( $arr[arg2] == 5 ) $str = "Сменил цвет ника";
				}
				else $str = "Купил или продлил премиум $arr[arg1]";
			}
			else if( $arr['type'] == 22 )
			{
				$str = "Купил через ";
				if( $arr['arg1'] == 0 ) $str .= "SMS";
				else if( $arr['arg1'] == 1 ) $str .= "WM";
			}
			else if( $arr['type'] == 23 )
			{
				$str = "Зачарованная шахта";
			}
			else if( $arr['type'] == 24 )
			{
				$str = "Восторженность в мини-игре $arr[arg1]";
			}
			else if( $arr['type'] == 25 )
			{
				$str = "Реферальная программа";
			}
			else if( $arr['type'] == 26 )
			{
				if( $arr['have'] > $arr['had'] && $arr['arg2'] > 0 ) $str = "Выиграл в миниигре у игрока $arr[arg2] в миниигре $arr[arg1]";
				else if( $arr['have'] > $arr['had'] )  $str = "Возврат ставки в миниигре $arr[arg1]"; 
				else $str = "Ставка в миниигре $arr[arg1]"; 
			}
			else if( $arr['type'] == 27 )
			{
				$str = "Подобрал в бою";
			}
			else if( $arr['type'] == 28 )
			{
				$str = "Зарядка Посохов";
			}
			else if( $arr['type'] == 29 )
			{
				$str = "Награда за заслуги перед Теллой";
			}
			else if( $arr['type'] == 30 )
				$str = "Купил или продлил аватарку на форуме";
			else if( $arr['type'] == 31 )
				$str = "Четвертый этаж БТЗ";
			else if( $arr['type'] == 32 )
			{
				if( $arr['have'] < $arr['had'] )
					$str = "Оплата услуг стражи подземелья";
				else $str = "Получил в подземелье";
			}
			else if( $arr['type'] == 33 )
				$str = "Подземелья, $arr[arg1] этап";
			else if( $arr['type'] == 34 )
				$str = "Работы облагораживанию территории или помощь в отбивании построек";
			else if( $arr['type'] == 35 )
			{
				if( $arr['had'] > $arr['have'] ) $str = "Порасходвал на готовку еды на вынос";
				else $str = "Приготовил еду на вынос";
			}
			else if( $arr['type'] == 36 )
			{
				$str = "";
				if( $arr['arg1'] == 0 ) $str .= "Алтарь Кузнецов";
				else if( $arr['arg1'] == 1 ) $str .=  "Алтарь Ювелиров";
				else if( $arr['arg1'] == 2 ) $str .=  "Алтарь Портных";
				$str .= ", ";
				if( $arr['arg2'] == 0 ) $str .= "Начал Работу";
				else if( $arr['arg2'] == 1 ) $str .=  "Отменил Работу";
				else if( $arr['arg2'] == 2 ) $str .=  "Завершил работу";
				else if( $arr['arg2'] == 3 && $arr['arg1'] == 2 ) $str .=  "Передача Мастерства";
				else if( $arr['arg2'] == 3 ) $str .=  "Обустройство мастерской";
				else if( $arr['arg2'] == 4 ) $str .=  "Готовка Смеси";
			}
			else if( $arr['type'] == 37 )
			{
				$str = "Покер";
			}
			else if( $arr['type'] == 38 )
			{
				if( $arr['have'] > $arr['had'] ) $str = "Нашел перо на зачарованной поляне";
				else $str = "Прицепил перышко к игроку";
			}
			else if( $arr['type'] == 39 )
			{
				$str = "Клановая вещь силой возвращена на склад";
			}

			else $str = "Неизвестный способ $arr[type]";
			echo "<tr><td>".date( "d.m.Y H:i:s", $arr['time'] )."</td><td>$name ($arr[item_id])</td><td>$num</td><td>$arr[had]</td><td>$arr[have]</td><td>$str</td></tr>";
		}
		echo "</table>";
	}
}


?>

<a href=index.php>На главную</a><br>
<b>Логи всех движений вещей и денег у персонажа</b><br>
<table>
<form action=player_log.php method=get>
<tr><td>Логин персонажа: </td><td><input type=text name=login value='<?=$login?>' class=m_btn></td></tr>
<tr><td>АйДи вещи: </td><td><input type=text name=item_id class=m_btn value=-2><br>0 - деньги, -1 - таланты, -2 - все</td></tr>
<tr><td>&nbsp;</td><td><input type=submit class=s_btn value=Показать></td></tr>
</form>
</table>

<?

f_MClose( );


?>
